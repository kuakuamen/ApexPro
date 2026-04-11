@extends('layouts.guest_plans')

@section('content')
<div class="py-16 bg-zinc-950 min-h-screen">
    <div class="mx-auto max-w-lg px-6">
        <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-8 shadow-xl text-center">

            @if($transaction->status === 'approved')
            <div class="w-16 h-16 rounded-full bg-teal-500/10 flex items-center justify-center mx-auto mb-6 ring-1 ring-teal-500/30">
                <svg class="w-8 h-8 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Pagamento Aprovado!</h1>
            <p class="text-sm text-zinc-400 mb-8">Sua assinatura foi ativada com sucesso. Bem-vindo ao ApexPro!</p>
            <a href="{{ route('personal.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-sm font-bold transition-colors">
                Acessar Dashboard
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>

            @elseif(in_array($transaction->status, ['rejected', 'cancelled']))
            <div class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-6 ring-1 ring-red-500/30">
                <svg class="w-8 h-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Pagamento Recusado</h1>
            @if($transaction->failure_reason)
            <p class="text-sm text-zinc-400 mb-2">Motivo: <span class="text-red-300">{{ $transaction->failure_reason }}</span></p>
            @endif
            <p class="text-sm text-zinc-400 mb-8">Verifique os dados do cartao e tente novamente.</p>
            <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-zinc-700 hover:bg-zinc-600 text-white text-sm font-bold transition-colors">
                Tentar Novamente
            </a>

            @elseif(in_array($transaction->status, ['in_process', 'pending']))
            <div class="w-16 h-16 rounded-full bg-yellow-500/10 flex items-center justify-center mx-auto mb-6 ring-1 ring-yellow-500/30">
                <svg class="w-8 h-8 text-yellow-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Processando Pagamento</h1>
            <p class="text-sm text-zinc-400 mb-4">A cobranca foi criada. O acesso sera liberado somente apos a confirmacao do pagamento.</p>
            <p id="payment-status-note" class="text-xs text-zinc-500 mb-2">Estamos consultando automaticamente o status da cobranca.</p>
            <p id="payment-status-meta" class="text-xs text-zinc-600 mb-8">Nenhuma confirmacao recebida ainda.</p>
            <a href="{{ route('plans.index') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Voltar aos planos</a>

            @else
            <div class="w-16 h-16 rounded-full bg-zinc-700/40 flex items-center justify-center mx-auto mb-6 ring-1 ring-zinc-600">
                <svg class="w-8 h-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Status do pagamento: {{ $transaction->status }}</h1>
            <p class="text-sm text-zinc-400 mb-8">Atualize a pagina para verificar mudancas.</p>
            <a href="{{ route('plans.index') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Voltar aos planos</a>
            @endif

        </div>
    </div>
</div>

@if(in_array($transaction->status, ['in_process', 'pending']))
<script>
    (() => {
        const pollUrl = @json(route('subscription.status', $transaction->mp_external_reference));
        const resultUrl = @json(route('subscription.payment-result', $transaction->mp_external_reference));
        const dashboardUrl = @json(route('personal.dashboard'));
        const noteEl = document.getElementById('payment-status-note');
        const metaEl = document.getElementById('payment-status-meta');
        const startedAt = Date.now();
        let lastStatus = @json($transaction->status);
        let timerId = null;

        const terminalStatuses = new Set(['approved', 'rejected', 'cancelled', 'refunded', 'charged_back']);

        function formatElapsed(ms) {
            const totalSeconds = Math.max(0, Math.floor(ms / 1000));
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return `${minutes}m ${seconds.toString().padStart(2, '0')}s`;
        }

        function setMeta(message) {
            if (metaEl) {
                metaEl.textContent = message;
            }
        }

        async function pollStatus() {
            try {
                const response = await fetch(pollUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                lastStatus = data.status || lastStatus;

                if (data.is_approved || data.status === 'approved') {
                    if (noteEl) {
                        noteEl.textContent = 'Pagamento confirmado. Redirecionando para o dashboard...';
                    }
                    setMeta(`Aprovado apos ${formatElapsed(Date.now() - startedAt)}.`);
                    clearInterval(timerId);
                    window.location.href = dashboardUrl;
                    return;
                }

                if (terminalStatuses.has(data.status) && data.status !== 'approved') {
                    if (noteEl) {
                        noteEl.textContent = 'Status final recebido. Atualizando a tela...';
                    }
                    setMeta(`Status final: ${data.status}.`);
                    clearInterval(timerId);
                    window.location.href = resultUrl;
                    return;
                }

                setMeta(`Ultima consulta: ${formatElapsed(Date.now() - startedAt)} aguardando confirmacao (${lastStatus}).`);
            } catch (error) {
                setMeta('Falha temporaria ao consultar o status. Tentando novamente...');
            }
        }

        pollStatus();
        timerId = setInterval(pollStatus, 5000);

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                pollStatus();
            }
        });
    })();
</script>
@endif
@endsection
