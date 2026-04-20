@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div class="bg-zinc-900/55 shadow-xl sm:rounded-lg border border-teal-900/30">
    <div class="p-6 sm:px-20 bg-zinc-900/70 border-b border-teal-900/40 flex justify-between items-center">
        <div class="mt-8 text-2xl font-bold text-stone-100">
            Editar Plano de Treino
        </div>
        <a href="{{ route('workouts.show', $workout) }}" class="text-teal-300 hover:text-teal-200">Cancelar</a>
    </div>

    <div class="p-6 sm:px-20 bg-zinc-950/40" x-data="workoutForm({{ json_encode($workout) }}, {{ json_encode($catalogExercises ?? []) }})">
        <form action="{{ route('workouts.update', $workout) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Dados Básicos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-stone-300">Aluno</label>
                    <input type="text" class="mt-1 block w-full py-2 px-3 border border-teal-900/30 bg-zinc-900/70 rounded-md shadow-sm text-stone-300 sm:text-sm" value="{{ $workout->student->name }}" disabled>
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-300">Nome do Plano</label>
                    <input type="text" name="name" x-model="name" class="mt-1 focus:ring-teal-600 focus:border-teal-600 block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md p-2" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-stone-300">Objetivo Principal</label>
                    <input type="text" name="goal" x-model="goal" class="mt-1 focus:ring-teal-600 focus:border-teal-600 block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md p-2">
                </div>
            </div>

            <hr class="my-8 border-teal-900/30">

            <!-- Dias de Treino -->
            <div class="space-y-8">
                <template x-for="(day, dayIndex) in days" :key="day.id">
                    <div class="bg-zinc-900/65 p-6 rounded-lg shadow border border-teal-900/30 relative">
                        <button type="button" @click="removeDay(dayIndex)" class="absolute top-4 right-4 text-red-400 hover:text-red-300" x-show="days.length > 1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <h4 class="text-lg font-medium text-stone-100 mb-4">Dia <span x-text="dayIndex + 1"></span></h4>

                        <!-- Seleção de Dia e Tipo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-stone-300">Dia da Semana</label>
                                <select x-model="day.weekDay" class="mt-1 block w-full py-2 px-3 border border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md shadow-sm focus:outline-none focus:ring-teal-600 focus:border-teal-600 sm:text-sm">
                                    <option value="">Selecione...</option>
                                    <option value="Segunda-feira">Segunda-feira</option>
                                    <option value="Terça-feira">Terça-feira</option>
                                    <option value="Quarta-feira">Quarta-feira</option>
                                    <option value="Quinta-feira">Quinta-feira</option>
                                    <option value="Sexta-feira">Sexta-feira</option>
                                    <option value="Sábado">Sábado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-stone-300">Tipo de Treino</label>
                                <select x-model="day.workoutType" class="mt-1 block w-full py-2 px-3 border border-teal-900/40 bg-zinc-900/70 text-stone-100 rounded-md shadow-sm focus:outline-none focus:ring-teal-600 focus:border-teal-600 sm:text-sm">
                                    <option value="">Selecione...</option>
                                    <optgroup label="Divisões Clássicas">
                                        <option value="Peito">Peito</option>
                                        <option value="Costas">Costas</option>
                                        <option value="Pernas (Completo)">Pernas (Completo)</option>
                                        <option value="Ombros">Ombros</option>
                                        <option value="Braços (Bíceps + Tríceps)">Braços (Bíceps + Tríceps)</option>
                                    </optgroup>
                                    <optgroup label="Combinações">
                                        <option value="Peito + Tríceps">Peito + Tríceps</option>
                                        <option value="Costas + Bíceps">Costas + Bíceps</option>
                                        <option value="Peito + Ombros">Peito + Ombros</option>
                                        <option value="Quadríceps + Panturrilha">Quadríceps + Panturrilha</option>
                                        <option value="Posterior + Glúteo">Posterior + Glúteo</option>
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

                        <!-- Lista de Exercícios -->
                        <div class="space-y-4 pl-4 border-l-2 border-teal-900/40">
                            <template x-for="(exercise, exerciseIndex) in day.exercises" :key="exercise.id">
                                <div class="grid grid-cols-12 gap-4 items-end bg-zinc-900/70 p-3 rounded border border-teal-900/30">
                                    <div class="col-span-4">
                                        <label class="block text-xs font-medium text-stone-400">Exercicio</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="exercise.name" class="block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md" readonly>
                                            <button type="button" @click="openExercisePicker(dayIndex, exerciseIndex)" class="px-2 py-1 text-xs rounded bg-indigo-600/20 border border-indigo-500/40 text-indigo-300 hover:bg-indigo-600/30">Editar</button>
                                        </div>
                                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][name]'" x-model="exercise.name">
                                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][video_url]'" x-model="exercise.video_url">
                                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][custom_exercise]'" x-model="exercise.custom_exercise">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-stone-400">Séries</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][sets]'" x-model="exercise.sets" class="block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md py-2 px-2">
                                            <option value="">...</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-stone-400">Reps</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][reps]'" x-model="exercise.reps" class="block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md py-2 px-2">
                                            <option value="">...</option>
                                            <option value="6">6</option>
                                            <option value="8">8</option>
                                            <option value="10">10</option>
                                            <option value="12">12</option>
                                            <option value="15">15</option>
                                            <option value="Falha">Falha</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-stone-400">Descanso (s)</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][rest_time]'" x-model="exercise.rest_time" class="block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md py-2 px-2">
                                            <option value="">...</option>
                                            <option value="30s">30s</option>
                                            <option value="60s">60s</option>
                                            <option value="90s">90s</option>
                                            <option value="120s">120s</option>
                                            <option value="180s">180s</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2 flex justify-end">
                                        <button type="button" @click="removeExercise(dayIndex, exerciseIndex)" class="text-red-400 hover:text-red-300 text-sm">
                                            Remover
                                        </button>
                                    </div>
                                    <div class="col-span-10">
                                        <label class="block text-xs font-medium text-stone-400">Previa da execucao</label>
                                        <template x-if="exercise.video_url">
                                            <img :src="exercise.video_url" alt="preview" class="w-28 h-20 object-cover rounded border border-teal-900/40 bg-zinc-950/60">
                                        </template>
                                        <template x-if="!exercise.video_url">
                                            <div class="text-xs text-amber-400">Exercicio personalizado sem video demonstrativo.</div>
                                        </template>
                                    </div>
                                    <div class="col-span-2 text-right">
                                        <span class="inline-flex px-2 py-1 text-xs rounded border" :class="exercise.custom_exercise ? 'text-amber-200 bg-amber-700/20 border-amber-600/30' : 'text-teal-200 bg-teal-700/20 border-teal-600/30'">
                                            <span x-text="exercise.custom_exercise ? 'Personalizado' : 'Catalogo'"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addExercise(dayIndex)" class="mt-2 inline-flex items-center px-3 py-1 border border-teal-900/40 shadow-sm text-xs font-medium rounded text-stone-200 bg-zinc-900/70 hover:bg-zinc-800/70 focus:outline-none">
                                + Adicionar Exercício
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6">
                <button type="button" @click="addDay()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-stone-100 bg-teal-700 hover:bg-teal-800 focus:outline-none">
                    + Adicionar Dia de Treino
                </button>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-stone-100 bg-teal-700 hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-600">
                    Salvar Alterações
                </button>
            </div>


        <div x-show="pickerOpen" x-transition style="display:none" class="fixed inset-0 z-50 flex items-start justify-center bg-black/70 p-3 overflow-hidden" @click.self="closeExercisePicker()">
            <div class="w-full overflow-hidden rounded-lg border border-teal-900/40 bg-zinc-900 p-3 flex flex-col shadow-2xl mt-8"
                 style="max-width: 560px; max-height: 70vh;">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-stone-100 font-semibold">Selecionar exercicio do catalogo</h4>
                    <button type="button" @click="closeExercisePicker()" class="text-stone-400 hover:text-stone-200">Fechar</button>
                </div>
                <input type="text" x-model="pickerQuery" placeholder="Buscar exercicio..." class="w-full mb-3 rounded border border-teal-900/40 bg-zinc-950/70 text-stone-100 px-3 py-2">
                <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain space-y-2 pr-1 touch-pan-y"
                     style="max-height: 42vh;"
                     @wheel.stop>
                    <template x-for="item in filteredCatalogExercises()" :key="item.name">
                        <button type="button" @click="applyCatalogExercise(item)" class="w-full text-left rounded border border-teal-900/30 bg-zinc-950/50 p-2 hover:bg-zinc-800/70">
                            <div class="text-stone-100 text-sm font-medium" x-text="item.name"></div>
                        </button>
                    </template>
                </div>
                <div class="mt-3 space-y-2">
                    <p class="text-xs text-amber-300">Opcao personalizado nao tem video demonstrativo.</p>
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-model="pickerCustomName"
                               placeholder="Nome do exercicio personalizado"
                               class="flex-1 rounded border border-amber-600/50 bg-zinc-950/80 text-stone-100 px-3 py-2 text-sm">
                        <button type="button"
                                @click.prevent.stop="applyCustomExercise()"
                                class="px-3 py-2 rounded border border-amber-300 bg-yellow-400 text-black text-sm font-bold hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-300"
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
        'Peito': ['Supino Reto (Barra)', 'Supino Inclinado (Halteres)', 'Crucifixo', 'Crossover', 'Flexão de Braço', 'Peck Deck', 'Supino Declinado'],
        'Costas': ['Puxada Alta', 'Remada Curvada', 'Remada Baixa', 'Barra Fixa', 'Levantamento Terra', 'Pulldown', 'Remada Cavalinho'],
        'Pernas (Completo)': ['Agachamento Livre', 'Leg Press 45', 'Cadeira Extensora', 'Mesa Flexora', 'Stiff', 'Afundo', 'Panturrilha Sentado', 'Panturrilha em Pé'],
        'Ombros': ['Desenvolvimento Militar', 'Elevação Lateral', 'Elevação Frontal', 'Crucifixo Inverso', 'Encolhimento', 'Remada Alta'],
        'Bíceps': ['Rosca Direta', 'Rosca Alternada', 'Rosca Martelo', 'Rosca Scott', 'Rosca Concentrada'],
        'Tríceps': ['Tríceps Corda', 'Tríceps Testa', 'Tríceps Banco', 'Tríceps Francês', 'Tríceps Coice'],
        'Abdômen': ['Abdominal Supra', 'Abdominal Infra', 'Prancha', 'Abdominal Remador'],
        'Quadríceps': ['Agachamento', 'Leg Press', 'Extensora', 'Afundo'],
        'Posterior': ['Mesa Flexora', 'Stiff', 'Cadeira Flexora', 'Elevação Pélvica'],
        'Glúteo': ['Elevação Pélvica', 'Glúteo 4 Apoios', 'Abdução de Quadril']
    };

    function workoutForm(workout, catalogExercises) {
        return {
            name: workout.name,
            goal: workout.goal,
            catalogExercises: Array.isArray(catalogExercises) ? catalogExercises : [],
            pickerOpen: false,
            pickerQuery: '',
            pickerCustomName: '',
            pickerDayIndex: null,
            pickerExerciseIndex: null,
            days: workout.days.map(day => {
                // Tentar extrair "Segunda-feira" e "Peito" da string "Segunda-feira - Peito"
                let parts = day.name.split(' - ');
                let wDay = '';
                let wType = '';
                let wCustom = '';
                
                if (parts.length >= 2) {
                    wDay = parts[0];
                    wType = parts[1];
                    // Verifica se o tipo existe na nossa lista padrão
                    // Se não, assume que é personalizado
                    // Simplificação: Vamos assumir que se não for um dos padrões, é Personalizado
                } else {
                    wDay = day.name; // Fallback
                }

                return {
                    id: day.id,
                    weekDay: wDay,
                    workoutType: wType, 
                    customName: wCustom,
                    exercises: day.exercises.map(ex => ({
                        id: ex.id,
                        name: ex.name,
                        sets: ex.sets,
                        reps: ex.reps,
                        rest_time: ex.rest_time,
                        video_url: ex.video_url ?? '',
                        custom_exercise: ex.video_url ? 0 : 1
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
                if (type.includes('Bíceps')) suggestions.push(...exerciseDB['Bíceps']);
                if (type.includes('Tríceps')) suggestions.push(...exerciseDB['Tríceps']);
                if (type.includes('Quadríceps')) suggestions.push(...exerciseDB['Quadríceps']);
                if (type.includes('Posterior')) suggestions.push(...exerciseDB['Posterior']);
                if (type.includes('Glúteo')) suggestions.push(...exerciseDB['Glúteo']);
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
