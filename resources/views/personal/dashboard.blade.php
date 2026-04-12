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
            <p class="mt-1 text-xs text-gray-500">Avaliações vencidas</p>
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

    {{-- Card Minha Assinatura --}}
    @php
        $sub = auth()->user()->professionalSubscription;
        $hasPendingReactivation = $sub
            && $sub->status === 'pending'
            && !empty($sub->asaas_subscription_id);
        $showPixRenew = $sub && in_array($sub->status, ['active','overdue']) &&
            ($sub->last_payment_method === 'pix' || ($sub->expires_at && $sub->expires_at->diffInDays(now(), false) >= -7));
    @endphp
    @if($sub)
    <div class="rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-teal-500/10 text-teal-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-400">Minha Assinatura</p>
                    <p class="mt-0.5 text-base font-bold text-white">{{ $sub->plan_name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        @if($sub->status === 'trial')
                            <span class="text-teal-400 font-semibold">Trial gratuito</span>
                            &mdash; expira em {{ $sub->trial_ends_at ? $sub->trial_ends_at->format('d/m/Y') : ($sub->expires_at ? $sub->expires_at->format('d/m/Y') : '—') }}
                        @elseif($sub->status === 'active')
                            <span class="text-green-400 font-semibold">Ativa</span>
                            &mdash; vence em {{ $sub->expires_at ? $sub->expires_at->format('d/m/Y') : '—' }}
                        @elseif($hasPendingReactivation)
                            <span class=\"text-yellow-400 font-semibold\">Reativacao pendente</span>
                        @php
                            $nextBillingDisplay = $sub->display_next_billing_at ?? $sub->next_billing_at;
                        @endphp
                        @if($nextBillingDisplay)
                            &mdash; primeira cobranca em {{ $nextBillingDisplay->format('d/m/Y') }}
                        @endif
                            @if($sub->expires_at && $sub->expires_at->isFuture())
                                &mdash; acesso ate {{ $sub->expires_at->format('d/m/Y') }}
                            @endif
                        @elseif($sub->status === 'cancelled')
                            <span class="text-red-400 font-semibold">Cancelada</span>
                            @if($sub->expires_at && $sub->expires_at->isFuture())
                                &mdash; acesso até {{ $sub->expires_at->format('d/m/Y') }}
                            @endif
                        @elseif($sub->status === 'overdue')
                            <span class="text-yellow-400 font-semibold">Vencida</span>
                        @else
                            <span class="text-gray-400 font-semibold">{{ ucfirst($sub->status) }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('subscription.history') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-600 bg-gray-700/50 hover:bg-gray-600/50 text-gray-300 hover:text-white px-4 py-2 text-sm font-medium transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Histórico
                </a>

                @if($sub->status === 'cancelled' && !$hasPendingReactivation)
                    <a href="{{ route('plans.checkout', $sub->plan_id) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-teal-500/30 bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 hover:text-teal-300 px-4 py-2 text-sm font-medium transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reativar Assinatura
                    </a>
                @endif

                @if(in_array($sub->status, ['active','overdue','trial']) || $hasPendingReactivation)
                    {{-- Renovar com PIX (só quando assinou via PIX ou está vencendo em ≤7 dias) --}}
                    @if($showPixRenew)
                    <form method="POST" action="{{ route('subscription.renew.process', $sub->plan_id) }}">
                        @csrf
                        <input type="hidden" name="payment_method" value="pix">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-teal-500/30 bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 hover:text-teal-300 px-4 py-2 text-sm font-medium transition-all">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11.354 2.646a1 1 0 011.292 0l8 7A1 1 0 0121 11v9a1 1 0 01-1 1h-5v-5H9v5H4a1 1 0 01-1-1v-9a1 1 0 01.354-.762l8-6.592z"/></svg>
                            Renovar com PIX
                        </button>
                    </form>
                    @endif

                    {{-- Mudar Plano --}}
                    <button onclick="document.getElementById('modal-change-plan').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-500/30 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 hover:text-indigo-300 px-4 py-2 text-sm font-medium transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Mudar Plano
                    </button>

                    {{-- Cancelar --}}
                    @if(in_array($sub->status, ['active','trial']) || $hasPendingReactivation)
                    <button onclick="document.getElementById('modal-cancel-sub').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 px-4 py-2 text-sm font-medium transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ $hasPendingReactivation ? 'Cancelar Reativacao' : 'Cancelar Plano' }}
                    </button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Modal: Mudar Plano --}}
    <div id="modal-change-plan" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8 overflow-y-auto">
        <div class="w-full max-w-3xl rounded-2xl border border-gray-700 bg-gray-900 p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white">Escolher Plano</h3>
                <button onclick="document.getElementById('modal-change-plan').classList.add('hidden')" class="text-gray-500 hover:text-gray-300 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($subscriptionPlans as $plan)
                <div class="rounded-xl border {{ $sub->plan_id === $plan['id'] ? 'border-teal-500/50 bg-teal-500/5' : 'border-gray-700 bg-gray-800/50' }} p-5 flex flex-col gap-4">
                    <div>
                        @if($sub->plan_id === $plan['id'])
                            <span class="inline-block mb-2 text-xs font-bold text-teal-400 uppercase tracking-wide">Plano atual</span>
                        @endif
                        <p class="font-bold text-white">{{ $plan['name'] }}</p>
                        <p class="text-2xl font-extrabold text-white mt-1">R$ {{ number_format($plan['price'], 2, ',', '.') }}<span class="text-sm font-normal text-gray-400">/mês</span></p>
                        <p class="text-xs text-gray-400 mt-1">Até {{ $plan['max_students'] }} alunos</p>
                    </div>
                    <div class="flex flex-col gap-2 mt-auto">
                        <a href="{{ route('subscription.renew.checkout', $plan['id']) }}?method=pix"
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-teal-500/30 bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 hover:text-teal-300 px-3 py-2 text-sm font-medium transition-all">
                            <svg class="h-4 w-4" viewBox="0 0 512 512" fill="currentColor"><path d="M242.4 292.5C247.8 287.1 256.1 287.1 261.5 292.5L353.6 384.6C368.7 399.7 368.7 424.6 353.6 439.7C338.5 454.8 313.6 454.8 298.5 439.7L256 397.3L213.5 439.7C198.4 454.8 173.5 454.8 158.4 439.7C143.3 424.6 143.3 399.7 158.4 384.6L250.5 292.5C250.5 292.5 242.4 292.5 242.4 292.5zM261.5 219.5C256.1 224.9 247.8 224.9 242.4 219.5L150.3 127.4C135.2 112.3 135.2 87.4 150.3 72.3C165.4 57.2 190.3 57.2 205.4 72.3L247.9 114.7L290.4 72.3C305.5 57.2 330.4 57.2 345.5 72.3C360.6 87.4 360.6 112.3 345.5 127.4L253.4 219.5C253.4 219.5 261.5 219.5 261.5 219.5z"/></svg>
                            Pagar com PIX
                        </a>
                        <a href="{{ route('subscription.renew.checkout', $plan['id']) }}?method=credit_card"
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-indigo-500/30 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 hover:text-indigo-300 px-3 py-2 text-sm font-medium transition-all">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Pagar com Cartão
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="mt-4 text-xs text-gray-500 text-center">Ao mudar de plano, o novo período começa imediatamente após o pagamento.</p>
        </div>
    </div>

    {{-- Modal de confirmação de cancelamento --}}
    <div id="modal-cancel-sub" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-700 bg-gray-900 p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-white">Cancelar assinatura?</h3>
            <p class="mt-2 text-sm text-gray-400">
                @if($hasPendingReactivation)
                    A reativacao pendente sera cancelada e <strong class="text-red-400">nao havera cobrancas futuras</strong>.
                    @if($sub->expires_at && $sub->expires_at->isFuture())
                        Seu acesso atual permanece ativo ate <strong class="text-white">{{ $sub->expires_at->format('d/m/Y') }}</strong>.
                    @endif
                @else
                    Seu acesso permanece ativo at?? <strong class="text-white">{{ $sub->expires_at ? $sub->expires_at->format('d/m/Y') : 'o fim do per?odo' }}</strong>.
                    Ap??s essa data, o sistema ser?? bloqueado e <strong class="text-red-400">n??o haver?? cobran??as futuras</strong>.
                @endif
            </p>
            <div class="mt-6 flex gap-3 justify-end">
                <button onclick="document.getElementById('modal-cancel-sub').classList.add('hidden')"
                    class="rounded-lg border border-gray-600 bg-gray-700/50 hover:bg-gray-600/50 text-gray-300 px-4 py-2 text-sm font-medium transition-all">
                    Voltar
                </button>
                <form method="POST" action="{{ route('subscription.cancel') }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-red-600 hover:bg-red-500 text-white px-4 py-2 text-sm font-semibold transition-all">
                        Confirmar Cancelamento
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

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
                                    @if($student->profile_photo_url)
                                        <img src="{{ $student->profile_photo_url }}" alt="Foto de {{ $student->name }}" class="h-10 w-10 rounded-full object-cover border border-white/10 shadow-lg">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                    @endif
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
