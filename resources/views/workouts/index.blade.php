@extends('layouts.app')

@section('content')
<div class="bg-zinc-900/55 shadow overflow-hidden sm:rounded-lg border border-teal-900/30">
    <div class="px-4 py-5 border-b border-teal-900/40 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-stone-100">
                Meus Treinos
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-stone-300">
                Lista de planos de treino ativos e históricos.
            </p>
        </div>
        @if(auth()->user()->role === 'personal')
            <a href="{{ route('workouts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-stone-100 bg-teal-700 hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-600">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Novo Treino
            </a>
        @endif
    </div>
    
    <ul role="list" class="divide-y divide-teal-900/30">
        @forelse($workouts as $workout)
            <li>
                <a href="{{ route('workouts.show', $workout) }}" class="block hover:bg-teal-950/30 transition-colors">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-teal-300 truncate">
                                {{ $workout->name }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $workout->is_active ? 'bg-teal-700/20 text-teal-200 border border-teal-600/40' : 'bg-zinc-700/20 text-stone-300 border border-zinc-600/50' }}">
                                    {{ $workout->is_active ? 'Ativo' : 'Inativo' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-stone-300">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    @if(auth()->user()->role === 'personal')
                                        Aluno: {{ $workout->student->name }}
                                    @else
                                        Personal: {{ $workout->personal->name }}
                                    @endif
                                </p>
                                <p class="mt-2 flex items-center text-sm text-stone-300 sm:mt-0 sm:ml-6">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Objetivo: {{ $workout->goal ?? 'Não definido' }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-stone-300 sm:mt-0">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p>
                                    Criado em {{ $workout->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
        @empty
            <li class="px-4 py-8 text-center text-stone-300">
                Nenhum treino encontrado.
            </li>
        @endforelse
    </ul>
</div>
@endsection
