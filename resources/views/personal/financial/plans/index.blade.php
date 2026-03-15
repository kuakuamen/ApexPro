@extends('layouts.app')

@section('content')
<div class="py-6 space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-100">Meus Planos</h1>
        <a href="{{ route('personal.financial.plans.create') }}" class="flex items-center gap-2 px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Plano
        </a>
    </div>

    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl overflow-hidden">
        @if($plans->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-slate-400">Nenhum plano cadastrado. Crie seu primeiro plano!</p>
            </div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Nome</th>
                    <th class="px-5 py-3 text-left">Periodicidade</th>
                    <th class="px-5 py-3 text-right">Valor</th>
                    <th class="px-5 py-3 text-center">Alunos</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40">
                @foreach($plans as $plan)
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-medium text-slate-100">{{ $plan->name }}</p>
                        @if($plan->description)
                            <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $plan->description }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-slate-300">{{ $plan->periodicityLabel() }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-emerald-400">R$ {{ number_format($plan->price, 2, ',', '.') }}</td>
                    <td class="px-5 py-3 text-center text-slate-300">{{ $plan->student_plans_count }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-slate-500/15 text-slate-400' }}">
                            {{ $plan->active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('personal.financial.plans.edit', $plan) }}" class="p-1.5 rounded-lg hover:bg-slate-700 text-slate-400 hover:text-cyan-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('personal.financial.plans.destroy', $plan) }}" onsubmit="return confirm('Excluir ou desativar este plano?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg hover:bg-slate-700 text-slate-400 hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
