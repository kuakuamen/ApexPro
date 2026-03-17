@php
    $financialTabs = [
        [
            'key'   => 'dashboard',
            'label' => 'Dashboard',
            'route' => 'personal.financial.dashboard',
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        [
            'key'   => 'plans',
            'label' => 'Planos',
            'route' => 'personal.financial.plans',
            'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        ],
        [
            'key'   => 'vinculos',
            'label' => 'Vínculos',
            'route' => 'personal.financial.student-plans',
            'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        ],
        [
            'key'   => 'payments',
            'label' => 'Pagamentos',
            'route' => 'personal.financial.payments',
            'icon'  => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        ],
        [
            'key'   => 'reports',
            'label' => 'Relatórios',
            'route' => 'personal.financial.reports',
            'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        ],
    ];
    $currentTab = $activeTab ?? 'dashboard';
@endphp

@once
<style>
.fin-nav-scroll::-webkit-scrollbar { display: none; }
</style>
@endonce

<div class="mb-5">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <a href="{{ route('personal.financial.dashboard') }}" class="hover:text-slate-300 transition-colors">Financeiro</a>
        @foreach($financialTabs as $tab)
            @if($tab['key'] === $currentTab && $currentTab !== 'dashboard')
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-300">{{ $tab['label'] }}</span>
            @endif
        @endforeach
    </div>

    {{-- Tab bar --}}
    <div class="fin-nav-scroll flex items-center gap-1 bg-[#0a1120]/60 border border-slate-700/50 rounded-2xl p-1 overflow-x-auto" style="scrollbar-width:none;-ms-overflow-style:none;">
        @foreach($financialTabs as $tab)
        @php $isActive = $tab['key'] === $currentTab; @endphp
        <a href="{{ route($tab['route']) }}"
           class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition-all duration-150 flex-shrink-0
                  {{ $isActive
                       ? 'bg-cyan-500/15 border border-cyan-500/30 text-cyan-300'
                       : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700/50' }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
            </svg>
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>
</div>
