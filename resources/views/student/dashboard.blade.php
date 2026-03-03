@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Boas-vindas -->
    <div class="bg-zinc-900/55 shadow rounded-lg p-6 border border-teal-900/30">
        <h2 class="text-2xl font-bold text-stone-100">Olá, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-stone-300">Vamos manter o foco hoje?</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card de Treino -->
        <div class="bg-zinc-900/55 shadow rounded-lg overflow-hidden border-l-4 border-teal-600">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-stone-100 flex items-center">
                    <svg class="h-6 w-6 text-teal-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Treino Atual
                </h3>
                
                @if($activeWorkout)
                    <div class="mt-4">
                        <p class="text-xl font-bold text-stone-100">{{ $activeWorkout->name }}</p>
                        <p class="text-sm text-stone-300 mb-4">{{ $activeWorkout->goal }}</p>
                        
                        <a href="{{ route('workouts.show', $activeWorkout) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-stone-100 bg-teal-700 hover:bg-teal-800 focus:outline-none">
                            Ver Treino Completo
                        </a>
                    </div>
                @else
                    <p class="mt-4 text-stone-300">Você ainda não tem um treino ativo.</p>
                @endif
            </div>
        </div>

        <!-- Card de Dieta -->
        <div class="bg-zinc-900/55 shadow rounded-lg overflow-hidden border-l-4 border-teal-600">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-stone-100 flex items-center">
                    <svg class="h-6 w-6 text-teal-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Plano Alimentar
                </h3>
                
                @if($activeDiet)
                    <div class="mt-4">
                        <p class="text-xl font-bold text-stone-100">{{ $activeDiet->name }}</p>
                        <p class="text-sm text-stone-300 mb-4">{{ $activeDiet->goal }}</p>
                        
                        <a href="{{ route('diets.show', $activeDiet) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-stone-100 bg-teal-700 hover:bg-teal-800 focus:outline-none">
                            Ver Dieta Completa
                        </a>
                    </div>
                @else
                    <p class="mt-4 text-stone-300">Você ainda não tem uma dieta ativa.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Peso Atual e IMC -->
    @if($weightHistory->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Peso -->
        <div class="bg-zinc-900/55 shadow rounded-lg overflow-hidden border-l-4 border-teal-600 p-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg leading-6 font-medium text-stone-100 flex items-center">
                    <svg class="h-6 w-6 text-teal-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    Seu Peso Atual
                </h3>
                <p class="text-sm text-stone-300 mt-1">Última avaliação</p>
            </div>
            <div class="text-right">
                <span class="text-4xl font-bold text-stone-100">{{ $weightHistory->last()->weight }}</span>
                <span class="text-xl text-stone-300 font-medium">kg</span>
            </div>
        </div>

        <!-- IMC -->
        <div class="bg-zinc-900/55 shadow rounded-lg overflow-hidden border-l-4 border-teal-600 p-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg leading-6 font-medium text-stone-100 flex items-center">
                    <svg class="h-6 w-6 text-teal-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Seu IMC
                </h3>
                @php
                    $last = $weightHistory->last();
                    $h = $last->height;
                    $w = $last->weight;
                    $imc = 0;
                    $status = '-';
                    if ($h > 0) {
                        if ($h > 3) $h = $h / 100; // Converte CM para M
                        $imc = $w / ($h * $h);
                        
                        if($imc < 18.5) $status = 'Abaixo do Peso';
                        elseif($imc < 25) $status = 'Peso Normal';
                        elseif($imc < 30) $status = 'Sobrepeso';
                        else $status = 'Obesidade';
                    }
                @endphp
                <p class="text-sm text-stone-300 mt-1">{{ $status }}</p>
            </div>
            <div class="text-right">
                <span class="text-4xl font-bold text-stone-100">{{ $imc > 0 ? number_format($imc, 1) : '-' }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Detalhes da Última Avaliação -->
    @if($weightHistory->isNotEmpty())
    @php $lastMeasurement = $weightHistory->last(); @endphp
    <div class="bg-zinc-900/55 shadow rounded-lg p-6 border border-teal-900/30 space-y-6">
        <h3 class="text-xl font-bold text-stone-100 border-b border-teal-900/40 pb-3">📋 Detalhes da Última Avaliação ({{ \Carbon\Carbon::parse($lastMeasurement->date)->format('d/m/Y') }})</h3>
        
        <!-- Composição Corporal -->
        <div>
            <h4 class="text-sm font-bold text-teal-300 mb-3 border-b border-gray-700 pb-2">Composição Corporal</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm bg-gray-900/60 p-3 rounded-lg border border-gray-700">
                <div><p class="text-gray-400">Peso</p><p class="font-semibold text-white">{{ $lastMeasurement->weight ? $lastMeasurement->weight . ' kg' : '-' }}</p></div>
                <div><p class="text-gray-400">Altura</p><p class="font-semibold text-white">{{ $lastMeasurement->height ? $lastMeasurement->height . ' m' : '-' }}</p></div>
                <div><p class="text-gray-400">% Gordura</p><p class="font-semibold text-white">{{ $lastMeasurement->body_fat ? $lastMeasurement->body_fat . '%' : '-' }}</p></div>
                <div><p class="text-gray-400">Massa Musc.</p><p class="font-semibold text-white">{{ $lastMeasurement->muscle_mass ? $lastMeasurement->muscle_mass . ' kg' : '-' }}</p></div>
            </div>
        </div>
        
        <!-- Circunferências -->
        <div>
            <h4 class="text-sm font-bold text-teal-300 mb-3 border-b border-gray-700 pb-2">Circunferências (cm)</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2 text-sm">
                <div><span class="text-gray-400">Peito:</span> <span class="font-medium text-white">{{ $lastMeasurement->chest ?? '-' }}</span></div>
                <div><span class="text-gray-400">Braço Esq:</span> <span class="font-medium text-white">{{ $lastMeasurement->left_arm ?? '-' }}</span></div>
                <div><span class="text-gray-400">Braço Dir:</span> <span class="font-medium text-white">{{ $lastMeasurement->right_arm ?? '-' }}</span></div>
                <div><span class="text-gray-400">Cintura:</span> <span class="font-medium text-white">{{ $lastMeasurement->waist ?? '-' }}</span></div>
                <div><span class="text-gray-400">Abdômen:</span> <span class="font-medium text-white">{{ $lastMeasurement->abdomen ?? '-' }}</span></div>
                <div><span class="text-gray-400">Quadril:</span> <span class="font-medium text-white">{{ $lastMeasurement->hips ?? '-' }}</span></div>
                <div><span class="text-gray-400">Coxa Esq:</span> <span class="font-medium text-white">{{ $lastMeasurement->left_thigh ?? '-' }}</span></div>
                <div><span class="text-gray-400">Coxa Dir:</span> <span class="font-medium text-white">{{ $lastMeasurement->right_thigh ?? '-' }}</span></div>
                <div><span class="text-gray-400">Pant. Esq:</span> <span class="font-medium text-white">{{ $lastMeasurement->left_calf ?? '-' }}</span></div>
                <div><span class="text-gray-400">Pant. Dir:</span> <span class="font-medium text-white">{{ $lastMeasurement->right_calf ?? '-' }}</span></div>
            </div>
        </div>

        <!-- Anamnese & Saúde -->
        <div>
            <h4 class="text-sm font-bold text-teal-300 mb-3 border-b border-gray-700 pb-2">Anamnese & Saúde</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-gray-900/60 p-4 rounded-lg border border-gray-700">
                <div><span class="font-bold text-gray-200">Lesões:</span> <span class="text-gray-300">{{ $lastMeasurement->injuries ?? 'Nenhuma' }}</span></div>
                <div><span class="font-bold text-gray-200">Dores:</span> <span class="text-gray-300">{{ $lastMeasurement->pain_points ?? 'Nenhuma' }}</span></div>
                <div><span class="font-bold text-gray-200">Cirurgias:</span> <span class="text-gray-300">{{ $lastMeasurement->surgeries ?? 'Nenhuma' }}</span></div>
                <div><span class="font-bold text-gray-200">Medicamentos:</span> <span class="text-gray-300">{{ $lastMeasurement->medications ?? 'Nenhum' }}</span></div>
            </div>
        </div>

        <!-- Classificação do Percentual de Gordura -->
        @php
            $dashboardGenderValue = strtolower((string) (auth()->user()->gender ?? ''));
            $dashboardIsMaleGender = str_contains($dashboardGenderValue, 'masc') || $dashboardGenderValue === 'm' || $dashboardGenderValue === 'male';
        @endphp
        <div>
            <h4 class="text-sm font-bold text-teal-300 mb-3 border-b border-gray-700 pb-2">📋 Classificação do Percentual de Gordura</h4>

            <!-- Tabela Mulheres -->
            @if(!$dashboardIsMaleGender)
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-teal-950/30 border-b border-teal-800/40">
                            <th class="border border-gray-700 px-2 py-2 text-left font-bold text-white">Classificação</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">18-25</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">26-35</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">36-45</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">46-55</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">56-65</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300">
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-200">Competitivo</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 12%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Excelente</td><td class="border border-gray-700 px-2 py-2 text-center">13 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">14 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">16 a 19%</td><td class="border border-gray-700 px-2 py-2 text-center">17 a 21%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 22%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Bom</td><td class="border border-gray-700 px-2 py-2 text-center">17 a 19%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">23 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 26%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Acima da Média</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 22%</td><td class="border border-gray-700 px-2 py-2 text-center">21 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 26%</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 28%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Média</td><td class="border border-gray-700 px-2 py-2 text-center">23 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td><td class="border border-gray-700 px-2 py-2 text-center">29 a 31%</td><td class="border border-gray-700 px-2 py-2 text-center">30 a 32%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Abaixo da Média</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 28%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td><td class="border border-gray-700 px-2 py-2 text-center">30 a 32%</td><td class="border border-gray-700 px-2 py-2 text-center">32 a 34%</td><td class="border border-gray-700 px-2 py-2 text-center">33 a 35%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Ruim</td><td class="border border-gray-700 px-2 py-2 text-center">29 a 31%</td><td class="border border-gray-700 px-2 py-2 text-center">31 a 33%</td><td class="border border-gray-700 px-2 py-2 text-center">33 a 36%</td><td class="border border-gray-700 px-2 py-2 text-center">35 a 38%</td><td class="border border-gray-700 px-2 py-2 text-center">36 a 38%</td></tr>
                        <tr class="border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-200">Muito Ruim</td><td class="border border-gray-700 px-2 py-2 text-center">&gt;31%</td><td class="border border-gray-700 px-2 py-2 text-center">&gt;33%</td><td class="border border-gray-700 px-2 py-2 text-center">&gt;36%</td><td class="border border-gray-700 px-2 py-2 text-center">&gt;38%</td><td class="border border-gray-700 px-2 py-2 text-center">&gt;38%</td></tr>
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Tabela Homens -->
            @if($dashboardIsMaleGender)
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-teal-950/30 border-b border-teal-800/40">
                            <th class="border border-gray-700 px-2 py-2 text-left font-bold text-white">Classificação</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">18-25</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">26-35</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">36-45</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">46-55</th>
                            <th class="border border-gray-700 px-2 py-2 text-center font-bold text-white">56-65</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-300">
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Competitivo</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Excelente</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 11%</td><td class="border border-gray-700 px-2 py-2 text-center">10 a 14%</td><td class="border border-gray-700 px-2 py-2 text-center">12 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">13 a 18%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Bom</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 10%</td><td class="border border-gray-700 px-2 py-2 text-center">12 a 15%</td><td class="border border-gray-700 px-2 py-2 text-center">16 a 18%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 21%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Acima da Média</td><td class="border border-gray-700 px-2 py-2 text-center">12 a 13%</td><td class="border border-gray-700 px-2 py-2 text-center">16 a 18%</td><td class="border border-gray-700 px-2 py-2 text-center">19 a 21%</td><td class="border border-gray-700 px-2 py-2 text-center">21 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">22 a 23%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Média</td><td class="border border-gray-700 px-2 py-2 text-center">14 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">21 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Abaixo da Média</td><td class="border border-gray-700 px-2 py-2 text-center">17 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">22 a 24%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 27%</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 27%</td></tr>
                        <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Ruim</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 24%</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 24%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td><td class="border border-gray-700 px-2 py-2 text-center">28 a 30%</td><td class="border border-gray-700 px-2 py-2 text-center">28 a 30%</td></tr>
                        <tr class="border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-gray-300">Muito Ruim</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 36%</td><td class="border border-gray-700 px-2 py-2 text-center">28 a 36%</td><td class="border border-gray-700 px-2 py-2 text-center">30 a 39%</td><td class="border border-gray-700 px-2 py-2 text-center">32 a 38%</td><td class="border border-gray-700 px-2 py-2 text-center">32 a 38%</td></tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Observações -->
        <div>
            <h4 class="text-sm font-bold text-teal-300 mb-1">Observações Gerais</h4>
            <p class="text-sm text-gray-300 italic bg-gray-900/60 p-2 rounded-lg border border-gray-700">{{ $lastMeasurement->notes ?? 'Sem observações' }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
