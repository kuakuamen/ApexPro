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
         x-data='dietForm(@json($rawMeals), @json($initialState), @json(route('diets.generate-ai')), @json(csrf_token()), @json($canUseDietAi), @json(array_values($goalOptions)), @json($studentAnamnesisSeed ?? []), @json(route('diets.anamnesis-pdf')), @json($studentContactMap ?? []), @json($anamnesisShareLinkMap ?? []))'>
        <div class="px-6 py-4 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white">Criar Novo Plano Alimentar</h1>
            <p class="mt-1 text-gray-400">Monte o plano alimentar do aluno.</p>
        </div>

        <div class="p-6">
            <form action="{{ route('diets.store') }}" method="POST" class="space-y-8">
                @csrf

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
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Anamnese Nutricional</h3>
                            <p class="text-xs text-gray-400 mt-1">Preencha para melhorar a assertividade da dieta da IA e manter historico do aluno.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="#"
                               @click.prevent="downloadAnamnesisPdf()"
                               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                               :class="canDownloadAnamnesisPdf()
                                    ? 'border-indigo-500/35 bg-indigo-500/10 text-indigo-200 hover:bg-indigo-500/20'
                                    : 'border-gray-600/60 bg-gray-800/50 text-gray-500 cursor-not-allowed'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2h-3.172a2 2 0 01-1.414-.586l-.828-.828A2 2 0 0011.172 2H6a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Baixar PDF
                            </a>

                            <button type="button"
                                    @click="copyAnamnesisLink()"
                                    :disabled="!canCopyAnamnesisLink()"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                    :class="canCopyAnamnesisLink()
                                        ? 'border-cyan-500/35 bg-cyan-500/10 text-cyan-200 hover:bg-cyan-500/20'
                                        : 'border-gray-600/60 bg-gray-800/50 text-gray-500 cursor-not-allowed'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2M10 18h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Copiar link
                            </button>

                            <button type="button"
                                    @click="openStudentWhatsapp()"
                                    :disabled="!canOpenStudentWhatsapp()"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                    :class="canOpenStudentWhatsapp()
                                        ? 'border-emerald-500/35 bg-emerald-500/10 text-emerald-200 hover:bg-emerald-500/20'
                                        : 'border-gray-600/60 bg-gray-800/50 text-gray-500 cursor-not-allowed'">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.84 11.84 0 0012.03 0C5.4 0 .02 5.38.02 12c0 2.11.55 4.17 1.6 5.99L0 24l6.18-1.61A11.95 11.95 0 0012.03 24c6.62 0 12-5.38 12-12 0-3.2-1.25-6.2-3.51-8.52zM12.03 21.8c-1.82 0-3.6-.49-5.16-1.42l-.37-.22-3.67.96.98-3.58-.24-.37A9.76 9.76 0 012.22 12C2.22 6.6 6.62 2.2 12.03 2.2a9.76 9.76 0 016.94 2.88 9.7 9.7 0 012.86 6.92c0 5.4-4.4 9.8-9.8 9.8zm5.37-7.35c-.29-.14-1.7-.84-1.96-.94-.26-.1-.45-.14-.64.14-.19.29-.74.94-.91 1.13-.17.19-.33.22-.62.07-.29-.14-1.2-.44-2.29-1.4-.85-.75-1.43-1.68-1.59-1.96-.17-.29-.02-.44.13-.58.13-.13.29-.33.43-.5.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.88-2.13-.23-.55-.46-.47-.64-.48h-.55c-.19 0-.5.07-.76.36-.26.29-1 1-.96 2.43.05 1.43 1.03 2.81 1.18 3 .14.19 2 3.05 4.84 4.28.68.29 1.21.47 1.62.6.68.21 1.29.18 1.77.11.54-.08 1.7-.69 1.94-1.36.24-.67.24-1.24.17-1.36-.07-.12-.26-.19-.55-.33z"/></svg>
                                WhatsApp aluno
                            </button>
                        </div>
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

                <hr class="border-gray-700">

                <div class="space-y-6">
                    <template x-for="(meal, mealIndex) in meals" :key="meal.id">
                        <div class="bg-gray-900/40 border border-gray-700 rounded-xl p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                                <h4 class="text-lg font-semibold text-teal-300">Refeicao <span x-text="mealIndex + 1"></span></h4>
                                <div class="flex items-center gap-2 ml-auto">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/15 text-teal-200 border border-teal-500/30">
                                        <span x-text="formatCalories(mealCalories(meal))"></span>&nbsp;kcal
                                    </span>
                                    <button
                                        type="button"
                                        @click="removeMeal(mealIndex)"
                                        x-show="meals.length > 1"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-rose-400/35 bg-rose-500/10 text-rose-200 hover:bg-rose-500/20 hover:border-rose-300/45 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-400/40"
                                        title="Remover refeicao">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span class="hidden sm:inline text-xs font-semibold">Remover</span>
                                    </button>
                                </div>
                            </div>

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
                                            <button
                                                type="button"
                                                @click="removeFood(mealIndex, foodIndex)"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-rose-400/35 bg-rose-500/10 text-rose-200 hover:bg-rose-500/20 hover:border-rose-300/45 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-400/40"
                                                title="Remover alimento">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
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

                <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <p class="text-sm font-semibold text-emerald-200">Total estimado da dieta</p>
                        <p class="text-xl font-bold text-emerald-300">
                            <span x-text="formatCalories(totalCalories())"></span> kcal/dia
                        </p>
                    </div>
                    <p class="mt-2 text-xs text-emerald-200/80">Soma automática baseada no campo de Kcal de cada alimento.</p>
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
function dietForm(initialMeals, initialState, generateAiUrl, csrfToken, canUseDietAi, goalOptions, studentAnamnesisSeed, anamnesisPdfBaseUrl, studentContactMap, anamnesisShareLinkMap) {
    const nowBase = Date.now();

    return {
        canUseDietAi: !!canUseDietAi,
        goalOptions: Array.isArray(goalOptions) ? goalOptions : [],
        studentAnamnesisSeed: (studentAnamnesisSeed && typeof studentAnamnesisSeed === 'object') ? studentAnamnesisSeed : {},
        anamnesisPdfBaseUrl: typeof anamnesisPdfBaseUrl === 'string' ? anamnesisPdfBaseUrl : '',
        studentContactMap: (studentContactMap && typeof studentContactMap === 'object') ? studentContactMap : {},
        anamnesisShareLinkMap: (anamnesisShareLinkMap && typeof anamnesisShareLinkMap === 'object') ? anamnesisShareLinkMap : {},
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

        canDownloadAnamnesisPdf() {
            return !!this.studentId && !!this.anamnesisPdfBaseUrl;
        },

        anamnesisPdfHref() {
            if (!this.canDownloadAnamnesisPdf()) {
                return '';
            }

            return this.anamnesisPdfBaseUrl + '?student_id=' + encodeURIComponent(this.studentId);
        },

        downloadAnamnesisPdf() {
            const href = this.anamnesisPdfHref();
            if (!href) {
                window.alert('Selecione um aluno para baixar o PDF da anamnese.');
                return;
            }
            window.open(href, '_blank');
        },

        studentAnamnesisShareLink() {
            const key = this.studentId ? String(this.studentId) : '';
            return key && this.anamnesisShareLinkMap[key]
                ? String(this.anamnesisShareLinkMap[key])
                : '';
        },

        canCopyAnamnesisLink() {
            return this.studentAnamnesisShareLink() !== '';
        },

        async copyAnamnesisLink() {
            const link = this.studentAnamnesisShareLink();
            if (!link) {
                window.alert('Selecione um aluno para copiar o link da anamnese.');
                return;
            }

            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(link);
                } else {
                    throw new Error('Clipboard indisponivel');
                }
                window.alert('Link da anamnese copiado com sucesso.');
            } catch (error) {
                window.prompt('Copie o link da anamnese:', link);
            }
        },

        selectedStudentContact() {
            const key = this.studentId ? String(this.studentId) : '';
            return key && this.studentContactMap[key] ? this.studentContactMap[key] : null;
        },

        normalizeWhatsappPhone(rawPhone) {
            const digits = String(rawPhone || '').replace(/\D/g, '');
            if (!digits) {
                return '';
            }

            if (digits.startsWith('55') && digits.length >= 12) {
                return digits;
            }

            if (digits.length === 10 || digits.length === 11) {
                return '55' + digits;
            }

            return digits.length >= 12 ? digits : '';
        },

        studentWhatsappHref() {
            const contact = this.selectedStudentContact();
            if (!contact) {
                return '';
            }

            const phone = this.normalizeWhatsappPhone(contact.phone || '');
            if (!phone) {
                return '';
            }

            const studentName = String(contact.name || 'aluno').trim();
            const shareLink = this.studentAnamnesisShareLink();
            const message = shareLink
                ? 'Oi ' + studentName + ', tudo bem? Por favor, preencha este link da anamnese nutricional e me envie quando concluir: ' + shareLink
                : 'Oi ' + studentName + ', tudo bem? Vou te enviar o PDF da anamnese nutricional para voce responder e me devolver preenchido.';
            return 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
        },

        canOpenStudentWhatsapp() {
            return this.studentWhatsappHref() !== '';
        },

        openStudentWhatsapp() {
            if (!this.studentId) {
                window.alert('Selecione um aluno para abrir o WhatsApp.');
                return;
            }

            const href = this.studentWhatsappHref();
            if (!href) {
                window.alert('Este aluno nao possui telefone valido para WhatsApp.');
                return;
            }

            window.open(href, '_blank');
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

        parseCaloriesValue(rawValue) {
            if (rawValue === null || rawValue === undefined) {
                return 0;
            }

            const cleaned = String(rawValue).trim().replace(',', '.');
            const match = cleaned.match(/-?\d+(\.\d+)?/);
            if (!match) {
                return 0;
            }

            const value = Number.parseFloat(match[0]);
            if (!Number.isFinite(value) || value <= 0) {
                return 0;
            }

            return value;
        },

        mealCalories(meal) {
            if (!meal || !Array.isArray(meal.foods)) {
                return 0;
            }

            return meal.foods.reduce((sum, food) => sum + this.parseCaloriesValue(food?.calories), 0);
        },

        totalCalories() {
            if (!Array.isArray(this.meals)) {
                return 0;
            }

            return this.meals.reduce((sum, meal) => sum + this.mealCalories(meal), 0);
        },

        formatCalories(value) {
            const safeValue = Number.isFinite(value) ? value : 0;
            return new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 }).format(Math.round(safeValue));
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
