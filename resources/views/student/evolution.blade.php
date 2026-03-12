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
