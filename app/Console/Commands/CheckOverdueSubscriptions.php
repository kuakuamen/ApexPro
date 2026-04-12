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

        $expired = ProfessionalSubscription::whereIn('status', ['active', 'overdue'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        $blockedCount = 0;

        foreach ($expired as $sub) {
            $windowHours = max(0, (int) config('services.asaas.processing_window_hours', 0));

            if (
                $windowHours > 0
                && !empty($sub->asaas_subscription_id)
                && $sub->expires_at
                && $now->lte($sub->expires_at->copy()->addHours($windowHours))
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
