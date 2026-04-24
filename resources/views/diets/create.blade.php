@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

@php
    $canUseDietAi = $canUseDietAi ?? false;
    $goalOptions = [
        'Perda de gordura',
        'Hipertrofia (ganho de massa)',
        'Recomposicao corporal',
        'Manutencao do peso',
        'Performance e condicionamento',
        'Saude e qualidade de vida',
    ];

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
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-8 pt-4">
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden"
         x-data='dietForm(@json($rawMeals), @json($initialState), @json(route('diets.generate-ai')), @json(csrf_token()), @json($canUseDietAi), @json(array_values($goalOptions)))'>
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
function dietForm(initialMeals, initialState, generateAiUrl, csrfToken, canUseDietAi, goalOptions) {
    const nowBase = Date.now();

    return {
        canUseDietAi: !!canUseDietAi,
        goalOptions: Array.isArray(goalOptions) ? goalOptions : [],
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

        meals: Array.isArray(initialMeals) && initialMeals.length
            ? initialMeals.map((meal, mealIndex) => thisNormalizeMeal(meal, mealIndex, nowBase))
            : [thisDefaultMeal(nowBase)],

        init() {
            this.applyGoal(this.goal);
        },

        syncGoalValue() {
            if (!this.canUseDietAi) {
                return;
            }

            if (this.goalSelect === '__custom__') {
                this.goal = this.goalCustom || '';
                return;
            }

            this.goal = this.goalSelect || '';
            this.goalCustom = '';
        },

        applyGoal(value) {
            this.goal = value || '';
            if (!this.canUseDietAi) {
                return;
            }

            if (!this.goal) {
                this.goalSelect = '';
                this.goalCustom = '';
                return;
            }

            if (this.goalOptions.includes(this.goal)) {
                this.goalSelect = this.goal;
                this.goalCustom = '';
                return;
            }

            this.goalSelect = '__custom__';
            this.goalCustom = this.goal;
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
                    }),
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Nao foi possivel gerar dieta com IA.');
                }

                if (data.name) this.planName = data.name;
                if (data.goal) this.applyGoal(data.goal);
                if (data.daily_kcal_target) this.initialKcal = String(data.daily_kcal_target);

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
</script>
@endsection
