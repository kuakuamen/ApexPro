@extends('layouts.guest_plans')

@section('content')
<div class="py-16 bg-zinc-950 min-h-screen" x-data="pixPolling()">
    <div class="mx-auto max-w-lg px-6">
        <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-8 shadow-xl text-center">
            <div class="w-16 h-16 rounded-full bg-teal-500/10 flex items-center justify-center mx-auto mb-6 ring-1 ring-teal-500/30">
                <svg class="w-8 h-8 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>

            <h1 class="text-2xl font-bold text-white mb-2">Pagamento via PIX</h1>
            <p class="text-sm text-zinc-400 mb-8">Escaneie o QR Code ou copie o codigo para pagar</p>

            @if($transaction->pix_qr_code_base64)
            <div class="bg-white rounded-xl p-4 inline-block mb-6">
                <img src="data:image/png;base64,{{ $transaction->pix_qr_code_base64 }}" alt="QR Code PIX" class="w-48 h-48">
            </div>
            @endif

            @if($transaction->pix_qr_code)
            <div class="mb-6">
                <label class="block text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Codigo PIX (copia e cola)</label>
                <div class="flex items-center gap-2">
                    <input type="text" value="{{ $transaction->pix_qr_code }}" readonly id="pix-code"
                        class="block w-full rounded-lg border-0 bg-zinc-800 py-2.5 px-3 text-xs text-zinc-300 ring-1 ring-inset ring-white/10 truncate">
                    <button type="button" @click="copyCode()" class="shrink-0 px-3 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white text-xs font-medium transition-colors">
                        <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                    </button>
                </div>
            </div>
            @endif

            <div class="mb-6 p-3 rounded-lg bg-zinc-800/50 border border-white/5">
                <p class="text-xs text-zinc-500 mb-1">Este codigo expira em</p>
                <p class="text-lg font-bold text-white" x-text="countdown"></p>
            </div>

            <div class="mb-6">
                <template x-if="status === 'pending'">
                    <div class="flex items-center justify-center gap-2 text-yellow-400">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span class="text-sm font-medium">Aguardando pagamento...</span>
                    </div>
                </template>
                <template x-if="status === 'approved'">
                    <div class="flex items-center justify-center gap-2 text-teal-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Pagamento aprovado!</span>
                    </div>
                </template>
            </div>

            <a href="{{ route('plans.index') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Voltar aos planos</a>
        </div>
    </div>
</div>

<script>
function pixPolling() {
    return {
        status: 'pending',
        copied: false,
        countdown: '--:--',
        expiresAt: new Date('{{ optional($transaction->pix_expires_at)->toIso8601String() ?? now()->addMinutes(10)->toIso8601String() }}'),
        interval: null,
        countdownInterval: null,

        init() {
            this.startPolling();
            this.startCountdown();
        },

        startPolling() {
            this.interval = setInterval(async () => {
                try {
                    const res = await fetch('{{ route("subscription.status", $transaction->mp_external_reference) }}');
                    const data = await res.json();
                    this.status = data.status;
                    if (data.is_approved) {
                        clearInterval(this.interval);
                        setTimeout(() => {
                            window.location.href = '{{ route("personal.dashboard") }}';
                        }, 2000);
                    }
                } catch (e) {}
            }, 5000);
        },

        startCountdown() {
            this.countdownInterval = setInterval(() => {
                const diff = this.expiresAt - new Date();
                if (diff <= 0) {
                    this.countdown = 'Expirado';
                    clearInterval(this.countdownInterval);
                    clearInterval(this.interval);
                    return;
                }
                const m = Math.floor(diff / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                this.countdown = `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
            }, 1000);
        },

        copyCode() {
            navigator.clipboard.writeText(document.getElementById('pix-code').value);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        destroy() {
            clearInterval(this.interval);
            clearInterval(this.countdownInterval);
        }
    };
}
</script>
@endsection