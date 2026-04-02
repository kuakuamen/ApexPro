<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Laudo IA – {{ $student->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #111827 !important;
                color: #ffffff !important;
            }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 p-8 max-w-4xl mx-auto min-h-screen">

    @php
        $data    = $assessment->ai_analysis_data ?? [];
        $posture = $data['posture_analysis'] ?? [];
        $body    = $data['body_composition'] ?? [];
        $obs     = $data['observations'] ?? [];
        $rec     = $data['recommendations'] ?? [];
        $focus   = $data['suggested_focus'] ?? [];
        $wRec    = $data['workout_recommendation'] ?? [];
    @endphp

    <!-- Botão imprimir (some ao imprimir) -->
    <div class="no-print flex justify-end mb-6">
        <button onclick="window.print()"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir / Salvar PDF
        </button>
    </div>

    <!-- Cabeçalho -->
    <div class="border-b border-gray-700 pb-6 mb-10 flex justify-between items-end">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">ApexPro <span class="text-indigo-400 font-light">AI</span></h1>
            </div>
            <p class="text-sm text-gray-400">Laudo de Avaliação Física & Prescrição Inteligente</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-white text-lg">{{ $student->name }}</p>
            <p class="text-sm text-gray-400">Avaliação de {{ $assessment->created_at->format('d/m/Y \à\s H:i') }}</p>
            @if($assessment->goal || $assessment->experience_level)
            <p class="text-xs text-indigo-300 mt-1">
                @if($assessment->goal){{ $assessment->goal }}@endif
                @if($assessment->goal && $assessment->experience_level) · @endif
                @if($assessment->experience_level){{ $assessment->experience_level }}@endif
            </p>
            @endif
        </div>
    </div>

    <!-- 1. Análise Postural -->
    @if(!empty($posture))
    <div class="mb-10">
        <h2 class="text-xl font-bold text-indigo-400 mb-6 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            1. Análise Postural
        </h2>

        <div class="grid grid-cols-2 gap-6">
            <!-- Desvios -->
            <div class="bg-gray-800 border border-gray-700 p-5 rounded-xl">
                <h3 class="font-bold text-red-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Desvios Identificados
                </h3>
                @php
                    $postureLabels = [
                        'lordosis' => 'Lordose',
                        'scoliosis' => 'Escoliose',
                        'shoulders' => 'Ombros',
                        'head_position' => 'Cabeça',
                        'knees' => 'Joelhos',
                        'feet' => 'Pés',
                    ];
                @endphp
                <ul class="space-y-3">
                    @foreach($posture as $key => $value)
                    @if($value)
                    <li class="text-sm border-b border-gray-700/50 pb-2 last:border-0">
                        <strong class="text-red-300 block mb-0.5">{{ $postureLabels[$key] ?? str_replace('_', ' ', $key) }}:</strong>
                        <span class="text-gray-300">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>

            <!-- Estratégia Corretiva -->
            @if(!empty($focus))
            <div class="bg-gray-800 border border-gray-700 p-5 rounded-xl">
                <h3 class="font-bold text-teal-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Estratégia Corretiva
                </h3>
                @if(!empty($focus['strengthen']))
                <div class="mb-4">
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-white bg-teal-600 px-2 py-0.5 rounded mb-1">Fortalecer</span>
                    <p class="text-sm text-gray-300 leading-relaxed">{{ implode(', ', (array)$focus['strengthen']) }}</p>
                </div>
                @endif
                @if(!empty($focus['stretch']))
                <div>
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-white bg-blue-600 px-2 py-0.5 rounded mb-1">Alongar</span>
                    <p class="text-sm text-gray-300 leading-relaxed">{{ implode(', ', (array)$focus['stretch']) }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- 2. Composição Corporal -->
    @if(!empty($body))
    <div class="mb-10">
        <h2 class="text-xl font-bold text-indigo-400 mb-4 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            2. Composição Corporal
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($body as $key => $value)
            @if($value)
            <div class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-3">
                <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</p>
                <p class="text-sm text-white font-semibold mt-0.5">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- 3. Fotos -->
    @php
        $frontPath = $assessment->front_image_path;
        $sidePath  = $assessment->side_image_path;
        $backPath  = $assessment->back_image_path;
        $extras    = $assessment->extra_image_paths ?? [];
        $allPaths  = array_filter(array_merge(
            [['path'=>$frontPath,'label'=>'Frontal'],['path'=>$sidePath,'label'=>'Lateral'],['path'=>$backPath,'label'=>'Posterior']],
            collect($extras)->map(fn($p,$i) => ['path'=>$p,'label'=>'Extra '.($i+1)])->toArray()
        ), fn($i) => !empty($i['path']));
    @endphp
    @if(!empty($allPaths))
    <div class="mb-10">
        <h2 class="text-xl font-bold text-indigo-400 mb-4 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            3. Evidências Visuais
        </h2>
        <div class="grid grid-cols-3 gap-3">
            @foreach($allPaths as $img)
            @php $fullPath = storage_path('app/private/'.$img['path']); @endphp
            @if(file_exists($fullPath))
            <div class="bg-gray-800 border border-gray-700 p-2 rounded-lg">
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($fullPath)) }}"
                     class="w-full rounded object-cover" style="max-height:240px;">
                <p class="text-center text-xs text-gray-500 mt-1.5 font-medium">{{ $img['label'] }}</p>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- 4. Observações -->
    @if(!empty($obs))
    <div class="mb-10">
        <h2 class="text-xl font-bold text-indigo-400 mb-4 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            {{ !empty($allPaths) ? '4' : '3' }}. Observações
        </h2>
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <ul class="space-y-2">
                @foreach((is_array($obs) ? $obs : [$obs]) as $o)
                <li class="flex items-start gap-2 text-sm text-gray-300">
                    <span class="text-purple-400 mt-0.5 shrink-0">•</span>{{ $o }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- 5. Recomendações -->
    @if(!empty($rec))
    <div class="mb-10">
        <h2 class="text-xl font-bold text-indigo-400 mb-4 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            {{ !empty($allPaths) ? '5' : '4' }}. Recomendações
        </h2>
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <ul class="space-y-2">
                @foreach((is_array($rec) ? $rec : [$rec]) as $r)
                <li class="flex items-start gap-2 text-sm text-gray-300">
                    <span class="text-emerald-400 mt-0.5 shrink-0">✓</span>{{ $r }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- 6. Treino Prescrito -->
    @if($workoutPlan && $workoutPlan->days->isNotEmpty())
    <div class="page-break"></div>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-indigo-400 mb-6 flex items-center">
            <div class="w-1.5 h-6 bg-indigo-500 rounded-full mr-3"></div>
            Prescrição de Treino
        </h2>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 mb-6 flex justify-between items-center">
            <div>
                <span class="text-xs text-gray-500 uppercase tracking-wider font-bold">Treino</span>
                <p class="text-white font-medium">{{ $workoutPlan->name }}</p>
            </div>
            @if(!empty($wRec['priority']))
            <div class="text-right">
                <span class="text-xs text-gray-500 uppercase tracking-wider font-bold">Foco Principal</span>
                <p class="text-indigo-400 font-medium">{{ $wRec['priority'] }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            @foreach($workoutPlan->days as $day)
            <div class="border border-gray-700 rounded-xl overflow-hidden break-inside-avoid bg-gray-800/50">
                <div class="bg-gray-800 px-5 py-3 border-b border-gray-700 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                    <h3 class="font-bold text-white">{{ $day->name }}</h3>
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-900/50 text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 font-medium tracking-wider">Exercício</th>
                            <th class="px-5 py-3 font-medium tracking-wider w-20 text-center">Séries</th>
                            <th class="px-5 py-3 font-medium tracking-wider w-24 text-center">Reps</th>
                            <th class="px-5 py-3 font-medium tracking-wider">Obs.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @foreach($day->exercises as $ex)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-200">{{ $ex->name }}</td>
                            <td class="px-5 py-3 text-center text-indigo-300 font-bold">{{ $ex->sets }}</td>
                            <td class="px-5 py-3 text-center text-gray-300">{{ $ex->reps }}</td>
                            <td class="px-5 py-3 text-gray-500 italic">{{ $ex->observation ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="mt-16 text-center border-t border-gray-700 pt-6 pb-8">
        <p class="text-xs text-gray-500 mb-1">Este documento foi gerado automaticamente pelo sistema ApexPro AI.</p>
        <p class="text-xs text-gray-600">© {{ date('Y') }} ApexPro – Todos os direitos reservados.</p>
    </div>

    <script>
        window.onload = function () {
            setTimeout(() => { window.print(); }, 600);
        };
    </script>
</body>
</html>
