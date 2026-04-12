@extends('layouts.guest_plans')

@section('content')
<div class="py-12 sm:py-16 bg-zinc-950 min-h-screen">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">

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

        @if (session('warning'))
            <div class="mb-6 rounded-xl border border-yellow-500/40 bg-yellow-500/10 p-4 text-sm text-yellow-200 flex items-center gap-3">
                <svg class="h-5 w-5 flex-shrink-0 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->has('payment'))
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-200">
                {{ $errors->first('payment') }}
            </div>
        @endif

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
            <section class="lg:col-span-7">
                <form id="checkout-form" method="POST"
                    action="{{ $isRenewal ? route('subscription.renew.process', $plan['id']) : route('plans.process', $plan['id']) }}">
                    @csrf

                    <input type="hidden" name="payment_method" id="payment_method_input" value="{{ $defaultMethod === 'credit_card' ? 'credit_card' : 'pix' }}">

                    @if (!$isRenewal)
                    <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-6 sm:p-8 shadow-xl mb-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Dados de Acesso</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Nome completo *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('name') border-red-500 @enderror">
                                @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">E-mail *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('email') border-red-500 @enderror">
                                @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">CPF *</label>
                                <input type="text" name="cpf" id="cpf_input" value="{{ old('cpf') }}" required maxlength="14" placeholder="000.000.000-00"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('cpf') border-red-500 @enderror">
                                @error('cpf')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

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
                                        <option value="">Mes</option>
                                        @foreach(['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $m)
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

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Genero *</label>
                                <select name="gender" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('gender') border-red-500 @enderror">
                                    <option value="">Selecione</option>
                                    <option value="masculino" @selected(old('gender') === 'masculino')>Masculino</option>
                                    <option value="feminino" @selected(old('gender') === 'feminino')>Feminino</option>
                                    <option value="outro" @selected(old('gender') === 'outro')>Outro</option>
                                </select>
                                @error('gender')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Telefone *</label>
                                <input type="text" name="phone" id="phone_input" value="{{ old('phone') }}" required maxlength="15" placeholder="(00) 00000-0000"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('phone') border-red-500 @enderror">
                                @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">CEP *</label>
                                <input type="text" name="address_cep" id="address_cep_input" value="{{ old('address_cep') }}" required maxlength="9" placeholder="00000-000"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('address_cep') border-red-500 @enderror">
                                @error('address_cep')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Estado *</label>
                                <input type="text" name="address_state" id="address_state_input" value="{{ old('address_state') }}" required maxlength="2" placeholder="PR"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 uppercase text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('address_state') border-red-500 @enderror">
                                @error('address_state')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Cidade *</label>
                                <input type="text" name="address_city" value="{{ old('address_city') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('address_city') border-red-500 @enderror">
                                @error('address_city')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Rua *</label>
                                <input type="text" name="address_street" value="{{ old('address_street') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('address_street') border-red-500 @enderror">
                                @error('address_street')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Numero *</label>
                                <input type="text" name="address_number" value="{{ old('address_number') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('address_number') border-red-500 @enderror">
                                @error('address_number')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Bairro *</label>
                                <input type="text" name="address_neighborhood" value="{{ old('address_neighborhood') }}" required
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('address_neighborhood') border-red-500 @enderror">
                                @error('address_neighborhood')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Profissao</label>
                                <select name="profession"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('profession') border-red-500 @enderror">
                                    <option value="">Selecione</option>
                                    <option value="Personal Trainer" @selected(old('profession') === 'Personal Trainer')>Personal Trainer</option>
                                    <option value="Educador Fisico" @selected(old('profession') === 'Educador Fisico')>Educador Fisico</option>
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

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Senha *</label>
                                <input type="password" name="password" required autocomplete="new-password"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('password') border-red-500 @enderror">
                                @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Confirmar senha *</label>
                                <input type="password" name="password_confirmation" required autocomplete="new-password"
                                    class="w-full rounded-lg bg-zinc-800/80 border border-white/10 px-4 py-2.5 text-white placeholder-zinc-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-6 sm:p-8 shadow-xl">
                        <h2 class="text-lg font-semibold text-white mb-6">Forma de Pagamento</h2>

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
                                class="flex-1 flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-medium transition-all tab-btn relative"
                                onclick="switchTab('card')">
                                @if(!$isRenewal && $trialEnabled)
                                <span style="position:absolute;top:-10px;right:8px;background:#10b981;color:#000;font-size:0.65rem;font-weight:800;padding:2px 8px;border-radius:100px;white-space:nowrap;">{{ $trialDays }} DIAS GRATIS</span>
                                @endif
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Cartao de Credito
                            </button>
                        </div>

                        <div id="panel-pix">
                            <div class="rounded-xl bg-teal-500/10 border border-teal-500/20 p-5 text-sm text-teal-200 flex gap-3 items-start mb-6">
                                <svg class="w-5 h-5 mt-0.5 shrink-0 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Pagamento via PIX</p>
                                    <p class="mt-1 text-teal-300/80">Apos confirmar, voce recebera um QR Code para realizar o pagamento. O acesso e liberado automaticamente apos a confirmacao.</p>
                                </div>
                            </div>
                            <button type="submit" id="btn-pix"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-black font-semibold px-6 py-3.5 transition-all">
                                Gerar QR Code PIX
                            </button>
                        </div>

                        <div id="panel-card" class="hidden">
                            @if(!$isRenewal && $trialEnabled)
                            <div class="mb-5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 p-4 text-sm text-emerald-200 flex gap-3 items-start">
                                <svg class="w-5 h-5 mt-0.5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-emerald-300">{{ $trialDays }} dias gratis para novos usuarios</p>
                                    <p class="mt-1 text-emerald-200/80">
                                        Cadastre seu cartao agora e tenha <strong>{{ $trialDays }} dias de acesso gratuito</strong>.<br>
                                        Apos esse periodo, a cobranca de <strong>R$ {{ number_format($plan['price'], 2, ',', '.') }}/mes</strong> inicia automaticamente. Cancele quando quiser.
                                    </p>
                                </div>
                            </div>
                            @elseif(!$isRenewal)
                            <div class="mb-5 rounded-xl bg-blue-500/10 border border-blue-500/20 p-4 text-sm text-blue-200 flex gap-3 items-start">
                                <svg class="w-5 h-5 mt-0.5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Assinatura mensal sem trial</p>
                                    <p class="mt-1 text-blue-300/80">Ao confirmar, a primeira cobranca do cartao sera processada para iniciar a assinatura mensal.</p>
                                </div>
                            </div>
                            @else
                            <div class="mb-5 rounded-xl bg-blue-500/10 border border-blue-500/20 p-4 text-sm text-blue-200 flex gap-3 items-start">
                                <svg class="w-5 h-5 mt-0.5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Assinatura recorrente mensal</p>
                                    <p class="mt-1 text-blue-300/80">Ao confirmar, sua renovacao entra em processamento. O acesso volta assim que a primeira cobranca da assinatura for confirmada.</p>
                                </div>
                            </div>
                            @endif

                            <div class="space-y-4">
                                <div class="rounded-xl border border-white/10 bg-zinc-800/40 p-5 text-sm text-zinc-300">
                                    <p class="font-medium text-white">Checkout seguro</p>
                                    <p class="mt-2 text-zinc-400">
                                        Ao continuar, voce sera redirecionado para o checkout seguro para concluir o pagamento.
                                    </p>
                                </div>

                                <button type="submit" id="btn-card"
                                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-black font-semibold px-6 py-3.5 transition-all mt-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    {{ $trialEnabled ? 'Continuar para o checkout' : 'Pagar com cartao' }}
                                </button>
                                <p class="text-center text-xs text-zinc-500">Pagamento seguro via checkout hospedado do Asaas.</p>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

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
                            <p class="text-sm text-zinc-400">Ate {{ number_format($plan['max_students']) }} alunos</p>
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

                    @if(!$isRenewal)
                    <div id="summary-pix" class="pt-4 border-t border-white/10">
                        <div class="flex justify-between items-center">
                            <span class="text-zinc-400 text-sm">Total hoje (PIX)</span>
                            <span class="text-2xl font-bold text-white">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                        </div>
                        <p class="mt-3 text-xs text-zinc-500 text-center">Cobranca mensal recorrente. Cancele quando quiser.</p>
                    </div>

                    <div id="summary-card" class="pt-4 border-t border-white/10 hidden">
                        @if($trialEnabled)
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-zinc-400 text-sm">Hoje</span>
                            <span class="text-lg font-bold text-emerald-400">Gratis</span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-zinc-400 text-sm">A partir do fim do trial</span>
                            <span class="text-lg font-bold text-white">R$ {{ number_format($plan['price'], 2, ',', '.') }}/mes</span>
                        </div>
                        <div class="rounded-lg bg-emerald-500/10 border border-emerald-500/20 p-3 text-center">
                            <span class="text-sm font-semibold text-emerald-300">{{ $trialDays }} dias gratis incluidos</span>
                        </div>
                        <p class="mt-3 text-xs text-zinc-500 text-center">Cancele antes do fim do trial e nao sera cobrado o plano.</p>
                        @else
                        <div class="flex justify-between items-center">
                            <span class="text-zinc-400 text-sm">Primeira cobranca</span>
                            <span class="text-2xl font-bold text-white">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                        </div>
                        <p class="mt-3 text-xs text-zinc-500 text-center">A assinatura mensal inicia apos a confirmacao da primeira cobranca no cartao.</p>
                        @endif
                    </div>
                    @else
                    <div class="flex justify-between items-center pt-4 border-t border-white/10">
                        <span class="text-zinc-400 text-sm">Total mensal</span>
                        <span class="text-2xl font-bold text-white">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                    </div>
                    <p class="mt-3 text-xs text-zinc-500 text-center">Sua recorrencia sera reativada e o acesso volta assim que a cobranca for confirmada.</p>
                    @endif
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

<script>
(function () {
    const form = document.getElementById('checkout-form');
    const pmInput = document.getElementById('payment_method_input');
    const birthDay = document.getElementById('birth_day');
    const birthMonth = document.getElementById('birth_month');
    const birthYear = document.getElementById('birth_year');
    const birthInput = document.getElementById('birth_date_input');

    if (birthDay && birthMonth && birthYear && birthInput) {
        const updateBirthDate = () => {
            const d = birthDay.value;
            const m = birthMonth.value;
            const y = birthYear.value;

            birthInput.value = d && m && y
                ? `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`
                : '';
        };

        if (birthInput.value) {
            const parts = birthInput.value.split('-');
            if (parts.length === 3) {
                birthYear.value = parts[0];
                birthMonth.value = String(parseInt(parts[1], 10));
                birthDay.value = String(parseInt(parts[2], 10));
            }
        }

        [birthDay, birthMonth, birthYear].forEach((el) => el.addEventListener('change', updateBirthDate));

        form?.addEventListener('submit', (event) => {
            if (!birthInput.value) {
                event.preventDefault();
                [birthDay, birthMonth, birthYear].forEach((el) => el.classList.add('border-red-500'));
                birthDay.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, true);
    }

    const cpfField = document.getElementById('cpf_input');
    if (cpfField) {
        cpfField.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '').substring(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
            this.value = value;
        });
    }

    const phoneField = document.getElementById('phone_input');
    if (phoneField) {
        phoneField.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '').substring(0, 11);
            value = value.length <= 10
                ? value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
                : value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            this.value = value.trim();
        });
    }

    const cepField = document.getElementById('address_cep_input');
    if (cepField) {
        cepField.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '').substring(0, 8);
            if (value.length > 5) {
                value = `${value.substring(0, 5)}-${value.substring(5)}`;
            }
            this.value = value;
        });
    }

    const stateField = document.getElementById('address_state_input');
    if (stateField) {
        stateField.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-Z]/g, '').substring(0, 2).toUpperCase();
        });
    }

    window.switchTab = function (tab) {
        const tabPix = document.getElementById('tab-pix');
        const tabCard = document.getElementById('tab-card');
        const panelPix = document.getElementById('panel-pix');
        const panelCard = document.getElementById('panel-card');
        const summaryPix = document.getElementById('summary-pix');
        const summaryCard = document.getElementById('summary-card');

        if (tab === 'pix') {
            pmInput.value = 'pix';
            tabPix?.classList.add('tab-active');
            tabCard?.classList.remove('tab-active');
            panelPix?.classList.remove('hidden');
            panelCard?.classList.add('hidden');
            summaryPix?.classList.remove('hidden');
            summaryCard?.classList.add('hidden');
            return;
        }

        pmInput.value = 'credit_card';
        tabCard?.classList.add('tab-active');
        tabPix?.classList.remove('tab-active');
        panelPix?.classList.add('hidden');
        panelCard?.classList.remove('hidden');
        summaryPix?.classList.add('hidden');
        summaryCard?.classList.remove('hidden');
    };

    const defaultMethod = @json($defaultMethod ?? 'pix');
    if (defaultMethod === 'credit_card') {
        switchTab('card');
    } else {
        switchTab('pix');
    }
})();
</script>
@endsection
