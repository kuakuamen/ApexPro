@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>

<div x-data="{ activeTab: 'overview', showResetPassword: false }">
    
    <!-- Cabeçalho do Perfil -->
    <div class="bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl mb-8 relative z-50">
        <div class="p-6 sm:p-8 md:flex md:items-start md:justify-between gap-6">
            <div class="flex items-center gap-6">
                <!-- Avatar -->
                <div class="relative flex-shrink-0">
                    <form action="{{ route('personal.students.photo', $student) }}" method="POST" enctype="multipart/form-data" id="student-photo-form" class="contents">
                        @csrf
                        @method('PATCH')
                        <input type="file" name="profile_photo" id="student-profile-photo-input" accept="image/*" class="hidden">
                    </form>

                    @if($student->profile_photo_url)
                        <img src="{{ $student->profile_photo_url }}" alt="Foto de perfil de {{ $student->name }}" class="h-20 w-20 sm:h-24 sm:w-24 rounded-full object-cover shadow-2xl ring-4 ring-gray-800/50">
                    @else
                        <div class="h-20 w-20 sm:h-24 sm:w-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-3xl sm:text-4xl shadow-2xl ring-4 ring-gray-800/50">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                    @endif

                    <button type="button" id="student-photo-trigger" class="absolute -top-1 -right-1 inline-flex h-9 w-9 items-center justify-center rounded-full border border-indigo-400/30 bg-gray-900/95 text-indigo-300 shadow-lg transition hover:border-indigo-300 hover:text-white hover:bg-indigo-600" title="Alterar foto do aluno">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.768-6.768a2.5 2.5 0 113.536 3.536L12.536 16.536A4 4 0 019.707 17.707L6 19l1.293-3.707A4 4 0 018.464 12.536z"></path></svg>
                    </button>
                    <div class="absolute bottom-1 right-1">
                        <span class="flex h-4 w-4 sm:h-5 sm:w-5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $student->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                            <span class="relative inline-flex rounded-full h-full w-full border-2 border-gray-800 {{ $student->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        </span>
                    </div>
                </div>
                
                <!-- Info -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">{{ $student->name }}</h1>
                    <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 text-sm text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            {{ $student->email }}
                        </span>
                        @if($student->phone)
                            <span class="hidden sm:inline text-gray-700">|</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone) }}" target="_blank" class="flex items-center gap-1.5 hover:text-green-400 transition-colors">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $student->phone) }}
                            </a>
                        @endif
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->is_active ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                            {{ $student->is_active ? 'Acesso Ativo' : 'Acesso Bloqueado' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="mt-8 md:mt-0 flex flex-wrap items-center gap-3 w-full md:w-auto" x-data="{ openActions: false }">
                <a href="{{ route('personal.measurements.create', $student) }}" class="w-full sm:w-auto justify-center inline-flex items-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Nova Avaliação
                </a>
                
                <a href="{{ route('workouts.create') }}?student_id={{ $student->id }}" class="flex-1 sm:flex-none justify-center inline-flex items-center px-4 py-2.5 rounded-xl border border-gray-600 hover:border-indigo-500 text-gray-300 hover:text-white bg-gray-800/50 hover:bg-gray-700/50 text-sm font-medium transition-all">
                    <svg class="mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Criar Treino
                </a>

                <!-- Dropdown Menu -->
                <div class="relative flex-shrink-0 z-50">
                    <button @click="openActions = !openActions" @click.away="openActions = false" class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-gray-600 hover:border-gray-500 text-gray-400 hover:text-white bg-gray-800/50 hover:bg-gray-700/50 transition-all">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                    </button>
                    
                    <div x-show="openActions" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 rounded-2xl shadow-xl bg-gray-800 border border-gray-700 ring-1 ring-black ring-opacity-5 divide-y divide-gray-700/50 z-50">
                        <div class="py-1">
                            <a href="{{ route('personal.students.edit', $student) }}" class="group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                <svg class="mr-3 h-5 w-5 text-gray-500 group-hover:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar Perfil
                            </a>
                            <button @click="showResetPassword = true; openActions = false" class="group flex w-full items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                <svg class="mr-3 h-5 w-5 text-gray-500 group-hover:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                Redefinir Senha
                            </button>
                        </div>
                        <div class="py-1">
                            <form action="{{ route('personal.students.toggle-status', $student) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm {{ $student->is_active ? 'text-red-400 hover:bg-red-500/10' : 'text-green-400 hover:bg-green-500/10' }}">
                                    @if($student->is_active)
                                        <svg class="mr-3 h-5 w-5 text-red-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        Bloquear Acesso
                                    @else
                                        <svg class="mr-3 h-5 w-5 text-green-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Desbloquear Acesso
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navegação de Abas -->
        <div class="bg-gray-800/30 border-t border-gray-700/50 px-6 sm:px-8 rounded-b-2xl">
            <nav class="-mb-px flex gap-6 sm:gap-8 overflow-x-auto hide-scrollbar" aria-label="Tabs">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="whitespace-nowrap py-4 border-b-2 font-medium text-sm transition-all duration-200">
                    Visão Geral
                </button>
                <button @click="activeTab = 'workouts'" 
                        :class="activeTab === 'workouts' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="whitespace-nowrap py-4 border-b-2 font-medium text-sm transition-all duration-200">
                    Treinos
                </button>
                <button @click="activeTab = 'diets'" 
                        :class="activeTab === 'diets' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="whitespace-nowrap py-4 border-b-2 font-medium text-sm transition-all duration-200">
                    Dietas
                </button>
                <button @click="activeTab = 'measurements'" 
                        :class="activeTab === 'measurements' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="whitespace-nowrap py-4 border-b-2 font-medium text-sm transition-all duration-200">
                    Histórico Avaliações
                </button>
                <button @click="activeTab = 'comparison'" 
                        :class="activeTab === 'comparison' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="whitespace-nowrap py-4 border-b-2 font-medium text-sm transition-all duration-200">
                    Comparativo
                </button>
                @if($aiAssessments->isNotEmpty())
                <button @click="activeTab = 'ai_assessments'"
                        :class="activeTab === 'ai_assessments' ? 'border-purple-500 text-purple-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                        class="whitespace-nowrap py-4 border-b-2 font-medium text-sm transition-all duration-200 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Avaliações IA
                    <span class="bg-purple-600/30 text-purple-300 text-xs rounded-full px-1.5 py-0.5">{{ $aiAssessments->count() }}</span>
                </button>
                @endif

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
    <div class="mt-6 relative z-0">
        
        <!-- Aba: Visão Geral -->
        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Card de Peso Recente -->
                <div class="lg:col-span-2 bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl overflow-hidden flex flex-col h-full">
                    <div class="px-6 py-5 border-b border-gray-700/50 bg-gray-800/30 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white tracking-tight flex items-center gap-2">
                            <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Últimas Pesagens
                        </h3>
                        <button @click="activeTab = 'measurements'" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                            Ver histórico <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                    <ul class="divide-y divide-gray-700/50 flex-1">
                        @forelse($measurements->take(5) as $measurement)
                            <li class="px-6 py-5 flex justify-between items-center hover:bg-gray-700/20 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full bg-gray-700/50 flex items-center justify-center text-gray-400 group-hover:bg-indigo-500/10 group-hover:text-indigo-400 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ $measurement->date->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $measurement->date->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-white">{{ $measurement->weight }} kg</p>
                                        @if($measurement->body_fat)
                                            <p class="text-xs text-gray-400">{{ $measurement->body_fat }}% gordura</p>
                                        @endif
                                    </div>
                                    
                                    <button @click="$dispatch('open-measurement-modal', { 
                                        date: '{{ $measurement->date->format('d/m/Y') }}',
                                        weight: '{{ $measurement->weight }}',
                                        height: '{{ $measurement->height }}',
                                        body_fat: '{{ $measurement->body_fat }}',
                                        muscle_mass: '{{ $measurement->muscle_mass }}',
                                        ombro: '{{ $measurement->ombro }}',
                                        torax: '{{ $measurement->torax }}',
                                        waist: '{{ $measurement->waist }}',
                                        abdomen: '{{ $measurement->abdomen }}',
                                        abdomen_inferior: '{{ $measurement->abdomen_inferior }}',
                                        hips: '{{ $measurement->hips }}',
                                        right_thigh_proximal: '{{ $measurement->right_thigh_proximal }}',
                                        right_thigh_medial: '{{ $measurement->right_thigh_medial }}',
                                        right_thigh_distal: '{{ $measurement->right_thigh_distal }}',
                                        left_thigh_proximal: '{{ $measurement->left_thigh_proximal }}',
                                        left_thigh_medial: '{{ $measurement->left_thigh_medial }}',
                                        left_thigh_distal: '{{ $measurement->left_thigh_distal }}',
                                        left_calf: '{{ $measurement->left_calf }}',
                                        right_calf: '{{ $measurement->right_calf }}',
                                        left_arm: '{{ $measurement->left_arm }}',
                                        right_arm: '{{ $measurement->right_arm }}',
                                        left_arm_contracted: '{{ $measurement->left_arm_contracted }}',
                                        right_arm_contracted: '{{ $measurement->right_arm_contracted }}',
                                        left_forearm: '{{ $measurement->left_forearm }}',
                                        right_forearm: '{{ $measurement->right_forearm }}',
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
                                    })" class="p-2 rounded-lg text-gray-500 hover:text-indigo-400 hover:bg-indigo-500/10 transition-all" title="Ver Detalhes">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-12 text-center">
                                <div class="mx-auto h-12 w-12 text-gray-500 mb-3">
                                    <svg class="h-full w-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </div>
                                <p class="text-gray-400 text-sm">Nenhuma avaliação registrada ainda.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Resumo Rápido -->
                <div class="bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl p-6 lg:p-8 h-fit">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Status Atual
                    </h3>
                    @if($measurements->isNotEmpty())
                        <div class="space-y-6">
                            <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-600/30">
                                <dt class="text-sm font-medium text-gray-400 mb-1">Peso Atual</dt>
                                <dd class="text-3xl font-bold text-white tracking-tight">{{ $measurements->first()->weight }} <span class="text-lg font-medium text-gray-500">kg</span></dd>
                            </div>
                            
                            <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-600/30">
                                <dt class="text-sm font-medium text-gray-400 mb-1">Altura</dt>
                                <dd class="text-3xl font-bold text-white tracking-tight">{{ $measurements->first()->height ?? '-' }} <span class="text-lg font-medium text-gray-500">m</span></dd>
                            </div>

                            <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-600/30">
                                <dt class="text-sm font-medium text-gray-400 mb-1">IMC (Estimado)</dt>
                                <dd class="flex items-baseline gap-2">
                                    <span class="text-3xl font-bold text-white tracking-tight">
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
                                    @else
                                        -
                                    @endif
                                    </span>
                                    
                                    @if($imc > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($imc < 18.5) bg-yellow-500/10 text-yellow-400
                                            @elseif($imc < 25) bg-green-500/10 text-green-400
                                            @elseif($imc < 30) bg-orange-500/10 text-orange-400
                                            @else bg-red-500/10 text-red-400
                                            @endif">
                                            @if($imc < 18.5) Abaixo do peso
                                            @elseif($imc < 25) Normal
                                            @elseif($imc < 30) Sobrepeso
                                            @else Obesidade
                                            @endif
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400 text-center py-8">Sem dados suficientes.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aba: Treinos -->
        <div x-show="activeTab === 'workouts'" x-cloak>
            <div class="bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl overflow-hidden">
                <ul class="divide-y divide-gray-700/50">
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
            <div class="bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl overflow-hidden">
                <ul class="divide-y divide-gray-700/50">
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
            <div class="bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-700/50 sm:px-8 flex justify-between items-center bg-gray-800/30">
                    <h3 class="text-lg leading-6 font-bold text-white tracking-tight">Histórico Completo</h3>
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
                                            ombro: '{{ $measurement->ombro }}',
                                            torax: '{{ $measurement->torax }}',
                                            waist: '{{ $measurement->waist }}',
                                            abdomen: '{{ $measurement->abdomen }}',
                                            abdomen_inferior: '{{ $measurement->abdomen_inferior }}',
                                            hips: '{{ $measurement->hips }}',
                                            right_thigh_proximal: '{{ $measurement->right_thigh_proximal }}',
                                            right_thigh_medial: '{{ $measurement->right_thigh_medial }}',
                                            right_thigh_distal: '{{ $measurement->right_thigh_distal }}',
                                            left_thigh_proximal: '{{ $measurement->left_thigh_proximal }}',
                                            left_thigh_medial: '{{ $measurement->left_thigh_medial }}',
                                            left_thigh_distal: '{{ $measurement->left_thigh_distal }}',
                                            left_calf: '{{ $measurement->left_calf }}',
                                            right_calf: '{{ $measurement->right_calf }}',
                                            left_arm: '{{ $measurement->left_arm }}',
                                            right_arm: '{{ $measurement->right_arm }}',
                                            left_arm_contracted: '{{ $measurement->left_arm_contracted }}',
                                            right_arm_contracted: '{{ $measurement->right_arm_contracted }}',
                                            left_forearm: '{{ $measurement->left_forearm }}',
                                            right_forearm: '{{ $measurement->right_forearm }}',
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

        <!-- Aba: Comparativo de Fotos (NOVA) -->
        <div x-show="activeTab === 'comparison'" x-cloak x-data="photoComparison()">
            @if($measurements->isEmpty())
                <div class="bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl p-8 text-center">
                    <p class="text-gray-400">Nenhuma avaliação encontrada para comparação.</p>
                </div>
            @else
                <div class="bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl p-6 sm:p-8">
                    <h3 class="text-lg font-medium text-white mb-6">Comparar Avaliações</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Select Left -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Avaliação 1 (Esquerda)</label>
                            <select x-model.number="leftId" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-700 text-white focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 sm:text-sm rounded-lg">
                                <template x-for="m in measurements" :key="m.id">
                                    <option :value="m.id" x-text="formatDate(m.date) + ' - ' + m.weight + 'kg'"></option>
                                </template>
                            </select>
                        </div>
                        <!-- Select Right -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Avaliação 2 (Direita)</label>
                            <select x-model.number="rightId" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-700 text-white focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 sm:text-sm rounded-lg">
                                <template x-for="m in measurements" :key="m.id">
                                    <option :value="m.id" x-text="formatDate(m.date) + ' - ' + m.weight + 'kg'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Side-by-Side Button -->
                    <div class="flex justify-center mb-8">
                        <button @click="openSideBySide('front')" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            Visualização Lado a Lado
                        </button>
                    </div>

                    <!-- Comparison Display -->
                    <div class="space-y-12">
                        <!-- Front -->
                        <div>
                            <div class="flex items-center justify-center mb-4">
                                <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Vista Frontal</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="leftMeasurement && leftMeasurement.photo_front">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'front'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag"
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(leftMeasurement.id, 'front')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!leftMeasurement || !leftMeasurement.photo_front">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                                </div>
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="rightMeasurement && rightMeasurement.photo_front">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'front'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag"
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(rightMeasurement.id, 'front')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!rightMeasurement || !rightMeasurement.photo_front">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Side Right -->
                        <div>
                            <div class="flex items-center justify-center mb-4">
                                <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Lado D (Direito)</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="leftMeasurement && leftMeasurement.photo_side_right">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'side_right'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag"
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(leftMeasurement.id, 'side_right')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!leftMeasurement || !leftMeasurement.photo_side_right">
                                        <!-- Fallback -->
                                        <template x-if="leftMeasurement && leftMeasurement.photo_side">
                                            <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                                 x-data="imageZoom()" 
                                                 @mouseleave="stopDrag" 
                                                 @mouseup="stopDrag" 
                                                 @touchend="stopDrag"
                                                 @click.outside="reset()">
                                                
                                                <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                     :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                    <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                    </button>
                                                    <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                    <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    </button>
                                                    <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'side'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                    </button>
                                                </div>

                                                <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                     :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                     @mousedown="startDrag" 
                                                     @touchstart="startDrag"
                                                     @mousemove="onDrag"
                                                     @touchmove="onDrag"
                                                     @wheel="onWheel">
                                                    <img :src="getPhotoUrl(leftMeasurement.id, 'side')" 
                                                         class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                         :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!leftMeasurement || (!leftMeasurement.photo_side_right && !leftMeasurement.photo_side)">
                                            <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                                <span class="text-sm">Sem foto</span>
                                            </div>
                                        </template>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                                </div>
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="rightMeasurement && rightMeasurement.photo_side_right">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'side_right'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag"
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(rightMeasurement.id, 'side_right')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!rightMeasurement || !rightMeasurement.photo_side_right">
                                        <!-- Fallback -->
                                        <template x-if="rightMeasurement && rightMeasurement.photo_side">
                                            <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                                 x-data="imageZoom()" 
                                                 @mouseleave="stopDrag" 
                                                 @mouseup="stopDrag" 
                                                 @touchend="stopDrag"
                                                 @click.outside="reset()">
                                                
                                                <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                     :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                    <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                    </button>
                                                    <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                    <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    </button>
                                                    <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'side'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                    </button>
                                                </div>

                                                <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                     :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                     @mousedown="startDrag" 
                                                     @touchstart="startDrag"
                                                     @mousemove="onDrag"
                                                     @touchmove="onDrag"
                                                     @wheel="onWheel">
                                                    <img :src="getPhotoUrl(rightMeasurement.id, 'side')" 
                                                         class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                         :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!rightMeasurement || (!rightMeasurement.photo_side_right && !rightMeasurement.photo_side)">
                                            <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                                <span class="text-sm">Sem foto</span>
                                            </div>
                                        </template>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Side Left -->
                        <div>
                            <div class="flex items-center justify-center mb-4">
                                <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Lado E (Esquerdo)</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="leftMeasurement && leftMeasurement.photo_side_left">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'side_left'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag"
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(leftMeasurement.id, 'side_left')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!leftMeasurement || !leftMeasurement.photo_side_left">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                                </div>
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="rightMeasurement && rightMeasurement.photo_side_left">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'side_left'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag"
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(rightMeasurement.id, 'side_left')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!rightMeasurement || !rightMeasurement.photo_side_left">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Back -->
                        <div>
                            <div class="flex items-center justify-center mb-4">
                                <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Vista Costas</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="leftMeasurement && leftMeasurement.photo_back">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'back'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag"
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(leftMeasurement.id, 'back')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!leftMeasurement || !leftMeasurement.photo_back">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                                </div>
                                <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                    <template x-if="rightMeasurement && rightMeasurement.photo_back">
                                        <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                             x-data="imageZoom()" 
                                             @mouseleave="stopDrag" 
                                             @mouseup="stopDrag" 
                                             @touchend="stopDrag">
                                            
                                            <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                                 :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                                <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                                <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'back'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                </button>
                                            </div>

                                            <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                                 :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                                 @mousedown="startDrag" 
                                                 @touchstart="startDrag"
                                                 @mousemove="onDrag" 
                                                 @touchmove="onDrag"
                                                 @wheel="onWheel">
                                                <img :src="getPhotoUrl(rightMeasurement.id, 'back')" 
                                                     class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                     :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!rightMeasurement || !rightMeasurement.photo_back">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                    <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <!-- ══ Avaliações com IA ══════════════════════════════════════════════════ -->
    @if($aiAssessments->isNotEmpty())
    <div x-show="activeTab === 'ai_assessments'" x-cloak class="space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-base font-semibold text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Histórico de Avaliações com IA
            </h3>
            <span class="text-xs text-gray-500">{{ $aiAssessments->count() }} {{ $aiAssessments->count() === 1 ? 'avaliação' : 'avaliações' }}</span>
        </div>

        @foreach($aiAssessments as $ai)
        @php
            $data    = $ai->ai_analysis_data ?? [];
            $posture = $data['posture_analysis'] ?? [];
            $body    = $data['body_composition'] ?? [];
            $obs     = $data['observations'] ?? [];
            $rec     = $data['recommendations'] ?? [];
        @endphp
        <div x-data="{ expanded: false }" class="bg-zinc-900 border border-zinc-700/60 rounded-2xl overflow-hidden">
            <button type="button" @click="expanded = !expanded"
                class="w-full flex items-center justify-between px-5 py-4 hover:bg-zinc-800/50 transition-colors text-left">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-purple-600/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    @php
                        $postureLabels = [
                            'lordosis' => 'Lordose',
                            'scoliosis' => 'Escoliose',
                            'shoulders' => 'Ombros',
                            'head_position' => 'Cabeça',
                            'knees' => 'Joelhos',
                            'feet' => 'Pés',
                        ];
                    @endphp
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $ai->created_at->format('d/m/Y \à\s H:i') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if($ai->goal)<span class="text-purple-400">{{ $ai->goal }}</span>@endif
                            @if($ai->experience_level) · {{ $ai->experience_level }}@endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($ai->workoutPlan)
                    <span class="hidden sm:inline-flex items-center gap-1 text-xs text-emerald-400 bg-emerald-400/10 rounded-full px-2.5 py-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ $ai->workoutPlan->name }}
                    </span>
                    @endif
                    <a href="{{ route('personal.ai-assessment.saved-pdf', $ai) }}"
                       target="_blank"
                       @click.stop
                       class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg px-2.5 py-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                    <svg class="w-4 h-4 text-gray-500 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

            <div x-show="expanded" x-cloak
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="border-t border-zinc-700/50 px-5 py-5 space-y-5">

                    {{-- Análise Postural --}}
                    @if(!empty($posture))
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Análise Postural
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($posture as $key => $value)
                            @if($value && strtolower((string)$value) !== 'normal')
                            <div class="bg-zinc-800/60 rounded-xl px-3 py-2">
                                <p class="text-xs text-gray-500">{{ $postureLabels[$key] ?? str_replace('_', ' ', $key) }}</p>
                                <p class="text-xs text-orange-300 font-medium mt-0.5">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Composição Corporal --}}
                    @if(!empty($body))
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Composição Corporal</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach($body as $key => $value)
                            @if($value)
                            <div class="bg-zinc-800/60 rounded-xl px-3 py-2">
                                <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</p>
                                <p class="text-xs text-white font-medium mt-0.5">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Observações --}}
                    @if(!empty($obs))
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Observações</h4>
                        <ul class="space-y-1.5">
                            @foreach((is_array($obs) ? $obs : [$obs]) as $o)
                            <li class="flex items-start gap-2 text-xs text-gray-300">
                                <span class="text-purple-400 mt-0.5 shrink-0">•</span>{{ $o }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Recomendações --}}
                    @if(!empty($rec))
                    <div>
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Recomendações</h4>
                        <ul class="space-y-1.5">
                            @foreach((is_array($rec) ? $rec : [$rec]) as $r)
                            <li class="flex items-start gap-2 text-xs text-gray-300">
                                <span class="text-emerald-400 mt-0.5 shrink-0">✓</span>{{ $r }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Treino vinculado --}}
                    @if($ai->workoutPlan)
                    <div class="flex items-center justify-between bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-xs text-emerald-400 font-semibold">Treino vinculado</p>
                            <p class="text-xs text-gray-300 mt-0.5">{{ $ai->workoutPlan->name }}</p>
                        </div>
                        <a href="{{ route('workouts.show', $ai->workoutPlan) }}"
                           class="text-xs text-emerald-400 hover:text-emerald-300 underline transition-colors">
                            Ver treino →
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif



    <!-- Image Modal (Reutilizado do Alpine acima, mas precisa estar fora do x-data de tabs para funcionar em tudo ou duplicado) -->
    <!-- Vamos usar um modal global ou específico para a aba de comparação -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-95 p-4" x-cloak @click="modalOpen = false" x-data="{ modalOpen: false, modalImage: '' }" @open-comparison-modal.window="modalOpen = true; modalImage = $event.detail">
        <div class="relative max-w-4xl w-full h-full flex items-center justify-center">
             <button @click="modalOpen = false" class="absolute top-0 right-0 m-4 text-white text-4xl font-light">&times;</button>
             <img :src="modalImage" class="max-w-full max-h-full rounded shadow-2xl">
        </div>
    </div>

    <script>
        function photoComparison() {
            return {
                measurements: @json($measurements->sortBy('date')->values()),
                leftId: null,
                rightId: null,
                photoRouteTemplate: "{{ route('measurement.photo', ['measurementId' => 999999, 'type' => 'placeholder_type']) }}",
                
                init() {
                    // Ordena novamente por data (ascendente) para garantir a ordem correta
                    this.measurements.sort((a, b) => new Date(a.date) - new Date(b.date));

                    // Usa $nextTick para garantir que o x-for já renderizou as opções do select antes de definir o valor
                    this.$nextTick(() => {
                        if (this.measurements.length > 0) {
                            // Se tiver pelo menos 2 avaliações, seleciona a penúltima na esquerda e a última na direita
                            if (this.measurements.length >= 2) {
                                this.leftId = this.measurements[this.measurements.length - 2].id;
                                this.rightId = this.measurements[this.measurements.length - 1].id;
                            } else {
                                // Se tiver apenas 1, seleciona ela em ambos
                                this.leftId = this.measurements[0].id;
                                this.rightId = this.measurements[0].id;
                            }
                        }
                    });
                    
                    this.initSbsListener();
                },

                getPhotoUrl(id, type) {
                    const url = this.photoRouteTemplate.replace('999999', id).replace('placeholder_type', type);
                    return `${url}?t=${new Date().getTime()}`;
                },

                get leftMeasurement() {
                    return this.measurements.find(m => m.id == this.leftId) || null;
                },
                get rightMeasurement() {
                    return this.measurements.find(m => m.id == this.rightId) || null;
                },
                
                formatDate(dateString) {
                    if(!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('pt-BR', { timeZone: 'UTC' }); 
                },
                
                openModal(imageUrl) {
                    window.dispatchEvent(new CustomEvent('open-comparison-modal', { detail: imageUrl }));
                },

                // Side-by-Side Logic
                sbsOpen: false,
                sbsView: 'front', // front, side_right, side_left, back
                sbsLeftImage: null,
                sbsRightImage: null,

                openSideBySide(view) {
                    this.sbsView = view;
                    this.updateSbsImages();
                    this.sbsOpen = true;
                    // Dispara evento para abrir o modal
                    this.$nextTick(() => {
                        window.dispatchEvent(new CustomEvent('open-sbs-modal', { 
                            detail: { 
                                leftImage: this.sbsLeftImage, 
                                rightImage: this.sbsRightImage,
                                view: this.sbsView
                            } 
                        }));
                    });
                },

                updateSbsImages() {
                    if (!this.leftMeasurement || !this.rightMeasurement) return;
                    
                    // Mapeamento de views para propriedades da foto
                    const viewMap = {
                        'front': 'photo_front',
                        'side_right': 'photo_side_right',
                        'side_left': 'photo_side_left',
                        'back': 'photo_back',
                        'side': 'photo_side' // fallback
                    };

                    let prop = viewMap[this.sbsView];
                    
                    // Lógica para pegar a URL
                    this.sbsLeftImage = this.getPhotoUrl(this.leftMeasurement.id, this.sbsView);
                    this.sbsRightImage = this.getPhotoUrl(this.rightMeasurement.id, this.sbsView);
                },
                
                // Listener para mudança de view dentro do modal
                initSbsListener() {
                    window.addEventListener('update-sbs-images', (e) => {
                        this.sbsView = e.detail;
                        this.updateSbsImages();
                        // Atualiza o modal com as novas imagens
                        window.dispatchEvent(new CustomEvent('open-sbs-modal', { 
                            detail: { 
                                leftImage: this.sbsLeftImage, 
                                rightImage: this.sbsRightImage,
                                view: this.sbsView
                            } 
                        }));
                    });
                }
            }
        }
    </script>

    <!-- Side-by-Side Modal Template -->
    <div x-data="{ open: false, leftImage: null, rightImage: null, view: 'front' }"
         @open-sbs-modal.window="open = true; leftImage = $event.detail.leftImage; rightImage = $event.detail.rightImage; view = $event.detail.view"
         x-on:switch-sbs-view.window="view = $event.detail; $dispatch('update-sbs-images', view)"
         x-show="open" 
         style="display: none;"
         class="fixed inset-0 z-[99999] overflow-hidden"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Overlay Backdrop -->
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-950/90 backdrop-blur-md transition-opacity" 
             aria-hidden="true"></div>

        <!-- Fullscreen Container -->
        <div class="fixed inset-0 z-[100000] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
            <div class="bg-gray-900 w-full max-w-5xl h-[70vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-800 pointer-events-auto">
            
                <!-- Toolbar -->
                <div class="bg-gray-900 px-4 py-3 flex justify-between items-center border-b border-gray-700 shadow-xl z-50 shrink-0">
                    <h3 class="text-lg leading-6 font-bold text-white tracking-wide" id="modal-title">
                        Comparação Lado a Lado
                    </h3>
                    <div class="flex items-center space-x-4">
                        <!-- View Switcher -->
                        <div class="flex bg-gray-800 rounded-lg p-1 border border-gray-700">
                            <button @click="$dispatch('switch-sbs-view', 'front')" :class="{'bg-indigo-600 text-white shadow': view === 'front', 'text-gray-400 hover:text-white hover:bg-gray-700': view !== 'front'}" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all">Frontal</button>
                            <button @click="$dispatch('switch-sbs-view', 'side_right')" :class="{'bg-indigo-600 text-white shadow': view === 'side_right', 'text-gray-400 hover:text-white hover:bg-gray-700': view !== 'side_right'}" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all">Lado D</button>
                            <button @click="$dispatch('switch-sbs-view', 'side_left')" :class="{'bg-indigo-600 text-white shadow': view === 'side_left', 'text-gray-400 hover:text-white hover:bg-gray-700': view !== 'side_left'}" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all">Lado E</button>
                            <button @click="$dispatch('switch-sbs-view', 'back')" :class="{'bg-indigo-600 text-white shadow': view === 'back', 'text-gray-400 hover:text-white hover:bg-gray-700': view !== 'back'}" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all">Costas</button>
                        </div>
                        
                        <!-- Close Button -->
                        <button @click="open = false" class="bg-gray-800 hover:bg-red-600 text-gray-400 hover:text-white p-2 rounded-full transition-colors border border-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Images Container -->
                <div class="flex-1 bg-black flex items-center justify-center overflow-hidden relative w-full h-full">
                    <div class="flex w-full h-full">
                        <!-- Left Image -->
                        <div class="w-1/2 h-full border-r border-gray-800 relative overflow-hidden flex items-center justify-center bg-black">
                            <template x-if="leftImage">
                                <img :src="leftImage" class="max-w-full max-h-full object-contain" alt="Esquerda">
                            </template>
                            <template x-if="!leftImage">
                                <div class="text-gray-600 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="font-medium">Sem imagem disponível</span>
                                </div>
                            </template>
                            <div class="absolute bottom-4 left-4 bg-black/80 backdrop-blur text-white px-3 py-1.5 rounded-md text-sm font-semibold border border-white/10 shadow-lg">Avaliação 1</div>
                        </div>
                        
                        <!-- Right Image -->
                        <div class="w-1/2 h-full relative overflow-hidden flex items-center justify-center bg-black">
                            <template x-if="rightImage">
                                <img :src="rightImage" class="max-w-full max-h-full object-contain" alt="Direita">
                            </template>
                            <template x-if="!rightImage">
                                <div class="text-gray-600 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="font-medium">Sem imagem disponível</span>
                                </div>
                            </template>
                            <div class="absolute bottom-4 right-4 bg-black/80 backdrop-blur text-white px-3 py-1.5 rounded-md text-sm font-semibold border border-white/10 shadow-lg">Avaliação 2</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function imageZoom() {
            return {
                scale: 1,
                panning: false,
                pointX: 0,
                pointY: 0,
                startX: 0,
                startY: 0,

                zoomIn() {
                    if (this.scale < 3) this.scale = Math.min(this.scale + 0.5, 3);
                },
                
                zoomOut() {
                    if (this.scale > 1) this.scale = Math.max(this.scale - 0.5, 1);
                    if (this.scale === 1) this.reset();
                },
                
                reset() {
                    this.scale = 1;
                    this.pointX = 0;
                    this.pointY = 0;
                    this.panning = false;
                },

                startDrag(e) {
                    if (this.scale <= 1) return;
                    e.preventDefault();
                    this.panning = true;
                    this.startX = e.clientX || e.touches[0].clientX;
                    this.startY = e.clientY || e.touches[0].clientY;
                },

                onDrag(e) {
                    if (!this.panning || this.scale <= 1) return;
                    e.preventDefault();
                    
                    const clientX = e.clientX || e.touches[0].clientX;
                    const clientY = e.clientY || e.touches[0].clientY;
                    
                    const deltaX = clientX - this.startX;
                    const deltaY = clientY - this.startY;
                    
                    this.pointX += deltaX;
                    this.pointY += deltaY;
                    
                    this.startX = clientX;
                    this.startY = clientY;
                },

                stopDrag() {
                    this.panning = false;
                },
                
                onWheel(e) {
                    e.preventDefault();
                    if (e.deltaY < 0) this.zoomIn();
                    else this.zoomOut();
                }
            }
        }
    </script>

    <!-- Modal de Detalhes da Avaliação -->
    <div x-data="{ 
            open: false, 
            data: {},
            fmtCirc(value) {
                if (value === null || value === undefined || value === '') return '-';
                const parsed = Number(String(value).replace(',', '.'));
                return Number.isFinite(parsed) ? parsed.toFixed(2) : '-';
            }
        }" 
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
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Ombro (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.ombro)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Tórax (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.torax)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Cintura (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.waist)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Abdômen (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.abdomen)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Abdômen inferior (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.abdomen_inferior)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Quadril (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.hips)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Coxa proximal D (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.right_thigh_proximal)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Coxa medial D (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.right_thigh_medial)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Coxa distal D (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.right_thigh_distal)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Coxa proximal E (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.left_thigh_proximal)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Coxa medial E (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.left_thigh_medial)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Coxa distal E (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.left_thigh_distal)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Panturrilha D (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.right_calf)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Panturrilha E (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.left_calf)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Braço relaxado D (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.right_arm)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Braço contraído D (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.right_arm_contracted)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Braço relaxado E (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.left_arm)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Braço contraído E (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.left_arm_contracted)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Antebraço D (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.right_forearm)"></p></div>
                                    <div class="rounded-lg border border-zinc-700 bg-zinc-800/50 p-3"><p class="text-xs text-stone-400">Antebraço E (cm)</p><p class="mt-1 font-semibold text-stone-100" x-text="fmtCirc(data.left_forearm)"></p></div>
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
            </div>
        </div>

        <!-- Zoom da Imagem — fora do container com opacity-35 para não herdar transparência -->
        <div x-show="zoomOpen" x-cloak class="absolute inset-0 flex items-center justify-center p-4 bg-black/90" style="z-index: 5200;"
             @click.self="zoomOpen = false"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="w-full max-w-4xl"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm text-indigo-300 font-medium" x-text="zoomPhoto?.label || 'Imagem'"></p>
                    <button type="button" @click="zoomOpen = false" class="text-gray-300 hover:text-white text-xl leading-none">✕</button>
                </div>
                <img :src="zoomPhoto?.url" :alt="zoomPhoto?.label || 'Imagem ampliada'" class="w-full max-h-[85vh] object-contain rounded-lg border border-zinc-700">
            </div>
        </div>
    </div>

    <script>
        (function () {
            const photoTrigger = document.getElementById('student-photo-trigger');
            const photoInput = document.getElementById('student-profile-photo-input');
            const photoForm = document.getElementById('student-photo-form');

            if (photoTrigger && photoInput && photoForm) {
                photoTrigger.addEventListener('click', function () {
                    photoInput.click();
                });

                photoInput.addEventListener('change', function () {
                    if (photoInput.files && photoInput.files[0]) {
                        photoForm.submit();
                    }
                });
            }
        })();

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
