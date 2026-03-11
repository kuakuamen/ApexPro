@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div x-data="{ activeTab: 'overview', showResetPassword: false }">
    
    <!-- Cabeçalho do Perfil -->
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="p-6 sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex sm:items-center">
                <div class="flex-shrink-0 relative mx-auto sm:mx-0">
                    <div class="h-20 w-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-3xl shadow-lg">
                        {{ substr($student->name, 0, 1) }}
                    </div>
                    <span class="absolute bottom-0 right-0 h-5 w-5 rounded-full border-2 border-gray-800 {{ $student->is_active ? 'bg-green-500' : 'bg-red-500' }}" title="{{ $student->is_active ? 'Ativo' : 'Inativo' }}"></span>
                </div>
                <div class="mt-4 text-center sm:mt-0 sm:ml-5 sm:text-left">
                    <h1 class="text-2xl font-bold text-white">{{ $student->name }}</h1>
                    <div class="flex items-center justify-center sm:justify-start space-x-2">
                        <p class="text-sm font-medium text-gray-300">{{ $student->email }}</p>
                        @if($student->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone) }}" target="_blank" class="text-green-400 hover:text-green-300" title="Conversar no WhatsApp">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </a>
                        @endif
                    </div>
                    <div class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->is_active ? 'bg-green-600/20 text-green-400' : 'bg-red-600/20 text-red-400' }}">
                            {{ $student->is_active ? 'Acesso Ativo' : 'Acesso Bloqueado' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3 sm:mt-0">
                <a href="{{ route('personal.measurements.create', $student) }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-all duration-300">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Nova Avaliação
                </a>
                <a href="{{ route('personal.students.edit', $student) }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-lg text-indigo-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-all duration-300" title="Editar Perfil">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Editar
                </a>
                <a href="{{ route('workouts.create') }}?student_id={{ $student->id }}" class="inline-flex justify-center items-center px-4 py-2 border border-indigo-600 shadow-sm text-sm font-medium rounded-lg text-indigo-300 bg-gray-800/60 hover:bg-gray-700/70 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-all duration-300">
                    Criar Treino
                </a>
                <button type="button" @click="showResetPassword = true" class="inline-flex justify-center items-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-lg text-gray-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-all duration-300">
                    Reset Senha
                </button>
                
                <!-- Menu de Ações Extras (Dropdown ou Botão Simples) -->
                <form action="{{ route('personal.students.toggle-status', $student) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-lg {{ $student->is_active ? 'text-red-400 bg-gray-700 hover:bg-red-600/20 hover:text-red-300' : 'text-green-400 bg-gray-700 hover:bg-green-600/20 hover:text-green-300' }} transition-all duration-300">
                        {{ $student->is_active ? 'Bloquear' : 'Desbloquear' }}
                    </button>
                </form>
            </div>

        </div>
        <!-- Navegação de Abas -->
        <div class="border-t border-gray-700">
            <nav class="-mb-px flex" aria-label="Tabs">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    Visão Geral
                </button>
                <button @click="activeTab = 'workouts'" 
                        :class="activeTab === 'workouts' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    Treinos
                </button>
                <button @click="activeTab = 'diets'" 
                        :class="activeTab === 'diets' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    Dietas
                </button>
                <button @click="activeTab = 'measurements'" 
                        :class="activeTab === 'measurements' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    Histórico Avaliações
                </button>
            </nav>
        </div>
    </div>

    <div x-show="showResetPassword"
         x-cloak
         x-transition.opacity
         @keydown.escape.window="showResetPassword = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @click.self="showResetPassword = false">
        <form action="{{ route('personal.students.reset-password', $student) }}" method="POST" class="w-full max-w-md bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-4 shadow-2xl">
            @csrf
            @method('PATCH')
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-white">Redefinir senha do aluno</h3>
                <button type="button" @click="showResetPassword = false" class="text-gray-400 hover:text-white">✕</button>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Nova senha</label>
                <input type="password" name="password" required minlength="6" class="w-full rounded-lg border border-gray-600 bg-gray-900 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Digite a nova senha">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Confirmar nova senha</label>
                <input type="password" name="password_confirmation" required minlength="6" class="w-full rounded-lg border border-gray-600 bg-gray-900 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Repita a nova senha">
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" @click="showResetPassword = false" class="px-3 py-2 text-xs rounded-md border border-gray-600 text-gray-300 hover:bg-gray-700">Cancelar</button>
                <button type="submit" class="px-3 py-2 text-xs rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Salvar senha</button>
            </div>
        </form>
    </div>

    <!-- Conteúdo das Abas -->
    <div class="mt-6">
        
        <!-- Aba: Visão Geral -->
        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card de Peso Recente -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-700 bg-gray-800/30">
                        <h3 class="text-lg leading-6 font-bold text-white">Últimas Pesagens</h3>
                    </div>
                    <ul class="divide-y divide-gray-700">
                        @forelse($measurements->take(5) as $measurement)
                            <li class="px-4 py-4 flex justify-between items-center hover:bg-gray-700/30 transition-colors">
                                <span class="text-sm text-gray-300">{{ $measurement->date->format('d/m/Y') }}</span>
                                <div class="flex items-center">
                                    <span class="text-sm font-bold text-white">{{ $measurement->weight }} kg</span>
                                    @if($measurement->body_fat)
                                        <span class="text-xs text-gray-400 ml-2">({{ $measurement->body_fat }}% gordura)</span>
                                    @endif
                                    <button @click="$dispatch('open-measurement-modal', { 
                                        date: '{{ $measurement->date->format('d/m/Y') }}',
                                        weight: '{{ $measurement->weight }}',
                                        height: '{{ $measurement->height }}',
                                        body_fat: '{{ $measurement->body_fat }}',
                                        muscle_mass: '{{ $measurement->muscle_mass }}',
                                        chest: '{{ $measurement->chest }}',
                                        left_arm: '{{ $measurement->left_arm }}',
                                        right_arm: '{{ $measurement->right_arm }}',
                                        waist: '{{ $measurement->waist }}',
                                        abdomen: '{{ $measurement->abdomen }}',
                                        hips: '{{ $measurement->hips }}',
                                        left_thigh: '{{ $measurement->left_thigh }}',
                                        right_thigh: '{{ $measurement->right_thigh }}',
                                        left_calf: '{{ $measurement->left_calf }}',
                                        right_calf: '{{ $measurement->right_calf }}',
                                        injuries: '{{ addslashes($measurement->injuries ?? 'Nenhuma') }}',
                                        medications: '{{ addslashes($measurement->medications ?? 'Nenhum') }}',
                                        surgeries: '{{ addslashes($measurement->surgeries ?? 'Nenhuma') }}',
                                        pain_points: '{{ addslashes($measurement->pain_points ?? 'Nenhuma') }}',
                                        habits: '{{ addslashes($measurement->habits ?? 'Não informado') }}',
                                        goal: '{{ addslashes($measurement->goal ?? 'Não informado') }}',
                                        notes: '{{ addslashes($measurement->notes ?? 'Sem observações') }}',
                                        photo_front: '{{ $measurement->photo_front ? '/' . ltrim(route('measurement.photo', [$measurement->id, 'front'], false), '/') . '?v=' . ($measurement->updated_at?->timestamp ?? time()) : '' }}',
                                        photo_side: '{{ $measurement->photo_side ? '/' . ltrim(route('measurement.photo', [$measurement->id, 'side'], false), '/') . '?v=' . ($measurement->updated_at?->timestamp ?? time()) : '' }}',
                                        photo_back: '{{ $measurement->photo_back ? '/' . ltrim(route('measurement.photo', [$measurement->id, 'back'], false), '/') . '?v=' . ($measurement->updated_at?->timestamp ?? time()) : '' }}',
                                        guedes_density: '{{ $measurement->guedes_density }}',
                                        guedes_fat_pct: '{{ $measurement->guedes_fat_pct }}',
                                        guedes_fat_mass: '{{ $measurement->guedes_fat_mass }}',
                                        guedes_lean_mass: '{{ $measurement->guedes_lean_mass }}',
                                        pollock3_density: '{{ $measurement->pollock3_density }}',
                                        pollock3_fat_pct: '{{ $measurement->pollock3_fat_pct }}',
                                        pollock3_fat_mass: '{{ $measurement->pollock3_fat_mass }}',
                                        pollock3_lean_mass: '{{ $measurement->pollock3_lean_mass }}',
                                        pollock7_density: '{{ $measurement->pollock7_density }}',
                                        pollock7_fat_pct: '{{ $measurement->pollock7_fat_pct }}',
                                        pollock7_fat_mass: '{{ $measurement->pollock7_fat_mass }}',
                                        pollock7_lean_mass: '{{ $measurement->pollock7_lean_mass }}'
                                    })" class="text-gray-300 hover:text-white ml-3 transition-colors" title="Ver Detalhes">
                                        <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-8 text-sm text-gray-400 text-center">Nenhuma avaliação registrada ainda.</li>
                        @endforelse
                    </ul>
                    <div class="bg-gray-800/30 px-4 py-3 text-right">
                        <button @click="activeTab = 'measurements'" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Ver histórico completo &rarr;</button>
                    </div>
                </div>

                <!-- Resumo Rápido -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Status Atual</h3>
                    @if($measurements->isNotEmpty())
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-300">Peso Atual</dt>
                                <dd class="mt-1 text-2xl font-semibold text-white">{{ $measurements->first()->weight }} kg</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-300">Altura</dt>
                                <dd class="mt-1 text-2xl font-semibold text-white">{{ $measurements->first()->height ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-300">IMC (Estimado)</dt>
                                <dd class="mt-1 text-2xl font-semibold text-white">
                                    @php
                                        $h = $measurements->first()->height;
                                        $w = $measurements->first()->weight;
                                        $imc = 0;
                                        if ($h > 0) {
                                            // Se altura for maior que 3, assume que é CM e converte para Metros
                                            if ($h > 3) {
                                                $h = $h / 100;
                                            }
                                            $imc = $w / ($h * $h);
                                        }
                                    @endphp
                                    
                                    @if($imc > 0)
                                        {{ number_format($imc, 1) }}
                                        <span class="text-xs font-normal text-gray-400 ml-1">
                                            @if($imc < 18.5) (Abaixo)
                                            @elseif($imc < 25) (Normal)
                                            @elseif($imc < 30) (Sobrepeso)
                                            @else (Obesidade)
                                            @endif
                                        </span>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-gray-400">Sem dados suficientes.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aba: Treinos -->
        <div x-show="activeTab === 'workouts'" x-cloak>
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
                <ul class="divide-y divide-gray-700">
                    @forelse($workouts as $workout)
                        <li>
                            <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-700/30 transition-colors">
                                <a href="{{ route('workouts.show', $workout) }}" class="flex-1 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-indigo-400">{{ $workout->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $workout->goal ?? 'Sem objetivo definido' }}</p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $workout->is_active ? 'bg-green-600/20 text-green-400' : 'bg-gray-600/20 text-gray-400' }}">
                                            {{ $workout->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </a>
                                <div class="ml-4">
                                    <a href="{{ route('workouts.edit', $workout) }}" class="text-gray-400 hover:text-indigo-400 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-gray-400">Nenhum treino criado.</li>
                    @endforelse
                </ul>
                <div class="bg-gray-800/30 px-4 py-3 text-right sm:px-6">
                    <a href="{{ route('workouts.create') }}?student_id={{ $student->id }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Criar novo treino &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Aba: Dietas -->
        <div x-show="activeTab === 'diets'" x-cloak>
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
                <ul class="divide-y divide-gray-700">
                    @forelse($diets as $diet)
                        <li>
                            <a href="{{ route('diets.show', $diet) }}" class="block hover:bg-gray-700/30 transition-colors">
                                <div class="px-4 py-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-green-400">{{ $diet->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $diet->goal }}</p>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $diet->is_active ? 'bg-green-600/20 text-green-400' : 'bg-gray-600/20 text-gray-400' }}">
                                        {{ $diet->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-gray-400">Nenhuma dieta atribuída.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Aba: Histórico de Avaliações (CRUD Completo) -->
        <div x-show="activeTab === 'measurements'" x-cloak>
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-700 sm:px-6 flex justify-between items-center bg-gray-800/30">
                    <h3 class="text-lg leading-6 font-bold text-white">Histórico Completo</h3>
                    <a href="{{ route('personal.measurements.create', $student) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-all duration-300">
                        + Nova Medida
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Data</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Peso</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Gordura</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Massa Muscular</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Fotos</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Ações</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800/30 divide-y divide-gray-700">
                            @forelse($measurements as $measurement)
                                <tr class="hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-white">
                                        {{ $measurement->date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $measurement->weight }} kg
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $measurement->body_fat ? $measurement->body_fat . '%' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $measurement->muscle_mass ? $measurement->muscle_mass . ' kg' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        @php
                                            $photoItems = [];

                                            if ($measurement->photo_front) {
                                                $photoItems[] = [
                                                    'label' => 'Frente',
                                                    'url' => route('measurement.photo', [$measurement->id, 'front']) . '?v=' . ($measurement->updated_at?->timestamp ?? time()),
                                                ];
                                            }

                                            if ($measurement->photo_back) {
                                                $photoItems[] = [
                                                    'label' => 'Costas',
                                                    'url' => route('measurement.photo', [$measurement->id, 'back']) . '?v=' . ($measurement->updated_at?->timestamp ?? time()),
                                                ];
                                            }

                                            if ($measurement->photo_side_right || $measurement->photo_side) {
                                                $photoItems[] = [
                                                    'label' => 'Lado D',
                                                    'url' => route('measurement.photo', [$measurement->id, 'side_right']) . '?v=' . ($measurement->updated_at?->timestamp ?? time()),
                                                ];
                                            }

                                            if ($measurement->photo_side_left) {
                                                $photoItems[] = [
                                                    'label' => 'Lado E',
                                                    'url' => route('measurement.photo', [$measurement->id, 'side_left']) . '?v=' . ($measurement->updated_at?->timestamp ?? time()),
                                                ];
                                            }

                                            if (is_array($measurement->extra_photos)) {
                                                foreach ($measurement->extra_photos as $index => $extraPhotoPath) {
                                                    $photoItems[] = [
                                                        'label' => 'Extra ' . ($index + 1),
                                                        'url' => route('measurement.photo.extra', [$measurement->id, $index]) . '?v=' . ($measurement->updated_at?->timestamp ?? time()),
                                                    ];
                                                }
                                            }

                                            $photoModalPayload = [
                                                'date' => $measurement->date->format('d/m/Y'),
                                                'photos' => $photoItems,
                                            ];
                                        @endphp

                                        @if(count($photoItems) > 0)
                                            @php $photoCount = count($photoItems); @endphp
                                            <script type="application/json" id="photos-payload-{{ $measurement->id }}">@json($photoModalPayload)</script>
                                            <button
                                                type="button"
                                                onclick="openMeasurementPhotosModal({{ $measurement->id }})"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-zinc-600 text-indigo-300 bg-zinc-800/60 hover:bg-zinc-700/80 hover:border-indigo-500 transition-colors"
                                                title="Ver {{ $photoCount }} imagem(ns)"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="sr-only">Ver imagens</span>
                                            </button>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botão Ver Detalhes (Modal) -->
                                        <button @click="$dispatch('open-measurement-modal', { 
                                            date: '{{ $measurement->date->format('d/m/Y') }}',
                                            weight: '{{ $measurement->weight }}',
                                            height: '{{ $measurement->height }}',
                                            body_fat: '{{ $measurement->body_fat }}',
                                            muscle_mass: '{{ $measurement->muscle_mass }}',
                                            chest: '{{ $measurement->chest }}',
                                            left_arm: '{{ $measurement->left_arm }}',
                                            right_arm: '{{ $measurement->right_arm }}',
                                            waist: '{{ $measurement->waist }}',
                                            abdomen: '{{ $measurement->abdomen }}',
                                            hips: '{{ $measurement->hips }}',
                                            left_thigh: '{{ $measurement->left_thigh }}',
                                            right_thigh: '{{ $measurement->right_thigh }}',
                                            left_calf: '{{ $measurement->left_calf }}',
                                            right_calf: '{{ $measurement->right_calf }}',
                                            injuries: '{{ addslashes($measurement->injuries ?? 'Nenhuma') }}',
                                            medications: '{{ addslashes($measurement->medications ?? 'Nenhum') }}',
                                            surgeries: '{{ addslashes($measurement->surgeries ?? 'Nenhuma') }}',
                                            pain_points: '{{ addslashes($measurement->pain_points ?? 'Nenhuma') }}',
                                            habits: '{{ addslashes($measurement->habits ?? 'Não informado') }}',
                                            goal: '{{ addslashes($measurement->goal ?? 'Não informado') }}',
                                            notes: '{{ addslashes($measurement->notes ?? 'Sem observações') }}',
                                            photo_front: '{{ $measurement->photo_front ? '/' . ltrim(route('measurement.photo', [$measurement->id, 'front'], false), '/') . '?v=' . ($measurement->updated_at?->timestamp ?? time()) : '' }}',
                                            photo_side: '{{ $measurement->photo_side ? '/' . ltrim(route('measurement.photo', [$measurement->id, 'side'], false), '/') . '?v=' . ($measurement->updated_at?->timestamp ?? time()) : '' }}',
                                            photo_back: '{{ $measurement->photo_back ? '/' . ltrim(route('measurement.photo', [$measurement->id, 'back'], false), '/') . '?v=' . ($measurement->updated_at?->timestamp ?? time()) : '' }}',
                                            guedes_density: '{{ $measurement->guedes_density }}',
                                            guedes_fat_pct: '{{ $measurement->guedes_fat_pct }}',
                                            guedes_fat_mass: '{{ $measurement->guedes_fat_mass }}',
                                            guedes_lean_mass: '{{ $measurement->guedes_lean_mass }}',
                                            pollock3_density: '{{ $measurement->pollock3_density }}',
                                            pollock3_fat_pct: '{{ $measurement->pollock3_fat_pct }}',
                                            pollock3_fat_mass: '{{ $measurement->pollock3_fat_mass }}',
                                            pollock3_lean_mass: '{{ $measurement->pollock3_lean_mass }}',
                                            pollock7_density: '{{ $measurement->pollock7_density }}',
                                            pollock7_fat_pct: '{{ $measurement->pollock7_fat_pct }}',
                                            pollock7_fat_mass: '{{ $measurement->pollock7_fat_mass }}',
                                            pollock7_lean_mass: '{{ $measurement->pollock7_lean_mass }}'
                                        })" class="text-gray-300 hover:text-white mr-4 transition-colors" title="Ver Detalhes">
                                            <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <a href="{{ route('personal.measurements.edit', $measurement) }}" class="text-indigo-400 hover:text-indigo-300 mr-4 transition-colors" title="Editar">
                                            <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                        Nenhuma avaliação registrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal de Detalhes da Avaliação -->
    <div x-data="{ open: false, data: {} }" 
         @open-measurement-modal.window="open = true; data = $event.detail"
         x-show="open" 
         class="fixed inset-0 overflow-y-auto" 
         style="z-index: 5000; display: none;"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black/80 transition-opacity" 
                 style="z-index: 1;"
                 aria-hidden="true" 
                 @click="open = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Content -->
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-zinc-900 border border-zinc-700 rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative"
                 style="z-index: 10;">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-teal-900/30 border border-teal-800/40">
                        <svg class="h-6 w-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-bold text-stone-100" id="modal-title">
                            Detalhes da Avaliação (<span x-text="data.date"></span>)
                        </h3>
                        
                        <div class="mt-4 text-left space-y-6">
                            <!-- Medidas Básicas & Corporais -->
                            <div>
                                <h4 class="text-sm font-bold text-teal-300 mb-2 border-b border-zinc-700 pb-1">Composição Corporal</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-zinc-800/60 p-3 rounded-lg border border-zinc-700">
                                    <div><p class="text-xs text-stone-400">Peso</p><p class="font-semibold text-stone-100" x-text="data.weight + ' kg'"></p></div>
                                    <div><p class="text-xs text-stone-400">Altura</p><p class="font-semibold text-stone-100" x-text="data.height ? data.height + ' m' : '-'"></p></div>
                                    <div><p class="text-xs text-stone-400">% Gordura</p><p class="font-semibold text-stone-100" x-text="data.body_fat ? data.body_fat + '%' : '-'"></p></div>
                                    <div><p class="text-xs text-stone-400">Massa Musc.</p><p class="font-semibold text-stone-100" x-text="data.muscle_mass ? data.muscle_mass + ' kg' : '-'"></p></div>
                                </div>
                            </div>

                            <!-- Circunferências -->
                            <div>
                                <h4 class="text-sm font-bold text-teal-300 mb-2 border-b border-zinc-700 pb-1">Circunferências (cm)</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2 text-sm">
                                    <div><span class="text-stone-400">Peito:</span> <span class="font-medium text-stone-100" x-text="data.chest || '-'"></span></div>
                                    <div><span class="text-stone-400">Braço Esq:</span> <span class="font-medium text-stone-100" x-text="data.left_arm || '-'"></span></div>
                                    <div><span class="text-stone-400">Braço Dir:</span> <span class="font-medium text-stone-100" x-text="data.right_arm || '-'"></span></div>
                                    <div><span class="text-stone-400">Cintura:</span> <span class="font-medium text-stone-100" x-text="data.waist || '-'"></span></div>
                                    <div><span class="text-stone-400">Abdômen:</span> <span class="font-medium text-stone-100" x-text="data.abdomen || '-'"></span></div>
                                    <div><span class="text-stone-400">Quadril:</span> <span class="font-medium text-stone-100" x-text="data.hips || '-'"></span></div>
                                    <div><span class="text-stone-400">Coxa Esq:</span> <span class="font-medium text-stone-100" x-text="data.left_thigh || '-'"></span></div>
                                    <div><span class="text-stone-400">Coxa Dir:</span> <span class="font-medium text-stone-100" x-text="data.right_thigh || '-'"></span></div>
                                    <div><span class="text-stone-400">Pant. Esq:</span> <span class="font-medium text-stone-100" x-text="data.left_calf || '-'"></span></div>
                                    <div><span class="text-stone-400">Pant. Dir:</span> <span class="font-medium text-stone-100" x-text="data.right_calf || '-'"></span></div>
                                </div>
                            </div>

                            <!-- Anamnese -->
                            <div>
                                <h4 class="text-sm font-bold text-teal-300 mb-2 border-b border-zinc-700 pb-1">Anamnese & Saúde</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-zinc-800/60 p-3 rounded-lg border border-zinc-700">
                                    <div><span class="font-bold text-stone-200">Lesões:</span> <span x-text="data.injuries" class="text-stone-300"></span></div>
                                    <div><span class="font-bold text-stone-200">Dores:</span> <span x-text="data.pain_points" class="text-stone-300"></span></div>
                                    <div><span class="font-bold text-stone-200">Cirurgias:</span> <span x-text="data.surgeries" class="text-stone-300"></span></div>
                                    <div><span class="font-bold text-stone-200">Medicamentos:</span> <span x-text="data.medications" class="text-stone-300"></span></div>
                                </div>
                            </div>

                            <!-- Cálculos de Composição Corporal -->
                            <div x-show="data.guedes_fat_pct || data.pollock3_fat_pct || data.pollock7_fat_pct">
                                <h4 class="text-sm font-bold text-teal-300 mb-2 border-b border-zinc-700 pb-1">📊 Resultados dos Cálculos de Composição</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- GUEDES -->
                                    <div x-show="data.guedes_fat_pct" class="bg-zinc-800/60 border border-zinc-700 rounded-lg p-3">
                                        <h5 class="text-xs font-bold text-teal-300 mb-2">GUEDES (3 Dobras)</h5>
                                        <div class="text-xs space-y-1">
                                            <div><span class="text-stone-400">Densidade:</span> <span class="font-mono font-bold text-stone-100" x-text="data.guedes_density ? parseFloat(data.guedes_density).toFixed(4) : '-'"></span> g/ml</div>
                                            <div><span class="text-stone-400">% Gordura:</span> <span class="font-mono font-bold text-teal-200" x-text="data.guedes_fat_pct ? parseFloat(data.guedes_fat_pct).toFixed(2) : '-'"></span> %</div>
                                            <div><span class="text-stone-400">Massa Gordura:</span> <span class="font-mono font-bold text-stone-100" x-text="data.guedes_fat_mass ? parseFloat(data.guedes_fat_mass).toFixed(2) : '-'"></span> kg</div>
                                            <div><span class="text-stone-400">Massa Livre:</span> <span class="font-mono font-bold text-stone-100" x-text="data.guedes_lean_mass ? parseFloat(data.guedes_lean_mass).toFixed(2) : '-'"></span> kg</div>
                                        </div>
                                    </div>

                                    <!-- POLLOCK 3 -->
                                    <div x-show="data.pollock3_fat_pct" class="bg-zinc-800/60 border border-zinc-700 rounded-lg p-3">
                                        <h5 class="text-xs font-bold text-teal-300 mb-2">POLLOCK 3</h5>
                                        <div class="text-xs space-y-1">
                                            <div><span class="text-stone-400">Densidade:</span> <span class="font-mono font-bold text-stone-100" x-text="data.pollock3_density ? parseFloat(data.pollock3_density).toFixed(4) : '-'"></span> g/ml</div>
                                            <div><span class="text-stone-400">% Gordura:</span> <span class="font-mono font-bold text-teal-200" x-text="data.pollock3_fat_pct ? parseFloat(data.pollock3_fat_pct).toFixed(2) : '-'"></span> %</div>
                                            <div><span class="text-stone-400">Massa Gordura:</span> <span class="font-mono font-bold text-stone-100" x-text="data.pollock3_fat_mass ? parseFloat(data.pollock3_fat_mass).toFixed(2) : '-'"></span> kg</div>
                                            <div><span class="text-stone-400">Massa Livre:</span> <span class="font-mono font-bold text-stone-100" x-text="data.pollock3_lean_mass ? parseFloat(data.pollock3_lean_mass).toFixed(2) : '-'"></span> kg</div>
                                        </div>
                                    </div>

                                    <!-- POLLOCK 7 -->
                                    <div x-show="data.pollock7_fat_pct" class="bg-zinc-800/60 border border-zinc-700 rounded-lg p-3">
                                        <h5 class="text-xs font-bold text-teal-300 mb-2">POLLOCK 7 (Mais Preciso)</h5>
                                        <div class="text-xs space-y-1">
                                            <div><span class="text-stone-400">Densidade:</span> <span class="font-mono font-bold text-stone-100" x-text="data.pollock7_density ? parseFloat(data.pollock7_density).toFixed(4) : '-'"></span> g/ml</div>
                                            <div><span class="text-stone-400">% Gordura:</span> <span class="font-mono font-bold text-teal-200" x-text="data.pollock7_fat_pct ? parseFloat(data.pollock7_fat_pct).toFixed(2) : '-'"></span> %</div>
                                            <div><span class="text-stone-400">Massa Gordura:</span> <span class="font-mono font-bold text-stone-100" x-text="data.pollock7_fat_mass ? parseFloat(data.pollock7_fat_mass).toFixed(2) : '-'"></span> kg</div>
                                            <div><span class="text-stone-400">Massa Livre:</span> <span class="font-mono font-bold text-stone-100" x-text="data.pollock7_lean_mass ? parseFloat(data.pollock7_lean_mass).toFixed(2) : '-'"></span> kg</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de Referência de Classificação de Gordura -->
                            @php
                                $genderValue = strtolower((string) ($student->gender ?? ''));
                                $isMaleGender = str_contains($genderValue, 'masc') || $genderValue === 'm' || $genderValue === 'male';
                            @endphp
                            <div>
                                <h4 class="text-sm font-bold text-teal-300 mb-3 border-b border-zinc-700 pb-1">📋 Classificação do Percentual de Gordura</h4>

                                <!-- Tabela Mulheres -->
                                @if(!$isMaleGender)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-teal-950/30 border-b border-teal-800/40">
                                                <th class="border border-zinc-700 px-2 py-2 text-left font-bold text-stone-100">Classificação</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">18-25</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">26-35</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">36-45</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">46-55</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">56-65</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-stone-300">
                                            <tr class="border-b border-zinc-700 bg-zinc-800/40"><td class="border border-zinc-700 px-2 py-2 font-semibold text-stone-200">Competitivo</td><td class="border border-zinc-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-zinc-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-zinc-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-zinc-700 px-2 py-2 text-center">8 a 12%</td><td class="border border-zinc-700 px-2 py-2 text-center">8 a 12%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Excelente</td><td class="border border-gray-700 px-2 py-2 text-center">13 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">14 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">16 a 19%</td><td class="border border-gray-700 px-2 py-2 text-center">17 a 21%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 22%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Bom</td><td class="border border-gray-700 px-2 py-2 text-center">17 a 19%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">23 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 26%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Acima da Média</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 22%</td><td class="border border-gray-700 px-2 py-2 text-center">21 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 26%</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 28%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Média</td><td class="border border-gray-700 px-2 py-2 text-center">23 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td><td class="border border-gray-700 px-2 py-2 text-center">29 a 31%</td><td class="border border-gray-700 px-2 py-2 text-center">30 a 32%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Abaixo da Média</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 28%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td><td class="border border-gray-700 px-2 py-2 text-center">30 a 32%</td><td class="border border-gray-700 px-2 py-2 text-center">32 a 34%</td><td class="border border-gray-700 px-2 py-2 text-center">33 a 35%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Ruim</td><td class="border border-gray-700 px-2 py-2 text-center">29 a 31%</td><td class="border border-gray-700 px-2 py-2 text-center">31 a 33%</td><td class="border border-gray-700 px-2 py-2 text-center">33 a 36%</td><td class="border border-gray-700 px-2 py-2 text-center">35 a 38%</td><td class="border border-gray-700 px-2 py-2 text-center">36 a 38%</td></tr>
                                            <tr class="border-zinc-700 bg-zinc-800/20"><td class="border border-zinc-700 px-2 py-2 font-semibold text-stone-200">Muito Ruim</td><td class="border border-zinc-700 px-2 py-2 text-center">&gt;31%</td><td class="border border-zinc-700 px-2 py-2 text-center">&gt;33%</td><td class="border border-zinc-700 px-2 py-2 text-center">&gt;36%</td><td class="border border-zinc-700 px-2 py-2 text-center">&gt;38%</td><td class="border border-zinc-700 px-2 py-2 text-center">&gt;38%</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                <!-- Tabela Homens -->
                                @if($isMaleGender)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-teal-950/30 border-b border-teal-800/40">
                                                <th class="border border-zinc-700 px-2 py-2 text-left font-bold text-stone-100">Classificação</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">18-25</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">26-35</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">36-45</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">46-55</th>
                                                <th class="border border-zinc-700 px-2 py-2 text-center font-bold text-stone-100">56-65</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-stone-300">
                                            <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Competitivo</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Excelente</td><td class="border border-gray-700 px-2 py-2 text-center">4 a 6%</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 11%</td><td class="border border-gray-700 px-2 py-2 text-center">10 a 14%</td><td class="border border-gray-700 px-2 py-2 text-center">12 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">13 a 18%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Bom</td><td class="border border-gray-700 px-2 py-2 text-center">8 a 10%</td><td class="border border-gray-700 px-2 py-2 text-center">12 a 15%</td><td class="border border-gray-700 px-2 py-2 text-center">16 a 18%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 21%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Acima da Média</td><td class="border border-gray-700 px-2 py-2 text-center">12 a 13%</td><td class="border border-gray-700 px-2 py-2 text-center">16 a 18%</td><td class="border border-gray-700 px-2 py-2 text-center">19 a 21%</td><td class="border border-gray-700 px-2 py-2 text-center">21 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">22 a 23%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Média</td><td class="border border-gray-700 px-2 py-2 text-center">14 a 16%</td><td class="border border-gray-700 px-2 py-2 text-center">18 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">21 a 23%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Abaixo da Média</td><td class="border border-gray-700 px-2 py-2 text-center">17 a 20%</td><td class="border border-gray-700 px-2 py-2 text-center">22 a 24%</td><td class="border border-gray-700 px-2 py-2 text-center">24 a 25%</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 27%</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 27%</td></tr>
                                            <tr class="border-b border-gray-700 bg-gray-800/40"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Ruim</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 24%</td><td class="border border-gray-700 px-2 py-2 text-center">20 a 24%</td><td class="border border-gray-700 px-2 py-2 text-center">27 a 29%</td><td class="border border-gray-700 px-2 py-2 text-center">28 a 30%</td><td class="border border-gray-700 px-2 py-2 text-center">28 a 30%</td></tr>
                                            <tr class="border-gray-700 bg-gray-800/20"><td class="border border-gray-700 px-2 py-2 font-semibold text-slate-300">Muito Ruim</td><td class="border border-gray-700 px-2 py-2 text-center">26 a 36%</td><td class="border border-gray-700 px-2 py-2 text-center">28 a 36%</td><td class="border border-gray-700 px-2 py-2 text-center">30 a 39%</td><td class="border border-gray-700 px-2 py-2 text-center">32 a 38%</td><td class="border border-gray-700 px-2 py-2 text-center">32 a 38%</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>

                            <!-- Observações -->
                            <div>
                                <h4 class="text-sm font-bold text-teal-300 mb-1">Observações Gerais</h4>
                                <p class="text-sm text-stone-300 italic bg-zinc-800/60 p-2 rounded-lg border border-zinc-700" x-text="data.notes"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-zinc-600 shadow-sm px-4 py-2 bg-zinc-700 text-base font-medium text-stone-100 hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-teal-600 sm:text-sm transition-all duration-300" @click="open = false">
                        Fechar
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal de Fotos da Avaliação -->
        <div x-data="{ open: false, data: { date: '', photos: [] }, zoomOpen: false, zoomPhoto: null }"
            @open-measurement-photos-modal.window="open = true; data = $event.detail || { date: '', photos: [] }; zoomOpen = false; zoomPhoto = null"
                @keydown.escape.window="if (zoomOpen) { zoomOpen = false } else { open = false }"
         x-show="open"
         x-cloak
         class="fixed inset-0"
         style="z-index: 5100;"
         aria-labelledby="photos-modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/80" style="z-index: 1;" @click="open = false"></div>

        <div class="absolute inset-0 flex items-center justify-center p-2 sm:p-4" style="z-index: 10;">
            <div :class="zoomOpen ? 'opacity-35' : 'opacity-100'" class="relative w-full max-w-4xl h-[94vh] sm:h-auto sm:max-h-[90vh] bg-zinc-900 border border-zinc-700 rounded-xl p-3 sm:p-6 shadow-xl overflow-hidden flex flex-col transition-opacity duration-200">
                <div class="sticky top-0 z-10 bg-zinc-900 pb-3 mb-3 border-b border-zinc-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-stone-100" id="photos-modal-title">
                        Fotos da Avaliação (<span x-text="data.date"></span>)
                    </h3>
                    <button type="button" @click="open = false" class="px-3 py-1.5 rounded-md border border-zinc-600 text-sm text-gray-200 hover:bg-zinc-800">Fechar</button>
                </div>

                <div x-show="(data.photos || []).length > 0" class="flex-1 overflow-hidden pr-1">
                    <div class="grid" style="grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 0.35rem; align-content: start;">
                    <template x-for="(photo, idx) in (data.photos || [])" :key="idx">
                        <button type="button" @click="zoomPhoto = photo; zoomOpen = true" class="block w-full text-left bg-zinc-800/60 border border-zinc-700 rounded-md p-0.5 hover:border-indigo-500 transition-colors cursor-zoom-in" style="touch-action: manipulation;" :title="photo.label">
                            <img :src="photo.url" :alt="photo.label" class="w-full object-cover rounded-sm bg-zinc-950" style="aspect-ratio: 1 / 1; display: block;">
                        </button>
                    </template>
                    </div>
                </div>

                <p x-show="(data.photos || []).length === 0" class="text-sm text-gray-400">Nenhuma imagem disponível para esta avaliação.</p>

                <div class="sticky bottom-0 z-10 bg-zinc-900 pt-3 mt-3 border-t border-zinc-700">
                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-zinc-600 shadow-sm px-4 py-2 bg-zinc-700 text-base font-medium text-stone-100 hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-teal-600 sm:text-sm transition-all duration-300" @click="open = false">
                        Fechar
                    </button>
                </div>

                <!-- Zoom da Imagem (na própria tela) -->
                <div x-show="zoomOpen" x-cloak class="fixed inset-0" style="z-index: 5200;" @keydown.escape.window="zoomOpen = false"
                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-black/55" @click="zoomOpen = false"></div>
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <div class="w-full max-w-6xl"
                             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm text-indigo-300 font-medium" x-text="zoomPhoto?.label || 'Imagem'"></p>
                                <button type="button" @click="zoomOpen = false" class="text-gray-300 hover:text-white text-xl">✕</button>
                            </div>
                            <img :src="zoomPhoto?.url" :alt="zoomPhoto?.label || 'Imagem ampliada'" class="w-full max-h-[82vh] object-contain rounded-lg border border-zinc-700 bg-zinc-950">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openMeasurementPhotosModal(measurementId) {
            const payloadElement = document.getElementById(`photos-payload-${measurementId}`);
            if (!payloadElement) {
                return;
            }

            try {
                const payload = JSON.parse(payloadElement.textContent || '{}');
                window.dispatchEvent(new CustomEvent('open-measurement-photos-modal', { detail: payload }));
            } catch (error) {
                console.error('Erro ao abrir modal de fotos:', error);
            }
        }
    </script>
</div>
@endsection
