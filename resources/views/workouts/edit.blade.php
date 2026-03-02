@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div class="bg-zinc-900/55 overflow-hidden shadow-xl sm:rounded-lg border border-teal-900/30">
    <div class="p-6 sm:px-20 bg-zinc-900/70 border-b border-teal-900/40 flex justify-between items-center">
        <div class="mt-8 text-2xl font-bold text-stone-100">
            Editar Plano de Treino
        </div>
        <a href="{{ route('workouts.show', $workout) }}" class="text-teal-300 hover:text-teal-200">Cancelar</a>
    </div>

    <div class="p-6 sm:px-20 bg-zinc-950/40" x-data="workoutForm({{ json_encode($workout) }})">
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
                                        <label class="block text-xs font-medium text-stone-400">Exercício</label>
                                        <input type="text" 
                                               x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][name]'" 
                                               x-model="exercise.name" 
                                               x-bind:list="'suggestions-edit-' + day.id"
                                               class="block w-full shadow-sm sm:text-sm border-teal-900/40 bg-zinc-950/60 text-stone-100 rounded-md" 
                                               placeholder="Nome" required>
                                               
                                        <datalist x-bind:id="'suggestions-edit-' + day.id">
                                            <template x-for="option in getExercisesForType(day.workoutType)">
                                                <option x-bind:value="option"></option>
                                            </template>
                                        </datalist>
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

    function workoutForm(workout) {
        return {
            name: workout.name,
            goal: workout.goal,
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
                        rest_time: ex.rest_time
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

            addDay() {
                this.days.push({
                    id: Date.now(),
                    weekDay: '',
                    workoutType: '',
                    customName: '',
                    exercises: [{ id: Date.now() + 1, name: '', sets: '', reps: '', rest_time: '' }]
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
                    rest_time: ''
                });
            },
            removeExercise(dayIndex, exerciseIndex) {
                this.days[dayIndex].exercises.splice(exerciseIndex, 1);
            }
        }
    }
</script>
@endsection
