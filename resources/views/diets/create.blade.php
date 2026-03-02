@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
        <div class="mt-8 text-2xl font-bold text-gray-900">
            Criar Novo Plano Alimentar
        </div>
    </div>

    <div class="p-6 sm:px-20 bg-green-50" x-data="dietForm()">
        <form action="{{ route('diets.store') }}" method="POST">
            @csrf

            <!-- Dados Básicos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Aluno</label>
                    <select name="student_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                        <option value="">Selecione um aluno...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome da Dieta</label>
                    <input type="text" name="name" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2" placeholder="Ex: Dieta de Cutting" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Objetivo</label>
                    <input type="text" name="goal" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2" placeholder="Ex: Perda de gordura">
                </div>
            </div>

            <hr class="my-8 border-gray-300">

            <!-- Refeições -->
            <div class="space-y-8">
                <template x-for="(meal, mealIndex) in meals" :key="meal.id">
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200 relative">
                        <!-- Botão Remover Refeição -->
                        <button type="button" @click="removeMeal(mealIndex)" class="absolute top-4 right-4 text-red-500 hover:text-red-700" x-show="meals.length > 1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <h4 class="text-lg font-medium text-green-700 mb-4">Refeição <span x-text="mealIndex + 1"></span></h4>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nome da Refeição</label>
                                <input type="text" x-bind:name="'meals[' + mealIndex + '][name]'" x-model="meal.name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2" placeholder="Ex: Café da Manhã" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Horário</label>
                                <input type="time" x-bind:name="'meals[' + mealIndex + '][time]'" x-model="meal.time" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md p-2">
                            </div>
                        </div>

                        <!-- Lista de Alimentos -->
                        <div class="space-y-4 pl-4 border-l-2 border-green-100">
                            <template x-for="(food, foodIndex) in meal.foods" :key="food.id">
                                <div class="grid grid-cols-12 gap-4 items-end bg-gray-50 p-3 rounded">
                                    <div class="col-span-4">
                                        <label class="block text-xs font-medium text-gray-500">Alimento</label>
                                        <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][name]'" x-model="food.name" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ex: Arroz Integral" required>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-500">Qtd</label>
                                        <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][quantity]'" x-model="food.quantity" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ex: 100g">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium text-gray-500">Kcal</label>
                                        <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][calories]'" x-model="food.calories" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ex: 120">
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-xs font-medium text-gray-500">Obs</label>
                                        <input type="text" x-bind:name="'meals[' + mealIndex + '][foods][' + foodIndex + '][observation]'" x-model="food.observation" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Opcional">
                                    </div>
                                    <div class="col-span-1 flex justify-end">
                                        <button type="button" @click="removeFood(mealIndex, foodIndex)" class="text-red-500 hover:text-red-700 text-sm">
                                            X
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addFood(mealIndex)" class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                + Adicionar Alimento
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6">
                <button type="button" @click="addMeal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none">
                    + Adicionar Refeição
                </button>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Salvar Dieta
                </button>
            </div>
        </form>
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
