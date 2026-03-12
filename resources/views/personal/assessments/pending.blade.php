@extends('layouts.app')

@section('content')
<script src="//unpkg.com/alpinejs" defer></script>
<div class="space-y-8" x-data="{ search: '' }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">
                @if($type === 'missing') Alunos Sem Avaliação
                @elseif($type === 'overdue') Avaliações Atrasadas
                @else Avaliações Pendentes
                @endif
            </h1>
            <p class="mt-1 text-lg text-gray-400">
                @if($type === 'missing') Lista de alunos que nunca realizaram uma avaliação física.
                @elseif($type === 'overdue') Alunos cuja última avaliação está vencida.
                @else Gerencie alunos com pendências de avaliação física.
                @endif
            </p>
        </div>
        <!-- Sem botão de novo aluno aqui -->
    </div>

    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <!-- Busca via GET (server-side) para manter filtro complexo, mas com suporte visual do Alpine -->
            <form action="{{ route('personal.assessments.pending') }}" method="GET" class="w-full">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="text" name="search" value="{{ $search }}" class="bg-gray-800/50 border border-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm rounded-lg p-2.5 transition" placeholder="Buscar aluno por nome...">
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($students as $student)
            <div>
                <div class="relative bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-md hover:shadow-indigo-500/20 hover:border-indigo-500 transition-all duration-300 group p-5 flex flex-col h-full">
                    
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-14 w-14 rounded-full bg-gray-700 border-2 border-gray-600 flex items-center justify-center text-indigo-400 font-bold text-2xl">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                        <div class="ml-4 truncate">
                            <a href="{{ route('personal.students.show', $student) }}" class="text-lg font-bold text-white truncate group-hover:text-indigo-400 transition-colors block hover:text-indigo-300">
                                {{ $student->name }}
                            </a>
                            <p class="text-sm text-gray-400">{{ $student->email ?? 'Sem email' }}</p>
                        </div>
                    </div>

                    <div class="flex-grow space-y-3 text-sm">
                        <!-- Status Badge -->
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->status_color === 'red' ? 'bg-red-900/50 text-red-200 border border-red-700/50' : 'bg-orange-900/50 text-orange-200 border border-orange-700/50' }}">
                            <svg class="mr-1.5 h-2 w-2 {{ $student->status_color === 'red' ? 'text-red-400' : 'text-orange-400' }}" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                            {{ $student->status_label }}
                        </div>

                        <div class="flex items-center text-gray-400">
                            <svg class="flex-shrink-0 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @if($student->last_assessment_date)
                                <span>Última: {{ $student->last_assessment_date->format('d/m/Y') }}</span>
                            @else
                                <span class="text-gray-500 italic">Nunca avaliado</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-700 flex items-center justify-between">
                        <a href="{{ route('personal.students.show', $student) }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                            Ver Perfil
                        </a>
                        <a href="{{ route('personal.measurements.create', $student) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Nova Avaliação
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-800/50 border border-dashed border-gray-700 rounded-xl text-center p-12">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-white">Tudo em dia!</h3>
                <p class="mt-1 text-sm text-gray-400">Nenhum aluno encontrado com este status de pendência.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection