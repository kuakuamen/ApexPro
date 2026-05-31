<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatorio de Evolucao - {{ $student->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            background: #111827;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #e5e7eb;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .report-page {
            min-height: 100vh;
            background: #111827;
            padding: 26px;
        }
        .header {
            width: 100%;
            border: 1px solid #1f2937;
            border-radius: 14px;
            background: #0f172a;
            padding: 18px 20px;
            margin-bottom: 18px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-left, .header-right {
            vertical-align: top;
        }
        .header-right {
            text-align: right;
        }
        .brand-row {
            margin-bottom: 8px;
        }
        .brand-icon {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: #0f3f3b;
            border: 1px solid #0f766e;
            margin-right: 9px;
            vertical-align: middle;
            text-align: center;
            line-height: 22px;
        }
        .brand-icon-glyph {
            font-size: 12px;
            color: #5eead4;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .brand-title {
            display: inline-block;
            vertical-align: middle;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: .2px;
        }
        .brand-title .apx {
            color: #67e8f9;
        }
        .brand-title .ai {
            color: #a5b4fc;
            font-weight: 400;
        }
        .subtitle {
            font-size: 12px;
            color: #94a3b8;
        }
        .meta-line {
            font-size: 12px;
            color: #cbd5e1;
            margin-top: 4px;
        }
        .meta-strong {
            color: #f8fafc;
            font-weight: 700;
        }
        .status-chip {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 9px;
            border-radius: 999px;
            border: 1px solid #0f766e;
            background: #0f3f3b;
            color: #5eead4;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .section {
            margin-top: 14px;
            border: 1px solid #1f2937;
            border-radius: 14px;
            overflow: hidden;
            background: #111827;
        }
        .section-header {
            padding: 11px 14px;
            background: #0f172a;
            border-bottom: 1px solid #1f2937;
            font-size: 15px;
            font-weight: 700;
            color: #a5b4fc;
        }
        .section-body {
            padding: 14px;
        }

        .cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin: 0 -10px;
        }
        .card {
            width: 33.33%;
            border: 1px solid #334155;
            border-radius: 12px;
            background: #1e293b;
            padding: 12px;
            vertical-align: top;
        }
        .card-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #93c5fd;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 26px;
            line-height: 1.1;
            font-weight: 700;
            color: #ffffff;
        }
        .card-diff {
            margin-top: 6px;
            font-size: 11px;
            color: #94a3b8;
        }
        .diff-good {
            color: #34d399;
            font-weight: 700;
        }
        .diff-bad {
            color: #f87171;
            font-weight: 700;
        }
        .diff-neutral {
            color: #cbd5e1;
            font-weight: 700;
        }

        .block-title {
            font-size: 12px;
            color: #d1d5db;
            font-weight: 700;
            margin: 8px 0 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            table-layout: fixed;
        }
        thead tr {
            background: #1e293b;
        }
        th {
            padding: 7px 7px;
            text-align: left;
            color: #cbd5e1;
            border-bottom: 1px solid #334155;
            font-weight: 700;
        }
        td {
            padding: 6px 7px;
            color: #e5e7eb;
            border-bottom: 1px solid #1f2937;
            vertical-align: top;
            word-wrap: break-word;
        }
        tbody tr:nth-child(even) td {
            background: #0f172a;
        }
        .center {
            text-align: center;
        }
        .empty {
            padding: 8px 2px;
            font-size: 11px;
            color: #94a3b8;
        }
        .helper-note {
            margin-top: 8px;
            font-size: 10px;
            color: #94a3b8;
        }
        .footer {
            margin-top: 18px;
            border-top: 1px solid #1f2937;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
@php
    $professionalName = $professional->name ?? 'Profissional';
    $professionalEmail = $professional->email ?? null;
    $professionalPhone = $professional->phone ?? null;
    $professionalCref = $professional->cref ?? null;
    $periodText = (($comparisonRows['prev_date'] ?? null) && ($comparisonRows['last_date'] ?? null))
        ? (($comparisonRows['prev_date'] ?? '') . ' ate ' . ($comparisonRows['last_date'] ?? ''))
        : 'Sem periodo comparativo';
    $totalAssessments = count($history ?? []);
    $hasComparisonRows =
        count($comparisonRows['corpo'] ?? []) > 0 ||
        count($comparisonRows['circs'] ?? []) > 0 ||
        count($comparisonRows['dobras'] ?? []) > 0;
@endphp

<div class="report-page">
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <div class="brand-row">
                        <span class="brand-icon"><span class="brand-icon-glyph">&#9604;&#9606;&#9608;</span></span>
                        <span class="brand-title"><span class="apx">ApexPro</span> <span class="ai">AI</span></span>
                    </div>
                    <div class="subtitle">Relatorio de Evolucao Fisica - Monitoramento de Performance</div>
                    <div class="meta-line">Aluno: <span class="meta-strong">{{ $student->name }}</span></div>
                    <div class="meta-line">Profissional: <span class="meta-strong">{{ $professionalName }}</span></div>
                    @if($professionalCref)
                        <div class="meta-line">Registro profissional: <span class="meta-strong">{{ $professionalCref }}</span></div>
                    @endif
                    <div class="meta-line">Periodo analisado: <span class="meta-strong">{{ $periodText }}</span></div>
                    <span class="status-chip">Documento confidencial</span>
                </td>
                <td class="header-right">
                    <div class="meta-line">Emitido em: <span class="meta-strong">{{ $generatedAt->format('d/m/Y H:i') }}</span></div>
                    <div class="meta-line">Sistema: <span class="meta-strong">ApexPro</span></div>
                    <div class="meta-line">Total de avaliacoes: <span class="meta-strong">{{ $totalAssessments }}</span></div>
                    @if($professionalEmail)
                        <div class="meta-line">Contato: <span class="meta-strong">{{ $professionalEmail }}</span></div>
                    @endif
                    @if($professionalPhone)
                        <div class="meta-line">Telefone: <span class="meta-strong">{{ $professionalPhone }}</span></div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-header">Resumo Atual</div>
        <div class="section-body">
            @if($summary)
                @php
                    $muscleDiff = $summary['muscle_mass']['diff'] ?? null;
                    $fatDiff = $summary['body_fat']['diff'] ?? null;
                @endphp
                <table class="cards">
                    <tr>
                        <td class="card">
                            <div class="card-label">Peso Atual</div>
                            <div class="card-value">{{ number_format((float) ($summary['weight']['value'] ?? 0), 1, ',', '.') }} kg</div>
                            <div class="card-diff">
                                Variacao:
                                <span class="diff-neutral">
                                    {{ ($summary['weight']['diff'] ?? null) !== null ? (($summary['weight']['diff'] > 0 ? '+' : '') . number_format((float) $summary['weight']['diff'], 1, ',', '.')) : '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="card">
                            <div class="card-label">Massa Muscular</div>
                            <div class="card-value">{{ number_format((float) ($summary['muscle_mass']['value'] ?? 0), 1, ',', '.') }} kg</div>
                            <div class="card-diff">
                                Variacao:
                                <span class="{{ $muscleDiff === null ? 'diff-neutral' : ($muscleDiff > 0 ? 'diff-good' : ($muscleDiff < 0 ? 'diff-bad' : 'diff-neutral')) }}">
                                    {{ $muscleDiff !== null ? (($muscleDiff > 0 ? '+' : '') . number_format((float) $muscleDiff, 1, ',', '.')) : '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="card">
                            <div class="card-label">% Gordura</div>
                            <div class="card-value">{{ number_format((float) ($summary['body_fat']['value'] ?? 0), 1, ',', '.') }}%</div>
                            <div class="card-diff">
                                Variacao:
                                <span class="{{ $fatDiff === null ? 'diff-neutral' : ($fatDiff < 0 ? 'diff-good' : ($fatDiff > 0 ? 'diff-bad' : 'diff-neutral')) }}">
                                    {{ $fatDiff !== null ? (($fatDiff > 0 ? '+' : '') . number_format((float) $fatDiff, 1, ',', '.')) : '-' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
            @else
                <p class="empty">Sem dados suficientes para resumo (necessario pelo menos 2 avaliacoes).</p>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-header">Comparativo de Medidas</div>
        <div class="section-body">
            @if($hasComparisonRows)
                @if(count($comparisonRows['corpo'] ?? []) > 0)
                    <div class="block-title">Composicao Corporal</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:36%;">Medida</th>
                                <th class="center" style="width:21%;">{{ $comparisonRows['prev_date'] ?? 'Anterior' }}</th>
                                <th class="center" style="width:21%;">{{ $comparisonRows['last_date'] ?? 'Atual' }}</th>
                                <th class="center" style="width:22%;">Variacao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparisonRows['corpo'] as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="center">{{ $row['prev'] }}</td>
                                    <td class="center">{{ $row['last'] }}</td>
                                    <td class="center diff-neutral">{{ $row['delta_text'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if(count($comparisonRows['circs'] ?? []) > 0)
                    <div class="block-title">Circunferencias (cm)</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:36%;">Medida</th>
                                <th class="center" style="width:21%;">{{ $comparisonRows['prev_date'] ?? 'Anterior' }}</th>
                                <th class="center" style="width:21%;">{{ $comparisonRows['last_date'] ?? 'Atual' }}</th>
                                <th class="center" style="width:22%;">Variacao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparisonRows['circs'] as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="center">{{ $row['prev'] }}</td>
                                    <td class="center">{{ $row['last'] }}</td>
                                    <td class="center diff-neutral">{{ $row['delta_text'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if(count($comparisonRows['dobras'] ?? []) > 0)
                    <div class="block-title">Dobras Cutaneas (mm)</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:36%;">Medida</th>
                                <th class="center" style="width:21%;">{{ $comparisonRows['prev_date'] ?? 'Anterior' }}</th>
                                <th class="center" style="width:21%;">{{ $comparisonRows['last_date'] ?? 'Atual' }}</th>
                                <th class="center" style="width:22%;">Variacao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparisonRows['dobras'] as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="center">{{ $row['prev'] }}</td>
                                    <td class="center">{{ $row['last'] }}</td>
                                    <td class="center {{ $row['delta_class'] }}">{{ $row['delta_text'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <p class="helper-note">Legenda: dobras cutaneas destacam melhora/piora. Composicao e circunferencias aparecem em cor neutra para leitura tecnica.</p>
            @else
                <p class="empty">Sem comparativo disponivel.</p>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-header">Historico de Avaliacoes</div>
        <div class="section-body">
            @if(count($history) > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width:16%;">Data</th>
                            <th class="center" style="width:16%;">Peso (kg)</th>
                            <th class="center" style="width:18%;">Massa Muscular (kg)</th>
                            <th class="center" style="width:14%;">% Gordura</th>
                            <th class="center" style="width:12%;">IMC</th>
                            <th class="center" style="width:14%;">Cintura (cm)</th>
                            <th class="center" style="width:10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $index => $row)
                            <tr>
                                <td>{{ $row['date'] ?? '-' }}</td>
                                <td class="center">{{ $row['weight'] ?? '-' }}</td>
                                <td class="center">{{ $row['muscle_mass'] ?? '-' }}</td>
                                <td class="center">{{ $row['body_fat'] ?? '-' }}</td>
                                <td class="center">{{ $row['imc'] ?? '-' }}</td>
                                <td class="center">{{ $row['waist'] ?? '-' }}</td>
                                <td class="center">{{ $index === 0 ? 'Atual' : 'Historico' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">Sem historico disponivel.</p>
            @endif
        </div>
    </div>

    <div class="footer">
        Documento gerado automaticamente pelo ApexPro em {{ $generatedAt->format('d/m/Y \\a\\s H:i') }}.
        Uso interno do profissional responsavel pelo aluno.
    </div>
</div>
</body>
</html>
