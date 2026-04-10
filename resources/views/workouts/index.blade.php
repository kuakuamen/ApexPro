@extends('layouts.app')

@section('content')
<div class="space-y-5 pt-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Meus Treinos</h1>
            <p class="text-sm text-slate-400 mt-0.5">{{ $workouts->count() }} plano(s) de treino</p>
        </div>
        @if(auth()->user()->role === 'personal')
            <a href="{{ route('workouts.create') }}"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white active:scale-95 transition-transform"
               style="background:linear-gradient(135deg,#0891b2,#7c3aed);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Novo
            </a>
        @endif
    </div>

    {{-- Workout Cards --}}
    @forelse($workouts as $workout)
    @php
        $totalEx = $workout->days->sum(fn($d) => $d->exercises->count());
        $colors = [
            ['from-cyan-500/20','to-blue-600/10','border-cyan-500/25','text-cyan-400'],
            ['from-purple-500/20','to-pink-600/10','border-purple-500/25','text-purple-400'],
            ['from-emerald-500/20','to-teal-600/10','border-emerald-500/25','text-emerald-400'],
            ['from-orange-500/20','to-red-600/10','border-orange-500/25','text-orange-400'],
        ];
        $c = $colors[$loop->index % count($colors)];
    @endphp
    <a href="{{ route('workouts.show', $workout) }}" class="block active:scale-[0.98] transition-transform">
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br {{ $c[0] }} {{ $c[1] }} border {{ $c[2] }}" style="background-color:rgba(9,18,38,0.7);">
            {{-- Status badge --}}
            <div class="flex items-start justify-between mb-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full {{ $workout->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-zinc-700/40 text-slate-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $workout->is_active ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                    {{ $workout->is_active ? 'Ativo' : 'Inativo' }}
                </span>
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>

            {{-- Name --}}
            <h2 class="text-lg font-extrabold text-white mb-1 leading-snug">{{ $workout->name }}</h2>
            @if($workout->goal)
                <p class="text-sm text-slate-400 mb-4 line-clamp-1">{{ $workout->goal }}</p>
            @else
                <div class="mb-4"></div>
            @endif

            {{-- Stats --}}
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                    <svg class="w-4 h-4 {{ $c[3] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="font-semibold text-white">{{ $workout->days->count() }}</span> dias
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                    <svg class="w-4 h-4 {{ $c[3] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span class="font-semibold text-white">{{ $totalEx }}</span> exercícios
                </div>
                @if(auth()->user()->role === 'personal')
                <div class="flex items-center gap-1.5 text-xs text-slate-400 ml-auto">
                    <svg class="w-4 h-4 {{ $c[3] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $workout->student->name ?? '—' }}
                </div>
                @endif
            </div>
        </div>
    </a>
    @empty
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-teal-900/30 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-teal-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <p class="text-slate-300 font-semibold">Nenhum treino encontrado</p>
        @if(auth()->user()->role === 'personal')
            <a href="{{ route('workouts.create') }}" class="mt-4 text-sm text-cyan-400 font-semibold">Criar primeiro treino →</a>
        @else
            <p class="text-slate-500 text-sm mt-1">Aguarde seu personal trainer criar um treino.</p>
        @endif
    </div>
    @endforelse

</div>
@endsection
