<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>ApexPro</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('storage/photos/Gemini_Generated_Image_.png')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        html, body {
            background-color: <?php echo e(auth()->check() ? '#0f1115' : '#f5f5f4'); ?>;
        }
        [x-cloak] { display: none !important; }
        .bg-personal-dark {
            background: linear-gradient(180deg, #18181b 0%, #0f172a 55%, #042f2e 100%);
        }
    </style>
</head>
<body class="<?php echo e(auth()->check() ? 'bg-personal-dark' : 'bg-stone-100'); ?> font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Backdrop (Mobile Only) -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 z-40 lg:hidden"
             x-cloak>
        </div>
        
        <!-- Sidebar (Desktop: Fixa / Mobile: Off-canvas) -->
         <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-zinc-950 via-zinc-900 to-teal-950 text-stone-100 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-2xl border-r border-teal-900/40">
            
            <?php
                $logoRoute = '/';
                if(auth()->check()) {
                    if(auth()->user()->role === 'personal') $logoRoute = route('personal.dashboard');
                    elseif(auth()->user()->role === 'aluno') $logoRoute = route('student.dashboard');
                    elseif(auth()->user()->role === 'nutri') $logoRoute = route('diets.index');
                }
            ?>
            
            <!-- Logo Area -->
            <div class="flex items-center justify-center h-20 border-b border-teal-900/40 bg-gradient-to-b from-black/40 to-transparent px-4">
                <a href="<?php echo e($logoRoute); ?>" class="group flex items-center gap-3">
                    <img src="<?php echo e(asset('storage/photos/Gemini_Generated_Image_.png')); ?>" alt="ApexPro Logo" class="w-10 h-10 object-contain group-hover:scale-110 transition-transform duration-300">
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-stone-100 to-teal-300">ApexPro</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                
                <!-- Links Comuns / Lógica de Role -->
                <?php if(auth()->check()): ?>
                    <!-- Link Tela Inicial (Dashboard) -->
                    <?php
                        $homeRoute = '#';
                        if(auth()->user()->role === 'personal') $homeRoute = route('personal.dashboard');
                        elseif(auth()->user()->role === 'aluno') $homeRoute = route('student.dashboard');
                        elseif(auth()->user()->role === 'nutri') $homeRoute = route('diets.index');
                    ?>

                    <a href="<?php echo e($homeRoute); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('*.dashboard') ? 'bg-teal-900/40 text-stone-100 border-l-4 border-teal-400' : 'text-stone-300 hover:bg-teal-950/50 hover:text-stone-100'); ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('*.dashboard') ? 'text-teal-300' : 'text-stone-500 group-hover:text-teal-300'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Tela Inicial
                    </a>

                    <!-- Links Específicos do Personal -->
                    <?php if(auth()->user()->role === 'personal'): ?>
                        <a href="<?php echo e(route('personal.students.create')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('personal.students.create') ? 'bg-teal-900/40 text-stone-100 border-l-4 border-teal-400' : 'text-stone-300 hover:bg-teal-950/50 hover:text-stone-100'); ?>">
                            <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('personal.students.create') ? 'text-teal-300' : 'text-stone-500 group-hover:text-teal-300'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Novo Aluno
                        </a>
                        <a href="<?php echo e(route('workouts.create')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('workouts.create') ? 'bg-teal-900/40 text-stone-100 border-l-4 border-teal-400' : 'text-stone-300 hover:bg-teal-950/50 hover:text-stone-100'); ?>">
                            <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('workouts.create') ? 'text-teal-300' : 'text-stone-500 group-hover:text-teal-300'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Criar Treino
                        </a>
                        <a href="<?php echo e(route('personal.ai-assessment.index')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('personal.ai-assessment.*') ? 'bg-teal-900/40 text-stone-100 border-l-4 border-teal-400' : 'text-stone-300 hover:bg-teal-950/50 hover:text-stone-100'); ?>">
                            <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('personal.ai-assessment.*') ? 'text-teal-300' : 'text-stone-500 group-hover:text-teal-300'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            Avaliação com IA
                        </a>
                    <?php endif; ?>

                    <!-- Links Específicos do Aluno -->
                    <?php if(auth()->user()->role === 'aluno'): ?>
                        <a href="<?php echo e(route('student.evolution')); ?>" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 <?php echo e(request()->routeIs('student.evolution') ? 'bg-teal-900/40 text-stone-100 border-l-4 border-teal-400' : 'text-stone-300 hover:bg-teal-950/50 hover:text-stone-100'); ?>">
                            <svg class="w-5 h-5 mr-3 <?php echo e(request()->routeIs('student.evolution') ? 'text-teal-300' : 'text-stone-500 group-hover:text-teal-300'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            Minha Evolução
                        </a>
                    <?php endif; ?>

                <?php else: ?>
                    <a href="/login" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-stone-300 hover:bg-teal-950/50 hover:text-stone-100">
                        <svg class="w-5 h-5 mr-3 text-stone-500 group-hover:text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Fazer Login
                    </a>
                <?php endif; ?>
            </nav>

            <!-- User Profile & Logout (Bottom) -->
            <?php if(auth()->check()): ?>
            <div class="border-t border-teal-900/40 bg-black/25 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-teal-700/20 flex items-center justify-center text-teal-300 font-bold text-lg border border-teal-600/40">
                            <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-stone-100 truncate"><?php echo e(auth()->user()->name); ?></p>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-xs font-medium text-stone-300 hover:text-teal-300 transition-colors flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Sair do Sistema
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Mobile Header -->
            <header class="flex items-center justify-start p-3 bg-gradient-to-r from-zinc-950/95 to-teal-950/70 text-stone-100 lg:hidden shadow-md px-4 border-b border-teal-900/40">
                <button @click="sidebarOpen = !sidebarOpen" class="p-1 rounded-md hover:bg-teal-950/50 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Mensagens Flash -->
                    <?php if(session('success')): ?>
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                            <p class="font-bold">Sucesso!</p>
                            <p><?php echo e(session('success')); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                            <p class="font-bold">Atenção!</p>
                            <ul class="list-disc list-inside mt-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\fitmanager\resources\views/layouts/app.blade.php ENDPATH**/ ?>