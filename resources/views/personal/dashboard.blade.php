@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Cabeçalho e Saudação -->
    <div>
        <h1 class="text-3xl font-bold text-white">Olá, {{ auth()->user()->name }}!</h1>
        <p class="mt-1 text-lg text-gray-400">Aqui está um resumo da sua jornada e de seus alunos.</p>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total de Alunos -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-emerald-400/70 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Total de Alunos</dt>
                            <dd class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-400">{{ $students->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <!-- Alunos Ativos -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-green-400/70 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Alunos Ativos</dt>
                            <dd class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-300 to-emerald-400">{{ $students->where('is_active', true)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <!-- Novos este Mês -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-teal-400/70 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Novos este Mês</dt>
                            <dd class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-300 to-cyan-400">{{ $students->where('created_at', '>=', now()->startOfMonth())->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <!-- Avaliações Pendentes -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-lime-400/70 transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Avaliações > 30 dias</dt>
                            <dd class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-lime-300 to-emerald-400">{{ $students->filter(fn($s) => $s->measurements->isNotEmpty() && $s->measurements->first()->date->diffInDays(now()) > 30)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
