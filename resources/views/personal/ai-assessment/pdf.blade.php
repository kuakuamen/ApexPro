<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Laudo Postural - {{ $student->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
                background-color: #111827 !important; /* bg-gray-900 */
                color: #ffffff !important;
            }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-100 p-8 max-w-4xl mx-auto min-h-screen">
    
    <!-- Cabeçalho -->
    <div class="border-b border-gray-700 pb-6 mb-10 flex justify-between items-end">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <!-- Logo Simulado -->
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">ApexPro <span class="text-indigo-400 font-light">AI</span></h1>
            </div>
            <p class="text-sm text-gray-400">Laudo de Avaliação Física & Prescrição Inteligente</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-white text-lg">{{ $student->name }}</p>
            <p class="text-sm text-gray-400">Gerado em {{ date('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Análise Postural -->
    <div class="mb-10">
        <h2 class="text-xl font-bold text-indigo-400 mb-6 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            1. Análise Postural
        </h2>
        
        <div class="grid grid-cols-2 gap-8">
            <!-- Desvios -->
            <div class="bg-gray-800 border border-gray-700 p-6 rounded-xl">
                <h3 class="font-bold text-red-400 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Desvios Identificados
                </h3>
                <ul class="space-y-3">
                    <li class="text-sm border-b border-gray-700/50 pb-2 last:border-0">
                        <strong class="text-red-300 block mb-1">Lordose:</strong> 
                        <span class="text-gray-300">{{ $analysisResult['posture_analysis']['lordosis'] ?? '-' }}</span>
                    </li>
                    <li class="text-sm border-b border-gray-700/50 pb-2 last:border-0">
                        <strong class="text-red-300 block mb-1">Escoliose:</strong> 
                        <span class="text-gray-300">{{ $analysisResult['posture_analysis']['scoliosis'] ?? '-' }}</span>
                    </li>
                    <li class="text-sm border-b border-gray-700/50 pb-2 last:border-0">
                        <strong class="text-red-300 block mb-1">Ombros:</strong> 
                        <span class="text-gray-300">{{ $analysisResult['posture_analysis']['shoulders'] ?? '-' }}</span>
                    </li>
                    <li class="text-sm border-b border-gray-700/50 pb-2 last:border-0">
                        <strong class="text-red-300 block mb-1">Cabeça:</strong> 
                        <span class="text-gray-300">{{ $analysisResult['posture_analysis']['head_position'] ?? '-' }}</span>
                    </li>
                    <li class="text-sm border-b border-gray-700/50 pb-2 last:border-0">
                        <strong class="text-red-300 block mb-1">Joelhos:</strong> 
                        <span class="text-gray-300">{{ $analysisResult['posture_analysis']['knees'] ?? '-' }}</span>
                    </li>
                </ul>
            </div>

            <!-- Estratégia -->
            <div class="bg-gray-800 border border-gray-700 p-6 rounded-xl">
                <h3 class="font-bold text-teal-400 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Estratégia Corretiva
                </h3>
                <div class="mb-6">
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-white bg-teal-600 px-2 py-1 rounded border border-teal-500 mb-2 shadow-sm">Fortalecer</span>
                    <p class="text-sm text-gray-300 leading-relaxed">{{ implode(', ', $analysisResult['suggested_focus']['strengthen'] ?? []) }}</p>
                </div>
                <div>
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-white bg-blue-600 px-2 py-1 rounded border border-blue-500 mb-2 shadow-sm">Alongar</span>
                    <p class="text-sm text-gray-300 leading-relaxed">{{ implode(', ', $analysisResult['suggested_focus']['stretch'] ?? []) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fotos -->
    <div class="mb-10 break-inside-avoid">
        <h2 class="text-xl font-bold text-indigo-400 mb-6 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            2. Evidências Visuais
        </h2>
        <div class="grid grid-cols-3 gap-4">
            @if($frontPath) 
                <div class="bg-gray-800 p-2 rounded-lg border border-gray-700 shadow-sm">
                    <div class="aspect-w-3 aspect-h-4 rounded overflow-hidden">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(storage_path('app/private/'.$frontPath))) }}" class="object-cover w-full h-full rounded">
                    </div> 
                    <p class="text-center text-xs text-gray-500 mt-2 font-medium">Frontal</p>
                </div>
            @endif
            @if($sidePath) 
                <div class="bg-gray-800 p-2 rounded-lg border border-gray-700 shadow-sm">
                    <div class="aspect-w-3 aspect-h-4 rounded overflow-hidden">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(storage_path('app/private/'.$sidePath))) }}" class="object-cover w-full h-full rounded">
                    </div> 
                    <p class="text-center text-xs text-gray-500 mt-2 font-medium">Lateral</p>
                </div>
            @endif
            @if($backPath) 
                <div class="bg-gray-800 p-2 rounded-lg border border-gray-700 shadow-sm">
                    <div class="aspect-w-3 aspect-h-4 rounded overflow-hidden">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(storage_path('app/private/'.$backPath))) }}" class="object-cover w-full h-full rounded">
                    </div> 
                    <p class="text-center text-xs text-gray-500 mt-2 font-medium">Posterior</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Treino Prescrito -->
    <div class="page-break"></div> <!-- Quebra de página forçada para o treino -->
    
    <div class="mb-8">
        <h2 class="text-xl font-bold text-indigo-400 mb-6 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            3. Prescrição de Treino
        </h2>
        
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6 flex justify-between items-center shadow-sm">
            <div>
                <span class="text-xs text-gray-500 uppercase tracking-wider font-bold">Objetivo</span>
                <p class="text-white font-medium">{{ $request->goal }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-gray-500 uppercase tracking-wider font-bold">Foco Principal</span>
                <p class="text-indigo-400 font-medium">{{ $analysisResult['workout_recommendation']['priority'] ?? 'Geral' }}</p>
            </div>
        </div>

        <div class="space-y-6">
            @if(isset($request->days))
                @foreach($request->days as $day)
                    <div class="border border-gray-700 rounded-xl overflow-hidden shadow-sm break-inside-avoid bg-gray-800/50">
                        <div class="bg-gray-800 px-5 py-3 border-b border-gray-700 flex items-center">
                            <div class="w-2 h-2 rounded-full bg-indigo-500 mr-3"></div>
                            <h3 class="font-bold text-white text-lg">{{ $day['name'] }}</h3>
                        </div>
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-900/50 text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-5 py-3 font-medium tracking-wider">Exercício</th>
                                    <th class="px-5 py-3 font-medium tracking-wider w-20 text-center">Séries</th>
                                    <th class="px-5 py-3 font-medium tracking-wider w-24 text-center">Reps</th>
                                    <th class="px-5 py-3 font-medium tracking-wider">Notas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                @if(isset($day['exercises']))
                                    @foreach($day['exercises'] as $exercise)
                                        <tr class="hover:bg-gray-700/20 transition-colors">
                                            <td class="px-5 py-3 font-medium text-gray-200">{{ $exercise['name'] }}</td>
                                            <td class="px-5 py-3 text-center text-indigo-300 font-bold">{{ $exercise['sets'] }}</td>
                                            <td class="px-5 py-3 text-center text-gray-300">{{ $exercise['reps'] }}</td>
                                            <td class="px-5 py-3 text-gray-500 italic">{{ $exercise['notes'] ?? '-' }}</td>
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
    <div class="mt-16 text-center border-t border-gray-700 pt-6 pb-8">
        <p class="text-xs text-gray-500 mb-1">Este documento foi gerado automaticamente pelo sistema FitManager AI.</p>
        <p class="text-xs text-gray-600">© {{ date('Y') }} FitManager - Todos os direitos reservados.</p>
    </div>

    <script>
        window.onload = function() {
            // Pequeno delay para garantir carregamento das imagens e estilos
            setTimeout(() => {
                window.print();
            }, 800);
        }
    </script>
</body>
</html>
