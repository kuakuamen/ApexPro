<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ApexPro - Redefinir Senha</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-900 text-gray-100">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex flex-col items-center mb-8">
                <a href="{{ route('login') }}" class="flex items-center gap-3 mb-4 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                        <span class="font-bold text-white text-2xl">A</span>
                    </div>
                </a>
                <h2 class="text-center text-3xl font-extrabold tracking-tight">Nova senha</h2>
                <p class="mt-2 text-center text-sm text-gray-400">Defina sua nova senha de acesso.</p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 py-10 px-6 shadow-2xl rounded-2xl sm:px-12">
                @if ($errors->any())
                    <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-4 mb-6 text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $email) }}"
                            class="mt-1 block w-full rounded-lg bg-gray-900/50 border border-gray-600 px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">Nova senha</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="mt-1 block w-full rounded-lg bg-gray-900/50 border border-gray-600 px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirmar nova senha</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="mt-1 block w-full rounded-lg bg-gray-900/50 border border-gray-600 px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg text-sm font-bold text-white bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 transition-all">
                        Redefinir senha
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
