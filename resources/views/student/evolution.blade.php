@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* ── Tema dark navy para Evolução ─────────────────────── */
    .space-y-8 { background: #0d0f1a; }

    /* Cards */
    .bg-gray-800\/50,
    .bg-gray-800\/50.backdrop-blur-sm {
        background: rgba(255,255,255,0.04) !important;
        backdrop-filter: blur(8px) !important;
    }
    .border-gray-700 { border-color: rgba(255,255,255,0.08) !important; }
    .border-gray-600 { border-color: rgba(255,255,255,0.1) !important; }
    .hover\:border-gray-600:hover { border-color: rgba(99,102,241,0.35) !important; }

    /* Texto */
    .text-gray-400 { color: #64748b !important; }
    .text-gray-300 { color: #94a3b8 !important; }

    /* Header */
    .space-y-8 h1 { font-size: 22px !important; font-weight: 900 !important; }
    .space-y-8 a[href*="dashboard"] {
        font-size: 13px; font-weight: 700; color: #6366f1 !important;
        background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.25);
        padding: 7px 14px; border-radius: 10px; text-decoration: none !important;
    }

    /* Tabs */
    .border-b.border-gray-700 { border-color: rgba(255,255,255,0.07) !important; }
    button[class*="border-b-2"] {
        font-size: 13px !important; font-weight: 700 !important;
        padding: 12px 4px !important;
    }
    button.border-gray-400 {
        border-color: #6366f1 !important;
        color: #fff !important;
    }
    button.border-transparent { color: #475569 !important; }

    /* Tabela */
    thead.bg-gray-700\/50 { background: rgba(255,255,255,0.03) !important; }
    .divide-y.divide-gray-700 > * { border-color: rgba(255,255,255,0.05) !important; }
    tr.hover\:bg-gray-700\/30:hover { background: rgba(99,102,241,0.06) !important; }

    /* Selects */
    select.bg-gray-700 {
        background: rgba(255,255,255,0.05) !important;
        border-color: rgba(255,255,255,0.1) !important;
        color: #e2e8f0 !important; border-radius: 10px !important;
    }

    /* Fotos sem conteúdo */
    .bg-gray-600 { background: rgba(255,255,255,0.05) !important; }
    .bg-gray-700\/50 { background: rgba(255,255,255,0.03) !important; }

    /* Botão lado a lado */
    .bg-indigo-600 { background: linear-gradient(135deg,#6366f1,#8b5cf6) !important; }
    .bg-indigo-600:hover { opacity: 0.9 !important; }

    /* Badge view switcher */
    .bg-gray-800 { background: rgba(255,255,255,0.05) !important; }
    .bg-gray-700 { background: rgba(255,255,255,0.08) !important; }
    .bg-gray-900 { background: #0d0f1a !important; }

    /* Empty state */
    .text-gray-600 { color: #334155 !important; }

    /* Cards resumo — destaque com borda roxa */
    .grid.grid-cols-1.md\:grid-cols-3 > div {
        border-radius: 16px !important;
    }
    .grid.grid-cols-1.md\:grid-cols-3 > div:first-child { border-color: rgba(99,102,241,0.3) !important; }
    .grid.grid-cols-1.md\:grid-cols-3 > div:nth-child(2) { border-color: rgba(236,72,153,0.3) !important; }
    .grid.grid-cols-1.md\:grid-cols-3 > div:nth-child(3) { border-color: rgba(52,211,153,0.3) !important; }

    /* Título dos gráficos */
    h3.text-lg { font-weight: 800 !important; font-size: 15px !important; }

    /* Rounded em todos os cards */
    .rounded-xl { border-radius: 16px !important; }
</style>

    <!-- Side-by-Side Modal Template -->
    <template x-teleport="body">
        <div x-data="{
                 open: false, leftImage: null, rightImage: null, view: 'front',
                 switchView(v) {
                     this.view = v;
                     window.dispatchEvent(new CustomEvent('sbs-view-change', { detail: { view: v } }));
                 }
             }"
             @open-sbs-modal.window="open = true; leftImage = $event.detail.leftImage; rightImage = $event.detail.rightImage; view = $event.detail.view"
             @update-sbs-images.window="leftImage = $event.detail.leftImage; rightImage = $event.detail.rightImage; view = $event.detail.view"
             x-show="open"
             style="display: none;"
             class="fixed inset-0 z-[99999] overflow-hidden"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">

            <!-- Overlay -->
            <div class="fixed inset-0 bg-black/95 backdrop-blur-sm" aria-hidden="true"></div>

            <!-- Fullscreen Container -->
            <div class="fixed inset-0 flex flex-col w-full h-full z-[100000]">

                <!-- Toolbar mobile-first -->
                <div style="background:#0d0f1a;border-bottom:1px solid rgba(255,255,255,0.08);padding:12px 16px;flex-shrink:0;">
                    <!-- Row 1: título + fechar -->
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-white font-extrabold text-sm">Comparação Lado a Lado</h3>
                        <button @click="open = false"
                                style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.35);color:#f87171;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Row 2: switcher de vista (full width) -->
                    <div style="display:flex;gap:6px;">
                        @foreach([['front','Frontal'],['side_right','Lado D'],['side_left','Lado E'],['back','Costas']] as [$val,$lbl])
                        <button @click="switchView('{{ $val }}')"
                                :style="view === '{{ $val }}' ? 'background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-color:#6366f1;' : 'background:rgba(255,255,255,0.05);color:#64748b;border-color:rgba(255,255,255,0.1);'"
                                style="flex:1;padding:8px 4px;border-radius:10px;font-size:12px;font-weight:700;border:1px solid;transition:all 0.2s;">
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Images Container -->
                <div class="flex-1 bg-black flex items-center justify-center overflow-hidden relative w-full h-full">
                    <div class="flex w-full h-full">
                        <!-- Left Image -->
                        <div class="w-1/2 h-full border-r border-gray-800 relative overflow-hidden flex items-center justify-center bg-black">
                            <template x-if="leftImage">
                                <img :src="leftImage" class="max-w-full max-h-full object-contain" alt="Esquerda">
                            </template>
                            <template x-if="!leftImage">
                                <div class="text-gray-600 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="font-medium">Sem imagem disponível</span>
                                </div>
                            </template>
                            <div class="absolute bottom-4 left-4 bg-black/80 backdrop-blur text-white px-3 py-1.5 rounded-md text-sm font-semibold border border-white/10 shadow-lg">Avaliação 1</div>
                        </div>
                        
                        <!-- Right Image -->
                        <div class="w-1/2 h-full relative overflow-hidden flex items-center justify-center bg-black">
                            <template x-if="rightImage">
                                <img :src="rightImage" class="max-w-full max-h-full object-contain" alt="Direita">
                            </template>
                            <template x-if="!rightImage">
                                <div class="text-gray-600 flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="font-medium">Sem imagem disponível</span>
                                </div>
                            </template>
                            <div class="absolute bottom-4 right-4 bg-black/80 backdrop-blur text-white px-3 py-1.5 rounded-md text-sm font-semibold border border-white/10 shadow-lg">Avaliação 2</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
<div class="space-y-8" x-data="evolutionData()"
     @sbs-view-change.window="sbsView = $event.detail.view; updateSbsImages(); window.dispatchEvent(new CustomEvent('update-sbs-images', { detail: { leftImage: sbsLeftImage, rightImage: sbsRightImage, view: sbsView } }))">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white">Sua Evolução</h1>
        <a href="{{ route('student.dashboard') }}" class="text-gray-400 hover:text-gray-300 font-medium transition">
            &larr; Voltar
        </a>
    </div>

    @if($measurements->isEmpty())
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <h3 class="mt-2 text-sm font-medium text-white">Nenhuma avaliação encontrada</h3>
            <p class="mt-1 text-sm text-gray-400">Peça ao seu Personal Trainer para registrar suas medidas.</p>
        </div>
    @else
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'charts'"
                    :class="activeTab === 'charts' ? 'border-gray-400 text-white' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition">
                    Gráficos e Medidas
                </button>
                <button @click="activeTab = 'photos'"
                    :class="activeTab === 'photos' ? 'border-gray-400 text-white' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition">
                    Comparativo de Fotos
                </button>
            </nav>
        </div>

        <!-- Tab: Charts -->
        <div x-show="activeTab === 'charts'" class="space-y-8 pt-6">
            <!-- Cards de Resumo (Última Avaliação) -->
            @php $last = $measurements->last(); @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-gray-600 transition-all">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-400 truncate">Peso Atual</dt>
                        <dd class="mt-2 text-3xl font-bold text-white">{{ $last->weight }} kg</dd>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-gray-600 transition-all">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-400 truncate">% de Gordura</dt>
                        <dd class="mt-2 text-3xl font-bold text-white">{{ $last->body_fat }}%</dd>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden hover:border-gray-600 transition-all">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-gray-400 truncate">Massa Muscular</dt>
                        <dd class="mt-2 text-3xl font-bold text-white">{{ $last->muscle_mass }} kg</dd>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gráfico de Peso e Massa Magra -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg p-6 hover:border-gray-600 transition-all">
                    <h3 class="text-lg leading-6 font-medium text-white mb-4">Composição Corporal</h3>
                    <canvas id="weightChart"></canvas>
                </div>

                <!-- Gráfico de Gordura -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg p-6 hover:border-gray-600 transition-all">
                    <h3 class="text-lg leading-6 font-medium text-white mb-4">Percentual de Gordura</h3>
                    <canvas id="fatChart"></canvas>
                </div>
            </div>

            <!-- Comparativo de Medidas (mobile-first) -->
            @php
                $firstMeasurement = $measurements->first();
                $lastMeasurement = $measurements->last();
                $startDate = $firstMeasurement?->date?->format('d/m/Y');
                $endDate = $lastMeasurement?->date?->format('d/m/Y');

                $comparisonSections = [
                    [
                        'key' => 'composition',
                        'title' => 'Composicao Corporal',
                        'variationMode' => 'neutral',
                        'rows' => [
                            ['field' => 'weight', 'label' => 'Peso (kg)', 'decimals' => 1, 'unit' => 'kg'],
                            ['field' => 'body_fat', 'label' => '% Gordura', 'decimals' => 1, 'unit' => '%'],
                            ['field' => 'muscle_mass', 'label' => 'Massa Muscular (kg)', 'decimals' => 1, 'unit' => 'kg'],
                        ],
                    ],
                    [
                        'key' => 'circumferences',
                        'title' => 'Circunferencias (cm)',
                        'variationMode' => 'neutral',
                        'rows' => [
                            ['field' => 'chest', 'label' => 'Peitoral', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'ombro', 'label' => 'Ombro', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'torax', 'label' => 'Torax', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'waist', 'label' => 'Cintura', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'abdomen', 'label' => 'Abdomen', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'abdomen_inferior', 'label' => 'Abd. Inferior', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'hips', 'label' => 'Quadril', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_arm', 'label' => 'Braco D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_arm', 'label' => 'Braco E', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_arm_contracted', 'label' => 'Braco Contraido D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_arm_contracted', 'label' => 'Braco Contraido E', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_forearm', 'label' => 'Antebraco D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_forearm', 'label' => 'Antebraco E', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_thigh', 'label' => 'Coxa D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_thigh', 'label' => 'Coxa E', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_thigh_proximal', 'label' => 'Coxa Prox. D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_thigh_medial', 'label' => 'Coxa Medial D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_thigh_distal', 'label' => 'Coxa Distal D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_thigh_proximal', 'label' => 'Coxa Prox. E', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_thigh_medial', 'label' => 'Coxa Medial E', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_thigh_distal', 'label' => 'Coxa Distal E', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'right_calf', 'label' => 'Panturrilha D', 'decimals' => 1, 'unit' => 'cm'],
                            ['field' => 'left_calf', 'label' => 'Panturrilha E', 'decimals' => 1, 'unit' => 'cm'],
                        ],
                    ],
                    [
                        'key' => 'skinfolds',
                        'title' => 'Dobras Cutaneas (mm)',
                        'variationMode' => 'skinfold',
                        'rows' => [
                            ['field' => 'subescapular', 'label' => 'Subescapular', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'tricipital', 'label' => 'Tricipital', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'bicipital', 'label' => 'Bicipital', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'toracica', 'label' => 'Toracica', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'abdominal_fold', 'label' => 'Abdominal', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'axilar_media', 'label' => 'Axilar Media', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'suprailiaca', 'label' => 'Supra-iliaca', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'coxa_fold', 'label' => 'Coxa', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'panturrilha_fold', 'label' => 'Panturrilha', 'decimals' => 1, 'unit' => 'mm'],
                            ['field' => 'sum_skinfolds', 'label' => 'Soma (mm)', 'decimals' => 1, 'unit' => 'mm'],
                        ],
                    ],
                ];

                $formatMetric = function ($value, $decimals = 1, $unit = '') {
                    if ($value === null || $value === '') {
                        return '—';
                    }
                    $formatted = is_numeric($value)
                        ? number_format((float) $value, $decimals, ',', '.')
                        : $value;

                    return trim($formatted . ($unit ? ' ' . $unit : ''));
                };

                $formatVariation = function ($initial, $final, $decimals = 1) {
                    if ($initial === null || $initial === '' || $final === null || $final === '') {
                        return '—';
                    }
                    $delta = (float) $final - (float) $initial;
                    if (abs($delta) < 0.00001) {
                        return '=';
                    }

                    return ($delta > 0 ? '+' : '-') . number_format(abs($delta), $decimals, ',', '.');
                };

                $variationClass = function ($initial, $final, $mode = 'neutral') {
                    if ($initial === null || $initial === '' || $final === null || $final === '') {
                        return 'text-slate-400';
                    }
                    $delta = (float) $final - (float) $initial;
                    if (abs($delta) < 0.00001) {
                        return 'text-slate-300';
                    }

                    if ($mode === 'skinfold') {
                        return $delta < 0 ? 'text-emerald-400' : 'text-rose-400';
                    }

                    return 'text-cyan-300';
                };
            @endphp

            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden"
                 x-data="{ composition: true, circumferences: true, skinfolds: true }">
                <div class="p-5 border-b border-gray-700">
                    <div>
                        <h3 class="text-lg leading-6 font-semibold text-white">Comparativo de Medidas</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            {{ $startDate ?? '—' }} — {{ $endDate ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="p-4 space-y-4">
                    @foreach($comparisonSections as $section)
                        @php
                            $visibleRows = collect($section['rows'])->filter(function ($row) use ($firstMeasurement, $lastMeasurement) {
                                $initial = $firstMeasurement?->{$row['field']} ?? null;
                                $final = $lastMeasurement?->{$row['field']} ?? null;

                                return ($initial !== null && $initial !== '') || ($final !== null && $final !== '');
                            })->values();
                        @endphp

                        <section class="rounded-xl border border-white/10 bg-white/[0.02] overflow-hidden">
                            <button type="button"
                                    class="w-full flex items-center justify-between px-4 py-3 text-left"
                                    @click="{{ $section['key'] }} = !{{ $section['key'] }}">
                                <span class="text-sm font-bold text-cyan-300 uppercase tracking-wide">{{ $section['title'] }}</span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform"
                                     :class="{{ $section['key'] }} ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="{{ $section['key'] }}" style="display: none;" class="px-4 pb-4 space-y-2.5">
                                @if($visibleRows->isEmpty())
                                    <div class="rounded-lg border border-white/10 bg-white/[0.02] p-3 text-xs text-slate-400">
                                        Sem dados suficientes para comparar nesta seção.
                                    </div>
                                @else
                                    @foreach($visibleRows as $row)
                                        @php
                                            $initial = $firstMeasurement?->{$row['field']} ?? null;
                                            $final = $lastMeasurement?->{$row['field']} ?? null;
                                            $variationText = $formatVariation($initial, $final, $row['decimals']);
                                            $variationTone = $variationClass($initial, $final, $section['variationMode']);
                                        @endphp
                                        <div class="rounded-lg border border-white/10 bg-white/[0.015] p-3">
                                            <p class="text-sm font-semibold text-white">{{ $row['label'] }}</p>
                                            <div class="mt-2 grid grid-cols-3 gap-2">
                                                <div class="rounded-md border border-white/10 bg-white/[0.02] px-2.5 py-2">
                                                    <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ $startDate ?? 'Inicial' }}</p>
                                                    <p class="text-xs font-semibold text-slate-200 mt-0.5">{{ $formatMetric($initial, $row['decimals'], $row['unit']) }}</p>
                                                </div>
                                                <div class="rounded-md border border-white/10 bg-white/[0.02] px-2.5 py-2">
                                                    <p class="text-[10px] uppercase tracking-wide text-slate-500">{{ $endDate ?? 'Final' }}</p>
                                                    <p class="text-xs font-semibold text-slate-200 mt-0.5">{{ $formatMetric($final, $row['decimals'], $row['unit']) }}</p>
                                                </div>
                                                <div class="rounded-md border border-white/10 bg-white/[0.02] px-2.5 py-2">
                                                    <p class="text-[10px] uppercase tracking-wide text-slate-500">Variacao</p>
                                                    <p class="text-xs font-bold mt-0.5 {{ $variationTone }}">{{ $variationText }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tab: Comparativo de Fotos -->
        <div x-show="activeTab === 'photos'" x-cloak class="pt-6 space-y-6">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-medium text-white mb-4">Comparar Avaliações</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Select Left -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Avaliação 1 (Esquerda)</label>
                        <select x-model.number="leftId" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-700 text-white focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 sm:text-sm rounded-lg">
                            <template x-for="m in measurements" :key="m.id">
                                <option :value="m.id" x-text="formatDate(m.date) + ' - ' + m.weight + 'kg'"></option>
                            </template>
                        </select>
                    </div>
                    <!-- Select Right -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Avaliação 2 (Direita)</label>
                        <select x-model.number="rightId" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-700 text-white focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 sm:text-sm rounded-lg">
                            <template x-for="m in measurements" :key="m.id">
                                <option :value="m.id" x-text="formatDate(m.date) + ' - ' + m.weight + 'kg'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Side-by-Side Button -->
                <div class="flex justify-center mb-8">
                    <button @click="openSideBySide('front')" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Visualização Lado a Lado
                    </button>
                </div>

                <!-- Comparison Display -->
                <div class="space-y-12">
                    <!-- Front -->
                    <div>
                        <div class="flex items-center justify-center mb-4">
                            <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Vista Frontal</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="leftMeasurement && leftMeasurement.photo_front">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'front'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(leftMeasurement.id, 'front')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!leftMeasurement || !leftMeasurement.photo_front">
                                    <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                        <span class="text-sm">Sem foto</span>
                                    </div>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                            </div>
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="rightMeasurement && rightMeasurement.photo_front">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'front'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(rightMeasurement.id, 'front')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!rightMeasurement || !rightMeasurement.photo_front">
                                    <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                        <span class="text-sm">Sem foto</span>
                                    </div>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Side Right -->
                    <div>
                        <div class="flex items-center justify-center mb-4">
                            <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Lado D (Direito)</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="leftMeasurement && leftMeasurement.photo_side_right">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'side_right'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(leftMeasurement.id, 'side_right')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!leftMeasurement || !leftMeasurement.photo_side_right">
                                    <!-- Fallback for old single side photo if needed, or just show empty -->
                                    <template x-if="leftMeasurement && leftMeasurement.photo_side">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'side'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(leftMeasurement.id, 'side')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                    <template x-if="!leftMeasurement || (!leftMeasurement.photo_side_right && !leftMeasurement.photo_side)">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                            </div>
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="rightMeasurement && rightMeasurement.photo_side_right">
                                    <div class="aspect-w-3 aspect-h-4">
                                        <img :src="getPhotoUrl(rightMeasurement.id, 'side_right')" class="w-full h-full object-cover rounded shadow cursor-pointer hover:opacity-90 transition" @click="openModal(getPhotoUrl(rightMeasurement.id, 'side_right'))">
                                    </div>
                                </template>
                                <template x-if="!rightMeasurement || !rightMeasurement.photo_side_right">
                                    <!-- Fallback -->
                                    <template x-if="rightMeasurement && rightMeasurement.photo_side">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'side'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(rightMeasurement.id, 'side')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                    <template x-if="!rightMeasurement || (!rightMeasurement.photo_side_right && !rightMeasurement.photo_side)">
                                        <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                            <span class="text-sm">Sem foto</span>
                                        </div>
                                    </template>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Side Left -->
                    <div>
                        <div class="flex items-center justify-center mb-4">
                            <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Lado E (Esquerdo)</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="leftMeasurement && leftMeasurement.photo_side_left">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'side_left'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(leftMeasurement.id, 'side_left')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!leftMeasurement || !leftMeasurement.photo_side_left">
                                    <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                        <span class="text-sm">Sem foto</span>
                                    </div>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                            </div>
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="rightMeasurement && rightMeasurement.photo_side_left">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'side_left'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(rightMeasurement.id, 'side_left')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!rightMeasurement || !rightMeasurement.photo_side_left">
                                    <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                        <span class="text-sm">Sem foto</span>
                                    </div>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Back -->
                    <div>
                        <div class="flex items-center justify-center mb-4">
                            <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Vista Costas</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="leftMeasurement && leftMeasurement.photo_back">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(leftMeasurement.id, 'back'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(leftMeasurement.id, 'back')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!leftMeasurement || !leftMeasurement.photo_back">
                                    <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                        <span class="text-sm">Sem foto</span>
                                    </div>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                            </div>
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="rightMeasurement && rightMeasurement.photo_back">
                                    <div class="aspect-w-3 aspect-h-4 relative overflow-hidden rounded-lg bg-gray-900" 
                                         x-data="imageZoom()" 
                                         @mouseleave="stopDrag" 
                                         @mouseup="stopDrag" 
                                         @touchend="stopDrag">
                                        
                                        <div class="absolute top-2 right-2 z-10 flex gap-1 transition-opacity duration-200" 
                                             :class="{'opacity-100': scale > 1, 'opacity-0 hover:opacity-100': scale === 1}">
                                            <button @click.stop="reset()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Resetar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button @click.stop="zoomOut()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom Out">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            </button>
                                            <span class="bg-black/50 text-white px-2 py-1 rounded text-xs flex items-center" x-text="Math.round(scale * 100) + '%'"></span>
                                            <button @click.stop="zoomIn()" class="bg-black/50 text-white p-1 rounded hover:bg-black/70" title="Zoom In">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            <button @click.stop="openModal(getPhotoUrl(rightMeasurement.id, 'back'))" class="bg-black/50 text-white p-1 rounded hover:bg-black/70 ml-1" title="Expandir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            </button>
                                        </div>

                                        <div class="w-full h-full cursor-grab active:cursor-grabbing"
                                             :class="{'cursor-grab': scale > 1 && !panning, 'cursor-grabbing': panning}"
                                             @mousedown="startDrag" 
                                             @touchstart="startDrag"
                                             @mousemove="onDrag"
                                             @touchmove="onDrag"
                                             @wheel="onWheel">
                                            <img :src="getPhotoUrl(rightMeasurement.id, 'back')" 
                                                 class="w-full h-full object-cover transition-transform duration-200 ease-out origin-center"
                                                 :style="`transform: scale(${scale}) translate(${pointX / scale}px, ${pointY / scale}px)`">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!rightMeasurement || !rightMeasurement.photo_back">
                                    <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                        <span class="text-sm">Sem foto</span>
                                    </div>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="rightMeasurement ? formatDate(rightMeasurement.date) : '-'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Image Modal -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-95 p-4" x-cloak @click="modalOpen = false">
        <div class="relative max-w-4xl w-full h-full flex items-center justify-center">
             <button @click="modalOpen = false" class="absolute top-0 right-0 m-4 text-white text-4xl font-light">&times;</button>
             <img :src="modalImage" class="max-w-full max-h-full rounded shadow-2xl">
        </div>
    </div>
</div>

<script>
    function evolutionData() {
        return {
            activeTab: 'charts',
            measurements: @json($measurements),
            leftId: null,
            rightId: null,
            modalOpen: false,
            modalImage: '',
            // Usamos IDs fictícios para garantir que a rota seja gerada corretamente e depois substituímos via JS
            photoRouteTemplate: "{{ route('measurement.photo', ['measurementId' => 999999, 'type' => 'placeholder_type']) }}",
            
            init() {
                // Ordena novamente por data (ascendente) para garantir a ordem correta
                this.measurements.sort((a, b) => new Date(a.date) - new Date(b.date));

                this.$nextTick(() => {
                    if (this.measurements.length > 0) {
                        // Se tiver pelo menos 2 avaliações, seleciona a penúltima na esquerda e a última na direita
                        if (this.measurements.length >= 2) {
                            this.leftId = this.measurements[this.measurements.length - 2].id;
                            this.rightId = this.measurements[this.measurements.length - 1].id;
                        } else {
                            // Se tiver apenas 1, seleciona ela em ambos
                            this.leftId = this.measurements[0].id;
                            this.rightId = this.measurements[0].id;
                        }
                    }
                });
            },

            getPhotoUrl(id, type) {
                const url = this.photoRouteTemplate.replace('999999', id).replace('placeholder_type', type);
                return `${url}?t=${new Date().getTime()}`;
            },

            get leftMeasurement() {
                return this.measurements.find(m => m.id == this.leftId) || null;
            },
            get rightMeasurement() {
                return this.measurements.find(m => m.id == this.rightId) || null;
            },
            
            formatDate(dateString) {
                if(!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('pt-BR', { timeZone: 'UTC' }); 
            },
            
            openModal(imageUrl) {
                    this.modalImage = imageUrl;
                    this.modalOpen = true;
                },

                // Side-by-Side Logic
                sbsOpen: false,
                sbsView: 'front', // front, side_right, side_left, back
                sbsLeftImage: null,
                sbsRightImage: null,

                openSideBySide(view) {
                    this.sbsView = view;
                    this.updateSbsImages();
                    this.sbsOpen = true;
                    // Dispara evento para o modal teleportado no body
                    window.dispatchEvent(new CustomEvent('open-sbs-modal', {
                        detail: {
                            leftImage: this.sbsLeftImage,
                            rightImage: this.sbsRightImage,
                            view: view
                        }
                    }));
                },

                updateSbsImages() {
                    if (!this.leftMeasurement || !this.rightMeasurement) return;
                    this.sbsLeftImage = this.getPhotoUrl(this.leftMeasurement.id, this.sbsView);
                    this.sbsRightImage = this.getPhotoUrl(this.rightMeasurement.id, this.sbsView);
                }
            }
        }
        
        function imageZoom() {
            return {
                scale: 1,
                panning: false,
                pointX: 0,
                pointY: 0,
                startX: 0,
                startY: 0,

                zoomIn() {
                    if (this.scale < 3) this.scale = Math.min(this.scale + 0.5, 3);
                },
                
                zoomOut() {
                    if (this.scale > 1) this.scale = Math.max(this.scale - 0.5, 1);
                    if (this.scale === 1) this.reset();
                },
                
                reset() {
                    this.scale = 1;
                    this.pointX = 0;
                    this.pointY = 0;
                    this.panning = false;
                },

                startDrag(e) {
                    if (this.scale <= 1) return;
                    e.preventDefault();
                    this.panning = true;
                    this.startX = e.clientX || e.touches[0].clientX;
                    this.startY = e.clientY || e.touches[0].clientY;
                },

                onDrag(e) {
                    if (!this.panning || this.scale <= 1) return;
                    e.preventDefault();
                    
                    const clientX = e.clientX || e.touches[0].clientX;
                    const clientY = e.clientY || e.touches[0].clientY;
                    
                    const deltaX = clientX - this.startX;
                    const deltaY = clientY - this.startY;
                    
                    this.pointX += deltaX;
                    this.pointY += deltaY;
                    
                    this.startX = clientX;
                    this.startY = clientY;
                },

                stopDrag() {
                    this.panning = false;
                },
                
                onWheel(e) {
                    e.preventDefault();
                    if (e.deltaY < 0) this.zoomIn();
                    else this.zoomOut();
                }
            }
        }
    
    // Gráficos Chart.js
    const dates = @json($dates);
    
    // Gráfico 1: Peso vs Massa Magra
    const ctxWeight = document.getElementById('weightChart').getContext('2d');
    new Chart(ctxWeight, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Peso Total (kg)',
                    data: @json($weights),
                    borderColor: 'rgb(99,102,241)',
                    backgroundColor: 'rgba(99,102,241,0.12)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(99,102,241)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Massa Muscular (kg)',
                    data: @json($muscleMasses),
                    borderColor: 'rgb(52,211,153)',
                    backgroundColor: 'rgba(52,211,153,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(52,211,153)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#cbd5e1' // Slate 300
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        color: '#94a3b8' // Slate 400
                    },
                    grid: {
                        color: 'rgba(71, 85, 105, 0.2)' // Slate 600 with opacity
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8' // Slate 400
                    },
                    grid: {
                        color: 'rgba(71, 85, 105, 0.2)' // Slate 600 with opacity
                    }
                }
            }
        }
    });

    // Gráfico 2: % Gordura
    const ctxFat = document.getElementById('fatChart').getContext('2d');
    new Chart(ctxFat, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: '% Gordura Corporal',
                    data: @json($bodyFats),
                    borderColor: 'rgb(236,72,153)',
                    backgroundColor: 'rgba(236,72,153,0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(236,72,153)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#cbd5e1' // Slate 300
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        color: '#94a3b8' // Slate 400
                    },
                    grid: {
                        color: 'rgba(71, 85, 105, 0.2)' // Slate 600 with opacity
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8' // Slate 400
                    },
                    grid: {
                        color: 'rgba(71, 85, 105, 0.2)' // Slate 600 with opacity
                    }
                }
            }
        }
    });
</script>
@endsection
