@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div class="max-w-7xl mx-auto space-y-8">
    <!-- Cabeçalho -->
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white">Criar Novo Plano de Treino</h1>
            <p class="mt-1 text-gray-400">Monte um plano personalizado para seu aluno</p>
        </div>
        
        <div class="p-6" x-data="workoutForm()">
        <form action="{{ route('workouts.store') }}" method="POST">
            @csrf

            <!-- Dados Básicos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Aluno</label>
                    <select name="student_id" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                        <option value="" class="bg-gray-700">Selecione um aluno...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" class="bg-gray-700" {{ isset($selectedStudentId) && $selectedStudentId == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nome do Plano</label>
                    <input type="text" name="name" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Hipertrofia Iniciante" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Objetivo Principal</label>
                    <input type="text" name="goal" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Ganho de massa muscular">
                </div>
            </div>

            <hr class="my-8 border-gray-300">

            <!-- Dias de Treino -->
            <div class="space-y-8">
                <template x-for="(day, dayIndex) in days" :key="day.id">
                    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6 relative shadow-lg">
                        <!-- Botão Remover Dia -->
                        <button type="button" @click="removeDay(dayIndex)" class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition-colors" x-show="days.length > 1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <h4 class="text-lg font-bold text-white mb-4">Dia <span x-text="dayIndex + 1"></span></h4>

                        <!-- Seleção de Dia e Tipo de Treino -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Dia da Semana</label>
                                <select x-model="day.weekDay" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <option value="" class="bg-gray-700">Selecione...</option>
                                    <option value="Segunda-feira" class="bg-gray-700">Segunda-feira</option>
                                    <option value="Terça-feira" class="bg-gray-700">Terça-feira</option>
                                    <option value="Quarta-feira" class="bg-gray-700">Quarta-feira</option>
                                    <option value="Quinta-feira" class="bg-gray-700">Quinta-feira</option>
                                    <option value="Sexta-feira" class="bg-gray-700">Sexta-feira</option>
                                    <option value="Sábado" class="bg-gray-700">Sábado</option>
                                    <option value="Domingo" class="bg-gray-700">Domingo</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Treino</label>
                                <select x-model="day.workoutType" @change="updateDayName(dayIndex)" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <option value="" class="bg-gray-700">Selecione...</option>
                                    <optgroup label="Divisões Clássicas" class="bg-gray-700 text-gray-300">
                                        <option value="Peito" class="bg-gray-700">Peito</option>
                                        <option value="Costas" class="bg-gray-700">Costas</option>
                                        <option value="Pernas (Completo)" class="bg-gray-700">Pernas (Completo)</option>
                                        <option value="Ombros" class="bg-gray-700">Ombros</option>
                                        <option value="Braços (Bíceps + Tríceps)" class="bg-gray-700">Braços (Bíceps + Tríceps)</option>
                                    </optgroup>
                                    <optgroup label="Combinações" class="bg-gray-700 text-gray-300">
                                        <option value="Peito + Tríceps" class="bg-gray-700">Peito + Tríceps</option>
                                        <option value="Costas + Bíceps" class="bg-gray-700">Costas + Bíceps</option>
                                        <option value="Peito + Ombros" class="bg-gray-700">Peito + Ombros</option>
                                        <option value="Quadríceps + Panturrilha" class="bg-gray-700">Quadríceps + Panturrilha</option>
                                        <option value="Posterior + Glúteo" class="bg-gray-700">Posterior + Glúteo</option>
                                        <option value="Superior Completo" class="bg-gray-700">Superior Completo</option>
                                        <option value="Inferior Completo" class="bg-gray-700">Inferior Completo</option>
                                        <option value="Full Body" class="bg-gray-700">Full Body</option>
                                    </optgroup>
                                    <option value="Personalizado" class="bg-gray-700">Outro (Personalizado)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Campo Oculto para o Backend (Concatena Dia + Treino) -->
                        <input type="hidden" x-bind:name="'days[' + dayIndex + '][name]'" x-bind:value="day.weekDay + ' - ' + (day.workoutType === 'Personalizado' ? day.customName : day.workoutType)">

                        <!-- Campo Personalizado (só aparece se escolher "Personalizado") -->
                        <div x-show="day.workoutType === 'Personalizado'" class="mb-6">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Nome do Treino (Personalizado)</label>
                            <input type="text" x-model="day.customName" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>

                        <!-- Lista de Exercícios -->
                        <div class="space-y-4 pl-4 border-l-2 border-indigo-500/50">
                            <template x-for="(exercise, exerciseIndex) in day.exercises" :key="exercise.id">
                                <div class="grid grid-cols-12 gap-4 items-end bg-gray-800/50 backdrop-blur-sm p-4 rounded-lg border border-gray-700">
                                    <div class="col-span-4">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Exercício</label>
                                        <input type="text" 
                                               x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][name]'" 
                                               x-model="exercise.name" 
                                               x-bind:list="'suggestions-' + day.id"
                                               class="block w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                                               placeholder="Selecione ou digite" required>
                                               
                                        <!-- Datalist Dinâmico por Dia -->
                                        <datalist x-bind:id="'suggestions-' + day.id">
                                            <template x-for="option in getExercisesForType(day.workoutType)">
                                                <option x-bind:value="option"></option>
                                            </template>
                                        </datalist>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Séries</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][sets]'" x-model="exercise.sets" class="block w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                            <option value="" class="bg-gray-700">...</option>
                                            <option value="1" class="bg-gray-700">1</option>
                                            <option value="2" class="bg-gray-700">2</option>
                                            <option value="3" class="bg-gray-700">3</option>
                                            <option value="4" class="bg-gray-700">4</option>
                                            <option value="5" class="bg-gray-700">5</option>
                                            <option value="6" class="bg-gray-700">6</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Reps</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][reps]'" x-model="exercise.reps" class="block w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                            <option value="" class="bg-gray-700">...</option>
                                            <option value="6" class="bg-gray-700">6</option>
                                            <option value="8" class="bg-gray-700">8</option>
                                            <option value="10" class="bg-gray-700">10</option>
                                            <option value="12" class="bg-gray-700">12</option>
                                            <option value="15" class="bg-gray-700">15</option>
                                            <option value="Falha" class="bg-gray-700">Falha</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Descanso (s)</label>
                                        <select x-bind:name="'days[' + dayIndex + '][exercises][' + exerciseIndex + '][rest_time]'" x-model="exercise.rest_time" class="block w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                            <option value="" class="bg-gray-700">...</option>
                                            <option value="30s" class="bg-gray-700">30s</option>
                                            <option value="60s" class="bg-gray-700">60s</option>
                                            <option value="90s" class="bg-gray-700">90s</option>
                                            <option value="120s" class="bg-gray-700">120s</option>
                                            <option value="180s" class="bg-gray-700">180s</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2 flex justify-end">
                                        <button type="button" @click="removeExercise(dayIndex, exerciseIndex)" class="text-red-400 hover:text-red-300 text-sm transition-colors">
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addExercise(dayIndex)" class="mt-2 inline-flex items-center px-4 py-2 border border-indigo-600 shadow-sm text-sm font-medium rounded-lg text-indigo-300 bg-gray-800/50 hover:bg-gray-700/70 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                + Adicionar Exercício
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6">
                <button type="button" @click="addDay()" class="inline-flex items-center px-6 py-3 border border-indigo-600 shadow-sm text-sm font-medium rounded-lg text-indigo-300 bg-gray-800/50 hover:bg-gray-700/70 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                    + Adicionar Dia de Treino
                </button>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-green-500 transition-all duration-300">
                    Salvar Treino Completo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Banco de Dados de Exercícios (Hardcoded para agilidade)
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

    function workoutForm() {
        return {
            days: [
                {
                    id: Date.now(),
                    weekDay: '',
                    workoutType: '',
                    customName: '',
                    exercises: [{ id: Date.now() + 1, name: '', sets: '', reps: '', rest_time: '' }]
                }
            ],
            
            // Retorna a lista de exercícios baseada no tipo selecionado
            getExercisesForType(type) {
                if (!type || type === 'Personalizado') return Object.values(exerciseDB).flat().sort();
                
                let suggestions = [];
                
                // Mapeamento de Tipos Compostos
                if (type.includes('Peito')) suggestions.push(...exerciseDB['Peito']);
                if (type.includes('Costas')) suggestions.push(...exerciseDB['Costas']);
                if (type.includes('Pernas')) suggestions.push(...exerciseDB['Pernas (Completo)']);
                if (type.includes('Ombros')) suggestions.push(...exerciseDB['Ombros']);
                if (type.includes('Bíceps')) suggestions.push(...exerciseDB['Bíceps']);
                if (type.includes('Tríceps')) suggestions.push(...exerciseDB['Tríceps']);
                if (type.includes('Quadríceps')) suggestions.push(...exerciseDB['Quadríceps']);
                if (type.includes('Posterior')) suggestions.push(...exerciseDB['Posterior']);
                if (type.includes('Glúteo')) suggestions.push(...exerciseDB['Glúteo']);
                
                // Remove duplicatas e retorna
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
            },
            updateDayName(dayIndex) {
                // Lógica opcional se quiser atualizar algo ao mudar o tipo
            }
        }
    }
</script>
@endsection
