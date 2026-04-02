<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalSubscription;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Rules\Cpf;
use App\Services\MercadoPagoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class SubscriptionController extends Controller
{
    protected $plans = [
        'plan_50' => [
            'id' => 'plan_50',
            'name' => 'Plano Profissional',
            'price' => 5.00,
            'max_students' => 50,
            'color' => '#3b82f6',
            'features' => [
                'Gerencie ate 50 alunos',
                'Organize e prescreva treinos',
                'Gere treinos instantaneos com IA',
                'Avalie a postura via IA',
                'Tenha suporte prioritario',
                'Controle o financeiro dos alunos',
            ],
        ],
        'plan_100' => [
            'id' => 'plan_100',
            'name' => 'Plano Elite',
            'price' => 10.00,
            'max_students' => 100,
            'color' => '#8b5cf6',
            'features' => [
                'Gerencie ate 100 alunos',
                'Organize e prescreva treinos',
                'Gere treinos instantaneos com IA',
                'Avalie a postura via IA',
                'Tenha suporte prioritario',
                'Controle o financeiro dos alunos',
                'Acesse suporte VIP exclusivo',
            ],
        ],
        'plan_500' => [
            'id' => 'plan_500',
            'name' => 'Plano Studio',
            'price' => 15.00,
            'max_students' => 500,
            'color' => '#f59e0b',
            'features' => [
                'Gerencie ate 500 alunos',
                'Gerencie todo o seu negocio',
                'Organize e prescreva treinos',
                'Analise a evolucao visual com IA',
                'Gere treinos instantaneos com IA',
                'Acesse suporte VIP exclusivo',
                'Acompanhe relatorios detalhados',
            ],
        ],
    ];

    public function index()
    {
        return view('plans.index', ['plans' => $this->plans]);
    }

    public function getPlans(): array
    {
        return $this->plans;
    }

    public function checkout($planId)
    {
        if (!isset($this->plans[$planId])) {
            abort(404, 'Plano nao encontrado');
        }

        if (Auth::check() && Auth::user()?->role === 'personal') {
            return redirect()->route('subscription.renew.checkout', ['plan' => $planId]);
        }

        return view('plans.checkout', [
            'plan'          => $this->plans[$planId],
            'isRenewal'     => false,
            'defaultMethod' => 'pix',
            'mpPublicKey'   => ((string) config('services.mercadopago.mode', 'live') === 'test'
                ? config('services.mercadopago.test_public_key')
                : config('services.mercadopago.public_key')),
        ]);
    }

    public function processPayment(Request $request, $planId, MercadoPagoService $mpService)
    {
        if (!isset($this->plans[$planId])) {
            abort(404);
        }

        if (Auth::check() && Auth::user()?->role === 'personal') {
            return $this->processRenew($request, $planId, $mpService);
        }

        $plan = $this->plans[$planId];
        $paymentMethod = $request->input('payment_method', 'pix');
        if ($paymentMethod === 'card') {
            $paymentMethod = 'credit_card';
        }
        $request->merge(['payment_method' => $paymentMethod]);

        $rules = [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf'            => ['required', 'string', 'unique:users,cpf', new Cpf],
            'birth_date'     => ['required', 'date'],
            'gender'         => ['required', 'string'],
            'phone'          => ['required', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:255'],
            'profession'     => ['required', 'string', 'max:255'],
            'cref'           => ['required', 'string', 'max:30'],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
            'payment_method' => ['required', 'in:pix,credit_card'],
        ];

        if ($paymentMethod === 'credit_card') {
            $rules['card_token']   = ['required', 'string'];
            $rules['installments'] = ['required', 'integer', 'min:1', 'max:1'];
        }

        $validated = $request->validate($rules);

        return DB::transaction(function () use ($validated, $plan, $planId, $paymentMethod, $request, $mpService) {
            $user = User::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'cpf'          => preg_replace('/[^0-9]/', '', $validated['cpf']),
                'birth_date'   => $validated['birth_date'],
                'gender'       => $validated['gender'],
                'phone'        => $validated['phone'],
                'address'      => $validated['address'] ?? null,
                'profession'   => $validated['profession'] ?? null,
                'cref'         => $validated['cref'] ?? null,
                'password'     => Hash::make($validated['password']),
                'role'         => 'personal',
                'is_active'    => false,
                'plan_name'    => $plan['name'],
                'max_students' => $plan['max_students'],
            ]);

            $subscription = ProfessionalSubscription::create([
                'user_id'      => $user->id,
                'plan_id'      => $planId,
                'plan_name'    => $plan['name'],
                'max_students' => $plan['max_students'],
                'price'        => $plan['price'],
                'status'       => 'pending',
            ]);

            $externalRef = Str::uuid()->toString();
            $transaction = SubscriptionTransaction::create([
                'subscription_id'       => $subscription->id,
                'user_id'               => $user->id,
                'plan_id'               => $planId,
                'amount'                => $plan['price'],
                'payment_method'        => $paymentMethod,
                'status'                => 'pending',
                'mp_external_reference' => $externalRef,
            ]);

            // PIX: login imediato para ver a tela de espera
            if ($paymentMethod === 'pix') {
                Auth::login($user);
            }

            try {
                return $this->processMP($mpService, $user, $plan, $transaction, $paymentMethod, $request);
            } catch (\Exception $e) {
                // Rollback em caso de falha na criação do pagamento/preapproval
                if (Auth::check() && Auth::id() === $user->id) {
                    Auth::logout();
                }
                $transaction->delete();
                $subscription->delete();
                $user->delete();

                $apiError = $mpService->extractApiErrorDetails($e);
                return back()->withInput()->withErrors([
                    'payment' => 'Erro ao processar pagamento: ' . ($apiError ?: $e->getMessage()),
                ]);
            }
        });
    }

    /**
     * Processa somente teste de cartao (sem criar conta/assinatura).
     */
    public function processCardTest(Request $request, $planId, MercadoPagoService $mpService)
    {
        if (!isset($this->plans[$planId])) {
            abort(404);
        }

        $plan = $this->plans[$planId];

        $validated = $request->validate([
            'card_token'   => ['required', 'string'],
            'installments' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        // Usar email genérico não vinculado ao vendedor para evitar "Payer email forbidden" no sandbox
        $payerEmail = (string) env('MP_CARD_TEST_PAYER_EMAIL', 'card_test_buyer@apexpro.com.br');
        $payerCpf   = (string) env('MP_SANDBOX_PAYER_CPF', '12345678909');

        try {
            $externalRef = 'cardtest-' . Str::uuid()->toString();

            Log::info('Card test runtime context', [
                'mode'            => config('services.mercadopago.mode'),
                'test_token_len'  => strlen((string) config('services.mercadopago.test_access_token')),
                'live_token_len'  => strlen((string) config('services.mercadopago.access_token')),
                'test_pk_prefix'  => substr((string) config('services.mercadopago.test_public_key'), 0, 7),
                'live_pk_prefix'  => substr((string) config('services.mercadopago.public_key'), 0, 7),
                'payer_email'     => $payerEmail,
            ]);

            $result = $mpService->createCardPaymentForTest(
                $plan,
                (string) $validated['card_token'],
                (int) $validated['installments'],
                $payerEmail,
                $payerCpf,
                $externalRef
            );

            Log::info('Card test payment response', [
                'status'             => $result['status'],
                'status_detail'      => $result['status_detail'] ?? null,
                'mp_payment_id'      => $result['mp_payment_id'],
                'external_reference' => $externalRef,
            ]);

            return response()->json([
                'ok'            => true,
                'status'        => $result['status'],
                'status_detail' => $result['status_detail'] ?? null,
                'mp_payment_id' => $result['mp_payment_id'],
            ]);
        } catch (\Throwable $e) {
            $apiError = $mpService->extractApiErrorDetails($e) ?: $e->getMessage();
            Log::warning('Card test payment error', [
                'error'          => $apiError,
                'mode'           => config('services.mercadopago.mode'),
                'test_token_len' => strlen((string) config('services.mercadopago.test_access_token')),
                'live_token_len' => strlen((string) config('services.mercadopago.access_token')),
                'payer_email'    => $payerEmail,
            ]);

            return response()->json(['ok' => false, 'error' => $apiError], 422);
        }
    }

    public function showRenew()
    {
        return view('subscription.renew', ['plans' => $this->plans]);
    }

    public function renewCheckout($planId)
    {
        if (!isset($this->plans[$planId])) {
            abort(404, 'Plano nao encontrado');
        }

        return view('plans.checkout', [
            'plan'            => $this->plans[$planId],
            'isRenewal'       => true,
            'defaultMethod'   => request()->query('method', 'pix'),
            'mpPublicKey'     => ((string) config('services.mercadopago.mode', 'live') === 'test'
                ? config('services.mercadopago.test_public_key')
                : config('services.mercadopago.public_key')),
        ]);
    }

    public function processRenew(Request $request, $planId, MercadoPagoService $mpService)
    {
        if (!isset($this->plans[$planId])) {
            abort(404);
        }

        $plan = $this->plans[$planId];
        $paymentMethod = $request->input('payment_method', 'pix');
        if ($paymentMethod === 'card') {
            $paymentMethod = 'credit_card';
        }
        $request->merge(['payment_method' => $paymentMethod]);

        $rules = ['payment_method' => ['required', 'in:pix,credit_card']];
        if ($paymentMethod === 'credit_card') {
            $rules['card_token']   = ['required', 'string'];
            $rules['installments'] = ['required', 'integer', 'min:1', 'max:1'];
        }
        $request->validate($rules);

        /** @var User $user */
        $user = Auth::user();

        return DB::transaction(function () use ($user, $plan, $planId, $paymentMethod, $request, $mpService) {
            // Cancelar preapproval antigo se existir (troca de plano ou renovação manual)
            $existingSubscription = ProfessionalSubscription::where('user_id', $user->id)->first();
            if ($existingSubscription && !empty($existingSubscription->mp_preapproval_id)) {
                try {
                    $mpService->cancelPreapproval($existingSubscription->mp_preapproval_id);
                } catch (\Throwable $e) {
                    Log::warning('Falha ao cancelar preapproval antigo na renovacao', [
                        'preapproval_id' => $existingSubscription->mp_preapproval_id,
                        'error'          => $e->getMessage(),
                    ]);
                }
            }

            $subscription = ProfessionalSubscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id'               => $planId,
                    'plan_name'             => $plan['name'],
                    'max_students'          => $plan['max_students'],
                    'price'                 => $plan['price'],
                    'status'                => 'pending',
                    'mp_preapproval_id'     => null,
                    'mp_preapproval_status' => null,
                    'next_billing_at'       => null,
                ]
            );

            $externalRef = Str::uuid()->toString();
            $transaction = SubscriptionTransaction::create([
                'subscription_id'       => $subscription->id,
                'user_id'               => $user->id,
                'plan_id'               => $planId,
                'amount'                => $plan['price'],
                'payment_method'        => $paymentMethod,
                'status'                => 'pending',
                'mp_external_reference' => $externalRef,
            ]);

            return $this->processMP($mpService, $user, $plan, $transaction, $paymentMethod, $request);
        });
    }

    protected function processMP(MercadoPagoService $mpService, User $user, array $plan, SubscriptionTransaction $transaction, string $paymentMethod, Request $request)
    {
        // ── PIX ──────────────────────────────────────────────────────────────
        if ($paymentMethod === 'pix') {
            $result = $mpService->createPixPayment($user, $plan, $transaction->mp_external_reference);

            $transaction->update([
                'mp_payment_id'      => $result['mp_payment_id'],
                'pix_qr_code'        => $result['qr_code'],
                'pix_qr_code_base64' => $result['qr_code_base64'],
                'pix_expires_at'     => Carbon::parse($result['expires_at']),
                'mp_raw_response'    => $result['raw_response'],
            ]);

            return redirect()->route('subscription.pix-waiting', ['ref' => $transaction->mp_external_reference]);
        }

        // ── CARTÃO: fluxo híbrido ─────────────────────────────────────────────
        $cardToken   = (string) $request->input('card_token');
        $installments = (int) $request->input('installments', 1);

        // 1. Criar/buscar customer no MP (best-effort; se falhar, segue sem customer)
        $customerId = null;
        $cardId     = null;
        try {
            $customerId = $mpService->createOrGetCustomer($user->email);
        } catch (\Throwable $e) {
            Log::warning('MP: falha ao criar/buscar customer', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        // 2. Salvar cartão ao customer (best-effort)
        if ($customerId) {
            try {
                $cardId = $mpService->saveCardToCustomer($customerId, $cardToken);
            } catch (\Throwable $e) {
                Log::warning('MP: falha ao salvar cartao no customer', [
                    'customer_id' => $customerId,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        // 3. Processar primeiro pagamento via /v1/payments (transparente)
        $result = $mpService->createCardPayment(
            $user,
            $plan,
            $cardToken,
            $installments,
            $transaction->mp_external_reference
        );

        $transaction->update([
            'mp_payment_id'   => $result['mp_payment_id'],
            'status'          => $result['status'],
            'mp_status_detail' => $result['status_detail'] ?? null,
            'mp_raw_response' => $result['raw_response'],
        ]);

        if ($result['status'] === 'approved') {
            // Pagamento aprovado imediatamente → ativar agora
            $transaction->update(['paid_at' => Carbon::now()]);
            $this->activateSubscription($transaction->fresh());
            $user->refresh();
            Auth::login($user->fresh());

            // [best-effort] Criar preapproval com card_id para recorrência automática
            if ($customerId && $cardId) {
                try {
                    $preRef    = 'pre-' . $transaction->mp_external_reference;
                    $preResult = $mpService->createPreapprovalWithCardId(
                        $user,
                        $plan,
                        $customerId,
                        $cardId,
                        $preRef
                    );

                    $transaction->update(['mp_preapproval_id' => $preResult['preapproval_id']]);

                    $subscription = $transaction->subscription;
                    if ($subscription) {
                        $subscription->update([
                            'mp_preapproval_id'     => $preResult['preapproval_id'],
                            'mp_preapproval_status' => $preResult['status'],
                            'next_billing_at'       => !empty($preResult['next_payment_date'])
                                ? Carbon::parse($preResult['next_payment_date'])
                                : Carbon::now()->addDays(30),
                        ]);
                    }

                    Log::info('MP: preapproval criado apos pagamento', [
                        'preapproval_id' => $preResult['preapproval_id'],
                        'status'         => $preResult['status'],
                        'user_id'        => $user->id,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('MP: falha ao criar preapproval pos-pagamento (best-effort)', [
                        'user_id'     => $user->id,
                        'customer_id' => $customerId,
                        'card_id'     => $cardId,
                        'error'       => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->route('personal.dashboard')
                ->with('success', 'Assinatura ativada! Bem-vindo ao ApexPro.');
        }

        if ($result['status'] === 'in_process' || $result['status'] === 'pending') {
            // Pagamento em análise (3DS / antifraude) → manter pendente, aguardar webhook
            Auth::login($user);
            Log::info('MP: pagamento em analise, aguardando webhook', [
                'payment_id' => $result['mp_payment_id'],
                'status'     => $result['status'],
                'user_id'    => $user->id,
            ]);
            return redirect()->route('subscription.payment-result', ['ref' => $transaction->mp_external_reference])
                ->with('info', 'Seu pagamento está sendo analisado pelo banco. Você receberá acesso assim que for confirmado. Aguarde nesta tela.');
        }

        // Qualquer outro status (rejected etc.) → rollback via exception
        $detail = $result['status_detail'] ?? $result['status'];
        throw new \RuntimeException('Pagamento com cartao nao aprovado: ' . $detail);
    }

    protected function activateSubscription(SubscriptionTransaction $transaction): void
    {
        $this->activateSubscriptionPublic($transaction);
    }

    public function activateSubscriptionPublic(SubscriptionTransaction $transaction): void
    {
        $subscription = $transaction->subscription;
        if (!$subscription) {
            return;
        }

        $now       = Carbon::now();
        $graceDays = (int) config('services.mercadopago.grace_period_days', 5);

        $updateData = [
            'status'              => 'active',
            'starts_at'           => $now,
            'expires_at'          => $now->copy()->addDays(30),
            'grace_until'         => $now->copy()->addDays(30 + $graceDays),
            'last_payment_method' => $transaction->payment_method,
            'last_paid_at'        => $now,
            'next_billing_at'     => $subscription->next_billing_at ?? $now->copy()->addDays(30),
        ];

        if ($transaction->payment_method === 'credit_card') {
            $updateData['mp_preapproval_status'] = 'authorized';
        }

        $subscription->update($updateData);

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

    public function pixWaiting(string $ref)
    {
        $transaction = SubscriptionTransaction::where('mp_external_reference', $ref)->firstOrFail();
        return view('subscription.pix-waiting', compact('transaction'));
    }

    public function paymentResult(string $ref, MercadoPagoService $mpService)
    {
        $transaction = SubscriptionTransaction::where('mp_external_reference', $ref)->firstOrFail();

        if ($transaction->status !== 'approved') {
            try {
                // Preapproval (cartão recorrente)
                if (!empty($transaction->mp_preapproval_id)) {
                    $info = $mpService->getPreapproval($transaction->mp_preapproval_id);
                    $remoteStatus = match ($info['status'] ?? '') {
                        'authorized' => 'approved',
                        'pending'    => 'pending',
                        default      => $info['status'] ?? null,
                    };
                } elseif (!empty($transaction->mp_payment_id)) {
                    // PIX / pagamento avulso
                    $paymentInfo  = $mpService->getPaymentStatus((string) $transaction->mp_payment_id);
                    $remoteStatus = $paymentInfo['status'] ?? null;
                } else {
                    $remoteStatus = null;
                }

                if ($remoteStatus === 'approved') {
                    $transaction->update(['status' => 'approved', 'paid_at' => Carbon::now()]);
                    $this->activateSubscription($transaction->fresh());
                } elseif ($remoteStatus && $remoteStatus !== 'approved') {
                    $transaction->update(['status' => in_array($remoteStatus, ['in_process']) ? 'pending' : $remoteStatus]);
                }
            } catch (\Throwable $e) {
                Log::warning('Falha ao sincronizar status no paymentResult', [
                    'ref'   => $ref,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $transaction->refresh();

        if ($transaction->status === 'approved') {
            if (!Auth::check()) {
                Auth::login($transaction->subscription->user);
            }
            return redirect()->route('personal.dashboard')
                ->with('success', 'Pagamento confirmado! Bem-vindo ao ApexPro.');
        }

        return view('subscription.payment-result', compact('transaction'));
    }

    public function checkStatus(string $ref, MercadoPagoService $mpService)
    {
        $transaction = SubscriptionTransaction::where('mp_external_reference', $ref)->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found', 'is_approved' => false]);
        }

        if ($transaction->status !== 'approved') {
            try {
                if (!empty($transaction->mp_preapproval_id)) {
                    $info = $mpService->getPreapproval($transaction->mp_preapproval_id);
                    $remoteStatus = match ($info['status'] ?? '') {
                        'authorized' => 'approved',
                        'pending'    => 'pending',
                        default      => $info['status'] ?? null,
                    };
                } elseif (!empty($transaction->mp_payment_id)) {
                    $paymentInfo  = $mpService->getPaymentStatus((string) $transaction->mp_payment_id);
                    $remoteStatus = $paymentInfo['status'] ?? null;
                } else {
                    $remoteStatus = null;
                }

                if ($remoteStatus === 'approved') {
                    $transaction->update(['status' => 'approved', 'paid_at' => Carbon::now()]);
                    $this->activateSubscription($transaction->fresh());
                } elseif ($remoteStatus && $remoteStatus !== 'approved') {
                    $transaction->update(['status' => in_array($remoteStatus, ['in_process']) ? 'pending' : $remoteStatus]);
                }
            } catch (\Throwable $e) {
                Log::warning('Falha ao sincronizar status MP via polling', [
                    'ref'   => $ref,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $transaction->refresh();

        return response()->json([
            'status'      => $transaction->status,
            'is_approved' => $transaction->status === 'approved',
        ]);
    }

    public function cancelSubscription(Request $request, MercadoPagoService $mpService)
    {
        /** @var User $user */
        $user         = Auth::user();
        $subscription = ProfessionalSubscription::where('user_id', $user->id)->first();

        if (!$subscription) {
            return back()->withErrors(['cancel' => 'Nenhuma assinatura encontrada.']);
        }

        if (!empty($subscription->mp_preapproval_id)) {
            try {
                $mpService->cancelPreapproval($subscription->mp_preapproval_id);
            } catch (\Throwable $e) {
                Log::warning('Falha ao cancelar preapproval no MP', [
                    'preapproval_id' => $subscription->mp_preapproval_id,
                    'user_id'        => $user->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }

        $subscription->update([
            'status'                => 'cancelled',
            'mp_preapproval_status' => 'cancelled',
        ]);

        Log::info('Subscription cancelled by user', ['user_id' => $user->id, 'subscription_id' => $subscription->id]);

        $expiresAt = $subscription->expires_at
            ? $subscription->expires_at->format('d/m/Y')
            : null;

        $message = $expiresAt
            ? "Assinatura cancelada. Seu acesso permanece ativo ate {$expiresAt}."
            : 'Assinatura cancelada.';

        return redirect()->route('personal.dashboard')->with('info', $message);
    }

    public function history()
    {
        /** @var User $user */
        $user = Auth::user();

        $transactions = SubscriptionTransaction::where('user_id', $user->id)->latest()->get();
        return view('personal.subscription.history', compact('transactions'));
    }
}
