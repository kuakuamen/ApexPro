@extends('layouts.app')

@section('content')
<div class="py-6 space-y-5">
    @include('personal.financial._nav', ['activeTab' => 'vinculos'])

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-slate-100">Vínculos Aluno ↔ Plano</h1>
            @if(request('status'))
            @php
                $filterLabels = ['active' => ['label' => 'Ativos', 'color' => 'purple'], 'overdue' => ['label' => 'Inadimplentes', 'color' => 'red']];
                $fl = $filterLabels[request('status')] ?? ['label' => request('status'), 'color' => 'gray'];
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $fl['color'] }}-500/15 text-{{ $fl['color'] }}-300 border border-{{ $fl['color'] }}-500/30">
                {{ $fl['label'] }}
                <a href="{{ route('personal.financial.student-plans') }}" class="hover:text-white transition-colors" title="Limpar filtro">×</a>
            </span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('personal.financial.run-suspend-check') }}">
                @csrf
                <button type="submit" title="Verificar e suspender alunos com mais de 5 dias de atraso"
                    class="flex items-center gap-2 px-4 py-2 bg-rose-600/15 hover:bg-rose-600/25 border border-rose-500/30 rounded-xl text-rose-300 text-sm font-medium transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z"/></svg>
                    Verificar Inadimplências
                </button>
            </form>
            <a href="{{ route('personal.financial.student-plans.create') }}" class="flex items-center gap-2 px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Vincular Plano
            </a>
        </div>
    </div>

    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl overflow-hidden">
        @if($studentPlans->isEmpty())
            <div class="p-12 text-center">
                <p class="text-slate-400">Nenhum aluno vinculado a um plano ainda.</p>
            </div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Aluno</th>
                    <th class="px-5 py-3 text-left">Plano</th>
                    <th class="px-5 py-3 text-center">Vencimento</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40">
                @foreach($studentPlans as $sp)
                @php
                    $statusColors = ['active' => 'emerald', 'overdue' => 'yellow', 'suspended' => 'red'];
                    $c = $statusColors[$sp->status] ?? 'gray';
                @endphp
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-cyan-500/15 flex items-center justify-center text-cyan-200 text-xs font-bold shrink-0">{{ strtoupper(substr($sp->student->name, 0, 1)) }}</div>
                            <span class="text-slate-100 font-medium">{{ $sp->student->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-slate-200">{{ $sp->financialPlan->name }}</p>
                        <p class="text-xs text-emerald-400 font-medium">R$ {{ number_format($sp->financialPlan->price, 2, ',', '.') }}</p>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="{{ $sp->due_date->isPast() ? 'text-red-400' : 'text-slate-300' }}">
                            {{ $sp->due_date->format('d/m/Y') }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $c }}-500/15 text-{{ $c }}-300">
                            {{ $sp->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('personal.financial.student-plans.edit', $sp) }}" class="p-1.5 rounded-lg hover:bg-slate-700 text-slate-400 hover:text-cyan-300 transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('personal.financial.student-plans.toggle', $sp) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-1.5 rounded-lg hover:bg-slate-700 transition-colors {{ $sp->status === 'suspended' ? 'text-emerald-400 hover:text-emerald-300' : 'text-red-400 hover:text-red-300' }}" title="{{ $sp->status === 'suspended' ? 'Liberar acesso' : 'Suspender acesso' }}">
                                    @if($sp->status === 'suspended')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zM10 11V7a2 2 0 114 0v4m-4 0h4"/></svg>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>
@endsection
