<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\StudentPlan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SuspendOverduePlans extends Command
{
    protected $signature   = 'financial:suspend-overdue';
    protected $description = 'Marca planos como overdue/suspended conforme atraso no pagamento';

    public function handle(): void
    {
        $today     = Carbon::today();
        $threshold = Carbon::today()->subDays(5);

        // 1) Pendente → Vencido nos pagamentos (passou da data)
        $pmOverdue = Payment::where('status', 'pending')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'overdue']);

        // 2) Plano Ativo → Atrasado (passou do vencimento, ainda dentro da tolerância)
        $spOverdue = StudentPlan::where('status', 'active')
            ->whereDate('due_date', '<', $today)
            ->whereDate('due_date', '>=', $threshold)
            ->update(['status' => 'overdue']);

        // 3) Plano Ativo ou Atrasado → Suspenso (mais de 5 dias sem pagar)
        $spSuspended = StudentPlan::whereIn('status', ['active', 'overdue'])
            ->whereDate('due_date', '<', $threshold)
            ->update(['status' => 'suspended']);

        $this->info("Pagamentos marcados como vencidos : {$pmOverdue}");
        $this->info("Planos marcados como atrasados    : {$spOverdue}");
        $this->info("Planos suspensos (>5 dias atraso) : {$spSuspended}");
    }
}
