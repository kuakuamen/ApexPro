<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Plano alimentar</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 24px;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.45;
        }
        .header {
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 14px;
        }
        .title {
            font-size: 20px;
            margin: 0 0 2px;
            color: #0f172a;
        }
        .subtitle {
            margin: 0;
            color: #334155;
        }
        .meta {
            margin-top: 8px;
            font-size: 11px;
            color: #475569;
        }
        .section {
            margin-top: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .section-head {
            background: #f8fafc;
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-title {
            margin: 0;
            font-size: 15px;
            color: #0f766e;
        }
        .section-sub {
            margin-top: 2px;
            font-size: 11px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: top;
            font-size: 11px;
        }
        th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .text-right { text-align: right; }
        .empty {
            padding: 12px;
            color: #64748b;
        }
        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #64748b;
            text-align: right;
        }
    </style>
</head>
<body>
@php
    $parseCalories = static function ($value): float {
        if ($value === null) {
            return 0.0;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return 0.0;
        }

        $normalized = str_replace(',', '.', $normalized);
        $normalized = preg_replace('/[^0-9.\\-]/', '', $normalized);
        if ($normalized === '' || !is_numeric($normalized)) {
            return 0.0;
        }

        return max(0, (float) $normalized);
    };

    $totalMeals = $diet->meals->count();
    $dailyKcal = $diet->meals->sum(function ($meal) use ($parseCalories) {
        return $meal->foods->sum(fn($food) => $parseCalories($food->calories));
    });
@endphp

<div class="header">
    <h1 class="title">{{ $diet->name }}</h1>
    <p class="subtitle">Objetivo: {{ $diet->goal ?: 'Nao definido' }}</p>
    <div class="meta">
        <div>Aluno: {{ $diet->student->name ?? 'Nao definido' }}</div>
        <div>Profissional: {{ $diet->nutritionist->name ?? 'Nao definido' }}</div>
        <div>Gerado em: {{ optional($generatedAt)->format('d/m/Y H:i') }}</div>
        <div>Resumo: {{ $totalMeals }} refeicao(oes) @if($dailyKcal > 0)- {{ (int) round($dailyKcal) }} kcal/dia @endif</div>
    </div>
</div>

@forelse($diet->meals as $meal)
    @php
        $mealKcal = $meal->foods->sum(fn($food) => $parseCalories($food->calories));
    @endphp
    <section class="section">
        <div class="section-head">
            <h2 class="section-title">{{ $meal->name }}</h2>
            <div class="section-sub">
                Horario: {{ $meal->time ? \Carbon\Carbon::parse($meal->time)->format('H:i') : '--:--' }}
                @if($mealKcal > 0)
                    | {{ (int) round($mealKcal) }} kcal
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Alimento</th>
                    <th>Quantidade</th>
                    <th class="text-right">Kcal</th>
                    <th>Observacao</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meal->foods as $food)
                    <tr>
                        <td>{{ $food->name }}</td>
                        <td>{{ $food->quantity ?: '-' }}</td>
                        <td class="text-right">{{ $food->calories ?: '-' }}</td>
                        <td>{{ $food->observation ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">Sem alimentos cadastrados nesta refeicao.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@empty
    <p class="empty">Nenhuma refeicao cadastrada.</p>
@endforelse

<div class="footer">
    ApexPro - Plano alimentar
</div>
</body>
</html>
