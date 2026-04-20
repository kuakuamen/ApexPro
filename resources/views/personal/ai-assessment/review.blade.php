@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="aiReviewForm({{ json_encode($analysisResult['workout_recommendation']['days'] ?? []) }}, {{ json_encode($catalogExercises ?? []) }})">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Resultado da Análise IA</h2>
            <p class="text-gray-400 mt-1">Revise os insights e o plano sugerido antes de aprovar.</p>
        </div>
        <a href="{{ route('personal.ai-assessment.index') }}" class="inline-flex items-center text-gray-400 hover:text-white transition-colors group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Voltar
        </a>
    </div>

    <!-- Análise Postural (Visual) -->
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 md:p-8">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                <div class="p-2 bg-indigo-500/20 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                Análise Postural
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Desvios Identificados -->
                <div class="bg-gray-800 border border-gray-700 p-6 rounded-xl shadow-sm hover:border-red-500/30 transition-colors duration-300">
                    <h4 class="font-bold text-red-400 mb-6 flex items-center text-lg tracking-wide">
                        <div class="p-2 bg-red-500/10 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        Desvios Identificados
                    </h4>
                    <ul class="space-y-5">
                        <li class="flex flex-col text-sm">
                            <span class="font-bold text-red-300 text-base mb-1">Lordose</span>
                            <span class="text-gray-300 leading-relaxed bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">{{ $analysisResult['posture_analysis']['lordosis'] ?? 'Não detectado' }}</span>
                        </li>
                        <li class="flex flex-col text-sm">
                            <span class="font-bold text-red-300 text-base mb-1">Escoliose</span>
                            <span class="text-gray-300 leading-relaxed bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">{{ $analysisResult['posture_analysis']['scoliosis'] ?? 'Não detectado' }}</span>
                        </li>
                        <li class="flex flex-col text-sm">
                            <span class="font-bold text-red-300 text-base mb-1">Ombros</span>
                            <span class="text-gray-300 leading-relaxed bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">{{ $analysisResult['posture_analysis']['shoulders'] ?? 'Neutro' }}</span>
                        </li>
                        <li class="flex flex-col text-sm">
                            <span class="font-bold text-red-300 text-base mb-1">Cabeça</span>
                            <span class="text-gray-300 leading-relaxed bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">{{ $analysisResult['posture_analysis']['head_position'] ?? 'Neutro' }}</span>
                        </li>
                        <li class="flex flex-col text-sm">
                            <span class="font-bold text-red-300 text-base mb-1">Joelhos</span>
                            <span class="text-gray-300 leading-relaxed bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">{{ $analysisResult['posture_analysis']['knees'] ?? 'Neutro' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Foco Sugerido -->
                <div class="bg-gray-800 border border-gray-700 p-6 rounded-xl shadow-sm hover:border-teal-500/30 transition-colors duration-300">
                    <h4 class="font-bold text-teal-400 mb-6 flex items-center text-lg tracking-wide">
                        <div class="p-2 bg-teal-500/10 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        Estratégia Corretiva
                    </h4>
                    <div class="mb-8">
                        <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-white bg-teal-600 px-3 py-1.5 rounded-full border border-teal-500 mb-3 shadow-md">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Fortalecer
                        </span>
                        <div class="text-gray-300 leading-loose bg-gray-900/50 p-4 rounded-lg border border-gray-700/50 text-sm">
                            {{ implode(', ', is_array($analysisResult['suggested_focus']['strengthen'] ?? []) ? ($analysisResult['suggested_focus']['strengthen'] ?? []) : explode(',', $analysisResult['suggested_focus']['strengthen'])) }}
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-white bg-blue-600 px-3 py-1.5 rounded-full border border-blue-500 mb-3 shadow-md">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            Alongar
                        </span>
                        <div class="text-gray-300 leading-loose bg-gray-900/50 p-4 rounded-lg border border-gray-700/50 text-sm">
                            {{ implode(', ', is_array($analysisResult['suggested_focus']['stretch'] ?? []) ? ($analysisResult['suggested_focus']['stretch'] ?? []) : explode(',', $analysisResult['suggested_focus']['stretch'])) }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat de Refinamento -->
            <div class="mt-8 border-t border-gray-700 pt-6">
                <h4 class="text-sm font-bold text-gray-300 mb-3 flex items-center gap-2">
                    <span>🤖</span> Ajustar Análise com IA
                </h4>
                <form id="refine-form" action="{{ route('personal.ai-assessment.refine') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="goal" value="{{ $request->goal }}">
                    
                    <input type="text" name="feedback" placeholder="Ex: O aluno não tem lordose. Remova isso e ajuste o treino." class="flex-1 bg-gray-900/50 border border-gray-600 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" required>
                    
                    <button id="refine-btn" type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-colors">
                        <span id="refine-btn-text">Refinar</span>
                        <svg id="refine-spinner" class="hidden animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">Escreva o que você quer mudar e a IA vai refazer a análise e o treino.</p>
            </div>
            
            <div class="mt-6 flex items-center text-sm text-gray-400 bg-gray-900/30 p-3 rounded-lg border border-gray-700/50">
                <span class="font-bold text-gray-300 mr-2">Prioridade do Treino:</span> {{ $analysisResult['workout_recommendation']['priority'] ?? 'Geral' }}
            </div>
        </div>
    </div>

    <!-- Sugestao de Treino (Editavel) -->
    <form action="{{ route('personal.ai-assessment.store') }}" method="POST" id="workoutForm">
        @csrf
        <input type="hidden" name="student_id"       value="{{ $student->id }}">
        <input type="hidden" name="goal"             value="{{ $request->goal }}">
        <input type="hidden" name="experience_level" value="{{ $request->experience_level }}">
        <input type="hidden" name="front_path"       value="{{ $frontPath }}">
        <input type="hidden" name="side_path"        value="{{ $sidePath }}">
        <input type="hidden" name="back_path"        value="{{ $backPath }}">
        <input type="hidden" name="ai_analysis_data" value="{{ htmlspecialchars(json_encode($analysisResult), ENT_QUOTES, 'UTF-8') }}">

        <div class="ai-workout-editor bg-zinc-900/60 border border-teal-900/40 rounded-xl overflow-hidden shadow-lg">
            <div class="p-6 border-b border-teal-900/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-zinc-900/60">
                <div>
                    <h3 class="text-xl font-bold text-white">Treino Sugerido pela IA</h3>
                    <p class="text-sm text-gray-400 mt-1">Revise e ajuste os exercicios antes de aprovar.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-72">
                        <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Nome do Plano</label>
                        <input type="text" name="workout_name" value="Treino IA: {{ $analysisResult['workout_recommendation']['type'] ?? $request->goal }}" class="block w-full bg-zinc-950/80 border border-teal-900/40 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                    <button type="button" onclick="generatePDF()" class="inline-flex items-center justify-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-300 bg-gray-800 hover:bg-gray-700 hover:text-white focus:outline-none transition-colors">
                        <svg class="h-4 w-4 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Exportar PDF
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <template x-for="(day, dayIndex) in days" :key="day.id">
                    <div class="ai-day-card relative border border-teal-900/30 rounded-xl p-5 bg-zinc-900/60">
                        <button type="button" @click="removeDay(dayIndex)" class="ai-day-remove absolute top-4 right-4" x-show="days.length > 1">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <div class="mb-4 max-w-2xl">
                            <label class="block text-xs font-medium text-teal-300 mb-1 uppercase tracking-wide">Dia do Treino</label>
                            <input type="text" :name="'days[' + dayIndex + '][name]'" x-model="day.name" class="block w-full bg-zinc-950/80 border border-teal-900/40 rounded-lg px-4 py-2 text-white font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>

                        <div class="ai-exercise-list space-y-3">
                            <template x-for="(exercise, exIndex) in day.exercises" :key="exercise.id">
                                <div class="ai-ex-card border border-teal-900/25 rounded-xl p-4 bg-zinc-950/55">
                                    <div class="ai-ex-main">
                                        <label class="block text-xs font-medium text-gray-400 mb-1 uppercase tracking-wide">Exercicio</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="exercise.name" class="block w-full bg-zinc-950/80 border border-teal-900/40 rounded-md px-3 py-2 text-sm text-white" readonly>
                                            <button type="button" @click="openExercisePicker(dayIndex, exIndex)" class="ai-edit-btn">Editar</button>
                                        </div>
                                        <input type="hidden" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][name]'" x-model="exercise.name">
                                        <input type="hidden" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][video_url]'" x-model="exercise.video_url">
                                        <input type="hidden" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][custom_exercise]'" x-model="exercise.custom_exercise">
                                    </div>

                                    <div class="ai-metrics-grid">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-400 mb-1 uppercase tracking-wide">Series</label>
                                            <select :name="'days[' + dayIndex + '][exercises][' + exIndex + '][sets]'" x-model="exercise.sets" class="block w-full bg-zinc-950/80 border border-teal-900/40 rounded-md px-2 py-2 text-sm text-white">
                                                <option value="">...</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-400 mb-1 uppercase tracking-wide">Reps</label>
                                            <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][reps]'" x-model="exercise.reps" class="block w-full bg-zinc-950/80 border border-teal-900/40 rounded-md px-3 py-2 text-sm text-white">
                                        </div>
                                        <div class="ai-notes-field">
                                            <label class="block text-xs font-medium text-gray-400 mb-1 uppercase tracking-wide">Notas</label>
                                            <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][notes]'" x-model="exercise.notes" class="block w-full bg-zinc-950/80 border border-teal-900/40 rounded-md px-3 py-2 text-sm text-white" placeholder="Observacoes...">
                                        </div>
                                        <div class="ai-remove-wrap">
                                            <button type="button" @click="removeExercise(dayIndex, exIndex)" class="ai-remove-btn">Remover</button>
                                        </div>
                                    </div>

                                    <div class="ai-preview-block">
                                        <label class="block text-xs font-medium text-gray-400 mb-1 uppercase tracking-wide">Previa da execucao</label>
                                        <template x-if="exercise.video_url">
                                            <img :src="exercise.video_url" alt="preview" class="ai-preview-image w-32 h-24 object-cover rounded border border-teal-900/40 bg-zinc-950/70">
                                        </template>
                                        <template x-if="!exercise.video_url">
                                            <div class="text-xs text-amber-300">Exercicio personalizado sem video demonstrativo.</div>
                                        </template>
                                    </div>

                                    <div class="ai-tag-wrap">
                                        <span class="inline-flex px-2 py-1 text-xs rounded border font-semibold" :class="exercise.custom_exercise ? 'text-amber-200 bg-amber-700/20 border-amber-600/30' : 'text-teal-200 bg-teal-700/20 border-teal-600/30'">
                                            <span x-text="exercise.custom_exercise ? 'Personalizado' : 'Catalogo'"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addExercise(dayIndex)" class="ai-add-ex-btn mt-1 inline-flex items-center text-xs font-medium">
                                + Adicionar Exercicio
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addDay()" class="w-full py-4 border border-dashed border-teal-500/40 rounded-xl text-sm font-bold text-teal-300 bg-teal-500/10 hover:bg-teal-500/15 hover:border-teal-500/60 transition-colors">
                    + Adicionar Dia de Treino
                </button>
            </div>

            <div class="px-6 py-5 bg-zinc-900 border-t border-teal-900/40 flex justify-end">
                <button type="submit" class="inline-flex justify-center items-center py-3 px-8 border border-transparent shadow-lg text-base font-medium rounded-lg text-zinc-900 bg-teal-400 hover:bg-teal-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-500 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Aprovar e Enviar para Aluno
                </button>
            </div>
        </div>

        <div x-show="pickerOpen" x-transition style="display:none" class="catalog-modal-backdrop fixed inset-0 z-50 flex items-start justify-center bg-black/70 p-3 overflow-hidden" @click.self="closeExercisePicker()">
            <div class="catalog-modal w-full overflow-hidden p-3 flex flex-col shadow-2xl mt-8" style="max-width: 560px; max-height: 72vh;">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-stone-100 font-semibold">Selecionar exercicio do catalogo</h4>
                    <button type="button" @click="closeExercisePicker()" class="text-stone-400 hover:text-stone-200">Fechar</button>
                </div>
                <input type="text" x-model="pickerQuery" placeholder="Buscar exercicio..." class="catalog-search w-full mb-3 rounded border bg-zinc-950/70 text-stone-100 px-3 py-2">
                <div class="catalog-scroll flex-1 min-h-0 overflow-y-auto overscroll-contain space-y-2 pr-1 touch-pan-y" style="max-height: 42vh;" @wheel.stop>
                    <template x-for="item in filteredCatalogExercises()" :key="item.name">
                        <button type="button" @click="applyCatalogExercise(item)" class="catalog-item-btn w-full text-left rounded border p-2">
                            <div class="text-stone-100 text-sm font-medium" x-text="item.name"></div>
                        </button>
                    </template>
                </div>
                <div class="mt-3 space-y-2">
                    <p class="catalog-custom-note text-xs">Opcao personalizada nao tem video demonstrativo.</p>
                    <div class="flex items-center gap-2">
                        <input type="text" x-model="pickerCustomName" placeholder="Nome do exercicio personalizado" class="catalog-custom-input flex-1 rounded border bg-zinc-950/80 text-stone-100 px-3 py-2 text-sm">
                        <button type="button" @click.prevent.stop="applyCustomExercise()" class="catalog-custom-btn px-3 py-2 rounded border text-sm font-bold focus:outline-none focus:ring-2 focus:ring-yellow-300">Usar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    function generatePDF() {
        const form = document.getElementById('workoutForm');
        const originalAction = form.action;
        const originalTarget = form.target;
        
        form.action = "{{ route('personal.ai-assessment.pdf') }}";
        form.target = "_blank";
        form.submit();
        form.action = originalAction;
        form.target = originalTarget;
    }

    function aiReviewForm(initialDays, catalogExercises) {
        const seed = Date.now();
        const fallbackDay = [{ name: 'Treino A', exercises: [] }];
        const sourceDays = (Array.isArray(initialDays) && initialDays.length > 0) ? initialDays : fallbackDay;

        const buildExercise = (exercise, dayIndex, exIndex) => {
            const mediaUrl = (exercise && (exercise.video_url || exercise.media_url)) ? (exercise.video_url || exercise.media_url) : '';

            return {
                id: `${seed}-${dayIndex}-${exIndex}-${Math.random().toString(36).slice(2, 7)}`,
                name: (exercise && exercise.name) ? exercise.name : '',
                sets: (exercise && exercise.sets !== undefined && exercise.sets !== null) ? String(exercise.sets) : '3',
                reps: (exercise && exercise.reps) ? exercise.reps : '10-12',
                notes: (exercise && (exercise.notes || exercise.observation)) ? (exercise.notes || exercise.observation) : '',
                video_url: mediaUrl,
                custom_exercise: mediaUrl ? 0 : 1,
            };
        };

        return {
            days: sourceDays.map((day, dayIndex) => {
                const exercises = Array.isArray(day.exercises) ? day.exercises : [];
                const mappedExercises = exercises.map((exercise, exIndex) => buildExercise(exercise, dayIndex, exIndex));

                return {
                    id: `${seed}-d-${dayIndex}-${Math.random().toString(36).slice(2, 7)}`,
                    name: day.name || `Treino ${dayIndex + 1}`,
                    exercises: mappedExercises.length ? mappedExercises : [buildExercise({}, dayIndex, 0)],
                };
            }),
            catalogExercises: Array.isArray(catalogExercises) ? catalogExercises : [],
            pickerOpen: false,
            pickerQuery: '',
            pickerCustomName: '',
            pickerDayIndex: null,
            pickerExerciseIndex: null,

            newExercise(dayIndex) {
                return buildExercise({}, dayIndex, Date.now());
            },
            addDay() {
                const dayIndex = this.days.length;
                this.days.push({
                    id: `${Date.now()}-day-${dayIndex}`,
                    name: `Treino ${dayIndex + 1}`,
                    exercises: [this.newExercise(dayIndex)],
                });
            },
            removeDay(index) {
                this.days.splice(index, 1);
            },
            addExercise(dayIndex) {
                this.days[dayIndex].exercises.push(this.newExercise(dayIndex));
            },
            removeExercise(dayIndex, exIndex) {
                this.days[dayIndex].exercises.splice(exIndex, 1);
            },
            openExercisePicker(dayIndex, exIndex) {
                this.pickerDayIndex = dayIndex;
                this.pickerExerciseIndex = exIndex;
                this.pickerQuery = '';
                this.pickerCustomName = '';
                this.pickerOpen = true;
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
            },
            closeExercisePicker() {
                this.pickerOpen = false;
                this.pickerQuery = '';
                this.pickerCustomName = '';
                this.pickerDayIndex = null;
                this.pickerExerciseIndex = null;
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
            },
            filteredCatalogExercises() {
                const q = (this.pickerQuery || '').toLowerCase().trim();
                if (!q) {
                    return this.catalogExercises.slice(0, 200);
                }
                return this.catalogExercises
                    .filter((item) => (item.name || '').toLowerCase().includes(q))
                    .slice(0, 200);
            },
            applyCatalogExercise(item) {
                if (this.pickerDayIndex === null || this.pickerExerciseIndex === null) return;
                const ex = this.days[this.pickerDayIndex].exercises[this.pickerExerciseIndex];
                ex.name = item.name;
                ex.video_url = item.media_url;
                ex.custom_exercise = 0;
                this.closeExercisePicker();
            },
            applyCustomExercise() {
                if (this.pickerDayIndex === null || this.pickerExerciseIndex === null) return;
                const customName = (this.pickerCustomName || '').trim();
                if (!customName) {
                    alert('Digite o nome do exercicio personalizado.');
                    return;
                }
                const ex = this.days[this.pickerDayIndex].exercises[this.pickerExerciseIndex];
                ex.name = customName;
                ex.video_url = '';
                ex.custom_exercise = 1;
                this.closeExercisePicker();
            },
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        const refineForm = document.getElementById('refine-form');
        if (!refineForm) return;

        const overlay    = document.getElementById('refine-overlay');
        const messageEl  = document.getElementById('refine-message');
        const progressEl = document.getElementById('refine-progress');
        const btnText    = document.getElementById('refine-btn-text');
        const spinner    = document.getElementById('refine-spinner');
        const messages = [
            "Processando seu feedback...",
            "Ajustando recomendações posturais...",
            "Recalculando proporções musculares...",
            "Gerando novos exercícios...",
            "Finalizando análise refinada..."
        ];

        refineForm.addEventListener('submit', function () {
            // Loading no botão
            btnText.textContent = 'Refinando...';
            spinner.classList.remove('hidden');

            // Overlay fullscreen
            overlay.classList.remove('hidden');
            void overlay.offsetWidth;
            overlay.classList.remove('opacity-0');

            let progress = 0, messageIndex = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 5;
                if (progress > 95) progress = 95;
                progressEl.style.width = `${progress}%`;
                const newIndex = Math.floor(progress / 20);
                if (newIndex !== messageIndex && newIndex < messages.length) {
                    messageIndex = newIndex;
                    messageEl.style.opacity = '0';
                    setTimeout(() => { messageEl.textContent = messages[messageIndex]; messageEl.style.opacity = '1'; }, 300);
                }
            }, 500);
            setTimeout(() => clearInterval(interval), 30000);
        });
    });
</script>

<!-- Loading Overlay (Refinar) -->
<div id="refine-overlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="text-center max-w-md mx-auto p-6">
        <div class="relative w-24 h-24 mx-auto mb-8">
            <div class="absolute inset-0 border-t-4 border-indigo-500 border-solid rounded-full animate-spin"></div>
            <div class="absolute inset-2 border-t-4 border-purple-500 border-solid rounded-full" style="animation: spin-reverse 1s linear infinite;"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-8 h-8 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-white mb-2 animate-pulse">Refinando com IA...</h3>
        <p id="refine-message" class="text-gray-300 text-lg transition-all duration-300">Processando seu feedback...</p>
        <div class="w-full bg-gray-700 rounded-full h-2.5 mt-6 overflow-hidden">
            <div id="refine-progress" class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
        <p class="text-sm text-gray-500 mt-4">Por favor, não feche esta janela.</p>
    </div>
</div>

<style>
    .ai-workout-editor {
        background:
            radial-gradient(120% 140% at 0% 0%, rgba(45, 212, 191, 0.1), rgba(2, 6, 23, 0.92) 58%),
            linear-gradient(180deg, rgba(2, 6, 23, 0.94), rgba(2, 6, 23, 0.98));
    }
    .ai-day-card {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.84), rgba(15, 23, 42, 0.72));
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.03), 0 14px 24px rgba(2, 6, 23, 0.28);
    }
    .ai-day-remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        color: #fda4af;
        border: 1px solid rgba(248, 113, 113, 0.35);
        background: rgba(127, 29, 29, 0.22);
    }
    .ai-day-remove:hover {
        color: #fecdd3;
        border-color: rgba(248, 113, 113, 0.6);
    }
    .ai-exercise-list {
        border: 1px solid rgba(45, 212, 191, 0.22);
        border-radius: 12px;
        background: rgba(2, 6, 23, 0.35);
        padding: 12px;
    }
    .ai-ex-card {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 10px;
    }
    .ai-ex-main {
        flex: 1 1 320px;
        min-width: 260px;
    }
    .ai-edit-btn {
        border-radius: 10px;
        padding: 8px 11px;
        border: 1px solid rgba(99, 102, 241, 0.45);
        background: rgba(79, 70, 229, 0.2);
        color: #c4b5fd;
        font-size: 12px;
        font-weight: 700;
    }
    .ai-edit-btn:hover {
        background: rgba(79, 70, 229, 0.32);
    }
    .ai-metrics-grid {
        flex: 2 1 430px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }
    .ai-notes-field {
        min-width: 200px;
    }
    .ai-remove-wrap {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
    }
    .ai-remove-btn {
        border: 1px solid rgba(248, 113, 113, 0.36);
        background: rgba(127, 29, 29, 0.16);
        color: #fda4af;
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 700;
    }
    .ai-remove-btn:hover {
        background: rgba(153, 27, 27, 0.24);
        color: #fecdd3;
    }
    .ai-preview-block {
        flex: 0 1 165px;
        min-width: 145px;
    }
    .ai-preview-image {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(2, 6, 23, 0.35);
    }
    .ai-tag-wrap {
        margin-left: auto;
    }
    .ai-add-ex-btn {
        border: 1px dashed rgba(45, 212, 191, 0.46);
        color: #99f6e4;
        background: rgba(20, 184, 166, 0.12);
        border-radius: 10px;
        padding: 9px 12px;
        font-weight: 800;
    }
    .ai-add-ex-btn:hover {
        background: rgba(20, 184, 166, 0.2);
    }
    .catalog-modal-backdrop {
        backdrop-filter: blur(2px);
    }
    .catalog-modal {
        border-radius: 16px;
        border: 1px solid rgba(45, 212, 191, 0.35);
        background: linear-gradient(180deg, rgba(17, 24, 39, 0.98), rgba(2, 6, 23, 0.98));
    }
    .catalog-search {
        border-color: rgba(45, 212, 191, 0.34) !important;
    }
    .catalog-item-btn {
        border-color: rgba(45, 212, 191, 0.26);
        background: rgba(2, 6, 23, 0.6);
    }
    .catalog-item-btn:hover {
        background: rgba(15, 23, 42, 0.88);
    }
    .catalog-custom-note {
        color: #fbbf24;
        font-weight: 600;
    }
    .catalog-custom-input {
        border-color: rgba(251, 191, 36, 0.42) !important;
    }
    .catalog-custom-btn {
        border-color: rgba(251, 191, 36, 0.75);
        background: linear-gradient(135deg, #facc15, #f59e0b);
        color: #111827;
        font-weight: 800;
    }
    .catalog-custom-btn:hover {
        background: linear-gradient(135deg, #fde047, #fbbf24);
    }
    .catalog-scroll {
        scrollbar-width: thin;
        scrollbar-color: #14b8a6 #0b1220;
    }
    .catalog-scroll::-webkit-scrollbar {
        width: 10px;
    }
    .catalog-scroll::-webkit-scrollbar-track {
        background: #0b1220;
        border-radius: 999px;
    }
    .catalog-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #22d3ee 0%, #0d9488 100%);
        border-radius: 999px;
        border: 2px solid #0b1220;
    }
    .catalog-scroll::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #67e8f9 0%, #14b8a6 100%);
    }
    @media (max-width: 1024px) {
        .ai-ex-main {
            min-width: 100%;
        }
        .ai-metrics-grid {
            flex-basis: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .ai-preview-block {
            flex-basis: 100%;
        }
    }
    @media (max-width: 768px) {
        .ai-day-card {
            padding: 14px;
        }
        .ai-ex-card {
            padding: 10px;
        }
        .ai-metrics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .ai-remove-wrap {
            justify-content: flex-start;
            grid-column: span 2;
        }
        .ai-preview-image {
            width: 100%;
            height: 140px;
        }
        .ai-tag-wrap {
            margin-left: 0;
        }
    }
    @keyframes spin-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
</style>

@endsection
