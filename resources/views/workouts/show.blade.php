@extends('layouts.app')

@section('content')
<style>
    .show-bg { min-height: 100vh; background: #0d0f1a; }

    /* Header */
    .back-btn {
        width: 38px; height: 38px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
        color: #94a3b8; text-decoration: none; transition: all 0.2s;
    }
    .back-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }

    /* Day section */
    .day-header {
        width: 100%; display: flex; align-items: center; justify-content: space-between;
        background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px; padding: 14px 16px;
        cursor: pointer; transition: all 0.2s;
    }
    .day-header:hover { background: rgba(99,102,241,0.08); border-color: rgba(99,102,241,0.3); }
    .day-header.open { background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.4); border-radius: 16px 16px 0 0; }

    /* Exercise card */
    .exercise-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.2s;
    }
    .exercise-card.done {
        background: rgba(99,102,241,0.06);
        border-color: rgba(99,102,241,0.2);
        opacity: 0.75;
    }

    /* Stat chips */
    .stat-chip {
        flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
        background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2);
        border-radius: 12px; padding: 12px 8px;
    }
    .stat-chip-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #818cf8; font-weight: 700; margin-bottom: 4px; }
    .stat-chip-value { font-size: 22px; font-weight: 800; color: #fff; line-height: 1; }

    /* Checkbox */
    .ex-check-empty {
        width: 36px; height: 36px; border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.04);
        flex-shrink: 0; cursor: pointer;
        transition: all 0.2s;
    }
    .ex-check-empty:hover { border-color: rgba(99,102,241,0.6); }
    .ex-check-done {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: 2px solid #6366f1;
        flex-shrink: 0; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
    }

    /* Timer btn */
    .timer-btn {
        width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 14px; border-radius: 12px; font-size: 15px; font-weight: 700; color: #fff;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none; cursor: pointer; transition: all 0.2s;
        active: scale(0.97);
    }
    .timer-running {
        background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.4);
        border-radius: 12px; padding: 14px;
        display: flex; align-items: center; justify-content: space-between;
    }

    /* Video btn */
    .video-btn {
        width: 100%; display: flex; align-items: center; justify-content: between; gap: 8px;
        padding: 12px 14px; border-radius: 12px; font-size: 13px; font-weight: 600;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        color: #94a3b8; cursor: pointer; transition: all 0.2s;
    }
    .video-btn:hover, .video-btn.active { background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.4); color: #a5b4fc; }

    /* Progress bar top */
    .progress-bar-bg { background: rgba(255,255,255,0.06); border-radius: 99px; height: 5px; overflow: hidden; }
    .progress-bar-fill { background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: 99px; height: 5px; transition: width 0.5s ease; }
</style>

<div class="show-bg pt-4 pb-6 space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center gap-3 px-1">
        <a href="{{ route('workouts.index') }}" class="back-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-white font-extrabold text-xl leading-tight truncate">{{ $workout->name }}</h1>
            @if($workout->goal)
                <p class="text-slate-400 text-xs mt-0.5 truncate">{{ $workout->goal }}</p>
            @endif
        </div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0
            {{ $workout->is_active ? 'bg-emerald-500/15 text-emerald-300' : 'bg-zinc-700/40 text-slate-400' }}">
            {{ $workout->is_active ? 'Ativo' : 'Inativo' }}
        </span>
    </div>

    @if(auth()->user()->role === 'personal')
    {{-- EDIT BUTTON FOR PERSONAL --}}
    <a href="{{ route('workouts.edit', $workout) }}"
       class="flex items-center justify-center gap-2 py-3 rounded-2xl font-bold text-sm text-white"
       style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Editar Treino
    </a>
    @endif

    @if(auth()->user()->role === 'aluno')
    {{-- PROGRESS BAR --}}
    @php $totalEx = $workout->days->sum(fn($d) => $d->exercises->count()); @endphp
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:16px;"
         x-data="progressTracker({{ $totalEx }}, {{ count($todayLogs ?? []) }}, {{ $workout->id }})">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-white font-bold text-sm">Progresso de Hoje</p>
                <p class="text-slate-500 text-xs mt-0.5"><span x-text="current"></span> de {{ $totalEx }} exercícios</p>
            </div>
            <p class="text-indigo-400 font-extrabold text-2xl" x-text="Math.round((current / total) * 100) + '%'"></p>
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" :style="'width:' + ((current / total) * 100) + '%'"></div>
        </div>
    </div>
    @endif

    {{-- DAY SECTIONS --}}
    <div class="space-y-3">
        @foreach($workout->days as $day)
        <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
            {{-- Day Header --}}
            <div :class="open ? 'day-header open' : 'day-header'" style="display:flex;align-items:center;gap:10px;">
                <button type="button" @click="open = !open" style="display:flex;align-items:center;gap:12px;flex:1;text-align:left;">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,rgba(99,102,241,0.3),rgba(139,92,246,0.2));border:1px solid rgba(99,102,241,0.3);">
                        <span class="text-indigo-300 font-extrabold text-sm">{{ $loop->iteration }}</span>
                    </div>
                    <div class="text-left">
                        <p class="text-white font-bold text-sm">{{ $day->name }}</p>
                        <p class="text-slate-500 text-xs">{{ $day->exercises->count() }} exercícios</p>
                    </div>
                </button>
                @if(auth()->user()->role === 'aluno')
                <a href="{{ route('student.workout.active', [$workout, $day]) }}"
                   style="flex-shrink:0;display:flex;align-items:center;gap:5px;padding:8px 12px;border-radius:10px;font-size:12px;font-weight:800;color:#fff;background:linear-gradient(135deg,#6366f1,#8b5cf6);text-decoration:none;">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Iniciar
                </a>
                @endif
                <button type="button" @click="open = !open" style="flex-shrink:0;">
                    <svg class="w-5 h-5 text-slate-500 transition-transform" :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Exercises --}}
            <div x-show="open" x-transition
                 style="background:rgba(255,255,255,0.02);border:1px solid rgba(99,102,241,0.2);border-top:none;border-radius:0 0 16px 16px;padding:12px;gap:10px;display:flex;flex-direction:column;">
                @foreach($day->exercises as $exercise)
                    @if(auth()->user()->role === 'aluno')
                    <div class="exercise-card" :class="completed ? 'done' : ''"
                         x-data="exerciseItem({{ $exercise->id }}, {{ in_array($exercise->id, $todayLogs ?? []) ? 'true' : 'false' }}, @js($exercise->rest_time), @js($exercise->name), @js($exercise->embed_video_url))">

                        {{-- Name + Check --}}
                        <div class="flex items-center gap-3 p-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-bold text-sm leading-snug"
                                   :class="{ 'line-through text-slate-500': completed }">{{ $exercise->name }}</p>
                            </div>
                            <div @click="completed = !completed; toggle()" class="cursor-pointer active:scale-90 transition-transform">
                                <div x-show="!completed" class="ex-check-empty"></div>
                                <div x-show="completed"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="scale-0 opacity-0"
                                     x-transition:enter-end="scale-100 opacity-100"
                                     class="ex-check-done">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Stats chips --}}
                        <div class="flex gap-2 px-4 pb-4">
                            <div class="stat-chip">
                                <span class="stat-chip-label">Séries</span>
                                <span class="stat-chip-value">{{ $exercise->sets ?? '—' }}</span>
                            </div>
                            <div class="stat-chip">
                                <span class="stat-chip-label">Reps</span>
                                <span class="stat-chip-value">{{ $exercise->reps ?? '—' }}</span>
                            </div>
                            <div class="stat-chip">
                                <span class="stat-chip-label">Descanso</span>
                                <span class="stat-chip-value" style="font-size:16px;">{{ $exercise->rest_time ?? '—' }}</span>
                            </div>
                        </div>

                        {{-- Video btn --}}
                        <div class="px-4 pb-3">
                            <button type="button" @click="toggleVideo()"
                                    :class="showVideo ? 'video-btn active' : 'video-btn'">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="flex-1 text-left" x-text="videoButtonLabel()">Ver execução</span>
                            </button>
                        </div>

                        {{-- Video area --}}
                        <div x-show="showVideo" x-transition class="border-t border-white/5">
                            <template x-if="videoLoading">
                                <div class="flex items-center justify-center gap-3 py-8">
                                    <svg class="w-6 h-6 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                    <span class="text-sm text-slate-400">Buscando vídeo...</span>
                                </div>
                            </template>
                            <template x-if="videoError">
                                <div class="px-4 py-3 text-sm text-amber-300 bg-amber-950/20">
                                    <span x-text="videoError"></span>
                                </div>
                            </template>
                            <template x-if="videoUrl">
                                <div>
                                    <div class="w-full bg-black" style="aspect-ratio:16/9">
                                        <iframe class="w-full h-full" :src="videoUrl"
                                                :title="'Execução de ' + exerciseName"
                                                loading="lazy"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen></iframe>
                                    </div>
                                    <template x-if="videoTitle">
                                        <div class="px-4 py-3">
                                            <p class="text-xs font-semibold text-slate-200 leading-snug" x-text="videoTitle"></p>
                                            <p class="text-xs text-slate-500 mt-0.5" x-text="videoChannel"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Rest timer --}}
                        <template x-if="restSeconds > 0 && !completed">
                            <div class="px-4 pb-4">
                                <button type="button" @click="startTimer()" x-show="!timerRunning" class="timer-btn">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Iniciar Descanso
                                </button>
                                <div x-show="timerRunning" class="timer-running">
                                    <span class="flex items-center gap-2 text-indigo-300 font-bold text-xl">
                                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span x-text="formatTime(timeLeft)"></span>
                                    </span>
                                    <button @click="stopTimer()"
                                            class="text-xs font-semibold text-red-400 border border-red-500/40 px-3 py-1.5 rounded-lg bg-red-500/10">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    @else
                    {{-- Personal read mode --}}
                    <div class="exercise-card">
                        <div class="p-4">
                            <p class="text-white font-bold text-sm mb-3">{{ $exercise->name }}</p>
                            <div class="flex gap-2">
                                @if($exercise->sets)
                                <div class="stat-chip">
                                    <span class="stat-chip-label">Séries</span>
                                    <span class="stat-chip-value">{{ $exercise->sets }}</span>
                                </div>
                                @endif
                                @if($exercise->reps)
                                <div class="stat-chip">
                                    <span class="stat-chip-label">Reps</span>
                                    <span class="stat-chip-value">{{ $exercise->reps }}</span>
                                </div>
                                @endif
                                @if($exercise->rest_time)
                                <div class="stat-chip">
                                    <span class="stat-chip-label">Descanso</span>
                                    <span class="stat-chip-value" style="font-size:16px;">{{ $exercise->rest_time }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

</div>

<script>
    function exerciseItem(id, initialStatus, restTimeStr, exerciseName, initialVideoUrl) {
        const storageKey = `workout_log_${new Date().toISOString().split('T')[0]}_${id}`;
        const storageKeyExpire = `workout_log_expire_${id}`;

        function isValidProgress() {
            const expireDate = localStorage.getItem(storageKeyExpire);
            if (!expireDate) return true;
            const daysDiff = Math.floor((new Date() - new Date(expireDate)) / 86400000);
            if (daysDiff >= 7) {
                localStorage.removeItem(storageKey);
                localStorage.setItem(storageKeyExpire, new Date().toISOString());
                return false;
            }
            return true;
        }

        const savedStatus = isValidProgress() && localStorage.getItem(storageKey) === 'true';
        const status = savedStatus || initialStatus;

        let seconds = 0;
        if (restTimeStr) {
            const numbers = restTimeStr.replace(/[^0-9]/g, '');
            seconds = parseInt(numbers) || 0;
            if (restTimeStr.toLowerCase().includes('min')) seconds *= 60;
        }

        return {
            id, exerciseName, completed: status,
            restSeconds: seconds, timeLeft: seconds,
            timerRunning: false, interval: null,
            showVideo: false, videoLoaded: Boolean(initialVideoUrl),
            videoLoading: false, videoError: '',
            videoUrl: initialVideoUrl || '', videoTitle: '', videoChannel: '',
            storageKey, storageKeyExpire,

            toggle() {
                localStorage.setItem(this.storageKey, this.completed);
                if (!localStorage.getItem(this.storageKeyExpire))
                    localStorage.setItem(this.storageKeyExpire, new Date().toISOString());
                window.dispatchEvent(new CustomEvent('progress-change', { detail: { value: this.completed ? 1 : -1 } }));
            },

            videoButtonLabel() {
                if (this.videoLoading) return 'Buscando...';
                if (this.showVideo) return 'Ocultar vídeo';
                return this.videoLoaded ? 'Ver execução' : 'Buscar no YouTube';
            },

            toggleVideo() {
                this.showVideo = !this.showVideo;
                if (this.showVideo && !this.videoLoaded && !this.videoLoading)
                    this.loadYoutubeVideo();
            },

            loadYoutubeVideo() {
                this.videoLoading = true;
                this.videoError = '';
                fetch(`{{ route('student.exercise.youtube') }}?name=${encodeURIComponent(this.exerciseName)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(async r => {
                    const data = await r.json().catch(() => ({}));
                    if (!r.ok) throw new Error(data.message || 'Não encontrei vídeo.');
                    return data;
                })
                .then(data => {
                    this.videoUrl = `https://www.youtube.com/embed/${data.video_id}`;
                    this.videoTitle = data.title || '';
                    this.videoChannel = data.channel_title || '';
                    this.videoLoaded = true;
                })
                .catch(e => { this.videoError = e.message || 'Não foi possível carregar o vídeo.'; })
                .finally(() => { this.videoLoading = false; });
            },

            startTimer() {
                if (this.restSeconds <= 0) return;
                this.timerRunning = true;
                this.timeLeft = this.restSeconds;
                this.interval = setInterval(() => {
                    this.timeLeft--;
                    if (this.timeLeft <= 0) this.finishTimer();
                }, 1000);
            },

            stopTimer() {
                clearInterval(this.interval);
                this.timerRunning = false;
                this.timeLeft = this.restSeconds;
            },

            finishTimer() {
                this.stopTimer();
                this.completed = true;
                this.toggle();
                if (navigator.vibrate) navigator.vibrate([200, 100, 200]);
            },

            formatTime(s) {
                return `${Math.floor(s/60)}:${(s%60).toString().padStart(2,'0')}`;
            }
        }
    }

    function progressTracker(total, initialCurrent, workoutId) {
        const keyCount = `workout_progress_${workoutId}`;
        const keyDate  = `workout_progress_date_${workoutId}`;

        function getWeekStart() {
            const t = new Date(), d = t.getDay();
            const diff = t.getDate() - d + (d === 0 ? -6 : 1);
            return new Date(t.setDate(diff)).toISOString().split('T')[0];
        }

        function load() {
            const saved = localStorage.getItem(keyDate);
            const wk = getWeekStart();
            if (!saved || saved !== wk) {
                localStorage.setItem(keyCount, initialCurrent);
                localStorage.setItem(keyDate, wk);
                return initialCurrent;
            }
            return parseInt(localStorage.getItem(keyCount) || initialCurrent);
        }

        return {
            total,
            current: load(),
            init() {
                window.addEventListener('progress-change', e => {
                    this.current = Math.max(0, Math.min(this.total, this.current + e.detail.value));
                    localStorage.setItem(keyCount, this.current);
                });
            }
        }
    }
</script>
@endsection
