@extends('layouts.guest_plans')

@section('content')
<div class="py-12 sm:py-16 bg-zinc-950 min-h-screen">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <div class="mb-10">
            <nav class="flex items-center text-sm font-medium text-zinc-400">
                <a href="{{ route('plans.index') }}" class="hover:text-white transition-colors">Planos</a>
                <svg class="mx-3 h-5 w-5 text-zinc-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-teal-400">{{ $isRenewal ? 'Renovar Plano' : 'Finalizar Compra' }}</span>
            </nav>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                {{ $isRenewal ? 'Renovar ' . $plan['name'] : 'Assinar ' . $plan['name'] }}
            </h1>
        </div>

        {{-- Aviso de conta inativa --}}
        @if (session('warning'))
            <div class="mb-6 rounded-xl border border-yellow-500/40 bg-yellow-500/10 p-4 text-sm text-yellow-200 flex items-center gap-3">
                <svg class="h-5 w-5 flex-shrink-0 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                {{ session('warning') }}
            </div>
        @endif

        {{-- Erros gerais --}}
        @if ($errors->has('payment'))
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-200">
                {{ $errors->first('payment') }}
            </div>
        @endif

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">

            {{-- Formulário principal --}}
            <section class="lg:col-span-7">
                <form id="checkout-form" method="POST"
                    action="{{ $isRenewal ? route('subscription.renew.process', $plan['id']) : route('plans.process', $plan['id']) }}">
                    @csrf

                    {{-- Campos ocultos preenchidos pelo JS --}}
                    <input type="hidden" name="payment_method" id="payment_method_input" value="pix">
                    <input type="hidden" name="card_token" id="card_token_input" value="">
                    <input type="hidden" name="installments" id="installments_input" value="1">

                    {{-- ===== DADOS DE CADASTRO (apenas novos usuários) ===== --}}
                    @if (!$isRenewal)
                    <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-6 sm:p-8 shadow-xl mb-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Dados de Acesso</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Nome --}}
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Nome completo *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('name') border-red-500 @enderror">
                                @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- Email --}}
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">E-mail *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('email') border-red-500 @enderror">
                                @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- CPF --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">CPF *</label>
                                <input type="text" name="cpf" id="cpf_input" value="{{ old('cpf') }}" required maxlength="14" placeholder="000.000.000-00"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('cpf') border-red-500 @enderror">
                                @error('cpf')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- Data de nascimento --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Data de nascimento *</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <select id="birth_day" class="rounded-lg bg-zinc-800/80 border border-white/10 px-3 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('birth_date') border-red-500 @enderror">
                                        <option value="">Dia</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                    <select id="birth_month" class="rounded-lg bg-zinc-800/80 border border-white/10 px-3 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('birth_date') border-red-500 @enderror">
                                        <option value="">Mês</option>
                                        @foreach(['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $m)
                                            <option value="{{ $i + 1 }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                    <select id="birth_year" class="rounded-lg bg-zinc-800/80 border border-white/10 px-3 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('birth_date') border-red-500 @enderror">
                                        <option value="">Ano</option>
                                        @for($y = date('Y'); $y >= 1900; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <input type="hidden" name="birth_date" id="birth_date_input" value="{{ old('birth_date') }}">
                                @error('birth_date')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- Gênero --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Gênero *</label>
                                <select name="gender" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('gender') border-red-500 @enderror">
                                    <option value="">Selecione</option>
                                    <option value="masculino" @selected(old('gender') === 'masculino')>Masculino</option>
                                    <option value="feminino" @selected(old('gender') === 'feminino')>Feminino</option>
                                    <option value="outro" @selected(old('gender') === 'outro')>Outro</option>
                                </select>
                                @error('gender')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- Telefone --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Telefone *</label>
                                <input type="text" name="phone" id="phone_input" value="{{ old('phone') }}" required maxlength="15" placeholder="(00) 00000-0000"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('phone') border-red-500 @enderror">
                                @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- CREF / CRN e Profissão --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Profissão</label>
                                <select name="profession"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('profession') border-red-500 @enderror">
                                    <option value="">Selecione</option>
                                    <option value="Personal Trainer" @selected(old('profession') === 'Personal Trainer')>Personal Trainer</option>
                                    <option value="Educador Físico" @selected(old('profession') === 'Educador Físico')>Educador Físico</option>
                                    <option value="Nutricionista" @selected(old('profession') === 'Nutricionista')>Nutricionista</option>
                                    <option value="Fisioterapeuta" @selected(old('profession') === 'Fisioterapeuta')>Fisioterapeuta</option>
                                    <option value="Outro" @selected(old('profession') === 'Outro')>Outro</option>
                                </select>
                                @error('profession')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">CREF / CRN *</label>
                                <input type="text" name="cref" value="{{ old('cref') }}" maxlength="30" placeholder="Ex: 123456-G/PR" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('cref') border-red-500 @enderror">
                                @error('cref')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- Senha --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Senha *</label>
                                <input type="password" name="password" required autocomplete="new-password"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('password') border-red-500 @enderror">
                                @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            {{-- Confirmar senha --}}
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Confirmar senha *</label>
                                <input type="password" name="password_confirmation" required autocomplete="new-password"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ===== MÉTODO DE PAGAMENTO ===== --}}
                    <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-6 sm:p-8 shadow-xl">
                        <h2 class="text-lg font-semibold text-white mb-6">Forma de Pagamento</h2>

                        {{-- Tabs --}}
                        <div class="flex gap-3 mb-6">
                            <button type="button" id="tab-pix"
                                class="flex-1 flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-medium transition-all tab-btn tab-active"
                                onclick="switchTab('pix')">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.3 13.35L13.35 17.3a1.5 1.5 0 01-2.7 0L6.7 13.35a1.5 1.5 0 010-2.7L10.65 6.7a1.5 1.5 0 012.7 0l3.95 3.95a1.5 1.5 0 010 2.7z"/>
                                </svg>
                                PIX
                            </button>
                            <button type="button" id="tab-card"
                                class="flex-1 flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-medium transition-all tab-btn"
                                onclick="switchTab('card')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Cartão de Crédito
                            </button>
                        </div>

                        {{-- PIX panel --}}
                        <div id="panel-pix">
                            <div class="rounded-xl bg-teal-500/10 border border-teal-500/20 p-5 text-sm text-teal-200 flex gap-3 items-start mb-6">
                                <svg class="w-5 h-5 mt-0.5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Pagamento via PIX</p>
                                    <p class="mt-1 text-teal-300/80">Após confirmar, você receberá um QR Code para realizar o pagamento. O acesso é liberado automaticamente após a confirmação.</p>
                                </div>
                            </div>
                            <button type="submit" id="btn-pix"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-black font-semibold px-6 py-3.5 transition-all">
                                Gerar QR Code PIX
                            </button>
                        </div>

                        {{-- Cartão panel --}}
                        <div id="panel-card" class="hidden">
                            <div class="mb-5 rounded-xl bg-blue-500/10 border border-blue-500/20 p-4 text-sm text-blue-200 flex gap-3 items-start">
                                <svg class="w-5 h-5 mt-0.5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Assinatura recorrente mensal</p>
                                    <p class="mt-1 text-blue-300/80">Seu cartão será cobrado automaticamente todo mês. Cancele quando quiser.</p>
                                </div>
                            </div>

                            <div id="card-errors" class="mb-4 hidden rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-200"></div>
                            <div id="cardPaymentBrick_container"></div>
                        </div>
                    </div>

                </form>
            </section>

            {{-- Resumo --}}
            <section class="lg:col-span-5 mt-8 lg:mt-0">
                <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-6 sm:p-8 shadow-xl sticky top-24">
                    <h3 class="text-xl font-semibold text-white mb-6">Resumo do Plano</h3>

                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-white/10">
                        <div class="h-12 w-12 rounded-xl flex items-center justify-center" style="background: {{ $plan['color'] }}22; border: 1px solid {{ $plan['color'] }}44;">
                            <svg class="w-6 h-6" style="color: {{ $plan['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-white">{{ $plan['name'] }}</p>
                            <p class="text-sm text-zinc-400">Até {{ number_format($plan['max_students']) }} alunos</p>
                        </div>
                    </div>

                    <ul class="space-y-2 mb-6">
                        @foreach ($plan['features'] as $feature)
                        <li class="flex items-center gap-2.5 text-sm text-zinc-300">
                            <svg class="w-4 h-4 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    <div class="flex justify-between items-center pt-4 border-t border-white/10">
                        <span class="text-zinc-400 text-sm">Total mensal</span>
                        <span class="text-2xl font-bold text-white">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                    </div>

                    <p class="mt-3 text-xs text-zinc-500 text-center">Cobrança mensal recorrente. Cancele quando quiser.</p>
                </div>
            </section>

        </div>
    </div>
</div>

<style>
.tab-active {
    background-color: rgb(20 184 166 / 0.15);
    border-color: rgb(20 184 166 / 0.5);
    color: rgb(94 234 212);
}
.tab-btn:not(.tab-active) {
    background-color: rgb(39 39 42 / 0.5);
    border-color: rgb(255 255 255 / 0.1);
    color: rgb(161 161 170);
}
.tab-btn:not(.tab-active):hover {
    border-color: rgb(255 255 255 / 0.2);
    color: white;
}
</style>

<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
(function () {
    const mpPublicKey    = @json($mpPublicKey);
    const planPrice      = Number(@json($plan['price']));
    const form           = document.getElementById('checkout-form');
    const pmInput        = document.getElementById('payment_method_input');
    const cardTokenInput = document.getElementById('card_token_input');
    const installInput   = document.getElementById('installments_input');
    const cardErrors     = document.getElementById('card-errors');
    let brickReady       = false;

    // ── Data de nascimento (3 selects → hidden) ──────────────────────────────
    const birthDay   = document.getElementById('birth_day');
    const birthMonth = document.getElementById('birth_month');
    const birthYear  = document.getElementById('birth_year');
    const birthInput = document.getElementById('birth_date_input');

    function updateBirthDate() {
        const d = birthDay.value, m = birthMonth.value, y = birthYear.value;
        if (d && m && y) {
            birthInput.value = y + '-' + String(m).padStart(2,'0') + '-' + String(d).padStart(2,'0');
        } else {
            birthInput.value = '';
        }
    }

    // Pré-popular se old() tiver valor
    if (birthInput.value) {
        const parts = birthInput.value.split('-');
        if (parts.length === 3) {
            birthYear.value  = parts[0];
            birthMonth.value = parseInt(parts[1]).toString();
            birthDay.value   = parseInt(parts[2]).toString();
        }
    }

    [birthDay, birthMonth, birthYear].forEach(el => el.addEventListener('change', updateBirthDate));

    form.addEventListener('submit', function(e) {
        if (!birthInput.value) {
            e.preventDefault();
            birthDay.classList.add('border-red-500');
            birthMonth.classList.add('border-red-500');
            birthYear.classList.add('border-red-500');
            birthDay.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, true);

    // ── CPF mask ──────────────────────────────────────────────────────────────
    const cpfField = document.getElementById('cpf_input');
    if (cpfField) {
        cpfField.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
            this.value = v;
        });
    }

    // ── Telefone mask ─────────────────────────────────────────────────────────
    const phoneField = document.getElementById('phone_input');
    if (phoneField) {
        phoneField.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 11);
            if (v.length <= 10) {
                v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else {
                v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            }
            this.value = v;
        });
    }

    // ── Tab switching ─────────────────────────────────────────────────────────
    window.switchTab = function (tab) {
        const tabPix    = document.getElementById('tab-pix');
        const tabCard   = document.getElementById('tab-card');
        const panelPix  = document.getElementById('panel-pix');
        const panelCard = document.getElementById('panel-card');

        if (tab === 'pix') {
            pmInput.value = 'pix';
            tabPix.classList.add('tab-active');
            tabCard.classList.remove('tab-active');
            panelPix.classList.remove('hidden');
            panelCard.classList.add('hidden');
        } else {
            pmInput.value = 'credit_card';
            tabCard.classList.add('tab-active');
            tabPix.classList.remove('tab-active');
            panelPix.classList.add('hidden');
            panelCard.classList.remove('hidden');
            if (!brickReady) initBrick();
        }
    };

    // ── MP Brick ──────────────────────────────────────────────────────────────
    function showCardError(msg) {
        cardErrors.textContent = msg || 'Erro no cartão.';
        cardErrors.classList.remove('hidden');
    }

    async function initBrick() {
        if (!mpPublicKey) { showCardError('Chave pública MP não configurada.'); return; }
        brickReady = true;
        try {
            const mp     = new MercadoPago(mpPublicKey, { locale: 'pt-BR' });
            const bricks = mp.bricks();
            await bricks.create('cardPayment', 'cardPaymentBrick_container', {
                initialization: { amount: planPrice },
                customization: {
                    visual: { style: { theme: 'dark' }, hideRedirectionPanel: true },
                    paymentMethods: { maxInstallments: 1 },
                },
                callbacks: {
                    onReady: () => {},
                    onError: (err) => showCardError(err?.message || 'Erro no formulário de cartão.'),
                    onSubmit: (cardFormData) => {
                        cardErrors.classList.add('hidden');
                        const token = cardFormData?.token;
                        if (!token) { showCardError('Token do cartão não gerado. Verifique os dados.'); return; }
                        cardTokenInput.value = token;
                        installInput.value   = cardFormData?.installments || 1;
                        pmInput.value        = 'credit_card';
                        form.submit();
                    },
                },
            });
        } catch (err) {
            showCardError('Erro ao carregar formulário: ' + (err?.message || err));
            brickReady = false;
        }
    }
    // ── Auto-select method (when coming from "Mudar Plano") ──────────────────
    const defaultMethod = @json($defaultMethod ?? 'pix');
    if (defaultMethod === 'credit_card') {
        switchTab('card');
    }

})();
</script>
@endsection
