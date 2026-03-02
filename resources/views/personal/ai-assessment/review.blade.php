@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
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
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Resultado da Análise IA</h2>
        <a href="{{ route('personal.ai-assessment.index') }}" class="text-gray-500 hover:text-gray-700">
            &larr; Voltar
        </a>
    </div>

    <!-- Análise Postural (Visual) -->
    <div class="bg-white shadow rounded-lg overflow-hidden border-l-4 border-indigo-500">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Análise Biomecânica
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Desvios Identificados -->
                <div class="bg-red-50 p-4 rounded-md">
                    <h4 class="font-bold text-red-800 mb-2">Desvios Identificados:</h4>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        <li><strong>Lordose:</strong> {{ $analysisResult['posture_analysis']['lordosis'] ?? 'Não detectado' }}</li>
                        <li><strong>Escoliose:</strong> {{ $analysisResult['posture_analysis']['scoliosis'] ?? 'Não detectado' }}</li>
                        <li><strong>Ombros:</strong> {{ $analysisResult['posture_analysis']['shoulders'] ?? 'Neutro' }}</li>
                        <li><strong>Cabeça:</strong> {{ $analysisResult['posture_analysis']['head_position'] ?? 'Neutro' }}</li>
                        <li><strong>Joelhos:</strong> {{ $analysisResult['posture_analysis']['knees'] ?? 'Neutro' }}</li>
                    </ul>
                </div>

                <!-- Foco Sugerido -->
                <div class="bg-green-50 p-4 rounded-md">
                    <h4 class="font-bold text-green-800 mb-2">Estratégia Corretiva:</h4>
                    <div class="mb-2">
                        <span class="text-xs font-bold uppercase text-green-600">Fortalecer:</span>
                        <p class="text-sm text-green-800">{{ implode(', ', $analysisResult['suggested_focus']['strengthen'] ?? []) }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-blue-600">Alongar:</span>
                        <p class="text-sm text-blue-800">{{ implode(', ', $analysisResult['suggested_focus']['stretch'] ?? []) }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Chat de Refinamento -->
            <div class="mt-6 border-t border-gray-200 pt-4">
                <h4 class="text-sm font-bold text-gray-700 mb-2">🤖 Ajustar Análise com IA</h4>
                <form action="{{ route('personal.ai-assessment.refine') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="goal" value="{{ $request->goal }}">
                    
                    <input type="text" name="feedback" placeholder="Ex: O aluno não tem lordose. Remova isso e ajuste o treino." class="flex-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                    
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Refinar
                    </button>
                </form>
                <p class="text-xs text-gray-400 mt-1">Escreva o que você quer mudar e a IA vai refazer a análise e o treino.</p>
            </div>
            
            <div class="mt-4 text-sm text-gray-500">
                <strong>Prioridade do Treino:</strong> {{ $analysisResult['workout_recommendation']['priority'] ?? 'Geral' }}
            </div>
        </div>
    </div>

    <!-- Sugestão de Treino (Editável) -->
    <form action="{{ route('personal.ai-assessment.store') }}" method="POST" id="workoutForm">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">
        <input type="hidden" name="goal" value="{{ $request->goal }}">

        <div class="bg-white shadow rounded-lg overflow-hidden mt-6">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Treino Sugerido pela IA</h3>
                    <p class="text-sm text-gray-500">Revise e edite antes de aprovar.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-64">
                        <label class="block text-xs font-medium text-gray-500">Nome do Plano</label>
                        <input type="text" name="workout_name" value="Treino IA: {{ $analysisResult['workout_recommendation']['type'] ?? $request->goal }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <!-- Botão Gerar PDF -->
                    <button type="button" onclick="generatePDF()" class="mt-5 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        <svg class="h-4 w-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Exportar Laudo PDF
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-8">
                <!-- Loop Days (Alpine.js) -->
                <template x-for="(day, dayIndex) in days" :key="dayIndex">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 relative">
                        <!-- Remove Day Button -->
                        <button type="button" @click="removeDay(dayIndex)" class="absolute top-4 right-4 text-red-400 hover:text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <div class="mb-4 w-1/2">
                            <label class="block text-sm font-medium text-gray-700">Nome do Dia (ex: Treino A)</label>
                            <input type="text" :name="'days[' + dayIndex + '][name]'" x-model="day.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Exercises Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Exercício</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20">Séries</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Reps</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
                                        <th class="px-3 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(exercise, exIndex) in day.exercises" :key="exIndex">
                                        <tr>
                                            <td class="px-3 py-2">
                                                <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][name]'" x-model="exercise.name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Nome do exercício">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][sets]'" x-model="exercise.sets" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][reps]'" x-model="exercise.reps" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" :name="'days[' + dayIndex + '][exercises][' + exIndex + '][notes]'" x-model="exercise.notes" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Técnica avançada...">
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <button type="button" @click="removeExercise(dayIndex, exIndex)" class="text-red-600 hover:text-red-900">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <button type="button" @click="addExercise(dayIndex)" class="mt-2 text-xs font-medium text-indigo-600 hover:text-indigo-900">
                                + Adicionar Exercício
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addDay()" class="w-full py-2 border-2 border-dashed border-gray-300 rounded-md text-sm font-medium text-gray-500 hover:border-indigo-500 hover:text-indigo-600 transition-colors">
                    + Adicionar Dia de Treino
                </button>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-105">
                    ✅ Aprovar e Enviar para Aluno
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function generatePDF() {
        // Coleta os dados atuais do formulário e da análise para enviar para a rota de PDF
        // Como é complexo enviar dados dinâmicos do Alpine via GET, vamos submeter para uma rota que gera o PDF
        
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
