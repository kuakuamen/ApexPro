@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>
<style>
.workout-editor {
    max-width: 1240px;
    margin: 20px auto;
    border-radius: 24px;
    border: 1px solid rgba(45, 212, 191, 0.25);
    background:
        radial-gradient(130% 140% at 0% 0%, rgba(45, 212, 191, 0.17), rgba(2, 6, 23, 0.9) 55%),
        linear-gradient(180deg, rgba(2, 6, 23, 0.95), rgba(2, 6, 23, 0.98));
    box-shadow: 0 26px 68px rgba(2, 6, 23, 0.62);
    overflow: hidden;
    position: relative;
}

.workout-editor::before {
    content: "";
    position: absolute;
    right: -120px;
    top: -120px;
    width: 260px;
    height: 260px;
    border-radius: 999px;
    background: radial-gradient(circle at center, rgba(129, 140, 248, 0.34), rgba(129, 140, 248, 0));
    pointer-events: none;
}

.editor-header {
    background: linear-gradient(90deg, rgba(15, 23, 42, 0.96), rgba(17, 24, 39, 0.9));
    border-bottom: 1px solid rgba(45, 212, 191, 0.2);
    padding-top: 22px;
    padding-bottom: 22px;
    position: relative;
    z-index: 1;
}

.editor-title {
    margin: 0;
    font-size: clamp(30px, 4vw, 42px);
    line-height: 1;
    font-weight: 900;
    letter-spacing: -0.03em;
    color: #f8fafc;
}

.editor-cancel-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #67e8f9;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid rgba(34, 211, 238, 0.25);
    background: rgba(15, 23, 42, 0.55);
    border-radius: 10px;
    padding: 8px 12px;
    transition: all 0.2s ease;
}

.editor-cancel-link:hover {
    color: #a5f3fc;
    border-color: rgba(34, 211, 238, 0.5);
    background: rgba(15, 23, 42, 0.8);
}

.editor-content {
    background: rgba(2, 6, 23, 0.66);
    padding-top: 28px;
    position: relative;
    z-index: 1;
}

.editor-basics {
    background: rgba(15, 23, 42, 0.42);
    border: 1px solid rgba(45, 212, 191, 0.18);
    border-radius: 16px;
    padding: 18px;
}

.editor-divider {
    border-color: rgba(45, 212, 191, 0.2);
}

.editor-content label.block.text-sm,
.editor-content label.block.text-xs {
    text-transform: uppercase;
    letter-spacing: 0.07em;
    font-size: 11px;
    font-weight: 800;
    color: #94a3b8;
}

.editor-content input[type="text"],
.editor-content select {
    border-color: rgba(45, 212, 191, 0.3) !important;
    background: rgba(2, 6, 23, 0.72) !important;
    border-radius: 11px !important;
    color: #e2e8f0 !important;
}

.editor-content input[type="text"]:focus,
.editor-content select:focus {
    outline: none;
    border-color: rgba(45, 212, 191, 0.68) !important;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2);
}

.editor-day-card {
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.83), rgba(15, 23, 42, 0.74));
    border: 1px solid rgba(45, 212, 191, 0.26);
    border-radius: 18px;
    padding: 20px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 20px 36px rgba(2, 6, 23, 0.34);
}

.day-delete-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid rgba(248, 113, 113, 0.35);
    background: rgba(127, 29, 29, 0.22);
    color: #fda4af;
    transition: all 0.2s ease;
}

.day-delete-btn:hover {
    color: #fecdd3;
    border-color: rgba(248, 113, 113, 0.55);
}

.day-title {
    font-size: 30px;
    font-weight: 900;
    color: #f8fafc;
    letter-spacing: -0.03em;
}

.day-meta-grid {
    margin-bottom: 16px;
}

.exercise-list {
    border: 1px solid rgba(45, 212, 191, 0.22);
    border-radius: 14px;
    padding: 12px;
    background: rgba(2, 6, 23, 0.36);
    display: grid;
    gap: 12px;
}

.editor-ex-card {
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.88), rgba(15, 23, 42, 0.74));
    border: 1px solid rgba(45, 212, 191, 0.22);
    border-radius: 14px;
    padding: 14px;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 12px;
}

.editor-ex-main {
    flex: 1 1 280px;
    min-width: 260px;
}

.editor-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.editor-name {
    font-weight: 800;
    color: #f8fafc;
}

.editor-edit-btn {
    border-radius: 10px;
    padding: 8px 11px;
    border: 1px solid rgba(99, 102, 241, 0.45);
    background: rgba(79, 70, 229, 0.2);
    color: #c4b5fd;
    font-weight: 700;
    transition: all 0.2s ease;
}

.editor-edit-btn:hover {
    background: rgba(79, 70, 229, 0.32);
}

.editor-metrics-grid {
    flex: 2 1 420px;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
}

.editor-metric-field {
    min-width: 0;
}

.editor-select {
    border-radius: 10px;
    color: #e2e8f0;
}

.editor-remove-wrap {
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
}

.editor-remove-btn {
    border: 1px solid rgba(248, 113, 113, 0.36) !important;
    background: rgba(127, 29, 29, 0.16);
    color: #fda4af !important;
    font-weight: 700;
    border-radius: 10px;
    padding: 8px 12px;
    transition: all 0.2s ease;
}

.editor-remove-btn:hover {
    background: rgba(153, 27, 27, 0.24);
    color: #fecdd3 !important;
}

.editor-preview-block {
    flex: 0 1 170px;
    min-width: 150px;
}

.editor-preview-label {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #94a3b8;
}

.editor-preview {
    border-radius: 12px;
    border: 1px solid rgba(45, 212, 191, 0.34) !important;
    box-shadow: 0 8px 20px rgba(2, 6, 23, 0.35);
}

.editor-preview-empty {
    font-size: 12px;
    color: #fbbf24;
}

.editor-tag-wrap {
    margin-left: auto;
}

.editor-tag {
    border-radius: 999px;
    padding: 7px 10px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.editor-add-ex-btn {
    border-style: dashed;
    border-width: 1px;
    border-color: rgba(45, 212, 191, 0.44);
    color: #99f6e4;
    background: rgba(20, 184, 166, 0.11);
    font-weight: 800;
    border-radius: 10px;
    padding: 9px 12px;
    transition: all 0.2s ease;
}

.editor-add-ex-btn:hover {
    background: rgba(20, 184, 166, 0.2);
}

.editor-add-day-btn {
    border-style: dashed !important;
    border-width: 1px !important;
    border-color: rgba(45, 212, 191, 0.5) !important;
    color: #99f6e4 !important;
    background: rgba(20, 184, 166, 0.15) !important;
    font-weight: 800;
    border-radius: 11px;
    padding: 10px 14px;
}

.editor-actions {
    margin-top: 30px;
}

.editor-save-btn {
    background: linear-gradient(135deg, #2dd4bf, #14b8a6) !important;
    color: #042f2e !important;
    font-weight: 900;
    border-radius: 12px;
    padding: 11px 18px;
    box-shadow: 0 14px 28px rgba(20, 184, 166, 0.3);
}

.catalog-modal-backdrop {
    backdrop-filter: blur(2px);
}

.catalog-modal {
    max-width: 620px;
    max-height: 72vh;
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

@media (max-width: 1024px) {
    .editor-content {
        padding-left: 18px !important;
        padding-right: 18px !important;
    }

    .editor-ex-main {
        min-width: 100%;
    }

    .editor-metrics-grid {
        flex-basis: 100%;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .editor-preview-block {
        flex-basis: 100%;
    }
}

@media (max-width: 768px) {
    .workout-editor {
        margin: 10px 8px 18px;
        border-radius: 16px;
    }

    .editor-header {
        padding-left: 14px !important;
        padding-right: 14px !important;
    }

    .editor-title {
        font-size: 28px;
    }

    .editor-cancel-link {
        padding: 7px 10px;
        font-size: 13px;
    }

    .editor-content {
        padding: 14px !important;
    }

    .editor-basics {
        padding: 13px;
    }

    .editor-day-card {
        padding: 14px;
        border-radius: 14px;
    }

    .day-title {
        font-size: 24px;
    }

    .editor-ex-card {
        padding: 12px;
    }

    .editor-metrics-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .editor-remove-wrap {
        justify-content: flex-start;
        grid-column: span 2;
    }

    .editor-preview {
        width: 100%;
        height: 140px;
    }

    .editor-tag-wrap {
        margin-left: 0;
    }

    .catalog-modal-backdrop {
        padding: 10px;
    }

    .catalog-modal {
        max-height: 84vh;
    }
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
</style>

<div class="workout-editor">
    <div class="editor-header p-6 sm:px-20 flex justify-between items-center">
        <div class="editor-title">
            Criar Novo Plano de Treino
        </div>
        <a href="{{ route('workouts.index') }}" class="editor-cancel-link">Cancelar</a>
    </div>

    @php
        $initialWorkout = [
            'name' => old('name', ''),
            'goal' => old('goal', ''),
            'days' => [[
                'id' => 1,
                'name' => '',
                'exercises' => [[
                    'id' => 1,
                    'name' => '',
                    'sets' => '',
                    'reps' => '',
                    'rest_time' => '',
                    'video_url' => '',
                    'custom_exercise' => 0,
                ]],
            ]],
        ];
        $selectedStudent = old('student_id', $selectedStudentId);
    @endphp

    <div class="editor-content p-6 sm:px-20" x-data='workoutForm(@json($initialWorkout), @json($catalogExercises ?? []))'>
        <form action="{{ route('workouts.store') }}" method="POST">
            @csrf

            <!-- Dados BÃ¡sicos -->
            <div class="editor-basics grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-stone-300">Aluno</label>
                    <select name="student_id" required class="mt-1 block w-full py-2 px-3 border border-teal-900/30 bg-zinc-900/70 rounded-md shadow-sm text-stone-100 sm:text-sm">
                        <option value="">Selecione um aluno...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected((string) $selectedStudent === (string) $student->id)>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-300">Nome do Plano</label>
                    <input type="text" name="name" x-model="name" class="mt-1 focus:ring-teal-600 focus:border-teal-600 block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md p-2" placeholder="Ex: Hipertrofia Iniciante" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-stone-300">Objetivo Principal</label>
                    <input type="text" name="goal" x-model="goal" class="mt-1 focus:ring-teal-600 focus:border-teal-600 block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md p-2" placeholder="Ex: Ganho de massa muscular">
                </div>
            </div>

            <hr class="editor-divider my-8">

            <!-- Dias de Treino -->
            <div class="space-y-8">
                <template x-for="(day, dayIndex) in days" :key="day.id">
                    <div class="editor-day-card relative">
                        <button type="button" @click="removeDay(dayIndex)" class="day-delete-btn absolute top-4 right-4" x-show="days.length > 1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <h4 class="day-title mb-4">Dia <span x-text="dayIndex + 1"></span></h4>

                        <!-- SeleÃ§Ã£o de Dia e Tipo -->
                        <div class="day-meta-grid grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-stone-300">Dia da Semana</label>
                                <select x-model="day.weekDay" class="mt-1 block w-full py-2 px-3 border border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md shadow-sm focus:outline-none focus:ring-teal-600 focus:border-teal-600 sm:text-sm">
                                    <option value="">Selecione...</option>
                                    <option value="Segunda-feira">Segunda-feira</option>
                                    <option value="TerÃ§a-feira">TerÃ§a-feira</option>
                                    <option value="Quarta-feira">Quarta-feira</option>
                                    <option value="Quinta-feira">Quinta-feira</option>
                                    <option value="Sexta-feira">Sexta-feira</option>
                                    <option value="SÃ¡bado">SÃ¡bado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-stone-300">Tipo de Treino</label>
                                <select x-model="day.workoutType" class="mt-1 block w-full py-2 px-3 border border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md shadow-sm focus:outline-none focus:ring-teal-600 focus:border-teal-600 sm:text-sm">
                                    <option value="">Selecione...</option>
                                    <optgroup label="DivisÃµes ClÃ¡ssicas">
                                        <option value="Peito">Peito</option>
                                        <option value="Costas">Costas</option>
                                        <option value="Pernas (Completo)">Pernas (Completo)</option>
                                        <option value="Ombros">Ombros</option>
                                        <option value="BraÃ§os (BÃ­ceps + TrÃ­ceps)">BraÃ§os (BÃ­ceps + TrÃ­ceps)</option>
                                    </optgroup>
                                    <optgroup label="CombinaÃ§Ãµes">
                                        <option value="Peito + TrÃ­ceps">Peito + TrÃ­ceps</option>
                                        <option value="Costas + BÃ­ceps">Costas + BÃ­ceps</option>
                                        <option value="Peito + Ombros">Peito + Ombros</option>
                                        <option value="QuadrÃ­ceps + Panturrilha">QuadrÃ­ceps + Panturrilha</option>
                                        <option value="Posterior + GlÃºteo">Posterior + GlÃºteo</option>
                                        <option value="Superior Completo">Superior Completo</option>
                                        <option value="Inferior Completo">Inferior Completo</option>
                                        <option value="Full Body">Full Body</option>
                                    </optgroup>
                                    <option value="Personalizado">Outro (Personalizado)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Campo Oculto para o Backend -->
                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][name]'" x-bind:value="day.weekDay + ' - ' + (day.workoutType === 'Personalizado' ? day.customName : day.workoutType)">

                        <div x-show="day.workoutType === 'Personalizado'" class="mb-4">
                            <label class="block text-sm font-medium text-stone-300">Nome do Treino (Personalizado)</label>
                            <input type="text" x-model="day.customName" class="mt-1 block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md p-2">
                        </div>

                        <!-- Lista de ExercÃ­cios -->
                        <div class="exercise-list">
                            <template x-for="(exercise, exerciseIndex) in day.exercises" :key="exercise.id">
                                <div class="editor-ex-card">
                                    <div class="editor-ex-main">
                                        <label class="block text-xs font-medium text-stone-400">Exercicio</label>
                                        <div class="editor-name-row">
                                            <input type="text" x-model="exercise.name" class="editor-name block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md" readonly>
                                            <button type="button" @click="openExercisePicker(dayIndex, exerciseIndex)" class="editor-edit-btn">Editar</button>
                                        </div>
                                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][name]'" x-model="exercise.name">
                                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][video_url]'" x-model="exercise.video_url">
                                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][custom_exercise]'" x-model="exercise.custom_exercise">
                                    </div>
                                    <div class="editor-metrics-grid">
                                    <div class="editor-metric-field">
                                        <label class="block text-xs font-medium text-stone-400">SÃ©ries</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][sets]'" x-model="exercise.sets" class="editor-select block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md py-2 px-2">
                                            <option value="">...</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                        </select>
                                    </div>
                                    <div class="editor-metric-field">
                                        <label class="block text-xs font-medium text-stone-400">Reps</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][reps]'" x-model="exercise.reps" class="editor-select block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md py-2 px-2">
                                            <option value="">...</option>
                                            <option value="6">6</option>
                                            <option value="8">8</option>
                                            <option value="10">10</option>
                                            <option value="12">12</option>
                                            <option value="15">15</option>
                                            <option value="Falha">Falha</option>
                                        </select>
                                    </div>
                                    <div class="editor-metric-field">
                                        <label class="block text-xs font-medium text-stone-400">Descanso (s)</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][rest_time]'" x-model="exercise.rest_time" class="editor-select block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md py-2 px-2">
                                            <option value="">...</option>
                                            <option value="30s">30s</option>
                                            <option value="60s">60s</option>
                                            <option value="90s">90s</option>
                                            <option value="120s">120s</option>
                                            <option value="180s">180s</option>
                                        </select>
                                    </div>
                                    <div class="editor-remove-wrap">
                                        <button type="button" @click="removeExercise(dayIndex, exerciseIndex)" class="editor-remove-btn">
                                            Remover
                                        </button>
                                    </div>
                                    </div>
                                    <div class="editor-preview-block">
                                        <label class="editor-preview-label">Previa da execucao</label>
                                        <template x-if="exercise.video_url">
                                            <img :src="exercise.video_url" alt="preview" class="editor-preview w-36 h-24 md:w-28 md:h-20 object-cover rounded border border-teal-900/40 bg-zinc-950/60">
                                        </template>
                                        <template x-if="!exercise.video_url">
                                            <div class="editor-preview-empty">Exercicio personalizado sem video demonstrativo.</div>
                                        </template>
                                    </div>
                                    <div class="editor-tag-wrap">
                                        <span class="editor-tag inline-flex rounded border" :class="exercise.custom_exercise ? 'text-amber-200 bg-amber-700/20 border-amber-600/30' : 'text-teal-200 bg-teal-700/20 border-teal-600/30'">
                                            <span x-text="exercise.custom_exercise ? 'Personalizado' : 'Catalogo'"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addExercise(dayIndex)" class="editor-add-ex-btn mt-2 inline-flex items-center text-xs font-medium focus:outline-none">
                                + Adicionar ExercÃ­cio
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6">
                <button type="button" @click="addDay()" class="editor-add-day-btn inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-stone-100 bg-teal-700 hover:bg-teal-800 focus:outline-none">
                    + Adicionar Dia de Treino
                </button>
            </div>

            <div class="editor-actions mt-8 flex justify-end">
                <button type="submit" class="editor-save-btn inline-flex items-center border border-transparent text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-600">
                    Salvar Treino Completo
                </button>
            </div>


        <div x-show="pickerOpen" x-transition style="display:none" class="catalog-modal-backdrop fixed inset-0 z-50 flex items-start justify-center bg-black/70 p-3 overflow-hidden" @click.self="closeExercisePicker()">
            <div class="catalog-modal w-full overflow-hidden p-3 flex flex-col shadow-2xl mt-8"
                 style="max-width: 560px; max-height: 70vh;">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-stone-100 font-semibold">Selecionar exercicio do catalogo</h4>
                    <button type="button" @click="closeExercisePicker()" class="text-stone-400 hover:text-stone-200">Fechar</button>
                </div>
                <input type="text" x-model="pickerQuery" placeholder="Buscar exercicio..." class="catalog-search w-full mb-3 rounded border bg-zinc-950/70 text-stone-100 px-3 py-2">
                <div class="catalog-scroll flex-1 min-h-0 overflow-y-auto overscroll-contain space-y-2 pr-1 touch-pan-y"
                     style="max-height: 42vh;"
                     @wheel.stop>
                    <template x-for="item in filteredCatalogExercises()" :key="item.name">
                        <button type="button" @click="applyCatalogExercise(item)" class="catalog-item-btn w-full text-left rounded border p-2">
                            <div class="text-stone-100 text-sm font-medium" x-text="item.name"></div>
                        </button>
                    </template>
                </div>
                <div class="mt-3 space-y-2">
                    <p class="catalog-custom-note text-xs">Opcao personalizado nao tem video demonstrativo.</p>
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-model="pickerCustomName"
                               placeholder="Nome do exercicio personalizado"
                               class="catalog-custom-input flex-1 rounded border bg-zinc-950/80 text-stone-100 px-3 py-2 text-sm">
                        <button type="button"
                                @click.prevent.stop="applyCustomExercise()"
                                class="catalog-custom-btn px-3 py-2 rounded border text-sm font-bold focus:outline-none focus:ring-2 focus:ring-yellow-300"
                                style="opacity:1; pointer-events:auto;">
                            Usar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>

<script>
    const exerciseDB = {
        'Peito': ['Supino Reto (Barra)', 'Supino Inclinado (Halteres)', 'Crucifixo', 'Crossover', 'FlexÃ£o de BraÃ§o', 'Peck Deck', 'Supino Declinado'],
        'Costas': ['Puxada Alta', 'Remada Curvada', 'Remada Baixa', 'Barra Fixa', 'Levantamento Terra', 'Pulldown', 'Remada Cavalinho'],
        'Pernas (Completo)': ['Agachamento Livre', 'Leg Press 45', 'Cadeira Extensora', 'Mesa Flexora', 'Stiff', 'Afundo', 'Panturrilha Sentado', 'Panturrilha em PÃ©'],
        'Ombros': ['Desenvolvimento Militar', 'ElevaÃ§Ã£o Lateral', 'ElevaÃ§Ã£o Frontal', 'Crucifixo Inverso', 'Encolhimento', 'Remada Alta'],
        'BÃ­ceps': ['Rosca Direta', 'Rosca Alternada', 'Rosca Martelo', 'Rosca Scott', 'Rosca Concentrada'],
        'TrÃ­ceps': ['TrÃ­ceps Corda', 'TrÃ­ceps Testa', 'TrÃ­ceps Banco', 'TrÃ­ceps FrancÃªs', 'TrÃ­ceps Coice'],
        'AbdÃ´men': ['Abdominal Supra', 'Abdominal Infra', 'Prancha', 'Abdominal Remador'],
        'QuadrÃ­ceps': ['Agachamento', 'Leg Press', 'Extensora', 'Afundo'],
        'Posterior': ['Mesa Flexora', 'Stiff', 'Cadeira Flexora', 'ElevaÃ§Ã£o PÃ©lvica'],
        'GlÃºteo': ['ElevaÃ§Ã£o PÃ©lvica', 'GlÃºteo 4 Apoios', 'AbduÃ§Ã£o de Quadril']
    };

    function workoutForm(workout, catalogExercises) {
        const initialDays = Array.isArray(workout?.days) && workout.days.length
            ? workout.days
            : [{
                id: Date.now(),
                name: '',
                exercises: [{
                    id: Date.now() + 1,
                    name: '',
                    sets: '',
                    reps: '',
                    rest_time: '',
                    video_url: '',
                    custom_exercise: 0
                }]
            }];

        return {
            name: workout?.name ?? '',
            goal: workout?.goal ?? '',
            catalogExercises: Array.isArray(catalogExercises) ? catalogExercises : [],
            pickerOpen: false,
            pickerQuery: '',
            pickerCustomName: '',
            pickerDayIndex: null,
            pickerExerciseIndex: null,
            days: initialDays.map(day => {
                // Tentar extrair "Segunda-feira" e "Peito" da string "Segunda-feira - Peito"
                let parts = (day.name || '').split(' - ');
                let wDay = '';
                let wType = '';
                let wCustom = '';
                
                if (parts.length >= 2) {
                    wDay = parts[0];
                    wType = parts[1];
                    // Verifica se o tipo existe na nossa lista padrÃ£o
                    // Se nÃ£o, assume que Ã© personalizado
                    // SimplificaÃ§Ã£o: Vamos assumir que se nÃ£o for um dos padrÃµes, Ã© Personalizado
                } else {
                    wDay = day.name; // Fallback
                }

                const dayExercises = Array.isArray(day.exercises) && day.exercises.length
                    ? day.exercises
                    : [{
                        id: Date.now() + 1,
                        name: '',
                        sets: '',
                        reps: '',
                        rest_time: '',
                        video_url: '',
                        custom_exercise: 0
                    }];

                return {
                    id: day.id,
                    weekDay: wDay,
                    workoutType: wType, 
                    customName: wCustom,
                    exercises: dayExercises.map(ex => ({
                        id: ex.id,
                        name: ex.name,
                        sets: ex.sets,
                        reps: ex.reps,
                        rest_time: ex.rest_time,
                        video_url: ex.video_url ?? '',
                        custom_exercise: typeof ex.custom_exercise !== 'undefined'
                            ? (Number(ex.custom_exercise) ? 1 : 0)
                            : (ex.video_url ? 0 : 1)
                    }))
                };
            }),
            
            getExercisesForType(type) {
                if (!type || type === 'Personalizado') return Object.values(exerciseDB).flat().sort();
                let suggestions = [];
                if (type.includes('Peito')) suggestions.push(...exerciseDB['Peito']);
                if (type.includes('Costas')) suggestions.push(...exerciseDB['Costas']);
                if (type.includes('Pernas')) suggestions.push(...exerciseDB['Pernas (Completo)']);
                if (type.includes('Ombros')) suggestions.push(...exerciseDB['Ombros']);
                if (type.includes('BÃ­ceps')) suggestions.push(...exerciseDB['BÃ­ceps']);
                if (type.includes('TrÃ­ceps')) suggestions.push(...exerciseDB['TrÃ­ceps']);
                if (type.includes('QuadrÃ­ceps')) suggestions.push(...exerciseDB['QuadrÃ­ceps']);
                if (type.includes('Posterior')) suggestions.push(...exerciseDB['Posterior']);
                if (type.includes('GlÃºteo')) suggestions.push(...exerciseDB['GlÃºteo']);
                return [...new Set(suggestions)];
            },

            openExercisePicker(dayIndex, exerciseIndex) {
                this.pickerDayIndex = dayIndex;
                this.pickerExerciseIndex = exerciseIndex;
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
                if (!q) return this.catalogExercises.slice(0, 200);
                return this.catalogExercises.filter(item => (item.name || '').toLowerCase().includes(q)).slice(0, 200);
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
                const ex = this.days[this.pickerDayIndex].exercises[this.pickerExerciseIndex];
                const customName = (this.pickerCustomName || '').trim();
                if (!customName) {
                    alert('Digite o nome do exercicio personalizado.');
                    return;
                }
                ex.custom_exercise = 1;
                ex.video_url = '';
                ex.name = customName;
                this.closeExercisePicker();
            },
            addDay() {
                this.days.push({
                    id: Date.now(),
                    weekDay: '',
                    workoutType: '',
                    customName: '',
                    exercises: [{ id: Date.now() + 1, name: '', sets: '', reps: '', rest_time: '', video_url: '', custom_exercise: 0 }]
                });
            },
            removeDay(index) {
                this.days.splice(index, 1);
            },
            addExercise(dayIndex) {
                this.days[dayIndex].exercises.push({
                    id: Date.now(),
                    name: '',
                    sets: '',
                    reps: '',
                    rest_time: '',
                    video_url: '',
                    custom_exercise: 0
                });
            },
            removeExercise(dayIndex, exerciseIndex) {
                this.days[dayIndex].exercises.splice(exerciseIndex, 1);
            }
        }
    }
</script>
@endsection

