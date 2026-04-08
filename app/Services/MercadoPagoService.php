<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    protected PaymentClient $paymentClient;
    protected string $accessToken;

    public function __construct()
    {
        $mode = (string) config('services.mercadopago.mode', 'live');
        $this->accessToken = $mode === 'test'
            ? (string) config('services.mercadopago.test_access_token')
            : (string) config('services.mercadopago.access_token');

        if (empty($this->accessToken)) {
            throw new \RuntimeException('Mercado Pago access token nao configurado para o modo atual.');
        }

        MercadoPagoConfig::setAccessToken($this->accessToken);

        $this->paymentClient = new PaymentClient();
    }

    protected function resolvePayerEmail(User $user): string
    {
        return (string) $user->email;
    }

    /**
     * Cria assinatura recorrente mensal via preapproval.
     */
    public function createRecurringSubscription(User $user, array $plan, string $externalRef): array
    {
        $appUrl  = rtrim((string) config('app.url'), '/');
        // MP exige HTTPS. Se APP_URL for localhost, usa o MP_BACK_URL do .env
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            $appUrl = rtrim((string) config('services.mercadopago.back_url', $appUrl), '/');
        }
        $backUrl = $appUrl . '/assinatura/resultado-preapproval';

        $payload = [
            'reason'             => $plan['name'] . ' - Assinatura Mensal ApexPro',
            'external_reference' => $externalRef,
            'payer_email'        => $this->resolvePayerEmail($user),
            'auto_recurring'     => [
                'frequency'          => 1,
                'frequency_type'     => 'months',
                'transaction_amount' => (float) $plan['price'],
                'currency_id'        => 'BRL',
            ],
            'back_url'           => $backUrl,
            'notification_url'   => $appUrl . '/webhooks/mercadopago',
        ];

        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/preapproval', $payload);

        $json = $response->json();

        if (!$response->successful()) {
            $message  = $json['message'] ?? $json['error'] ?? 'Erro ao criar assinatura recorrente.';
            $causeDesc = data_get($json, 'cause.0.description');
            if (!empty($causeDesc)) {
                $message .= ' | ' . $causeDesc;
            }
            throw new \RuntimeException($message);
        }

        return [
            'preapproval_id'   => (string) ($json['id'] ?? ''),
            'status'           => (string) ($json['status'] ?? 'pending'),
            'init_point'       => (string) ($json['init_point'] ?? ''),
            'next_payment_date' => $json['next_payment_date'] ?? null,
            'raw_response'     => $json,
        ];
    }

    public function getPreapproval(string $preapprovalId): array
    {
        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->get('https://api.mercadopago.com/preapproval/' . $preapprovalId);

        $json = $response->json();
        if (!$response->successful()) {
            Log::warning('MP getPreapproval failed', [
                'preapproval_id' => $preapprovalId,
                'http_status'    => $response->status(),
                'response_body'  => $json,
            ]);
            $message = $json['message'] ?? $json['error'] ?? 'Erro ao consultar assinatura.';
            throw new \RuntimeException($message);
        }

        return $json;
    }

    /**
     * Cria pagamento via PIX.
     */
    public function createPixPayment(User $user, array $plan, string $externalRef): array
    {
        $payment = $this->paymentClient->create([
            'transaction_amount' => (float) $plan['price'],
            'description'        => $plan['name'] . ' - ApexPro',
            'payment_method_id'  => 'pix',
            'payer' => [
                'email'          => $this->resolvePayerEmail($user),
                'first_name'     => explode(' ', $user->name)[0],
                'identification' => [
                    'type'   => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $user->cpf),
                ],
            ],
            'external_reference' => $externalRef,
        ]);

        Log::info('MP PIX Payment created', ['payment_id' => $payment->id, 'status' => $payment->status]);

        $expiresAt = Carbon::now()->addMinutes(10)->toIso8601String();

        return [
            'mp_payment_id'     => (string) $payment->id,
            'status'            => $payment->status,
            'qr_code'           => $payment->point_of_interaction->transaction_data->qr_code ?? null,
            'qr_code_base64'    => $payment->point_of_interaction->transaction_data->qr_code_base64 ?? null,
            'expires_at'        => $expiresAt,
            'raw_response'      => json_decode(json_encode($payment), true),
        ];
    }

    public function createCardPayment(User $user, array $plan, string $token, int $installments, string $externalRef): array
    {
        $payment = $this->paymentClient->create([
            'transaction_amount' => (float) $plan['price'],
            'token'              => $token,
            'description'        => $plan['name'] . ' - ApexPro',
            'installments'       => $installments,
            'payer' => [
                'email'          => $this->resolvePayerEmail($user),
                'identification' => [
                    'type'   => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $user->cpf),
                ],
            ],
            'external_reference' => $externalRef,
        ]);

        Log::info('MP Card Payment created', ['payment_id' => $payment->id, 'status' => $payment->status]);

        return [
            'mp_payment_id'  => (string) $payment->id,
            'status'         => $payment->status,
            'status_detail'  => $payment->status_detail ?? null,
            'card_last_four' => $payment->card->last_four_digits ?? null,
            'card_brand'     => $payment->payment_method_id ?? null,
            'failure_reason' => $payment->status_detail ?? null,
            'raw_response'   => json_decode(json_encode($payment), true),
        ];
    }

    public function createCardPaymentForTest(array $plan, string $token, int $installments, string $payerEmail, string $payerCpf, string $externalRef): array
    {
        $payment = $this->paymentClient->create([
            'transaction_amount' => (float) $plan['price'],
            'token'              => $token,
            'description'        => $plan['name'] . ' - ApexPro (Card Test)',
            'installments'       => $installments,
            'payer' => [
                'email'          => $payerEmail,
                'identification' => [
                    'type'   => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $payerCpf),
                ],
            ],
            'external_reference' => $externalRef,
        ]);

        return [
            'mp_payment_id'  => (string) $payment->id,
            'status'         => $payment->status,
            'status_detail'  => $payment->status_detail ?? null,
            'raw_response'   => json_decode(json_encode($payment), true),
        ];
    }

    // -------------------------------------------------------------------------
    // Customer & Preapproval com card_id (recorrência transparente)
    // -------------------------------------------------------------------------

    /**
     * Busca customer existente por email ou cria um novo. Retorna customer_id.
     */
    public function createOrGetCustomer(string $email): string
    {
        // Buscar customer existente
        $search = Http::withToken($this->accessToken)
            ->acceptJson()
            ->get('https://api.mercadopago.com/v1/customers/search', [
                'email' => $email,
            ]);

        if ($search->successful()) {
            $results = $search->json('results', []);
            if (!empty($results[0]['id'])) {
                return (string) $results[0]['id'];
            }
        }

        // Criar novo customer
        $create = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/v1/customers', [
                'email' => $email,
            ]);

        $json = $create->json();

        if (!$create->successful()) {
            $msg = $json['message'] ?? $json['error'] ?? 'Erro ao criar customer MP.';
            throw new \RuntimeException($msg);
        }

        return (string) ($json['id'] ?? '');
    }

    /**
     * Salva cartão (via token) ao customer. Retorna card_id como string.
     */
    public function saveCardToCustomer(string $customerId, string $cardToken): string
    {
        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post("https://api.mercadopago.com/v1/customers/{$customerId}/cards", [
                'token' => $cardToken,
            ]);

        $json = $response->json();

        if (!$response->successful()) {
            $msg = $json['message'] ?? $json['error'] ?? 'Erro ao salvar cartao no customer MP.';
            throw new \RuntimeException($msg);
        }

        return (string) ($json['id'] ?? '');
    }

    /**
     * Cria preapproval recorrente usando card_id salvo (best-effort, sem status forçado).
     * Em sandbox: fica pending. Em produção: torna-se authorized automaticamente.
     */
    /**
     * @deprecated Use createSubscriptionWithToken() instead.
     * Mantido para compatibilidade com renovações existentes.
     */
    public function createPreapprovalWithCardId(User $user, array $plan, string $customerId, string $cardToken, string $externalRef): array
    {
        return $this->createSubscriptionWithToken($user, $plan, $cardToken, $externalRef);
    }

    /**
     * Cria ou retorna um preapproval_plan no MP para o plano dado.
     * Armazena o mp_plan_id no plan_configs para reuso.
     */
    public function createOrGetPlan(array $plan, int $trialDays = 1): string
    {
        // Verificar se já existe no banco
        $planConfig = \App\Models\PlanConfig::where('plan_id', $plan['id'])->first();
        if ($planConfig && !empty($planConfig->mp_plan_id)) {
            return $planConfig->mp_plan_id;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            $appUrl = rtrim((string) config('services.mercadopago.back_url', $appUrl), '/');
        }

        $payload = [
            'reason'         => $plan['name'] . ' - ApexPro',
            'auto_recurring' => [
                'frequency'          => 1,
                'frequency_type'     => 'months',
                'transaction_amount' => (float) $plan['price'],
                'currency_id'        => 'BRL',
                'free_trial'         => [
                    'frequency'      => $trialDays,
                    'frequency_type' => 'days',
                ],
            ],
            'payment_methods_allowed' => [
                'payment_types' => [['id' => 'credit_card']],
            ],
            'back_url' => $appUrl . '/assinatura/resultado-preapproval',
        ];

        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/preapproval_plan', $payload);

        $json = $response->json();

        Log::info('MP: preapproval_plan criado', [
            'plan_id'    => $plan['id'],
            'mp_plan_id' => $json['id'] ?? null,
            'status'     => $json['status'] ?? null,
            'trial_days' => $trialDays,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException($json['message'] ?? $json['error'] ?? 'Erro ao criar plano MP.');
        }

        $mpPlanId = (string) ($json['id'] ?? '');

        // Salvar no banco para reuso
        if ($planConfig) {
            $planConfig->update(['mp_plan_id' => $mpPlanId]);
        } else {
            \App\Models\PlanConfig::create([
                'plan_id'    => $plan['id'],
                'mp_plan_id' => $mpPlanId,
            ]);
        }

        return $mpPlanId;
    }

    /**
     * Cria assinatura vinculada a um plano MP (com free_trial nativo).
     * status: "authorized" = checkout transparente sem redirect.
     */
    public function createSubscriptionWithPlan(User $user, string $mpPlanId, string $cardToken, string $externalRef): array
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            $appUrl = rtrim((string) config('services.mercadopago.back_url', $appUrl), '/');
        }

        $payload = [
            'preapproval_plan_id' => $mpPlanId,
            'payer_email'         => $this->resolvePayerEmail($user),
            'card_token_id'       => $cardToken,
            'status'              => 'authorized',
            'external_reference'  => $externalRef,
            'back_url'            => $appUrl . '/assinatura/resultado-preapproval',
            'notification_url'    => $appUrl . '/webhooks/mercadopago',
        ];

        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/preapproval', $payload);

        $json = $response->json();

        Log::info('MP: createSubscriptionWithPlan response', [
            'status'            => $json['status'] ?? null,
            'next_payment_date' => $json['next_payment_date'] ?? null,
            'http_status'       => $response->status(),
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException($json['message'] ?? $json['error'] ?? 'Erro ao criar assinatura com plano.');
        }

        return [
            'preapproval_id'    => (string) ($json['id'] ?? ''),
            'status'            => (string) ($json['status'] ?? 'pending'),
            'next_payment_date' => $json['next_payment_date'] ?? null,
            'card_id'           => (string) ($json['card_id'] ?? ''),
            'raw_response'      => $json,
        ];
    }

    /**
     * Cria assinatura recorrente transparente usando card_token_id do Brick.
     * Passa status "authorized" para ativar sem redirect.
     * $startDate: data da primeira cobrança (null = imediato).
     */
    public function createSubscriptionWithToken(User $user, array $plan, string $cardToken, string $externalRef, ?Carbon $startDate = null): array
    {
        $appUrl = rtrim((string) config('app.url'), '/');
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            $appUrl = rtrim((string) config('services.mercadopago.back_url', $appUrl), '/');
        }
        $backUrl = $appUrl . '/assinatura/resultado-preapproval';

        $autoRecurring = [
            'frequency'          => 1,
            'frequency_type'     => 'months',
            'transaction_amount' => (float) $plan['price'],
            'currency_id'        => 'BRL',
        ];

        if ($startDate) {
            $autoRecurring['start_date'] = $startDate->setTimezone('America/Sao_Paulo')->format('Y-m-d\TH:i:s.000P');
        }

        $payload = [
            'reason'             => $plan['name'] . ' - Assinatura Mensal ApexPro',
            'external_reference' => $externalRef,
            'payer_email'        => $this->resolvePayerEmail($user),
            'card_token_id'      => $cardToken,   // token do Brick (uso único)
            'auto_recurring'     => $autoRecurring,
            'back_url'           => $backUrl,
            'status'             => 'authorized',  // checkout transparente, sem redirect
            'notification_url'   => $appUrl . '/webhooks/mercadopago',
        ];

        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/preapproval', $payload);

        $json = $response->json();

        Log::info('MP: createSubscriptionWithToken response', [
            'status'            => $json['status'] ?? null,
            'next_payment_date' => $json['next_payment_date'] ?? null,
            'payment_method_id' => $json['payment_method_id'] ?? null,
            'http_status'       => $response->status(),
        ]);

        if (!$response->successful()) {
            $msg = $json['message'] ?? $json['error'] ?? 'Erro ao criar assinatura.';
            throw new \RuntimeException($msg);
        }

        return [
            'preapproval_id'    => (string) ($json['id'] ?? ''),
            'status'            => (string) ($json['status'] ?? 'pending'),
            'next_payment_date' => $json['next_payment_date'] ?? null,
            'card_id'           => (string) ($json['card_id'] ?? ''),
            'raw_response'      => $json,
        ];
    }

    public function cancelPreapproval(string $preapprovalId): void
    {
        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->put('https://api.mercadopago.com/preapproval/' . $preapprovalId, [
                'status' => 'cancelled',
            ]);

        if (!$response->successful()) {
            $json = $response->json();
            $message = $json['message'] ?? $json['error'] ?? 'Erro ao cancelar assinatura recorrente.';
            throw new \RuntimeException($message);
        }
    }

    public function getPaymentStatus(string $mpPaymentId): array
    {
        $payment = $this->paymentClient->get((int) $mpPaymentId);

        return [
            'status'        => $payment->status,
            'status_detail' => $payment->status_detail ?? null,
            'raw'           => json_decode(json_encode($payment), true),
        ];
    }

    public function extractApiErrorDetails(\Throwable $e): ?string
    {
        if (!$e instanceof MPApiException) {
            return null;
        }

        try {
            $content = $e->getApiResponse()->getContent();
            if (!is_array($content)) {
                return null;
            }

            $messages = [];
            if (!empty($content['message']) && is_string($content['message'])) {
                $messages[] = $content['message'];
            }
            if (!empty($content['error']) && is_string($content['error'])) {
                $messages[] = $content['error'];
            }
            if (!empty($content['cause']) && is_array($content['cause'])) {
                foreach ($content['cause'] as $cause) {
                    if (!is_array($cause)) {
                        continue;
                    }
                    if (!empty($cause['description']) && is_string($cause['description'])) {
                        $messages[] = $cause['description'];
                    }
                    if (!empty($cause['code']) && is_string($cause['code'])) {
                        $messages[] = 'code: ' . $cause['code'];
                    }
                }
            }

            $messages = array_values(array_unique(array_filter($messages)));
            return empty($messages) ? null : implode(' | ', $messages);
        } catch (\Throwable $parseError) {
            Log::warning('Failed to parse Mercado Pago API error', ['error' => $parseError->getMessage()]);
            return null;
        }
    }

    public function validateWebhookSignature(Request $request): bool
    {
        $secret = config('services.mercadopago.webhook_secret');

        if (empty($secret) || $secret === 'xxxx') {
            return true;
        }

        $xSignature = $request->header('x-signature');
        $xRequestId = $request->header('x-request-id');
        if (!$xSignature || !$xRequestId) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $xSignature) as $part) {
            $kv = explode('=', trim($part), 2);
            if (count($kv) === 2) {
                $parts[$kv[0]] = $kv[1];
            }
        }

        $ts = $parts['ts'] ?? null;
        $v1 = $parts['v1'] ?? null;
        if (!$ts || !$v1) {
            return false;
        }

        $dataId = $request->input('data.id', '');
        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
        $computed = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($computed, $v1);
    }
}