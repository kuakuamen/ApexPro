<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ApexPro</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#06b6d4">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        [x-cloak] { display: none !important; }
        .bg-personal-dark {
            background: linear-gradient(180deg, #111827 0%, #1f2937 55%, #111827 100%);
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .hide-scrollbar::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
        }
    </style>
</head>
<body class="{{ auth()->check() ? 'bg-personal-dark' : 'bg-stone-100' }} font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
           <!-- Backdrop (Click outside to close) -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
               class="fixed inset-0 bg-black/60 lg:bg-black/20 z-40"
             x-cloak>
        </div>
        
        <!-- Sidebar (Desktop: Fixa / Mobile: Off-canvas) -->
         <aside :class="sidebarOpen ? 'translate-x-0 pointer-events-auto' : '-translate-x-full pointer-events-none'"
             @keydown.escape.window="sidebarOpen = false"
             class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0b1220]/95 backdrop-blur-sm text-stone-100 transition-transform duration-500 ease-in-out flex flex-col shadow-2xl border-r"
             style="border-right-color: rgba(51, 65, 85, 0.65);">
            
            @php
                $logoRoute = '/';
                if(auth()->check()) {
                    if(auth()->user()->role === 'personal') $logoRoute = route('personal.dashboard');
                    elseif(auth()->user()->role === 'aluno') $logoRoute = route('student.dashboard');
                    elseif(auth()->user()->role === 'nutri') $logoRoute = route('diets.index');
                }
            @endphp
            
            <!-- Logo Area -->
            <div class="flex items-center justify-center h-20 border-b bg-[#0b1220] px-4" style="border-bottom-color: rgba(51, 65, 85, 0.65);">
                <a href="{{ $logoRoute }}" class="group flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center shadow-lg shadow-cyan-500/20 group-hover:scale-110 transition-transform duration-300">
                        <span class="font-bold text-white text-lg">A</span>
                    </div>
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-purple-500">ApexPro</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-5 space-y-2 overflow-y-auto hide-scrollbar">
                
                <!-- Links Comuns / Lógica de Role -->
                @if(auth()->check())
                    <!-- Link Tela Inicial (Dashboard) -->
                    @php
                        $homeRoute = '#';
                        if(auth()->user()->role === 'personal') $homeRoute = route('personal.dashboard');
                        elseif(auth()->user()->role === 'aluno') $homeRoute = route('student.dashboard');
                        elseif(auth()->user()->role === 'nutri') $homeRoute = route('diets.index');
                    @endphp
                    <p class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Principal</p>
                    <a href="{{ $homeRoute }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('*.dashboard') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('*.dashboard') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Tela Inicial
                    </a>

                    <!-- Links Específicos do Personal -->
                    @if(auth()->user()->role === 'personal')
                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Gestão</p>
                        <a href="{{ route('personal.students.index') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.students.index') || request()->routeIs('personal.students.show') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('personal.students.index') || request()->routeIs('personal.students.show') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Alunos
                        </a>

                        <a href="{{ route('personal.students.create') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.students.create') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('personal.students.create') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Novo Aluno
                        </a>
                        <a href="{{ route('workouts.create') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('workouts.create') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('workouts.create') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Criar Treino
                        </a>
                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">IA</p>
                        <a href="{{ route('personal.ai-assessment.index') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.ai-assessment.*') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('personal.ai-assessment.*') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Avaliação com IA
                        </a>

                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Análise</p>
                        <a href="{{ route('personal.evolution.index') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.evolution.*') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('personal.evolution.*') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Evolução
                        </a>

                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Financeiro</p>
                        <a href="{{ route('personal.financial.dashboard') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.financial.*') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('personal.financial.*') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Financeiro
                        </a>

                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Pendências</p>
                        <a href="{{ route('personal.assessments.pending', ['type' => 'overdue']) }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'overdue'])) ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'overdue'])) ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Avaliação Atrasada
                        </a>
                        <a href="{{ route('personal.assessments.pending', ['type' => 'missing']) }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'missing'])) ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'missing'])) ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Sem Avaliação
                        </a>
                    @endif

                    <!-- Links Específicos do Aluno -->
                    @if(auth()->user()->role === 'aluno')
                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aluno</p>
                        <a href="{{ route('student.evolution') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('student.evolution') ? 'bg-cyan-500/10 text-cyan-100 border-l-2 border-cyan-400 shadow-[inset_0_0_0_1px_rgba(34,211,238,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('student.evolution') ? 'text-cyan-300' : 'text-slate-500 group-hover:text-cyan-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Minha Evolução
                        </a>
                    @endif

                @else
                    <a href="/login" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 text-slate-300 hover:bg-slate-800/80 hover:text-slate-100">
                        <svg class="w-5 h-5 mr-3 text-slate-500 group-hover:text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Fazer Login
                    </a>
                @endif
            </nav>

            <!-- User Profile & Logout (Bottom) -->
            @if(auth()->check())
            <div class="border-t bg-[#0b1220] p-4" style="border-top-color: rgba(51, 65, 85, 0.65);">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-cyan-500/15 flex items-center justify-center text-cyan-200 font-semibold text-base border border-cyan-400/30">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-100 truncate">{{ auth()->user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-slate-400 hover:text-cyan-300 transition-colors flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Sair do Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </aside>

        <!-- Main Content Wrapper -->
           <div class="flex-1 flex flex-col overflow-hidden transition-all duration-500 ease-in-out"
               :style="sidebarOpen ? 'margin-left: 16rem; width: calc(100% - 16rem);' : 'margin-left: 0; width: 100%;'">

            <!-- Top Bar -->
            <header class="h-14 shrink-0 flex items-center border-b bg-zinc-950/85 backdrop-blur-sm px-3 md:px-4" style="border-bottom-color: rgba(51, 65, 85, 0.65);">
                <button x-show="!sidebarOpen" x-cloak @click.stop="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center rounded-md text-stone-100 bg-zinc-900/70 hover:bg-cyan-950/60 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto hide-scrollbar px-4 md:px-8 pb-4 md:pb-8 pt-0">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- ── Global Toast Notifications ─────────────────────────── --}}
    @php
        $toasts = [];
        if (session('success')) $toasts[] = ['type' => 'success', 'msg' => session('success')];
        if (session('error'))   $toasts[] = ['type' => 'error',   'msg' => session('error')];
        if (session('warning')) $toasts[] = ['type' => 'warning', 'msg' => session('warning')];
        if (session('info'))    $toasts[] = ['type' => 'info',    'msg' => session('info')];
    @endphp

    <div
        x-data="toastManager({{ json_encode($toasts) }})"
        x-init="init()"
        class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"
        style="max-width: 380px; width: calc(100vw - 2.5rem)"
    >
        <template x-for="(toast, index) in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
                :class="toastClasses(toast.type)"
                class="pointer-events-auto flex items-start gap-3 px-4 py-3.5 rounded-2xl shadow-2xl border backdrop-blur-sm"
                role="alert"
            >
                {{-- Ícone --}}
                <div :class="iconBgClass(toast.type)" class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-4 h-4 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                </div>

                {{-- Texto --}}
                <div class="flex-1 min-w-0 pt-0.5">
                    <p :class="labelClass(toast.type)" class="text-xs font-semibold uppercase tracking-wider mb-0.5" x-text="labelText(toast.type)"></p>
                    <p class="text-sm text-slate-200 leading-snug" x-text="toast.msg"></p>
                </div>

                {{-- Botão fechar --}}
                <button @click="dismiss(index)" class="shrink-0 mt-0.5 text-slate-500 hover:text-slate-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- Barra de progresso --}}
                <div :class="progressClass(toast.type)" class="absolute bottom-0 left-0 h-0.5 rounded-b-2xl transition-all ease-linear" :style="`width: ${toast.progress}%`"></div>
            </div>
        </template>
    </div>

    <script>
    function toastManager(initialToasts) {
        return {
            toasts: [],
            init() {
                initialToasts.forEach(t => this.add(t.type, t.msg));
            },
            add(type, msg) {
                const id      = Date.now() + Math.random();
                const toast   = { id, type, msg, visible: false, progress: 100 };
                this.toasts.push(toast);
                this.$nextTick(() => {
                    toast.visible = true;
                    const duration  = 4500;
                    const interval  = 50;
                    const decrement = (interval / duration) * 100;
                    const timer = setInterval(() => {
                        toast.progress -= decrement;
                        if (toast.progress <= 0) {
                            clearInterval(timer);
                            this.dismiss(this.toasts.indexOf(toast));
                        }
                    }, interval);
                });
            },
            dismiss(index) {
                if (this.toasts[index]) {
                    this.toasts[index].visible = false;
                    setTimeout(() => this.toasts.splice(index, 1), 250);
                }
            },
            toastClasses(type) {
                return {
                    'success': 'bg-[#0d1f17]/95 border-emerald-500/30 relative overflow-hidden',
                    'error':   'bg-[#1f0d0d]/95 border-red-500/30 relative overflow-hidden',
                    'warning': 'bg-[#1f1a0d]/95 border-yellow-500/30 relative overflow-hidden',
                    'info':    'bg-[#0d1520]/95 border-cyan-500/30 relative overflow-hidden',
                }[type] ?? 'bg-[#111827]/95 border-slate-700/50 relative overflow-hidden';
            },
            iconBgClass(type) {
                return {
                    'success': 'bg-emerald-500/20',
                    'error':   'bg-red-500/20',
                    'warning': 'bg-yellow-500/20',
                    'info':    'bg-cyan-500/20',
                }[type] ?? 'bg-slate-500/20';
            },
            labelClass(type) {
                return {
                    'success': 'text-emerald-400',
                    'error':   'text-red-400',
                    'warning': 'text-yellow-400',
                    'info':    'text-cyan-400',
                }[type] ?? 'text-slate-400';
            },
            labelText(type) {
                return { 'success': 'Sucesso', 'error': 'Erro', 'warning': 'Atenção', 'info': 'Informação' }[type] ?? type;
            },
            progressClass(type) {
                return {
                    'success': 'bg-emerald-400/60',
                    'error':   'bg-red-400/60',
                    'warning': 'bg-yellow-400/60',
                    'info':    'bg-cyan-400/60',
                }[type] ?? 'bg-slate-400/60';
            },
        };
    }
    </script>
</body>
</html>
