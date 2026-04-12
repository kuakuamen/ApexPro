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
    protected function billingTimezone(): string
    {
        return 'America/Sao_Paulo';
    }

    protected function billingNow(): Carbon
    {
        return Carbon::now($this->billingTimezone());
    }

    protected function parseAsaasBillingAt(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->timezone($this->billingTimezone());
    }

    protected function syncLatestAsaasSubscription(ProfessionalSubscription $subscription, AsaasService $asaas): void
    {
        $transaction = $subscription->transactions()
            ->whereNotNull('asaas_checkout_id')
            ->latest('id')
            ->first();

        if ($transaction?->asaas_checkout_id) {
            $payment = $asaas->findLatestPaymentByCheckoutId($transaction->asaas_checkout_id);
            if (!empty($payment)) {
                $remoteSubscription = !empty($payment['subscription'])
                    ? $asaas->getSubscription($payment['subscription'])
                    : null;

                $nextBillingAt = $this->parseAsaasBillingAt($remoteSubscription['nextDueDate'] ?? $payment['dueDate'] ?? null);

                $subscription->update([
                    'status' => $subscription->status === 'cancelled' ? 'pending' : $subscription->status,
                    'asaas_customer_id' => $payment['customer'] ?? $subscription->asaas_customer_id,
                    'asaas_subscription_id' => $payment['subscription'] ?? $subscription->asaas_subscription_id,
                    'next_billing_at' => $nextBillingAt ?? $subscription->next_billing_at,
                ]);

                return;
            }
        }

        $cpf = $subscription->user?->cpf;
        if ($transaction?->asaas_checkout_id && $cpf) {
            foreach ($asaas->listCustomersByCpf($cpf) as $customer) {
                if (!empty($customer['deleted'])) {
                    continue;
                }

                $remoteSubscription = collect($asaas->listSubscriptions([
                    'customer' => $customer['id'],
                    'limit' => 20,
                    'offset' => 0,
                ]))->firstWhere('checkoutSession', $transaction->asaas_checkout_id);

                if ($remoteSubscription) {
                    $nextBillingAt = $this->parseAsaasBillingAt($remoteSubscription['nextDueDate'] ?? null);

                    $subscription->update([
                        'status' => $subscription->status === 'cancelled' ? 'pending' : $subscription->status,
                        'asaas_customer_id' => $customer['id'],
                        'asaas_subscription_id' => $remoteSubscription['id'] ?? $subscription->asaas_subscription_id,
                        'next_billing_at' => $nextBillingAt ?? $subscription->next_billing_at,
                    ]);

                    return;
                }
            }
        }

        if (empty($subscription->asaas_customer_id)) {
            return;
        }

        $latestSubscription = $asaas->findLatestSubscriptionByCustomer($subscription->asaas_customer_id);
        if (!$latestSubscription) {
            return;
        }

        $nextBillingAt = $this->parseAsaasBillingAt($latestSubscription['nextDueDate'] ?? null);

        $subscription->update([
            'status' => $subscription->status === 'cancelled' ? 'pending' : $subscription->status,
            'asaas_subscription_id' => $latestSubscription['id'] ?? $subscription->asaas_subscription_id,
            'next_billing_at' => $nextBillingAt ?? $subscription->next_billing_at,
        ]);
    }

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
                'AtÃ© 15 alunos ativos',
                'PrescriÃ§Ã£o de treinos completa',
                'Medidas e avaliaÃ§Ãµes corporais',
                'Acompanhamento de evoluÃ§Ã£o com grÃ¡ficos',
                'App exclusivo para o aluno',
                'GeraÃ§Ã£o de treinos com IA',
                'AvaliaÃ§Ã£o postural com IA',
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
                'AtÃ© 50 alunos ativos',
                'PrescriÃ§Ã£o de treinos completa',
                'Medidas e avaliaÃ§Ãµes corporais',
                'Acompanhamento de evoluÃ§Ã£o com grÃ¡ficos',
                'App exclusivo para o aluno',
                'Controle financeiro dos alunos',
                'GeraÃ§Ã£o de treinos com IA',
                'AvaliaÃ§Ã£o postural com IA',
                'RelatÃ³rios em PDF e Excel',
                'Suporte prioritÃ¡rio',
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
                'PrescriÃ§Ã£o de treinos completa',
                'Medidas e avaliaÃ§Ãµes corporais',
                'Acompanhamento de evoluÃ§Ã£o com grÃ¡ficos',
                'App exclusivo para o aluno',
                'Controle financeiro dos alunos',
                'GeraÃ§Ã£o de treinos com IA',
                'AvaliaÃ§Ã£o postural com IA',
                'RelatÃ³rios em PDF e Excel',
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
            'trialEnabled'  => (int) config('services.asaas.trial_days', 7) > 0,
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

        if ($request->has('cpf')) {
            $request->merge(['cpf' => preg_replace('/[^0-9]/', '', $request->input('cpf'))]);
        }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf'            => ['required', 'string', 'unique:users,cpf', new Cpf],
            'birth_date'     => ['required', 'date'],
            'gender'         => ['required', 'string'],
            'phone'          => ['required', 'string', 'max:20'],
            'address_cep'    => ['required', 'regex:/^\d{5}-\d{3}$/'],
            'address_state'  => ['required', 'string', 'size:2'],
            'address_city'   => ['required', 'string', 'max:255'],
            'address_street' => ['required', 'string', 'max:255'],
            'address_neighborhood' => ['required', 'string', 'max:255'],
            'address_number' => ['required', 'string', 'max:30'],
            'profession'     => ['required', 'string', 'max:255'],
            'cref'           => ['required', 'string', 'max:30'],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
            'payment_method' => ['required', 'in:pix,credit_card'],
        ]);

        return DB::transaction(function () use ($validated, $plan, $planId, $paymentMethod, $request, $asaas) {
            $isPixPayment = $paymentMethod === 'pix';
            $trialDays    = $isPixPayment ? 0 : (int) config('services.asaas.trial_days', 7);
            $isTrial      = !$isPixPayment && $trialDays > 0;
            $trialEndsAt  = $isTrial ? $this->billingNow()->addDays($trialDays) : null;

            $validated['address_state'] = mb_strtoupper($validated['address_state']);
            $validated['address'] = "{$validated['address_street']}, {$validated['address_number']} - {$validated['address_neighborhood']}, {$validated['address_city']}/{$validated['address_state']} - CEP {$validated['address_cep']}";

            $user = User::create([
                'name'                    => $validated['name'],
                'email'                   => $validated['email'],
                'cpf'                     => preg_replace('/[^0-9]/', '', $validated['cpf']),
                'birth_date'              => $validated['birth_date'],
                'gender'                  => $validated['gender'],
                'phone'                   => $validated['phone'],
                'address'                 => $validated['address'],
                'address_cep'             => $validated['address_cep'],
                'address_state'           => $validated['address_state'],
                'address_city'            => $validated['address_city'],
                'address_street'          => $validated['address_street'],
                'address_neighborhood'    => $validated['address_neighborhood'],
                'address_number'          => $validated['address_number'],
                'profession'              => $validated['profession'] ?? null,
                'cref'                    => $validated['cref'] ?? null,
                'password'                => Hash::make($validated['password']),
                'role'                    => 'personal',
                'is_active'               => false,
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
                'status'        => 'pending',
                'starts_at'     => null,
                'expires_at'    => $trialEndsAt,
                'trial_ends_at' => $trialEndsAt,
            ]);

            $transaction = SubscriptionTransaction::create([
                'subscription_id'       => $subscription->id,
                'user_id'               => $user->id,
                'plan_id'               => $planId,
                'amount'                => $plan['price'],
                'payment_method'        => $paymentMethod,
                'status'                => 'pending',
                'mp_external_reference' => Str::uuid()->toString(),
            ]);

            try {
                return $this->processAsaas($asaas, $user, $plan, $subscription, $transaction, $paymentMethod, $request, isTrial: $isTrial);
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

        // Usar email genÃ©rico nÃ£o vinculado ao vendedor para evitar "Payer email forbidden" no sandbox
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

            // Trial ainda ativo â†’ nÃ£o faz nada
            if ($existingSubscription && $existingSubscription->isInTrial()) {
                return redirect()->route('personal.dashboard')
                    ->with('info', 'VocÃª estÃ¡ no perÃ­odo de teste gratuito atÃ© ' . $existingSubscription->trial_ends_at->format('d/m/Y') . '. A cobranÃ§a inicia automaticamente apÃ³s esse perÃ­odo.');
            }

            // Assinatura jÃ¡ ativa para o mesmo plano â†’ nÃ£o renova (ignorar se cancelada)
            if ($existingSubscription
                && $existingSubscription->status !== 'cancelled'
                && $existingSubscription->plan_id === $planId
                && $existingSubscription->canAccessPlatform()) {
                return redirect()->route('personal.dashboard')
                    ->with('info', 'Sua assinatura jÃ¡ estÃ¡ ativa atÃ© ' . $existingSubscription->expires_at?->format('d/m/Y') . '.');
            }

            // NÃ£o cancelamos a assinatura Asaas anterior aqui â€” o cancelamento dispara
            // SUBSCRIPTION_DELETED que conflita com a renovaÃ§Ã£o em andamento.
            // O Asaas mantÃ©m mÃºltiplas assinaturas por cliente sem problema.

            $keepAccessDuringRenewal = $existingSubscription?->canAccessPlatform() ?? false;
            $renewalStatus = $existingSubscription?->status === 'cancelled'
                ? 'cancelled'
                : ($keepAccessDuringRenewal ? 'active' : 'pending');

            // Captura expires_at anterior se era cancelada com acesso ainda vÃ¡lido
            // (usado em processAsaas para nÃ£o cobrar em dobro)
            $previousExpiresAt = ($existingSubscription?->status === 'cancelled'
                && $existingSubscription?->expires_at?->isFuture())
                ? $existingSubscription->expires_at
                : null;

            $subscription = ProfessionalSubscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id'                => $planId,
                    'plan_name'              => $plan['name'],
                    'max_students'           => $plan['max_students'],
                    'price'                  => $plan['price'],
                    'status'                 => $renewalStatus,
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

            try {
                return $this->processAsaas($asaas, $user, $plan, $subscription, $transaction, $paymentMethod, $request, isTrial: false, previousExpiresAt: $previousExpiresAt);
            } catch (\Exception $e) {
                // Desfaz transaÃ§Ã£o criada mas mantÃ©m subscription existente (apenas restaura status anterior)
                $transaction->delete();
                if ($keepAccessDuringRenewal) {
                    $subscription->update(['status' => 'active']);
                } elseif ($existingSubscription?->status === 'cancelled') {
                    $subscription->update(['status' => 'cancelled']);
                }

                Log::error('Asaas processRenew failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                return back()->withInput()->withErrors([
                    'payment' => 'Erro ao processar pagamento: ' . $e->getMessage(),
                ]);
            }
        });
    }

    protected function processAsaas(
        AsaasService $asaas,
        User $user,
        array $plan,
        ProfessionalSubscription $subscription,
        SubscriptionTransaction $transaction,
        string $paymentMethod,
        Request $request,
        bool $isTrial = false,
        ?Carbon $previousExpiresAt = null
    ) {
        $alreadyHadSubscription = ProfessionalSubscription::where('user_id', $user->id)
            ->where('id', '!=', $subscription->id)
            ->exists();

        $trialDays = ($isTrial && !$alreadyHadSubscription)
            ? (int) config('services.asaas.trial_days', 7)
            : 0;

        $nextDueAt = $previousExpiresAt && $previousExpiresAt->isFuture()
            ? $previousExpiresAt->copy()->timezone($this->billingTimezone())
            : $this->billingNow()->addDays($trialDays);

        $customer = $asaas->createOrFindCustomer([
            'name'  => $user->name,
            'email' => $user->email,
            'cpf'   => $user->cpf,
            'phone' => $user->phone ?? '',
            'postal_code' => $user->address_cep ?? '',
            'address' => $user->address_street ?? '',
            'address_number' => $user->address_number ?? '',
            'province' => $user->address_neighborhood ?? '',
        ]);
        $customerId = $customer['id'] ?? null;
        $useSavedCustomer = $customerId && $asaas->customerSupportsCheckout($customer);

        $subscription->update([
            'asaas_customer_id' => $customerId,
            'trial_ends_at' => $trialDays > 0 ? $nextDueAt : null,
            'expires_at' => $trialDays > 0 ? $nextDueAt : $subscription->expires_at,
            'next_billing_at' => $nextDueAt,
        ]);

        $baseResultUrl = route('subscription.payment-result', ['ref' => $transaction->mp_external_reference]);

        $payload = [
            'items' => [[
                'name' => 'Plano ' . $plan['name'] . ' - ApexPro AI',
                'description' => $paymentMethod === 'credit_card'
                    ? 'Assinatura recorrente mensal'
                    : 'Pagamento via PIX',
                'quantity' => 1,
                'value' => $plan['price'],
            ]],
            'billingTypes' => [$paymentMethod === 'credit_card' ? 'CREDIT_CARD' : 'PIX'],
            'chargeTypes' => [$paymentMethod === 'credit_card' ? 'RECURRENT' : 'DETACHED'],
            'minutesToExpire' => max(5, (int) config('services.asaas.checkout_expire_minutes', 60)),
            'callback' => [
                'successUrl' => $baseResultUrl . '?checkout_state=success',
                'cancelUrl' => $baseResultUrl . '?checkout_state=cancelled',
                'expiredUrl' => $baseResultUrl . '?checkout_state=expired',
            ],
        ];

        if ($useSavedCustomer) {
            $payload['customer'] = $customerId;
        } else {
            $payload['customerData'] = [
                'name' => $user->name,
                'email' => $user->email,
                'cpfCnpj' => preg_replace('/\D/', '', (string) $user->cpf),
                'phone' => preg_replace('/\D/', '', (string) ($user->phone ?? '')),
                'postalCode' => preg_replace('/\D/', '', (string) ($user->address_cep ?? '')),
                'address' => $user->address_street ?? '',
                'addressNumber' => $user->address_number ?? '',
                'province' => $user->address_neighborhood ?? '',
            ];
        }

        if ($paymentMethod === 'credit_card') {
            $payload['subscription'] = [
                'cycle' => 'MONTHLY',
                'nextDueDate' => $nextDueAt->format('Y-m-d H:i:s'),
            ];
        }

        $checkout = $asaas->createCheckout($payload);

        $effectiveNextDueAt = $this->parseAsaasBillingAt(data_get($checkout, 'subscription.nextDueDate'));

        if ($effectiveNextDueAt) {
            $subscription->update([
                'trial_ends_at' => $trialDays > 0 ? $effectiveNextDueAt : null,
                'expires_at' => $trialDays > 0 ? $effectiveNextDueAt : $subscription->expires_at,
                'next_billing_at' => $effectiveNextDueAt,
            ]);
        }

        $transaction->update([
            'asaas_checkout_id' => $checkout['id'] ?? null,
            'asaas_raw_response' => $checkout,
            'next_due_at' => ($effectiveNextDueAt ?? $nextDueAt)->toIso8601String(),
        ]);

        Log::info('Asaas: checkout criado', [
            'checkout_id' => $checkout['id'] ?? null,
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'is_trial' => $trialDays > 0,
            'next_due_at' => $nextDueAt->toIso8601String(),
            'effective_next_due_at' => $effectiveNextDueAt?->toIso8601String(),
            'uses_saved_customer' => $useSavedCustomer,
        ]);

        return redirect()->away($asaas->getCheckoutUrl($checkout['id']));
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

        if (!empty($transaction->asaas_payment_id)) {
            $updateData['status'] = 'active';
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

    protected function syncAsaasTransactionStatus(SubscriptionTransaction $transaction, AsaasService $asaas): void
    {
        if ($transaction->status === 'approved') {
            return;
        }

        $payment = null;

        try {
            if ($transaction->asaas_payment_id) {
                $payment = $asaas->getPayment($transaction->asaas_payment_id);
            } elseif ($transaction->subscription?->asaas_subscription_id) {
                $payments = $asaas->getSubscriptionPayments($transaction->subscription->asaas_subscription_id);
                $payment = $payments[0] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Asaas sync transaction failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            return;
        }

        if (!$payment) {
            return;
        }

        $remoteStatus = match ($payment['status'] ?? '') {
            'RECEIVED', 'CONFIRMED' => 'approved',
            'REFUNDED', 'CHARGEBACK_REQUESTED', 'CHARGEBACK_DISPUTE', 'AWAITING_CHARGEBACK_REVERSAL' => 'refunded',
            'OVERDUE', 'DELETED' => 'rejected',
            'PENDING', 'RECEIVED_IN_CASH', 'AWAITING_RISK_ANALYSIS' => 'pending',
            default => 'pending',
        };

        $transaction->update([
            'asaas_payment_id' => $payment['id'] ?? $transaction->asaas_payment_id,
            'amount' => (float) ($payment['value'] ?? $transaction->amount),
            'status' => $remoteStatus,
            'paid_at' => $remoteStatus === 'approved' ? Carbon::now() : $transaction->paid_at,
            'failure_reason' => $remoteStatus === 'rejected' ? ($payment['status'] ?? 'rejected') : $transaction->failure_reason,
            'asaas_raw_response' => $payment,
        ]);

        if ($remoteStatus === 'approved') {
            $this->activateSubscription($transaction->fresh());
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
        $checkoutState = request()->query('checkout_state');

        $this->syncAsaasTransactionStatus($transaction, $asaas);

        $transaction->refresh();

        if ($checkoutState === 'success') {
            if ($transaction->payment_method === 'credit_card' && $transaction->subscription) {
                $this->syncLatestAsaasSubscription($transaction->subscription, $asaas);
                $transaction->refresh();
            }

            if (!Auth::check()) {
                Auth::login($transaction->subscription->user);
            }

            return redirect()->route('personal.dashboard');
        }

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

        $this->syncAsaasTransactionStatus($transaction, $asaas);

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
            'asaas_subscription_id' => null,
            'next_billing_at' => null,
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
