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
        $now = Carbon::now();
        $windowHours = max(0, (int) config('services.mercadopago.processing_window_hours', 0));

        $expired = ProfessionalSubscription::whereIn('status', ['active', 'overdue'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        $blockedCount = 0;

        foreach ($expired as $sub) {
            if (
                $windowHours > 0
                && !empty($sub->mp_preapproval_id)
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
