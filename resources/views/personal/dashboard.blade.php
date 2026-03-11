@extends('layouts.app')

@section('content')
<div class="mt-4 space-y-8">
    <!-- Hero Section -->
    <div class="relative rounded-2xl border border-teal-700/50 bg-gray-800/50 backdrop-blur-md p-6 sm:p-8 shadow-xl overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-teal-500/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-indigo-500/10 rounded-full blur-xl"></div>
        
        <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-teal-400">Painel do Personal</p>
                <h1 class="mt-2 text-3xl font-bold text-white tracking-tight">Olá, {{ auth()->user()->name }}!</h1>
                <p class="mt-3 text-sm text-gray-300 leading-relaxed">
                    Acompanhe os alunos que precisam de atenção e acesse as ações principais sem sair da tela inicial.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('personal.students.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white px-5 py-3 text-sm font-semibold shadow-lg shadow-teal-900/20 transition-all hover:-translate-y-0.5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Novo Aluno
                </a>
                <a href="{{ route('personal.ai-assessment.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-teal-500/30 bg-gray-800/50 hover:bg-gray-700/50 text-teal-300 hover:text-white px-5 py-3 text-sm font-semibold transition-all hover:-translate-y-0.5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Registrar Avaliação IA
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Total Alunos -->
        <div class="group relative rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-5 shadow-lg hover:border-cyan-500/30 transition-all">
            <div class="flex items-start justify-between">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Total de Alunos</p>
                <div class="p-2 rounded-lg bg-cyan-500/10 text-cyan-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-white">{{ $totalStudents }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $newThisMonth }} novos neste mês</p>
        </div>

        <!-- Alunos Ativos -->
        <div class="group relative rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-5 shadow-lg hover:border-green-500/30 transition-all">
            <div class="flex items-start justify-between">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Alunos Ativos</p>
                <div class="p-2 rounded-lg bg-green-500/10 text-green-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-white">{{ $activeStudents }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ max($totalStudents - $activeStudents, 0) }} inativos</p>
        </div>

        <!-- Avaliações em Atraso -->
        <div class="group relative rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-5 shadow-lg hover:border-yellow-500/30 transition-all">
            <div class="flex items-start justify-between">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Avaliações em Atraso</p>
                <div class="p-2 rounded-lg bg-yellow-500/10 text-yellow-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-yellow-400">{{ $pendingAssessmentsCount }}</p>
            <p class="mt-1 text-xs text-gray-500">Mais de 30 dias sem atualização</p>
        </div>

        <!-- Sem Avaliação -->
        <div class="group relative rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-5 shadow-lg hover:border-indigo-500/30 transition-all">
            <div class="flex items-start justify-between">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Sem Avaliação</p>
                <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-indigo-400">{{ $studentsWithoutAssessmentCount }}</p>
            <p class="mt-1 text-xs text-gray-500">Alunos sem primeira medição</p>
        </div>
    </div>

    @if($students->isEmpty())
        <section class="rounded-2xl border border-dashed border-teal-800/50 bg-gray-800/30 p-12 text-center">
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-teal-900/30 text-teal-500 mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-white">Você ainda não tem alunos cadastrados</h2>
            <p class="mx-auto mt-2 max-w-xl text-sm text-gray-400">
                Comece criando seu primeiro aluno para liberar evolução, avaliações e planejamento de treino.
            </p>
            <a href="{{ route('personal.students.create') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white px-6 py-3 text-sm font-semibold shadow-lg shadow-teal-900/20 transition-all hover:-translate-y-0.5">
                Cadastrar Primeiro Aluno
            </a>
        </section>
    @else
        <div class="grid grid-cols-1 gap-6">
            <section class="rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md shadow-xl overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-700/50 px-6 py-5 bg-gray-800/50">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Últimos Alunos
                    </h2>
                    <a href="{{ route('personal.students.index') }}" class="text-sm font-semibold text-teal-400 hover:text-teal-300 transition-colors flex items-center gap-1">
                        Ver todos <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
                <div class="divide-y divide-gray-700/50">
                    @foreach($students->sortByDesc('created_at')->take(5) as $student)
                        <a href="{{ route('personal.students.show', $student) }}" class="group block px-6 py-4 hover:bg-gray-700/30 transition-colors">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-white group-hover:text-teal-400 transition-colors">{{ $student->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            Cadastro em {{ $student->created_at ? $student->created_at->format('d/m/Y') : '-' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->is_active ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                    {{ $student->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    @endif
</div>
@endsection
