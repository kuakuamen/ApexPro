@extends('layouts.app')

@section('content')
<div class="py-6 space-y-5" x-data="{ showForm: false }">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-100">Pagamentos</h1>
        <button @click="showForm = !showForm" class="flex items-center gap-2 px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm font-medium transition-all">
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

        <form method="POST" action="{{ route('personal.financial.payments.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
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
                <label class="block text-xs text-slate-400 mb-1.5">Data do Pagamento *</label>
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

    {{-- Filtros --}}
    <form method="GET" action="{{ route('personal.financial.payments') }}" class="flex flex-wrap gap-3">
        <select name="status" class="bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2 text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
            <option value="">Todos os status</option>
            <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Pago</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente (dentro do prazo)</option>
            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencido / Atrasado</option>
        </select>
        <select name="student_id" class="bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2 text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
            <option value="">Todos os alunos</option>
            @foreach($students as $s)
                <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
        <input type="month" name="month" value="{{ request('month') }}" class="bg-slate-800/60 border border-slate-700/50 rounded-xl px-3 py-2 text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
        <button type="submit" class="px-4 py-2 bg-cyan-600/20 border border-cyan-500/40 rounded-xl text-cyan-300 text-sm hover:bg-cyan-600/30 transition-all">Filtrar</button>
        <a href="{{ route('personal.financial.payments') }}" class="px-4 py-2 rounded-xl text-sm text-slate-400 hover:text-slate-200">Limpar</a>
    </form>

    {{-- Tabela --}}
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl overflow-hidden">
        @if($payments->isEmpty())
            <div class="p-12 text-center"><p class="text-slate-400">Nenhum pagamento encontrado.</p></div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Aluno</th>
                    <th class="px-5 py-3 text-left">Plano</th>
                    <th class="px-5 py-3 text-right">Valor</th>
                    <th class="px-5 py-3 text-center">Vencimento</th>
                    <th class="px-5 py-3 text-center">Pago em</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Ação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40">
                @foreach($payments as $payment)
                @php
                    $sc = ['paid' => 'emerald', 'pending' => 'yellow', 'overdue' => 'red'];
                    $c  = $sc[$payment->status] ?? 'gray';
                @endphp
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-3 text-slate-100 font-medium">{{ $payment->student->name }}</td>
                    <td class="px-5 py-3 text-slate-300">{{ $payment->studentPlan?->financialPlan?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-emerald-400">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                    <td class="px-5 py-3 text-center text-slate-300">{{ $payment->due_date->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-center text-slate-400">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : '—' }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $c }}-500/15 text-{{ $c }}-300">
                            {{ $payment->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($payment->status !== 'paid')
                        <form method="POST" action="{{ route('personal.financial.payments.mark-paid', $payment) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-1 bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/40 rounded-lg text-emerald-300 text-xs font-medium transition-all">
                                Marcar Pago
                            </button>
                        </form>
                        @else
                            <span class="text-slate-600 text-xs">—</span>
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
</div>
@endsection
