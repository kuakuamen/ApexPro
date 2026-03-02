<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Laudo Biomecânico - {{ $student->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8 max-w-4xl mx-auto">
    
    <!-- Cabeçalho -->
    <div class="border-b-2 border-gray-800 pb-4 mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Laudo Biomecânico & Prescrição</h1>
            <p class="text-sm text-gray-500 mt-1">Gerado por Inteligência Artificial - ApexPro</p>
        </div>
        <div class="text-right">
            <p class="font-bold">{{ $student->name }}</p>
            <p class="text-sm">{{ date('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Análise Postural -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-indigo-700 mb-4 border-l-4 border-indigo-700 pl-3">1. Análise Postural</h2>
        
        <div class="grid grid-cols-2 gap-8 bg-gray-50 p-6 rounded-lg">
            <div>
                <h3 class="font-bold text-gray-700 mb-2">Desvios Identificados</h3>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <li><strong>Lordose:</strong> {{ $analysisResult['posture_analysis']['lordosis'] ?? '-' }}</li>
                    <li><strong>Escoliose:</strong> {{ $analysisResult['posture_analysis']['scoliosis'] ?? '-' }}</li>
                    <li><strong>Ombros:</strong> {{ $analysisResult['posture_analysis']['shoulders'] ?? '-' }}</li>
                    <li><strong>Cabeça:</strong> {{ $analysisResult['posture_analysis']['head_position'] ?? '-' }}</li>
                    <li><strong>Joelhos:</strong> {{ $analysisResult['posture_analysis']['knees'] ?? '-' }}</li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 mb-2">Estratégia Corretiva</h3>
                <div class="mb-3">
                    <span class="text-xs font-bold uppercase bg-green-100 text-green-800 px-2 py-1 rounded">Fortalecer</span>
                    <p class="text-sm mt-1">{{ implode(', ', $analysisResult['suggested_focus']['strengthen'] ?? []) }}</p>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase bg-blue-100 text-blue-800 px-2 py-1 rounded">Alongar</span>
                    <p class="text-sm mt-1">{{ implode(', ', $analysisResult['suggested_focus']['stretch'] ?? []) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fotos (Opcional - Layout Grid) -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-indigo-700 mb-4 border-l-4 border-indigo-700 pl-3">2. Evidências Visuais</h2>
        <div class="grid grid-cols-3 gap-4">
            @if($frontPath) 
                <div class="aspect-w-3 aspect-h-4 bg-gray-200 rounded overflow-hidden">
                    <img src="data:image/png;base64,{{ base64_encode(Storage::disk('private')->get($frontPath)) }}" class="object-cover w-full h-full">
                </div> 
            @endif
            @if($sidePath) 
                <div class="aspect-w-3 aspect-h-4 bg-gray-200 rounded overflow-hidden">
                    <img src="data:image/png;base64,{{ base64_encode(Storage::disk('private')->get($sidePath)) }}" class="object-cover w-full h-full">
                </div> 
            @endif
            @if($backPath) 
                <div class="aspect-w-3 aspect-h-4 bg-gray-200 rounded overflow-hidden">
                    <img src="data:image/png;base64,{{ base64_encode(Storage::disk('private')->get($backPath)) }}" class="object-cover w-full h-full">
                </div> 
            @endif
        </div>
    </div>

    <!-- Treino Prescrito -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-indigo-700 mb-4 border-l-4 border-indigo-700 pl-3">3. Prescrição de Treino</h2>
        <p class="mb-4 text-sm"><strong>Objetivo:</strong> {{ $request->goal }} | <strong>Foco:</strong> {{ $analysisResult['workout_recommendation']['priority'] ?? 'Geral' }}</p>

        <div class="space-y-6">
            @if(isset($request->days))
                @foreach($request->days as $day)
                    <div class="border border-gray-200 rounded-lg overflow-hidden break-inside-avoid">
                        <div class="bg-gray-100 px-4 py-2 font-bold text-gray-800 border-b border-gray-200">
                            {{ $day['name'] }}
                        </div>
                        <table class="w-full text-sm">
                            <thead class="bg-white">
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="px-4 py-2">Exercício</th>
                                    <th class="px-4 py-2 w-16">Séries</th>
                                    <th class="px-4 py-2 w-20">Reps</th>
                                    <th class="px-4 py-2">Notas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if(isset($day['exercises']))
                                    @foreach($day['exercises'] as $exercise)
                                        <tr>
                                            <td class="px-4 py-2 font-medium">{{ $exercise['name'] }}</td>
                                            <td class="px-4 py-2">{{ $exercise['sets'] }}</td>
                                            <td class="px-4 py-2">{{ $exercise['reps'] }}</td>
                                            <td class="px-4 py-2 text-gray-500 italic">{{ $exercise['notes'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-12 text-center text-xs text-gray-400 border-t pt-4">
        <p>Este documento é uma sugestão baseada em análise de inteligência artificial e deve ser validado por um profissional de Educação Física.</p>
        <p>Gerado em {{ date('d/m/Y H:i') }} via ApexPro System.</p>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
