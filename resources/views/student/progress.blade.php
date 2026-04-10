@extends('layouts.app')

@section('content')
<style>
    .prog-bg { background: #0d0f1a; min-height: 100vh; padding-bottom: 8px; }
    .stat-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px; padding: 18px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .stat-val { font-size: 32px; font-weight: 900; color: #fff; line-height: 1; }
    .stat-lbl { font-size: 11px; text-transform: uppercase; letter-spacing: 0.07em; color: #64748b; margin-top: 5px; font-weight: 700; }

    /* Bar chart */
    .bar-wrap { display: flex; align-items: flex-end; gap: 6px; height: 80px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end; }
    .bar { width: 100%; border-radius: 6px 6px 0 0; min-height: 4px; transition: height 0.5s ease; }
    .bar-lbl { font-size: 9px; color: #475569; font-weight: 700; }

    /* Section header */
    .sec-title { font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 14px; }

    /* Measurement card */
    .meas-row { display: flex; justify-content: space-between; align-items: center;
        padding: 13px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .meas-row:last-child { border-bottom: none; }
</style>

<div class="prog-bg pt-5 space-y-5">

    {{-- Header --}}
    <div class="px-1">
        <h1 class="text-white font-extrabold text-2xl">Progresso</h1>
        <p class="text-slate-500 text-sm mt-0.5">Seu histórico de treinos</p>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="stat-card" style="background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(139,92,246,0.08));border-color:rgba(99,102,241,0.3);">
            <span class="text-3xl mb-1">🔥</span>
            <span class="stat-val" style="color:#f97316;">{{ $streak }}</span>
            <span class="stat-lbl">Streak atual</span>
        </div>
        <div class="stat-card">
            <span class="text-3xl mb-1">🏆</span>
            <span class="stat-val" style="color:#f59e0b;">{{ $bestStreak }}</span>
            <span class="stat-lbl">Melhor streak</span>
        </div>
        <div class="stat-card">
            <span class="text-3xl mb-1">💪</span>
            <span class="stat-val" style="color:#818cf8;">{{ $totalWorkouts }}</span>
            <span class="stat-lbl">Total de treinos</span>
        </div>
        <div class="stat-card">
            <span class="text-3xl mb-1">📅</span>
            <span class="stat-val" style="color:#34d399;">{{ $thisMonthCount }}</span>
            <span class="stat-lbl">Este mês</span>
        </div>
    </div>

    {{-- Bar chart semanal --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px;padding:18px;">
        <p class="sec-title">Treinos por Semana</p>
        @php $maxVal = max(array_column($weeklyStats, 'count'), 1); @endphp
        <div class="bar-wrap">
            @foreach($weeklyStats as $wk)
            @php
                $pct = round(($wk['count'] / $maxVal) * 100);
                $isLast = $loop->last;
            @endphp
            <div class="bar-col">
                <div class="bar" style="height:{{ max($pct, 5) }}%;background:{{ $isLast ? 'linear-gradient(180deg,#818cf8,#6366f1)' : 'rgba(99,102,241,0.3)' }};"></div>
                <span class="bar-lbl">{{ $wk['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Últimas medidas --}}
    @if($latestMeasurement)
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px;padding:18px;">
        <div class="flex items-center justify-between mb-2">
            <p class="sec-title" style="margin-bottom:0;">Medidas Corporais</p>
            <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($latestMeasurement->date)->format('d/m/Y') }}</span>
        </div>
        <p class="text-xs text-slate-500 mb-4">Última avaliação</p>

        @foreach([
            ['label'=>'Peso','value'=>$latestMeasurement->weight,'unit'=>'kg','icon'=>'⚖️'],
            ['label'=>'% Gordura','value'=>$latestMeasurement->body_fat,'unit'=>'%','icon'=>'📊'],
            ['label'=>'Massa Muscular','value'=>$latestMeasurement->muscle_mass,'unit'=>'kg','icon'=>'💪'],
            ['label'=>'Cintura','value'=>$latestMeasurement->waist,'unit'=>'cm','icon'=>'📏'],
            ['label'=>'Abdômen','value'=>$latestMeasurement->abdomen,'unit'=>'cm','icon'=>'📐'],
        ] as $item)
        @if($item['value'])
        <div class="meas-row">
            <div class="flex items-center gap-3">
                <span class="text-xl">{{ $item['icon'] }}</span>
                <span class="text-slate-300 text-sm font-semibold">{{ $item['label'] }}</span>
            </div>
            <span class="text-white font-bold">{{ $item['value'] }} <span class="text-slate-500 font-normal text-xs">{{ $item['unit'] }}</span></span>
        </div>
        @endif
        @endforeach

        <a href="{{ route('student.evolution') }}" class="mt-4 flex items-center justify-center gap-2 py-3 rounded-2xl text-sm font-bold text-indigo-300"
           style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.25);">
            Ver evolução completa →
        </a>
    </div>
    @else
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:18px;padding:32px 18px;text-align:center;">
        <span class="text-4xl">📊</span>
        <p class="text-slate-300 font-semibold mt-3">Sem medidas cadastradas</p>
        <p class="text-slate-500 text-sm mt-1">Peça ao seu personal para adicionar sua avaliação.</p>
    </div>
    @endif

</div>
@endsection
