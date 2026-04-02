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

        // Fase 1: active com expires_at vencido → overdue
        $overdue = ProfessionalSubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($overdue as $sub) {
            $sub->update(['status' => 'overdue']);
            Log::info("Subscription #{$sub->id} (user #{$sub->user_id}) marked as overdue");
        }

        $this->info("Fase 1: {$overdue->count()} assinaturas marcadas como vencidas (overdue).");

        // Fase 2: overdue com grace_until vencido → suspended + bloqueia user
        $suspended = ProfessionalSubscription::where('status', 'overdue')
            ->whereNotNull('grace_until')
            ->where('grace_until', '<', $now)
            ->get();

        foreach ($suspended as $sub) {
            $sub->update(['status' => 'suspended']);

            $user = $sub->user;
            if ($user) {
                $user->update(['is_active' => false]);
                Log::info("User #{$user->id} suspended due to overdue subscription #{$sub->id}");
            }
        }

        $this->info("Fase 2: {$suspended->count()} assinaturas suspensas (grace period expirado).");

        return self::SUCCESS;
    }
}
