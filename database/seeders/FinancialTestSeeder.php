<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialPlan;
use App\Models\StudentPlan;
use App\Models\Payment;
use Carbon\Carbon;

class FinancialTestSeeder extends Seeder
{
    public function run(): void
    {
        $personalId = 1;
        $student1   = 2; // LUIZ MATEUS
        $student2   = 4; // gremio

        // Limpar dados anteriores
        Payment::where('personal_id', $personalId)->delete();
        StudentPlan::where('personal_id', $personalId)->delete();
        FinancialPlan::where('personal_id', $personalId)->delete();

        $now = Carbon::now(); // março/2026

        // ─────────────────────────────────────────
        // PLANOS
        // ─────────────────────────────────────────
        $planMensal = FinancialPlan::create([
            'personal_id' => $personalId,
            'name'        => 'Mensalidade',
            'description' => 'Acompanhamento mensal completo.',
            'price'       => 250.00,
            'periodicity' => 'monthly',
            'active'      => true,
        ]);

        $planTrimestral = FinancialPlan::create([
            'personal_id' => $personalId,
            'name'        => 'Plano Trimestral',
            'description' => 'Pacote 3 meses com desconto.',
            'price'       => 680.00,
            'periodicity' => 'quarterly',
            'active'      => true,
        ]);

        $planOnline = FinancialPlan::create([
            'personal_id' => $personalId,
            'name'        => 'Consultoria Online',
            'description' => 'Acompanhamento remoto via app.',
            'price'       => 180.00,
            'periodicity' => 'monthly',
            'active'      => true,
        ]);

        // ─────────────────────────────────────────
        // ALUNO 1 (LUIZ MATEUS) — Mensalidade ativa
        // Histórico: Jan pago, Fev pago, Mar pago, Abr pendente
        // ─────────────────────────────────────────
        $sp1 = StudentPlan::create([
            'student_id'        => $student1,
            'personal_id'       => $personalId,
            'financial_plan_id' => $planMensal->id,
            'start_date'        => $now->copy()->subMonths(3)->startOfMonth(),
            'due_date'          => $now->copy()->addMonth()->startOfMonth(),
            'periodicity'       => 'monthly',
            'status'            => 'active',
        ]);

        // Janeiro — PAGO
        Payment::create([
            'student_plan_id' => $sp1->id,
            'student_id'      => $student1,
            'personal_id'     => $personalId,
            'amount'          => 250.00,
            'original_amount' => 250.00,
            'due_date'        => Carbon::create(2026, 1, 5),
            'paid_at'         => Carbon::create(2026, 1, 5),
            'status'          => 'paid',
            'payment_method'  => 'pix',
            'notes'           => 'Pago via Pix.',
        ]);

        // Fevereiro — PAGO (com desconto)
        Payment::create([
            'student_plan_id' => $sp1->id,
            'student_id'      => $student1,
            'personal_id'     => $personalId,
            'amount'          => 225.00,
            'original_amount' => 250.00,
            'discount_value'  => 25.00,
            'due_date'        => Carbon::create(2026, 2, 5),
            'paid_at'         => Carbon::create(2026, 2, 3),
            'status'          => 'paid',
            'payment_method'  => 'cash',
            'notes'           => 'Desconto de R$25 concedido.',
        ]);

        // Março — PAGO
        Payment::create([
            'student_plan_id' => $sp1->id,
            'student_id'      => $student1,
            'personal_id'     => $personalId,
            'amount'          => 250.00,
            'original_amount' => 250.00,
            'due_date'        => Carbon::create(2026, 3, 5),
            'paid_at'         => Carbon::create(2026, 3, 5),
            'status'          => 'paid',
            'payment_method'  => 'card',
            'notes'           => 'Pago no cartão.',
        ]);

        // Abril — PENDENTE (próximo vencimento)
        Payment::create([
            'student_plan_id' => $sp1->id,
            'student_id'      => $student1,
            'personal_id'     => $personalId,
            'amount'          => 250.00,
            'original_amount' => 250.00,
            'due_date'        => Carbon::create(2026, 4, 5),
            'paid_at'         => null,
            'status'          => 'pending',
        ]);

        // ─────────────────────────────────────────
        // ALUNO 2 (gremio) — Consultoria Suspenso (inadimplente)
        // Venceu há 16 dias, não pagou
        // ─────────────────────────────────────────
        $sp2 = StudentPlan::create([
            'student_id'        => $student2,
            'personal_id'       => $personalId,
            'financial_plan_id' => $planOnline->id,
            'start_date'        => $now->copy()->subMonths(2)->startOfMonth(),
            'due_date'          => $now->copy()->subDays(16),
            'periodicity'       => 'monthly',
            'status'            => 'suspended',
        ]);

        // Fevereiro — PAGO (entrada)
        Payment::create([
            'student_plan_id' => $sp2->id,
            'student_id'      => $student2,
            'personal_id'     => $personalId,
            'amount'          => 180.00,
            'original_amount' => 180.00,
            'due_date'        => Carbon::create(2026, 2, 3),
            'paid_at'         => Carbon::create(2026, 2, 3),
            'status'          => 'paid',
            'payment_method'  => 'pix',
        ]);

        // Março — VENCIDO (suspenso)
        Payment::create([
            'student_plan_id' => $sp2->id,
            'student_id'      => $student2,
            'personal_id'     => $personalId,
            'amount'          => 180.00,
            'original_amount' => 180.00,
            'due_date'        => $now->copy()->subDays(16),
            'paid_at'         => null,
            'status'          => 'overdue',
            'notes'           => 'Sem contato.',
        ]);

        // ─────────────────────────────────────────
        // PLANO TRIMESTRAL — gremio — vencido há 5 dias
        // ─────────────────────────────────────────
        $sp3 = StudentPlan::create([
            'student_id'        => $student2,
            'personal_id'       => $personalId,
            'financial_plan_id' => $planTrimestral->id,
            'start_date'        => $now->copy()->subMonths(4)->startOfMonth(),
            'due_date'          => $now->copy()->subDays(5),
            'periodicity'       => 'quarterly',
            'status'            => 'overdue',
        ]);

        // Pagamento trimestral anterior — PAGO (dezembro)
        Payment::create([
            'student_plan_id' => $sp3->id,
            'student_id'      => $student2,
            'personal_id'     => $personalId,
            'amount'          => 680.00,
            'original_amount' => 680.00,
            'due_date'        => Carbon::create(2025, 12, 14),
            'paid_at'         => Carbon::create(2025, 12, 14),
            'status'          => 'paid',
            'payment_method'  => 'pix',
        ]);

        // Pagamento trimestral atual — VENCIDO (venceu há 5 dias)
        Payment::create([
            'student_plan_id' => $sp3->id,
            'student_id'      => $student2,
            'personal_id'     => $personalId,
            'amount'          => 680.00,
            'original_amount' => 680.00,
            'due_date'        => $now->copy()->subDays(5),
            'paid_at'         => null,
            'status'          => 'overdue',
            'notes'           => 'Trimestral vencido.',
        ]);

        // ─────────────────────────────────────────
        // PLANO MENSAL — LUIZ MATEUS — vencido em janeiro (histórico)
        // Representa um mês que ficou sem pagar antes de regularizar
        // ─────────────────────────────────────────
        $sp4 = StudentPlan::create([
            'student_id'        => $student1,
            'personal_id'       => $personalId,
            'financial_plan_id' => $planOnline->id,
            'start_date'        => Carbon::create(2025, 11, 1),
            'due_date'          => Carbon::create(2026, 1, 10),
            'periodicity'       => 'monthly',
            'status'            => 'overdue',
        ]);

        // Novembro — PAGO
        Payment::create([
            'student_plan_id' => $sp4->id,
            'student_id'      => $student1,
            'personal_id'     => $personalId,
            'amount'          => 180.00,
            'original_amount' => 180.00,
            'due_date'        => Carbon::create(2025, 11, 10),
            'paid_at'         => Carbon::create(2025, 11, 10),
            'status'          => 'paid',
            'payment_method'  => 'cash',
        ]);

        // Dezembro — PAGO
        Payment::create([
            'student_plan_id' => $sp4->id,
            'student_id'      => $student1,
            'personal_id'     => $personalId,
            'amount'          => 180.00,
            'original_amount' => 180.00,
            'due_date'        => Carbon::create(2025, 12, 10),
            'paid_at'         => Carbon::create(2025, 12, 12),
            'status'          => 'paid',
            'payment_method'  => 'pix',
        ]);

        // Janeiro — VENCIDO (não pagou)
        Payment::create([
            'student_plan_id' => $sp4->id,
            'student_id'      => $student1,
            'personal_id'     => $personalId,
            'amount'          => 180.00,
            'original_amount' => 180.00,
            'due_date'        => Carbon::create(2026, 1, 10),
            'paid_at'         => null,
            'status'          => 'overdue',
            'notes'           => 'Não quitado em janeiro.',
        ]);

        $this->command->info('');
        $this->command->info('Dados de teste criados com sucesso!');
        $this->command->info('');
        $this->command->table(
            ['Aluno', 'Plano', 'Status Vínculo'],
            [
                ['LUIZ MATEUS', 'Mensalidade R$250', 'Ativo'],
                ['gremio',      'Consultoria R$180', 'Suspenso (inadimplente)'],
            ]
        );
        $this->command->info('Pagamentos:');
        $this->command->info('  PAGO     Jan — Luiz Mateus  R$ 250,00 (Pix)');
        $this->command->info('  PAGO     Fev — Luiz Mateus  R$ 225,00 (Dinheiro, desc. R$25)');
        $this->command->info('  PAGO     Mar — Luiz Mateus  R$ 250,00 (Cartão)');
        $this->command->info('  PENDENTE Abr — Luiz Mateus  R$ 250,00');
        $this->command->info('  PAGO     Fev — gremio       R$ 180,00 (Pix)');
        $this->command->info('  VENCIDO  Mar — gremio       R$ 180,00 (16d atraso)');
        $this->command->info('');
        $this->command->info('Dashboard Março:');
        $this->command->info('  Faturamento Esperado : R$ 430,00 (250 recebido + 180 vencido)');
        $this->command->info('  Total Recebido       : R$ 250,00');
        $this->command->info('  Total Pendente       : R$ 0,00   (abril nao conta em marco)');
        $this->command->info('  Total Vencido        : R$ 180,00');
    }
}
