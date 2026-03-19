@extends('layouts.app')
@section('content')

<div class="min-h-screen bg-[#060d1a] text-white p-4 md:p-6 space-y-6">

    {{-- ── Cabeçalho ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('personal.financial.dashboard') }}"
               class="w-8 h-8 rounded-lg bg-slate-700/50 hover:bg-slate-700 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-slate-100">Relatórios Financeiros</h1>
        </div>
        <form method="GET" action="{{ route('personal.financial.reports') }}" class="flex items-center gap-2 flex-wrap">
            <select name="month" class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500">
                <option value="0" @selected($filterMonth == 0)>Todos os meses</option>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" @selected($m == $filterMonth)>
                        {{ \Carbon\Carbon::create(null,$m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500">
                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                    <option value="{{ $y }}" @selected($y == $filterYear)>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Filtrar
            </button>
            <a href="{{ route('personal.financial.reports.export-pdf', array_merge(request()->query(), ['month'=>$filterMonth,'year'=>$filterYear])) }}"
               target="_blank"
               class="flex items-center gap-1.5 bg-rose-600/80 hover:bg-rose-600 text-white text-sm font-medium px-3 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
            </a>
            <a href="{{ route('personal.financial.reports.export-excel', array_merge(request()->query(), ['month'=>$filterMonth,'year'=>$filterYear])) }}"
               class="flex items-center gap-1.5 bg-emerald-700/80 hover:bg-emerald-700 text-white text-sm font-medium px-3 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
        </form>
    </div>

    {{-- ── Métricas Analíticas ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
            <p class="text-xs text-slate-400 mb-1">Receita Recorrente Mensal (MRR)</p>
            <p class="text-2xl font-bold text-teal-400">R$ {{ number_format($mrr, 2, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1">Base: planos ativos normalizados por mês</p>
        </div>
        <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
            <p class="text-xs text-slate-400 mb-1">Ticket Médio — {{ $filterMonth > 0 ? \Carbon\Carbon::create(null,$filterMonth)->translatedFormat('F').'/'.$filterYear : 'Ano '.$filterYear }}</p>
            <p class="text-2xl font-bold text-indigo-400">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1">Recebido ÷ alunos pagantes no mês</p>
        </div>
        <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
            <p class="text-xs text-slate-400 mb-1">Taxa de Inadimplência</p>
            <p class="text-2xl font-bold {{ $taxaInadimplencia > 20 ? 'text-rose-400' : ($taxaInadimplencia > 5 ? 'text-amber-400' : 'text-emerald-400') }}">
                {{ $taxaInadimplencia }}%
            </p>
            <p class="text-xs text-slate-500 mt-1">{{ $alertasInadimplentes->count() }} aluno(s) em atraso/suspenso</p>
        </div>
    </div>

    {{-- ── Alertas Financeiros ── --}}
    @if($alertasVencimento->count() > 0)
    <div class="grid grid-cols-1 gap-4">
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-amber-300">Vencendo em até 7 dias ({{ $alertasVencimento->count() }})</p>
            </div>
            <div class="space-y-1.5">
                @foreach($alertasVencimento as $a)
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-300">{{ $a->student->name }}</span>
                    <span class="text-amber-400 font-medium">{{ $a->due_date->format('d/m/Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Faturamento Futuro ── --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <h2 class="text-sm font-semibold text-slate-200">Faturamento Futuro (previsão)</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($faturamentoFuturo as $i => $f)
            <div class="bg-teal-500/5 border border-teal-500/20 rounded-xl p-4">
                <p class="text-xs text-slate-400 capitalize mb-1">{{ $f['label'] }}</p>
                <p class="text-xl font-bold text-teal-300">R$ {{ number_format($f['valor'], 2, ',', '.') }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $f['alunos'] }} aluno(s) com vencimento</p>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-slate-600 mt-3">* Baseado nos planos ativos com data de vencimento no mês correspondente.</p>
    </div>

    {{-- ── Relatório por Período ── --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5 space-y-5">
        <h2 class="text-sm font-semibold text-slate-200">
            Relatório —
            {{ \Carbon\Carbon::create(null,$filterMonth)->translatedFormat('F') }}/{{ $filterYear }}
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach([
                ['Faturamento Esperado',  $totalFaturado,  'text-slate-200',   'bg-slate-500/10 border-slate-500/30'],
                ['Recebido',  $totalRecebido,  'text-emerald-400', 'bg-emerald-500/10 border-emerald-500/30'],
                ['Pendente',  $totalPendente,  'text-amber-400',   'bg-amber-500/10 border-amber-500/30'],
                ['Vencido',   $totalVencido,   'text-rose-400',    'bg-rose-500/10 border-rose-500/30'],
            ] as [$lbl, $val, $color, $bg])
            <div class="border {{ $bg }} rounded-xl p-4">
                <p class="text-xs text-slate-400 mb-1">{{ $lbl }}</p>
                <p class="text-lg font-bold {{ $color }}">R$ {{ number_format($val, 2, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        {{-- Filtros avançados --}}
        <form method="GET" action="{{ route('personal.financial.reports') }}" class="flex flex-wrap gap-2 items-end">
            <input type="hidden" name="month" value="{{ $filterMonth }}">
            <input type="hidden" name="year"  value="{{ $filterYear }}">

            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-400">Aluno</label>
                <select name="student_id" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500 min-w-[150px]">
                    <option value="">Todos</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}" @selected($s->id == $studentId)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-400">Plano</label>
                <select name="plan_id" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500 min-w-[130px]">
                    <option value="">Todos</option>
                    @foreach($plans as $p)
                        <option value="{{ $p->id }}" @selected($p->id == $planFilter)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-400">Status</label>
                <select name="status_filter" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500">
                    <option value="">Todos</option>
                    <option value="paid"    @selected($statusFilter=='paid')>Pago</option>
                    <option value="pending" @selected($statusFilter=='pending')>Pendente</option>
                    <option value="overdue" @selected($statusFilter=='overdue')>Vencido</option>
                </select>
            </div>

            <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-medium px-4 py-2 rounded-lg transition-colors self-end">
                Aplicar
            </button>
            @if($studentId || $planFilter || $statusFilter)
            <a href="{{ route('personal.financial.reports', ['month'=>$filterMonth,'year'=>$filterYear]) }}"
               class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs px-3 py-2 rounded-lg transition-colors self-end">
                Limpar
            </a>
            @endif
        </form>

        {{-- Tabela de pagamentos do período --}}
        @if($pagamentosMes->isEmpty())
        <p class="text-slate-500 text-sm text-center py-6">Nenhum pagamento encontrado para o período.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-slate-500 border-b border-slate-700/50">
                        <th class="text-left pb-2 font-medium">Aluno</th>
                        <th class="text-left pb-2 font-medium">Plano</th>
                        <th class="text-center pb-2 font-medium">Vencimento</th>
                        <th class="text-center pb-2 font-medium">Pagamento</th>
                        <th class="text-right pb-2 font-medium">Valor</th>
                        <th class="text-center pb-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @foreach($pagamentosMes as $pm)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-2 text-slate-200">{{ $pm->student->name ?? '—' }}</td>
                        <td class="py-2 text-slate-400 text-xs">{{ $pm->studentPlan->financialPlan->name ?? '—' }}</td>
                        <td class="py-2 text-center text-slate-400 text-xs">{{ $pm->due_date?->format('d/m/Y') }}</td>
                        <td class="py-2 text-center text-slate-400 text-xs">{{ $pm->paid_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="py-2 text-right font-medium text-slate-200">R$ {{ number_format($pm->amount, 2, ',', '.') }}</td>
                        <td class="py-2 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $pm->statusColor() }}">
                                {{ $pm->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Gráfico Faturamento Anual ── --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
        <h2 class="text-sm font-semibold text-slate-200 mb-4">Faturamento Mensal {{ $filterYear }}</h2>
        <div style="height:240px"><canvas id="reportChart"></canvas></div>
    </div>

    {{-- ── Ranking de Alunos ── --}}
    @if($ranking->count() > 0)
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            <h2 class="text-sm font-semibold text-slate-200">Ranking de Alunos (últimos 12 meses)</h2>
        </div>
        <div class="space-y-2">
            @foreach($ranking as $i => $r)
            @php $pct = $ranking->first()->total > 0 ? ($r->total / $ranking->first()->total * 100) : 0; @endphp
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-500 w-4">{{ $i+1 }}</span>
                <span class="text-sm text-slate-300 w-40 truncate">{{ $r->student->name ?? '—' }}</span>
                <div class="flex-1 bg-slate-800 rounded-full h-2">
                    <div class="h-2 rounded-full bg-teal-500" style="width:{{ $pct }}%"></div>
                </div>
                <span class="text-sm font-semibold text-teal-400 w-28 text-right">R$ {{ number_format($r->total, 2, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Inadimplentes ── --}}
    @if($inadimplentes->count() > 0)
    <div class="bg-rose-950/40 border-2 border-rose-500/50 rounded-2xl p-5 shadow-lg shadow-rose-900/20">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-rose-300">Alunos Inadimplentes</h2>
                    <p class="text-xs text-rose-500">Planos vencidos sem pagamento registrado</p>
                </div>
            </div>
            <span class="bg-rose-500 text-white text-sm font-bold px-3 py-1 rounded-full">{{ $inadimplentes->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-rose-400/70 border-b border-rose-500/30">
                        <th class="text-left pb-3 font-semibold">Aluno</th>
                        <th class="text-left pb-3 font-semibold">Plano</th>
                        <th class="text-center pb-3 font-semibold">Vencimento</th>
                        <th class="text-center pb-3 font-semibold">Atraso</th>
                        <th class="text-center pb-3 font-semibold">Status</th>
                        <th class="text-right pb-3 font-semibold">Valor</th>
                        <th class="text-center pb-3 font-semibold">Contato</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-500/10">
                    @foreach($inadimplentes as $in)
                    @php
                        $dias = $in->due_date ? (int) $in->due_date->diffInDays(now(), false) : 0;
                        $phone = preg_replace('/\D/', '', $in->student->phone ?? '');
                        $waMsg = urlencode('Olá ' . $in->student->name . ', identificamos que seu plano *' . ($in->financialPlan->name ?? '') . '* está com pagamento em aberto desde ' . ($in->due_date?->format('d/m/Y') ?? '') . '. Por favor, entre em contato para regularizar. 😊');
                        $waLink = $phone ? 'https://wa.me/55' . $phone . '?text=' . $waMsg : null;
                    @endphp
                    <tr class="hover:bg-rose-500/5 transition-colors">
                        <td class="py-3 text-slate-100 font-medium">{{ $in->student->name }}</td>
                        <td class="py-3 text-slate-400 text-xs">{{ $in->financialPlan->name ?? '—' }}</td>
                        <td class="py-3 text-center text-rose-400 text-xs font-medium">{{ $in->due_date?->format('d/m/Y') }}</td>
                        <td class="py-3 text-center">
                            @if($dias > 0)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                {{ $dias }}d
                            </span>
                            @else
                            <span class="text-xs text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $in->statusColor() }}">
                                {{ $in->statusLabel() }}
                            </span>
                        </td>
                        <td class="py-3 text-right font-bold text-rose-300">R$ {{ number_format($in->financialPlan->price ?? 0, 2, ',', '.') }}</td>
                        <td class="py-3 text-center">
                            @if($waLink)
                            <a href="{{ $waLink }}" target="_blank"
                               class="inline-flex items-center gap-1.5 bg-green-600/20 hover:bg-green-600/40 text-green-400 border border-green-600/30 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition-colors"
                               title="Enviar mensagem no WhatsApp">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp
                            </a>
                            @else
                            <span class="text-xs text-slate-600">Sem telefone</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-rose-500/30">
                        <td colspan="5" class="pt-3 text-xs text-rose-400/70 font-semibold">Total em aberto</td>
                        <td class="pt-3 text-right font-bold text-rose-300">
                            R$ {{ number_format($inadimplentes->sum(fn($i) => $i->financialPlan->price ?? 0), 2, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Histórico por Aluno ── --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
        <h2 class="text-sm font-semibold text-slate-200 mb-4">Histórico por Aluno</h2>
        <form method="GET" action="{{ route('personal.financial.reports') }}" class="flex flex-wrap gap-2 items-end mb-4">
            <input type="hidden" name="month" value="{{ $filterMonth }}">
            <input type="hidden" name="year"  value="{{ $filterYear }}">
            <select name="student_id" class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-teal-500 min-w-[180px]">
                <option value="">Selecione um aluno</option>
                @foreach($students as $s)
                    <option value="{{ $s->id }}" @selected($s->id == $studentId)>{{ $s->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Ver Histórico
            </button>
        </form>

        @if($historyStudent)
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-slate-300 font-medium">{{ $historyStudent->name }}</p>
                <p class="text-sm text-emerald-400 font-semibold">Total recebido: R$ {{ number_format($historyTotal, 2, ',', '.') }}</p>
            </div>
            @if($historyPayments->isEmpty())
                <p class="text-slate-500 text-sm">Nenhum pagamento encontrado.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-500 border-b border-slate-700/50">
                            <th class="text-left pb-2 font-medium">Plano</th>
                            <th class="text-center pb-2 font-medium">Vencimento</th>
                            <th class="text-center pb-2 font-medium">Pagamento</th>
                            <th class="text-right pb-2 font-medium">Valor</th>
                            <th class="text-center pb-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($historyPayments as $hp)
                        <tr>
                            <td class="py-2 text-slate-400 text-xs">{{ $hp->studentPlan->financialPlan->name ?? '—' }}</td>
                            <td class="py-2 text-center text-slate-400 text-xs">{{ $hp->due_date?->format('d/m/Y') }}</td>
                            <td class="py-2 text-center text-slate-400 text-xs">{{ $hp->paid_at?->format('d/m/Y') ?? '—' }}</td>
                            <td class="py-2 text-right font-medium text-slate-200">R$ {{ number_format($hp->amount, 2, ',', '.') }}</td>
                            <td class="py-2 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $hp->statusColor() }}">
                                    {{ $hp->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @else
            <p class="text-slate-500 text-sm text-center py-4">Selecione um aluno para ver o histórico.</p>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('reportChart');
    if (!ctx) return;
    const data = @json($faturamentoPorMes);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [
                {
                    label: 'Recebido',
                    data: data.map(d => d.recebido),
                    backgroundColor: 'rgba(20,184,166,0.7)',
                    borderRadius: 4,
                },
                {
                    label: 'Pendente',
                    data: data.map(d => d.pendente),
                    backgroundColor: 'rgba(234,179,8,0.5)',
                    borderRadius: 4,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { color: 'rgba(71,85,105,0.2)' }, ticks: { color: '#94a3b8' } },
                y: {
                    grid: { color: 'rgba(71,85,105,0.2)' },
                    ticks: {
                        color: '#94a3b8',
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                    }
                }
            },
            plugins: {
                legend: { labels: { color: '#e2e8f0', boxWidth: 12 } },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: { label: ctx => ctx.dataset.label + ': R$ ' + ctx.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits:2}) }
                }
            }
        }
    });
});
</script>

@endsection
