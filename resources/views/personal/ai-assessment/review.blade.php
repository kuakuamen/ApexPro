@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="{ 
    days: {{ json_encode($analysisResult['workout_recommendation']['days'] ?? []) }},
    addDay() {
        this.days.push({ name: 'Novo Treino', exercises: [] });
    },
    removeDay(index) {
        this.days.splice(index, 1);
    },
    addExercise(dayIndex) {
        this.days[dayIndex].exercises.push({ name: '', sets: 3, reps: '10-12', notes: '' });
    },
    removeExercise(dayIndex, exIndex) {
        this.days[dayIndex].exercises.splice(exIndex, 1);
    }
}">
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
                            {{ implode(', ', $analysisResult['suggested_focus']['strengthen'] ?? []) }}
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-white bg-blue-600 px-3 py-1.5 rounded-full border border-blue-500 mb-3 shadow-md">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            Alongar
                        </span>
                        <div class="text-gray-300 leading-loose bg-gray-900/50 p-4 rounded-lg border border-gray-700/50 text-sm">
                            {{ implode(', ', $analysisResult['suggested_focus']['stretch'] ?? []) }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat de Refinamento -->
            <div class="mt-8 border-t border-gray-700 pt-6">
                <h4 class="text-sm font-bold text-gray-300 mb-3 flex items-center gap-2">
                    <span>🤖</span> Ajustar Análise com IA
                </h4>
                <form action="{{ route('personal.ai-assessment.refine') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="goal" value="{{ $request->goal }}">
                    
                    <input type="text" name="feedback" placeholder="Ex: O aluno não tem lordose. Remova isso e ajuste o treino." class="flex-1 bg-gray-900/50 border border-gray-600 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" required>
                    
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-colors">
                        Refinar
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">Escreva o que você quer mudar e a IA vai refazer a análise e o treino.</p>
            </div>
            
            <div class="mt-6 flex items-center text-sm text-gray-400 bg-gray-900/30 p-3 rounded-lg border border-gray-700/50">
                <span class="font-bold text-gray-300 mr-2">Prioridade do Treino:</span> {{ $analysisResult['workout_recommendation']['priority'] ?? 'Geral' }}
            </div>
        </div>
    </div>

    <!-- Sugestão de Treino (Editável) -->
    <form action="{{ route('personal.ai-assessment.store') }}" method="POST" id="workoutForm">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">
        <input type="hidden" name="goal" value="{{ $request->goal }}">

        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-xl font-bold text-white">Treino Sugerido pela IA</h3>
                    <p class="text-sm text-gray-400 mt-1">Revise e edite antes de aprovar.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-64">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nome do Plano</label>
                        <input type="text" name="workout_name" value="Treino IA: {{ $analysisResult['workout_recommendation']['type'] ?? $request->goal }}" class="block w-full bg-gray-900/50 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                    </div>
                    <!-- Botão Gerar PDF -->
                    <button type="button" onclick="generatePDF()" class="inline-flex items-center justify-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-300 bg-gray-800 hover:bg-gray-700 hover:text-white focus:outline-none transition-colors">
                        <svg class="h-4 w-4 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Exportar PDF
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-8">
                <!-- Loop Days (Alpine.js) -->
                <template x-for="(day, dayIndex) in days" :key="dayIndex">
                    <div class="border border-gray-700 rounded-xl p-5 bg-gray-900/30 relative hover:border-gray-600 transition-colors">
                        <!-- Remove Day Button -->
                        <button type="button" @click="removeDay(dayIndex)" class="absolute top-4 right-4 text-gray-500 hover:text-red-400 transition-colors p-1 rounded-md hover:bg-gray-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <div class="mb-6 max-w-md">
                            <label class="block text-xs font-medium text-indigo-400 mb-1 uppercase tracking-wide">Dia do Treino</label>
                            <input type="text" :name="'days[' + dayIndex + '][name]'" x-model="day.name" class="block w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-2 text-white font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                        </div>

                        <!-- Exercises Table -->
                        <div class="overflow-x-auto rounded-lg border border-gray-700">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Exercício</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider w-24">Séries</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider w-28">Reps</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Notas</th>
                                        <th class="px-4 py-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-900/50 divide-y divide-gray-700">
                                    <template x-for="(exercise, exIndex) in day.exercises" :key="exIndex">
                                        <tr class="hover:bg-gray-800/50 transition-colors">
                                            <td class="px-4 py-3">
                                                <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][name]'" x-model="exercise.name" class="block w-full bg-transparent border-0 text-white placeholder-gray-600 focus:ring-0 sm:text-sm p-0" placeholder="Nome do exercício">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][sets]'" x-model="exercise.sets" class="block w-full bg-gray-800 border border-gray-600 rounded text-white text-center focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-1">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][reps]'" x-model="exercise.reps" class="block w-full bg-gray-800 border border-gray-600 rounded text-white text-center focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-1">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][notes]'" x-model="exercise.notes" class="block w-full bg-transparent border-0 text-gray-400 placeholder-gray-700 focus:ring-0 sm:text-sm p-0" placeholder="Observações...">
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="removeExercise(dayIndex, exIndex)" class="text-gray-600 hover:text-red-400 transition-colors">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" @click="addExercise(dayIndex)" class="mt-3 inline-flex items-center text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Adicionar Exercício
                        </button>
                    </div>
                </template>

                <button type="button" @click="addDay()" class="w-full py-4 border-2 border-dashed border-indigo-500/30 rounded-xl text-sm font-bold text-indigo-400 bg-indigo-500/5 hover:bg-indigo-500/10 hover:border-indigo-500/50 hover:text-indigo-300 hover:shadow-lg transition-all duration-300">
                    + Adicionar Dia de Treino
                </button>
            </div>

            <div class="px-6 py-5 bg-gray-800 border-t border-gray-700 flex justify-end">
                <button type="submit" class="inline-flex justify-center items-center py-3 px-8 border border-transparent shadow-lg text-base font-medium rounded-lg text-white bg-teal-500 hover:bg-teal-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-teal-500 transition-all duration-300 transform hover:scale-[1.02]">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Aprovar e Enviar para Aluno
                </button>
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
        form.target = "_blank"; // Abre em nova aba
        form.submit();
        
        // Restaura o formulário
        form.action = originalAction;
        form.target = originalTarget;
    }
</script>
@endsection
