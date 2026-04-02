@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>
<div class="space-y-8" x-data="{ search: '' }">
    <!-- Header e Ações -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Alunos</h1>
            <p class="mt-2 text-lg text-gray-400">Gerencie todos os alunos vinculados ao seu perfil.</p>
        </div>
        <a href="{{ route('personal.students.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Novo Aluno
        </a>
    </div>

    <!-- Barra de Busca -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-800/30 backdrop-blur-md p-4 rounded-2xl border border-gray-700/50">
        <div class="relative w-full md:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" x-model="search" class="bg-gray-900/50 border border-gray-600 text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent block w-full pl-10 py-2.5 sm:text-sm rounded-xl transition-all" placeholder="Buscar aluno por nome...">
        </div>

        <span class="text-sm font-medium text-gray-400 bg-gray-800/50 px-3 py-1.5 rounded-lg border border-gray-700/50">
            {{ $students->count() }} cadastrados
        </span>
    </div>

    <!-- Grid de Alunos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($students as $student)
            <div x-show="search === '' || '{{ strtolower($student->name) }}'.includes(search.toLowerCase())" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="relative bg-gray-800/50 backdrop-blur-md border border-gray-700 rounded-2xl shadow-xl hover:shadow-2xl hover:border-indigo-500/50 transition-all duration-300 group flex flex-col h-full overflow-hidden">
                    <div class="absolute top-0 right-0 p-4">
                        <span title="{{ $student->is_active ? 'Ativo' : 'Inativo' }}" class="block h-3 w-3 rounded-full {{ $student->is_active ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]' }}"></span>
                    </div>

                    <div class="p-6 pb-0 flex-grow">
                        <div class="flex items-center mb-6">
                            @if($student->profile_photo_url)
                                <img src="{{ $student->profile_photo_url }}" alt="Foto de {{ $student->name }}" class="flex-shrink-0 h-16 w-16 rounded-full object-cover shadow-lg ring-4 ring-gray-800/50 border border-white/10">
                            @else
                                <div class="flex-shrink-0 h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg ring-4 ring-gray-800/50">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="ml-4 min-w-0 flex-1">
                                <a href="{{ route('personal.students.show', $student) }}" class="text-lg font-bold text-white truncate group-hover:text-indigo-400 transition-colors block">
                                    {{ $student->name }}
                                </a>
                                <p class="text-sm text-gray-400 truncate">{{ $student->email ?? 'Sem email' }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-400 bg-gray-900/30 p-2 rounded-lg border border-gray-700/30">
                                <svg class="flex-shrink-0 mr-2.5 h-4 w-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @if($student->measurements->isNotEmpty())
                                    <span class="{{ $student->measurements->first()->date->diffInDays(now()) > 30 ? 'text-yellow-400 font-medium' : '' }}">
                                        Última: {{ $student->measurements->first()->date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-500 italic">Sem avaliações</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-gray-900/30 border-t border-gray-700/50 flex items-center justify-between">
                        <a href="{{ route('personal.students.show', $student) }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                            Ver Perfil <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </a>
                        @if($student->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone) }}" target="_blank" class="text-gray-400 hover:text-green-400 p-2 rounded-lg hover:bg-green-500/10 transition-colors" title="WhatsApp">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-800/30 backdrop-blur-md border border-dashed border-gray-700 rounded-2xl text-center p-12">
                <div class="mx-auto h-16 w-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="mt-2 text-lg font-medium text-white">Nenhum aluno encontrado</h3>
                <p class="mt-1 text-sm text-gray-400">Comece cadastrando um novo aluno para gerenciar.</p>
                <div class="mt-6">
                    <a href="{{ route('personal.students.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 shadow-lg transition-all hover:-translate-y-0.5">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Cadastrar Primeiro Aluno
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
