<?php

namespace App\Console\Commands;

use App\Models\ProfessionalSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueSubscriptions extends Command
{
    protected $signature = 'subscription:check-overdue';
    protected $description = 'Verifica assinaturas vencidas e suspende acesso após grace period';

    public function handle(): int
    {
        $now = Carbon::now();

        // active com expires_at vencido → suspended + bloqueia user imediatamente (sem grace period)
        $expired = ProfessionalSubscription::whereIn('status', ['active', 'overdue'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($expired as $sub) {
            $sub->update(['status' => 'suspended']);

            $user = $sub->user;
            if ($user) {
                $user->update(['is_active' => false]);
                Log::info("User #{$user->id} blocked - subscription #{$sub->id} expired at {$sub->expires_at}");
            }
        }

        $this->info("{$expired->count()} assinaturas expiradas → acesso bloqueado.");

        return self::SUCCESS;
    }
}
