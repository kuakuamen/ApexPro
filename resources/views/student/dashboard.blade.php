@extends('layouts.app')

@section('content')
@php
    $hour = now()->setTimezone('America/Sao_Paulo')->hour;
    $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
    $firstName = explode(' ', auth()->user()->name)[0];

    $last = $weightHistory->last();
    $imc = null;
    $imcLabel = null;
    if ($last && $last->height > 0) {
        $h = $last->height > 3 ? $last->height / 100 : $last->height;
        $imc = $last->weight / ($h * $h);
        if ($imc < 18.5)      $imcLabel = 'Abaixo do peso';
        elseif ($imc < 25)    $imcLabel = 'Peso normal';
        elseif ($imc < 30)    $imcLabel = 'Sobrepeso';
        else                  $imcLabel = 'Obesidade';
    }
@endphp

<div class="space-y-5 pt-4">

    {{-- Greeting --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-400 font-medium">{{ $greeting }},</p>
            <h1 class="text-2xl font-extrabold text-white leading-tight">{{ $firstName }} 👋</h1>
        </div>
        @if(auth()->user()->profile_photo_url)
            <img src="{{ auth()->user()->profile_photo_url }}" class="w-12 h-12 rounded-2xl object-cover border-2 border-cyan-500/30">
        @else
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        @endif
    </div>

    {{-- Quick Stats --}}
    @if($last)
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-zinc-900/70 border border-teal-900/40 rounded-2xl p-3 text-center">
            <p class="text-xl font-extrabold text-white">{{ $last->weight ?? '—' }}</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-wider mt-0.5">kg</p>
        </div>
        <div class="bg-zinc-900/70 border border-teal-900/40 rounded-2xl p-3 text-center">
            <p class="text-xl font-extrabold text-white">{{ $imc ? number_format($imc, 1) : '—' }}</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-wider mt-0.5">IMC</p>
        </div>
        <div class="bg-zinc-900/70 border border-teal-900/40 rounded-2xl p-3 text-center">
            <p class="text-xl font-extrabold text-white">{{ $last->body_fat ? $last->body_fat . '%' : '—' }}</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-wider mt-0.5">Gordura</p>
        </div>
    </div>
    @endif

    {{-- Treino Ativo --}}
    @if($activeWorkout)
    <div class="relative overflow-hidden rounded-2xl" style="background:linear-gradient(135deg,rgba(6,182,212,0.15) 0%,rgba(124,58,237,0.12) 100%);border:1px solid rgba(6,182,212,0.25);">
        <div class="absolute top-0 right-0 w-32 h-32 opacity-5">
            <svg fill="currentColor" viewBox="0 0 24 24" class="text-cyan-400 w-full h-full"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div class="p-5">
            <p class="text-xs font-bold text-cyan-400 uppercase tracking-widest mb-2">Treino Atual</p>
            <h2 class="text-xl font-extrabold text-white mb-1">{{ $activeWorkout->name }}</h2>
            @if($activeWorkout->goal)
                <p class="text-sm text-slate-400 mb-4">{{ $activeWorkout->goal }}</p>
            @endif
            <div class="flex items-center gap-3 mb-4 text-sm text-slate-300">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $activeWorkout->days->count() }} dias
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    {{ $activeWorkout->days->sum(fn($d) => $d->exercises->count()) }} exercícios
                </span>
            </div>
            <a href="{{ route('workouts.show', $activeWorkout) }}"
               class="inline-flex items-center justify-center gap-2 w-full py-3.5 rounded-xl font-bold text-sm text-white active:scale-95 transition-transform"
               style="background:linear-gradient(135deg,#0891b2,#7c3aed);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Iniciar Treino
            </a>
        </div>
    </div>
    @else
    <div class="bg-zinc-900/70 border border-teal-900/30 rounded-2xl p-5 text-center">
        <div class="w-12 h-12 rounded-2xl bg-teal-900/40 flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <p class="text-slate-300 font-medium">Nenhum treino ativo</p>
        <p class="text-slate-500 text-sm mt-1">Aguarde seu personal trainer atribuir um treino.</p>
    </div>
    @endif

    {{-- Última Avaliação --}}
    @if($last)
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Última Avaliação</h3>
            <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($last->date)->format('d/m/Y') }}</span>
        </div>
        <div class="grid grid-cols-2 gap-3">
            @if($last->muscle_mass)
            <div class="bg-zinc-900/70 border border-teal-900/30 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(6,182,212,0.15);">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-white">{{ $last->muscle_mass }} kg</p>
                    <p class="text-[10px] text-slate-400 uppercase">Massa Musc.</p>
                </div>
            </div>
            @endif
            @if($imc)
            <div class="bg-zinc-900/70 border border-teal-900/30 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(124,58,237,0.15);">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-white">{{ number_format($imc, 1) }}</p>
                    <p class="text-[10px] text-slate-400 uppercase">{{ $imcLabel }}</p>
                </div>
            </div>
            @endif
            @if($last->waist)
            <div class="bg-zinc-900/70 border border-teal-900/30 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.15);">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18M3 18h18"/></svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-white">{{ $last->waist }} cm</p>
                    <p class="text-[10px] text-slate-400 uppercase">Cintura</p>
                </div>
            </div>
            @endif
            @if($last->chest)
            <div class="bg-zinc-900/70 border border-teal-900/30 rounded-2xl p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(234,179,8,0.15);">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-white">{{ $last->chest }} cm</p>
                    <p class="text-[10px] text-slate-400 uppercase">Peito</p>
                </div>
            </div>
            @endif
        </div>

        <a href="{{ route('student.evolution') }}"
           class="mt-3 flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-semibold text-cyan-400 border border-cyan-900/50 bg-cyan-950/20 active:scale-95 transition-transform">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            Ver evolução completa
        </a>
    </div>
    @endif

</div>
@endsection
