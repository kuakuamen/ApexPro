<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ApexPro - Planos</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/photos/Gemini_Generated_Image_.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background-main font-sans text-text-primary antialiased min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-background-card/80 backdrop-blur-md border-b border-white/5 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('storage/photos/Gemini_Generated_Image_.png') }}" alt="ApexPro Logo" class="w-10 h-10 object-contain">
                    <span class="text-xl font-bold text-text-primary tracking-tight">ApexPro</span>
                </div>
                <div>
                    @if(auth()->check())
                        <a href="{{ route('personal.dashboard') }}" class="text-sm text-text-tertiary hover:text-text-primary transition-colors font-medium">Voltar ao Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-text-tertiary hover:text-text-primary transition-colors font-medium">Login</a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-background-card border-t border-white/5 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-text-tertiary text-sm">
            &copy; {{ date('Y') }} ApexPro. Todos os direitos reservados.
        </div>
    </footer>
</body>
</html>
