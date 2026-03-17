@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<div class="py-6 space-y-6">
    @include('personal.financial._nav', ['activeTab' => 'reports'])

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-100">Relatórios Financeiros</h1>
        <form method="GET" action="{{ route('personal.financial.reports') }}" class="flex items-center gap-2">
            <select name="year" class="bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2 text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-4 py-2 bg-cyan-600/20 border border-cyan-500/40 rounded-xl text-cyan-300 text-sm">Ver</button>
        </form>
    </div>

    {{-- Gráfico Faturamento por Mês --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-slate-300 mb-4">Faturamento Mensal {{ $year }}</h3>
        <div style="height: 240px">
            <canvas id="annualChart"></canvas>
        </div>
    </div>

    {{-- Inadimplentes --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700/40 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-300">Alunos Inadimplentes</h3>
            <span class="bg-red-500/15 text-red-300 text-xs font-medium px-2 py-0.5 rounded-full">{{ $inadimplentes->count() }}</span>
        </div>
        @if($inadimplentes->isEmpty())
            <div class="p-8 text-center"><p class="text-slate-400 text-sm">Nenhum aluno inadimplente.</p></div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Aluno</th>
                    <th class="px-5 py-3 text-left">Plano</th>
                    <th class="px-5 py-3 text-center">Vencimento</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-right">Valor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40">
                @foreach($inadimplentes as $sp)
                <tr class="hover:bg-slate-800/30">
                    <td class="px-5 py-3 text-slate-100 font-medium">{{ $sp->student->name }}</td>
                    <td class="px-5 py-3 text-slate-300">{{ $sp->financialPlan->name }}</td>
                    <td class="px-5 py-3 text-center text-red-400">{{ $sp->due_date->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-center">
                        @php $c = $sp->status === 'suspended' ? 'red' : 'yellow'; @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $c }}-500/15 text-{{ $c }}-300">{{ $sp->statusLabel() }}</span>
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-emerald-400">R$ {{ number_format($sp->financialPlan->price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Histórico por Aluno --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700/40">
            <h3 class="text-sm font-semibold text-slate-300 mb-3">Histórico por Aluno</h3>
            <form method="GET" action="{{ route('personal.financial.reports') }}" class="flex items-center gap-3">
                <input type="hidden" name="year" value="{{ $year }}">
                <select name="student_id" class="bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2 text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                    <option value="">Selecione um aluno</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-cyan-600/20 border border-cyan-500/40 rounded-xl text-cyan-300 text-sm">Ver Histórico</button>
            </form>
        </div>

        @if($historyStudent)
            <div class="px-5 py-3 border-b border-slate-700/30 bg-slate-800/20">
                <p class="text-sm text-slate-300">Histórico de <span class="font-semibold text-cyan-300">{{ $historyStudent->name }}</span>
                    — Total recebido: <span class="font-semibold text-emerald-400">R$ {{ number_format($historyPayments->where('status','paid')->sum('amount'), 2, ',', '.') }}</span>
                </p>
            </div>
            @if($historyPayments->isEmpty())
                <div class="p-8 text-center"><p class="text-slate-400 text-sm">Nenhum pagamento encontrado.</p></div>
            @else
            <table class="w-full text-sm">
                <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Plano</th>
                        <th class="px-5 py-3 text-right">Valor</th>
                        <th class="px-5 py-3 text-center">Vencimento</th>
                        <th class="px-5 py-3 text-center">Pago em</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40">
                    @foreach($historyPayments as $p)
                    @php $sc = ['paid'=>'emerald','pending'=>'yellow','overdue'=>'red']; $c = $sc[$p->status] ?? 'gray'; @endphp
                    <tr class="hover:bg-slate-800/30">
                        <td class="px-5 py-3 text-slate-300">{{ $p->studentPlan?->financialPlan?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-emerald-400">R$ {{ number_format($p->amount, 2, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center text-slate-300">{{ $p->due_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-center text-slate-400">{{ $p->paid_at ? $p->paid_at->format('d/m/Y') : '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $c }}-500/15 text-{{ $c }}-300">{{ $p->statusLabel() }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        @else
            <div class="p-8 text-center"><p class="text-slate-500 text-sm">Selecione um aluno para ver o histórico.</p></div>
        @endif
    </div>
</div>

<script>
(function() {
    const labels   = @json(collect($faturamentoPorMes)->pluck('label'));
    const recebido = @json(collect($faturamentoPorMes)->pluck('recebido'));
    const pendente = @json(collect($faturamentoPorMes)->pluck('pendente'));
    const ctx      = document.getElementById('annualChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'Recebido',  data: recebido, backgroundColor: 'rgba(16,185,129,0.7)',  borderRadius: 4 },
                { label: 'Pendente',  data: pendente, backgroundColor: 'rgba(234,179,8,0.5)',   borderRadius: 4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                x: { grid: { color: 'rgba(71,85,105,0.2)' }, ticks: { color: '#64748b', font: { size: 11 } } },
                y: { grid: { color: 'rgba(71,85,105,0.2)' }, ticks: { color: '#64748b', font: { size: 11 }, callback: v => 'R$ '+v } }
            },
            plugins: {
                legend: { labels: { color: '#94a3b8' } },
                tooltip: { callbacks: { label: ctx => ctx.dataset.label+': R$ '+ctx.parsed.y.toFixed(2).replace('.',',') } }
            }
        }
    });
})();
</script>
@endsection
