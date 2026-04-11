<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalSubscription;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Rules\Cpf;
use App\Services\AsaasService;
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
    protected function signupTrialDays(): int
    {
        return max(0, (int) config('services.mercadopago.signup_trial_days', 0));
    }

    public function __construct()
    {
        $this->loadPlans();
    }

    private function loadPlans(): void
    {
        try {
            $dbPlans = \App\Models\PlanConfig::where('is_active', true)->get();
            if ($dbPlans->isNotEmpty()) {
                foreach ($dbPlans as $plan) {
                    $this->plans[$plan->plan_id] = [
                        'id'               => $plan->plan_id,
                        'name'             => $plan->name,
                        'price'            => (float) $plan->effectivePrice(),
                        'original_price'   => (float) $plan->price,
                        'max_students'     => $plan->max_students,
                        'color'            => $plan->color,
                        'features'         => $plan->features,
                        'discount_percent' => $plan->hasActiveDiscount() ? $plan->discount_percent : null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // fallback to hardcoded plans
        }
    }

    protected $plans = [
        'plan_starter' => [
            'id'           => 'plan_starter',
            'name'         => 'Starter',
            'price'        => 3.00,
            'max_students' => 15,
            'color'        => '#3b82f6',
            'features'     => [
                'Até 15 alunos ativos',
                'Prescrição de treinos completa',
                'Medidas e avaliações corporais',
                'Acompanhamento de evolução com gráficos',
                'App exclusivo para o aluno',
                'Geração de treinos com IA',
                'Avaliação postural com IA',
                'Suporte por e-mail',
            ],
        ],
        'plan_pro' => [
            'id'           => 'plan_pro',
            'name'         => 'Pro',
            'price'        => 4.00,
            'max_students' => 50,
            'color'        => '#8b5cf6',
            'features'     => [
                'Até 50 alunos ativos',
                'Prescrição de treinos completa',
                'Medidas e avaliações corporais',
                'Acompanhamento de evolução com gráficos',
                'App exclusivo para o aluno',
                'Controle financeiro dos alunos',
                'Geração de treinos com IA',
                'Avaliação postural com IA',
                'Relatórios em PDF e Excel',
                'Suporte prioritário',
            ],
        ],
        'plan_elite' => [
            'id'           => 'plan_elite',
            'name'         => 'Elite',
            'price'        => 5.00,
            'max_students' => 100,
            'color'        => '#f59e0b',
            'features'     => [
                'A partir de 100 alunos ativos',
                'Prescrição de treinos completa',
                'Medidas e avaliações corporais',
                'Acompanhamento de evolução com gráficos',
                'App exclusivo para o aluno',
                'Controle financeiro dos alunos',
                'Geração de treinos com IA',
                'Avaliação postural com IA',
                'Relatórios em PDF e Excel',
                'Suporte VIP exclusivo',
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
            'trialEnabled'  => true,
            'trialDays'     => (int) config('services.asaas.trial_days', 7),
        ]);
    }

    public function processPayment(Request $request, $planId, AsaasService $asaas)
    {
        if (!isset($this->plans[$planId])) {
            abort(404);
        }

        if (Auth::check() && Auth::user()?->role === 'personal') {
            return $this->processRenew($request, $planId, $asaas);
        }

        $plan          = $this->plans[$planId];
        $paymentMethod = $request->input('payment_method', 'pix');
        if ($paymentMethod === 'card') {
            $paymentMethod = 'credit_card';
        }
        $request->merge(['payment_method' => $paymentMethod]);

        // Normaliza CPF
        if ($request->has('cpf')) {
            $request->merge(['cpf' => preg_replace('/[^0-9]/', '', $request->input('cpf'))]);
        }

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
            $rules['card_holder_name']    = ['required', 'string', 'max:100'];
            $rules['card_number']         = ['required', 'string'];
            $rules['card_expiry_month']   = ['required', 'string'];
            $rules['card_expiry_year']    = ['required', 'string'];
            $rules['card_cvv']            = ['required', 'string', 'min:3', 'max:4'];
            $rules['card_zip']            = ['nullable', 'string'];
            $rules['card_address_number'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        return DB::transaction(function () use ($validated, $plan, $planId, $paymentMethod, $request, $asaas) {
            // PIX: paga primeiro, acessa depois. Cartão: trial de 7 dias imediato.
            $isPixPayment = $paymentMethod === 'pix';
            $trialDays    = $isPixPayment ? 0 : (int) config('services.asaas.trial_days', 7);
            $trialEndsAt  = $trialDays > 0 ? Carbon::now()->addDays($trialDays) : null;

            $user = User::create([
                'name'                    => $validated['name'],
                'email'                   => $validated['email'],
                'cpf'                     => preg_replace('/[^0-9]/', '', $validated['cpf']),
                'birth_date'              => $validated['birth_date'],
                'gender'                  => $validated['gender'],
                'phone'                   => $validated['phone'],
                'address'                 => $validated['address'] ?? null,
                'profession'              => $validated['profession'] ?? null,
                'cref'                    => $validated['cref'] ?? null,
                'password'                => Hash::make($validated['password']),
                'role'                    => 'personal',
                'is_active'               => !$isPixPayment, // PIX: bloqueado até pagar; Cartão: trial imediato
                'plan_name'               => $plan['name'],
                'max_students'            => $plan['max_students'],
                'trial_ends_at'           => $trialEndsAt,
                'subscription_expires_at' => $trialEndsAt,
            ]);

            $subscription = ProfessionalSubscription::create([
                'user_id'       => $user->id,
                'plan_id'       => $planId,
                'plan_name'     => $plan['name'],
                'max_students'  => $plan['max_students'],
                'price'         => $plan['price'],
                'status'        => $isPixPayment ? 'pending' : 'trial',
                'starts_at'     => $isPixPayment ? null : Carbon::now(),
                'expires_at'    => $trialEndsAt,
                'trial_ends_at' => $trialEndsAt,
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

            try {
                return $this->processAsaas($asaas, $user, $plan, $subscription, $transaction, $paymentMethod, $request, isTrial: !$isPixPayment);
            } catch (\Exception $e) {
                if (Auth::check() && Auth::id() === $user->id) {
                    Auth::logout();
                }
                $transaction->delete();
                $subscription->delete();
                $user->delete();

                Log::error('Asaas processPayment failed', ['error' => $e->getMessage()]);
                return back()->withInput()->withErrors([
                    'payment' => 'Erro ao processar pagamento: ' . $e->getMessage(),
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
            'plan'          => $this->plans[$planId],
            'isRenewal'     => true,
            'defaultMethod' => request()->query('method', 'pix'),
            'trialEnabled'  => false,
            'trialDays'     => 0,
        ]);
    }

    public function processRenew(Request $request, $planId, AsaasService $asaas)
    {
        if (!isset($this->plans[$planId])) {
            abort(404);
        }

        $plan          = $this->plans[$planId];
        $paymentMethod = $request->input('payment_method', 'pix');
        if ($paymentMethod === 'card') {
            $paymentMethod = 'credit_card';
        }
        $request->merge(['payment_method' => $paymentMethod]);

        $rules = ['payment_method' => ['required', 'in:pix,credit_card']];
        $request->validate($rules);

        /** @var User $user */
        $user = Auth::user();

        return DB::transaction(function () use ($user, $plan, $planId, $paymentMethod, $request, $asaas) {
            $existingSubscription = ProfessionalSubscription::where('user_id', $user->id)->first();

            // Trial ainda ativo → não faz nada
            if ($existingSubscription && $existingSubscription->isInTrial()) {
                return redirect()->route('personal.dashboard')
                    ->with('info', 'Você está no período de teste gratuito até ' . $existingSubscription->trial_ends_at->format('d/m/Y') . '. A cobrança inicia automaticamente após esse período.');
            }

            // Assinatura já ativa para o mesmo plano → não renova (ignorar se cancelada)
            if ($existingSubscription
                && $existingSubscription->status !== 'cancelled'
                && $existingSubscription->plan_id === $planId
                && $existingSubscription->canAccessPlatform()) {
                return redirect()->route('personal.dashboard')
                    ->with('info', 'Sua assinatura já está ativa até ' . $existingSubscription->expires_at?->format('d/m/Y') . '.');
            }

            // Cancela assinatura Asaas anterior se existir
            if ($existingSubscription && $existingSubscription->asaas_subscription_id) {
                try {
                    $asaas->cancelSubscription($existingSubscription->asaas_subscription_id);
                } catch (\Throwable $e) {
                    Log::warning('Falha ao cancelar assinatura Asaas antiga', [
                        'asaas_subscription_id' => $existingSubscription->asaas_subscription_id,
                        'error'                 => $e->getMessage(),
                    ]);
                }
            }

            $keepAccessDuringRenewal = $existingSubscription?->canAccessPlatform() ?? false;

            $subscription = ProfessionalSubscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id'                => $planId,
                    'plan_name'              => $plan['name'],
                    'max_students'           => $plan['max_students'],
                    'price'                  => $plan['price'],
                    'status'                 => $keepAccessDuringRenewal ? 'active' : 'pending',
                    'asaas_subscription_id'  => null,
                    'mp_preapproval_id'      => null,
                    'mp_preapproval_status'  => null,
                    'next_billing_at'        => null,
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

            return $this->processAsaas($asaas, $user, $plan, $subscription, $transaction, $paymentMethod, $request, isTrial: false);
        });
    }

    // ── Asaas ─────────────────────────────────────────────────────────────────

    protected function processAsaas(
        AsaasService $asaas,
        User $user,
        array $plan,
        ProfessionalSubscription $subscription,
        SubscriptionTransaction $transaction,
        string $paymentMethod,
        Request $request,
        bool $isTrial = false
    ) {
        // Trial só para quem NUNCA teve assinatura antes (primeira vez)
        $alreadyHadSubscription = ProfessionalSubscription::where('user_id', $user->id)
            ->where('id', '!=', $subscription->id)
            ->exists();

        $trialDays = ($isTrial && !$alreadyHadSubscription)
            ? (int) config('services.asaas.trial_days', 7)
            : 0;

        // Se o usuário reativou e ainda tem acesso válido de um plano anterior cancelado,
        // a primeira cobrança só ocorre quando esse acesso expirar (sem cobrar em dobro)
        $previousSub = ProfessionalSubscription::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if ($previousSub && $previousSub->expires_at->isFuture()) {
            $nextDueDate = $previousSub->expires_at->format('Y-m-d');
        } else {
            $nextDueDate = Carbon::now()->addDays($trialDays)->format('Y-m-d');
        }

        // 1. Criar/buscar cliente Asaas
        $customer = $asaas->createOrFindCustomer([
            'name'  => $user->name,
            'email' => $user->email,
            'cpf'   => $user->cpf,
            'phone' => $user->phone ?? '',
        ]);
        $customerId = $customer['id'];

        // 2. Salvar customer_id na subscription
        $subscription->update(['asaas_customer_id' => $customerId]);

        // 3. Montar billing type
        $billingType = match ($paymentMethod) {
            'credit_card' => 'CREDIT_CARD',
            'boleto'      => 'BOLETO',
            default       => 'PIX',
        };

        // 3.1 Tokenizar cartão se necessário
        $cardToken = null;
        if ($paymentMethod === 'credit_card') {
            $cardToken = $asaas->tokenizeCreditCard($customerId, [
                'holder_name'  => $request->input('card_holder_name'),
                'number'       => preg_replace('/\D/', '', $request->input('card_number', '')),
                'expiry_month' => $request->input('card_expiry_month'),
                'expiry_year'  => $request->input('card_expiry_year'),
                'ccv'          => $request->input('card_cvv'),
            ], [
                'name'           => $user->name,
                'email'          => $user->email,
                'cpf'            => $user->cpf,
                'zip'            => preg_replace('/\D/', '', $request->input('card_zip', '01310100')),
                'address_number' => $request->input('card_address_number', '0'),
                'phone'          => $user->phone ?? '',
            ]);
        }

        // 4. Criar assinatura Asaas
        $asaasSubscription = $asaas->createSubscription([
            'customer_id'        => $customerId,
            'billing_type'       => $billingType,
            'value'              => $plan['price'],
            'next_due_date'      => $nextDueDate,
            'description'        => 'Plano ' . $plan['name'] . ' - ApexPro AI',
            'external_reference' => $transaction->mp_external_reference,
            'credit_card_token'  => $cardToken,
        ]);

        // 5. Salvar asaas_subscription_id
        $subscription->update([
            'asaas_subscription_id' => $asaasSubscription['id'],
            'next_billing_at'       => Carbon::parse($nextDueDate),
        ]);

        $transaction->update([
            'asaas_raw_response' => $asaasSubscription,
        ]);

        Log::info('Asaas: assinatura criada', [
            'asaas_id'   => $asaasSubscription['id'],
            'user_id'    => $user->id,
            'is_trial'   => $isTrial,
            'due_date'   => $nextDueDate,
            'billing'    => $billingType,
        ]);

        // 6. Para PIX/BOLETO → buscar QR Code do primeiro pagamento
        if (in_array($paymentMethod, ['pix', 'boleto'])) {
            try {
                $payments = $asaas->getSubscriptionPayments($asaasSubscription['id']);
                if (!empty($payments)) {
                    $firstPayment = $payments[0];
                    $transaction->update(['asaas_payment_id' => $firstPayment['id']]);

                    if ($paymentMethod === 'pix') {
                        $pixData = $asaas->getPixQrCode($firstPayment['id']);
                        $transaction->update([
                            'pix_qr_code'        => $pixData['payload'] ?? null,
                            'pix_qr_code_base64' => $pixData['encodedImage'] ?? null,
                            'pix_expires_at'     => !empty($pixData['expirationDate'])
                                                     ? Carbon::parse($pixData['expirationDate'])
                                                     : Carbon::now()->addDays($trialDays + 1),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Asaas: não foi possível obter QR Code do primeiro pagamento', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Trial: login imediato + acesso liberado + mostra QR para pagar quando quiser
            Auth::login($user->fresh());

            if ($isTrial) {
                return redirect()->route('personal.dashboard')
                    ->with('success', "🎉 Seu período de teste de {$trialDays} dias está liberado! A cobrança do plano começa em " . Carbon::parse($nextDueDate)->format('d/m/Y') . '. Você receberá o link de pagamento por e-mail.');
            }

            return redirect()->route('subscription.pix-waiting', ['ref' => $transaction->mp_external_reference]);
        }

        // 7. Para cartão → acesso liberado (cobrança automática no vencimento)
        Auth::login($user->fresh());

        if ($isTrial) {
            return redirect()->route('personal.dashboard')
                ->with('success', "🎉 Seu período de teste de {$trialDays} dias está liberado! O cartão será cobrado automaticamente em " . Carbon::parse($nextDueDate)->format('d/m/Y') . '.');
        }

        return redirect()->route('personal.dashboard')
            ->with('success', 'Assinatura criada! O cartão será cobrado automaticamente na data de vencimento.');
    }

    // ── MercadoPago (mantido para compatibilidade) ─────────────────────────────

    protected function processMP(MercadoPagoService $mpService, User $user, array $plan, SubscriptionTransaction $transaction, string $paymentMethod, Request $request, bool $isTrial = false)
    {
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

        $cardToken = (string) $request->input('card_token');
        $trialDays = $this->signupTrialDays();
        $preRef = 'sub-' . $transaction->mp_external_reference;

        try {
            if ($isTrial) {
                $mpPlanId = $mpService->createOrGetPlan($plan, $trialDays);
                $subResult = $mpService->createSubscriptionWithPlan($user, $mpPlanId, $cardToken, $preRef);
            } else {
                $subResult = $mpService->createSubscriptionWithToken($user, $plan, $cardToken, $preRef);
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro ao criar assinatura: ' . $e->getMessage());
        }

        $authorized = ($subResult['status'] ?? null) === 'authorized';
        $nextBilling = !empty($subResult['next_payment_date'])
            ? Carbon::parse($subResult['next_payment_date'])
            : ($isTrial ? Carbon::now()->addDays($trialDays) : Carbon::now()->addDays(30));

        $transaction->update([
            'status'            => ($isTrial && $authorized) ? 'approved' : 'pending',
            'mp_preapproval_id' => $subResult['preapproval_id'],
            'mp_status_detail'  => $subResult['status'],
            'mp_raw_response'   => $subResult['raw_response'],
            'paid_at'           => ($isTrial && $authorized) ? Carbon::now() : null,
        ]);

        $subscription = $transaction->fresh()->subscription;
        if ($subscription) {
            $subscription->update([
                'mp_preapproval_id'     => $subResult['preapproval_id'],
                'mp_preapproval_status' => $subResult['status'],
                'mp_card_id'            => $subResult['card_id'] ?? null,
                'next_billing_at'       => $nextBilling,
            ]);
        }

        Log::info('MP: assinatura criada com sucesso', [
            'preapproval_id' => $subResult['preapproval_id'],
            'status'         => $subResult['status'],
            'is_trial'       => $isTrial,
            'next_payment'   => $subResult['next_payment_date'] ?? null,
            'user_id'        => $user->id,
        ]);

        if ($authorized && $isTrial) {
            $this->activateSubscriptionUntil($transaction->fresh(), Carbon::now()->addDays($trialDays));
            $transaction->update([
                'amount'  => 0,
                'status'  => 'approved',
                'paid_at' => Carbon::now(),
            ]);

            Auth::login($user->fresh());

            return redirect()->route('personal.dashboard')->with(
                'success',
                "Seu periodo gratuito de {$trialDays} dia(s) esta liberado! A cobranca do plano inicia automaticamente apos o trial."
            );
        }

        Auth::login($user->fresh());

        return redirect()->route('subscription.payment-result', ['ref' => $transaction->mp_external_reference])
            ->with(
                'info',
                $isTrial
                    ? 'Sua assinatura esta sendo processada. Aguarde a confirmacao.'
                    : 'Assinatura criada. O acesso sera liberado apos a confirmacao da primeira cobranca.'
            );
    }

    protected function activateSubscription(SubscriptionTransaction $transaction): void
    {
        $this->activateSubscriptionPublic($transaction);
    }

    protected function activateSubscriptionForDays(SubscriptionTransaction $transaction, int $days): void
    {
        $this->activateSubscriptionPublic($transaction, $days);
    }

    protected function activateSubscriptionUntil(SubscriptionTransaction $transaction, Carbon $until): void
    {
        $subscription = $transaction->subscription;
        if (!$subscription) return;

        $now = Carbon::now();

        $subscription->update([
            'status'              => 'active',
            'starts_at'           => $now,
            'expires_at'          => $until,
            'last_payment_method' => $transaction->payment_method,
            'last_paid_at'        => $now,
            'next_billing_at'     => $until,
        ]);

        $user = $subscription->user;
        if ($user) {
            $user->update([
                'subscription_expires_at' => $until,
                'is_active'               => true,
                'plan_name'               => $subscription->plan_name,
                'max_students'            => $subscription->max_students,
            ]);
        }
    }

    public function activateSubscriptionPublic(SubscriptionTransaction $transaction, int $days = 30): void
    {
        $subscription = $transaction->subscription;
        if (!$subscription) {
            return;
        }

        $now = Carbon::now();

        $updateData = [
            'status'              => 'active',
            'starts_at'           => $now,
            'expires_at'          => $now->copy()->addDays($days),
            'last_payment_method' => $transaction->payment_method,
            'last_paid_at'        => $now,
            'next_billing_at'     => $subscription->next_billing_at ?? $now->copy()->addDays($days),
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

    public function paymentResult(string $ref, AsaasService $asaas)
    {
        $transaction = SubscriptionTransaction::where('mp_external_reference', $ref)->firstOrFail();

        if ($transaction->status !== 'approved') {
            try {
                $asaasPaymentId = $transaction->asaas_payment_id;
                if ($asaasPaymentId) {
                    $payment      = $asaas->getPayment($asaasPaymentId);
                    $remoteStatus = match ($payment['status'] ?? '') {
                        'RECEIVED', 'CONFIRMED' => 'approved',
                        'OVERDUE', 'REFUNDED'   => 'rejected',
                        default                 => 'pending',
                    };

                    if ($remoteStatus === 'approved') {
                        $transaction->update(['status' => 'approved', 'paid_at' => Carbon::now()]);
                        $this->activateSubscription($transaction->fresh());
                    } elseif ($remoteStatus !== 'pending') {
                        $transaction->update(['status' => $remoteStatus]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('paymentResult Asaas error', ['ref' => $ref, 'error' => $e->getMessage()]);
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

    public function checkStatus(string $ref, AsaasService $asaas)
    {
        $transaction = SubscriptionTransaction::where('mp_external_reference', $ref)->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found', 'is_approved' => false]);
        }

        if ($transaction->status !== 'approved') {
            try {
                $asaasPaymentId = $transaction->asaas_payment_id;
                if ($asaasPaymentId) {
                    $payment      = $asaas->getPayment($asaasPaymentId);
                    $remoteStatus = match ($payment['status'] ?? '') {
                        'RECEIVED', 'CONFIRMED' => 'approved',
                        'OVERDUE', 'REFUNDED'   => 'rejected',
                        default                 => 'pending',
                    };

                    if ($remoteStatus === 'approved') {
                        $transaction->update(['status' => 'approved', 'paid_at' => Carbon::now()]);
                        $this->activateSubscription($transaction->fresh());
                    } elseif ($remoteStatus === 'rejected') {
                        $transaction->update(['status' => 'rejected']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('checkStatus Asaas error', ['ref' => $ref, 'error' => $e->getMessage()]);
            }
        }

        $transaction->refresh();

        return response()->json([
            'status'      => $transaction->status,
            'is_approved' => $transaction->status === 'approved',
        ]);
    }

    public function cancelSubscription(Request $request, AsaasService $asaas)
    {
        /** @var User $user */
        $user         = Auth::user();
        $subscription = ProfessionalSubscription::where('user_id', $user->id)->first();

        if (!$subscription) {
            return back()->withErrors(['cancel' => 'Nenhuma assinatura encontrada.']);
        }

        // Cancela no Asaas se houver assinatura vinculada
        if (!empty($subscription->asaas_subscription_id)) {
            try {
                $asaas->cancelSubscription($subscription->asaas_subscription_id);
            } catch (\Throwable $e) {
                Log::warning('Falha ao cancelar assinatura no Asaas', [
                    'asaas_subscription_id' => $subscription->asaas_subscription_id,
                    'user_id'               => $user->id,
                    'error'                 => $e->getMessage(),
                ]);
            }
        }

        $subscription->update([
            'status' => 'cancelled',
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
