<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Plano de treino</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 24px;
            color: #0f172a;
            font-family: DejaVu Sans, Arial, sans-serif;
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
            margin: 0 0 2px;
            font-size: 20px;
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
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 12px;
        }
        .section-title {
            margin: 0;
            font-size: 15px;
            color: #4338ca;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
            font-size: 11px;
        }
        th {
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .empty {
            color: #64748b;
            padding: 12px;
        }
        .footer {
            margin-top: 16px;
            text-align: right;
            color: #64748b;
            font-size: 10px;
        }
    </style>
</head>
<body>
@php
    $days = $workout->days->sortBy('order')->values();
    $totalExercises = $days->sum(fn($day) => $day->exercises->count());
@endphp

<div class="header">
    <h1 class="title">{{ $workout->name }}</h1>
    <p class="subtitle">Objetivo: {{ $workout->goal ?: 'Nao definido' }}</p>
    <div class="meta">
        <div>Aluno: {{ $workout->student->name ?? 'Nao definido' }}</div>
        <div>Profissional: {{ $workout->personal->name ?? 'Nao definido' }}</div>
        <div>Status: {{ $workout->is_active ? 'Ativo' : 'Inativo' }}</div>
        <div>Resumo: {{ $days->count() }} dia(s) - {{ $totalExercises }} exercicio(s)</div>
        <div>Gerado em: {{ optional($generatedAt)->format('d/m/Y H:i') }}</div>
    </div>
</div>

@forelse($days as $day)
    <section class="section">
        <div class="section-head">
            <h2 class="section-title">{{ $day->name }}</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Exercicio</th>
                    <th>Series</th>
                    <th>Repeticoes</th>
                    <th>Descanso (s)</th>
                    <th>Observacao</th>
                </tr>
            </thead>
            <tbody>
                @forelse($day->exercises->sortBy('order') as $exercise)
                    <tr>
                        <td>{{ $exercise->name }}</td>
                        <td>{{ $exercise->sets ?: '-' }}</td>
                        <td>{{ $exercise->reps ?: '-' }}</td>
                        <td>{{ $exercise->rest_time ?: '-' }}</td>
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
    ApexPro - Plano de treino
</div>
</body>
</html>
