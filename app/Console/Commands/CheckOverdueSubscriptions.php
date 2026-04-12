<?php

namespace App\Console\Commands;

use App\Models\ProfessionalSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueSubscriptions extends Command
{
    protected $signature = 'subscription:check-overdue';
    protected $description = 'Verifica assinaturas vencidas e suspende acesso fora da janela tecnica de cobranca.';

    public function handle(): int
    {
        $timezone = (string) config('app.timezone', 'America/Sao_Paulo');
        $now = Carbon::now($timezone);

        $expired = ProfessionalSubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        $blockedCount = 0;

        foreach ($expired as $sub) {
            $cutoffHour = max(0, min(23, (int) config('services.asaas.block_cutoff_hour', 6)));
            $expiresAt = $sub->expires_at?->copy()->timezone($timezone);
            $cutoffAt = $expiresAt?->copy()->addDay()->setTime($cutoffHour, 0, 0);

            if (
                !empty($sub->asaas_subscription_id)
                && $cutoffAt
                && $now->lte($cutoffAt)
            ) {
                continue;
            }

            $sub->update(['status' => 'suspended']);
            $blockedCount++;

            $user = $sub->user;
            if ($user) {
                $user->update(['is_active' => false]);
                Log::info("User #{$user->id} blocked - subscription #{$sub->id} expired at {$sub->expires_at}");
            }
        }

        $this->info("{$blockedCount} assinaturas expiradas -> acesso bloqueado.");

        return self::SUCCESS;
    }
}
