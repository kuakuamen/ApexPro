<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; padding: 20px; }
    h1 { font-size: 16px; color: #0f172a; margin-bottom: 2px; }
    h2 { font-size: 12px; color: #334155; margin: 16px 0 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .header-right { text-align: right; font-size: 10px; color: #64748b; }
    .cards { display: flex; gap: 8px; margin-bottom: 14px; }
    .card { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; }
    .card .label { font-size: 9px; color: #64748b; margin-bottom: 2px; }
    .card .value { font-size: 13px; font-weight: bold; }
    .green { color: #059669; }
    .amber { color: #d97706; }
    .red   { color: #dc2626; }
    .slate { color: #475569; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    thead tr { background: #f1f5f9; }
    th { padding: 5px 6px; text-align: left; font-weight: 600; color: #475569; border-bottom: 1px solid #e2e8f0; }
    td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; color: #334155; }
    tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .badge-paid    { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-overdue { background: #fee2e2; color: #991b1b; }
    .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: center; }
    .future { display: flex; gap: 8px; margin-bottom: 14px; }
    .future-card { flex: 1; border: 1px solid #99f6e4; border-radius: 6px; padding: 8px; background: #f0fdf4; }
    .future-card .label { font-size: 9px; color: #6b7280; text-transform: capitalize; }
    .future-card .value { font-size: 13px; font-weight: bold; color: #0d9488; }
    .future-card .sub   { font-size: 9px; color: #6b7280; margin-top: 1px; }
</style>
</head>
<body>

<div class="header">
    <div>
        <h1>Relatório Financeiro</h1>
        <p style="color:#64748b;font-size:10px;margin-top:2px;">
            {{ \Carbon\Carbon::create(null,$filterMonth)->translatedFormat('F') }}/{{ $filterYear }}
            — {{ $personal->name }}
        </p>
    </div>
    <div class="header-right">
        <p>Emitido em: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>

{{-- Resumo do período --}}
<h2>Resumo do Período</h2>
<div class="cards">
    <div class="card">
        <div class="label">Faturado</div>
        <div class="value slate">R$ {{ number_format($totalFaturado, 2, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Recebido</div>
        <div class="value green">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Pendente</div>
        <div class="value amber">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Vencido</div>
        <div class="value red">R$ {{ number_format($totalVencido, 2, ',', '.') }}</div>
    </div>
</div>

{{-- Métricas --}}
<h2>Métricas</h2>
<div class="cards">
    <div class="card">
        <div class="label">MRR</div>
        <div class="value green">R$ {{ number_format($mrr, 2, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Ticket Médio</div>
        <div class="value slate">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Taxa Inadimplência</div>
        <div class="value {{ $taxaInadimplencia > 20 ? 'red' : 'slate' }}">{{ $taxaInadimplencia }}%</div>
    </div>
</div>

{{-- Faturamento Futuro --}}
@if(!empty($faturamentoFuturo))
<h2>Faturamento Futuro (Previsão)</h2>
<div class="future">
    @foreach($faturamentoFuturo as $f)
    <div class="future-card">
        <div class="label">{{ $f['label'] }}</div>
        <div class="value">R$ {{ number_format($f['valor'], 2, ',', '.') }}</div>
        <div class="sub">{{ $f['alunos'] }} aluno(s)</div>
    </div>
    @endforeach
</div>
@endif

{{-- Pagamentos do período --}}
<h2>Pagamentos do Período</h2>
@if($pagamentosMes->isEmpty())
    <p style="color:#94a3b8;font-size:10px;margin-top:6px;">Nenhum pagamento no período.</p>
@else
<table>
    <thead>
        <tr>
            <th>Aluno</th>
            <th>Plano</th>
            <th>Vencimento</th>
            <th>Pagamento</th>
            <th>Valor Original</th>
            <th>Valor Final</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagamentosMes as $pm)
        <tr>
            <td>{{ $pm->student->name ?? '—' }}</td>
            <td>{{ $pm->studentPlan->financialPlan->name ?? '—' }}</td>
            <td>{{ $pm->due_date?->format('d/m/Y') }}</td>
            <td>{{ $pm->paid_at?->format('d/m/Y') ?? '—' }}</td>
            <td>R$ {{ number_format($pm->original_amount ?? $pm->amount, 2, ',', '.') }}</td>
            <td>R$ {{ number_format($pm->amount, 2, ',', '.') }}</td>
            <td>
                <span class="badge badge-{{ $pm->status }}">
                    {{ match($pm->status) { 'paid'=>'Pago','pending'=>'Pendente','overdue'=>'Vencido',default=>$pm->status } }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Inadimplentes --}}
@if($inadimplentes->count() > 0)
<h2>Inadimplentes</h2>
<table>
    <thead>
        <tr>
            <th>Aluno</th>
            <th>Plano</th>
            <th>Vencimento</th>
            <th>Status</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inadimplentes as $in)
        <tr>
            <td>{{ $in->student->name }}</td>
            <td>{{ $in->financialPlan->name ?? '—' }}</td>
            <td>{{ $in->due_date?->format('d/m/Y') }}</td>
            <td><span class="badge badge-overdue">{{ ucfirst($in->status) }}</span></td>
            <td>R$ {{ number_format($in->financialPlan->price ?? 0, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">
    ApexPro — Relatório gerado automaticamente em {{ now()->format('d/m/Y \à\s H:i') }}
</div>

</body>
</html>
