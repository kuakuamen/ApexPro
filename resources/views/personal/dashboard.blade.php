@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>
<div class="space-y-8" x-data="{ search: '' }">

    <!-- Cabeçalho e Saudação -->
    <div>
        <h1 class="text-3xl font-bold text-white">Olá, {{ auth()->user()->name }}!</h1>
        <p class="mt-1 text-lg text-gray-400">Aqui está um resumo da sua jornada e de seus alunos.</p>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total de Alunos -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-indigo-500 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Total de Alunos</dt>
                            <dd class="text-3xl font-bold text-white">{{ $students->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <!-- Alunos Ativos -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-green-500 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Alunos Ativos</dt>
                            <dd class="text-3xl font-bold text-green-400">{{ $students->where('is_active', true)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <!-- Novos este Mês -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-blue-500 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Novos este Mês</dt>
                            <dd class="text-3xl font-bold text-blue-400">{{ $students->where('created_at', '>=', now()->startOfMonth())->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <!-- Avaliações Pendentes -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-yellow-500 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Avaliações > 30 dias</dt>
                            <dd class="text-3xl font-bold text-yellow-400">{{ $students->filter(fn($s) => $s->measurements->isNotEmpty() && $s->measurements->first()->date->diffInDays(now()) > 30)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Ações e Busca -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" x-model="search" class="bg-gray-800/50 border border-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm rounded-lg p-2.5 transition" placeholder="Buscar aluno por nome...">
        </div>

    </div>

    <!-- Grid de Alunos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($students as $student)
            <div x-show="search === '' || '{{ strtolower($student->name) }}'.includes(search.toLowerCase())" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="relative bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-md hover:shadow-indigo-500/20 hover:border-indigo-500 transition-all duration-300 group p-5 flex flex-col h-full">
                    
                    <!-- Status Indicator -->
<span title="{{ $student->is_active ? 'Ativo' : 'Inativo' }}" class="absolute top-4 right-4 h-3 w-3 rounded-full {{ $student->is_active ? 'bg-green-500' : 'bg-red-500' }} border-2 border-gray-800"></span>

                    <!-- Info Principal -->
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-14 w-14 rounded-full bg-gray-700 border-2 border-gray-600 flex items-center justify-center text-indigo-400 font-bold text-2xl">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                        <div class="ml-4 truncate">
                            <p class="text-lg font-bold text-white truncate group-hover:text-indigo-400 transition-colors">{{ $student->name }}</p>
                            <p class="text-sm text-gray-400">{{ $student->email ?? 'Sem email' }}</p>
                        </div>
                    </div>

                    <!-- Info de Avaliação -->
                    <div class="flex-grow space-y-2 text-sm">
                        <div class="flex items-center text-gray-400">
                            <svg class="flex-shrink-0 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @if($student->measurements->isNotEmpty())
                                <span class="{{ $student->measurements->first()->date->diffInDays(now()) > 30 ? 'text-yellow-400 font-semibold' : '' }}">
                                    Última avaliação: {{ $student->measurements->first()->date->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-gray-500 italic">Nenhuma avaliação</span>
                            @endif
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="mt-5 pt-4 border-t border-gray-700 flex items-center justify-between">
                        <a href="{{ route('personal.students.show', $student) }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                            Ver Perfil Completo
                        </a>
                        @if($student->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone) }}" target="_blank" class="text-green-400 hover:text-green-300 p-2 rounded-full hover:bg-gray-700 transition-colors" title="Chamar no WhatsApp">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-800/50 border border-dashed border-gray-700 rounded-xl text-center p-12">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-white">Nenhum aluno encontrado</h3>
                <p class="mt-1 text-sm text-gray-400">Comece cadastrando um novo aluno para gerenciar.</p>
                <div class="mt-6">
                    <a href="{{ route('personal.students.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Cadastrar Primeiro Aluno
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
