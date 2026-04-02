@extends('layouts.guest_plans')

@section('content')
<div class="relative isolate overflow-hidden py-24 sm:py-32 bg-background-main">
    <!-- Background effects -->
    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-primary-500 to-secondary-500 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
    </div>
    
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <h2 class="text-base font-bold leading-7 text-primary-400 uppercase tracking-wide">Preços Flexíveis</h2>
            <p class="mt-2 text-4xl font-extrabold tracking-tight text-text-primary sm:text-5xl">Escolha o plano ideal para sua carreira</p>
            <p class="mt-6 text-lg leading-8 text-text-secondary max-w-2xl mx-auto">Potencialize seus resultados com a plataforma completa para personal trainers e nutricionistas. Cancele quando quiser.</p>
        </div>
        
        <div class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-y-8 sm:mt-20 lg:mx-auto lg:max-w-5xl lg:grid-cols-3 lg:gap-x-8">
            @foreach($plans as $plan)
                @php
                    $isPopular = $plan['id'] === 'plan_50'; // Plano Profissional como destaque
                @endphp
                
                <div class="relative flex flex-col justify-between rounded-3xl p-8 xl:p-10 transition-all duration-300 {{ $isPopular ? 'bg-background-card ring-2 ring-primary-500 shadow-2xl shadow-primary-500/20 scale-105 z-10' : 'bg-background-card/60 ring-1 ring-white/10 hover:bg-background-card hover:scale-[1.02]' }}">
                    
                    @if($isPopular)
                        <div class="absolute -top-5 left-0 right-0 mx-auto w-40 rounded-full bg-primary-500 px-4 py-1.5 text-center text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-primary-500/50 flex items-center justify-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Mais Popular
                        </div>
                    @endif

                    <div class="{{ $isPopular ? 'mt-4' : '' }}">
                        <div class="flex items-center justify-between gap-x-4">
                            <h3 id="tier-{{ $plan['id'] }}" class="text-xl font-bold leading-8 text-text-primary">{{ $plan['name'] }}</h3>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-text-tertiary">Ideal para gerenciar até <span class="text-text-primary font-bold">{{ $plan['max_students'] }} alunos</span></p>
                        <p class="mt-6 flex items-baseline gap-x-1">
                            <span class="text-4xl font-extrabold tracking-tight text-text-primary whitespace-nowrap">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                            <span class="text-sm font-semibold leading-6 text-text-tertiary">/mês</span>
                        </p>
                        <ul role="list" class="mt-8 space-y-3 text-base leading-6 text-text-secondary xl:mt-10">
                            @foreach($plan['features'] as $feature)
                                <li class="flex gap-x-3 items-start">
                                    <svg class="h-6 w-5 flex-none text-primary-400 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <a href="{{ route('plans.checkout', $plan['id']) }}" aria-describedby="tier-{{ $plan['id'] }}" class="mt-8 block w-full rounded-xl px-3 py-4 text-center text-base font-bold leading-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 transition-all duration-200 transform hover:-translate-y-1 {{ $isPopular ? 'bg-primary-500 text-white hover:bg-primary-600 focus-visible:outline-primary-500 shadow-lg shadow-primary-500/30' : 'bg-white/10 text-text-primary hover:bg-white/20 focus-visible:outline-white' }}">
                        Começar Agora
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Bottom gradient -->
    <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
        <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-primary-500 to-secondary-500 opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"></div>
    </div>
</div>
@endsection
