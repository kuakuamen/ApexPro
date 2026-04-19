@extends('layouts.app')

@section('content')
<style>
    .aw-bg { background: #0d0f1a; min-height: 100vh; margin: -1rem; padding: 0; }

    /* Header */
    .aw-header {
        display: flex; align-items: center; gap: 12px;
        padding: 16px 16px 12px;
        background: rgba(13,15,26,0.98);
        position: sticky; top: 0; z-index: 10;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .aw-back {
        width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
        color: #94a3b8; text-decoration: none;
    }
    .aw-timer {
        margin-left: auto; flex-shrink: 0;
        font-size: 15px; font-weight: 800; font-family: monospace;
        color: #4ade80; background: rgba(74,222,128,0.1);
        border: 1px solid rgba(74,222,128,0.25);
        padding: 5px 10px; border-radius: 8px;
    }

    /* Progress bar */
    .aw-progress-bar { height: 3px; background: rgba(255,255,255,0.08); }
    .aw-progress-fill { height: 3px; background: linear-gradient(90deg,#6366f1,#8b5cf6); transition: width 0.4s ease; }

    /* Content area */
    .aw-content { padding: 20px 16px 120px; }

    /* Exercise card */
    .ex-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 18px; padding: 18px; margin-bottom: 16px;
    }
    .ex-muscle { font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.1em; color: #818cf8; margin-bottom: 6px; }
    .ex-name { font-size: 22px; font-weight: 900; color: #fff; line-height: 1.2; }

    /* Video placeholder */
    .video-area {
        background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.07);
        border-radius: 14px; overflow: hidden; margin-bottom: 16px;
        aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;
        position: relative; cursor: pointer;
    }
    .video-play-btn {
        width: 56px; height: 56px; border-radius: 50%;
        background: rgba(99,102,241,0.8); backdrop-filter: blur(4px);
        display: flex; align-items: center; justify-content: center;
        border: 2px solid rgba(99,102,241,0.6);
    }
    .video-label { position: absolute; bottom: 10px; left: 12px;
        font-size: 11px; color: #64748b; font-weight: 600; }
    .video-credit {
        position: absolute;
        bottom: 8px;
        right: 10px;
        font-size: 10px;
        color: #94a3b8;
        background: rgba(2, 6, 23, 0.65);
        border: 1px solid rgba(148, 163, 184, 0.25);
        border-radius: 999px;
        padding: 2px 8px;
    }

    /* Series card */
    .series-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 16px; padding: 16px; margin-bottom: 12px;
    }
    .series-title { font-size: 15px; font-weight: 800; color: #fff; }
    .series-sub { font-size: 13px; color: #64748b; margin-top: 3px; }
    .rest-badge {
        background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.35);
        color: #818cf8; font-size: 12px; font-weight: 700;
        padding: 5px 12px; border-radius: 20px; white-space: nowrap;
    }

    /* Input fields */
    .input-group { display: flex; gap: 10px; margin-bottom: 16px; }
    .input-box {
        flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px; padding: 14px;
    }
    .input-label { font-size: 11px; color: #64748b; font-weight: 700; margin-bottom: 6px; }
    .input-val {
        font-size: 28px; font-weight: 900; color: #64748b;
        background: none; border: none; outline: none; width: 100%;
        padding: 0;
    }
    .input-val:focus { color: #fff; }

    /* Buttons */
    .btn-done {
        flex: 1; padding: 16px; border-radius: 14px; font-size: 15px; font-weight: 800;
        color: #fff; background: #22c55e; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: opacity 0.2s;
    }
    .btn-done:active { opacity: 0.85; }
    .btn-next {
        flex: 1; padding: 16px; border-radius: 14px; font-size: 15px; font-weight: 800;
        color: #fff; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        background: linear-gradient(135deg,#6366f1,#8b5cf6);
        transition: opacity 0.2s;
    }
    .btn-next:active { opacity: 0.85; }

    /* Rest overlay */
    .rest-overlay {
        position: fixed; inset: 0; z-index: 100;
        background: rgba(13,15,26,0.97);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 12px;
    }
    .rest-circle {
        width: 160px; height: 160px; border-radius: 50%;
        border: 4px solid rgba(99,102,241,0.3);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        position: relative; margin-bottom: 8px;
    }
    .rest-time { font-size: 48px; font-weight: 900; color: #fff; font-family: monospace; line-height: 1; }
    .rest-lbl { font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-top: 4px; }
    .skip-btn {
        padding: 14px 40px; border-radius: 14px; font-size: 15px; font-weight: 800;
        color: #818cf8; background: rgba(99,102,241,0.12);
        border: 1px solid rgba(99,102,241,0.3); cursor: pointer; margin-top: 12px;
    }
</style>

@php
    $exercises = $day->exercises;
    $totalEx   = $exercises->count();
@endphp

<div class="aw-bg"
     x-data="activeWorkout({{ $exercises->toJson() }}, {{ json_encode($todayLogs) }})">

    {{-- HEADER --}}
    <div class="aw-header">
        <a href="{{ route('workouts.show', $workout) }}" class="aw-back">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <p class="text-white font-extrabold text-sm truncate">{{ $workout->name }} — <span x-text="'Exercício ' + (currentIdx+1) + '/{{ $totalEx }}'"></span></p>
            <p class="text-slate-500 text-xs mt-0.5 truncate">{{ $day->name }}</p>
        </div>
        <div class="aw-timer" x-text="formatTime(elapsed)">00:00:00</div>
    </div>

    {{-- PROGRESS BAR --}}
    <div class="aw-progress-bar">
        <div class="aw-progress-fill" :style="'width:' + ((currentIdx / {{ $totalEx }}) * 100) + '%'"></div>
    </div>
    <div class="flex justify-between px-4 py-1.5">
        <span class="text-xs text-slate-500">Exercício <span x-text="currentIdx+1"></span> de {{ $totalEx }}</span>
        <span class="text-xs text-slate-500"><span x-text="{{ $totalEx }} - currentIdx - 1"></span> restantes</span>
    </div>

    {{-- CONTENT --}}
    <div class="aw-content">

        {{-- Exercise name card --}}
        <div class="ex-card">
            <p class="ex-muscle" x-text="currentEx().observation || '{{ $day->name }}'"></p>
            <p class="ex-name" x-text="currentEx().name"></p>
        </div>

        {{-- Video area --}}
        <div class="video-area" @click="toggleVideo()">
            <template x-if="!showVideo">
                <div class="flex flex-col items-center gap-3">
                    <div class="video-play-btn">
                        <svg class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            </template>
            <template x-if="showVideo && videoLoading">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-8 h-8 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span class="text-sm text-slate-400">Buscando vídeo...</span>
                </div>
            </template>
            <template x-if="showVideo && videoUrl && !videoLoading">
                <template x-if="videoType === 'iframe'">
                    <iframe class="w-full h-full absolute inset-0"
                            :src="videoUrl"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </template>
            </template>
            <template x-if="showVideo && videoUrl && !videoLoading && videoType === 'image'">
                <img class="w-full h-full absolute inset-0 object-contain bg-black" :src="videoUrl" alt="Demonstracao do exercicio">
            </template>
            <template x-if="showVideo && videoUrl && !videoLoading && videoType === 'video'">
                <video class="w-full h-full absolute inset-0 object-contain bg-black" :src="videoUrl" controls playsinline></video>
            </template>
            <template x-if="showVideo && mediaAttribution && !videoLoading">
                <span class="video-credit" x-text="mediaAttribution"></span>
            </template>
            <template x-if="showVideo && videoError && !videoLoading">
                <p class="text-amber-400 text-sm px-4 text-center" x-text="videoError"></p>
            </template>
            <span class="video-label">▶ Ver vídeo demonstrativo</span>
        </div>

        {{-- Series info card --}}
        <div class="series-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="series-title">Série <span x-text="currentSerie"></span> de <span x-text="currentEx().sets || '—'"></span></p>
                    <p class="series-sub" x-text="(currentEx().reps || '—') + ' repetições'"></p>
                </div>
                <template x-if="currentEx().rest_time">
                    <span class="rest-badge" x-text="currentEx().rest_time + ' descanso'"></span>
                </template>
            </div>
        </div>

        {{-- Weight + Reps inputs --}}
        <div class="input-group">
            <div class="input-box">
                <p class="input-label">Peso (kg)</p>
                <input type="number" class="input-val" x-model="currentWeight" placeholder="60" inputmode="decimal">
            </div>
            <div class="input-box">
                <p class="input-label">Reps feitas</p>
                <input type="number" class="input-val" x-model="currentReps" placeholder="12" inputmode="numeric">
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex gap-3">
            <button class="btn-done" @click="completeSerie()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                Concluir Série
            </button>
            <button class="btn-next" @click="nextExercise()">
                Próximo
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
        <p class="text-xs text-slate-600 text-center mt-3" x-text="currentEx().rest_time ? 'ⓘ Descanso configurado: ' + currentEx().rest_time : ''"></p>

    </div>

    {{-- REST OVERLAY --}}
    <div class="rest-overlay" x-show="resting" x-transition style="display:none;">
        <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">Descansando</p>
        <div class="rest-circle">
            <span class="rest-time" x-text="formatRestTime(restLeft)"></span>
            <span class="rest-lbl">segundos</span>
        </div>
        <p class="text-slate-500 text-sm">Próximo: <span class="text-white font-bold" x-text="nextExName()"></span></p>
        <button class="skip-btn" @click="skipRest()">Pular descanso →</button>
    </div>

    {{-- WORKOUT DONE OVERLAY --}}
    <div class="rest-overlay" x-show="workoutDone" x-transition style="display:none;text-align:center;">
        <span style="font-size:64px;">🏆</span>
        <p class="text-white font-extrabold text-2xl mt-2">Treino Concluído!</p>
        <p class="text-slate-400 text-sm mt-1">Você completou <strong class="text-white">{{ $day->name }}</strong></p>
        <p class="text-indigo-400 font-bold text-lg mt-2" x-text="formatTime(elapsed)"></p>
        <a href="{{ route('student.dashboard') }}"
           style="margin-top:28px;padding:16px 40px;border-radius:16px;font-size:16px;font-weight:800;color:#fff;background:linear-gradient(135deg,#6366f1,#8b5cf6);text-decoration:none;display:block;">
            Voltar ao Início
        </a>
    </div>

</div>

<script>
function activeWorkout(exercises, todayLogs) {
    return {
        exercises,
        currentIdx: 0,
        currentSerie: 1,
        currentWeight: '',
        currentReps: '',
        elapsed: 0,
        elapsedInterval: null,

        // Rest
        resting: false,
        restLeft: 0,
        restInterval: null,

        // Video
        showVideo: false,
        videoLoading: false,
        videoType: 'iframe',
        videoUrl: '',
        videoError: '',
        mediaAttribution: '',
        videoCache: {},

        workoutDone: false,

        init() {
            // Pula exercícios já feitos hoje
            while (this.currentIdx < this.exercises.length &&
                   todayLogs.includes(this.exercises[this.currentIdx].id)) {
                this.currentIdx++;
            }
            if (this.currentIdx >= this.exercises.length) {
                this.workoutDone = true;
                return;
            }
            // Inicia stopwatch geral
            this.elapsedInterval = setInterval(() => this.elapsed++, 1000);
        },

        currentEx() {
            return this.exercises[this.currentIdx] || {};
        },

        nextExName() {
            const next = this.exercises[this.currentIdx + 1];
            return next ? next.name : 'Fim do treino';
        },

        completeSerie() {
            const ex = this.currentEx();
            const totalSeries = parseInt(ex.sets) || 1;

            if (this.currentSerie < totalSeries) {
                // Ainda tem séries: inicia descanso e avança série
                this.currentSerie++;
                this.startRest(ex.rest_time);
            } else {
                // Última série: marca exercício como feito e avança
                this.logExercise(ex.id);
                this.currentSerie = 1;
                this.currentWeight = '';
                this.currentReps = '';
                this.advanceExercise();
            }
        },

        nextExercise() {
            this.currentSerie = 1;
            this.currentWeight = '';
            this.currentReps = '';
            this.showVideo = false;
            this.videoType = 'iframe';
            this.videoUrl = '';
            this.videoError = '';
            this.mediaAttribution = '';
            this.advanceExercise();
        },

        advanceExercise() {
            this.showVideo = false;
            this.videoType = 'iframe';
            this.videoUrl = '';
            this.videoError = '';
            this.mediaAttribution = '';
            this.currentIdx++;
            if (this.currentIdx >= this.exercises.length) {
                clearInterval(this.elapsedInterval);
                this.workoutDone = true;
            }
        },

        startRest(restTimeStr) {
            let seconds = 60;
            if (restTimeStr) {
                const n = parseInt(restTimeStr.replace(/[^0-9]/g, '')) || 60;
                seconds = restTimeStr.toLowerCase().includes('min') ? n * 60 : n;
            }
            this.restLeft = seconds;
            this.resting = true;
            this.restInterval = setInterval(() => {
                this.restLeft--;
                if (this.restLeft <= 0) this.skipRest();
            }, 1000);
            if (navigator.vibrate) navigator.vibrate(200);
        },

        skipRest() {
            clearInterval(this.restInterval);
            this.resting = false;
            this.restLeft = 0;
        },

        logExercise(exerciseId) {
            fetch(`/aluno/exercicio/${exerciseId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            }).catch(() => {});
        },

        toggleVideo() {
            this.showVideo = !this.showVideo;
            if (this.showVideo && !this.videoUrl && !this.videoLoading) {
                const ex = this.currentEx();
                if (ex.embed_video_url) {
                    this.videoType = this.detectMediaType(ex.embed_video_url);
                    this.videoUrl = ex.embed_video_url;
                    this.mediaAttribution = this.detectAttribution(ex.embed_video_url);
                } else {
                    this.loadVideo(ex.name);
                }
            }
        },

        loadVideo(name) {
            if (this.videoCache[name]) {
                this.videoType = this.detectMediaType(this.videoCache[name]);
                this.videoUrl = this.videoCache[name];
                this.mediaAttribution = this.detectAttribution(this.videoCache[name]);
                return;
            }
            this.videoLoading = true;
            this.videoError = '';
            fetch(`{{ route('student.exercise.youtube') }}?name=${encodeURIComponent(name)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async r => {
                const d = await r.json().catch(() => ({}));
                if (!r.ok) throw new Error(d.message || 'Vídeo não encontrado.');
                return d;
            })
            .then(d => {
                this.videoUrl = `https://www.youtube.com/embed/${d.video_id}`;
                this.videoType = 'iframe';
                this.mediaAttribution = this.detectAttribution(this.videoUrl);
                this.videoCache[name] = this.videoUrl;
            })
            .catch(e => { this.videoError = e.message; })
            .finally(() => { this.videoLoading = false; });
        },

        detectMediaType(url) {
            const u = (url || '').toLowerCase();
            if (u.includes('youtube.com/embed') || u.includes('player.vimeo.com/video')) return 'iframe';
            if (u.match(/\.(gif|png|jpg|jpeg|webp)(\?|$)/)) return 'image';
            if (u.match(/\.(mp4|webm|ogg)(\?|$)/)) return 'video';
            return 'iframe';
        },

        detectAttribution(url) {
            const u = (url || '').toLowerCase();
            if (u.includes('gifdotreino.com') || u.includes('/media/gifdotreino/')) {
                return 'Fonte: gifdotreino.com';
            }
            return '';
        },

        formatTime(s) {
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
        },

        formatRestTime(s) {
            return String(s).padStart(2, '0');
        }
    }
}
</script>
@endsection
