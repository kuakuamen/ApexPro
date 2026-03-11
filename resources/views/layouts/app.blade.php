<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ApexPro</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/photos/Gemini_Generated_Image_.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        [x-cloak] { display: none !important; }
        .bg-personal-dark {
            background: linear-gradient(180deg, #18181b 0%, #0f172a 55%, #042f2e 100%);
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
                    <img src="{{ asset('storage/photos/Gemini_Generated_Image_.png') }}" alt="ApexPro Logo" class="w-10 h-10 object-contain group-hover:scale-110 transition-transform duration-300">
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-stone-100 to-teal-300">ApexPro</span>
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
                    <a href="{{ $homeRoute }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('*.dashboard') ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                        <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('*.dashboard') ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Tela Inicial
                    </a>

                    <!-- Links Específicos do Personal -->
                    @if(auth()->user()->role === 'personal')
                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Gestão</p>
                        <a href="{{ route('personal.students.index') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.students.index') || request()->routeIs('personal.students.show') ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('personal.students.index') || request()->routeIs('personal.students.show') ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Alunos
                        </a>

                        <a href="{{ route('personal.students.create') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.students.create') ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('personal.students.create') ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Novo Aluno
                        </a>
                        <a href="{{ route('workouts.create') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('workouts.create') ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('workouts.create') ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Criar Treino
                        </a>
                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">IA</p>
                        <a href="{{ route('personal.ai-assessment.index') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('personal.ai-assessment.*') ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('personal.ai-assessment.*') ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Avaliação com IA
                        </a>

                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Pendências</p>
                        <a href="{{ route('personal.assessments.pending', ['type' => 'overdue']) }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'overdue'])) ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'overdue'])) ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Avaliação Atrasada
                        </a>
                        <a href="{{ route('personal.assessments.pending', ['type' => 'missing']) }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'missing'])) ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->fullUrlIs(route('personal.assessments.pending', ['type' => 'missing'])) ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Sem Avaliação
                        </a>
                    @endif

                    <!-- Links Específicos do Aluno -->
                    @if(auth()->user()->role === 'aluno')
                        <p class="px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aluno</p>
                        <a href="{{ route('student.evolution') }}" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('student.evolution') ? 'bg-teal-500/10 text-teal-100 border-l-2 border-teal-400 shadow-[inset_0_0_0_1px_rgba(45,212,191,0.22)]' : 'text-slate-300 hover:bg-slate-800/80 hover:text-slate-100' }}">
                            <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:translate-x-0.5 {{ request()->routeIs('student.evolution') ? 'text-teal-300' : 'text-slate-500 group-hover:text-teal-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Minha Evolução
                        </a>
                    @endif

                @else
                    <a href="/login" class="group flex items-center px-3 py-2 min-h-[42px] text-sm font-medium rounded-lg transition-all duration-200 text-slate-300 hover:bg-slate-800/80 hover:text-slate-100">
                        <svg class="w-5 h-5 mr-3 text-slate-500 group-hover:text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Fazer Login
                    </a>
                @endif
            </nav>

            <!-- User Profile & Logout (Bottom) -->
            @if(auth()->check())
            <div class="border-t bg-[#0b1220] p-4" style="border-top-color: rgba(51, 65, 85, 0.65);">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-teal-500/15 flex items-center justify-center text-teal-200 font-semibold text-base border border-teal-400/30">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-100 truncate">{{ auth()->user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-slate-400 hover:text-teal-300 transition-colors flex items-center gap-1 mt-0.5">
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
                <button x-show="!sidebarOpen" x-cloak @click.stop="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center rounded-md text-stone-100 bg-zinc-900/70 hover:bg-teal-950/60 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto hide-scrollbar px-4 md:px-8 pb-4 md:pb-8 pt-0">
                <div class="max-w-7xl mx-auto">
                    <!-- Mensagens Flash -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                            <p class="font-bold">Sucesso!</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
