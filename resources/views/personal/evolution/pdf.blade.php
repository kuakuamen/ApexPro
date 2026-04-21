<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; background: #ffffff; padding: 20px; }
        h1 { font-size: 18px; color: #0f172a; margin-bottom: 2px; }
        h2 { font-size: 13px; color: #1e293b; margin: 16px 0 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        h3 { font-size: 11px; color: #334155; margin: 10px 0 4px; }
        .header { display: table; width: 100%; margin-bottom: 14px; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; font-size: 10px; color: #64748b; }
        .sub { font-size: 10px; color: #64748b; margin-top: 2px; }
        .cards { margin-top: 8px; }
        .card { display: inline-block; width: 32%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; margin-right: 1%; vertical-align: top; }
        .card:last-child { margin-right: 0; }
        .label { font-size: 9px; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .value { font-size: 14px; font-weight: bold; color: #0f172a; }
        .diff { font-size: 10px; color: #64748b; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr { background: #f8fafc; }
        th { padding: 6px 6px; text-align: left; color: #475569; border-bottom: 1px solid #e2e8f0; }
        td { padding: 5px 6px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        th.center, td.center { text-align: center; }
        tr:nth-child(even) td { background: #fcfdff; }
        .delta-good { color: #059669; font-weight: bold; }
        .delta-bad { color: #dc2626; font-weight: bold; }
        .delta-neutral { color: #64748b; font-weight: bold; }
        .small-note { font-size: 9px; color: #64748b; margin-top: 6px; }
        .empty { font-size: 10px; color: #64748b; padding: 8px 0; }
        .footer { margin-top: 18px; text-align: center; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Relatorio de Evolucao</h1>
            <p class="sub">Aluno: {{ $student->name }}</p>
            @if(($comparisonRows['prev_date'] ?? null) && ($comparisonRows['last_date'] ?? null))
                <p class="sub">Comparativo: {{ $comparisonRows['prev_date'] }} ate {{ $comparisonRows['last_date'] }}</p>
            @endif
        </div>
        <div class="header-right">
            <p>Emitido em: {{ $generatedAt->format('d/m/Y H:i') }}</p>
            <p>Sistema: ApexPro</p>
        </div>
    </div>

    <h2>Resumo Atual</h2>
    @if($summary)
        <div class="cards">
            <div class="card">
                <div class="label">Peso Atual</div>
                <div class="value">{{ number_format((float) ($summary['weight']['value'] ?? 0), 1, ',', '.') }} kg</div>
                <div class="diff">Variacao: {{ ($summary['weight']['diff'] ?? null) !== null ? (($summary['weight']['diff'] > 0 ? '+' : '') . number_format((float) $summary['weight']['diff'], 1, ',', '.')) : '—' }}</div>
            </div>
            <div class="card">
                <div class="label">Massa Muscular</div>
                <div class="value">{{ number_format((float) ($summary['muscle_mass']['value'] ?? 0), 1, ',', '.') }} kg</div>
                <div class="diff">Variacao: {{ ($summary['muscle_mass']['diff'] ?? null) !== null ? (($summary['muscle_mass']['diff'] > 0 ? '+' : '') . number_format((float) $summary['muscle_mass']['diff'], 1, ',', '.')) : '—' }}</div>
            </div>
            <div class="card">
                <div class="label">% Gordura</div>
                <div class="value">{{ number_format((float) ($summary['body_fat']['value'] ?? 0), 1, ',', '.') }}%</div>
                <div class="diff">Variacao: {{ ($summary['body_fat']['diff'] ?? null) !== null ? (($summary['body_fat']['diff'] > 0 ? '+' : '') . number_format((float) $summary['body_fat']['diff'], 1, ',', '.')) : '—' }}</div>
            </div>
        </div>
    @else
        <p class="empty">Sem dados suficientes para resumo (necessario pelo menos 2 avaliacoes).</p>
    @endif

    @php
        $hasComparisonRows =
            count($comparisonRows['corpo'] ?? []) > 0 ||
            count($comparisonRows['circs'] ?? []) > 0 ||
            count($comparisonRows['dobras'] ?? []) > 0;
    @endphp

    <h2>Comparativo de Medidas</h2>
    @if($hasComparisonRows)
        @if(count($comparisonRows['corpo'] ?? []) > 0)
            <h3>Composicao Corporal</h3>
            <table>
                <thead>
                    <tr>
                        <th>Medida</th>
                        <th class="center">{{ $comparisonRows['prev_date'] ?? 'Anterior' }}</th>
                        <th class="center">{{ $comparisonRows['last_date'] ?? 'Atual' }}</th>
                        <th class="center">Variacao</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comparisonRows['corpo'] as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td class="center">{{ $row['prev'] }}</td>
                            <td class="center">{{ $row['last'] }}</td>
                            <td class="center delta-neutral">{{ $row['delta_text'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($comparisonRows['circs'] ?? []) > 0)
            <h3>Circunferencias (cm)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Medida</th>
                        <th class="center">{{ $comparisonRows['prev_date'] ?? 'Anterior' }}</th>
                        <th class="center">{{ $comparisonRows['last_date'] ?? 'Atual' }}</th>
                        <th class="center">Variacao</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comparisonRows['circs'] as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td class="center">{{ $row['prev'] }}</td>
                            <td class="center">{{ $row['last'] }}</td>
                            <td class="center delta-neutral">{{ $row['delta_text'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($comparisonRows['dobras'] ?? []) > 0)
            <h3>Dobras Cutaneas (mm)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Medida</th>
                        <th class="center">{{ $comparisonRows['prev_date'] ?? 'Anterior' }}</th>
                        <th class="center">{{ $comparisonRows['last_date'] ?? 'Atual' }}</th>
                        <th class="center">Variacao</th>
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

        <p class="small-note">Legenda: Dobras (verde = reducao, vermelho = aumento). Composicao e Circunferencias ficam neutras.</p>
    @else
        <p class="empty">Sem comparativo disponivel.</p>
    @endif

    <h2>Historico de Avaliacoes</h2>
    @if(count($history) > 0)
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th class="center">Peso (kg)</th>
                    <th class="center">Massa Muscular (kg)</th>
                    <th class="center">% Gordura</th>
                    <th class="center">IMC</th>
                    <th class="center">Cintura (cm)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $row)
                    <tr>
                        <td>{{ $row['date'] ?? '—' }}</td>
                        <td class="center">{{ $row['weight'] ?? '—' }}</td>
                        <td class="center">{{ $row['muscle_mass'] ?? '—' }}</td>
                        <td class="center">{{ $row['body_fat'] ?? '—' }}</td>
                        <td class="center">{{ $row['imc'] ?? '—' }}</td>
                        <td class="center">{{ $row['waist'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty">Sem historico disponivel.</p>
    @endif

    <div class="footer">
        ApexPro - Relatorio gerado automaticamente em {{ $generatedAt->format('d/m/Y \a\s H:i') }}
    </div>
</body>
</html>
