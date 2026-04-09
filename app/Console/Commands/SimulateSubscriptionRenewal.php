<?php

namespace App\Console\Commands;

use App\Models\ProfessionalSubscription;
use App\Models\SubscriptionTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SimulateSubscriptionRenewal extends Command
{
    protected $signature = 'subscription:simulate-renewal {email : E-mail do personal trainer}';
    protected $description = 'Simula a renovação mensal de uma assinatura (para testes locais)';

    public function handle(): int
    {
        $email = $this->argument('email');

        $subscription = ProfessionalSubscription::whereHas('user', fn($q) => $q->where('email', $email))
            ->with('user')
            ->first();

        if (!$subscription) {
            $this->error("Nenhuma assinatura encontrada para: {$email}");
            return 1;
        }

        $user = $subscription->user;

        $this->line('');
        $this->info('=== ESTADO ANTES ===');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['user', $user->name . ' (' . $user->email . ')'],
                ['status', $subscription->status],
                ['expires_at', $subscription->expires_at?->format('d/m/Y H:i:s') ?? 'NULL'],
                ['grace_until', $subscription->grace_until?->format('d/m/Y H:i:s') ?? 'NULL'],
                ['is_active (user)', $user->is_active ? 'true' : 'false'],
                ['isActive()', $subscription->isActive() ? 'SIM' : 'NÃO'],
                ['isInGrace()', $subscription->isInGrace() ? 'SIM' : 'NÃO'],
                ['isExpired()', $subscription->isExpired() ? 'SIM' : 'NÃO'],
            ]
        );

        if ($subscription->status !== 'active') {
            $this->warn("AVISO: status = '{$subscription->status}'. O webhook de renovação exige status = 'active'.");
            $this->warn("Para simular corretamente, use o SQL abaixo para forçar expiração ANTES de rodar este comando:");
            $this->line("UPDATE professional_subscriptions SET status='active', expires_at=NOW()-INTERVAL 2 DAY, grace_until=NOW()-INTERVAL 1 DAY WHERE user_id={$user->id};");
            $this->line("UPDATE users SET subscription_expires_at=NOW()-INTERVAL 2 DAY WHERE id={$user->id};");
            $this->line('');

            if (!$this->confirm('Deseja continuar mesmo assim?')) {
                return 0;
            }
        }

        $this->line('');
        $this->info('Simulando renovação automática via preapproval...');

        $now = Carbon::now();
        $graceDays = (int) config('services.mercadopago.grace_period_days', 5);
        $preapprovalId = $subscription->mp_preapproval_id ?? 'simulated-test';
        $yearMonth = $now->format('Ym');

        // Deduplicação: já existe transaction aprovada na última hora?
        $recent = SubscriptionTransaction::where('subscription_id', $subscription->id)
            ->where('status', 'approved')
            ->where('paid_at', '>=', $now->copy()->subHour())
            ->exists();

        if ($recent) {
            $this->warn('Existe uma transaction aprovada na última hora — seria ignorado por deduplicação.');
            if (!$this->confirm('Forçar mesmo assim?')) {
                return 0;
            }
        }

        // Criar transaction de renovação
        $tx = SubscriptionTransaction::create([
            'subscription_id'       => $subscription->id,
            'user_id'               => $subscription->user_id,
            'plan_id'               => $subscription->plan_id,
            'amount'                => $subscription->price,
            'payment_method'        => 'credit_card',
            'status'                => 'approved',
            'mp_preapproval_id'     => $preapprovalId,
            'mp_external_reference' => (string) \Illuminate\Support\Str::uuid(),
            'paid_at'               => $now,
            'mp_status_detail'      => 'authorized',
        ]);

        // Atualizar subscription
        $subscription->update([
            'status'                => 'active',
            'mp_preapproval_status' => 'authorized',
            'expires_at'            => $now->copy()->addDays(30),
            'grace_until'           => $now->copy()->addDays(30 + $graceDays),
            'last_paid_at'          => $now,
            'next_billing_at'       => $now->copy()->addDays(30),
        ]);

        // Atualizar user
        $user->update([
            'subscription_expires_at' => $subscription->fresh()->expires_at,
            'is_active'               => true,
        ]);

        $subscription->refresh();
        $user->refresh();

        $this->line('');
        $this->info('=== ESTADO DEPOIS ===');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['user', $user->name . ' (' . $user->email . ')'],
                ['status', $subscription->status],
                ['expires_at', $subscription->expires_at?->format('d/m/Y H:i:s') ?? 'NULL'],
                ['grace_until', $subscription->grace_until?->format('d/m/Y H:i:s') ?? 'NULL'],
                ['is_active (user)', $user->is_active ? 'true' : 'false'],
                ['isActive()', $subscription->isActive() ? 'SIM ✓' : 'NÃO'],
                ['transaction_id criada', (string) $tx->id],
            ]
        );

        $this->line('');
        $this->info('✓ Renovação simulada com sucesso!');
        $this->line('Agora tente acessar o sistema com o usuário acima para confirmar que o acesso foi restaurado.');

        return 0;
    }
}
