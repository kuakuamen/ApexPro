<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ApexPro - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900"> <!-- Fundo escuro para combinar com a logo -->
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo Section -->
            <div class="flex justify-center mb-8">
                <div class="relative w-48 h-48 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full blur-2xl opacity-75 animate-pulse"></div>
                    <img src="{{ asset('storage/photos/Gemini_Generated_Image_.png') }}" alt="ApexPro Logo" class="relative w-28 h-28 rounded-full shadow-2xl object-cover border-4 border-gray-800">
                </div>
            </div>
            <h2 class="mt-4 text-center text-4xl font-bold bg-gradient-to-r from-cyan-400 via-teal-400 to-purple-400 bg-clip-text text-transparent">
                ApexPro
            </h2>
            <p class="mt-3 text-center text-base text-gray-400 font-medium">
                Gestão de Alta Performance
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-xl border border-gray-700 py-10 px-6 shadow-2xl sm:rounded-2xl sm:px-12">
                <form class="space-y-8" action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="rounded-lg bg-red-900 bg-opacity-30 border border-red-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-300">
                                        {{ $errors->first() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-200">
                            Email
                        </label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="username" required 
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-200"
                                placeholder="seu@email.com"
                                value="{{ old('email') }}">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-200">
                            Senha
                        </label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-200"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" @checked(old('remember', true)) class="h-4 w-4 text-cyan-500 focus:ring-cyan-400 border-gray-600 rounded bg-gray-700">
                        <label for="remember" class="ml-2 block text-sm text-gray-300">
                            Lembrar de mim
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg shadow-lg text-base font-bold text-white bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-cyan-500 transform hover:scale-105 transition duration-200 ease-in-out">
                            Entrar
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} ApexPro. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const storageKey = 'apexpro_last_login_email';
            const form = document.querySelector('form[action="{{ route('login') }}"]');
            const emailInput = document.getElementById('email');
            const rememberInput = document.getElementById('remember');

            if (!form || !emailInput || !rememberInput) return;

            const savedEmail = localStorage.getItem(storageKey);
            if (!emailInput.value && savedEmail) {
                emailInput.value = savedEmail;
            }

            form.addEventListener('submit', function () {
                const currentEmail = (emailInput.value || '').trim();

                if (!currentEmail) return;

                if (rememberInput.checked) {
                    localStorage.setItem(storageKey, currentEmail);
                } else {
                    localStorage.removeItem(storageKey);
                }
            });
        })();
    </script>
</body>
</html>
