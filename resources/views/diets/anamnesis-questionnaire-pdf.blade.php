<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Anamnese Nutricional</title>
    <style>
        @page { margin: 22px 26px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.45;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }
        .subtitle {
            margin: 4px 0 0;
            font-size: 11px;
            color: #4b5563;
        }
        .meta {
            margin: 10px 0 0;
            font-size: 11px;
            color: #1f2937;
        }
        .meta span {
            display: inline-block;
            margin-right: 14px;
        }
        .section {
            margin-top: 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            overflow: hidden;
        }
        .section h2 {
            margin: 0;
            padding: 8px 10px;
            background: #f3f4f6;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }
        .section-content {
            padding: 10px 12px;
        }
        .q {
            margin-bottom: 9px;
        }
        .q:last-child {
            margin-bottom: 0;
        }
        .q strong {
            display: block;
            margin-bottom: 3px;
            font-size: 11px;
            color: #111827;
        }
        .options {
            font-size: 11px;
            color: #374151;
        }
        .line {
            border-bottom: 1px solid #9ca3af;
            height: 16px;
            margin-top: 5px;
        }
        .line-lg {
            border-bottom: 1px solid #9ca3af;
            height: 30px;
            margin-top: 5px;
        }
        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Anamnese Nutricional - ApexPro</h1>
        <p class="subtitle">Questionario para o aluno preencher e devolver ao profissional.</p>
        <div class="meta">
            <span><strong>Aluno:</strong> {{ $student->name }}</span>
            <span><strong>Telefone:</strong> {{ $student->phone ?: '-' }}</span>
            <span><strong>Email:</strong> {{ $student->email ?: '-' }}</span>
        </div>
        <div class="meta">
            <span><strong>Profissional:</strong> {{ $professional->name }}</span>
            <span><strong>Gerado em:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="section">
        <h2>Objetivo e corpo</h2>
        <div class="section-content">
            <div class="q">
                <strong>1. Qual e o seu objetivo?</strong>
                <div class="options">[ ] Perder gordura   [ ] Ganhar massa   [ ] Saude geral   [ ] Definicao   [ ] Performance esportiva</div>
            </div>
            <div class="q"><strong>2. Peso atual (kg)</strong><div class="line"></div></div>
            <div class="q"><strong>3. Altura (cm)</strong><div class="line"></div></div>
            <div class="q"><strong>4. Peso desejado (kg)</strong><div class="line"></div></div>
        </div>
    </div>

    <div class="section">
        <h2>Saude</h2>
        <div class="section-content">
            <div class="q">
                <strong>5. Tem alguma doenca diagnosticada?</strong>
                <div class="options">[ ] Diabetes   [ ] Hipertensao   [ ] Colesterol alto   [ ] Tireoide   [ ] Nenhuma   [ ] Outra</div>
            </div>
            <div class="q"><strong>6. Usa algum medicamento continuo? Se sim, qual?</strong><div class="line-lg"></div></div>
            <div class="q">
                <strong>7. Tem alguma restricao ou intolerancia alimentar?</strong>
                <div class="options">[ ] Lactose   [ ] Gluten   [ ] Frutose   [ ] Nenhuma   [ ] Outra</div>
            </div>
            <div class="q">
                <strong>8. Tem alguma alergia alimentar?</strong>
                <div class="options">[ ] Amendoim   [ ] Frutos do mar   [ ] Ovos   [ ] Nozes   [ ] Soja   [ ] Nenhuma   [ ] Outra</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Alimentacao atual</h2>
        <div class="section-content">
            <div class="q"><strong>9. Quantas refeicoes faz por dia?</strong><div class="line"></div></div>
            <div class="q"><strong>10. Quantos litros de agua bebe por dia?</strong><div class="line"></div></div>
            <div class="q">
                <strong>11. Come fora de casa com frequencia?</strong>
                <div class="options">[ ] Todos os dias   [ ] 3-4x por semana   [ ] 1-2x por semana   [ ] Raramente</div>
            </div>
            <div class="q">
                <strong>12. Consome bebida alcoolica?</strong>
                <div class="options">[ ] Nao   [ ] Socialmente   [ ] 1-2x por semana   [ ] Frequentemente</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Preferencias</h2>
        <div class="section-content">
            <div class="q"><strong>13. Quais alimentos voce detesta ou nao consegue comer?</strong><div class="line-lg"></div></div>
            <div class="q"><strong>14. Quais sao seus alimentos favoritos?</strong><div class="line-lg"></div></div>
            <div class="q">
                <strong>15. Segue algum estilo alimentar?</strong>
                <div class="options">[ ] Sem restricao   [ ] Vegetariano   [ ] Vegano   [ ] Low carb   [ ] Cetogenico   [ ] Outro</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Treino e rotina</h2>
        <div class="section-content">
            <div class="q">
                <strong>16. Em qual periodo do dia costuma treinar?</strong>
                <div class="options">[ ] Manha   [ ] Tarde   [ ] Noite   [ ] Varia</div>
            </div>
            <div class="q">
                <strong>17. Come algo antes do treino?</strong>
                <div class="options">[ ] Sim, sempre   [ ] As vezes   [ ] Nao, treino em jejum</div>
            </div>
            <div class="q">
                <strong>18. Come algo logo apos o treino?</strong>
                <div class="options">[ ] Sim, sempre   [ ] As vezes   [ ] Nao costumo</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Comportamento</h2>
        <div class="section-content">
            <div class="q">
                <strong>19. Tem habito de comer quando esta ansioso ou estressado?</strong>
                <div class="options">[ ] Nunca   [ ] Raramente   [ ] As vezes   [ ] Frequentemente</div>
            </div>
            <div class="q">
                <strong>20. Ja tentou fazer dieta antes? Conseguiu manter?</strong>
                <div class="options">[ ] Nunca tentei   [ ] Tentei e mantive   [ ] Tentei mas nao consegui manter</div>
            </div>
            <div class="q"><strong>21. Horario com mais fome</strong><div class="line"></div></div>
            <div class="q"><strong>22. Horario com menos fome</strong><div class="line"></div></div>
        </div>
    </div>

    <div class="footer">
        Assinatura do aluno: ______________________________________
    </div>
</body>
</html>
