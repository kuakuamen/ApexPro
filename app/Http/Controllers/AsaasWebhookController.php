<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalSubscription;
use App\Models\SubscriptionTransaction;
use App\Services\AsaasService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    public function handle(Request $request, AsaasService $asaas)
    {
        $payload = $request->all();
        $event   = $payload['event'] ?? null;

        Log::info('Asaas Webhook', [
            'event'      => $event,
            'payment_id' => $payload['payment']['id'] ?? ($payload['subscription']['id'] ?? null),
        ]);

        // Validação de token (opcional mas recomendado)
        if (!$asaas->validateWebhookToken($request)) {
            Log::warning('Asaas Webhook: token inválido');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        match ($event) {
            'PAYMENT_RECEIVED',
            'PAYMENT_CONFIRMED'        => $this->handlePaymentConfirmed($payload),
            'PAYMENT_OVERDUE'          => $this->handlePaymentOverdue($payload),
            'PAYMENT_REFUNDED',
            'PAYMENT_CHARGEBACK'       => $this->handlePaymentRefunded($payload),
            'PAYMENT_DELETED'          => $this->handlePaymentDeleted($payload),
            'SUBSCRIPTION_DELETED',
            'SUBSCRIPTION_INACTIVATED' => $this->handleSubscriptionDeleted($payload),
            default                    => Log::info('Asaas Webhook: evento ignorado', ['event' => $event]),
        };

        return response()->json(['ok' => true]);
    }

    // ── Mapeia billingType do Asaas → nosso payment_method ────────────────────

    protected function mapBillingType(string $billingType): string
    {
        return match (strtoupper($billingType)) {
            'CREDIT_CARD' => 'credit_card',
            default       => 'pix',   // PIX, BOLETO e undefined → tudo vira pix
        };
    }

    // ── Pagamento confirmado (PIX recebido ou cartão aprovado) ─────────────────

    protected function handlePaymentConfirmed(array $payload): void
    {
        $payment        = $payload['payment'] ?? [];
        $asaasPaymentId = $payment['id'] ?? null;
        $asaasSubId     = $payment['subscription'] ?? null;

        if (!$asaasPaymentId) {
            return;
        }

        // Encontra nossa assinatura via asaas_subscription_id ou asaas_payment_id
        $subscription = $this->findSubscription($asaasSubId, $asaasPaymentId);

        if (!$subscription) {
            Log::warning('Asaas Webhook: subscription not found', [
                'asaas_payment_id'      => $asaasPaymentId,
                'asaas_subscription_id' => $asaasSubId,
            ]);
            return;
        }

        // Não reativa assinatura que foi cancelada pelo usuário
        if ($subscription->status === 'cancelled') {
            Log::info('Asaas Webhook: pagamento ignorado pois assinatura foi cancelada pelo usuário', [
                'subscription_id' => $subscription->id,
            ]);
            return;
        }

        $now       = Carbon::now();
        $expiresAt = $now->copy()->addDays(30);

        // Atualiza assinatura
        $subscription->update([
            'status'              => 'active',
            'starts_at'           => $now,
            'expires_at'          => $expiresAt,
            'last_paid_at'        => $now,
            'next_billing_at'     => $expiresAt,
            'last_payment_method' => $this->mapBillingType($payment['billingType'] ?? 'PIX'),
        ]);

        // Atualiza usuário
        $user = $subscription->user;
        if ($user) {
            $user->update([
                'subscription_expires_at' => $expiresAt,
                'is_active'               => true,
                'plan_name'               => $subscription->plan_name,
                'max_students'            => $subscription->max_students,
            ]);
        }

        // Cria/atualiza transação
        SubscriptionTransaction::updateOrCreate(
            ['asaas_payment_id' => $asaasPaymentId],
            [
                'subscription_id' => $subscription->id,
                'user_id'         => $subscription->user_id,
                'plan_id'         => $subscription->plan_id,
                'amount'          => (float) ($payment['value'] ?? $subscription->price),
                'payment_method'  => $this->mapBillingType($payment['billingType'] ?? 'PIX'),
                'status'          => 'approved',
                'paid_at'         => $now,
            ]
        );

        Log::info('Asaas: pagamento confirmado → assinatura ativada', [
            'subscription_id' => $subscription->id,
            'expires_at'      => $expiresAt,
            'amount'          => $payment['value'] ?? null,
        ]);
    }

    // ── Pagamento vencido ──────────────────────────────────────────────────────

    protected function handlePaymentOverdue(array $payload): void
    {
        $payment    = $payload['payment'] ?? [];
        $asaasSubId = $payment['subscription'] ?? null;

        $subscription = $this->findSubscription($asaasSubId, $payment['id'] ?? null);

        if (!$subscription) {
            return;
        }

        // Se ainda está no período de trial → não bloqueia ainda
        $user = $subscription->user;
        if ($user && $subscription->status === 'trial') {
            $trialEndsAt = $subscription->trial_ends_at ?? $subscription->expires_at;
            if ($trialEndsAt && Carbon::parse($trialEndsAt)->isFuture()) {
                Log::info('Asaas Webhook: overdue ignorado pois trial ainda vigente', [
                    'subscription_id' => $subscription->id,
                    'trial_ends_at'   => $trialEndsAt,
                ]);
                return;
            }
        }

        $subscription->update(['status' => 'overdue']);

        if ($user) {
            $user->update(['is_active' => false]);
        }

        Log::info('Asaas: pagamento vencido → acesso bloqueado', ['subscription_id' => $subscription->id]);
    }

    // ── Pagamento cancelado ────────────────────────────────────────────────────

    protected function handlePaymentDeleted(array $payload): void
    {
        $asaasPaymentId = $payload['payment']['id'] ?? null;

        if ($asaasPaymentId) {
            SubscriptionTransaction::where('asaas_payment_id', $asaasPaymentId)
                ->update(['status' => 'cancelled']);
        }
    }

    // ── Assinatura cancelada no Asaas ──────────────────────────────────────────

    protected function handleSubscriptionDeleted(array $payload): void
    {
        $asaasSubId = $payload['subscription']['id'] ?? ($payload['id'] ?? null);

        if (!$asaasSubId) {
            return;
        }

        $subscription = ProfessionalSubscription::where('asaas_subscription_id', $asaasSubId)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'cancelled']);

        $user = $subscription->user;
        if ($user && (!$subscription->expires_at || $subscription->expires_at->isPast())) {
            $user->update(['is_active' => false]);
        }

        Log::info('Asaas: assinatura cancelada', ['subscription_id' => $subscription->id]);
    }

    // ── Estorno / Chargeback ───────────────────────────────────────────────────

    protected function handlePaymentRefunded(array $payload): void
    {
        $payment    = $payload['payment'] ?? [];
        $asaasSubId = $payment['subscription'] ?? null;

        $subscription = $this->findSubscription($asaasSubId, $payment['id'] ?? null);

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'overdue']);

        $user = $subscription->user;
        if ($user) {
            $user->update(['is_active' => false]);
        }

        // Marca transação como estornada
        $asaasPaymentId = $payment['id'] ?? null;
        if ($asaasPaymentId) {
            SubscriptionTransaction::where('asaas_payment_id', $asaasPaymentId)
                ->update(['status' => 'refunded']);
        }

        Log::info('Asaas: pagamento estornado → acesso bloqueado', ['subscription_id' => $subscription->id]);
    }

    // ── Helper ─────────────────────────────────────────────────────────────────

    protected function findSubscription(?string $asaasSubId, ?string $asaasPaymentId): ?ProfessionalSubscription
    {
        if ($asaasSubId) {
            $sub = ProfessionalSubscription::where('asaas_subscription_id', $asaasSubId)->first();
            if ($sub) {
                return $sub;
            }
        }

        if ($asaasPaymentId) {
            $tx = SubscriptionTransaction::where('asaas_payment_id', $asaasPaymentId)->first();
            if ($tx) {
                return $tx->subscription;
            }
        }

        return null;
    }
}
