<?php

namespace App\Http\Controllers;

use App\Exports\FinancialReportExport;
use App\Models\FinancialPlan;
use App\Models\StudentPlan;
use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FinancialController extends Controller
{
    protected function personal(): User
    {
        return Auth::user();
    }

    protected function validatePlanOwnership(FinancialPlan $plan): void
    {
        if ($plan->personal_id !== Auth::id()) abort(403);
    }

    protected function validateStudentPlanOwnership(StudentPlan $sp): void
    {
        if ($sp->personal_id !== Auth::id()) abort(403);
    }

    protected function validatePaymentOwnership(Payment $payment): void
    {
        if ($payment->personal_id !== Auth::id()) abort(403);
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $personalId = Auth::id();
        $now        = Carbon::now();
        $startMonth = $now->copy()->startOfMonth();
        $endMonth   = $now->copy()->endOfMonth();

        // Recebido = dinheiro que entrou no caixa em março (independente do mês de vencimento)
        $recebido = Payment::where('personal_id', $personalId)
            ->where('status', 'paid')
            ->whereYear('paid_at', $now->year)
            ->whereMonth('paid_at', $now->month)
            ->sum('amount');

        // Pendente = cobranças com vencimento no mês atual ainda não pagas
        $pendente = Payment::where('personal_id', $personalId)
            ->where('status', 'pending')
            ->whereYear('due_date', $now->year)
            ->whereMonth('due_date', $now->month)
            ->sum('amount');

        // Vencido = cobranças com vencimento no mês atual que já passaram sem pagamento
        $vencido = Payment::where('personal_id', $personalId)
            ->where('status', 'overdue')
            ->whereYear('due_date', $now->year)
            ->whereMonth('due_date', $now->month)
            ->sum('amount');

        // Faturamento Esperado = Recebido + Pendente + Vencido do mês atual
        $faturamento = $recebido + $pendente + $vencido;

        $alunosAtivos = StudentPlan::where('personal_id', $personalId)
            ->where('status', 'active')->count();

        $alunosInadimplentes = StudentPlan::where('personal_id', $personalId)
            ->whereIn('status', ['overdue', 'suspended'])->count();

        // Receita mensal últimos 12 meses
        $receitaMensal = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes   = $now->copy()->subMonths($i);
            $label = $mes->translatedFormat('M/Y');
            $valor = Payment::where('personal_id', $personalId)
                ->where('status', 'paid')
                ->whereYear('paid_at', $mes->year)
                ->whereMonth('paid_at', $mes->month)
                ->sum('amount');
            $receitaMensal[] = ['label' => $label, 'value' => (float) $valor];
        }

        // Próximos vencimentos — apenas dentro de 7 dias
        $proximosVencimentos = StudentPlan::with('student', 'financialPlan')
            ->where('personal_id', $personalId)
            ->where('status', 'active')
            ->whereBetween('due_date', [$now->copy()->startOfDay(), $now->copy()->addDays(7)->endOfDay()])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('personal.financial.dashboard', compact(
            'faturamento', 'recebido', 'pendente', 'vencido',
            'alunosAtivos', 'alunosInadimplentes',
            'receitaMensal', 'proximosVencimentos'
        ));
    }

    // ─── Planos Financeiros ───────────────────────────────────────────────────

    public function plans()
    {
        $plans = FinancialPlan::where('personal_id', Auth::id())
            ->withCount('studentPlans')
            ->orderBy('name')
            ->get();
        return view('personal.financial.plans.index', compact('plans'));
    }

    public function createPlan()
    {
        return view('personal.financial.plans.create');
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'periodicity' => 'required|in:monthly,quarterly,semiannual,annual,custom',
            'custom_days' => 'nullable|integer|min:1|required_if:periodicity,custom',
        ]);

        $data['personal_id'] = Auth::id();
        $data['active']      = true;

        FinancialPlan::create($data);

        return redirect()->route('personal.financial.plans')
            ->with('success', 'Plano criado com sucesso!');
    }

    public function editPlan(FinancialPlan $plan)
    {
        $this->validatePlanOwnership($plan);
        return view('personal.financial.plans.edit', compact('plan'));
    }

    public function updatePlan(Request $request, FinancialPlan $plan)
    {
        $this->validatePlanOwnership($plan);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'periodicity' => 'required|in:monthly,quarterly,semiannual,annual,custom',
            'custom_days' => 'nullable|integer|min:1|required_if:periodicity,custom',
            'active'      => 'boolean',
        ]);

        $plan->update($data);

        return redirect()->route('personal.financial.plans')
            ->with('success', 'Plano atualizado com sucesso!');
    }

    public function destroyPlan(FinancialPlan $plan)
    {
        $this->validatePlanOwnership($plan);

        if ($plan->studentPlans()->exists()) {
            $plan->update(['active' => false]);
            return back()->with('success', 'Plano desativado (possui alunos vinculados).');
        }

        $plan->delete();
        return redirect()->route('personal.financial.plans')
            ->with('success', 'Plano excluído com sucesso!');
    }

    // ─── Vínculos Aluno ↔ Plano ──────────────────────────────────────────────

    public function studentPlans(Request $request)
    {
        $query = StudentPlan::with('student', 'financialPlan')
            ->where('personal_id', Auth::id());

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->whereIn('status', ['overdue', 'suspended']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $studentPlans = $query
            ->orderByRaw("FIELD(status,'overdue','suspended','active')")
            ->orderBy('due_date')
            ->get();

        return view('personal.financial.student-plans.index', compact('studentPlans'));
    }

    public function assignPlan()
    {
        $personal   = $this->personal();
        $students   = $personal->students()->orderBy('users.name')->get(['users.id', 'users.name']);
        $plans      = FinancialPlan::where('personal_id', Auth::id())->where('active', true)->orderBy('name')->get();
        return view('personal.financial.student-plans.create', compact('students', 'plans'));
    }

    protected function calcDueDate(string $startDate, string $periodicity, ?int $customDays): Carbon
    {
        $date = Carbon::parse($startDate);
        return match($periodicity) {
            'monthly'    => $date->addMonth(),
            'quarterly'  => $date->addMonths(3),
            'semiannual' => $date->addMonths(6),
            'annual'     => $date->addYear(),
            'custom'     => $date->addDays($customDays ?? 30),
            default      => $date->addMonth(),
        };
    }

    public function storePlanAssignment(Request $request)
    {
        $data = $request->validate([
            'student_id'        => 'required|exists:users,id',
            'financial_plan_id' => 'required|exists:financial_plans,id',
            'start_date'        => 'required|date',
            'due_date'          => 'required|date',
            'periodicity'       => 'required|in:monthly,quarterly,semiannual,annual,custom',
            'custom_days'       => 'nullable|integer|min:1|required_if:periodicity,custom',
            'payment_method'    => 'nullable|in:pix,card,cash,other',
            'skip_payment'      => 'nullable|in:0,1',
            'discount_type'     => 'nullable|in:none,fixed,percent',
            'discount_value'    => 'nullable|numeric|min:0',
        ]);

        $plan = FinancialPlan::findOrFail($data['financial_plan_id']);
        $this->validatePlanOwnership($plan);

        $isOwnStudent = $this->personal()->students()->where('users.id', $data['student_id'])->exists();
        if (!$isOwnStudent) abort(403);

        // Bloquear duplo vínculo
        $planAtivo = StudentPlan::where('student_id', $data['student_id'])
            ->where('personal_id', Auth::id())
            ->whereIn('status', ['active', 'overdue'])
            ->first();

        if ($planAtivo) {
            return back()
                ->withInput()
                ->withErrors(['student_id' => 'Este aluno já possui o plano "' . $planAtivo->financialPlan->name . '" ativo. Para trocar, edite o vínculo existente.']);
        }

        $sp = StudentPlan::create([
            'student_id'        => $data['student_id'],
            'personal_id'       => Auth::id(),
            'financial_plan_id' => $data['financial_plan_id'],
            'start_date'        => $data['start_date'],
            'due_date'          => $data['due_date'],
            'periodicity'       => $data['periodicity'],
            'custom_days'       => $data['custom_days'] ?? null,
            'status'            => 'active',
        ]);

        // 1) Pagamento do mês atual — PAGO ou PENDENTE conforme escolha
        $skipPayment  = ($data['skip_payment'] ?? '0') === '1';
        $discountType = $data['discount_type'] ?? 'none';
        $discountVal  = (float) ($data['discount_value'] ?? 0);

        $entryAmount = $plan->price;
        if (!$skipPayment && $discountType !== 'none' && $discountVal > 0) {
            if ($discountType === 'fixed') {
                $entryAmount = max(0, $plan->price - $discountVal);
            } elseif ($discountType === 'percent') {
                $entryAmount = max(0, $plan->price - ($plan->price * $discountVal / 100));
            }
        }

        Payment::create([
            'student_plan_id' => $sp->id,
            'student_id'      => $sp->student_id,
            'personal_id'     => Auth::id(),
            'amount'          => $entryAmount,
            'original_amount' => $plan->price,
            'due_date'        => $data['start_date'],
            'paid_at'         => $skipPayment ? null : now(),
            'status'          => $skipPayment ? 'pending' : 'paid',
            'payment_method'  => $skipPayment ? null : ($data['payment_method'] ?? null),
        ]);

        // 2) Próximo vencimento — PENDENTE
        Payment::create([
            'student_plan_id' => $sp->id,
            'student_id'      => $sp->student_id,
            'personal_id'     => Auth::id(),
            'amount'          => $plan->price,
            'original_amount' => $plan->price,
            'due_date'        => $data['due_date'],
            'paid_at'         => null,
            'status'          => 'pending',
        ]);

        return redirect()->route('personal.financial.student-plans')
            ->with('success', $skipPayment
                ? 'Plano vinculado! Ambos os pagamentos registrados como Pendente.'
                : 'Plano vinculado! Pagamento de hoje registrado e próximo vencimento agendado.'
            );
    }

    public function editAssignment(StudentPlan $sp)
    {
        $this->validateStudentPlanOwnership($sp);
        $sp->load('student', 'financialPlan');
        $plans = FinancialPlan::where('personal_id', Auth::id())->where('active', true)->orderBy('name')->get();
        return view('personal.financial.student-plans.edit', compact('sp', 'plans'));
    }

    public function updateAssignment(Request $request, StudentPlan $sp)
    {
        $this->validateStudentPlanOwnership($sp);

        $data = $request->validate([
            'financial_plan_id' => 'required|exists:financial_plans,id',
            'start_date'        => 'required|date',
            'due_date'          => 'required|date',
            'periodicity'       => 'required|in:monthly,quarterly,semiannual,annual,custom',
            'custom_days'       => 'nullable|integer|min:1|required_if:periodicity,custom',
            'status'            => 'required|in:active,overdue,suspended',
        ]);

        $sp->update($data);

        return redirect()->route('personal.financial.student-plans')
            ->with('success', 'Vínculo atualizado com sucesso!');
    }

    public function toggleStudentAccess(StudentPlan $sp)
    {
        $this->validateStudentPlanOwnership($sp);

        $sp->update([
            'status' => $sp->status === 'suspended' ? 'active' : 'suspended',
        ]);

        $msg = $sp->fresh()->status === 'suspended'
            ? 'Acesso do aluno suspenso.'
            : 'Acesso do aluno liberado.';

        return back()->with('success', $msg);
    }

    public function destroyAssignment(StudentPlan $sp)
    {
        $this->validateStudentPlanOwnership($sp);

        $sp->payments()->delete();
        $sp->delete();

        return redirect()->route('personal.financial.student-plans')
            ->with('success', 'Vínculo e todos os pagamentos foram excluídos permanentemente.');
    }

    // ─── Pagamentos ───────────────────────────────────────────────────────────

    public function payments(Request $request)
    {
        $pid       = Auth::id();
        $now       = now()->startOfDay();
        $weekAhead = now()->addDays(7)->endOfDay();
        $tab       = in_array($request->tab, ['pending', 'all_pending', 'overdue', 'paid'])
                        ? $request->tab : 'pending';

        $base = fn() => Payment::with('student', 'studentPlan.financialPlan')
            ->where('personal_id', $pid);

        // Contagens para os badges (sem filtro de busca, sempre o total real)
        $countPending    = (clone $base())->where('status', 'pending')
                            ->whereBetween('due_date', [$now, $weekAhead])->count();
        $countAllPending = (clone $base())->where('status', 'pending')
                            ->where('due_date', '>=', $now)->count();
        $countOverdue    = (clone $base())->where(
                            fn($q) => $q->where('status', 'overdue')
                                ->orWhere(fn($q2) => $q2->where('status', 'pending')->where('due_date', '<', $now))
                            )->count();
        $countPaid       = (clone $base())->where('status', 'paid')->count();

        // Query da aba ativa
        $query = $base();
        match ($tab) {
            'pending'     => $query->where('status', 'pending')
                                   ->whereBetween('due_date', [$now, $weekAhead])
                                   ->orderBy('due_date'),
            'all_pending' => $query->where('status', 'pending')
                                   ->where('due_date', '>=', $now)
                                   ->orderBy('due_date'),
            'overdue'     => $query->where(fn($q) => $q->where('status', 'overdue')
                                ->orWhere(fn($q2) => $q2->where('status', 'pending')->where('due_date', '<', $now)))
                                ->orderBy('due_date'),
            'paid'        => $query->where('status', 'paid')->orderByDesc('paid_at'),
        };

        // Busca por nome do aluno
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student', fn($q) => $q->where('name', 'like', "%{$s}%"));
        }

        $payments = $query->paginate(20)->withQueryString();

        $students = $this->personal()->students()->orderBy('users.name')->get(['users.id', 'users.name']);
        $studentPlansActive = StudentPlan::with('financialPlan', 'student')
            ->where('personal_id', $pid)->get();

        return view('personal.financial.payments.index', compact(
            'payments', 'students', 'studentPlansActive', 'tab',
            'countPending', 'countAllPending', 'countOverdue', 'countPaid'
        ));
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'student_plan_id' => 'required|exists:student_plans,id',
            'amount'          => 'required|numeric|min:0',
            'due_date'        => 'required|date',
            'status'          => 'required|in:paid,pending,overdue',
            'paid_at'         => 'nullable|date|required_if:status,paid',
            'notes'           => 'nullable|string|max:500',
        ]);

        $sp = StudentPlan::findOrFail($data['student_plan_id']);
        $this->validateStudentPlanOwnership($sp);

        $data['student_id']  = $sp->student_id;
        $data['personal_id'] = Auth::id();

        Payment::create($data);

        return redirect()->route('personal.financial.payments')
            ->with('success', 'Pagamento registrado com sucesso!');
    }

    public function markPaid(Request $request, Payment $p)
    {
        $this->validatePaymentOwnership($p);

        $request->validate([
            'payment_method' => 'required|in:pix,cartao,dinheiro,outro',
            'discount_type'  => 'nullable|in:value,percent',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        // Calcular desconto
        $originalAmount = $p->amount;
        $discountType   = $request->discount_type;
        $discountValue  = $request->filled('discount_value') ? (float) $request->discount_value : null;
        $finalAmount    = $originalAmount;

        if ($discountValue > 0 && $discountType) {
            if ($discountType === 'percent') {
                $discountValue = min($discountValue, 100);
                $finalAmount   = round($originalAmount * (1 - $discountValue / 100), 2);
            } else {
                $finalAmount = max(0, round($originalAmount - $discountValue, 2));
            }
        } else {
            $discountType  = null;
            $discountValue = null;
        }

        $p->update([
            'status'          => 'paid',
            'paid_at'         => Carbon::today(),
            'payment_method'  => $request->payment_method,
            'original_amount' => $originalAmount,
            'discount_type'   => $discountType,
            'discount_value'  => $discountValue,
            'amount'          => $finalAmount,
        ]);

        // Recalcular próximo vencimento no StudentPlan
        $sp = $p->studentPlan;
        if ($sp) {
            $nextDue = $sp->financialPlan->nextDueDate($p->due_date);

            $sp->update([
                'due_date' => $nextDue,
                'status'   => 'active',
            ]);

            $exists = Payment::where('student_plan_id', $sp->id)
                ->where('due_date', $nextDue)
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                Payment::create([
                    'student_plan_id' => $sp->id,
                    'student_id'      => $sp->student_id,
                    'personal_id'     => Auth::id(),
                    'amount'          => $sp->financialPlan->price,
                    'due_date'        => $nextDue,
                    'status'          => 'pending',
                ]);
            }
        }

        return back()->with('success', 'Pagamento confirmado com sucesso!');
    }

    public function reversePayment(Payment $p)
    {
        $this->validatePaymentOwnership($p);

        if ($p->status !== 'paid') {
            return back()->with('error', 'Apenas pagamentos com status "Pago" podem ser estornados.');
        }

        $newStatus = $p->due_date->isPast() ? 'overdue' : 'pending';

        $p->update([
            'status'          => $newStatus,
            'paid_at'         => null,
            'payment_method'  => null,
            'amount'          => $p->original_amount ?? $p->amount,
            'original_amount' => null,
            'discount_type'   => null,
            'discount_value'  => null,
        ]);

        // Desfazer o que markPaid fez: deletar o próximo pagamento auto-gerado e resetar due_date
        $sp = $p->studentPlan;
        if ($sp) {
            $nextDue = $sp->financialPlan->nextDueDate($p->due_date);

            // Remover o pagamento futuro gerado automaticamente (somente se ainda pendente)
            Payment::where('student_plan_id', $sp->id)
                ->where('due_date', $nextDue)
                ->where('status', 'pending')
                ->delete();

            // Resetar student_plan.due_date para o vencimento que foi estornado
            $sp->update([
                'due_date' => $p->due_date,
                'status'   => $newStatus === 'overdue' ? 'overdue' : 'active',
            ]);
        }

        return back()->with('success', 'Pagamento estornado. Status revertido para ' . ($newStatus === 'overdue' ? 'Vencido' : 'Pendente') . '.');
    }

    public function generateMonthlyPayments()
    {
        $personalId = Auth::id();
        $today      = Carbon::today();
        $generated  = 0;

        $studentPlans = StudentPlan::with('financialPlan')
            ->where('personal_id', $personalId)
            ->where('status', 'active')
            ->get();

        foreach ($studentPlans as $sp) {
            // Marcar overdue se vencimento já passou e não tem pagamento pendente do mês atual
            if ($sp->due_date->isPast()) {
                $hasPending = Payment::where('student_plan_id', $sp->id)
                    ->where('status', 'pending')
                    ->where('due_date', $sp->due_date)
                    ->exists();

                if (!$hasPending) {
                    // Criar cobrança overdue
                    Payment::create([
                        'student_plan_id' => $sp->id,
                        'student_id'      => $sp->student_id,
                        'personal_id'     => $personalId,
                        'amount'          => $sp->financialPlan->price,
                        'due_date'        => $sp->due_date,
                        'status'          => 'overdue',
                    ]);

                    $sp->update(['status' => 'overdue']);
                    $generated++;
                }
            }
        }

        return back()->with('success', "{$generated} cobrança(s) gerada(s) com sucesso.");
    }

    // ─── Relatórios ───────────────────────────────────────────────────────────

    public function runSuspendCheck()
    {
        $today     = \Carbon\Carbon::today();
        $threshold = \Carbon\Carbon::today()->subDays(5);

        Payment::where('status', 'pending')->whereDate('due_date', '<', $today)->update(['status' => 'overdue']);

        StudentPlan::where('status', 'active')
            ->whereDate('due_date', '<', $today)
            ->whereDate('due_date', '>=', $threshold)
            ->update(['status' => 'overdue']);

        $suspended = StudentPlan::whereIn('status', ['active', 'overdue'])
            ->whereDate('due_date', '<', $threshold)
            ->update(['status' => 'suspended']);

        $msg = $suspended > 0
            ? "{$suspended} aluno(s) suspenso(s) por inadimplência."
            : 'Nenhum novo aluno suspenso.';

        return back()->with('success', $msg);
    }

    public function reports(Request $request)
    {
        $data = $this->buildReportData($request);
        $students = $this->personal()->students()->orderBy('users.name')->get(['users.id', 'users.name']);
        $plans    = FinancialPlan::where('personal_id', Auth::id())->orderBy('name')->get(['id', 'name']);

        return view('personal.financial.reports', array_merge($data, compact('students', 'plans')));
    }

    public function exportPdf(Request $request)
    {
        $data     = $this->buildReportData($request);
        $personal = Auth::user();
        $pdf = Pdf::loadView('personal.financial.reports-pdf', array_merge($data, compact('personal')))
            ->setPaper('a4', 'landscape');

        $filename = 'relatorio-financeiro-' . $data['filterMonth'] . '-' . $data['filterYear'] . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $data     = $this->buildReportData($request);
        $filename = 'relatorio-financeiro-' . $data['filterMonth'] . '-' . $data['filterYear'] . '.xlsx';
        return Excel::download(new FinancialReportExport($data['pagamentosMes']), $filename);
    }

    private function buildReportData(Request $request): array
    {
        $personalId  = Auth::id();
        $now         = Carbon::now();
        $filterYear  = (int) $request->input('year',  $now->year);
        $filterMonth = (int) $request->input('month', $now->month); // 0 = todos os meses do ano
        $studentId   = $request->input('student_id');
        $statusFilter = $request->input('status_filter');
        $planFilter   = $request->input('plan_id');

        // ── Faturamento anual (gráfico existente) ──────────────────────────
        $faturamentoPorMes = [];
        for ($m = 1; $m <= 12; $m++) {
            $faturamentoPorMes[] = [
                'label'    => Carbon::create($filterYear, $m)->translatedFormat('M'),
                'recebido' => (float) Payment::where('personal_id', $personalId)->where('status', 'paid')
                    ->whereYear('paid_at', $filterYear)->whereMonth('paid_at', $m)->sum('amount'),
                'pendente' => (float) Payment::where('personal_id', $personalId)->whereIn('status', ['pending', 'overdue'])
                    ->whereYear('due_date', $filterYear)->whereMonth('due_date', $m)->sum('amount'),
            ];
        }

        // ── Relatório por período ──────────────────────────────────────────
        // Quando filterMonth=0, considera o ano inteiro
        $applyPeriodFilter = function ($query) use ($personalId, $filterYear, $filterMonth) {
            $query->where('personal_id', $personalId)->whereYear('due_date', $filterYear);
            if ($filterMonth > 0) {
                $query->whereMonth('due_date', $filterMonth);
            }
            return $query;
        };

        $baseQuery = fn() => $applyPeriodFilter(Payment::query());

        if ($filterMonth > 0) {
            $totalRecebido = (float) Payment::where('personal_id', $personalId)->where('status', 'paid')
                ->whereYear('paid_at', $filterYear)->whereMonth('paid_at', $filterMonth)->sum('amount');
        } else {
            $totalRecebido = (float) Payment::where('personal_id', $personalId)->where('status', 'paid')
                ->whereYear('paid_at', $filterYear)->sum('amount');
        }
        $totalPendente  = (float) $baseQuery()->where('status', 'pending')->sum('amount');
        $totalVencido   = (float) $baseQuery()->where('status', 'overdue')->sum('amount');
        $totalFaturado  = $totalRecebido + $totalPendente + $totalVencido;

        $pmQuery = Payment::with(['student', 'studentPlan.financialPlan'])
            ->where('personal_id', $personalId)
            ->where(function ($q) use ($filterYear, $filterMonth) {
                if ($filterMonth > 0) {
                    // Vence no mês selecionado OU foi pago no mês selecionado
                    $q->where(fn($q2) => $q2->whereYear('due_date', $filterYear)->whereMonth('due_date', $filterMonth))
                      ->orWhere(fn($q2) => $q2->where('status', 'paid')
                            ->whereYear('paid_at', $filterYear)->whereMonth('paid_at', $filterMonth));
                } else {
                    // Ano inteiro: vence no ano OU foi pago no ano
                    $q->where(fn($q2) => $q2->whereYear('due_date', $filterYear))
                      ->orWhere(fn($q2) => $q2->where('status', 'paid')->whereYear('paid_at', $filterYear));
                }
            });
        if ($statusFilter) $pmQuery->where('status', $statusFilter);
        if ($studentId)    $pmQuery->where('student_id', $studentId);
        if ($planFilter)   $pmQuery->whereHas('studentPlan', fn($q) => $q->where('financial_plan_id', $planFilter));
        $pagamentosMes = $pmQuery->orderBy('due_date')->get();

        // ── Métricas analíticas ────────────────────────────────────────────
        $activePlans = StudentPlan::with('financialPlan')
            ->where('personal_id', $personalId)->where('status', 'active')->get();

        $mrr = $activePlans->sum(function ($sp) {
            $price = (float) ($sp->financialPlan->price ?? 0);
            return match ($sp->periodicity) {
                'monthly'    => $price,
                'quarterly'  => $price / 3,
                'semiannual' => $price / 6,
                'annual'     => $price / 12,
                default      => $price,
            };
        });

        $qtdPagantesQ = Payment::where('personal_id', $personalId)->where('status', 'paid')
            ->whereYear('paid_at', $filterYear);
        if ($filterMonth > 0) $qtdPagantesQ->whereMonth('paid_at', $filterMonth);
        $qtdPagantes = $qtdPagantesQ->distinct('student_id')->count('student_id');
        $ticketMedio = $qtdPagantes > 0 ? $totalRecebido / $qtdPagantes : 0;

        $totalAtivos     = StudentPlan::where('personal_id', $personalId)->whereIn('status', ['active', 'overdue', 'suspended'])->count();
        $totalInadimpl   = StudentPlan::where('personal_id', $personalId)->whereIn('status', ['overdue', 'suspended'])->count();
        $taxaInadimplencia = $totalAtivos > 0 ? round($totalInadimpl / $totalAtivos * 100, 1) : 0;

        $ranking = Payment::where('personal_id', $personalId)->where('status', 'paid')
            ->where('paid_at', '>=', $now->copy()->subMonths(12))
            ->select('student_id', DB::raw('SUM(amount) as total'))
            ->groupBy('student_id')->orderByDesc('total')->limit(5)
            ->with('student')->get();

        // ── Faturamento Futuro (3 meses) ──────────────────────────────────
        $faturamentoFuturo = [];
        for ($i = 1; $i <= 3; $i++) {
            $mes = $now->copy()->addMonths($i);
            $plans = StudentPlan::with('financialPlan')
                ->where('personal_id', $personalId)->where('status', 'active')
                ->whereYear('due_date', $mes->year)->whereMonth('due_date', $mes->month)->get();
            $faturamentoFuturo[] = [
                'label'   => $mes->translatedFormat('F/Y'),
                'valor'   => $plans->sum(fn($sp) => (float) ($sp->financialPlan->price ?? 0)),
                'alunos'  => $plans->count(),
            ];
        }

        // ── Alertas ────────────────────────────────────────────────────────
        $alertasVencimento = StudentPlan::with(['student', 'financialPlan'])
            ->where('personal_id', $personalId)->where('status', 'active')
            ->whereBetween('due_date', [$now->toDateString(), $now->copy()->addDays(7)->toDateString()])
            ->orderBy('due_date')->get();

        $alertasInadimplentes = StudentPlan::with(['student', 'financialPlan'])
            ->where('personal_id', $personalId)->whereIn('status', ['overdue', 'suspended'])
            ->orderBy('due_date')->get();

        // ── Inadimplentes (lista) ─────────────────────────────────────────
        $inadimplentes = StudentPlan::with('student', 'financialPlan')
            ->where('personal_id', $personalId)->whereIn('status', ['overdue', 'suspended'])->get();

        // ── Histórico por aluno ───────────────────────────────────────────
        $historyPayments = collect();
        $historyStudent  = null;
        $historyTotal    = 0;
        if ($studentId) {
            $historyStudent  = User::find($studentId);
            $hQuery = Payment::with('studentPlan.financialPlan')
                ->where('personal_id', $personalId)->where('student_id', $studentId);
            $historyPayments = $hQuery->orderBy('due_date', 'desc')->get();
            $historyTotal    = $historyPayments->where('status', 'paid')->sum('amount');
        }

        return compact(
            'faturamentoPorMes', 'faturamentoFuturo',
            'totalFaturado', 'totalRecebido', 'totalPendente', 'totalVencido', 'pagamentosMes',
            'mrr', 'ticketMedio', 'taxaInadimplencia', 'ranking',
            'alertasVencimento', 'alertasInadimplentes', 'inadimplentes',
            'historyPayments', 'historyStudent', 'historyTotal',
            'filterYear', 'filterMonth', 'studentId', 'statusFilter', 'planFilter'
        );
    }
}
