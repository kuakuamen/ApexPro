<?php

namespace App\Http\Controllers;

use App\Models\FinancialPlan;
use App\Models\StudentPlan;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Pendente = cobranças ainda dentro do prazo (due_date >= hoje, qualquer mês)
        $pendente = Payment::where('personal_id', $personalId)
            ->where('status', 'pending')
            ->where('due_date', '>=', $now->copy()->startOfDay())
            ->sum('amount');

        // Vencido = overdue + pending cujo prazo já passou (qualquer mês)
        $vencido = Payment::where('personal_id', $personalId)
            ->where(function ($q) use ($now) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($q2) use ($now) {
                      $q2->where('status', 'pending')
                         ->where('due_date', '<', $now->copy()->startOfDay());
                  });
            })
            ->sum('amount');

        // Faturamento = caixa real do mês (paid_at em março) + em aberto com vencimento em março
        // Cobre tanto pagamentos antecipados quanto cobranças normais do mês
        $faturamento = Payment::where('personal_id', $personalId)
            ->where(function ($q) use ($now) {
                $q->where(function ($q2) use ($now) {
                    // Dinheiro recebido em março (qualquer vencimento, ex: pagamento antecipado de abril)
                    $q2->where('status', 'paid')
                       ->whereYear('paid_at', $now->year)
                       ->whereMonth('paid_at', $now->month);
                })->orWhere(function ($q2) use ($now) {
                    // Cobranças com vencimento em março ainda em aberto
                    $q2->whereIn('status', ['pending', 'overdue'])
                       ->whereYear('due_date', $now->year)
                       ->whereMonth('due_date', $now->month);
                });
            })
            ->sum('amount');

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
            'student_id'       => 'required|exists:users,id',
            'financial_plan_id'=> 'required|exists:financial_plans,id',
            'start_date'       => 'required|date',
            'periodicity'      => 'required|in:monthly,quarterly,semiannual,annual,custom',
            'custom_days'      => 'nullable|integer|min:1|required_if:periodicity,custom',
        ]);

        $plan = FinancialPlan::findOrFail($data['financial_plan_id']);
        $this->validatePlanOwnership($plan);

        $isOwnStudent = $this->personal()->students()->where('users.id', $data['student_id'])->exists();
        if (!$isOwnStudent) abort(403);

        $data['due_date']    = $this->calcDueDate($data['start_date'], $data['periodicity'], $data['custom_days'] ?? null);
        $data['personal_id'] = Auth::id();
        $data['status']      = 'active';

        $sp = StudentPlan::create($data);

        // Criar primeira cobrança automaticamente
        Payment::create([
            'student_plan_id' => $sp->id,
            'student_id'      => $sp->student_id,
            'personal_id'     => Auth::id(),
            'amount'          => $plan->price,
            'due_date'        => $sp->due_date,
            'status'          => 'pending',
        ]);

        return redirect()->route('personal.financial.student-plans')
            ->with('success', 'Plano vinculado ao aluno com sucesso!');
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
            'financial_plan_id'=> 'required|exists:financial_plans,id',
            'start_date'       => 'required|date',
            'periodicity'      => 'required|in:monthly,quarterly,semiannual,annual,custom',
            'custom_days'      => 'nullable|integer|min:1|required_if:periodicity,custom',
            'status'           => 'required|in:active,overdue,suspended',
        ]);

        $data['due_date'] = $this->calcDueDate($data['start_date'], $data['periodicity'], $data['custom_days'] ?? null);

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

    // ─── Pagamentos ───────────────────────────────────────────────────────────

    public function payments(Request $request)
    {
        $pid  = Auth::id();
        $now  = now()->startOfDay();
        $tab  = in_array($request->tab, ['pending', 'overdue', 'paid']) ? $request->tab : 'pending';

        $base = fn() => Payment::with('student', 'studentPlan.financialPlan')
            ->where('personal_id', $pid);

        // Contagens para os badges de cada aba
        $countPending = (clone $base())
            ->where('status', 'pending')->where('due_date', '>=', $now)->count();
        $countOverdue = (clone $base())
            ->where(fn($q) => $q->where('status', 'overdue')
                ->orWhere(fn($q2) => $q2->where('status', 'pending')->where('due_date', '<', $now)))
            ->count();
        $countPaid = (clone $base())->where('status', 'paid')->count();

        // Dados da aba ativa
        $query = $base();
        if ($tab === 'pending') {
            $query->where('status', 'pending')->where('due_date', '>=', $now)->orderBy('due_date');
        } elseif ($tab === 'overdue') {
            $query->where(fn($q) => $q->where('status', 'overdue')
                ->orWhere(fn($q2) => $q2->where('status', 'pending')->where('due_date', '<', $now)))
                ->orderBy('due_date');
        } else {
            $query->where('status', 'paid')->orderByDesc('paid_at');
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $payments = $query->paginate(20)->withQueryString();

        $students = $this->personal()->students()->orderBy('users.name')->get(['users.id', 'users.name']);
        $studentPlansActive = StudentPlan::with('financialPlan', 'student')
            ->where('personal_id', $pid)->get();

        return view('personal.financial.payments.index', compact(
            'payments', 'students', 'studentPlansActive', 'tab',
            'countPending', 'countOverdue', 'countPaid'
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
        ]);

        $p->update([
            'status'         => 'paid',
            'paid_at'        => Carbon::today(),
            'payment_method' => $request->payment_method,
        ]);

        // Recalcular próximo vencimento no StudentPlan
        $sp = $p->studentPlan;
        if ($sp) {
            $nextDue = $sp->financialPlan->nextDueDate($p->due_date);

            $sp->update([
                'due_date' => $nextDue,
                'status'   => 'active',
            ]);

            // Criar próxima cobrança somente se não existir uma para esse vencimento
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
            'status'         => $newStatus,
            'paid_at'        => null,
            'payment_method' => null,
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

    public function reports(Request $request)
    {
        $personalId = Auth::id();
        $year       = $request->input('year', Carbon::now()->year);

        // Faturamento por mês no ano
        $faturamentoPorMes = [];
        for ($m = 1; $m <= 12; $m++) {
            $faturamentoPorMes[] = [
                'label'    => Carbon::create($year, $m)->translatedFormat('M'),
                'recebido' => (float) Payment::where('personal_id', $personalId)->where('status', 'paid')
                    ->whereYear('paid_at', $year)->whereMonth('paid_at', $m)->sum('amount'),
                'pendente' => (float) Payment::where('personal_id', $personalId)->whereIn('status', ['pending', 'overdue'])
                    ->whereYear('due_date', $year)->whereMonth('due_date', $m)->sum('amount'),
            ];
        }

        // Inadimplentes
        $inadimplentes = StudentPlan::with('student', 'financialPlan')
            ->where('personal_id', $personalId)
            ->whereIn('status', ['overdue', 'suspended'])
            ->get();

        // Histórico por aluno (filtro)
        $studentId = $request->input('student_id');
        $historyPayments = collect();
        $historyStudent  = null;
        if ($studentId) {
            $historyStudent  = User::find($studentId);
            $historyPayments = Payment::with('studentPlan.financialPlan')
                ->where('personal_id', $personalId)
                ->where('student_id', $studentId)
                ->orderBy('due_date', 'desc')
                ->get();
        }

        $students = $this->personal()->students()->orderBy('users.name')->get(['users.id', 'users.name']);

        return view('personal.financial.reports', compact(
            'faturamentoPorMes', 'inadimplentes', 'historyPayments',
            'historyStudent', 'students', 'year'
        ));
    }
}
