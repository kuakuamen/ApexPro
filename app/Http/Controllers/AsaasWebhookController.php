<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalSubscription;
use App\Models\SubscriptionTransaction;
use App\Services\AsaasService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AsaasWebhookController extends Controller
{
    protected function resolveNextPendingDueDate(AsaasService $asaas, ?string $asaasSubscriptionId): ?Carbon
    {
        if (empty($asaasSubscriptionId)) {
            return null;
        }

        return $asaas->getEarliestPendingDueDate(
            $asaasSubscriptionId,
            (string) config('app.timezone', 'America/Sao_Paulo')
        );
    }

    public function handle(Request $request, AsaasService $asaas)
    {
        $payload = $request->all();
        $event = $payload['event'] ?? null;

        Log::info('Asaas Webhook', [
            'event' => $event,
            'payment_id' => $payload['payment']['id'] ?? null,
            'subscription_id' => $payload['subscription']['id'] ?? ($payload['payment']['subscription'] ?? null),
            'checkout_id' => $payload['checkout']['id'] ?? null,
        ]);

        if (!$asaas->validateWebhookToken($request)) {
            Log::warning('Asaas Webhook: token invalido');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        match ($event) {
            'CHECKOUT_CREATED' => $this->handleCheckoutCreated($payload),
            'CHECKOUT_CANCELED' => $this->handleCheckoutClosed($payload, 'cancelled'),
            'CHECKOUT_EXPIRED' => $this->handleCheckoutClosed($payload, 'expired'),
            'CHECKOUT_PAID' => $this->handleCheckoutPaid($payload, $asaas),
            'PAYMENT_RECEIVED',
            'PAYMENT_CONFIRMED',
            'PAYMENT_APPROVED_BY_RISK_ANALYSIS' => $this->handlePaymentConfirmed($payload, $asaas),
            'PAYMENT_OVERDUE' => $this->handlePaymentOverdue($payload),
            'PAYMENT_DECLINED',
            'PAYMENT_REPROVED_BY_RISK_ANALYSIS' => $this->handlePaymentDeclined($payload),
            'PAYMENT_REFUNDED',
            'PAYMENT_CHARGEBACK' => $this->handlePaymentRefunded($payload),
            'PAYMENT_DELETED' => $this->handlePaymentDeleted($payload),
            'SUBSCRIPTION_DELETED',
            'SUBSCRIPTION_INACTIVATED' => $this->handleSubscriptionDeleted($payload),
            default => Log::info('Asaas Webhook: evento ignorado', ['event' => $event]),
        };

        return response()->json(['ok' => true]);
    }

    protected function mapBillingType(?string $billingType): string
    {
        return strtoupper((string) $billingType) === 'CREDIT_CARD' ? 'credit_card' : 'pix';
    }

    protected function handleCheckoutCreated(array $payload): void
    {
        $checkoutId = $payload['checkout']['id'] ?? null;
        if (!$checkoutId) {
            return;
        }

        $transaction = SubscriptionTransaction::where('asaas_checkout_id', $checkoutId)->first();
        if (!$transaction) {
            return;
        }

        $transaction->update([
            'status' => 'pending',
            'asaas_raw_response' => $payload,
        ]);
    }

    protected function handleCheckoutClosed(array $payload, string $reason): void
    {
        $checkoutId = $payload['checkout']['id'] ?? null;
        if (!$checkoutId) {
            return;
        }

        $transaction = SubscriptionTransaction::where('asaas_checkout_id', $checkoutId)->first();
        if (!$transaction) {
            return;
        }

        $transaction->update([
            'status' => 'cancelled',
            'failure_reason' => $reason,
            'asaas_raw_response' => $payload,
        ]);
    }

    protected function handleCheckoutPaid(array $payload, AsaasService $asaas): void
    {
        $checkout = $payload['checkout'] ?? [];
        $checkoutId = $checkout['id'] ?? null;
        if (!$checkoutId) {
            return;
        }

        $transaction = SubscriptionTransaction::where('asaas_checkout_id', $checkoutId)->first();
        if (!$transaction) {
            return;
        }

        $subscription = $transaction->subscription;
        if (!$subscription) {
            return;
        }

        $customerId = $checkout['customer'] ?? $subscription->asaas_customer_id;

        $transaction->update([
            'status' => 'pending',
            'asaas_raw_response' => $payload,
        ]);

        $subscription->update([
            'asaas_customer_id' => $customerId,
        ]);

        if ($transaction->payment_method === 'credit_card' && $customerId) {
            try {
                $latestSubscription = $asaas->findLatestSubscriptionByCustomer($customerId);

                if ($latestSubscription) {
                    $subscriptionId = $latestSubscription['id'] ?? $subscription->asaas_subscription_id;
                    $nextBillingAt = $this->resolveNextPendingDueDate($asaas, $subscriptionId)
                        ?? (!empty($latestSubscription['nextDueDate'])
                            ? Carbon::parse($latestSubscription['nextDueDate'])
                            : $subscription->next_billing_at);

                    $subscription->update([
                        'asaas_subscription_id' => $subscriptionId,
                        'next_billing_at' => $nextBillingAt,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Asaas checkout paid: failed to sync subscription', [
                    'checkout_id' => $checkoutId,
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
            $transaction->update([
                'status' => 'approved',
                'amount' => 0,
                'paid_at' => Carbon::now(),
            ]);

            $this->grantAccessUntil($subscription, $transaction, $subscription->trial_ends_at);
        }
    }

    protected function handlePaymentConfirmed(array $payload, AsaasService $asaas): void
    {
        $payment = $payload['payment'] ?? [];
        $transaction = $this->findTransactionForPayment($payment);
        $subscription = $transaction?->subscription ?? $this->findSubscription(
            $payment['subscription'] ?? null,
            $payment['id'] ?? null,
            $payment['customer'] ?? null
        );

        if (!$subscription) {
            Log::warning('Asaas Webhook: subscription not found for confirmed payment', [
                'payment_id' => $payment['id'] ?? null,
                'subscription_id' => $payment['subscription'] ?? null,
                'customer_id' => $payment['customer'] ?? null,
            ]);
            return;
        }

        if (!$transaction) {
            $transaction = $this->createFallbackTransaction($subscription, $payment);
        }

        $confirmedAt = !empty($payment['confirmedDate'])
            ? Carbon::parse($payment['confirmedDate'])
            : Carbon::now();

        $nextBillingAt = null;
        if (!empty($payment['subscription'])) {
            try {
                $nextBillingAt = $this->resolveNextPendingDueDate($asaas, $payment['subscription']);
                if (!$nextBillingAt) {
                    $remoteSubscription = $asaas->getSubscription($payment['subscription']);
                    if (!empty($remoteSubscription['nextDueDate'])) {
                        $nextBillingAt = Carbon::parse($remoteSubscription['nextDueDate']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Asaas payment confirmed: failed to sync next billing date', [
                    'payment_id' => $payment['id'] ?? null,
                    'subscription_id' => $payment['subscription'] ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $expiresAt = $nextBillingAt ?: $confirmedAt->copy()->addDays(30);

        $transaction->update([
            'asaas_payment_id' => $payment['id'] ?? $transaction->asaas_payment_id,
            'amount' => (float) ($payment['value'] ?? $subscription->price),
            'payment_method' => $this->mapBillingType($payment['billingType'] ?? null),
            'status' => 'approved',
            'paid_at' => $confirmedAt,
            'failure_reason' => null,
            'asaas_raw_response' => $payload,
        ]);

        $subscription->update([
            'status' => 'active',
            'asaas_customer_id' => $payment['customer'] ?? $subscription->asaas_customer_id,
            'asaas_subscription_id' => $payment['subscription'] ?? $subscription->asaas_subscription_id,
            'starts_at' => $confirmedAt,
            'expires_at' => $expiresAt,
            'last_paid_at' => $confirmedAt,
            'next_billing_at' => $nextBillingAt ?: $expiresAt,
            'last_payment_method' => $this->mapBillingType($payment['billingType'] ?? null),
        ]);

        $this->syncUserAccess($subscription, $expiresAt, true);
    }

    protected function handlePaymentOverdue(array $payload): void
    {
        $payment = $payload['payment'] ?? [];
        $subscription = $this->findSubscription(
            $payment['subscription'] ?? null,
            $payment['id'] ?? null,
            $payment['customer'] ?? null
        );

        if (!$subscription) {
            return;
        }

        $transaction = $this->findTransactionForPayment($payment) ?? $this->findLatestPendingTransaction($subscription);
        if ($transaction) {
            $transaction->update([
                'asaas_payment_id' => $payment['id'] ?? $transaction->asaas_payment_id,
                'status' => 'rejected',
                'amount' => (float) ($payment['value'] ?? $transaction->amount),
                'failure_reason' => 'payment_overdue',
                'asaas_raw_response' => $payload,
            ]);
        }

        if ($this->shouldKeepCurrentAccess($subscription)) {
            return;
        }

        $subscription->update(['status' => 'overdue']);
        $this->syncUserAccess($subscription, $subscription->expires_at, false);
    }

    protected function handlePaymentDeclined(array $payload): void
    {
        $payment = $payload['payment'] ?? [];
        $subscription = $this->findSubscription(
            $payment['subscription'] ?? null,
            $payment['id'] ?? null,
            $payment['customer'] ?? null
        );

        if (!$subscription) {
            return;
        }

        $transaction = $this->findTransactionForPayment($payment) ?? $this->findLatestPendingTransaction($subscription);
        if ($transaction) {
            $transaction->update([
                'asaas_payment_id' => $payment['id'] ?? $transaction->asaas_payment_id,
                'status' => 'rejected',
                'amount' => (float) ($payment['value'] ?? $transaction->amount),
                'failure_reason' => $payload['event'] ?? 'payment_declined',
                'asaas_raw_response' => $payload,
            ]);
        }

        if ($this->shouldKeepCurrentAccess($subscription)) {
            return;
        }

        $subscription->update(['status' => 'overdue']);
        $this->syncUserAccess($subscription, $subscription->expires_at, false);
    }

    protected function handlePaymentDeleted(array $payload): void
    {
        $payment = $payload['payment'] ?? [];
        $transaction = $this->findTransactionForPayment($payment);

        if (!$transaction) {
            return;
        }

        $transaction->update([
            'status' => 'cancelled',
            'failure_reason' => 'payment_deleted',
            'asaas_raw_response' => $payload,
        ]);
    }

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

        if (!$subscription->expires_at || $subscription->expires_at->isPast()) {
            $this->syncUserAccess($subscription, $subscription->expires_at, false);
        }
    }

    protected function handlePaymentRefunded(array $payload): void
    {
        $payment = $payload['payment'] ?? [];
        $subscription = $this->findSubscription(
            $payment['subscription'] ?? null,
            $payment['id'] ?? null,
            $payment['customer'] ?? null
        );

        if (!$subscription) {
            return;
        }

        $transaction = $this->findTransactionForPayment($payment);
        if ($transaction) {
            $transaction->update([
                'status' => 'refunded',
                'failure_reason' => $payload['event'] ?? 'payment_refunded',
                'asaas_raw_response' => $payload,
            ]);
        }

        $subscription->update(['status' => 'overdue']);
        $this->syncUserAccess($subscription, $subscription->expires_at, false);
    }

    protected function findSubscription(?string $asaasSubId, ?string $asaasPaymentId, ?string $customerId = null): ?ProfessionalSubscription
    {
        if ($asaasSubId) {
            $subscription = ProfessionalSubscription::where('asaas_subscription_id', $asaasSubId)->first();
            if ($subscription) {
                return $subscription;
            }
        }

        if ($asaasPaymentId) {
            $transaction = SubscriptionTransaction::where('asaas_payment_id', $asaasPaymentId)->first();
            if ($transaction?->subscription) {
                return $transaction->subscription;
            }
        }

        if ($customerId) {
            return ProfessionalSubscription::where('asaas_customer_id', $customerId)
                ->latest('id')
                ->first();
        }

        return null;
    }

    protected function findTransactionForPayment(array $payment): ?SubscriptionTransaction
    {
        $asaasPaymentId = $payment['id'] ?? null;
        if ($asaasPaymentId) {
            $transaction = SubscriptionTransaction::where('asaas_payment_id', $asaasPaymentId)->first();
            if ($transaction) {
                return $transaction;
            }
        }

        $asaasSubId = $payment['subscription'] ?? null;
        if ($asaasSubId) {
            $transaction = SubscriptionTransaction::whereHas('subscription', function ($query) use ($asaasSubId) {
                $query->where('asaas_subscription_id', $asaasSubId);
            })->whereIn('status', ['pending', 'in_process'])->latest('id')->first();

            if ($transaction) {
                return $transaction;
            }
        }

        $customerId = $payment['customer'] ?? null;
        if ($customerId) {
            return SubscriptionTransaction::whereHas('subscription', function ($query) use ($customerId) {
                $query->where('asaas_customer_id', $customerId);
            })->whereIn('status', ['pending', 'in_process'])->latest('id')->first();
        }

        return null;
    }

    protected function findLatestPendingTransaction(ProfessionalSubscription $subscription): ?SubscriptionTransaction
    {
        return $subscription->transactions()
            ->whereIn('status', ['pending', 'in_process'])
            ->latest('id')
            ->first();
    }

    protected function createFallbackTransaction(ProfessionalSubscription $subscription, array $payment): SubscriptionTransaction
    {
        return SubscriptionTransaction::create([
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'plan_id' => $subscription->plan_id,
            'amount' => (float) ($payment['value'] ?? $subscription->price),
            'payment_method' => $this->mapBillingType($payment['billingType'] ?? null),
            'status' => 'pending',
            'asaas_payment_id' => $payment['id'] ?? null,
            'mp_external_reference' => Str::uuid()->toString(),
            'asaas_raw_response' => $payment,
        ]);
    }

    protected function shouldKeepCurrentAccess(ProfessionalSubscription $subscription): bool
    {
        if ($subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
            return true;
        }

        return $subscription->expires_at && $subscription->expires_at->isFuture();
    }

    protected function grantAccessUntil(
        ProfessionalSubscription $subscription,
        SubscriptionTransaction $transaction,
        Carbon $expiresAt
    ): void {
        $subscription->update([
            'status' => 'active',
            'starts_at' => Carbon::now(),
            'expires_at' => $expiresAt,
            'last_paid_at' => $transaction->paid_at ?? Carbon::now(),
            'next_billing_at' => $subscription->next_billing_at ?: $expiresAt,
            'last_payment_method' => $transaction->payment_method,
        ]);

        $this->syncUserAccess($subscription, $expiresAt, true);
    }

    protected function syncUserAccess(ProfessionalSubscription $subscription, ?Carbon $expiresAt, bool $active): void
    {
        $user = $subscription->user;
        if (!$user) {
            return;
        }

        $user->update([
            'subscription_expires_at' => $expiresAt,
            'is_active' => $active,
            'plan_name' => $subscription->plan_name,
            'max_students' => $subscription->max_students,
        ]);
    }
}
