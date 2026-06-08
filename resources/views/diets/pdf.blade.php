<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Plano alimentar - {{ $diet->student->name ?? 'Aluno' }}</title>
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
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #e5e7eb;
            font-size: 11px;
            line-height: 1.45;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .report-page {
            min-height: 100vh;
            background: #111827;
            padding: 24px 38px 24px 24px;
        }
        .hero {
            width: auto;
            border: 1.5px solid #0f766e;
            border-radius: 14px;
            background: #064e3b;
            padding: 18px 20px;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .hero-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .hero-left {
            width: 64%;
            vertical-align: top;
            padding-right: 14px;
        }
        .hero-right {
            width: 36%;
            vertical-align: top;
            text-align: right;
            padding-left: 14px;
            word-break: break-word;
        }
        .eyebrow {
            margin: 0 0 4px;
            color: #99f6e4;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
        }
        .brand {
            color: #67e8f9;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 10px;
        }
        .title {
            color: #ffffff;
            font-size: 20px;
            line-height: 1.2;
            font-weight: 700;
            margin: 0 0 4px;
        }
        .subtitle {
            margin: 0 0 10px;
            color: #ccfbf1;
            font-size: 12px;
        }
        .meta-line {
            color: #d1fae5;
            font-size: 11px;
            margin-top: 4px;
        }
        .meta-strong {
            color: #ffffff;
            font-weight: 700;
        }
        .chip {
            display: inline-block;
            margin: 8px 6px 0 0;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid #10b981;
            background: #065f46;
            color: #d1fae5;
            font-size: 10px;
            font-weight: 700;
        }
        .status-chip {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 999px;
            border: 1px solid #10b981;
            background: #065f46;
            color: #d1fae5;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .meal {
            width: auto;
            margin-top: 14px;
            border: 1px solid #1f2937;
            border-radius: 14px;
            overflow: hidden;
            background: #111827;
            page-break-inside: avoid;
        }
        .meal-head {
            background: #0f172a;
            border-bottom: 1px solid #1f2937;
            padding: 12px 14px;
        }
        .meal-title {
            margin: 0 0 4px;
            color: #5eead4;
            font-size: 16px;
            font-weight: 700;
        }
        .meal-sub {
            color: #93c5fd;
            font-size: 11px;
        }
        table.food-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px;
        }
        .food-table thead tr {
            background: #1e293b;
        }
        .food-table th {
            color: #cbd5e1;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #334155;
        }
        .food-table td {
            color: #e5e7eb;
            padding: 8px 10px;
            border-bottom: 1px solid #1f2937;
            vertical-align: top;
            word-wrap: break-word;
        }
        .food-table tbody tr:nth-child(even) td {
            background: #0f172a;
        }
        .food-table tbody tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .empty {
            padding: 12px;
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
    $studentName = $diet->student->name ?? 'Nao definido';
    $professionalName = $diet->nutritionist->name ?? 'Nao definido';
@endphp

<div class="report-page">
    <div class="hero">
        <table class="hero-table">
            <tr>
                <td class="hero-left">
                    <p class="eyebrow">Plano alimentar</p>
                    <p class="brand">ApexPro</p>
                    <h1 class="title">{{ $diet->name }}</h1>
                    <p class="subtitle">Objetivo: {{ $diet->goal ?: 'Nao definido' }}</p>

                    <div class="meta-line">Aluno: <span class="meta-strong">{{ $studentName }}</span></div>
                    <div class="meta-line">Profissional: <span class="meta-strong">{{ $professionalName }}</span></div>

                    <span class="chip">{{ $totalMeals }} {{ $totalMeals === 1 ? 'refeicao' : 'refeicoes' }}</span>
                    @if($dailyKcal > 0)
                        <span class="chip">{{ (int) round($dailyKcal) }} kcal/dia</span>
                    @endif
                </td>
                <td class="hero-right">
                    <span class="status-chip">Ativo</span>
                    <div class="meta-line">Gerado em: <span class="meta-strong">{{ optional($generatedAt)->format('d/m/Y H:i') }}</span></div>
                    <div class="meta-line">Sistema: <span class="meta-strong">ApexPro</span></div>
                    <div class="meta-line">Documento: <span class="meta-strong">Plano alimentar</span></div>
                </td>
            </tr>
        </table>
    </div>

    @forelse($diet->meals as $meal)
        @php
            $mealKcal = $meal->foods->sum(fn($food) => $parseCalories($food->calories));
        @endphp
        <section class="meal">
            <div class="meal-head">
                <h2 class="meal-title">{{ $meal->name }}</h2>
                <div class="meal-sub">
                    Horario: {{ $meal->time ? \Carbon\Carbon::parse($meal->time)->format('H:i') : '--:--' }}
                    @if($mealKcal > 0)
                        | {{ (int) round($mealKcal) }} kcal
                    @endif
                </div>
            </div>

            <table class="food-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Alimento</th>
                        <th style="width: 18%;">Quantidade</th>
                        <th class="text-right" style="width: 12%;">Kcal</th>
                        <th style="width: 45%;">Observacao</th>
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
        Documento gerado automaticamente pelo ApexPro em {{ optional($generatedAt)->format('d/m/Y \\a\\s H:i') }}.
    </div>
</div>
</body>
</html>
