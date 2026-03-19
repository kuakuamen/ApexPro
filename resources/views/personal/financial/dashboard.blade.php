@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<div class="py-6 space-y-6">
    @include('personal.financial._nav', ['activeTab' => 'dashboard'])

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-600 flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-100">Financeiro</h1>
                <p class="text-sm text-slate-400">Resumo do mês atual</p>
            </div>
        </div>
        <form method="POST" action="{{ route('personal.financial.generate') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Gerar Cobranças
            </button>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        @php
            $currentMonth = now()->format('Y-m');
            $kpis = [
                ['label' => 'Faturamento Esperado',  'value' => 'R$ '.number_format($faturamento, 2, ',', '.'), 'color' => 'cyan',    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'href' => route('personal.financial.payments', ['month' => $currentMonth])],
                ['label' => 'Total Recebido',   'value' => 'R$ '.number_format($recebido,    2, ',', '.'), 'color' => 'emerald', 'icon' => 'M5 13l4 4L19 7',                                                                                                                                                                                                                                                                                                                              'href' => route('personal.financial.payments', ['status' => 'paid', 'month' => $currentMonth])],
                ['label' => 'Total Pendente',   'value' => 'R$ '.number_format($pendente,    2, ',', '.'), 'color' => 'yellow',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                                                                                                                               'href' => route('personal.financial.payments', ['status' => 'pending'])],
                ['label' => 'Total Vencido',    'value' => 'R$ '.number_format($vencido,     2, ',', '.'), 'color' => 'orange',  'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',                                                                                                                                                                                                  'href' => route('personal.financial.payments', ['status' => 'overdue'])],
                ['label' => 'Alunos Ativos',    'value' => $alunosAtivos,                                  'color' => 'purple',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',                                                                                                                                'href' => route('personal.financial.student-plans', ['status' => 'active'])],
                ['label' => 'Inadimplentes',    'value' => $alunosInadimplentes,                           'color' => 'red',     'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',                                                                                                                                                                                                                                             'href' => route('personal.financial.student-plans', ['status' => 'overdue'])],
            ];
            $colorMap = [
                'cyan'    => ['bg' => 'bg-cyan-500/10',    'border' => 'border-cyan-500/30',    'hover' => 'hover:bg-cyan-500/20 hover:border-cyan-500/50',       'text' => 'text-cyan-300',    'icon' => 'text-cyan-400'],
                'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/30', 'hover' => 'hover:bg-emerald-500/20 hover:border-emerald-500/50', 'text' => 'text-emerald-300', 'icon' => 'text-emerald-400'],
                'yellow'  => ['bg' => 'bg-yellow-500/10',  'border' => 'border-yellow-500/30',  'hover' => 'hover:bg-yellow-500/20 hover:border-yellow-500/50',   'text' => 'text-yellow-300',  'icon' => 'text-yellow-400'],
                'orange'  => ['bg' => 'bg-orange-500/10',  'border' => 'border-orange-500/30',  'hover' => 'hover:bg-orange-500/20 hover:border-orange-500/50',   'text' => 'text-orange-300',  'icon' => 'text-orange-400'],
                'purple'  => ['bg' => 'bg-purple-500/10',  'border' => 'border-purple-500/30',  'hover' => 'hover:bg-purple-500/20 hover:border-purple-500/50',   'text' => 'text-purple-300',  'icon' => 'text-purple-400'],
                'red'     => ['bg' => 'bg-red-500/10',     'border' => 'border-red-500/30',     'hover' => 'hover:bg-red-500/20 hover:border-red-500/50',         'text' => 'text-red-300',     'icon' => 'text-red-400'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        @php $c = $colorMap[$kpi['color']]; @endphp
        <a href="{{ $kpi['href'] }}" class="{{ $c['bg'] }} {{ $c['border'] }} {{ $c['hover'] }} border rounded-2xl p-4 block transition-all duration-150 cursor-pointer group">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/></svg>
                <p class="text-xs text-slate-400 uppercase tracking-wide leading-tight">{{ $kpi['label'] }}</p>
            </div>
            <p class="text-2xl font-bold {{ $c['text'] }}">{{ $kpi['value'] }}</p>
            <p class="text-[10px] text-slate-500 mt-1 group-hover:text-slate-400 transition-colors">Ver detalhes →</p>
        </a>
        @endforeach
    </div>

    {{-- Gráfico + Próximos vencimentos --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Gráfico receita 12 meses --}}
        <div class="lg:col-span-2 bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">Receita Mensal (últimos 12 meses)</h3>
            <div style="height:220px">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Próximos vencimentos --}}
        <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-slate-300 mb-4">Próximos Vencimentos <span class="text-xs text-slate-500 font-normal">(7 dias)</span></h3>
            @if($proximosVencimentos->isEmpty())
                <p class="text-slate-500 text-sm text-center py-6">Nenhum vencimento nos próximos 7 dias.</p>
            @else
            <div class="space-y-3">
                @foreach($proximosVencimentos as $sp)
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-cyan-500/15 flex items-center justify-center text-cyan-200 text-xs font-bold shrink-0">
                            {{ strtoupper(substr($sp->student->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm text-slate-200 truncate">{{ $sp->student->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $sp->financialPlan->name }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-medium text-yellow-400 shrink-0">{{ $sp->due_date->format('d/m/Y') }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<script>
(function() {
    const labels = @json(collect($receitaMensal)->pluck('label'));
    const values = @json(collect($receitaMensal)->pluck('value'));
    const ctx    = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Receita Recebida (R$)',
                data: values,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.12)',
                fill: true, tension: 0.4,
                pointBackgroundColor: '#10b981', pointRadius: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                x: { grid: { color: 'rgba(71,85,105,0.2)' }, ticks: { color: '#64748b', font: { size: 11 } } },
                y: { grid: { color: 'rgba(71,85,105,0.2)' }, ticks: { color: '#64748b', font: { size: 11 }, callback: v => 'R$ '+v } }
            },
            plugins: {
                legend: { labels: { color: '#94a3b8' } },
                tooltip: { callbacks: { label: ctx => 'R$ ' + ctx.parsed.y.toFixed(2).replace('.',',') } }
            }
        }
    });
})();
</script>
@endsection
