@extends('layouts.guest_plans')

@section('content')
<div class="py-12 sm:py-20 bg-background-main min-h-screen">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <!-- Header Section -->
            <div class="mb-12 text-center animate-fade-in">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-status-error/10 text-status-error mb-6 ring-1 ring-status-error/20 shadow-lg shadow-status-error/10">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-text-primary sm:text-5xl">Assinatura Expirada</h1>
                <p class="mt-4 text-xl text-text-tertiary max-w-2xl mx-auto">
                    Para continuar acessando sua conta e gerenciando seus alunos, é necessário renovar seu plano de acesso.
                </p>
                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-status-error/5 border border-status-error/10 text-sm text-status-error">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-status-error opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-status-error"></span>
                    </span>
                    Seus alunos também perderam o acesso temporariamente.
                </div>
            </div>
            
            <!-- Plans Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach($plans as $plan)
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-2xl blur opacity-0 group-hover:opacity-10 transition duration-500"></div>
                        <div class="relative flex flex-col h-full bg-background-card border border-white/5 rounded-2xl p-8 transition-all duration-300 hover:border-primary-500/50">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-text-primary mb-2">{{ $plan['name'] }}</h3>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-bold text-text-primary">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                                    <span class="text-sm text-text-secondary">/mês</span>
                                </div>
                            </div>

                            <ul class="mb-8 space-y-4 flex-grow">
                                <li class="flex items-center gap-3 text-sm text-text-tertiary">
                                    <svg class="h-5 w-5 text-primary-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Até {{ $plan['max_students'] }} alunos
                                </li>
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-center gap-3 text-sm text-text-tertiary">
                                        <svg class="h-5 w-5 text-primary-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-8">
                                <a href="{{ route('subscription.renew.checkout', $plan['id']) }}" class="block w-full rounded-xl bg-primary-500 px-4 py-3 text-center text-sm font-bold text-white shadow-lg shadow-primary-500/20 hover:bg-primary-600 hover:shadow-primary-500/40 transition-all duration-200 transform hover:-translate-y-0.5">
                                    Escolher este Plano
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer Actions -->
            <div class="mt-12 text-center animate-fade-in" style="animation-delay: 0.2s">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 text-sm font-medium text-text-tertiary hover:text-text-primary transition-colors group">
                        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Sair da conta e voltar depois
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
