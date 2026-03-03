@extends('layouts.app')

@section('content')
<div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 shadow-lg overflow-hidden sm:rounded-lg max-w-4xl mx-auto">
    <div class="px-4 py-5 border-b border-gray-700 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-white">
                Editar Avaliação: {{ $student->name }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-400">
                Data original: {{ $measurement->date->format('d/m/Y') }}
            </p>
        </div>
        
        <!-- Botão de Excluir -->
        <form action="{{ route('personal.measurements.destroy', $measurement) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta avaliação?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-500 bg-red-900/50 hover:bg-red-800/60 focus:outline-none transition-colors">
                Excluir Avaliação
            </button>
        </form>
    </div>
    
    <div class="p-6">
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                <p class="font-bold">Ops! Algo deu errado.</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="measurementForm" action="{{ route('personal.measurements.update', $measurement) }}" method="POST" enctype="multipart/form-data" data-student-url="{{ route('personal.students.show', $student) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" x-data="{ 
                evalDate: '{{ old('date', $measurement->date->format('Y-m-d')) }}', 
                birthDate: '{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : '' }}',
                age: null,
                calculateAge() {
                    if (!this.birthDate || !this.evalDate) {
                        this.age = null;
                        return;
                    }
                    const birth = new Date(this.birthDate);
                    const eval = new Date(this.evalDate);
                    let age = eval.getFullYear() - birth.getFullYear();
                    const m = eval.getMonth() - birth.getMonth();
                    if (m < 0 || (m === 0 && eval.getDate() < birth.getDate())) {
                        age--;
                    }
                    this.age = age;
                }
            }" x-init="calculateAge()">
                <div>
                    <label class="block text-sm font-medium text-gray-300">Data da Avaliação</label>
                    <input type="date" name="date" x-model="evalDate" @change="calculateAge()" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    
                    <p class="mt-1 text-sm text-gray-400" x-show="age !== null">
                        Idade na data: <span class="font-bold text-indigo-400" x-text="age + ' anos'"></span>
                    </p>
                    <p class="mt-1 text-xs text-red-400" x-show="!birthDate">
                        * Data de nascimento não cadastrada no perfil do aluno.
                    </p>
                </div>
            </div>

            <h4 class="text-lg font-medium text-white mb-4 border-b border-gray-700 pb-2">Dados Corporais</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-400">Peso (kg)</label>
                    <input type="number" step="0.01" name="weight" value="{{ old('weight', $measurement->weight) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Altura (cm ou m)</label>
                    <input type="number" step="0.01" name="height" value="{{ old('height', $measurement->height) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">% Gordura</label>
                    <input type="number" step="any" name="body_fat" value="{{ old('body_fat', $measurement->body_fat) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label for="muscle_mass" class="block text-sm font-medium text-gray-300">Massa Muscular (kg)</label>
                    <input type="number" step="any" name="muscle_mass" value="{{ old('muscle_mass', $measurement->muscle_mass) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
            </div>

            <h4 class="text-lg font-medium text-gray-200 mb-4 border-b border-gray-700 pb-2">Dobras Cutâneas (mm)</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" x-data="{ 
                subescapular: {{ $measurement->subescapular ?? 0 }}, 
                tricipital: {{ $measurement->tricipital ?? 0 }}, 
                bicipital: {{ $measurement->bicipital ?? 0 }}, 
                toracica: {{ $measurement->toracica ?? 0 }}, 
                abdominal_fold: {{ $measurement->abdominal_fold ?? 0 }}, 
                axilar_media: {{ $measurement->axilar_media ?? 0 }}, 
                suprailiaca: {{ $measurement->suprailiaca ?? 0 }}, 
                coxa_fold: {{ $measurement->coxa_fold ?? 0 }}, 
                panturrilha_fold: {{ $measurement->panturrilha_fold ?? 0 }},
                calculateSum() {
                    const sum = parseFloat(this.subescapular || 0) + parseFloat(this.tricipital || 0) + parseFloat(this.bicipital || 0) + parseFloat(this.toracica || 0) + parseFloat(this.abdominal_fold || 0) + parseFloat(this.axilar_media || 0) + parseFloat(this.suprailiaca || 0) + parseFloat(this.coxa_fold || 0) + parseFloat(this.panturrilha_fold || 0);
                    document.querySelector('input[name=sum_skinfolds]').value = sum > 0 ? sum.toFixed(1) : '';
                }
            }" x-init="calculateSum()">
                <div>
                    <label class="block text-xs font-medium text-gray-400">Subescapular (mm)</label>
                    <input type="number" step="0.1" name="subescapular" x-model="subescapular" @input="calculateSum()" value="{{ old('subescapular', $measurement->subescapular) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Tricipital (mm)</label>
                    <input type="number" step="0.1" name="tricipital" x-model="tricipital" @input="calculateSum()" value="{{ old('tricipital', $measurement->tricipital) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Bicipital (mm)</label>
                    <input type="number" step="0.1" name="bicipital" x-model="bicipital" @input="calculateSum()" value="{{ old('bicipital', $measurement->bicipital) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Torácica (mm)</label>
                    <input type="number" step="0.1" name="toracica" x-model="toracica" @input="calculateSum()" value="{{ old('toracica', $measurement->toracica) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Abdominal (mm)</label>
                    <input type="number" step="0.1" name="abdominal_fold" x-model="abdominal_fold" @input="calculateSum()" value="{{ old('abdominal_fold', $measurement->abdominal_fold) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Axilar Média (mm)</label>
                    <input type="number" step="0.1" name="axilar_media" x-model="axilar_media" @input="calculateSum()" value="{{ old('axilar_media', $measurement->axilar_media) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Supra-ilíaca (mm)</label>
                    <input type="number" step="0.1" name="suprailiaca" x-model="suprailiaca" @input="calculateSum()" value="{{ old('suprailiaca', $measurement->suprailiaca) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa (mm)</label>
                    <input type="number" step="0.1" name="coxa_fold" x-model="coxa_fold" @input="calculateSum()" value="{{ old('coxa_fold', $measurement->coxa_fold) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Panturrilha (mm)</label>
                    <input type="number" step="0.1" name="panturrilha_fold" x-model="panturrilha_fold" @input="calculateSum()" value="{{ old('panturrilha_fold', $measurement->panturrilha_fold) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Somatório de Dobras (mm)</label>
                    <input type="number" step="0.1" name="sum_skinfolds" id="sum_skinfolds" value="{{ old('sum_skinfolds', $measurement->sum_skinfolds) }}" class="mt-1 block w-full bg-gray-700/50 border border-indigo-500 rounded-lg px-4 py-3 text-indigo-300 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-400">Peitoral</label>
                    <input type="number" step="0.1" name="chest" value="{{ old('chest', $measurement->chest) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Cintura</label>
                    <input type="number" step="0.1" name="waist" value="{{ old('waist', $measurement->waist) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Abdômen</label>
                    <input type="number" step="0.1" name="abdomen" value="{{ old('abdomen', $measurement->abdomen) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Quadril</label>
                    <input type="number" step="0.1" name="hips" value="{{ old('hips', $measurement->hips) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                
                <!-- Membros -->
                <div>
                    <label class="block text-xs font-medium text-gray-400">Braço Esq.</label>
                    <input type="number" step="0.1" name="left_arm" value="{{ old('left_arm', $measurement->left_arm) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Braço Dir.</label>
                    <input type="number" step="0.1" name="right_arm" value="{{ old('right_arm', $measurement->right_arm) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa Esq.</label>
                    <input type="number" step="0.1" name="left_thigh" value="{{ old('left_thigh', $measurement->left_thigh) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa Dir.</label>
                    <input type="number" step="0.1" name="right_thigh" value="{{ old('right_thigh', $measurement->right_thigh) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Panturrilha Esq.</label>
                    <input type="number" step="0.1" name="left_calf" value="{{ old('left_calf', $measurement->left_calf) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Panturrilha Dir.</label>
                    <input type="number" step="0.1" name="right_calf" value="{{ old('right_calf', $measurement->right_calf) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-400">Ombro (cm)</label>
                    <input type="number" step="0.1" name="ombro" value="{{ old('ombro', $measurement->ombro) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Tórax (cm)</label>
                    <input type="number" step="0.1" name="torax" value="{{ old('torax', $measurement->torax) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Abdômen inferior (cm)</label>
                    <input type="number" step="0.1" name="abdomen_inferior" value="{{ old('abdomen_inferior', $measurement->abdomen_inferior) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Braço contraído D (cm)</label>
                    <input type="number" step="0.1" name="right_arm_contracted" value="{{ old('right_arm_contracted', $measurement->right_arm_contracted) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400">Braço contraído E (cm)</label>
                    <input type="number" step="0.1" name="left_arm_contracted" value="{{ old('left_arm_contracted', $measurement->left_arm_contracted) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Antebraço D (cm)</label>
                    <input type="number" step="0.1" name="right_forearm" value="{{ old('right_forearm', $measurement->right_forearm) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Antebraço E (cm)</label>
                    <input type="number" step="0.1" name="left_forearm" value="{{ old('left_forearm', $measurement->left_forearm) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa proximal D (cm)</label>
                    <input type="number" step="0.1" name="right_thigh_proximal" value="{{ old('right_thigh_proximal', $measurement->right_thigh_proximal) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa medial D (cm)</label>
                    <input type="number" step="0.1" name="right_thigh_medial" value="{{ old('right_thigh_medial', $measurement->right_thigh_medial) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa distal D (cm)</label>
                    <input type="number" step="0.1" name="right_thigh_distal" value="{{ old('right_thigh_distal', $measurement->right_thigh_distal) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa proximal E (cm)</label>
                    <input type="number" step="0.1" name="left_thigh_proximal" value="{{ old('left_thigh_proximal', $measurement->left_thigh_proximal) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa medial E (cm)</label>
                    <input type="number" step="0.1" name="left_thigh_medial" value="{{ old('left_thigh_medial', $measurement->left_thigh_medial) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400">Coxa distal E (cm)</label>
                    <input type="number" step="0.1" name="left_thigh_distal" value="{{ old('left_thigh_distal', $measurement->left_thigh_distal) }}" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                </div>
            </div>

            <div class="bg-gradient-to-r from-indigo-900/30 to-purple-900/30 border border-indigo-700/50 rounded-lg p-6 mb-6">
                <h4 class="text-lg font-medium text-indigo-300 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Resultados dos Cálculos (Auto)
                </h4>
                <p class="text-xs text-gray-400 mb-4">Os valores abaixo serão recalculados automaticamente ao salvar.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- GUEDES -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-semibold text-indigo-300 border-b border-indigo-700 pb-2">GUEDES (3 Dobras)</h5>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Densidade (g/ml)</label>
                            <input type="number" step="0.0001" name="guedes_density" value="{{ old('guedes_density', $measurement->guedes_density) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">% Gordura</label>
                            <input type="number" step="0.01" name="guedes_fat_pct" value="{{ old('guedes_fat_pct', $measurement->guedes_fat_pct) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Massa Gordura (kg)</label>
                            <input type="number" step="0.01" name="guedes_fat_mass" value="{{ old('guedes_fat_mass', $measurement->guedes_fat_mass) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Massa Livre (kg)</label>
                            <input type="number" step="0.01" name="guedes_lean_mass" value="{{ old('guedes_lean_mass', $measurement->guedes_lean_mass) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                    </div>

                    <!-- POLLOCK 3 -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-semibold text-purple-300 border-b border-purple-700 pb-2">POLLOCK 3</h5>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Densidade (g/ml)</label>
                            <input type="number" step="0.0001" name="pollock3_density" value="{{ old('pollock3_density', $measurement->pollock3_density) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">% Gordura</label>
                            <input type="number" step="0.01" name="pollock3_fat_pct" value="{{ old('pollock3_fat_pct', $measurement->pollock3_fat_pct) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Massa Gordura (kg)</label>
                            <input type="number" step="0.01" name="pollock3_fat_mass" value="{{ old('pollock3_fat_mass', $measurement->pollock3_fat_mass) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Massa Livre (kg)</label>
                            <input type="number" step="0.01" name="pollock3_lean_mass" value="{{ old('pollock3_lean_mass', $measurement->pollock3_lean_mass) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                    </div>

                    <!-- POLLOCK 7 -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-semibold text-pink-300 border-b border-pink-700 pb-2">POLLOCK 7 (Mais Preciso)</h5>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Densidade (g/ml)</label>
                            <input type="number" step="0.0001" name="pollock7_density" value="{{ old('pollock7_density', $measurement->pollock7_density) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">% Gordura</label>
                            <input type="number" step="0.01" name="pollock7_fat_pct" value="{{ old('pollock7_fat_pct', $measurement->pollock7_fat_pct) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Massa Gordura (kg)</label>
                            <input type="number" step="0.01" name="pollock7_fat_mass" value="{{ old('pollock7_fat_mass', $measurement->pollock7_fat_mass) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400">Massa Livre (kg)</label>
                            <input type="number" step="0.01" name="pollock7_lean_mass" value="{{ old('pollock7_lean_mass', $measurement->pollock7_lean_mass) }}" class="mt-1 block w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-gray-300 cursor-not-allowed" readonly />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300">Observações Gerais</label>
                <textarea name="notes" rows="3" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $measurement->notes) }}</textarea>
            </div>

            <!-- Anamnese desta Avaliação -->
            <div class="border-t border-gray-700 pt-6 mb-6" x-data="{ showAnamnese: {{ $measurement->injuries || $measurement->medications ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between mb-4 cursor-pointer" @click="showAnamnese = !showAnamnese">
                    <h4 class="text-lg font-medium text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Anamnese & Saúde (Opcional)
                    </h4>
                    <span class="text-sm text-indigo-400 font-medium" x-text="showAnamnese ? '- Ocultar' : '+ Preencher'"></span>
                </div>
                
                <div x-show="showAnamnese" class="bg-gray-900/50 p-4 rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Histórico de Lesões</label>
                        <input type="text" name="injuries" value="{{ old('injuries', $measurement->injuries) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2" placeholder="Ex: Joelho, Ombro...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Medicamentos</label>
                        <input type="text" name="medications" value="{{ old('medications', $measurement->medications) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2" placeholder="Ex: Nenhum">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Cirurgias</label>
                        <input type="text" name="surgeries" value="{{ old('surgeries', $measurement->surgeries) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2" placeholder="Ex: Apêndice (2019)...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Dores Atuais</label>
                        <input type="text" name="pain_points" value="{{ old('pain_points', $measurement->pain_points) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2" placeholder="Ex: Dor na lombar ao agachar...">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300">Hábitos (Fumo, Álcool, Sono)</label>
                        <input type="text" name="habits" value="{{ old('habits', $measurement->habits) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2" placeholder="Ex: Fuma socialmente, dorme 6h/noite...">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300">Objetivo Específico desta Fase</label>
                        <input type="text" name="goal" value="{{ old('goal', $measurement->goal) }}" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white shadow-sm sm:text-sm rounded-md p-2" placeholder="Ex: Focar em perder barriga para o casamento...">
                    </div>
                </div>
            </div>

            <h4 class="text-lg font-medium text-white mb-4 border-b border-gray-700 pb-2">Galeria de Evolução</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Frente -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300 text-center">Frente</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-md relative hover:border-indigo-500 transition-colors">
                        <div class="space-y-1 text-center">
                            @if($measurement->photo_front)
                                <img src="{{ route('measurement.photo', [$measurement->id, 'front']) }}?v={{ $measurement->updated_at?->timestamp }}" class="mx-auto h-32 object-cover rounded-md mb-2">
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                            <div class="flex text-sm text-gray-400 justify-center">
                                <label for="photo_front" class="relative cursor-pointer bg-gray-800 rounded-md font-medium text-indigo-400 hover:text-indigo-300 focus-within:outline-none">
                                    <span>{{ $measurement->photo_front ? 'Alterar' : 'Tirar / Upload' }}</span>
                                    <input id="photo_front" name="photo_front" type="file" class="sr-only" accept="image/*" capture="environment">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Costas -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300 text-center">Costas</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-md relative hover:border-indigo-500 transition-colors">
                        <div class="space-y-1 text-center">
                            @if($measurement->photo_back)
                                <img src="{{ route('measurement.photo', [$measurement->id, 'back']) }}?v={{ $measurement->updated_at?->timestamp }}" class="mx-auto h-32 object-cover rounded-md mb-2">
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                            <div class="flex text-sm text-gray-400 justify-center">
                                <label for="photo_back" class="relative cursor-pointer bg-gray-800 rounded-md font-medium text-indigo-400 hover:text-indigo-300 focus-within:outline-none">
                                    <span>{{ $measurement->photo_back ? 'Alterar' : 'Tirar / Upload' }}</span>
                                    <input id="photo_back" name="photo_back" type="file" class="sr-only" accept="image/*" capture="environment">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lado D -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300 text-center">Lado D</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-md relative hover:border-indigo-500 transition-colors">
                        <div class="space-y-1 text-center">
                            @if($measurement->photo_side_right || $measurement->photo_side)
                                <img src="{{ route('measurement.photo', [$measurement->id, 'side_right']) }}?v={{ $measurement->updated_at?->timestamp }}" class="mx-auto h-32 object-cover rounded-md mb-2">
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                            <div class="flex text-sm text-gray-400 justify-center">
                                <label for="photo_side_right" class="relative cursor-pointer bg-gray-800 rounded-md font-medium text-indigo-400 hover:text-indigo-300 focus-within:outline-none">
                                    <span>{{ ($measurement->photo_side_right || $measurement->photo_side) ? 'Alterar' : 'Tirar / Upload' }}</span>
                                    <input id="photo_side_right" name="photo_side_right" type="file" class="sr-only" accept="image/*" capture="environment">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lado E -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-300 text-center">Lado E</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-md relative hover:border-indigo-500 transition-colors">
                        <div class="space-y-1 text-center">
                            @if($measurement->photo_side_left)
                                <img src="{{ route('measurement.photo', [$measurement->id, 'side_left']) }}?v={{ $measurement->updated_at?->timestamp }}" class="mx-auto h-32 object-cover rounded-md mb-2">
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                            <div class="flex text-sm text-gray-400 justify-center">
                                <label for="photo_side_left" class="relative cursor-pointer bg-gray-800 rounded-md font-medium text-indigo-400 hover:text-indigo-300 focus-within:outline-none">
                                    <span>{{ $measurement->photo_side_left ? 'Alterar' : 'Tirar / Upload' }}</span>
                                    <input id="photo_side_left" name="photo_side_left" type="file" class="sr-only" accept="image/*" capture="environment">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-300 mb-2">Mais</label>
                @php
                    $existingSinglesCount = (!empty($measurement->photo_front) ? 1 : 0)
                        + (!empty($measurement->photo_back) ? 1 : 0)
                        + ((!empty($measurement->photo_side_right) || !empty($measurement->photo_side)) ? 1 : 0)
                        + (!empty($measurement->photo_side_left) ? 1 : 0);
                    $existingExtraCount = is_array($measurement->extra_photos) ? count($measurement->extra_photos) : 0;
                    $remainingExtraSlots = max(0, 9 - $existingSinglesCount - $existingExtraCount);
                @endphp
                <p id="edit_photo_limit_hint" class="text-xs text-gray-400 mb-2">Máximo de 9 imagens no total por avaliação. Restante para "Mais": {{ $remainingExtraSlots }}.</p>
                <div class="border-2 border-gray-600 border-dashed rounded-md p-4 hover:border-indigo-500 transition-colors">
                    @if(is_array($measurement->extra_photos) && count($measurement->extra_photos) > 0)
                        <p class="text-xs text-gray-400 mb-3">{{ count($measurement->extra_photos) }} imagem(ns) extra(s) já cadastrada(s). Você pode adicionar mais abaixo.</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-4">
                            @foreach($measurement->extra_photos as $index => $photoPath)
                                <div id="extra_card_{{ $index }}" class="space-y-2">
                                    <img
                                        id="extra_preview_{{ $index }}"
                                        src="{{ route('measurement.photo.extra', [$measurement->id, $index]) }}?v={{ $measurement->updated_at?->timestamp }}"
                                        alt="Foto extra {{ $index + 1 }}"
                                        class="w-full object-contain rounded-md border border-gray-600 bg-gray-900"
                                        style="aspect-ratio: 16 / 9;"
                                    >
                                    <div class="flex items-center justify-center gap-2">
                                        <label for="replace_extra_{{ $index }}" class="cursor-pointer px-2 py-1 text-xs rounded-md border border-gray-500 text-gray-200 bg-gray-700 hover:bg-gray-600">
                                            Alterar
                                            <input
                                                id="replace_extra_{{ $index }}"
                                                name="replace_extra_photos[{{ $index }}]"
                                                type="file"
                                                class="sr-only replace-extra-input"
                                                data-index="{{ $index }}"
                                                accept="image/*"
                                                capture="environment"
                                            >
                                        </label>

                                        <button
                                            type="button"
                                            class="px-2 py-1 text-xs rounded-md border border-red-500 text-red-300 bg-red-900/30 hover:bg-red-900/50 remove-extra-btn"
                                            data-index="{{ $index }}"
                                        >
                                            Remover
                                        </button>

                                        <input
                                            id="remove_extra_{{ $index }}"
                                            type="checkbox"
                                            name="remove_extra_photos[]"
                                            value="{{ $index }}"
                                            class="hidden"
                                        >
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="flex justify-center">
                        <label for="photo_extra" class="relative cursor-pointer bg-gray-800 rounded-md font-medium text-indigo-400 hover:text-indigo-300 focus-within:outline-none px-3 py-1 text-sm">
                            <span>Adicionar imagens extras</span>
                            <input id="photo_extra" name="photo_extra[]" type="file" class="sr-only" accept="image/*" capture="environment" multiple data-max-extra="{{ $remainingExtraSlots }}">
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Campo hidden para método escolhido -->
            <input type="hidden" name="selected_protocol" id="selected_protocol" value="">
            
            <!-- Botão submit escondido para submeter via JS -->
            <button type="submit" id="hiddenSubmit" class="hidden">Submit</button>

            <div class="flex justify-end">
                <a href="{{ route('personal.students.show', $student) }}" class="mr-3 inline-flex items-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-300 bg-gray-800 hover:bg-gray-700 transition-all">
                    Cancelar
                </a>
                <button type="button" onclick="showProtocolSelector()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-all">
                    Continuar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Seleção de Protocolo -->
<div id="protocolModal" class="hidden fixed inset-0 overflow-y-auto" style="z-index: 9999;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/80 transition-opacity" aria-hidden="true" onclick="closeProtocolModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-zinc-900 border border-zinc-700 rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6 relative" style="z-index: 10000;">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-teal-900/30 border border-teal-800/40">
                    <svg class="h-6 w-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-bold text-stone-100" id="modal-title">
                        Escolha o Protocolo para Composição Corporal
                    </h3>
                    <p class="mt-2 text-sm text-stone-400">
                        Selecione qual método deseja usar para % de Gordura e Massa Muscular
                    </p>
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4" id="protocolCards">
                    <!-- GUEDES -->
                    <div class="protocol-card cursor-pointer bg-zinc-800/60 border-2 border-zinc-700 hover:border-teal-600 rounded-lg p-4 transition-all" data-protocol="guedes" onclick="selectProtocol('guedes')">
                        <h4 class="text-sm font-bold text-teal-300 mb-3">GUEDES (3 Dobras)</h4>
                        <div class="space-y-2 text-xs">
                            <div><span class="text-stone-400">Densidade:</span> <span class="font-mono font-bold text-stone-100" id="guedes_density">-</span> g/ml</div>
                            <div><span class="text-stone-400">% Gordura:</span> <span class="font-mono font-bold text-teal-200 text-base" id="guedes_fat">-</span> %</div>
                            <div><span class="text-stone-400">Massa Gordura:</span> <span class="font-mono font-bold text-stone-100" id="guedes_fat_mass">-</span> kg</div>
                            <div><span class="text-stone-400">Massa Livre:</span> <span class="font-mono font-bold text-stone-100" id="guedes_lean_mass">-</span> kg</div>
                        </div>
                    </div>
                    
                    <!-- POLLOCK 3 -->
                    <div class="protocol-card cursor-pointer bg-zinc-800/60 border-2 border-zinc-700 hover:border-teal-600 rounded-lg p-4 transition-all" data-protocol="pollock3" onclick="selectProtocol('pollock3')">
                        <h4 class="text-sm font-bold text-teal-300 mb-3">POLLOCK 3</h4>
                        <div class="space-y-2 text-xs">
                            <div><span class="text-stone-400">Densidade:</span> <span class="font-mono font-bold text-stone-100" id="pollock3_density">-</span> g/ml</div>
                            <div><span class="text-stone-400">% Gordura:</span> <span class="font-mono font-bold text-teal-200 text-base" id="pollock3_fat">-</span> %</div>
                            <div><span class="text-stone-400">Massa Gordura:</span> <span class="font-mono font-bold text-stone-100" id="pollock3_fat_mass">-</span> kg</div>
                            <div><span class="text-stone-400">Massa Livre:</span> <span class="font-mono font-bold text-stone-100" id="pollock3_lean_mass">-</span> kg</div>
                        </div>
                    </div>
                    
                    <!-- POLLOCK 7 -->
                    <div class="protocol-card cursor-pointer bg-zinc-800/60 border-2 border-zinc-700 hover:border-teal-600 rounded-lg p-4 transition-all" data-protocol="pollock7" onclick="selectProtocol('pollock7')">
                        <h4 class="text-sm font-bold text-teal-300 mb-3">POLLOCK 7 (Mais Preciso)</h4>
                        <div class="space-y-2 text-xs">
                            <div><span class="text-stone-400">Densidade:</span> <span class="font-mono font-bold text-stone-100" id="pollock7_density">-</span> g/ml</div>
                            <div><span class="text-stone-400">% Gordura:</span> <span class="font-mono font-bold text-teal-200 text-base" id="pollock7_fat">-</span> %</div>
                            <div><span class="text-stone-400">Massa Gordura:</span> <span class="font-mono font-bold text-stone-100" id="pollock7_fat_mass">-</span> kg</div>
                            <div><span class="text-stone-400">Massa Livre:</span> <span class="font-mono font-bold text-stone-100" id="pollock7_lean_mass">-</span> kg</div>
                        </div>
                    </div>
                </div>
                
                <div id="errorMessage" class="hidden mt-4 bg-red-900/30 border border-red-800/40 text-red-300 px-4 py-3 rounded-lg text-sm">
                </div>
            </div>
            
            <div class="mt-5 sm:mt-6 flex gap-3">
                <button type="button" onclick="closeProtocolModal()" class="flex-1 inline-flex justify-center rounded-md border border-zinc-600 shadow-sm px-4 py-2 bg-zinc-800 text-base font-medium text-stone-300 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-teal-600 sm:text-sm transition-all">
                    Voltar
                </button>
                <button type="button" id="confirmButton" disabled onclick="confirmAndSubmit()" class="flex-1 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-700 text-base font-medium text-stone-100 hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-teal-600 sm:text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    Salvar Alterações
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let calculatedProtocols = {};
    const studentGender = '{{ $student->gender ?? "" }}';
    const studentBirthDate = '{{ $student->birth_date ? $student->birth_date->format("Y-m-d") : "" }}';
    
    function showProtocolSelector() {
        const weight = parseFloat(document.querySelector('input[name="weight"]').value);
        const evalDate = document.querySelector('input[name="date"]').value;
        
        if (!weight || weight <= 0) {
            alert('Por favor, preencha o peso do aluno.');
            return;
        }
        
        if (!evalDate) {
            alert('Por favor, preencha a data da avaliação.');
            return;
        }
        
        if (!studentBirthDate) {
            alert('O aluno não possui data de nascimento cadastrada.');
            return;
        }
        
        const age = calculateAgeFromDates(studentBirthDate, evalDate);
        if (!age || age < 0) {
            alert('Não foi possível calcular a idade do aluno.');
            return;
        }
        
        const skinfolds = {
            subescapular: parseFloat(document.querySelector('input[name="subescapular"]').value) || 0,
            tricipital: parseFloat(document.querySelector('input[name="tricipital"]').value) || 0,
            bicipital: parseFloat(document.querySelector('input[name="bicipital"]').value) || 0,
            toracica: parseFloat(document.querySelector('input[name="toracica"]').value) || 0,
            abdominal_fold: parseFloat(document.querySelector('input[name="abdominal_fold"]').value) || 0,
            axilar_media: parseFloat(document.querySelector('input[name="axilar_media"]').value) || 0,
            suprailiaca: parseFloat(document.querySelector('input[name="suprailiaca"]').value) || 0,
            coxa_fold: parseFloat(document.querySelector('input[name="coxa_fold"]').value) || 0,
            panturrilha_fold: parseFloat(document.querySelector('input[name="panturrilha_fold"]').value) || 0
        };
        
        try {
            calculatedProtocols = calculateAllProtocols(weight, age, studentGender, skinfolds);
            updateProtocolModal(calculatedProtocols);
            document.getElementById('protocolModal').classList.remove('hidden');
        } catch (error) {
            document.getElementById('errorMessage').textContent = error.message;
            document.getElementById('errorMessage').classList.remove('hidden');
            document.getElementById('protocolModal').classList.remove('hidden');
        }
    }
    
    function calculateAgeFromDates(birthDate, evalDate) {
        const birth = new Date(birthDate);
        const eval = new Date(evalDate);
        let age = eval.getFullYear() - birth.getFullYear();
        const m = eval.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && eval.getDate() < birth.getDate())) {
            age--;
        }
        return age;
    }
    
    function calculateAllProtocols(weight, age, gender, skinfolds) {
        const results = {};
        results.guedes = calculateGuedes(weight, age, gender, skinfolds);
        
        if (gender && (gender.toLowerCase().includes('masc') || gender.toLowerCase() === 'm' || gender.toLowerCase() === 'male')) {
            results.pollock3 = calculatePollock3Male(weight, age, skinfolds);
            results.pollock7 = calculatePollock7Male(weight, age, skinfolds);
        } else if (gender && (gender.toLowerCase().includes('fem') || gender.toLowerCase() === 'f' || gender.toLowerCase() === 'female')) {
            results.pollock3 = calculatePollock3Female(weight, age, skinfolds);
            results.pollock7 = calculatePollock7Female(weight, age, skinfolds);
        } else {
            throw new Error('Gênero do aluno não está definido.');
        }
        
        return results;
    }
    
    function calculateGuedes(weight, age, gender, skinfolds) {
        const sum = skinfolds.subescapular + skinfolds.suprailiaca + skinfolds.coxa_fold;
        if (sum <= 0) return null;
        const isMale = gender && (gender.toLowerCase().includes('masc') || gender.toLowerCase() === 'm' || gender.toLowerCase() === 'male');
        const density = isMale ? 1.17136 - (0.06706 * Math.log10(sum)) : 1.16055 - (0.06706 * Math.log10(sum));
        const fatPct = ((4.95 / density) - 4.5) * 100;
        const fatMass = (fatPct / 100) * weight;
        const leanMass = weight - fatMass;
        return { density, fatPct, fatMass, leanMass };
    }
    
    function calculatePollock3Male(weight, age, skinfolds) {
        const sum = skinfolds.toracica + skinfolds.abdominal_fold + skinfolds.coxa_fold;
        if (sum <= 0) return null;
        const density = 1.10938 - (0.0008267 * sum) + (0.0000016 * sum * sum) - (0.0002574 * age);
        const fatPct = ((4.95 / density) - 4.5) * 100;
        const fatMass = (fatPct / 100) * weight;
        const leanMass = weight - fatMass;
        return { density, fatPct, fatMass, leanMass };
    }
    
    function calculatePollock3Female(weight, age, skinfolds) {
        const sum = skinfolds.tricipital + skinfolds.suprailiaca + skinfolds.coxa_fold;
        if (sum <= 0) return null;
        const density = 1.0994921 - (0.0009929 * sum) + (0.0000023 * sum * sum) - (0.0001392 * age);
        const fatPct = ((4.95 / density) - 4.5) * 100;
        const fatMass = (fatPct / 100) * weight;
        const leanMass = weight - fatMass;
        return { density, fatPct, fatMass, leanMass };
    }
    
    function calculatePollock7Male(weight, age, skinfolds) {
        const sum = skinfolds.subescapular + skinfolds.tricipital + skinfolds.toracica + skinfolds.axilar_media + skinfolds.abdominal_fold + skinfolds.suprailiaca + skinfolds.coxa_fold;
        if (sum <= 0) return null;
        const density = 1.112 - (0.00043499 * sum) + (0.00000055 * sum * sum) - (0.00028826 * age);
        const fatPct = ((4.95 / density) - 4.5) * 100;
        const fatMass = (fatPct / 100) * weight;
        const leanMass = weight - fatMass;
        return { density, fatPct, fatMass, leanMass };
    }
    
    function calculatePollock7Female(weight, age, skinfolds) {
        const sum = skinfolds.subescapular + skinfolds.tricipital + skinfolds.toracica + skinfolds.axilar_media + skinfolds.abdominal_fold + skinfolds.suprailiaca + skinfolds.coxa_fold;
        if (sum <= 0) return null;
        const density = 1.097 - (0.00046971 * sum) + (0.00000056 * sum * sum) - (0.00012828 * age);
        const fatPct = ((4.95 / density) - 4.5) * 100;
        const fatMass = (fatPct / 100) * weight;
        const leanMass = weight - fatMass;
        return { density, fatPct, fatMass, leanMass };
    }
    
    function updateProtocolModal(protocols) {
        if (protocols.guedes) {
            document.getElementById('guedes_density').textContent = protocols.guedes.density.toFixed(4);
            document.getElementById('guedes_fat').textContent = protocols.guedes.fatPct.toFixed(2);
            document.getElementById('guedes_fat_mass').textContent = protocols.guedes.fatMass.toFixed(2);
            document.getElementById('guedes_lean_mass').textContent = protocols.guedes.leanMass.toFixed(2);
        }
        if (protocols.pollock3) {
            document.getElementById('pollock3_density').textContent = protocols.pollock3.density.toFixed(4);
            document.getElementById('pollock3_fat').textContent = protocols.pollock3.fatPct.toFixed(2);
            document.getElementById('pollock3_fat_mass').textContent = protocols.pollock3.fatMass.toFixed(2);
            document.getElementById('pollock3_lean_mass').textContent = protocols.pollock3.leanMass.toFixed(2);
        }
        if (protocols.pollock7) {
            document.getElementById('pollock7_density').textContent = protocols.pollock7.density.toFixed(4);
            document.getElementById('pollock7_fat').textContent = protocols.pollock7.fatPct.toFixed(2);
            document.getElementById('pollock7_fat_mass').textContent = protocols.pollock7.fatMass.toFixed(2);
            document.getElementById('pollock7_lean_mass').textContent = protocols.pollock7.leanMass.toFixed(2);
        }
    }
    
    function selectProtocol(protocol) {
        document.querySelectorAll('.protocol-card').forEach(card => {
            card.classList.remove('border-teal-500', 'bg-teal-900/20');
            card.classList.add('border-zinc-700');
        });
        const selectedCard = document.querySelector(`.protocol-card[data-protocol="${protocol}"]`);
        selectedCard.classList.remove('border-zinc-700');
        selectedCard.classList.add('border-teal-500', 'bg-teal-900/20');
        document.getElementById('confirmButton').disabled = false;
        document.getElementById('selected_protocol').value = protocol;
    }
    
    function confirmAndSubmit() {
        const selectedProtocol = document.getElementById('selected_protocol').value;
        if (!selectedProtocol || !calculatedProtocols[selectedProtocol]) {
            alert('Por favor, selecione um protocolo.');
            return;
        }
        const protocol = calculatedProtocols[selectedProtocol];
        document.querySelector('input[name="body_fat"]').value = protocol.fatPct.toFixed(2);
        document.querySelector('input[name="muscle_mass"]').value = protocol.leanMass.toFixed(2);
        closeProtocolModal();
        document.getElementById('measurementForm').submit();
    }
    
    function closeProtocolModal() {
        document.getElementById('protocolModal').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');
        document.querySelectorAll('.protocol-card').forEach(card => {
            card.classList.remove('border-teal-500', 'bg-teal-900/20');
            card.classList.add('border-zinc-700');
        });
        document.getElementById('confirmButton').disabled = true;
        document.getElementById('selected_protocol').value = '';
    }

    function setupEditImageLivePreview(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        let objectUrl = null;

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file || !file.type.startsWith('image/')) {
                return;
            }

            const container = input.closest('.space-y-1');
            if (!container) return;

            const existingSvg = container.querySelector('svg');
            if (existingSvg) {
                existingSvg.classList.add('hidden');
            }

            let previewImage = container.querySelector('img');
            if (!previewImage) {
                previewImage = document.createElement('img');
                previewImage.className = 'mx-auto h-32 object-cover rounded-md mb-2';
                container.insertBefore(previewImage, container.firstChild);
            }

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }

            objectUrl = URL.createObjectURL(file);
            previewImage.src = objectUrl;
        });
    }

    setupEditImageLivePreview('photo_front');
    setupEditImageLivePreview('photo_back');
    setupEditImageLivePreview('photo_side_right');
    setupEditImageLivePreview('photo_side_left');

    const editExtraInput = document.getElementById('photo_extra');
    if (editExtraInput) {
        editExtraInput.addEventListener('change', () => {
            const maxExtra = parseInt(editExtraInput.dataset.maxExtra || '0', 10);
            const selectedCount = (editExtraInput.files || []).length;

            if (selectedCount > maxExtra) {
                alert(`Limite excedido. Você pode adicionar no máximo ${maxExtra} imagem(ns) em "Mais" nesta avaliação.`);
                editExtraInput.value = '';
            }
        });
    }

    document.querySelectorAll('.replace-extra-input').forEach((input) => {
        let objectUrl = null;

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const index = input.dataset.index;
            const preview = document.getElementById(`extra_preview_${index}`);
            const removeCheckbox = document.getElementById(`remove_extra_${index}`);
            const card = document.getElementById(`extra_card_${index}`);
            const removeBtn = document.querySelector(`.remove-extra-btn[data-index="${index}"]`);

            if (!preview) return;

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }

            objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;

            if (removeCheckbox && removeCheckbox.checked) {
                removeCheckbox.checked = false;
                if (card) {
                    card.classList.remove('opacity-40');
                }
                if (removeBtn) {
                    removeBtn.textContent = 'Remover';
                    removeBtn.classList.remove('border-emerald-500', 'text-emerald-300', 'bg-emerald-900/30');
                    removeBtn.classList.add('border-red-500', 'text-red-300', 'bg-red-900/30');
                }
            }
        });
    });

    document.querySelectorAll('.remove-extra-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const index = button.dataset.index;
            const checkbox = document.getElementById(`remove_extra_${index}`);
            const card = document.getElementById(`extra_card_${index}`);

            if (!checkbox) return;

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                button.textContent = 'Desfazer';
                button.classList.remove('border-red-500', 'text-red-300', 'bg-red-900/30');
                button.classList.add('border-emerald-500', 'text-emerald-300', 'bg-emerald-900/30');
                if (card) {
                    card.classList.add('opacity-40');
                }
            } else {
                button.textContent = 'Remover';
                button.classList.remove('border-emerald-500', 'text-emerald-300', 'bg-emerald-900/30');
                button.classList.add('border-red-500', 'text-red-300', 'bg-red-900/30');
                if (card) {
                    card.classList.remove('opacity-40');
                }
            }
        });
    });
</script>
@endsection
