@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

@php
    $canUseDietAi = $canUseDietAi ?? false;
    $goalOptions = [
        'Perder gordura',
        'Ganhar massa',
        'Saude geral',
        'Definicao',
        'Performance esportiva',
    ];
    $diseaseOptions = ['Diabetes', 'Hipertensao', 'Colesterol alto', 'Tireoide', 'Nenhuma', 'Outra'];
    $restrictionOptions = ['Lactose', 'Gluten', 'Frutose', 'Nenhuma', 'Outra'];
    $allergyOptions = ['Amendoim', 'Frutos do mar', 'Ovos', 'Nozes', 'Soja', 'Nenhuma', 'Outra'];
    $eatsOutOptions = ['Todos os dias', '3-4x por semana', '1-2x por semana', 'Raramente'];
    $alcoholOptions = ['Nao', 'Socialmente', '1-2x por semana', 'Frequentemente'];
    $foodStyleOptions = ['Sem restricao', 'Vegetariano', 'Vegano', 'Low carb', 'Cetogenico', 'Outro'];
    $trainingPeriodOptions = ['Manha', 'Tarde', 'Noite', 'Varia'];
    $preWorkoutOptions = ['Sim, sempre', 'As vezes', 'Nao, treino em jejum'];
    $postWorkoutOptions = ['Sim, sempre', 'As vezes', 'Nao costumo'];
    $emotionalEatingOptions = ['Nunca', 'Raramente', 'As vezes', 'Frequentemente'];
    $dietHistoryOptions = ['Nunca tentei', 'Tentei e mantive', 'Tentei mas nao consegui manter'];

    $rawMeals = old('meals');
    if (!is_array($rawMeals) || count($rawMeals) === 0) {
        $rawMeals = [[
            'name' => '',
            'time' => '',
            'foods' => [[
                'name' => '',
                'quantity' => '',
                'calories' => '',
                'observation' => '',
            ]],
        ]];
    }

    $initialState = [
        'student_id' => old('student_id', ''),
        'name' => old('name', ''),
        'goal' => old('goal', ''),
        'initial_kcal' => old('initial_kcal', ''),
        'anamnesis' => is_array($initialAnamnesis ?? null) ? $initialAnamnesis : [],
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-8 pt-4">
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden"
         x-data='dietForm(@json($rawMeals), @json($initialState), @json(route('diets.generate-ai')), @json(csrf_token()), @json($canUseDietAi), @json(array_values($goalOptions)), @json($studentAnamnesisSeed ?? []))'>
        <div class="px-6 py-4 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white">Criar Novo Plano Alimentar</h1>
            <p class="mt-1 text-gray-400">Monte o plano alimentar do aluno.</p>
        </div>

        <div class="p-6">
            <form action="{{ route('diets.store') }}" method="POST" class="space-y-8">
                @csrf

                @if($canUseDietAi)
                    <div class="rounded-xl border border-indigo-500/30 bg-indigo-500/10 p-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-indigo-200">Geracao com IA (revisao manual obrigatoria)</p>
                                <p class="text-xs text-indigo-300/90 mt-1">A IA sugere o plano alimentar. Revise e ajuste tudo antes de salvar.</p>
                            </div>
                            <button type="button"
                                    @click="generateWithAi()"
                                    :disabled="generatingAi"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                                <svg x-show="!generatingAi" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <svg x-show="generatingAi" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                <span x-text="generatingAi ? 'Gerando...' : 'Gerar dieta com IA'"></span>
                            </button>
                        </div>

                        <p x-show="aiError" x-text="aiError" class="mt-3 text-sm text-red-300"></p>
                        <p x-show="aiSuccess" x-text="aiSuccess" class="mt-3 text-sm text-emerald-300"></p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Aluno</label>
                        <select name="student_id"
                                x-model="studentId"
                                class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                required>
                            <option value="" class="bg-gray-700">Selecione um aluno...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" class="bg-gray-700">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nome da Dieta</label>
                        <input type="text"
                               name="name"
                               x-model="planName"
                               class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Ex: Dieta de Cutting"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Objetivo</label>
                        @if($canUseDietAi)
                            <select x-model="goalSelect"
                                    @change="syncGoalValue()"
                                    class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($goalOptions as $goalOption)
                                    <option value="{{ $goalOption }}" class="bg-gray-700">{{ $goalOption }}</option>
                                @endforeach
                                <option value="__custom__" class="bg-gray-700">Outro (digitar manualmente)</option>
                            </select>

                            <div class="mt-3" x-show="goalSelect === '__custom__'" x-cloak>
                                <input type="text"
                                       x-model="goalCustom"
                                       @input="syncGoalValue()"
                                       class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                       placeholder="Digite o objetivo">
                            </div>

                            <input type="hidden" name="goal" :value="goal">
                        @else
                            <input type="text"
                                   name="goal"
                                   x-model="goal"
                                   class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="Ex: Perda de gordura">
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Kcal Dia (opcional)</label>
                        <input type="number"
                               name="initial_kcal"
                               x-model="initialKcal"
                               min="600"
                               max="10000"
                               step="1"
                               class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Ex: 2200">
                    </div>
                </div>

                <div class="bg-gray-900/40 border border-gray-700 rounded-xl p-5 space-y-5">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Anamnese Nutricional</h3>
                        <p class="text-xs text-gray-400 mt-1">Preencha para melhorar a assertividade da dieta da IA e manter historico do aluno.</p>
                    </div>

                    <input type="hidden" name="anamnesis[main_goal]" :value="goal">
                    <input type="hidden" name="anamnesis[kcal_day]" :value="initialKcal">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Peso atual (kg)</label>
                            <input type="number" step="0.1" min="20" max="400" name="anamnesis[weight_kg]" x-model="anamnesis.weight_kg" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: 72.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Altura (cm)</label>
                            <input type="number" step="0.1" min="80" max="260" name="anamnesis[height_cm]" x-model="anamnesis.height_cm" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: 172">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Peso desejado (kg)</label>
                            <input type="number" step="0.1" min="20" max="400" name="anamnesis[target_weight_kg]" x-model="anamnesis.target_weight_kg" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: 68">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-300">Doenca diagnosticada</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($diseaseOptions as $option)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-200">
                                        <input type="checkbox" class="rounded bg-gray-800 border-gray-600 text-indigo-500 focus:ring-indigo-500" name="anamnesis[diagnosed_conditions][]" value="{{ $option }}" x-model="anamnesis.diagnosed_conditions">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="text" name="anamnesis[diagnosed_conditions_other]" x-model="anamnesis.diagnosed_conditions_other" x-show="anamnesis.diagnosed_conditions.includes('Outra')" x-cloak class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Qual outra doenca?">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Medicamento continuo</label>
                            <textarea name="anamnesis[continuous_medication]" x-model="anamnesis.continuous_medication" rows="4" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Se sim, qual?"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-300">Restricao/intolerancia alimentar</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($restrictionOptions as $option)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-200">
                                        <input type="checkbox" class="rounded bg-gray-800 border-gray-600 text-indigo-500 focus:ring-indigo-500" name="anamnesis[food_restrictions][]" value="{{ $option }}" x-model="anamnesis.food_restrictions">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="text" name="anamnesis[food_restrictions_other]" x-model="anamnesis.food_restrictions_other" x-show="anamnesis.food_restrictions.includes('Outra')" x-cloak class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Qual outra restricao?">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-300">Alergia alimentar</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($allergyOptions as $option)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-200">
                                        <input type="checkbox" class="rounded bg-gray-800 border-gray-600 text-indigo-500 focus:ring-indigo-500" name="anamnesis[food_allergies][]" value="{{ $option }}" x-model="anamnesis.food_allergies">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="text" name="anamnesis[food_allergies_other]" x-model="anamnesis.food_allergies_other" x-show="anamnesis.food_allergies.includes('Outra')" x-cloak class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Qual outra alergia?">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Refeicoes por dia</label>
                            <input type="number" min="1" max="15" name="anamnesis[meals_per_day]" x-model="anamnesis.meals_per_day" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: 5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Agua por dia (litros)</label>
                            <input type="number" step="0.1" min="0" max="20" name="anamnesis[water_liters_per_day]" x-model="anamnesis.water_liters_per_day" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: 2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Come fora com frequencia?</label>
                            <select name="anamnesis[eats_out_frequency]" x-model="anamnesis.eats_out_frequency" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($eatsOutOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Consome bebida alcoolica?</label>
                            <select name="anamnesis[alcohol_frequency]" x-model="anamnesis.alcohol_frequency" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($alcoholOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Estilo alimentar</label>
                            <select name="anamnesis[food_style]" x-model="anamnesis.food_style" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($foodStyleOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="anamnesis[food_style_other]" x-model="anamnesis.food_style_other" x-show="anamnesis.food_style === 'Outro'" x-cloak class="block w-full mt-3 bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Qual outro estilo?">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Alimentos que nao consegue comer</label>
                            <textarea name="anamnesis[disliked_foods]" x-model="anamnesis.disliked_foods" rows="3" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: figado, brocolis..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Alimentos favoritos</label>
                            <textarea name="anamnesis[favorite_foods]" x-model="anamnesis.favorite_foods" rows="3" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: ovos, arroz, frango..."></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Periodo de treino</label>
                            <select name="anamnesis[training_period]" x-model="anamnesis.training_period" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($trainingPeriodOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Come algo antes do treino?</label>
                            <select name="anamnesis[pre_workout_meal]" x-model="anamnesis.pre_workout_meal" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($preWorkoutOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Come algo apos treino?</label>
                            <select name="anamnesis[post_workout_meal]" x-model="anamnesis.post_workout_meal" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($postWorkoutOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Come por ansiedade/estresse?</label>
                            <select name="anamnesis[emotional_eating]" x-model="anamnesis.emotional_eating" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($emotionalEatingOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Historico com dieta</label>
                            <select name="anamnesis[diet_history]" x-model="anamnesis.diet_history" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($dietHistoryOptions as $option)
                                    <option value="{{ $option }}" class="bg-gray-700">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Horario com mais fome</label>
                            <input type="time" name="anamnesis[most_hungry_time]" x-model="anamnesis.most_hungry_time" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Horario com menos fome</label>
                            <input type="time" name="anamnesis[least_hungry_time]" x-model="anamnesis.least_hungry_time" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-700">

                <div class="space-y-6">
                    <template x-for="(meal, mealIndex) in meals" :key="meal.id">
                        <div class="bg-gray-900/40 border border-gray-700 rounded-xl p-5 relative">
                            <button type="button" @click="removeMeal(mealIndex)" class="absolute top-4 right-4 text-red-400 hover:text-red-300 transition-colors" x-show="meals.length > 1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <h4 class="text-lg font-semibold text-teal-300 mb-4">Refeicao <span x-text="mealIndex + 1"></span></h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Nome da Refeicao</label>
                                    <input type="text" x-bind:name="'meals[' + mealIndex + '][name]'" x-model="meal.name" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Cafe da manha" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Horario</label>
                                    <input type="time" x-bind:name="'meals[' + mealIndex + '][time]'" x-model="meal.time" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                </div>
                            </div>

                            <div class="space-y-3 pl-3 border-l-2 border-teal-500/30">
                                <template x-for="(food, foodIndex) in meal.foods" :key="food.id">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-gray-800/60 border border-gray-700 rounded-lg p-3">
                                        <div class="md:col-span-4">
                                            <label class="block text-xs font-medium text-gray-400 mb-1">Alimento</label>
                                            <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][name]'" x-model="food.name" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Arroz integral" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-400 mb-1">Qtd</label>
                                            <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][quantity]'" x-model="food.quantity" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: 100g">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-400 mb-1">Kcal</label>
                                            <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][calories]'" x-model="food.calories" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: 120">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-400 mb-1">Obs</label>
                                            <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][observation]'" x-model="food.observation" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Opcional">
                                        </div>
                                        <div class="md:col-span-1 flex justify-end">
                                            <button type="button" @click="removeFood(mealIndex, foodIndex)" class="text-red-400 hover:text-red-300 text-sm font-semibold transition-colors">X</button>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="addFood(mealIndex)" class="mt-1 inline-flex items-center px-4 py-2 border border-teal-500/40 rounded-lg text-sm font-medium text-teal-200 bg-teal-900/20 hover:bg-teal-900/35 transition-colors">
                                    + Adicionar Alimento
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div>
                    <button type="button" @click="addMeal()" class="inline-flex items-center px-4 py-2 border border-cyan-500/40 rounded-lg text-sm font-medium text-cyan-200 bg-cyan-900/20 hover:bg-cyan-900/35 transition-colors">
                        + Adicionar Refeicao
                    </button>
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-gray-700">
                    <a href="{{ route('diets.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-600 rounded-lg text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/25">
                        Salvar Dieta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function dietForm(initialMeals, initialState, generateAiUrl, csrfToken, canUseDietAi, goalOptions, studentAnamnesisSeed) {
    const nowBase = Date.now();

    return {
        canUseDietAi: !!canUseDietAi,
        goalOptions: Array.isArray(goalOptions) ? goalOptions : [],
        studentAnamnesisSeed: (studentAnamnesisSeed && typeof studentAnamnesisSeed === 'object') ? studentAnamnesisSeed : {},
        generateAiUrl,
        csrfToken,
        generatingAi: false,
        aiError: '',
        aiSuccess: '',

        studentId: initialState?.student_id ? String(initialState.student_id) : '',
        planName: initialState?.name || '',
        goal: initialState?.goal || '',
        goalSelect: '',
        goalCustom: '',
        initialKcal: initialState?.initial_kcal || '',
        anamnesis: thisNormalizeAnamnesis(initialState?.anamnesis || {}),

        meals: Array.isArray(initialMeals) && initialMeals.length
            ? initialMeals.map((meal, mealIndex) => thisNormalizeMeal(meal, mealIndex, nowBase))
            : [thisDefaultMeal(nowBase)],

        init() {
            this.applyGoal(this.goal);
            this.syncAnamnesisGoalAndKcal();
            this.$watch('studentId', (value) => this.applyStudentAnamnesisSeed(value));
        },

        syncGoalValue() {
            if (!this.canUseDietAi) {
                this.syncAnamnesisGoalAndKcal();
                return;
            }

            if (this.goalSelect === '__custom__') {
                this.goal = this.goalCustom || '';
                this.syncAnamnesisGoalAndKcal();
                return;
            }

            this.goal = this.goalSelect || '';
            this.goalCustom = '';
            this.syncAnamnesisGoalAndKcal();
        },

        applyGoal(value) {
            this.goal = value || '';
            if (!this.canUseDietAi) {
                this.syncAnamnesisGoalAndKcal();
                return;
            }

            if (!this.goal) {
                this.goalSelect = '';
                this.goalCustom = '';
                this.syncAnamnesisGoalAndKcal();
                return;
            }

            if (this.goalOptions.includes(this.goal)) {
                this.goalSelect = this.goal;
                this.goalCustom = '';
                this.syncAnamnesisGoalAndKcal();
                return;
            }

            this.goalSelect = '__custom__';
            this.goalCustom = this.goal;
            this.syncAnamnesisGoalAndKcal();
        },

        syncAnamnesisGoalAndKcal() {
            this.anamnesis.main_goal = this.goal || '';
            this.anamnesis.kcal_day = this.initialKcal ? String(this.initialKcal) : '';
        },

        applyStudentAnamnesisSeed(studentId) {
            const key = studentId ? String(studentId) : '';
            const seed = key && this.studentAnamnesisSeed[key] ? this.studentAnamnesisSeed[key] : null;

            if (!seed) {
                this.anamnesis = thisNormalizeAnamnesis({
                    main_goal: this.goal || '',
                    kcal_day: this.initialKcal || '',
                });
                return;
            }

            this.anamnesis = thisNormalizeAnamnesis(seed);

            if (!this.goal && this.anamnesis.main_goal) {
                this.applyGoal(this.anamnesis.main_goal);
            } else {
                this.syncAnamnesisGoalAndKcal();
            }

            if (!this.initialKcal && this.anamnesis.kcal_day) {
                this.initialKcal = this.anamnesis.kcal_day;
            }
        },

        async generateWithAi() {
            if (!this.canUseDietAi) {
                return;
            }

            this.aiError = '';
            this.aiSuccess = '';

            if (!this.studentId) {
                this.aiError = 'Selecione um aluno antes de gerar com IA.';
                return;
            }

            this.syncGoalValue();
            this.syncAnamnesisGoalAndKcal();
            this.generatingAi = true;

            try {
                const response = await fetch(this.generateAiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({
                        student_id: this.studentId,
                        goal: this.goal,
                        initial_kcal: this.initialKcal,
                        anamnesis: this.anamnesis,
                    }),
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Nao foi possivel gerar dieta com IA.');
                }

                if (data.name) this.planName = data.name;
                if (data.goal) this.applyGoal(data.goal);
                if (data.daily_kcal_target) this.initialKcal = String(data.daily_kcal_target);
                this.syncAnamnesisGoalAndKcal();

                if (Array.isArray(data.meals) && data.meals.length) {
                    const base = Date.now();
                    this.meals = data.meals.map((meal, index) => thisNormalizeMeal(meal, index, base));
                }

                this.aiSuccess = 'Dieta gerada com IA. Revise e ajuste antes de salvar.';
            } catch (error) {
                this.aiError = error?.message || 'Nao foi possivel gerar dieta com IA.';
            } finally {
                this.generatingAi = false;
            }
        },

        addMeal() {
            this.meals.push(thisDefaultMeal(Date.now()));
        },

        removeMeal(index) {
            this.meals.splice(index, 1);
        },

        addFood(mealIndex) {
            this.meals[mealIndex].foods.push(thisDefaultFood(Date.now()));
        },

        removeFood(mealIndex, foodIndex) {
            this.meals[mealIndex].foods.splice(foodIndex, 1);
        },
    };
}

function thisDefaultFood(seed) {
    return {
        id: seed + Math.random(),
        name: '',
        quantity: '',
        calories: '',
        observation: '',
    };
}

function thisDefaultMeal(seed) {
    return {
        id: seed + Math.random(),
        name: '',
        time: '',
        foods: [thisDefaultFood(seed + 1)],
    };
}

function thisNormalizeMeal(meal, index, seed) {
    const time = typeof meal?.time === 'string' && /^\d{2}:\d{2}$/.test(meal.time) ? meal.time : '';
    const foods = Array.isArray(meal?.foods) && meal.foods.length
        ? meal.foods.map((food, foodIndex) => ({
            id: seed + index + foodIndex + Math.random(),
            name: food?.name || '',
            quantity: food?.quantity || '',
            calories: food?.calories || '',
            observation: food?.observation || '',
        }))
        : [thisDefaultFood(seed + index + 10)];

    return {
        id: seed + index + Math.random(),
        name: meal?.name || '',
        time,
        foods,
    };
}

function thisNormalizeAnamnesis(data) {
    const toArray = (value) => Array.isArray(value)
        ? value.map((item) => String(item).trim()).filter(Boolean)
        : [];

    const toStringValue = (value) => value === null || value === undefined ? '' : String(value);

    return {
        main_goal: toStringValue(data.main_goal || ''),
        weight_kg: toStringValue(data.weight_kg || ''),
        height_cm: toStringValue(data.height_cm || ''),
        target_weight_kg: toStringValue(data.target_weight_kg || ''),
        diagnosed_conditions: toArray(data.diagnosed_conditions),
        diagnosed_conditions_other: toStringValue(data.diagnosed_conditions_other || ''),
        continuous_medication: toStringValue(data.continuous_medication || ''),
        food_restrictions: toArray(data.food_restrictions),
        food_restrictions_other: toStringValue(data.food_restrictions_other || ''),
        food_allergies: toArray(data.food_allergies),
        food_allergies_other: toStringValue(data.food_allergies_other || ''),
        meals_per_day: toStringValue(data.meals_per_day || ''),
        water_liters_per_day: toStringValue(data.water_liters_per_day || ''),
        eats_out_frequency: toStringValue(data.eats_out_frequency || ''),
        alcohol_frequency: toStringValue(data.alcohol_frequency || ''),
        disliked_foods: toStringValue(data.disliked_foods || ''),
        favorite_foods: toStringValue(data.favorite_foods || ''),
        food_style: toStringValue(data.food_style || ''),
        food_style_other: toStringValue(data.food_style_other || ''),
        training_period: toStringValue(data.training_period || ''),
        pre_workout_meal: toStringValue(data.pre_workout_meal || ''),
        post_workout_meal: toStringValue(data.post_workout_meal || ''),
        emotional_eating: toStringValue(data.emotional_eating || ''),
        diet_history: toStringValue(data.diet_history || ''),
        most_hungry_time: toStringValue(data.most_hungry_time || ''),
        least_hungry_time: toStringValue(data.least_hungry_time || ''),
        kcal_day: toStringValue(data.kcal_day || ''),
    };
}
</script>
@endsection
