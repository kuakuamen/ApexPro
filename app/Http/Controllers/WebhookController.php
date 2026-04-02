<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalSubscription;
use App\Models\SubscriptionTransaction;
use App\Services\MercadoPagoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function handle(Request $request, MercadoPagoService $mpService)
    {
        Log::info('MP Webhook received', $request->all());

        if (!$mpService->validateWebhookSignature($request)) {
            Log::warning('MP Webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $type = $request->input('type') ?? $request->input('action');

        if ($type === 'payment') {
            return $this->handlePaymentWebhook($request, $mpService);
        }

        if ($type === 'subscription_preapproval') {
            return $this->handlePreapprovalWebhook($request, $mpService);
        }

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // Pagamento avulso (PIX, cartão legado)
    // -------------------------------------------------------------------------

    protected function handlePaymentWebhook(Request $request, MercadoPagoService $mpService)
    {
        $mpPaymentId = (string) ($request->input('data.id') ?? $request->input('data_id'));

        if (!$mpPaymentId) {
            return response()->json(['ok' => true]);
        }

        $transaction = SubscriptionTransaction::where('mp_payment_id', $mpPaymentId)->first();
        if (!$transaction) {
            // Fallback 1: external_reference vindo direto no payload do webhook
            $extRefFromPayload = $request->input('data.external_reference')
                ?? $request->input('external_reference');
            if ($extRefFromPayload) {
                $transaction = SubscriptionTransaction::where('mp_external_reference', $extRefFromPayload)->first();
            }
        }

        if (!$transaction) {
            // Fallback 2: buscar via API do MP (pode ter latência — race condition com o controller)
            // Aguarda 2s para o controller ter tempo de salvar o mp_payment_id
            sleep(2);
            $transaction = SubscriptionTransaction::where('mp_payment_id', $mpPaymentId)->first();
        }

        if (!$transaction) {
            try {
                $paymentInfo = $mpService->getPaymentStatus($mpPaymentId);
                $extRef = $paymentInfo['raw']['external_reference'] ?? null;
                if ($extRef) {
                    $transaction = SubscriptionTransaction::where('mp_external_reference', $extRef)->first();
                }
            } catch (\Exception $e) {
                Log::warning('MP Webhook: failed to get payment status for fallback', [
                    'mp_payment_id' => $mpPaymentId,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        // Busca definitiva pela API do MP para ter status atualizado
        if ($transaction && empty($paymentInfo)) {
            try {
                $paymentInfo = $mpService->getPaymentStatus($mpPaymentId);
            } catch (\Exception $e) {
                Log::error('MP Webhook: failed to get payment status', ['error' => $e->getMessage()]);
                return response()->json(['ok' => true]);
            }
        }

        if (!$transaction) {
            Log::warning('MP Webhook: transaction not found', ['mp_payment_id' => $mpPaymentId]);
            return response()->json(['ok' => true]);
        }

        $status = $paymentInfo['status'] ?? null;

        // Idempotência
        if ($transaction->status === 'approved' && $status === 'approved') {
            return response()->json(['ok' => true]);
        }

        if (!$transaction->mp_payment_id) {
            $transaction->mp_payment_id = $mpPaymentId;
        }

        $transaction->mp_raw_response = $paymentInfo['raw'];

        switch ($status) {
            case 'approved':
                $this->handleApproved($transaction);
                break;
            case 'rejected':
                $transaction->status = 'rejected';
                $transaction->failure_reason = $paymentInfo['status_detail'] ?? 'rejected';
                $transaction->save();
                break;
            case 'cancelled':
                $transaction->status = 'cancelled';
                $transaction->save();
                break;
            case 'refunded':
            case 'charged_back':
                $this->handleRefund($transaction, $status);
                break;
            case 'in_process':
            case 'pending':
                $transaction->status = 'pending';
                $transaction->save();
                break;
            default:
                $transaction->save();
                break;
        }

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // Assinatura recorrente (preapproval)
    // -------------------------------------------------------------------------

    protected function handlePreapprovalWebhook(Request $request, MercadoPagoService $mpService)
    {
        $preapprovalId = (string) ($request->input('data.id') ?? '');

        if (!$preapprovalId) {
            return response()->json(['ok' => true]);
        }

        try {
            $info = $mpService->getPreapproval($preapprovalId);
        } catch (\Exception $e) {
            Log::error('MP Webhook: failed to get preapproval', [
                'preapproval_id' => $preapprovalId,
                'error'          => $e->getMessage(),
            ]);
            return response()->json(['ok' => true]);
        }

        $mpStatus = $info['status'] ?? null;

        // Buscar subscription por preapproval_id
        $subscription = ProfessionalSubscription::where('mp_preapproval_id', $preapprovalId)->first();

        // Fallback: buscar via external_reference na tabela de transactions
        if (!$subscription) {
            $extRef = $info['external_reference'] ?? null;
            if ($extRef) {
                $tx = SubscriptionTransaction::where('mp_external_reference', $extRef)->first();
                if ($tx) {
                    $subscription = $tx->subscription;
                    // Guardar preapproval_id se ainda não tiver (race condition)
                    if ($subscription && empty($subscription->mp_preapproval_id)) {
                        $subscription->update(['mp_preapproval_id' => $preapprovalId]);
                    }
                }
            }
        }

        if (!$subscription) {
            Log::warning('MP Webhook: subscription not found for preapproval', [
                'preapproval_id' => $preapprovalId,
            ]);
            return response()->json(['ok' => true]);
        }

        // Idempotência: status já é o mesmo
        if ($subscription->mp_preapproval_status === $mpStatus) {
            return response()->json(['ok' => true]);
        }

        Log::info('MP Webhook preapproval', [
            'preapproval_id'  => $preapprovalId,
            'status'          => $mpStatus,
            'subscription_id' => $subscription->id,
        ]);

        switch ($mpStatus) {
            case 'authorized':
                $this->handlePreapprovalAuthorized($subscription, $info, $preapprovalId);
                break;

            case 'paused':
                $subscription->update([
                    'status'                => 'overdue',
                    'mp_preapproval_status' => 'paused',
                ]);
                Log::info('MP Webhook: preapproval paused → subscription overdue', [
                    'subscription_id' => $subscription->id,
                ]);
                break;

            case 'cancelled':
                $updateData = [
                    'status'                => 'cancelled',
                    'mp_preapproval_status' => 'cancelled',
                ];
                $subscription->update($updateData);

                // Desativar user apenas se o acesso já venceu
                $user = $subscription->user;
                if ($user && ($subscription->expires_at === null || $subscription->expires_at->isPast())) {
                    $user->update(['is_active' => false]);
                }
                Log::info('MP Webhook: preapproval cancelled', ['subscription_id' => $subscription->id]);
                break;

            default:
                $subscription->update(['mp_preapproval_status' => $mpStatus]);
                break;
        }

        return response()->json(['ok' => true]);
    }

    protected function handlePreapprovalAuthorized(ProfessionalSubscription $subscription, array $info, string $preapprovalId): void
    {
        $now = Carbon::now();
        $nextBillingAt = !empty($info['next_payment_date'])
            ? Carbon::parse($info['next_payment_date'])
            : $now->copy()->addDays(30);

        // --- Renovação mensal automática (subscription já estava ativa) ---
        if ($subscription->status === 'active') {
            // Deduplicação: já existe transaction aprovada na última hora?
            $recent = SubscriptionTransaction::where('subscription_id', $subscription->id)
                ->where('status', 'approved')
                ->where('paid_at', '>=', $now->copy()->subHour())
                ->exists();

            if ($recent) {
                Log::info('MP Webhook: preapproval renewal duplicata ignorada', [
                    'subscription_id' => $subscription->id,
                ]);
                return;
            }

            // Criar transaction de registro da cobrança mensal
            $yearMonth = $now->format('Ym');
            SubscriptionTransaction::create([
                'subscription_id'       => $subscription->id,
                'user_id'               => $subscription->user_id,
                'plan_id'               => $subscription->plan_id,
                'amount'                => $subscription->price,
                'payment_method'        => 'credit_card',
                'status'                => 'approved',
                'mp_preapproval_id'     => $preapprovalId,
                'mp_external_reference' => 'preapproval-renewal-' . $preapprovalId . '-' . $yearMonth,
                'paid_at'               => $now,
                'mp_status_detail'      => 'authorized',
            ]);

            $graceDays = (int) config('services.mercadopago.grace_period_days', 5);

            $subscription->update([
                'status'                => 'active',
                'mp_preapproval_status' => 'authorized',
                'expires_at'            => $now->copy()->addDays(30),
                'grace_until'           => $now->copy()->addDays(30 + $graceDays),
                'last_paid_at'          => $now,
                'next_billing_at'       => $nextBillingAt,
            ]);

            $user = $subscription->user;
            if ($user) {
                $user->update([
                    'subscription_expires_at' => $subscription->expires_at,
                    'is_active'               => true,
                ]);
            }

            Log::info('MP Webhook: preapproval monthly renewal processed', [
                'subscription_id' => $subscription->id,
            ]);
            return;
        }

        // --- Primeira ativação (subscription estava pending) ---
        $transaction = SubscriptionTransaction::where('subscription_id', $subscription->id)
            ->where('status', 'pending')
            ->where('payment_method', 'credit_card')
            ->latest()
            ->first();

        if ($transaction) {
            $transaction->update([
                'status'            => 'approved',
                'paid_at'           => $now,
                'mp_preapproval_id' => $preapprovalId,
                'mp_status_detail'  => 'authorized',
            ]);

            // Usar SubscriptionController helper via instância
            $controller = app(SubscriptionController::class);
            $controller->activateSubscriptionPublic($transaction->fresh());
        } else {
            // Sem transaction pendente: ativar diretamente
            $graceDays = (int) config('services.mercadopago.grace_period_days', 5);
            $subscription->update([
                'status'                => 'active',
                'mp_preapproval_status' => 'authorized',
                'starts_at'             => $now,
                'expires_at'            => $now->copy()->addDays(30),
                'grace_until'           => $now->copy()->addDays(30 + $graceDays),
                'last_paid_at'          => $now,
                'next_billing_at'       => $nextBillingAt,
            ]);

            $user = $subscription->user;
            if ($user) {
                $user->update([
                    'subscription_expires_at' => $subscription->expires_at,
                    'is_active'               => true,
                    'plan_name'               => $subscription->plan_name,
                    'max_students'            => $subscription->max_students,
                ]);
            }
        }

        Log::info('MP Webhook: preapproval first activation processed', [
            'subscription_id' => $subscription->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers existentes (pagamento avulso)
    // -------------------------------------------------------------------------

    protected function handleApproved(SubscriptionTransaction $transaction): void
    {
        $transaction->status = 'approved';
        $transaction->paid_at = Carbon::now();
        $transaction->save();

        $subscription = $transaction->subscription;
        if ($subscription) {
            $now       = Carbon::now();
            $graceDays = config('services.mercadopago.grace_period_days', 5);

            $subscription->update([
                'status'              => 'active',
                'starts_at'           => $now,
                'expires_at'          => $now->copy()->addDays(30),
                'grace_until'         => $now->copy()->addDays(30 + $graceDays),
                'last_payment_method' => $transaction->payment_method,
                'last_paid_at'        => $now,
            ]);

            $user = $subscription->user;
            if ($user) {
                $user->update([
                    'subscription_expires_at' => $subscription->expires_at,
                    'is_active'               => true,
                    'plan_name'               => $subscription->plan_name,
                    'max_students'            => $subscription->max_students,
                ]);
            }
        }

        Log::info('MP Webhook: payment approved', ['transaction_id' => $transaction->id]);
    }

    protected function handleRefund(SubscriptionTransaction $transaction, string $status): void
    {
        $transaction->status = $status;
        $transaction->refunded_at = Carbon::now();
        $transaction->save();

        $subscription = $transaction->subscription;
        if ($subscription) {
            $subscription->update(['status' => 'suspended']);

            $user = $subscription->user;
            if ($user) {
                $user->update(['is_active' => false]);
            }
        }

        Log::info("MP Webhook: payment {$status}", ['transaction_id' => $transaction->id]);
    }
}
