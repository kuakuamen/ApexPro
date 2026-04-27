<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anamnese enviada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div class="max-w-xl mx-auto px-4 py-12">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
            <div class="px-6 py-6 border-b border-slate-700">
                <h1 class="text-2xl font-bold">Respostas enviadas com sucesso</h1>
                <p class="text-slate-300 text-sm mt-2">
                    Obrigado, <strong>{{ $student->name }}</strong>. Sua anamnese nutricional foi enviada para <strong>{{ $professional->name }}</strong>.
                </p>
            </div>

            <div class="px-6 py-6 space-y-4">
                <div class="rounded-lg border border-emerald-500/35 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    O profissional ja pode usar suas respostas para montar o plano alimentar.
                </div>

                <p class="text-xs text-slate-400">
                    Este formulario pode expirar conforme o prazo definido no link compartilhado.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
