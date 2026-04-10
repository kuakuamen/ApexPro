@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;

    $now   = Carbon::now()->setTimezone('America/Sao_Paulo');
    $hour  = $now->hour;
    $greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');
    $firstName = explode(' ', auth()->user()->name)[0];

    // Data formatada
    $days   = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
    $months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
    $dateStr = $days[$now->dayOfWeek] . ', ' . $now->day . ' ' . $months[$now->month - 1];

    // Medidas
    $last = $weightHistory->last();
    $imc = null;
    $bodyFat = $last?->body_fat;
    if ($last && $last->height > 0) {
        $h = $last->height > 3 ? $last->height / 100 : $last->height;
        $imc = round($last->weight / ($h * $h), 1);
    }

    // Dias da semana para o tracker (Seg=1 ... Dom=7)
    $weekLabels = ['S','T','Q','Q','S','S','D'];
    $todayIso   = $now->dayOfWeekIso; // 1=Seg, 7=Dom
@endphp

<style>
    .dash-bg {
        background: #0d0f1a;
    }
    /* Hero card gradiente */
    .workout-hero {
        background: linear-gradient(135deg, #2d1b69 0%, #1a1040 40%, #0f172a 100%);
        border: 1px solid rgba(124,58,237,0.3);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    .workout-hero::before {
        content:'';
        position:absolute;
        top:-40px;right:-40px;
        width:180px;height:180px;
        background: radial-gradient(circle, rgba(124,58,237,0.25) 0%, transparent 70%);
        border-radius:50%;
    }
    /* Stat cards */
    .stat-card {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        padding: 14px 10px;
        text-align: center;
        flex: 1;
    }
    /* Day circles */
    .day-circle {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
        flex-shrink: 0;
    }
    .day-done    { background: linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; }
    .day-today   { background: rgba(99,102,241,0.2); border: 2px solid #6366f1; color:#a5b4fc; }
    .day-pending { background: rgba(255,255,255,0.06); color: #4b5563; }

    /* Week progress bar */
    .week-bar-bg { background: rgba(255,255,255,0.08); border-radius: 99px; height: 6px; overflow:hidden; }
    .week-bar-fill { background: linear-gradient(90deg,#6366f1,#8b5cf6); border-radius: 99px; height: 6px; transition: width 0.6s ease; }

    /* Last workout card */
    .last-workout-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        padding: 16px;
        display: flex; align-items: center; gap: 12px;
    }
    /* Streak card */
    .streak-card {
        background: linear-gradient(135deg, rgba(234,88,12,0.25) 0%, rgba(154,52,18,0.15) 100%);
        border: 1px solid rgba(234,88,12,0.3);
        border-radius: 16px;
        padding: 16px;
        display: flex; align-items: center; gap: 12px;
    }
    /* Initiar btn */
    .btn-start {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 14px; font-weight: 700; color: #fff;
        text-decoration: none;
        transition: all 0.2s;
        active: scale(0.97);
    }
    .btn-start:hover { background: rgba(255,255,255,0.2); color:#fff; }
</style>

<div class="dash-bg pt-4 pb-2 space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between px-1">
        <div class="flex items-center gap-3">
            {{-- Avatar --}}
            @if(auth()->user()->profile_photo_url)
                <img src="{{ auth()->user()->profile_photo_url }}" class="w-11 h-11 rounded-2xl object-cover border-2 border-purple-500/30">
            @else
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                     style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <p class="text-white font-bold text-base leading-tight">Olá, {{ $firstName }} 👋</p>
                <p class="text-slate-400 text-xs">{{ $dateStr }}</p>
            </div>
        </div>
        {{-- Bell --}}
        <button class="w-10 h-10 rounded-2xl flex items-center justify-center" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);">
            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </button>
    </div>

    {{-- WORKOUT HERO CARD --}}
    @if($activeWorkout)
    <div class="workout-hero p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="text-purple-300 text-xs font-bold uppercase tracking-widest mb-1">Treino do Dia</p>
                <h2 class="text-white text-2xl font-extrabold leading-tight">{{ $activeWorkout->name }}</h2>
                <p class="text-slate-400 text-sm mt-1">
                    {{ $activeWorkout->goal ?? 'Sem objetivo definido' }}
                    @php $totalEx = $activeWorkout->days->sum(fn($d) => $d->exercises->count()); @endphp
                    · {{ $totalEx }} exercícios
                </p>
            </div>
            <div class="text-right flex-shrink-0 ml-3">
                <span class="text-white text-2xl font-extrabold">{{ $weekDaysWorked }}</span>
                <p class="text-slate-400 text-xs">de {{ $totalWorkoutDays }} dias</p>
            </div>
        </div>
        <a href="{{ route('workouts.show', $activeWorkout) }}" class="btn-start mt-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            Iniciar Treino
        </a>
    </div>
    @else
    <div class="workout-hero p-5 text-center">
        <p class="text-purple-300 text-xs font-bold uppercase tracking-widest mb-2">Treino do Dia</p>
        <p class="text-white font-bold text-lg">Sem treino ativo</p>
        <p class="text-slate-400 text-sm mt-1">Aguarde seu personal trainer criar um treino.</p>
    </div>
    @endif

    {{-- STATS ROW --}}
    @if($last)
    <div class="flex gap-3">
        <div class="stat-card">
            <p class="text-white text-xl font-extrabold">{{ $last->weight ?? '—' }}</p>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">kg · Peso</p>
        </div>
        <div class="stat-card">
            <p class="text-white text-xl font-extrabold">{{ $imc ?? '—' }}</p>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">IMC</p>
        </div>
        <div class="stat-card">
            <p class="text-xl font-extrabold {{ $bodyFat && $bodyFat > 25 ? 'text-pink-400' : 'text-white' }}">
                {{ $bodyFat ? $bodyFat . '%' : '—' }}
            </p>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">Gordura</p>
        </div>
    </div>
    @endif

    {{-- ESTA SEMANA --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:20px;padding:18px;">
        <div class="flex items-center justify-between mb-4">
            <p class="text-white font-bold text-sm">Esta Semana</p>
            <p class="text-purple-400 text-sm font-bold">{{ $weekDaysWorked }} / {{ $totalWorkoutDays }} dias</p>
        </div>

        {{-- Day circles --}}
        <div class="flex justify-between mb-4">
            @foreach($weekLabels as $idx => $label)
                @php
                    $isoDay  = $idx + 1; // 1=Seg...7=Dom
                    $isDone  = in_array($isoDay, $logsThisWeek);
                    $isToday = $isoDay === $todayIso;
                    $class   = $isDone ? 'day-done' : ($isToday ? 'day-today' : 'day-pending');
                @endphp
                <div class="day-circle {{ $class }}">{{ $label }}</div>
            @endforeach
        </div>

        {{-- Progress bar --}}
        <div class="week-bar-bg">
            <div class="week-bar-fill" style="width: {{ $totalWorkoutDays > 0 ? round(($weekDaysWorked / $totalWorkoutDays) * 100) : 0 }}%"></div>
        </div>
    </div>

    {{-- ÚLTIMO TREINO --}}
    @if($lastTrainingDate)
    <a href="{{ $activeWorkout ? route('workouts.show', $activeWorkout) : route('workouts.index') }}" class="last-workout-card" style="text-decoration:none;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(99,102,241,0.15);">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Último Treino</p>
            <p class="text-white text-sm font-bold mt-0.5">{{ $activeWorkout?->name ?? 'Treino' }}</p>
            <p class="text-slate-400 text-xs mt-0.5">
                há {{ $lastTrainingDaysAgo }} {{ $lastTrainingDaysAgo == 1 ? 'dia' : 'dias' }}
            </p>
        </div>
        <svg class="w-4 h-4 text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @endif

    {{-- STREAK --}}
    @if($streak > 0)
    <div class="streak-card">
        <span class="text-3xl flex-shrink-0">🔥</span>
        <div>
            <p class="text-orange-300 font-extrabold text-base">{{ $streak }} {{ $streak == 1 ? 'dia consecutivo' : 'dias consecutivos' }}!</p>
            <p class="text-orange-400/70 text-sm">Continue assim, {{ $firstName }}!</p>
        </div>
    </div>
    @endif

</div>
@endsection
