@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8" x-data="evolutionData()">
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

            <!-- Tabela Detalhada -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
                <div class="p-5 border-b border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-white">Histórico de Medidas</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Data</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Peso</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Gordura</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cintura</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Abdômen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($measurements->reverse() as $measurement)
                                <tr class="hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $measurement->date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $measurement->weight }} kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $measurement->body_fat }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $measurement->waist ?? '-' }} cm</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $measurement->abdomen ?? '-' }} cm</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                        <select x-model="leftId" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-700 text-white focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 sm:text-sm rounded-lg">
                            <template x-for="m in measurements" :key="m.id">
                                <option :value="m.id" x-text="formatDate(m.date) + ' - ' + m.weight + 'kg'"></option>
                            </template>
                        </select>
                    </div>
                    <!-- Select Right -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Avaliação 2 (Direita)</label>
                        <select x-model="rightId" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-600 bg-gray-700 text-white focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500 sm:text-sm rounded-lg">
                            <template x-for="m in measurements" :key="m.id">
                                <option :value="m.id" x-text="formatDate(m.date) + ' - ' + m.weight + 'kg'"></option>
                            </template>
                        </select>
                    </div>
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
                                    <div class="aspect-w-3 aspect-h-4">
                                        <img :src="getPhotoUrl(leftMeasurement.id, 'front')" class="w-full h-full object-cover rounded shadow cursor-pointer hover:opacity-90 transition" @click="openModal(getPhotoUrl(leftMeasurement.id, 'front'))">
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
                                    <div class="aspect-w-3 aspect-h-4">
                                        <img :src="getPhotoUrl(rightMeasurement.id, 'front')" class="w-full h-full object-cover rounded shadow cursor-pointer hover:opacity-90 transition" @click="openModal(getPhotoUrl(rightMeasurement.id, 'front'))">
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
                    
                    <!-- Side -->
                    <div>
                        <div class="flex items-center justify-center mb-4">
                            <span class="px-3 py-1 bg-gray-700 text-gray-200 rounded-full text-sm font-bold">Vista Lateral (Perfil)</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="leftMeasurement && leftMeasurement.photo_side">
                                    <div class="aspect-w-3 aspect-h-4">
                                        <img :src="getPhotoUrl(leftMeasurement.id, 'side')" class="w-full h-full object-cover rounded shadow cursor-pointer hover:opacity-90 transition" @click="openModal(getPhotoUrl(leftMeasurement.id, 'side'))">
                                    </div>
                                </template>
                                <template x-if="!leftMeasurement || !leftMeasurement.photo_side">
                                    <div class="flex items-center justify-center h-64 bg-gray-600 text-gray-400 rounded">
                                        <span class="text-sm">Sem foto</span>
                                    </div>
                                </template>
                                <p class="text-center text-sm font-bold mt-2 text-gray-300" x-text="leftMeasurement ? formatDate(leftMeasurement.date) : '-'"></p>
                            </div>
                            <div class="bg-gray-700/50 p-2 rounded-lg shadow-sm border border-gray-600">
                                <template x-if="rightMeasurement && rightMeasurement.photo_side">
                                    <div class="aspect-w-3 aspect-h-4">
                                        <img :src="getPhotoUrl(rightMeasurement.id, 'side')" class="w-full h-full object-cover rounded shadow cursor-pointer hover:opacity-90 transition" @click="openModal(getPhotoUrl(rightMeasurement.id, 'side'))">
                                    </div>
                                </template>
                                <template x-if="!rightMeasurement || !rightMeasurement.photo_side">
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
                                    <div class="aspect-w-3 aspect-h-4">
                                        <img :src="getPhotoUrl(leftMeasurement.id, 'back')" class="w-full h-full object-cover rounded shadow cursor-pointer hover:opacity-90 transition" @click="openModal(getPhotoUrl(leftMeasurement.id, 'back'))">
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
                                    <div class="aspect-w-3 aspect-h-4">
                                        <img :src="getPhotoUrl(rightMeasurement.id, 'back')" class="w-full h-full object-cover rounded shadow cursor-pointer hover:opacity-90 transition" @click="openModal(getPhotoUrl(rightMeasurement.id, 'back'))">
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
            
            init() {
                if (this.measurements.length > 0) {
                    this.leftId = this.measurements[0].id;
                    this.rightId = this.measurements[this.measurements.length - 1].id;
                }
            },

            getPhotoUrl(id, type) {
                return `/measurement/${id}/${type}`;
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
                    borderColor: 'rgb(148, 163, 184)', // Slate 400
                    backgroundColor: 'rgba(148, 163, 184, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Massa Muscular (kg)',
                    data: @json($muscleMasses),
                    borderColor: 'rgb(100, 116, 139)', // Slate 600
                    backgroundColor: 'rgba(100, 116, 139, 0.1)',
                    tension: 0.3,
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
                    borderColor: 'rgb(200, 30, 30)', // Red 700
                    backgroundColor: 'rgba(200, 30, 30, 0.1)',
                    tension: 0.3,
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
