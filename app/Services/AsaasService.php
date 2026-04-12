<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $checkoutBaseUrl;

    public function __construct()
    {
        $this->apiKey  = (string) config('services.asaas.api_key');
        $this->baseUrl = (string) config('services.asaas.base_url', 'https://sandbox.asaas.com/api/v3');
        $this->checkoutBaseUrl = (string) config('services.asaas.checkout_base_url', 'https://asaas.com/checkoutSession/show?id=');
    }

    // ── HTTP Client ────────────────────────────────────────────────────────────

    protected function http()
    {
        return Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl)->timeout(30);
    }

    // ── Clientes ───────────────────────────────────────────────────────────────

    /**
     * Busca cliente pelo CPF/CNPJ.
     */
    public function findCustomerByCpf(string $cpf): ?array
    {
        $clean    = preg_replace('/\D/', '', $cpf);
        $response = $this->http()->get('/customers', ['cpfCnpj' => $clean]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['data'])) {
                return $data['data'][0];
            }
        }

        return null;
    }

    public function listCustomersByCpf(string $cpf): array
    {
        $clean    = preg_replace('/\D/', '', $cpf);
        $response = $this->http()->get('/customers', ['cpfCnpj' => $clean]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json()['data'] ?? [];
    }

    /**
     * Cria um novo cliente no Asaas.
     */
    public function createCustomer(array $data): array
    {
        $response = $this->http()->post('/customers', [
            'name'        => $data['name'],
            'cpfCnpj'     => preg_replace('/\D/', '', $data['cpf']),
            'email'       => $data['email'],
            'mobilePhone' => preg_replace('/\D/', '', $data['phone'] ?? ''),
            'postalCode'  => preg_replace('/\D/', '', $data['postal_code'] ?? ''),
            'address'     => $data['address'] ?? null,
            'addressNumber' => $data['address_number'] ?? null,
            'province'    => $data['province'] ?? null,
        ]);

        if (!$response->successful()) {
            $err = $response->json()['errors'][0]['description'] ?? 'Erro desconhecido';
            Log::error('Asaas createCustomer failed', ['body' => $response->json()]);
            throw new \RuntimeException("Erro ao criar cliente no Asaas: {$err}");
        }

        return $response->json();
    }

    /**
     * Busca ou cria cliente pelo CPF.
     */
    public function createOrFindCustomer(array $data): array
    {
        $existing = $this->findCustomerByCpf($data['cpf']);
        if ($existing) {
            return $existing;
        }

        return $this->createCustomer($data);
    }

    public function customerSupportsCheckout(array $customer): bool
    {
        return !empty($customer['address'])
            && !empty($customer['addressNumber'])
            && !empty($customer['postalCode'])
            && !empty($customer['province']);
    }

    // ── Assinaturas ────────────────────────────────────────────────────────────

    /**
     * Cria assinatura recorrente no Asaas.
     *
     * $data keys:
     *   customer_id        string  — ID do cliente no Asaas
     *   billing_type       string  — PIX | BOLETO | CREDIT_CARD
     *   value              float   — valor mensal
     *   next_due_date      string  — Y-m-d (primeira cobrança / trial end)
     *   description        string  — opcional
     *   external_reference string  — opcional
     *   credit_card_token  string  — obrigatório quando billing_type=CREDIT_CARD
     */
    public function createSubscription(array $data): array
    {
        $payload = [
            'customer'          => $data['customer_id'],
            'billingType'       => $data['billing_type'],
            'value'             => $data['value'],
            'nextDueDate'       => $data['next_due_date'],
            'cycle'             => $data['cycle'] ?? 'MONTHLY',
            'description'       => $data['description'] ?? 'Assinatura ApexPro AI',
            'externalReference' => $data['external_reference'] ?? null,
        ];

        if (!empty($data['credit_card_token'])) {
            $payload['creditCardToken'] = $data['credit_card_token'];
        }

        Log::info('Asaas createSubscription payload', $payload);

        $response = $this->http()->post('/subscriptions', $payload);

        if (!$response->successful()) {
            $err = $response->json()['errors'][0]['description'] ?? 'Erro desconhecido';
            Log::error('Asaas createSubscription failed', ['body' => $response->json(), 'payload' => $payload]);
            throw new \RuntimeException("Erro ao criar assinatura no Asaas: {$err}");
        }

        return $response->json();
    }

    /**
     * Cria um checkout hospedado no Asaas.
     */
    public function createCheckout(array $payload): array
    {
        $response = $this->http()->post('/checkouts', $payload);

        if (!$response->successful()) {
            $err = $response->json()['errors'][0]['description'] ?? 'Erro desconhecido';
            Log::error('Asaas createCheckout failed', ['body' => $response->json(), 'payload' => $payload]);
            throw new \RuntimeException("Erro ao criar checkout no Asaas: {$err}");
        }

        return $response->json();
    }

    public function getCheckoutUrl(string $checkoutId): string
    {
        return rtrim($this->checkoutBaseUrl, '=') . '=' . $checkoutId;
    }

    public function getCheckout(string $checkoutId): array
    {
        $response = $this->http()->get("/checkouts/{$checkoutId}");

        if (!$response->successful()) {
            throw new \RuntimeException("Checkout Asaas nao encontrado: {$checkoutId}");
        }

        return $response->json();
    }

    public function listSubscriptions(array $filters = []): array
    {
        $response = $this->http()->get('/subscriptions', $filters);

        if (!$response->successful()) {
            throw new \RuntimeException('Nao foi possivel consultar assinaturas no Asaas.');
        }

        return $response->json()['data'] ?? [];
    }

    public function findLatestSubscriptionByCustomer(string $customerId): ?array
    {
        $subscriptions = $this->listSubscriptions([
            'customer' => $customerId,
            'limit' => 20,
            'offset' => 0,
        ]);

        if (empty($subscriptions)) {
            return null;
        }

        usort($subscriptions, function (array $a, array $b) {
            return strcmp((string) ($b['dateCreated'] ?? ''), (string) ($a['dateCreated'] ?? ''));
        });

        return $subscriptions[0] ?? null;
    }

    public function findLatestPaymentByCheckoutId(string $checkoutId): ?array
    {
        $payments = $this->listPayments([
            'checkoutSession' => $checkoutId,
            'limit' => 20,
            'offset' => 0,
        ]);

        if (empty($payments)) {
            return null;
        }

        usort($payments, function (array $a, array $b) {
            return strcmp((string) ($b['dateCreated'] ?? ''), (string) ($a['dateCreated'] ?? ''));
        });

        return $payments[0] ?? null;
    }

    /**
     * Busca dados de uma assinatura.
     */
    public function getSubscription(string $id): array
    {
        $response = $this->http()->get("/subscriptions/{$id}");

        if (!$response->successful()) {
            throw new \RuntimeException("Assinatura Asaas não encontrada: {$id}");
        }

        return $response->json();
    }

    /**
     * Cancela assinatura.
     */
    public function cancelSubscription(string $id): bool
    {
        $response = $this->http()->delete("/subscriptions/{$id}");
        Log::info('Asaas cancelSubscription', [
            'id'     => $id,
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
        return $response->successful();
    }

    /**
     * Lista cobranças de uma assinatura.
     */
    public function getSubscriptionPayments(string $subscriptionId): array
    {
        $response = $this->http()->get('/payments', [
            'subscription' => $subscriptionId,
            'limit'        => 5,
        ]);

        return $response->json()['data'] ?? [];
    }

    public function listPayments(array $filters = []): array
    {
        $response = $this->http()->get('/payments', $filters);

        if (!$response->successful()) {
            throw new \RuntimeException('Nao foi possivel consultar pagamentos no Asaas.');
        }

        return $response->json()['data'] ?? [];
    }

    // ── Pagamentos ─────────────────────────────────────────────────────────────

    /**
     * Busca dados de um pagamento.
     */
    public function getPayment(string $id): array
    {
        $response = $this->http()->get("/payments/{$id}");

        if (!$response->successful()) {
            throw new \RuntimeException("Pagamento Asaas não encontrado: {$id}");
        }

        return $response->json();
    }

    /**
     * Gera/recupera QR Code PIX de um pagamento.
     */
    public function getPixQrCode(string $paymentId): array
    {
        $response = $this->http()->get("/payments/{$paymentId}/pixQrCode");

        if (!$response->successful()) {
            Log::warning('Asaas getPixQrCode failed', ['payment_id' => $paymentId, 'body' => $response->json()]);
            throw new \RuntimeException('QR Code PIX não disponível ainda.');
        }

        return $response->json();
    }

    /**
     * Retorna link de boleto de um pagamento.
     */
    public function getBoletoUrl(string $paymentId): ?string
    {
        $payment = $this->getPayment($paymentId);
        return $payment['bankSlipUrl'] ?? null;
    }

    // ── Cartão de Crédito ──────────────────────────────────────────────────────

    /**
     * Tokeniza cartão de crédito para uso em assinaturas.
     *
     * $cardData: holder_name, number, expiry_month, expiry_year, ccv
     * $holderInfo: name, email, cpf, zip, address_number, phone
     */
    public function tokenizeCreditCard(string $customerId, array $cardData, array $holderInfo): string
    {
        $payload = [
            'customer'   => $customerId,
            'creditCard' => [
                'holderName'  => $cardData['holder_name'],
                'number'      => preg_replace('/\D/', '', $cardData['number']),
                'expiryMonth' => $cardData['expiry_month'],
                'expiryYear'  => $cardData['expiry_year'],
                'ccv'         => $cardData['ccv'],
            ],
            'creditCardHolderInfo' => [
                'name'          => $holderInfo['name'],
                'email'         => $holderInfo['email'],
                'cpfCnpj'       => preg_replace('/\D/', '', $holderInfo['cpf']),
                'postalCode'    => preg_replace('/\D/', '', $holderInfo['zip'] ?? ''),
                'addressNumber' => $holderInfo['address_number'] ?? '0',
                'phone'         => preg_replace('/\D/', '', $holderInfo['phone'] ?? ''),
            ],
        ];
        $response = $this->http()->post('/creditCard/tokenizeCreditCard', $payload);

        if (!$response->successful()) {
            $err = $response->json()['errors'][0]['description'] ?? 'Dados do cartão inválidos';
            Log::error('Asaas tokenize failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'json'   => $response->json(),
            ]);
            throw new \RuntimeException("Erro ao tokenizar cartão: {$err}");
        }

        return $response->json()['creditCardToken'];
    }

    // ── Webhook ────────────────────────────────────────────────────────────────

    /**
     * Valida token do webhook (se configurado).
     */
    public function validateWebhookToken(Request $request): bool
    {
        $token = config('services.asaas.webhook_token');

        // Se não configurado, aceita todos (proteja via IP no painel Asaas)
        if (!$token) {
            return true;
        }

        return $request->header('asaas-access-token') === $token;
    }
}
