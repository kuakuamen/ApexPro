@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div class="max-w-5xl mx-auto space-y-8 pt-4">
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white">Criar Novo Plano Alimentar</h1>
            <p class="mt-1 text-gray-400">Monte o plano alimentar do aluno.</p>
        </div>

        <div class="p-6" x-data="dietForm()">
            <form action="{{ route('diets.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Aluno</label>
                        <select name="student_id" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            <option value="" class="bg-gray-700">Selecione um aluno...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" class="bg-gray-700">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nome da Dieta</label>
                        <input type="text" name="name" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Dieta de Cutting" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Objetivo</label>
                        <input type="text" name="goal" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Perda de gordura">
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
                                    <input type="text" x-bind:name="'meals[' + mealIndex + '][name]'" x-model="meal.name" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Cafe da Manha" required>
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
                                            <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][name]'" x-model="food.name" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Arroz Integral" required>
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
                                            <button type="button" @click="removeFood(mealIndex, foodIndex)" class="text-red-400 hover:text-red-300 text-sm font-semibold transition-colors">
                                                X
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
    function dietForm() {
        return {
            meals: [
                {
                    id: Date.now(),
                    name: '',
                    time: '',
                    foods: [
                        { id: Date.now() + 1, name: '', quantity: '', calories: '', observation: '' }
                    ]
                }
            ],
            addMeal() {
                this.meals.push({
                    id: Date.now(),
                    name: '',
                    time: '',
                    foods: [
                        { id: Date.now() + 1, name: '', quantity: '', calories: '', observation: '' }
                    ]
                });
            },
            removeMeal(index) {
                this.meals.splice(index, 1);
            },
            addFood(mealIndex) {
                this.meals[mealIndex].foods.push({
                    id: Date.now(),
                    name: '',
                    quantity: '',
                    calories: '',
                    observation: ''
                });
            },
            removeFood(mealIndex, foodIndex) {
                this.meals[mealIndex].foods.splice(foodIndex, 1);
            }
        }
    }
</script>
@endsection