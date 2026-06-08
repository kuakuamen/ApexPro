<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Plano de treino - {{ $workout->student->name ?? 'Aluno' }}</title>
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
            border: 1.5px solid #3730a3;
            border-radius: 14px;
            background: #1e1b4b;
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
            color: #c4b5fd;
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
            color: #ddd6fe;
            font-size: 12px;
        }
        .meta-line {
            color: #dbeafe;
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
            border: 1px solid #6366f1;
            background: #312e81;
            color: #e0e7ff;
            font-size: 10px;
            font-weight: 700;
        }
        .status-chip {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 999px;
            border: 1px solid #10b981;
            background: #064e3b;
            color: #d1fae5;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        .status-chip.inactive {
            border-color: #475569;
            background: #1e293b;
            color: #cbd5e1;
        }

        .day {
            width: auto;
            margin-top: 14px;
            border: 1px solid #1f2937;
            border-radius: 14px;
            overflow: hidden;
            background: #111827;
            page-break-inside: avoid;
        }
        .day-head {
            background: #0f172a;
            border-bottom: 1px solid #1f2937;
            padding: 12px 14px;
        }
        .day-title {
            margin: 0 0 4px;
            color: #a5b4fc;
            font-size: 16px;
            font-weight: 700;
        }
        .day-sub {
            color: #93c5fd;
            font-size: 11px;
        }
        table.exercise-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px;
        }
        .exercise-table thead tr {
            background: #1e293b;
        }
        .exercise-table th {
            color: #cbd5e1;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #334155;
        }
        .exercise-table td {
            color: #e5e7eb;
            padding: 8px 10px;
            border-bottom: 1px solid #1f2937;
            vertical-align: top;
            word-wrap: break-word;
        }
        .exercise-table tbody tr:nth-child(even) td {
            background: #0f172a;
        }
        .exercise-table tbody tr:last-child td {
            border-bottom: none;
        }
        .center {
            text-align: center;
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
    $days = $workout->days->sortBy('order')->values();
    $totalExercises = $days->sum(fn($day) => $day->exercises->count());
    $studentName = $workout->student->name ?? 'Nao definido';
    $professionalName = $workout->personal->name ?? 'Nao definido';
@endphp

<div class="report-page">
    <div class="hero">
        <table class="hero-table">
            <tr>
                <td class="hero-left">
                    <p class="eyebrow">Plano de treino</p>
                    <p class="brand">ApexPro</p>
                    <h1 class="title">{{ $workout->name }}</h1>
                    <p class="subtitle">Objetivo: {{ $workout->goal ?: 'Nao definido' }}</p>

                    <div class="meta-line">Aluno: <span class="meta-strong">{{ $studentName }}</span></div>
                    <div class="meta-line">Profissional: <span class="meta-strong">{{ $professionalName }}</span></div>

                    <span class="chip">{{ $days->count() }} {{ $days->count() === 1 ? 'dia' : 'dias' }}</span>
                    <span class="chip">{{ $totalExercises }} {{ $totalExercises === 1 ? 'exercicio' : 'exercicios' }}</span>
                </td>
                <td class="hero-right">
                    <span class="status-chip {{ $workout->is_active ? '' : 'inactive' }}">
                        {{ $workout->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                    <div class="meta-line">Gerado em: <span class="meta-strong">{{ optional($generatedAt)->format('d/m/Y H:i') }}</span></div>
                    <div class="meta-line">Sistema: <span class="meta-strong">ApexPro</span></div>
                    <div class="meta-line">Documento: <span class="meta-strong">Plano de treino</span></div>
                </td>
            </tr>
        </table>
    </div>

    @forelse($days as $day)
        @php
            $exercises = $day->exercises->sortBy('order')->values();
        @endphp
        <section class="day">
            <div class="day-head">
                <h2 class="day-title">{{ $day->name }}</h2>
                <div class="day-sub">{{ $exercises->count() }} {{ $exercises->count() === 1 ? 'exercicio' : 'exercicios' }}</div>
            </div>
            <table class="exercise-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Exercicio</th>
                        <th class="center" style="width: 10%;">Series</th>
                        <th class="center" style="width: 14%;">Repeticoes</th>
                        <th class="center" style="width: 12%;">Descanso</th>
                        <th style="width: 39%;">Observacao</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exercises as $exercise)
                        <tr>
                            <td>{{ $exercise->name }}</td>
                            <td class="center">{{ $exercise->sets ?: '-' }}</td>
                            <td class="center">{{ $exercise->reps ?: '-' }}</td>
                            <td class="center">{{ $exercise->rest_time ?: '-' }}</td>
                            <td>{{ $exercise->observation ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">Sem exercicios cadastrados neste dia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    @empty
        <p class="empty">Nenhum dia de treino cadastrado.</p>
    @endforelse

    <div class="footer">
        Documento gerado automaticamente pelo ApexPro em {{ optional($generatedAt)->format('d/m/Y \\a\\s H:i') }}.
    </div>
</div>
</body>
</html>
