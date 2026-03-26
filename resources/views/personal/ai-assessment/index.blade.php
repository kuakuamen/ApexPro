@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white flex items-center">
            <svg class="w-8 h-8 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            Avaliação com Inteligência Artificial
        </h2>
    </div>

    <!-- Tabs para alternar entre dois modos -->
    <div class="border-b border-gray-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('tab-images')" id="btn-tab-images" class="tab-btn active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base focus:outline-none border-gray-400 text-white transition">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Com Análise de Imagens
            </button>
            <button onclick="switchTab('tab-noimage')" id="btn-tab-noimage" class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-medium text-base focus:outline-none border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600 transition">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Gerar Treino Direto
            </button>
        </nav>
    </div>

    <!-- TAB 1: COM IMAGENS -->
    <div id="tab-images" class="tab-content">
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-white">Como funciona?</h3>
                    <p class="text-gray-400 mt-1">
                        Envie as fotos do aluno e nossa IA fará a análise postural, identificará desvios e 
                        <strong class="text-indigo-400">criará automaticamente uma sugestão de treino</strong> baseada no objetivo.
                    </p>
                </div>

                <form action="{{ route('personal.ai-assessment.analyze') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Hidden inputs para reutilização de imagens -->
                    <input type="hidden" name="reuse_source_type" id="reuse_source_type" value="">
                    <input type="hidden" name="reuse_source_id"   id="reuse_source_id"   value="">
                    <input type="hidden" name="reuse_front"       id="reuse_front"       value="">
                    <input type="hidden" name="reuse_side_right"  id="reuse_side_right"  value="">
                    <input type="hidden" name="reuse_side_left"   id="reuse_side_left"   value="">
                    <input type="hidden" name="reuse_back"        id="reuse_back"        value="">
                    <input type="hidden" name="reuse_extras"      id="reuse_extras"      value="">
                    <div id="reuse_extra_indices_container"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Seleção de Aluno -->
                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-300 mb-2">Selecione o Aluno</label>
                            <select id="student_id" name="student_id" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" class="bg-gray-700">{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Objetivo -->
                        <div>
                            <label for="goal" class="block text-sm font-medium text-gray-300 mb-2">Objetivo do Treino</label>
                            <select id="goal" name="goal" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="Hipertrofia" class="bg-gray-700">Hipertrofia (Ganho de Massa)</option>
                                <option value="Emagrecimento" class="bg-gray-700">Emagrecimento (Perda de Gordura)</option>
                                <option value="Resistência" class="bg-gray-700">Resistência Muscular</option>
                                <option value="Força" class="bg-gray-700">Força Pura</option>
                                <option value="Correção Postural" class="bg-gray-700">Foco em Correção Postural</option>
                            </select>
                        </div>

                        <!-- Nível de Experiência -->
                        <div>
                            <label for="experience_level" class="block text-sm font-medium text-gray-300 mb-2">Nível de Experiência</label>
                            <select id="experience_level" name="experience_level" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="Iniciante" class="bg-gray-700">Iniciante (Nunca treinou ou parou há muito tempo)</option>
                                <option value="Intermediário" class="bg-gray-700">Intermediário (Treina regularmente há 6 meses+)</option>
                                <option value="Avançado" class="bg-gray-700">Avançado (Treina sério há anos)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Banner: Reutilizar imagens da última avaliação -->
                    <div id="reuse-banner" class="hidden rounded-xl border border-indigo-500/50 bg-indigo-900/30 p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-indigo-300">Imagens encontradas</p>
                                <p id="reuse-banner-date" class="text-xs text-gray-400 mt-0.5"></p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <button type="button" id="btn-apply-reuse" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors" style="background:#3730a3;border:1px solid #6366f1;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Utilizar imagens da última avaliação
                                </button>
                                <button type="button" id="btn-dismiss-reuse" class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-gray-200 transition-colors border border-gray-600 hover:border-gray-400">
                                    Ignorar
                                </button>
                            </div>
                        </div>
                        <!-- Preview das imagens disponíveis -->
                        <div id="reuse-preview-row" class="hidden mt-4 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                    </div>

                    <!-- Observações do Personal (Contexto Extra para este Treino) -->
                    <div class="pt-6 border-t border-gray-700">
                        <h4 class="text-md font-bold text-white mb-4">Contexto do Treino</h4>
                        <div>
                            <label for="additional_notes" class="block text-sm font-medium text-gray-300 mb-2">Observações Específicas para este Treino</label>
                            <div class="mt-1">
                                <textarea id="additional_notes" name="additional_notes" rows="3" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Focar em glúteos hoje. O aluno reclamou de cansaço..."></textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-400">
                                Use este campo para dar instruções pontuais à IA sobre o treino.
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-700 pt-6">
                        <h4 class="text-md font-bold text-white mb-4">Fotos para Análise</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Foto Frente -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Vista Frontal</label>
                                <div id="photo_front_container" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm">
                                    <div class="space-y-3 text-center w-full">
                                        <svg id="photo_front_icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <img id="photo_front_preview" src="" alt="Preview frontal" class="hidden w-full object-cover rounded-lg border border-gray-600" style="aspect-ratio: 16 / 9;">
                                        <div class="flex flex-col gap-2">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#0f7490;border:1px solid #22a7c7;color:#f1f5f9;" onclick="openCamera('photo_front')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Câmera
                                            </button>
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#5b5fd6;border:1px solid #7c86ee;color:#f1f5f9;" onclick="openGallery('photo_front')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Galeria
                                            </button>
                                        </div>
                                        <input id="photo_front" name="photo_front" type="file" class="sr-only" accept="image/*" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto Lado D -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Vista Lateral D</label>
                                <div id="photo_side_right_container" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm">
                                    <div class="space-y-3 text-center w-full">
                                        <svg id="photo_side_right_icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <img id="photo_side_right_preview" src="" alt="Preview lateral direita" class="hidden w-full object-cover rounded-lg border border-gray-600" style="aspect-ratio: 16 / 9;">
                                        <div class="flex flex-col gap-2">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#0f7490;border:1px solid #22a7c7;color:#f1f5f9;" onclick="openCamera('photo_side_right')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Câmera
                                            </button>
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#5b5fd6;border:1px solid #7c86ee;color:#f1f5f9;" onclick="openGallery('photo_side_right')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Galeria
                                            </button>
                                        </div>
                                        <input id="photo_side_right" name="photo_side_right" type="file" class="sr-only" accept="image/*" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto Lado E -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Vista Lateral E</label>
                                <div id="photo_side_left_container" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm">
                                    <div class="space-y-3 text-center w-full">
                                        <svg id="photo_side_left_icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <img id="photo_side_left_preview" src="" alt="Preview lateral esquerda" class="hidden w-full object-cover rounded-lg border border-gray-600" style="aspect-ratio: 16 / 9;">
                                        <div class="flex flex-col gap-2">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#0f7490;border:1px solid #22a7c7;color:#f1f5f9;" onclick="openCamera('photo_side_left')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Câmera
                                            </button>
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#5b5fd6;border:1px solid #7c86ee;color:#f1f5f9;" onclick="openGallery('photo_side_left')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Galeria
                                            </button>
                                        </div>
                                        <input id="photo_side_left" name="photo_side_left" type="file" class="sr-only" accept="image/*" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto Costas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Vista Costas</label>
                                <div id="photo_back_container" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm">
                                    <div class="space-y-3 text-center w-full">
                                        <svg id="photo_back_icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <img id="photo_back_preview" src="" alt="Preview costas" class="hidden w-full object-cover rounded-lg border border-gray-600" style="aspect-ratio: 16 / 9;">
                                        <div class="flex flex-col gap-2">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#0f7490;border:1px solid #22a7c7;color:#f1f5f9;" onclick="openCamera('photo_back')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Câmera
                                            </button>
                                            <button type="button" class="w-full flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#5b5fd6;border:1px solid #7c86ee;color:#f1f5f9;" onclick="openGallery('photo_back')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Galeria
                                            </button>
                                        </div>
                                        <input id="photo_back" name="photo_back" type="file" class="sr-only" accept="image/*" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Fotos Extras (Opcional)</label>
                            <div id="photo_extra_container" class="mt-1 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm p-4">
                                <div id="photo_extra_add_row" class="flex flex-col sm:flex-row gap-2 items-center justify-between">
                                    <p class="text-xs text-gray-400">Adicione até 6 fotos extras para a IA analisar melhor.</p>
                                    <button type="button" class="w-full sm:w-auto flex items-center justify-center gap-2 font-medium py-2 px-4 rounded-lg transition-colors" style="background:#5b5fd6;border:1px solid #7c86ee;color:#f1f5f9;" onclick="openGallery('photo_extra')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Adicionar Fotos Extras
                                    </button>
                                </div>
                                <input id="photo_extra" name="photo_extra[]" type="file" class="sr-only" accept="image/*" multiple>
                                <div id="photo_extra_preview_grid" class="hidden mt-4 grid grid-cols-2 md:grid-cols-3 gap-3"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-700">
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Analisar e Gerar Treino
                            </button>
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-400">
                        <strong class="text-indigo-400">Importante:</strong> Certifique-se de que as fotos estejam bem iluminadas e mostrem claramente a postura do aluno.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB 2: SEM IMAGENS -->
    <div id="tab-noimage" class="tab-content hidden">
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-white">Gerar Treino Direto</h3>
                    <p class="text-gray-400 mt-1">
                        Crie um treino personalizado para seu aluno sem necessidade de análise de imagens. 
                        <strong class="text-indigo-400">A IA usará dados pessoais e suas observações</strong> para sugerir o melhor plano.
                    </p>
                </div>

                <form action="{{ route('personal.ai-assessment.analyze-no-images') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Seleção de Aluno -->
                        <div>
                            <label for="student_id_noimg" class="block text-sm font-medium text-gray-300 mb-2">Selecione o Aluno</label>
                            <select id="student_id_noimg" name="student_id" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="" class="bg-gray-700">Selecione...</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" class="bg-gray-700">{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Objetivo -->
                        <div>
                            <label for="goal_noimg" class="block text-sm font-medium text-gray-300 mb-2">Objetivo do Treino</label>
                            <select id="goal_noimg" name="goal" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="Hipertrofia" class="bg-gray-700">Hipertrofia (Ganho de Massa)</option>
                                <option value="Emagrecimento" class="bg-gray-700">Emagrecimento (Perda de Gordura)</option>
                                <option value="Resistência" class="bg-gray-700">Resistência Muscular</option>
                                <option value="Força" class="bg-gray-700">Força Pura</option>
                                <option value="Correção Postural" class="bg-gray-700">Foco em Correção Postural</option>
                            </select>
                        </div>

                        <!-- Nível de Experiência -->
                        <div>
                            <label for="experience_level_noimg" class="block text-sm font-medium text-gray-300 mb-2">Nível de Experiência</label>
                            <select id="experience_level_noimg" name="experience_level" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="Iniciante" class="bg-gray-700">Iniciante (Nunca treinou ou parou há muito tempo)</option>
                                <option value="Intermediário" class="bg-gray-700">Intermediário (Treina regularmente há 6 meses+)</option>
                                <option value="Avançado" class="bg-gray-700">Avançado (Treina sério há anos)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Observações e Contexto -->
                    <div class="pt-6 border-t border-gray-700">
                        <h4 class="text-md font-bold text-white mb-4">Informações Importantes</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="description_noimg" class="block text-sm font-medium text-gray-300 mb-2">Descreva o Aluno (Física/Condicionamento)</label>
                                <textarea id="description_noimg" name="description" rows="3" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Genético ectomorfo, postura curvada, mobilidade limitada em lombares..."></textarea>
                                <p class="mt-2 text-sm text-gray-400">Descreva características físicas, limitações e pontos fortes do aluno.</p>
                            </div>

                            <div>
                                <label for="notes_noimg" class="block text-sm font-medium text-gray-300 mb-2">Objetivos e Observações Específicas</label>
                                <textarea id="notes_noimg" name="additional_notes" rows="4" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Ex: Quer melhorar postura para trabalho. Dor em ombros. Quer ganhar massa em peito e costas. Tem lesão no joelho esquerdo..."></textarea>
                                <p class="mt-2 text-sm text-gray-400">Inclua lesões, dores, restrições, prioridades e qualquer contexto importante.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-700">
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-green-500 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Gerar Treino com IA
                            </button>
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-400">
                        <strong class="text-green-400">Dica:</strong> Quanto mais detalhes você fornecer, melhor será o treino personalizado gerado pela IA.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="bg-gray-800/50 backdrop-blur-sm border-t border-gray-700 px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-400">
                <p>© {{ date('Y') }} ApexPro - Todos os direitos reservados</p>
            </div>
            <div class="text-sm text-gray-400">
                <p>Desenvolvido com ❤️ para personal trainers</p>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="text-center max-w-md mx-auto p-6">
        <!-- Spinner -->
        <div class="relative w-24 h-24 mx-auto mb-8">
            <div class="absolute inset-0 border-t-4 border-indigo-500 border-solid rounded-full animate-spin"></div>
            <div class="absolute inset-2 border-t-4 border-purple-500 border-solid rounded-full animate-spin-reverse"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-8 h-8 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>

        <!-- Status Text -->
        <h3 class="text-2xl font-bold text-white mb-2 animate-pulse">Analisando com IA...</h3>
        <p id="loading-message" class="text-gray-300 text-lg transition-all duration-300">Iniciando processamento das imagens...</p>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-700 rounded-full h-2.5 mt-6 overflow-hidden">
            <div id="loading-progress" class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
        
        <p class="text-sm text-gray-500 mt-4">Por favor, não feche esta janela.</p>
    </div>
</div>

<style>
    @keyframes spin-reverse {
        from { transform: rotate(360deg); }
        to { transform: rotate(0deg); }
    }
    .animate-spin-reverse {
        animation: spin-reverse 1.5s linear infinite;
    }
</style>

<script>
    const LAST_IMAGES_URL = "{{ route('personal.ai-assessment.last-images', ['student' => '__ID__']) }}";

    // ===================== ABA =====================
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-gray-400', 'text-white');
            btn.classList.add('border-transparent', 'text-gray-400');
        });
        document.getElementById(tabName).classList.remove('hidden');
        const activeBtn = document.getElementById('btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.add('active', 'border-gray-400', 'text-white');
            activeBtn.classList.remove('border-transparent', 'text-gray-400');
        }
    }

    // ===================== LOADING =====================
    function initLoadingSystem() {
        const forms = document.querySelectorAll('form');
        const overlay = document.getElementById('loading-overlay');
        const messageEl = document.getElementById('loading-message');
        const progressEl = document.getElementById('loading-progress');
        const messages = [
            "Analisando simetria corporal...",
            "Identificando desvios posturais...",
            "Calculando proporções musculares...",
            "Gerando recomendações de exercícios...",
            "Finalizando relatório detalhado..."
        ];
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                overlay.classList.remove('hidden');
                void overlay.offsetWidth;
                overlay.classList.remove('opacity-0');
                let progress = 0, messageIndex = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 5;
                    if (progress > 95) progress = 95;
                    progressEl.style.width = `${progress}%`;
                    const newIndex = Math.floor(progress / 20);
                    if (newIndex !== messageIndex && newIndex < messages.length) {
                        messageIndex = newIndex;
                        messageEl.style.opacity = '0';
                        setTimeout(() => { messageEl.textContent = messages[messageIndex]; messageEl.style.opacity = '1'; }, 300);
                    }
                }, 500);
                setTimeout(() => {
                    clearInterval(interval);
                    if (!document.hidden) {
                        messageEl.textContent = "O processamento está demorando mais que o esperado...";
                        messageEl.classList.add('text-yellow-400');
                    }
                }, 30000);
            });
        });
    }

    // ===================== CÂMERA / GALERIA =====================
    function openCamera(inputId) {
        const input = document.getElementById(inputId);
        if (input) { input.setAttribute('capture', 'environment'); input.click(); }
    }
    function openGallery(inputId) {
        const input = document.getElementById(inputId);
        if (input) { input.removeAttribute('capture'); input.click(); }
    }

    // ===================== INDICADORES DE FOTO =====================
    const PHOTO_SLOTS = [
        { input: 'photo_front',      container: 'photo_front_container',      preview: 'photo_front_preview',      icon: 'photo_front_icon',      reuseKey: 'front' },
        { input: 'photo_side_right', container: 'photo_side_right_container', preview: 'photo_side_right_preview', icon: 'photo_side_right_icon', reuseKey: 'side_right' },
        { input: 'photo_side_left',  container: 'photo_side_left_container',  preview: 'photo_side_left_preview',  icon: 'photo_side_left_icon',  reuseKey: 'side_left' },
        { input: 'photo_back',       container: 'photo_back_container',       preview: 'photo_back_preview',       icon: 'photo_back_icon',       reuseKey: 'back' },
    ];

    function setupPhotoIndicators() {
        PHOTO_SLOTS.forEach(photo => {
            const input     = document.getElementById(photo.input);
            const container = document.getElementById(photo.container);
            const preview   = document.getElementById(photo.preview);
            const icon      = document.getElementById(photo.icon);
            if (!input || !container || !preview || !icon) return;

            input.addEventListener('change', () => {
                // Se o usuário fez upload manual, cancela reuse para esse slot
                clearReuseSlot(photo.reuseKey);
                if (input.files && input.files.length > 0) {
                    container.classList.remove('border-gray-600', 'hover:border-indigo-400');
                    container.classList.add('border-green-500', 'hover:border-green-400');
                    const file = input.files[0];
                    if (file && file.type.startsWith('image/')) {
                        preview.src = URL.createObjectURL(file);
                        preview.classList.remove('hidden');
                        icon.classList.add('hidden');
                        removeReuseTag(photo.container);
                    }
                } else {
                    resetSlotUI(photo);
                }
            });
        });
    }

    function resetSlotUI(photo) {
        const container = document.getElementById(photo.container);
        const preview   = document.getElementById(photo.preview);
        const icon      = document.getElementById(photo.icon);
        const input     = document.getElementById(photo.input);
        if (!container) return;
        container.classList.add('border-gray-600', 'hover:border-indigo-400');
        container.classList.remove('border-green-500', 'hover:border-green-400', 'border-indigo-400', 'hover:border-indigo-300');
        if (preview) { preview.classList.add('hidden'); preview.removeAttribute('src'); }
        if (icon) icon.classList.remove('hidden');
        // Restaurar botões de upload e required
        const btnsDiv = container.querySelector('.flex.flex-col.gap-2');
        if (btnsDiv) btnsDiv.classList.remove('hidden');
        if (input) input.setAttribute('required', '');
        removeReuseTag(photo.container);
    }

    function removeReuseTag(containerId) {
        const tag = document.querySelector(`#${containerId} .reuse-tag`);
        if (tag) tag.remove();
    }

    // ===================== EXTRA PHOTOS =====================
    function setupExtraPhotosPreview() {
        const input = document.getElementById('photo_extra');
        const grid  = document.getElementById('photo_extra_preview_grid');
        if (!input || !grid) return;
        input.addEventListener('change', () => {
            let files = Array.from(input.files || []).filter(f => f.type.startsWith('image/'));
            if (files.length > 6) {
                files = files.slice(0, 6);
                const dt = new DataTransfer();
                files.forEach(f => dt.items.add(f));
                input.files = dt.files;
                alert('Máximo de 6 fotos extras. Mantivemos as 6 primeiras.');
            }
            grid.innerHTML = '';
            if (!files.length) { grid.classList.add('hidden'); return; }
            files.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'relative border border-gray-600 rounded-md overflow-hidden bg-gray-900';
                const label = document.createElement('div');
                label.className = 'absolute top-1 left-1 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded';
                label.textContent = `Extra ${index + 1}`;
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = `Preview Extra ${index + 1}`;
                img.className = 'w-full object-cover';
                img.style.aspectRatio = '16 / 9';
                wrapper.appendChild(img);
                wrapper.appendChild(label);
                grid.appendChild(wrapper);
            });
            grid.classList.remove('hidden');
        });
    }

    // ===================== REUTILIZAÇÃO DE IMAGENS =====================
    let _reuseData = null;

    function clearReuseSlot(slot) {
        document.getElementById('reuse_' + slot).value = '';
    }

    function clearAllReuse() {
        ['reuse_source_type','reuse_source_id','reuse_front','reuse_side_right','reuse_side_left','reuse_back','reuse_extras'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('reuse_extra_indices_container').innerHTML = '';
        _reuseData = null;
        PHOTO_SLOTS.forEach(photo => resetSlotUI(photo));
        // Restaurar botão de adicionar extras
        const addRow = document.getElementById('photo_extra_add_row');
        if (addRow) addRow.classList.remove('hidden');
        // Limpar preview de extras reutilizadas
        const grid = document.getElementById('photo_extra_preview_grid');
        if (grid) { grid.innerHTML = ''; grid.classList.add('hidden'); }
    }

    function applyReuseImages(data) {
        _reuseData = data;
        document.getElementById('reuse_source_type').value = data.source;
        document.getElementById('reuse_source_id').value   = data.source_id;

        const slotMap = {
            front:      { hiddenId: 'reuse_front',      inputId: 'photo_front',      previewId: 'photo_front_preview',      iconId: 'photo_front_icon',      containerId: 'photo_front_container',      label: 'Frontal' },
            side_right: { hiddenId: 'reuse_side_right', inputId: 'photo_side_right', previewId: 'photo_side_right_preview', iconId: 'photo_side_right_icon', containerId: 'photo_side_right_container', label: 'Lat. D' },
            side_left:  { hiddenId: 'reuse_side_left',  inputId: 'photo_side_left',  previewId: 'photo_side_left_preview',  iconId: 'photo_side_left_icon',  containerId: 'photo_side_left_container',  label: 'Lat. E' },
            back:       { hiddenId: 'reuse_back',        inputId: 'photo_back',       previewId: 'photo_back_preview',       iconId: 'photo_back_icon',       containerId: 'photo_back_container',       label: 'Costas' },
        };

        Object.entries(slotMap).forEach(([slot, cfg]) => {
            const url = data.images[slot];
            if (!url) return;

            document.getElementById(cfg.hiddenId).value = '1';

            const container = document.getElementById(cfg.containerId);
            const preview   = document.getElementById(cfg.previewId);
            const icon      = document.getElementById(cfg.iconId);
            const input     = document.getElementById(cfg.inputId);

            // Remover required para não bloquear submit nativo do browser
            if (input) input.removeAttribute('required');

            if (preview) {
                preview.src = url;
                preview.classList.remove('hidden');
            }
            if (icon) icon.classList.add('hidden');
            if (container) {
                container.classList.remove('border-gray-600', 'hover:border-indigo-400');
                container.classList.add('border-indigo-400', 'hover:border-indigo-300');
                removeReuseTag(cfg.containerId);

                // Ocultar botões Câmera/Galeria
                const btnsDiv = container.querySelector('.flex.flex-col.gap-2');
                if (btnsDiv) btnsDiv.classList.add('hidden');

                // Adicionar tag "Reutilizada"
                const tag = document.createElement('div');
                tag.className = 'reuse-tag flex items-center justify-between gap-1 mt-1 px-2 py-1 rounded text-xs font-medium bg-indigo-900/60 border border-indigo-500/40 text-indigo-300';
                tag.innerHTML = `<span>Reutilizada</span>
                    <button type="button" class="hover:text-red-400 transition-colors ml-auto" title="Remover imagem reutilizada" onclick="removeReuseSlot('${slot}')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>`;
                container.querySelector('.space-y-3').appendChild(tag);
            }
        });

        // Aplicar TODAS as extras reutilizadas (sem limite)
        const extras = data.extras || [];
        if (extras.length > 0) {
            // Ocultar botão "Adicionar Fotos Extras"
            const addRow = document.getElementById('photo_extra_add_row');
            if (addRow) addRow.classList.add('hidden');

            document.getElementById('reuse_extras').value = '1';
            const container = document.getElementById('reuse_extra_indices_container');
            container.innerHTML = '';
            extras.forEach((url, index) => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'reuse_extra_indices[]';
                inp.value = index;
                container.appendChild(inp);
            });
            // Exibir previews das extras reutilizadas na grade
            const grid = document.getElementById('photo_extra_preview_grid');
            grid.innerHTML = '';
            extras.forEach((url, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'relative border border-indigo-500/50 rounded-md overflow-hidden bg-gray-900';
                const labelEl = document.createElement('div');
                labelEl.className = 'absolute top-1 left-1 bg-indigo-900/80 text-indigo-300 text-[10px] px-1.5 py-0.5 rounded';
                labelEl.textContent = `Extra ${index + 1}`;
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.title = 'Remover esta extra';
                removeBtn.className = 'absolute top-1 right-1 bg-black/60 hover:bg-red-900/80 text-white hover:text-red-300 rounded p-0.5 transition-colors';
                removeBtn.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
                removeBtn.addEventListener('click', () => removeReuseExtra(index, wrapper));
                const img = document.createElement('img');
                img.src = url;
                img.alt = `Extra reutilizada ${index + 1}`;
                img.className = 'w-full object-cover';
                img.style.aspectRatio = '16 / 9';
                wrapper.dataset.reuseIndex = index;
                wrapper.appendChild(img);
                wrapper.appendChild(labelEl);
                wrapper.appendChild(removeBtn);
                grid.appendChild(wrapper);
            });
            grid.classList.remove('hidden');
        }
    }

    function removeReuseSlot(slot) {
        clearReuseSlot(slot);
        const photo = PHOTO_SLOTS.find(p => p.reuseKey === slot);
        if (photo) resetSlotUI(photo);
    }

    function removeReuseExtra(index, wrapperEl) {
        // Remove o hidden input do índice correspondente
        const container = document.getElementById('reuse_extra_indices_container');
        const inp = container.querySelector(`input[value="${index}"]`);
        if (inp) inp.remove();
        // Remove o card de preview
        if (wrapperEl) wrapperEl.remove();
        // Se não restam mais extras reutilizadas E não há novos uploads, ocultar grade
        const grid = document.getElementById('photo_extra_preview_grid');
        if (grid && grid.children.length === 0) {
            grid.classList.add('hidden');
            document.getElementById('reuse_extras').value = '';
        }
        // Renumerar labels visuais
        if (grid) {
            Array.from(grid.children).forEach((el, i) => {
                const lbl = el.querySelector('.text-\\[10px\\]') || el.querySelector('[class*="text-[10px]"]');
                if (lbl) lbl.textContent = `Extra ${i + 1}`;
            });
        }
    }

    function setupReuseSystem() {
        const studentSelect = document.getElementById('student_id');
        const banner        = document.getElementById('reuse-banner');
        const bannerDate    = document.getElementById('reuse-banner-date');
        const previewRow    = document.getElementById('reuse-preview-row');
        const btnApply      = document.getElementById('btn-apply-reuse');
        const btnDismiss    = document.getElementById('btn-dismiss-reuse');

        studentSelect.addEventListener('change', function() {
            const studentId = this.value;
            banner.classList.add('hidden');
            previewRow.innerHTML = '';
            previewRow.classList.add('hidden');
            clearAllReuse();

            if (!studentId) return;

            const url = LAST_IMAGES_URL.replace('__ID__', studentId);
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (!data.has_images) return;
                    _reuseData = data;

                    const sourceLabel = data.source === 'assessment' ? 'avaliação com IA' : 'medição corporal';
                    bannerDate.textContent = `Última ${sourceLabel} em ${data.date}`;
                    banner.classList.remove('hidden');

                    // Montar preview das imagens disponíveis no banner (principais + extras)
                    const labels = { front: 'Frontal', side_right: 'Lat. D', side_left: 'Lat. E', back: 'Costas' };
                    previewRow.innerHTML = '';
                    let hasAny = false;
                    Object.entries(data.images).forEach(([slot, imgUrl]) => {
                        if (!imgUrl) return;
                        hasAny = true;
                        const div = document.createElement('div');
                        div.className = 'relative rounded-lg overflow-hidden border border-gray-600';
                        div.innerHTML = `<img src="${imgUrl}" alt="${labels[slot]}" class="w-full object-cover" style="aspect-ratio:16/9">
                            <div class="absolute bottom-0 inset-x-0 bg-black/60 text-white text-xs text-center py-1">${labels[slot]}</div>`;
                        previewRow.appendChild(div);
                    });
                    (data.extras || []).forEach((imgUrl, i) => {
                        hasAny = true;
                        const div = document.createElement('div');
                        div.className = 'relative rounded-lg overflow-hidden border border-gray-600';
                        div.innerHTML = `<img src="${imgUrl}" alt="Extra ${i+1}" class="w-full object-cover" style="aspect-ratio:16/9">
                            <div class="absolute bottom-0 inset-x-0 bg-black/60 text-white text-xs text-center py-1">Extra ${i+1}</div>`;
                        previewRow.appendChild(div);
                    });
                    if (hasAny) previewRow.classList.remove('hidden');
                })
                .catch(() => {});
        });

        btnApply.addEventListener('click', function() {
            if (!_reuseData) return;
            applyReuseImages(_reuseData);
            banner.classList.add('hidden');
        });

        btnDismiss.addEventListener('click', function() {
            banner.classList.add('hidden');
            clearAllReuse();
        });
    }

    // ===================== INIT =====================
    document.addEventListener('DOMContentLoaded', () => {
        initLoadingSystem();
        setupPhotoIndicators();
        setupExtraPhotosPreview();
        setupReuseSystem();
    });
</script>
@endsection
