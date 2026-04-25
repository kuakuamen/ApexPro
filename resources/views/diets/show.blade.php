@extends('layouts.app')

@section('content')
@php
    $parseCalories = static function ($value): float {
        if ($value === null) {
            return 0.0;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return 0.0;
        }

        $normalized = str_replace(',', '.', $normalized);
        $normalized = preg_replace('/[^0-9.\\-]/', '', $normalized);
        if ($normalized === '' || !is_numeric($normalized)) {
            return 0.0;
        }

        return max(0, (float) $normalized);
    };

    $totalMeals = $diet->meals->count();
    $dailyKcal = $diet->meals->sum(function ($meal) use ($parseCalories) {
        return $meal->foods->sum(fn($food) => $parseCalories($food->calories));
    });
@endphp

<style>
    .diet-show-hero {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 38%, #0f172a 100%);
        border: 1px solid rgba(16,185,129,0.32);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    .diet-show-hero::before {
        content: '';
        position: absolute;
        top: -42px;
        right: -42px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16,185,129,0.24) 0%, transparent 72%);
    }
    .diet-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        border: 1px solid rgba(16,185,129,0.35);
        background: rgba(16,185,129,0.16);
        color: #d1fae5;
        font-size: 11px;
        font-weight: 800;
        line-height: 1;
        padding: 6px 10px;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .meal-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 18px;
        padding: 14px;
    }
    .food-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(148,163,184,0.16);
    }
    .food-row:last-child {
        border-bottom: none;
    }
    .food-side {
        text-align: right;
        min-width: 112px;
        flex-shrink: 0;
    }
    .food-qty {
        color: #cbd5e1;
        font-size: 12px;
        font-weight: 600;
    }
    .food-kcal {
        color: #34d399;
        font-size: 12px;
        font-weight: 700;
        margin-top: 2px;
    }
</style>

<div class="space-y-4 pt-4 pb-2">
    <div class="diet-show-hero p-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-emerald-200 text-xs font-bold uppercase tracking-widest mb-1">Plano alimentar</p>
                <h1 class="text-white text-2xl font-extrabold leading-tight">{{ $diet->name }}</h1>
                <p class="text-emerald-100/80 text-sm mt-1">Objetivo: {{ $diet->goal ?: 'Nao definido' }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full {{ $diet->is_active ? 'bg-emerald-500/15 text-emerald-200 border border-emerald-400/25' : 'bg-rose-500/15 text-rose-200 border border-rose-400/25' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $diet->is_active ? 'bg-emerald-300' : 'bg-rose-300' }}"></span>
                {{ $diet->is_active ? 'Ativo' : 'Inativo' }}
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4">
            <span class="diet-chip">{{ $totalMeals }} {{ $totalMeals === 1 ? 'refeicao' : 'refeicoes' }}</span>
            @if($dailyKcal > 0)
                <span class="diet-chip">{{ (int) round($dailyKcal) }} kcal/dia</span>
            @endif
        </div>
    </div>

    @forelse($diet->meals as $meal)
        @php
            $mealKcal = $meal->foods->sum(fn($food) => $parseCalories($food->calories));
        @endphp
        <section class="meal-card">
            <div class="flex items-start justify-between gap-3 mb-2">
                <h2 class="text-lg font-extrabold text-emerald-300 leading-tight">{{ $meal->name }}</h2>
                <div class="flex items-center gap-2">
                    @if($meal->time)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-700/55 text-slate-200 border border-slate-500/30">
                            {{ \Carbon\Carbon::parse($meal->time)->format('H:i') }}
                        </span>
                    @endif
                    @if($mealKcal > 0)
                        <span class="diet-chip">{{ (int) round($mealKcal) }} kcal</span>
                    @endif
                </div>
            </div>

            <div class="mt-2">
                @forelse($meal->foods as $food)
                    <div class="food-row">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white leading-snug">{{ $food->name }}</p>
                            @if(!empty($food->observation))
                                <p class="text-xs text-slate-400 mt-1 leading-snug">{{ $food->observation }}</p>
                            @endif
                        </div>
                        <div class="food-side">
                            @if(!empty($food->quantity))
                                <p class="food-qty">{{ $food->quantity }}</p>
                            @endif
                            @if(!empty($food->calories))
                                <p class="food-kcal">{{ $food->calories }} kcal</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 py-2">Sem alimentos cadastrados nesta refeicao.</p>
                @endforelse
            </div>
        </section>
    @empty
        <div class="meal-card text-center py-10">
            <p class="text-slate-300 font-semibold">Nenhuma refeicao cadastrada</p>
            <p class="text-slate-500 text-sm mt-1">Este plano ainda nao possui alimentos definidos.</p>
        </div>
    @endforelse

    <a href="{{ route('diets.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition-colors pt-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar para Minhas Dietas
    </a>
</div>
@endsection
