@extends('layouts.app')

@section('content')
@php
    $canManageDiets = in_array(auth()->user()->role, ['personal', 'nutri'], true);
@endphp

<div class="space-y-5 pt-4">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Meus Planos Alimentares</h1>
            <p class="text-sm text-slate-400 mt-0.5">{{ $diets->count() }} plano(s) alimentar(es)</p>
        </div>

        @if($canManageDiets)
            <a href="{{ route('diets.create') }}"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white active:scale-95 transition-transform whitespace-nowrap"
               style="background:linear-gradient(135deg,#0891b2,#7c3aed);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nova Dieta
            </a>
        @endif
    </div>

    @if($canManageDiets)
        <form method="GET" action="{{ route('diets.index') }}" class="rounded-2xl border border-cyan-900/35 bg-slate-900/45 p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-4.35-4.35m1.1-4.4a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"/>
                        </svg>
                    </span>
                    <input type="text"
                           name="q"
                           value="{{ $search ?? '' }}"
                           placeholder="Pesquisar dietas pelo nome do aluno..."
                           class="w-full rounded-xl border border-slate-700/70 bg-slate-950/70 pl-10 pr-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500/50">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white border border-cyan-500/40 bg-cyan-600/20 hover:bg-cyan-500/25 transition-colors">
                        Buscar
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('diets.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 border border-slate-700 bg-slate-900/60 hover:bg-slate-800/80 transition-colors">
                            Limpar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    @endif

    @forelse($diets as $diet)
        @php
            $colors = [
                ['from-cyan-500/20','to-blue-600/10','border-cyan-500/25','text-cyan-400'],
                ['from-purple-500/20','to-pink-600/10','border-purple-500/25','text-purple-400'],
                ['from-emerald-500/20','to-teal-600/10','border-emerald-500/25','text-emerald-400'],
                ['from-orange-500/20','to-red-600/10','border-orange-500/25','text-orange-400'],
            ];
            $c = $colors[$loop->index % count($colors)];
        @endphp

        <a href="{{ route('diets.show', $diet) }}" class="block active:scale-[0.98] transition-transform">
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br {{ $c[0] }} {{ $c[1] }} border {{ $c[2] }}" style="background-color:rgba(9,18,38,0.7);">
                <div class="flex items-start justify-between mb-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full {{ $diet->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-zinc-700/40 text-slate-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $diet->is_active ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                        {{ $diet->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <h2 class="text-lg font-extrabold text-white mb-1 leading-snug">{{ $diet->name }}</h2>
                <p class="text-sm text-slate-400 mb-4 line-clamp-1">Objetivo: {{ $diet->goal ?: 'Nao definido' }}</p>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5 text-xs text-slate-400 min-w-0">
                        <svg class="w-4 h-4 {{ $c[3] }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @if($canManageDiets)
                            <span class="truncate">Aluno: <span class="font-semibold text-white">{{ $diet->student->name ?? 'Nao definido' }}</span></span>
                        @else
                            <span class="truncate">Profissional: <span class="font-semibold text-white">{{ $diet->nutritionist->name ?? 'Nao definido' }}</span></span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400 ml-auto shrink-0">
                        <svg class="w-4 h-4 {{ $c[3] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span><span class="font-semibold text-white">{{ $diet->created_at->format('d/m/Y') }}</span></span>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-teal-900/30 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-teal-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21C12 21 5 13.5 5 9a7 7 0 1114 0c0 4.5-7 12-7 12z"/><circle cx="12" cy="9" r="2"/></svg>
            </div>
            <p class="text-slate-300 font-semibold">Nenhum plano alimentar encontrado</p>
            @if($canManageDiets)
                <a href="{{ route('diets.create') }}" class="mt-4 text-sm text-cyan-400 font-semibold">Criar primeira dieta -></a>
            @else
                <p class="text-slate-500 text-sm mt-1">Aguarde seu profissional criar um plano alimentar.</p>
            @endif
        </div>
    @endforelse
</div>
@endsection
