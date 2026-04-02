@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<div x-data="evolutionPage()" x-init="init()" class="py-6 space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-100">Evolução dos Alunos</h1>
            <p class="text-sm text-slate-400">Selecione um aluno para visualizar os gráficos de progresso</p>
        </div>
    </div>

    <!-- Seletor de aluno -->
    <div class="bg-[#0f1a2e]/80 border border-slate-700/50 rounded-2xl p-5 space-y-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Selecionar Aluno</p>

        <!-- Busca -->
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
            </svg>
            <input
                type="text"
                x-model="search"
                placeholder="Buscar aluno pelo nome..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-800/60 border border-slate-700/50 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50"
            >
        </div>

        <!-- Grid de alunos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-64 overflow-y-auto hide-scrollbar pr-1">
            @forelse($students as $student)
            <button
                type="button"
                @click="selectStudent({{ $student->id }}, '{{ addslashes($student->name) }}', '{{ $student->profile_photo_url ?? '' }}')"
                :class="selectedId === {{ $student->id }} ? 'bg-cyan-500/15 border-cyan-400/60 text-cyan-100' : 'bg-slate-800/40 border-slate-700/40 text-slate-300 hover:bg-slate-700/50 hover:text-slate-100'"
                x-show="search === '' || '{{ strtolower($student->name) }}'.includes(search.toLowerCase())"
                class="flex items-center gap-2 px-3 py-2 rounded-xl border text-sm font-medium transition-all duration-150 text-left"
            >
                @if($student->profile_photo_url)
                    <img src="{{ $student->profile_photo_url }}" alt="Foto de {{ $student->name }}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-cyan-400/20">
                @else
                    <div class="w-7 h-7 rounded-full bg-cyan-500/15 flex items-center justify-center text-cyan-200 font-semibold text-xs shrink-0 border border-cyan-400/20">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
                <span class="truncate">{{ $student->name }}</span>
            </button>
            @empty
            <p class="col-span-full text-center text-slate-500 text-sm py-4">Nenhum aluno cadastrado.</p>
            @endforelse
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" x-cloak class="flex justify-center py-16">
        <div class="flex flex-col items-center gap-3">
            <svg class="animate-spin w-8 h-8 text-cyan-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <p class="text-slate-400 text-sm">Carregando dados...</p>
        </div>
    </div>

    <!-- Sem dados suficientes -->
    <div x-show="!loading && selectedId && noData" x-cloak class="bg-gray-800/50 border border-gray-700 rounded-xl p-10 text-center">
        <svg class="w-14 h-14 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p class="text-gray-400 font-medium">Dados insuficientes para gerar gráficos.</p>
        <p class="text-gray-500 text-sm mt-1">Registre pelo menos 2 avaliações para visualizar a evolução.</p>
    </div>

    <!-- Painel de evolução -->
    <div x-show="!loading && selectedId && !noData" x-cloak class="space-y-6">

        <!-- Nome do aluno -->
        <div class="flex items-center gap-2 pt-1">
            <template x-if="selectedPhoto">
                <img :src="selectedPhoto" :alt="'Foto de ' + selectedName" class="w-9 h-9 rounded-full object-cover border border-cyan-400/20">
            </template>
            <template x-if="!selectedPhoto">
                <div class="w-9 h-9 rounded-full bg-cyan-500/15 flex items-center justify-center text-cyan-200 font-bold text-sm border border-cyan-400/20" x-text="selectedName.charAt(0).toUpperCase()"></div>
            </template>
            <h2 class="text-lg font-semibold text-slate-100" x-text="selectedName"></h2>
            <span class="ml-auto text-xs text-slate-500" x-text="summary ? summary.total + ' registros' : ''"></span>
        </div>

        <!-- Cards de resumo -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Peso -->
            <div class="bg-gray-800/60 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Peso Atual</p>
                <p class="text-2xl font-bold text-white">
                    <span x-text="summary && summary.weight.value ? summary.weight.value : '—'"></span>
                    <span class="text-sm font-normal text-gray-400"> kg</span>
                </p>
                <p
                    class="text-xs mt-1"
                    x-show="summary && summary.weight.diff !== null"
                    :class="summary && summary.weight.diff > 0 ? 'text-red-400' : (summary && summary.weight.diff < 0 ? 'text-green-400' : 'text-gray-500')"
                    x-text="summary && summary.weight.diff !== null ? (summary.weight.diff > 0 ? '+' : '') + summary.weight.diff + ' kg vs anterior' : ''"
                ></p>
            </div>
            <!-- Massa Muscular -->
            <div class="bg-gray-800/60 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Massa Muscular</p>
                <p class="text-2xl font-bold text-white">
                    <span x-text="summary && summary.muscle_mass.value ? summary.muscle_mass.value : '—'"></span>
                    <span class="text-sm font-normal text-gray-400"> kg</span>
                </p>
                <p
                    class="text-xs mt-1"
                    x-show="summary && summary.muscle_mass.diff !== null"
                    :class="summary && summary.muscle_mass.diff >= 0 ? 'text-green-400' : 'text-red-400'"
                    x-text="summary && summary.muscle_mass.diff !== null ? (summary.muscle_mass.diff > 0 ? '+' : '') + summary.muscle_mass.diff + ' kg vs anterior' : ''"
                ></p>
            </div>
            <!-- Gordura % -->
            <div class="bg-gray-800/60 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Gordura Corporal</p>
                <p class="text-2xl font-bold text-white">
                    <span x-text="summary && summary.body_fat.value ? summary.body_fat.value : '—'"></span>
                    <span class="text-sm font-normal text-gray-400"> %</span>
                </p>
                <p
                    class="text-xs mt-1"
                    x-show="summary && summary.body_fat.diff !== null"
                    :class="summary && summary.body_fat.diff <= 0 ? 'text-green-400' : 'text-red-400'"
                    x-text="summary && summary.body_fat.diff !== null ? (summary.body_fat.diff > 0 ? '+' : '') + summary.body_fat.diff + '% vs anterior' : ''"
                ></p>
            </div>
            <!-- Total -->
            <div class="bg-gray-800/60 border border-gray-700 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Avaliações</p>
                <p class="text-2xl font-bold text-indigo-400" x-text="summary ? summary.total : '—'"></p>
                <p class="text-xs text-gray-500 mt-1">registros</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Peso + Massa Muscular -->
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Peso &amp; Massa Muscular
                </h3>
                <div class="relative" style="height:220px">
                    <canvas id="evoCompositionChart"></canvas>
                </div>
            </div>
            <!-- Gordura % -->
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    % de Gordura Corporal
                </h3>
                <div class="relative" style="height:220px">
                    <canvas id="evoBodyFatChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabela histórico -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-700">
                <h3 class="text-sm font-semibold text-white">Histórico de Medições</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-800 text-xs text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Data</th>
                            <th class="px-4 py-3">Peso (kg)</th>
                            <th class="px-4 py-3">Musc. (kg)</th>
                            <th class="px-4 py-3">Gordura (%)</th>
                            <th class="px-4 py-3">IMC</th>
                            <th class="px-4 py-3">Cintura (cm)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        <template x-for="row in history" :key="row.date + row.weight">
                            <tr class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3 text-gray-300 font-medium" x-text="row.date ?? '—'"></td>
                                <td class="px-4 py-3 text-white" x-text="row.weight ?? '—'"></td>
                                <td class="px-4 py-3 text-blue-400" x-text="row.muscle_mass ?? '—'"></td>
                                <td class="px-4 py-3 text-yellow-400" x-text="row.body_fat ?? '—'"></td>
                                <td class="px-4 py-3 text-gray-300" x-text="row.imc ?? '—'"></td>
                                <td class="px-4 py-3 text-gray-300" x-text="row.waist ?? '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Comparativo entre duas últimas avaliações -->
        <div x-show="comparison" x-cloak class="bg-[#0f1a2e]/80 border border-indigo-500/30 rounded-2xl overflow-hidden">
            <button type="button" @click="compOpen = !compOpen"
                    class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-indigo-900/10 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/15 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-indigo-300">Comparativo de Medidas</p>
                        <p class="text-xs text-slate-400" x-text="comparison ? comparison.prev_date + ' → ' + comparison.last_date : ''"></p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="compOpen ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="compOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="border-t border-indigo-500/20 px-5 pb-5 space-y-5">

                    <!-- Tabela reutilizável via template -->
                    <template x-if="compRows('corpo').length > 0">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-4 mb-2">Composição Corporal</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-xs text-slate-500 border-b border-slate-700/50">
                                            <th class="text-left pb-1.5 font-medium">Medida</th>
                                            <th class="text-center pb-1.5 font-medium w-28" x-text="comparison ? comparison.prev_date : 'Anterior'"></th>
                                            <th class="text-center pb-1.5 font-medium w-28" x-text="comparison ? comparison.last_date : 'Atual'"></th>
                                            <th class="text-center pb-1.5 font-medium w-24">Variação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/50">
                                        <template x-for="row in compRows('corpo')" :key="row.label">
                                            <tr>
                                                <td class="py-1.5 text-slate-300 text-xs" x-text="row.label"></td>
                                                <td class="py-1.5 text-center text-slate-400 text-xs" x-text="row.prev"></td>
                                                <td class="py-1.5 text-center text-slate-200 text-xs" x-text="row.last"></td>
                                                <td class="py-1.5 text-center text-xs font-semibold" :class="row.deltaClass" x-text="row.deltaText"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <template x-if="compRows('circs').length > 0">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-2 mb-2">Circunferências (cm)</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-xs text-slate-500 border-b border-slate-700/50">
                                            <th class="text-left pb-1.5 font-medium">Medida</th>
                                            <th class="text-center pb-1.5 font-medium w-28" x-text="comparison ? comparison.prev_date : 'Anterior'"></th>
                                            <th class="text-center pb-1.5 font-medium w-28" x-text="comparison ? comparison.last_date : 'Atual'"></th>
                                            <th class="text-center pb-1.5 font-medium w-24">Variação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/50">
                                        <template x-for="row in compRows('circs')" :key="row.label">
                                            <tr>
                                                <td class="py-1.5 text-slate-300 text-xs" x-text="row.label"></td>
                                                <td class="py-1.5 text-center text-slate-400 text-xs" x-text="row.prev"></td>
                                                <td class="py-1.5 text-center text-slate-200 text-xs" x-text="row.last"></td>
                                                <td class="py-1.5 text-center text-xs font-semibold" :class="row.deltaClass" x-text="row.deltaText"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <template x-if="compRows('dobras').length > 0">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-2 mb-2">Dobras Cutâneas (mm)</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-xs text-slate-500 border-b border-slate-700/50">
                                            <th class="text-left pb-1.5 font-medium">Medida</th>
                                            <th class="text-center pb-1.5 font-medium w-28" x-text="comparison ? comparison.prev_date : 'Anterior'"></th>
                                            <th class="text-center pb-1.5 font-medium w-28" x-text="comparison ? comparison.last_date : 'Atual'"></th>
                                            <th class="text-center pb-1.5 font-medium w-24">Variação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/50">
                                        <template x-for="row in compRows('dobras')" :key="row.label">
                                            <tr>
                                                <td class="py-1.5 text-slate-300 text-xs" x-text="row.label"></td>
                                                <td class="py-1.5 text-center text-slate-400 text-xs" x-text="row.prev"></td>
                                                <td class="py-1.5 text-center text-slate-200 text-xs" x-text="row.last"></td>
                                                <td class="py-1.5 text-center text-xs font-semibold" :class="row.deltaClass" x-text="row.deltaText"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <p class="text-xs text-slate-500 flex items-center gap-2 flex-wrap pt-1">
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Melhora</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span> Piora</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-500 inline-block"></span> Neutro</span>
                    </p>
                </div>
            </div>
        </div>

    </div><!-- /painel -->

</div>

<script>
function evolutionPage() {
    return {
        search: '',
        selectedId: null,
        selectedName: '',
        selectedPhoto: '',
        loading: false,
        noData: false,
        summary: null,
        history: [],
        comparison: null,
        compOpen: true,
        compositionChart: null,
        bodyFatChart: null,

        init() {},

        selectStudent(id, name, photo) {
            if (this.selectedId === id) return;
            this.selectedId = id;
            this.selectedName = name;
            this.selectedPhoto = photo || '';
            this.loadData(id);
        },

        loadData(id) {
            this.loading = true;
            this.noData  = false;
            this.summary = null;
            this.history = [];
            this.comparison = null;
            this.destroyCharts();

            fetch(`{{ url('personal/evolucao/dados') }}/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                this.loading = false;

                if (!data.dates || data.dates.length < 2) {
                    this.noData = true;
                    return;
                }

                this.summary    = data.summary;
                this.history    = data.history;
                this.comparison = data.comparison ?? null;

                this.$nextTick(() => {
                    this.buildCompositionChart(data.dates, data.weights, data.muscles);
                    this.buildBodyFatChart(data.dates, data.bodyFats);
                });
            })
            .catch(() => {
                this.loading = false;
                this.noData  = true;
            });
        },

        compRows(group) {
            const c = this.comparison;
            if (!c) return [];
            const prev = c.prev;
            const last = c.last;
            const groups = {
                corpo: [
                    { label: 'Peso (kg)',           field: 'weight',       downIsGood: false, neutral: true  },
                    { label: '% Gordura',           field: 'body_fat',     downIsGood: true,  neutral: false },
                    { label: 'Massa Muscular (kg)', field: 'muscle_mass',  downIsGood: false, neutral: false },
                ],
                circs: [
                    { label: 'Peitoral',       field: 'chest',        downIsGood: false, neutral: true  },
                    { label: 'Cintura',        field: 'waist',        downIsGood: true,  neutral: false },
                    { label: 'Abdômen',        field: 'abdomen',      downIsGood: true,  neutral: false },
                    { label: 'Quadril',        field: 'hips',         downIsGood: false, neutral: true  },
                    { label: 'Braço D',        field: 'right_arm',    downIsGood: false, neutral: true  },
                    { label: 'Braço E',        field: 'left_arm',     downIsGood: false, neutral: true  },
                    { label: 'Coxa D',         field: 'right_thigh',  downIsGood: false, neutral: true  },
                    { label: 'Coxa E',         field: 'left_thigh',   downIsGood: false, neutral: true  },
                    { label: 'Panturrilha D',  field: 'right_calf',   downIsGood: false, neutral: true  },
                    { label: 'Panturrilha E',  field: 'left_calf',    downIsGood: false, neutral: true  },
                ],
                dobras: [
                    { label: 'Subescapular',   field: 'subescapular',     downIsGood: true, neutral: false },
                    { label: 'Tricipital',     field: 'tricipital',       downIsGood: true, neutral: false },
                    { label: 'Bicipital',      field: 'bicipital',        downIsGood: true, neutral: false },
                    { label: 'Torácica',       field: 'toracica',         downIsGood: true, neutral: false },
                    { label: 'Abdominal',      field: 'abdominal_fold',   downIsGood: true, neutral: false },
                    { label: 'Axilar Média',   field: 'axilar_media',     downIsGood: true, neutral: false },
                    { label: 'Supra-ilíaca',   field: 'suprailiaca',      downIsGood: true, neutral: false },
                    { label: 'Coxa',           field: 'coxa_fold',        downIsGood: true, neutral: false },
                    { label: 'Panturrilha',    field: 'panturrilha_fold', downIsGood: true, neutral: false },
                    { label: 'Soma (mm)',       field: 'sum_skinfolds',   downIsGood: true, neutral: false },
                ],
            };
            return (groups[group] || [])
                .filter(m => prev[m.field] !== null || last[m.field] !== null)
                .map(m => {
                    const p = prev[m.field];
                    const l = last[m.field];
                    const d = (p !== null && l !== null) ? parseFloat((l - p).toFixed(2)) : null;
                    const dt = d === null ? '—' : (d === 0 ? '=' : (d > 0 ? '+' : '') + d.toFixed(1).replace('.', ','));
                    let dc = 'text-slate-400';
                    if (!m.neutral && d !== null && d !== 0) {
                        dc = m.downIsGood
                            ? (d < 0 ? 'text-emerald-400' : 'text-rose-400')
                            : (d > 0 ? 'text-emerald-400' : 'text-rose-400');
                    }
                    const fmt = v => v !== null ? parseFloat(v).toFixed(1).replace('.', ',') : '—';
                    return { label: m.label, prev: fmt(p), last: fmt(l), deltaText: dt, deltaClass: dc };
                });
        },

        destroyCharts() {
            if (this.compositionChart) { this.compositionChart.destroy(); this.compositionChart = null; }
            if (this.bodyFatChart)     { this.bodyFatChart.destroy();     this.bodyFatChart     = null; }
        },

        commonOptions() {
            const gridColor = 'rgba(71,85,105,0.2)';
            const tickColor = '#94a3b8';
            return {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: tickColor, maxRotation: 45 } },
                    y: { grid: { color: gridColor }, ticks: { color: tickColor } }
                },
                plugins: {
                    legend: { labels: { color: '#e2e8f0', boxWidth: 12 } },
                    tooltip: { mode: 'index', intersect: false }
                }
            };
        },

        buildCompositionChart(labels, weights, muscles) {
            const ctx = document.getElementById('evoCompositionChart');
            if (!ctx) return;
            this.compositionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Peso (kg)',
                            data: weights,
                            borderColor: '#818cf8',
                            backgroundColor: 'rgba(129,140,248,0.15)',
                            fill: true, tension: 0.4,
                            pointBackgroundColor: '#818cf8', pointRadius: 4
                        },
                        {
                            label: 'Massa Muscular (kg)',
                            data: muscles,
                            borderColor: '#34d399',
                            backgroundColor: 'rgba(52,211,153,0.15)',
                            fill: true, tension: 0.4,
                            pointBackgroundColor: '#34d399', pointRadius: 4
                        }
                    ]
                },
                options: this.commonOptions()
            });
        },

        buildBodyFatChart(labels, bodyFats) {
            const ctx = document.getElementById('evoBodyFatChart');
            if (!ctx) return;
            this.bodyFatChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: '% Gordura',
                        data: bodyFats,
                        borderColor: '#fbbf24',
                        backgroundColor: 'rgba(251,191,36,0.15)',
                        fill: true, tension: 0.4,
                        pointBackgroundColor: '#fbbf24', pointRadius: 4
                    }]
                },
                options: this.commonOptions()
            });
        }
    };
}
</script>
@endsection
