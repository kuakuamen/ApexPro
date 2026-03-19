{{-- tailwind-safelist: bg-indigo-500/15 border-indigo-500/30 text-indigo-300 bg-indigo-500/30 text-indigo-200 hover:bg-indigo-500/20 hover:border-indigo-500/50 --}}
@extends('layouts.app')

@section('content')
<div class="py-6 space-y-5" x-data="{
    showForm: false,
    confirmReverse: null,
    confirmPay: null,
    payMethod: '',
    discountType: 'value',
    discountInput: '',
    originalAmount: 0,
    get discountAmount() {
        const d = parseFloat(this.discountInput) || 0;
        if (this.discountType === 'percent') return Math.min(d, 100) / 100 * this.originalAmount;
        return Math.min(d, this.originalAmount);
    },
    get finalAmount() {
        return Math.max(0, this.originalAmount - this.discountAmount);
    },
    openPayModal(id, amount) {
        this.confirmPay = id;
        this.payMethod = '';
        this.discountInput = '';
        this.discountType = 'value';
        this.originalAmount = parseFloat(amount);
    }
}">
    @include('personal.financial._nav', ['activeTab' => 'payments'])

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-100">Pagamentos</h1>
        <button @click="showForm = !showForm"
                class="flex items-center gap-2 px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Registrar Pagamento
        </button>
    </div>

    {{-- Formulário novo pagamento --}}
    <div x-show="showForm" x-cloak class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-slate-300 mb-4">Registrar Novo Pagamento</h3>
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm mb-4 space-y-1">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('personal.financial.payments.store') }}"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
              x-data="{ status: '{{ old('status','pending') }}' }">
            @csrf
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Vínculo Aluno/Plano *</label>
                <select name="student_plan_id" required class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    <option value="">Selecione</option>
                    @foreach($studentPlansActive as $sp)
                        <option value="{{ $sp->id }}" {{ old('student_plan_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->student->name }} — {{ $sp->financialPlan->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Valor (R$) *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" min="0" step="0.01" required
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Data Vencimento *</label>
                <input type="date" name="due_date" value="{{ old('due_date', now()->format('Y-m-d')) }}" required
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Status *</label>
                <select name="status" x-model="status" required class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    <option value="pending">Pendente</option>
                    <option value="paid">Pago</option>
                    <option value="overdue">Atrasado</option>
                </select>
            </div>
            <div x-show="status === 'paid'" x-cloak>
                <label class="block text-xs text-slate-400 mb-1.5">Data do Pagamento</label>
                <input type="date" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d')) }}"
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-xs text-slate-400 mb-1.5">Observações</label>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Opcional"
                    class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            </div>
            <div class="sm:col-span-2 lg:col-span-3 flex justify-end gap-3">
                <button type="button" @click="showForm = false" class="px-4 py-2 rounded-xl text-sm text-slate-400 hover:text-slate-200">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium">Registrar</button>
            </div>
        </form>
    </div>

    {{-- Busca + Abas --}}
    <div class="space-y-3">
        {{-- Campo de busca por aluno --}}
        <form method="GET" action="{{ route('personal.financial.payments') }}"
              id="searchForm" x-data="{ q: '{{ request('search') }}' }">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" x-model="q"
                       x-on:input.debounce.500ms="$el.closest('form').submit()"
                       placeholder="Buscar aluno por nome..."
                       value="{{ request('search') }}"
                       autocomplete="off"
                       class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl pl-9 pr-10 py-2.5 text-slate-100 text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                @if(request('search'))
                <a href="{{ route('personal.financial.payments', ['tab' => $tab]) }}"
                   class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
                @endif
            </div>
        </form>

        {{-- Abas --}}
        <div class="flex gap-1 bg-[#0a1120]/60 border border-slate-700/50 rounded-2xl p-1 overflow-x-auto" style="scrollbar-width:none;-ms-overflow-style:none;">
            @php
                $tabs = [
                    ['key' => 'pending',     'label' => 'Pendentes',      'count' => $countPending,    'color' => 'yellow',  'title' => 'Vencem em até 7 dias'],
                    ['key' => 'all_pending', 'label' => 'Pendências Futuras','count' => $countAllPending, 'color' => 'indigo',  'title' => 'Todos os pagamentos em aberto'],
                    ['key' => 'overdue',     'label' => 'Vencidos',       'count' => $countOverdue,    'color' => 'red',     'title' => 'Prazo expirado'],
                    ['key' => 'paid',        'label' => 'Pagos',          'count' => $countPaid,       'color' => 'emerald', 'title' => 'Pagamentos confirmados'],
                ];
            @endphp
            @foreach($tabs as $t)
            @php $isActive = $tab === $t['key']; @endphp
            <a href="{{ route('personal.financial.payments', array_merge(request()->only('search'), ['tab' => $t['key']])) }}"
               title="{{ $t['title'] }}"
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all duration-150 flex-shrink-0
                      {{ $isActive
                           ? 'bg-' . $t['color'] . '-500/15 border border-' . $t['color'] . '-500/30 text-' . $t['color'] . '-300'
                           : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700/50' }}">
                {{ $t['label'] }}
                @if($t['count'] > 0)
                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-bold
                             {{ $isActive ? 'bg-' . $t['color'] . '-500/30 text-' . $t['color'] . '-200' : 'bg-slate-700 text-slate-300' }}">
                    {{ $t['count'] }}
                </span>
                @endif
            </a>
            @endforeach
        </div>
    </div>

    {{-- Tabela --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl overflow-hidden">
        @if($payments->isEmpty())
            <div class="p-12 text-center">
                <p class="text-slate-400 text-sm">
                    @if($tab === 'pending') Nenhum pagamento com vencimento nos próximos 7 dias.
                    @elseif($tab === 'all_pending') Nenhum pagamento pendente em aberto.
                    @elseif($tab === 'overdue') Nenhum pagamento vencido.
                    @else Nenhum pagamento registrado ainda.
                    @endif
                    @if(request('search')) <br><span class="text-slate-500">Busca: "{{ request('search') }}"</span>@endif
                </p>
            </div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Aluno</th>
                    <th class="px-5 py-3 text-left">Plano</th>
                    <th class="px-5 py-3 text-right">Valor</th>
                    <th class="px-5 py-3 text-center">Vencimento</th>
                    @if($tab === 'paid')
                    <th class="px-5 py-3 text-center">Pago em</th>
                    <th class="px-5 py-3 text-center">Forma</th>
                    @endif
                    <th class="px-5 py-3 text-center">Ação</th>                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40">
                @foreach($payments as $payment)
                @php
                    $isLate = $tab === 'overdue';
                @endphp
                <tr class="{{ $isLate ? 'bg-red-500/5' : '' }} hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-3 font-medium {{ $isLate ? 'text-red-200' : 'text-slate-100' }}">
                        {{ $payment->student->name }}
                    </td>
                    <td class="px-5 py-3 text-slate-300">{{ $payment->studentPlan?->financialPlan?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-right font-semibold {{ $isLate ? 'text-red-400' : 'text-emerald-400' }}">
                        R$ {{ number_format($payment->amount, 2, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-center {{ $isLate ? 'text-red-300' : 'text-slate-300' }}">
                        {{ $payment->due_date->format('d/m/Y') }}
                        @if($isLate)
                            <span class="block text-xs text-red-500 mt-0.5">{{ $payment->due_date->diffForHumans() }}</span>
                        @endif
                    </td>
                    @if($tab === 'paid')
                    <td class="px-5 py-3 text-center text-slate-400">
                        {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @php
                            $methodLabels = ['pix' => 'Pix', 'cartao' => 'Cartão', 'dinheiro' => 'Dinheiro', 'outro' => 'Outro'];
                            $methodColors = ['pix' => 'cyan', 'cartao' => 'purple', 'dinheiro' => 'green', 'outro' => 'slate'];
                            $m = $payment->payment_method;
                        @endphp
                        @if($m)
                        <span class="px-2 py-0.5 rounded-full text-xs bg-{{ $methodColors[$m] ?? 'slate' }}-500/15 text-{{ $methodColors[$m] ?? 'slate' }}-300 border border-{{ $methodColors[$m] ?? 'slate' }}-500/25">
                            {{ $methodLabels[$m] ?? $m }}
                        </span>
                        @else
                        <span class="text-slate-500 text-xs">—</span>
                        @endif
                    </td>
                    @endif
                    <td class="px-5 py-3 text-center">
                        @if($tab !== 'paid')
                            <button type="button"
                                    @click="openPayModal({{ $payment->id }}, {{ $payment->amount }})"
                                    class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/40 rounded-lg text-emerald-300 text-xs font-medium transition-all">
                                Marcar Pago
                            </button>                        @else
                            {{-- Estornar --}}
                            <button type="button"
                                    @click="confirmReverse = {{ $payment->id }}"
                                    class="px-3 py-1.5 bg-orange-600/20 hover:bg-orange-600/40 border border-orange-500/40 rounded-lg text-orange-300 text-xs font-medium transition-all">
                                Estornar
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-700/40">{{ $payments->links() }}</div>
        @endif
    </div>

    {{-- Modal: Confirmar Pagamento --}}
    <div x-show="confirmPay !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
         @click.self="confirmPay = null">
        <div class="bg-[#0f1a2e] border border-emerald-500/30 rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-full bg-emerald-500/15 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-slate-100 font-semibold">Confirmar Pagamento</h3>
                    <p class="text-slate-400 text-xs mt-0.5">Selecione a forma e aplique desconto se necessário.</p>
                </div>
            </div>

            {{-- Forma de Pagamento --}}
            <p class="text-xs text-slate-400 mb-2 font-medium uppercase tracking-wide">Forma de pagamento *</p>
            <div class="grid grid-cols-2 gap-2 mb-5">
                @foreach([['pix','Pix','cyan','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'], ['cartao','Cartão','purple','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'], ['dinheiro','Dinheiro','green','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'], ['outro','Outro','slate','M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z']] as [$val,$label,$color,$icon])
                <button type="button" @click="payMethod = '{{ $val }}'"
                        :class="payMethod === '{{ $val }}' ? 'border-{{ $color }}-400 bg-{{ $color }}-500/20 text-{{ $color }}-200' : 'border-slate-700/60 text-slate-400 hover:border-slate-600 hover:text-slate-200'"
                        class="flex items-center gap-2 px-3 py-2.5 border rounded-xl text-sm transition-all">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Desconto (opcional) --}}
            <div class="border border-slate-700/50 rounded-xl p-4 mb-5 space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Desconto <span class="text-slate-600 normal-case">(opcional)</span></p>
                    {{-- Toggle R$ / % --}}
                    <div class="flex rounded-lg overflow-hidden border border-slate-700/50 text-xs">
                        <button type="button" @click="discountType = 'value'"
                                :class="discountType === 'value' ? 'bg-slate-600 text-white' : 'bg-slate-800/60 text-slate-400 hover:text-slate-200'"
                                class="px-3 py-1 transition-all">R$</button>
                        <button type="button" @click="discountType = 'percent'"
                                :class="discountType === 'percent' ? 'bg-slate-600 text-white' : 'bg-slate-800/60 text-slate-400 hover:text-slate-200'"
                                class="px-3 py-1 transition-all">%</button>
                    </div>
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm pointer-events-none"
                          x-text="discountType === 'value' ? 'R$' : '%'"></span>
                    <input type="number" x-model="discountInput" min="0"
                           :max="discountType === 'percent' ? 100 : originalAmount"
                           step="0.01" placeholder="0,00"
                           class="w-full bg-slate-800/60 border border-slate-700/50 rounded-xl pl-9 pr-3 py-2.5 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
                {{-- Resumo de valores --}}
                <div class="grid grid-cols-3 gap-2 pt-1 text-center text-xs" x-show="originalAmount > 0">
                    <div>
                        <p class="text-slate-500 mb-0.5">Original</p>
                        <p class="text-slate-300 font-medium" x-text="'R$ ' + originalAmount.toFixed(2).replace('.',',')"></p>
                    </div>
                    <div>
                        <p class="text-slate-500 mb-0.5">Desconto</p>
                        <p class="text-orange-300 font-medium" x-text="'- R$ ' + discountAmount.toFixed(2).replace('.',',')"></p>
                    </div>
                    <div>
                        <p class="text-slate-500 mb-0.5">Total Pago</p>
                        <p class="text-emerald-300 font-bold" x-text="'R$ ' + finalAmount.toFixed(2).replace('.',',')"></p>
                    </div>
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex gap-3 justify-end">
                <button @click="confirmPay = null"
                        class="px-4 py-2 rounded-xl text-sm text-slate-400 hover:text-slate-200 transition-colors">
                    Cancelar
                </button>
                @foreach($payments as $payment)
                <form method="POST" action="{{ route('personal.financial.payments.mark-paid', $payment) }}"
                      x-show="confirmPay === {{ $payment->id }}" x-cloak>
                    @csrf @method('PATCH')
                    <input type="hidden" name="payment_method"  :value="payMethod">
                    <input type="hidden" name="discount_type"   :value="discountInput > 0 ? discountType : ''">
                    <input type="hidden" name="discount_value"  :value="discountInput > 0 ? discountInput : ''">
                    <button type="submit"
                            :disabled="!payMethod"
                            :class="payMethod ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-700 text-slate-500 cursor-not-allowed'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-all">
                        Confirmar Pagamento
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal: Confirmar Estorno --}}
    <div x-show="confirmReverse !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
         @click.self="confirmReverse = null">
        <div class="bg-[#0f1a2e] border border-orange-500/30 rounded-2xl p-6 w-full max-w-sm mx-4 shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-orange-500/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-slate-100 font-semibold">Estornar pagamento?</h3>
                    <p class="text-slate-400 text-xs mt-0.5">Esta ação reverte o status para Pendente ou Vencido.</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button @click="confirmReverse = null"
                        class="px-4 py-2 rounded-xl text-sm text-slate-400 hover:text-slate-200 transition-colors">
                    Cancelar
                </button>
                @foreach($payments as $payment)
                @if($payment->status === 'paid')
                <form method="POST" action="{{ route('personal.financial.payments.reverse', $payment) }}"
                      x-show="confirmReverse === {{ $payment->id }}" x-cloak>
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-medium transition-all">
                        Confirmar Estorno
                    </button>
                </form>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
