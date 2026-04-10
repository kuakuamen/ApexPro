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
    // Pagamento avulso (PIX, cartao legado)
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
            // Fallback 2: buscar via API do MP (pode ter latencia - race condition com o controller)
            // Aguarda 2s para o controller ter tempo de salvar o mp_payment_id
            sleep(2);
            $transaction = SubscriptionTransaction::where('mp_payment_id', $mpPaymentId)->first();
        }

        if (!$transaction) {
            try {
                $paymentInfo = $mpService->getPaymentStatus($mpPaymentId);
                $extRef = $paymentInfo['raw']['external_reference'] ?? null;
                if ($extRef) {
                    // MP recebe 'sub-{uuid}' mas salvamos so '{uuid}' no banco
                    $normalizedRef = preg_replace('/^sub-/', '', $extRef);
                    $transaction = SubscriptionTransaction::where('mp_external_reference', $normalizedRef)
                        ->orWhere('mp_external_reference', $extRef)
                        ->first();
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

        // Idempotencia: so ignora se for literalmente o mesmo payment_id ja processado
        if ($transaction->status === 'approved' && $status === 'approved'
            && $transaction->mp_payment_id === $mpPaymentId) {
            $this->syncTransactionWithPaymentPayload($transaction, $paymentInfo, $mpPaymentId);
            return response()->json(['ok' => true]);
        }

        // Se e um pagamento diferente mas mesma external_reference (renovacao recorrente),
        // cria nova transaction em vez de reutilizar a original
        $isRecurringAttempt = $transaction->status === 'approved'
            && $transaction->payment_method === 'credit_card'
            && $transaction->mp_payment_id !== $mpPaymentId;

        if ($isRecurringAttempt) {
            $transaction = $this->createRecurringAttemptTransaction($transaction, $mpPaymentId, $paymentInfo['raw'] ?? []);
        }

        if (!$transaction->mp_payment_id) {
            $transaction->mp_payment_id = $mpPaymentId;
        }

        $this->syncTransactionWithPaymentPayload($transaction, $paymentInfo, $mpPaymentId);

        switch ($status) {
            case 'approved':
                $this->handleApproved($transaction);
                break;
            case 'rejected':
                $transaction->status = 'rejected';
                $transaction->mp_status_detail = $paymentInfo['status_detail'] ?? $status;
                $transaction->failure_reason = $paymentInfo['status_detail'] ?? 'rejected';
                $transaction->save();
                $this->blockSubscriptionAfterFailedRenewal($transaction);
                break;
            case 'cancelled':
                $transaction->status = 'cancelled';
                $transaction->mp_status_detail = $paymentInfo['status_detail'] ?? $status;
                $transaction->failure_reason = $paymentInfo['status_detail'] ?? 'cancelled';
                $transaction->save();
                $this->blockSubscriptionAfterFailedRenewal($transaction);
                break;
            case 'refunded':
            case 'charged_back':
                $this->handleRefund($transaction, $status);
                break;
            case 'in_process':
            case 'pending':
                $transaction->status = 'pending';
                $transaction->mp_status_detail = $paymentInfo['status_detail'] ?? $status;
                $transaction->save();
                break;
            default:
                $transaction->mp_status_detail = $paymentInfo['status_detail'] ?? $status;
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
                    // Guardar preapproval_id se ainda nao tiver (race condition)
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

        // Idempotencia: pula somente se nao for 'authorized' (renovacoes sempre chegam como authorized -> authorized)
        // Para 'authorized', a deduplicacao real fica dentro de handlePreapprovalAuthorized.
        if ($subscription->mp_preapproval_status === $mpStatus && $mpStatus !== 'authorized') {
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
                $this->syncLatestPendingCardTransaction($subscription, [
                    'mp_preapproval_id' => $preapprovalId,
                    'mp_status_detail'  => 'paused',
                    'status'            => 'rejected',
                    'failure_reason'    => 'preapproval_paused',
                ]);
                Log::info('MP Webhook: preapproval paused -> subscription overdue', [
                    'subscription_id' => $subscription->id,
                ]);
                break;

            case 'cancelled':
                $updateData = [
                    'status'                => 'cancelled',
                    'mp_preapproval_status' => 'cancelled',
                ];
                $subscription->update($updateData);
                $this->syncLatestPendingCardTransaction($subscription, [
                    'mp_preapproval_id' => $preapprovalId,
                    'mp_status_detail'  => 'cancelled',
                    'status'            => 'cancelled',
                    'failure_reason'    => 'preapproval_cancelled',
                ]);

                // Desativar user apenas se o acesso ja venceu
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
        $nextBillingAt = !empty($info['next_payment_date'])
            ? Carbon::parse($info['next_payment_date'])
            : Carbon::now()->addDays(30);

        $subscription->update([
            'mp_preapproval_id'     => $preapprovalId,
            'mp_preapproval_status' => 'authorized',
            'mp_card_id'            => $info['card_id'] ?? $subscription->mp_card_id,
            'next_billing_at'       => $nextBillingAt,
        ]);

        $this->syncLatestPendingCardTransaction($subscription, [
            'mp_preapproval_id' => $preapprovalId,
            'mp_status_detail'  => 'authorized',
        ]);

        Log::info('MP Webhook: preapproval authorized synchronized without activation', [
            'subscription_id' => $subscription->id,
            'preapproval_id'  => $preapprovalId,
        ]);
    }

    protected function syncLatestPendingCardTransaction(ProfessionalSubscription $subscription, array $attributes): void
    {
        $pendingTransaction = SubscriptionTransaction::where('subscription_id', $subscription->id)
            ->where('payment_method', 'credit_card')
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingTransaction) {
            $pendingTransaction->update($attributes);
        }
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
            $now = Carbon::now();
            $nextBillingAt = $now->copy()->addDays(30);

            $subscription->update([
                'status'              => 'active',
                'starts_at'           => $now,
                'expires_at'          => $nextBillingAt,
                'last_payment_method' => $transaction->payment_method,
                'last_paid_at'        => $now,
                'next_billing_at'     => $nextBillingAt,
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

        Log::info('MP Webhook: payment approved', [
            'transaction_id' => $transaction->id,
            'payment_id'     => $transaction->mp_payment_id,
            'amount'         => $transaction->amount,
            'status_detail'  => $transaction->mp_status_detail,
        ]);
    }

    protected function handleRefund(SubscriptionTransaction $transaction, string $status): void
    {
        $transaction->status = $status;
        $transaction->mp_status_detail = $transaction->mp_status_detail ?: $status;
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

        Log::info("MP Webhook: payment {$status}", [
            'transaction_id' => $transaction->id,
            'payment_id'     => $transaction->mp_payment_id,
            'amount'         => $transaction->amount,
            'status_detail'  => $transaction->mp_status_detail,
        ]);
    }

    protected function createRecurringAttemptTransaction(SubscriptionTransaction $baseTransaction, string $mpPaymentId, array $rawPayment): SubscriptionTransaction
    {
        $existingAttempt = SubscriptionTransaction::where('mp_payment_id', $mpPaymentId)->first();
        if ($existingAttempt) {
            return $existingAttempt;
        }

        $attempt = $baseTransaction->replicate([
            'mp_payment_id',
            'mp_raw_response',
            'paid_at',
            'refunded_at',
            'pix_qr_code',
            'pix_qr_code_base64',
            'pix_expires_at',
            'failure_reason',
        ]);

        $attempt->mp_payment_id = $mpPaymentId;
        $attempt->mp_external_reference = sprintf('pay-%s', $mpPaymentId);
        $attempt->mp_raw_response = $rawPayment;
        $attempt->status = 'pending';
        $attempt->paid_at = null;
        $attempt->failure_reason = null;
        $attempt->save();

        return $attempt;
    }

    protected function syncTransactionWithPaymentPayload(SubscriptionTransaction $transaction, array $paymentInfo, ?string $mpPaymentId = null): void
    {
        $rawPayment = $paymentInfo['raw'] ?? [];
        $amount = data_get($rawPayment, 'transaction_amount');
        $statusDetail = $paymentInfo['status_detail']
            ?? data_get($rawPayment, 'status_detail')
            ?? ($paymentInfo['status'] ?? null);

        if ($mpPaymentId && !$transaction->mp_payment_id) {
            $transaction->mp_payment_id = $mpPaymentId;
        }

        $transaction->mp_raw_response = $rawPayment;

        if ($amount !== null) {
            $transaction->amount = (float) $amount;
        }

        if ($statusDetail) {
            $transaction->mp_status_detail = $statusDetail;
        }

        $transaction->save();
    }

    protected function blockSubscriptionAfterFailedRenewal(SubscriptionTransaction $transaction): void
    {
        $subscription = $transaction->subscription;
        if (!$subscription || $transaction->payment_method !== 'credit_card') {
            return;
        }

        $subscription->update([
            'status' => 'suspended',
        ]);

        $user = $subscription->user;
        if ($user) {
            $user->update([
                'is_active' => false,
            ]);
        }
    }
}
