@extends('layouts.app')

@section('content')
<div class="py-6 space-y-5" x-data="{ deleteModal: false, deleteUrl: '', deleteName: '' }">
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
                            <button type="button"
                                @click="deleteModal = true; deleteUrl = '{{ route('personal.financial.student-plans.destroy', $sp) }}'; deleteName = '{{ addslashes($sp->student->name) }}'"
                                class="p-1.5 rounded-lg hover:bg-slate-700 text-slate-500 hover:text-rose-400 transition-colors" title="Excluir vínculo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>

    {{-- Modal de confirmação de exclusão --}}
    <div x-show="deleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-black/60" @click="deleteModal = false"></div>

        <div class="relative bg-[#0f1a2e] border border-rose-500/40 rounded-2xl p-6 w-full max-w-sm shadow-2xl"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-100">Excluir Vínculo</p>
                    <p class="text-xs text-slate-400" x-text="deleteName"></p>
                </div>
            </div>

            <div class="bg-rose-500/10 border border-rose-500/20 rounded-xl px-4 py-3 mb-5 space-y-1.5">
                <p class="text-xs text-rose-300 font-semibold">Esta ação é irreversível.</p>
                <p class="text-xs text-slate-400">Todos os <strong class="text-slate-200">registros de pagamento</strong> vinculados a este aluno serão <strong class="text-slate-200">permanentemente excluídos</strong> do sistema.</p>
            </div>

            <form method="POST" :action="deleteUrl">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" @click="deleteModal = false"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm text-slate-400 hover:text-slate-200 bg-slate-800/50 hover:bg-slate-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm text-white font-medium bg-rose-600 hover:bg-rose-700 transition-colors">
                        Excluir permanentemente
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
