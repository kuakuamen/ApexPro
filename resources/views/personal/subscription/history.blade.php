@extends('layouts.app')

@section('content')
<div class="py-6 space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-100">Histórico de Pagamentos</h1>
            <p class="text-sm text-slate-400 mt-1">Todas as transações da sua assinatura</p>
        </div>
        <a href="{{ route('personal.dashboard') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Voltar</a>
    </div>

    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl overflow-hidden">
        @if($transactions->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="text-slate-400">Nenhuma transação encontrada.</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Data</th>
                    <th class="px-5 py-3 text-left">Plano</th>
                    <th class="px-5 py-3 text-right">Valor</th>
                    <th class="px-5 py-3 text-center">Método</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-left">ID MP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40">
                @foreach($transactions as $tx)
                <tr class="hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-3 text-slate-300">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 text-slate-100 font-medium">{{ $tx->plan_id }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-emerald-400">R$ {{ number_format($tx->amount, 2, ',', '.') }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center gap-1 text-xs text-slate-300">
                            @if($tx->payment_method === 'pix')
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                PIX
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Cartão
                                @if($tx->card_last_four) *{{ $tx->card_last_four }} @endif
                            @endif
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @php
                            $statusColors = [
                                'approved'    => 'bg-emerald-500/15 text-emerald-300',
                                'pending'     => 'bg-yellow-500/15 text-yellow-300',
                                'in_process'  => 'bg-yellow-500/15 text-yellow-300',
                                'rejected'    => 'bg-red-500/15 text-red-300',
                                'cancelled'   => 'bg-slate-500/15 text-slate-400',
                                'refunded'    => 'bg-purple-500/15 text-purple-300',
                                'charged_back' => 'bg-red-500/15 text-red-300',
                            ];
                            $statusLabels = [
                                'approved'    => 'Aprovado',
                                'pending'     => 'Pendente',
                                'in_process'  => 'Processando',
                                'rejected'    => 'Recusado',
                                'cancelled'   => 'Cancelado',
                                'refunded'    => 'Estornado',
                                'charged_back' => 'Chargeback',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$tx->status] ?? 'bg-slate-500/15 text-slate-400' }}">
                            {{ $statusLabels[$tx->status] ?? $tx->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-500 text-xs font-mono">{{ $tx->mp_payment_id ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
