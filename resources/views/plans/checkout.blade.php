@extends('layouts.guest_plans')

@section('content')
<div class="py-12 sm:py-16 bg-zinc-950 min-h-screen">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mb-10">
            <nav class="flex items-center text-sm font-medium text-zinc-400">
                @if(isset($isRenewal) && $isRenewal)
                    <a href="{{ route('subscription.renew') }}" class="hover:text-white transition-colors">Renovação</a>
                @else
                    <a href="{{ route('plans.index') }}" class="hover:text-white transition-colors">Planos</a>
                @endif
                <svg class="mx-3 h-5 w-5 text-zinc-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="text-teal-400">Checkout</span>
            </nav>
            <h1 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">Finalizar Compra</h1>
        </div>

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
            <!-- Checkout Form -->
            <section class="lg:col-span-7">
                <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-6 sm:p-8 shadow-xl">
                    <form method="POST" action="{{ isset($isRenewal) && $isRenewal ? route('subscription.renew.process', $plan['id']) : route('plans.process', $plan['id']) }}" id="checkout-form">
                        @csrf
                        
                        <h3 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-500/10 text-teal-400 text-sm ring-1 ring-teal-500/50">1</span>
                            Forma de Pagamento
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-8">
                            <!-- PIX Option -->
                            <div class="relative">
                                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-zinc-700 bg-zinc-800/50 p-4 hover:bg-zinc-800 hover:border-zinc-600 has-[:checked]:border-teal-500 has-[:checked]:ring-1 has-[:checked]:ring-teal-500 has-[:checked]:bg-teal-500/5 transition-all group" for="payment_pix">
                                    <input class="peer sr-only" id="payment_pix" type="radio" name="payment_method" value="pix" checked>
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-zinc-700 flex items-center justify-center text-zinc-300 group-hover:text-white transition-colors">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-semibold text-white">PIX</span>
                                            <span class="block text-xs text-zinc-400">Aprovação imediata</span>
                                        </div>
                                    </div>
                                    <div class="h-5 w-5 rounded-full border border-zinc-600 peer-checked:border-teal-500 peer-checked:bg-teal-500 relative flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Credit Card Option -->
                            <div class="relative">
                                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-zinc-700 bg-zinc-800/50 p-4 hover:bg-zinc-800 hover:border-zinc-600 has-[:checked]:border-teal-500 has-[:checked]:ring-1 has-[:checked]:ring-teal-500 has-[:checked]:bg-teal-500/5 transition-all group" for="payment_card">
                                    <input class="peer sr-only" id="payment_card" type="radio" name="payment_method" value="card">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-zinc-700 flex items-center justify-center text-zinc-300 group-hover:text-white transition-colors">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-semibold text-white">Cartão de Crédito</span>
                                            <span class="block text-xs text-zinc-400">Visa, Mastercard, Elo</span>
                                        </div>
                                    </div>
                                    <div class="h-5 w-5 rounded-full border border-zinc-600 peer-checked:border-teal-500 peer-checked:bg-teal-500 relative flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Card Fields (Hidden by default) -->
                        <div id="card-fields" class="space-y-5 mb-8 hidden border-t border-white/5 pt-6 animate-fade-in">
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-2">Número do Cartão</label>
                                <div class="relative">
                                    <input type="text" class="block w-full rounded-lg border-0 bg-zinc-800/50 py-2.5 pl-11 text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-zinc-500 focus:ring-2 focus:ring-inset focus:ring-teal-500 sm:text-sm sm:leading-6 transition-all" placeholder="0000 0000 0000 0000">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">Validade</label>
                                    <input type="text" class="block w-full rounded-lg border-0 bg-zinc-800/50 py-2.5 text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-zinc-500 focus:ring-2 focus:ring-inset focus:ring-teal-500 sm:text-sm sm:leading-6 transition-all" placeholder="MM/AA">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-zinc-300 mb-2">CVC</label>
                                    <div class="relative">
                                        <input type="text" class="block w-full rounded-lg border-0 bg-zinc-800/50 py-2.5 text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-zinc-500 focus:ring-2 focus:ring-inset focus:ring-teal-500 sm:text-sm sm:leading-6 transition-all" placeholder="123">
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-300 mb-2">Nome no Cartão</label>
                                <input type="text" class="block w-full rounded-lg border-0 bg-zinc-800/50 py-2.5 text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-zinc-500 focus:ring-2 focus:ring-inset focus:ring-teal-500 sm:text-sm sm:leading-6 transition-all" placeholder="Como impresso no cartão">
                            </div>
                        </div>

                        <!-- PIX Info -->
                        <div id="pix-info" class="mb-8 rounded-xl bg-teal-500/10 border border-teal-500/20 p-4 flex items-start gap-3 animate-fade-in">
                            <svg class="h-6 w-6 text-teal-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-teal-200">Informação Importante</h4>
                                <p class="text-sm text-teal-200/80 mt-1">Ao confirmar, você será redirecionado para visualizar o QR Code do PIX. O pagamento é processado instantaneamente.</p>
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-teal-600 px-4 py-3.5 text-center text-sm font-bold text-white shadow-lg shadow-teal-900/20 hover:bg-teal-500 hover:shadow-teal-900/40 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600 transition-all duration-200 transform hover:-translate-y-0.5">
                            Confirmar Pagamento de R$ {{ number_format($plan['price'], 2, ',', '.') }}
                        </button>
                        
                        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-zinc-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Pagamento 100% seguro e criptografado</span>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Order Summary -->
            <section class="lg:col-span-5 mt-8 lg:mt-0">
                <div class="bg-zinc-900/50 rounded-2xl border border-white/5 p-6 sm:p-8 shadow-xl sticky top-24">
                    <h3 class="text-xl font-semibold text-white mb-6">Resumo do Pedido</h3>
                    
                    <div class="flow-root">
                        <ul role="list" class="-my-6 divide-y divide-white/10">
                            <li class="flex py-6">
                                <div class="flex-shrink-0">
                                    <div class="h-16 w-16 rounded-xl bg-teal-500/10 flex items-center justify-center text-teal-400 ring-1 ring-teal-500/20">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex flex-1 flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-white">
                                            <h3>{{ $plan['name'] }}</h3>
                                            <p class="ml-4">R$ {{ number_format($plan['price'], 2, ',', '.') }}</p>
                                        </div>
                                        <p class="mt-1 text-sm text-zinc-400">Assinatura Mensal</p>
                                    </div>
                                    <div class="flex flex-1 items-end justify-between text-sm">
                                        <p class="text-zinc-500">Até {{ $plan['max_students'] }} alunos</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="border-t border-white/10 mt-6 pt-6">
                        <div class="flex justify-between text-base font-medium text-zinc-300">
                            <p>Subtotal</p>
                            <p>R$ {{ number_format($plan['price'], 2, ',', '.') }}</p>
                        </div>
                        <div class="flex justify-between text-base font-medium text-zinc-300 mt-2">
                            <p>Taxas</p>
                            <p>R$ 0,00</p>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-white mt-4 pt-4 border-t border-white/10">
                            <p>Total</p>
                            <p class="text-teal-400">R$ {{ number_format($plan['price'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 rounded-lg bg-zinc-800/50 p-4 border border-white/5">
                        <h4 class="text-sm font-semibold text-white mb-2">O que está incluído:</h4>
                        <ul class="text-sm text-zinc-400 space-y-1">
                            @foreach($plan['features'] as $feature)
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>

<script>
    const radioCard = document.getElementById('payment_card');
    const radioPix = document.getElementById('payment_pix');
    const cardFields = document.getElementById('card-fields');
    const pixInfo = document.getElementById('pix-info');

    function togglePayment() {
        if (radioCard.checked) {
            cardFields.classList.remove('hidden');
            pixInfo.classList.add('hidden');
        } else {
            cardFields.classList.add('hidden');
            pixInfo.classList.remove('hidden');
        }
    }

    radioCard.addEventListener('change', togglePayment);
    radioPix.addEventListener('change', togglePayment);
</script>
@endsection
