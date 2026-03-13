<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ApexPro - Gestão de Alta Performance</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#06b6d4">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .hero-glow {
            background: radial-gradient(circle at center, rgba(34, 211, 238, 0.15) 0%, rgba(17, 24, 39, 0) 70%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(34, 211, 238, 0.1);
            border-color: rgba(34, 211, 238, 0.3);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 antialiased selection:bg-cyan-500 selection:text-white">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <!-- Logo Placeholder -->
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                        <span class="font-bold text-white text-xl">A</span>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white">Apex<span class="text-cyan-400">Pro</span></span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Recursos</a>
                    <a href="#testimonials" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Depoimentos</a>
                    <a href="#pricing" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Planos</a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-lg bg-gray-800 text-white border border-gray-700 hover:bg-gray-700 hover:border-gray-600 transition-all text-sm font-medium">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition-colors text-sm font-medium hidden sm:block">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-cyan-600 to-purple-600 text-white font-medium hover:from-cyan-500 hover:to-purple-500 shadow-lg shadow-purple-500/25 transition-all transform hover:scale-105 text-sm">
                            Começar Agora
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 hero-glow pointer-events-none"></div>
        
        <!-- Animated Background Shapes -->
        <div class="absolute top-20 right-0 -mr-20 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 left-0 -ml-20 w-80 h-80 bg-cyan-600/10 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gray-800/50 border border-gray-700 backdrop-blur-sm mb-8 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-cyan-400 mr-2 animate-pulse"></span>
                <span class="text-sm text-cyan-300 font-medium">A plataforma definitiva para Personal Trainers</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 leading-tight">
                Eleve sua Consultoria <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500">ao Nível Pro</span>
            </h1>
            
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-400 mb-10 leading-relaxed">
                Gerencie alunos, treinos, avaliações e pagamentos em um único lugar. 
                Tecnologia de ponta para quem busca alta performance e escalabilidade.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-white text-gray-900 font-bold text-lg hover:bg-gray-100 transition-all transform hover:-translate-y-1 shadow-xl shadow-white/10 flex items-center justify-center gap-2">
                    Criar Conta Grátis
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#demo" class="px-8 py-4 rounded-xl bg-gray-800/50 text-white border border-gray-700 font-semibold text-lg hover:bg-gray-800 transition-all backdrop-blur-sm flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ver Demonstração
                </a>
            </div>

            <!-- Dashboard Preview (Mockup) -->
            <div class="mt-20 relative rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur shadow-2xl overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent z-10"></div>
                <!-- Mockup Content (Abstract) -->
                <div class="p-4 border-b border-gray-800 flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                </div>
                <div class="grid grid-cols-12 gap-6 p-8 opacity-80 group-hover:opacity-100 transition-opacity duration-500">
                    <!-- Sidebar Mock -->
                    <div class="col-span-2 space-y-4">
                        <div class="h-8 w-3/4 bg-gray-800 rounded animate-pulse"></div>
                        <div class="h-4 w-full bg-gray-800/50 rounded"></div>
                        <div class="h-4 w-full bg-gray-800/50 rounded"></div>
                        <div class="h-4 w-full bg-gray-800/50 rounded"></div>
                    </div>
                    <!-- Main Content Mock -->
                    <div class="col-span-10 space-y-6">
                        <div class="flex justify-between">
                            <div class="h-10 w-1/3 bg-gray-800 rounded"></div>
                            <div class="h-10 w-10 bg-cyan-600/20 rounded-full"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-6">
                            <div class="h-32 bg-gray-800/30 border border-gray-700/50 rounded-xl p-4"></div>
                            <div class="h-32 bg-gray-800/30 border border-gray-700/50 rounded-xl p-4"></div>
                            <div class="h-32 bg-gray-800/30 border border-gray-700/50 rounded-xl p-4"></div>
                        </div>
                        <div class="h-64 bg-gray-800/30 border border-gray-700/50 rounded-xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gray-900 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Tudo o que você precisa para <span class="text-cyan-400">escalar</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Ferramentas profissionais projetadas para otimizar seu tempo e entregar resultados incríveis para seus alunos.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8 rounded-2xl bg-gray-800/30 border border-gray-700/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-lg bg-cyan-500/10 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Avaliações Físicas</h3>
                    <p class="text-gray-400 leading-relaxed">Protocolos completos (Pollock, Guedes, etc), bioimpedância e comparação visual de progresso (antes e depois).</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card p-8 rounded-2xl bg-gray-800/30 border border-gray-700/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-lg bg-purple-500/10 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Gestão de Treinos</h3>
                    <p class="text-gray-400 leading-relaxed">Prescrição ágil de treinos com biblioteca de exercícios, vídeos demonstrativos e periodização inteligente.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card p-8 rounded-2xl bg-gray-800/30 border border-gray-700/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-lg bg-green-500/10 flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Controle Financeiro</h3>
                    <p class="text-gray-400 leading-relaxed">Gerencie mensalidades, planos e recebimentos. Notificações automáticas de vencimento para reduzir a inadimplência.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="font-bold text-2xl tracking-tight text-white">Apex<span class="text-cyan-400">Pro</span></span>
                    </div>
                    <p class="text-gray-500 max-w-xs">Transformando a carreira de personal trainers através de tecnologia e alta performance.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6">Produto</h4>
                    <ul class="space-y-4 text-gray-500 text-sm">
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">Recursos</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">Preços</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">App do Aluno</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6">Legal</h4>
                    <ul class="space-y-4 text-gray-500 text-sm">
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">Termos de Uso</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">Privacidade</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">Contato</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-600 text-sm">&copy; {{ date('Y') }} ApexPro. Todos os direitos reservados.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-600 hover:text-white transition-colors"><span class="sr-only">Instagram</span><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.315 2zm-1.08 1.95c-2.316 0-2.584.009-3.495.05-1.036.046-1.6.204-1.974.35-.494.191-.846.42-1.215.789a3.176 3.176 0 00-.789 1.215c-.145.373-.304.938-.35 1.974-.042.91-.05 1.179-.05 3.495 0 2.316.008 2.585.05 3.495.046 1.036.204 1.6.35 1.974.19.494.42.846.789 1.215.368.369.721.597 1.215.789.373.145.938.304 1.974.35.91.042 1.179.05 3.495.05 2.316 0 2.585-.008 3.495-.05 1.036-.046 1.6-.204 1.974-.35.494-.19.846-.42 1.215-.789a3.24 3.24 0 00.789-1.215c.145-.373.304-.938.35-1.974.041-1.08.05-1.353.05-3.66v-.413c0-2.308-.009-2.576-.05-3.66-.046-1.035-.204-1.6-.35-1.974a3.16 3.16 0 00-.789-1.215 3.16 3.16 0 00-1.215-.789c-.373-.145-.938-.304-1.974-.35-.91-.042-1.18-.05-3.495-.05zm1.08 3.53a5.48 5.48 0 100 10.96 5.48 5.48 0 000-10.96zm0 1.95a3.53 3.53 0 110 7.06 3.53 3.53 0 010-7.06zm6.51-4.88a1.3 1.3 0 100 2.6 1.3 1.3 0 000-2.6z" clip-rule="evenodd" /></svg></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
