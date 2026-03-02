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
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Foto Frente -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Vista Frontal</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm">
                                    <div class="space-y-3 text-center w-full">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex flex-col gap-2">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="openCamera('photo_front')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Câmera
                                            </button>
                                            <button type="button" class="w-full flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="openGallery('photo_front')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Galeria
                                            </button>
                                        </div>
                                        <div id="photo_front_status" class="hidden">
                                            <span class="inline-flex items-center gap-2 bg-green-500 text-white px-3 py-2 rounded-lg font-medium">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Foto adicionada
                                            </span>
                                        </div>
                                        <input id="photo_front" name="photo_front" type="file" class="sr-only" accept="image/*" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto Lado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Vista Lateral (Perfil)</label>
                                <div id="photo_side_container" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm">
                                    <div class="space-y-3 text-center w-full">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex flex-col gap-2">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="openCamera('photo_side')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Câmera
                                            </button>
                                            <button type="button" class="w-full flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="openGallery('photo_side')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Galeria
                                            </button>
                                        </div>
                                        <div id="photo_side_status" class="hidden">
                                            <span class="inline-flex items-center gap-2 bg-green-500 text-white px-3 py-2 rounded-lg font-medium">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Foto adicionada
                                            </span>
                                        </div>
                                        <input id="photo_side" name="photo_side" type="file" class="sr-only" accept="image/*" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Foto Costas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Vista Costas</label>
                                <div id="photo_back_container" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-600 border-dashed rounded-lg hover:border-indigo-400 transition-colors bg-gray-800/50 backdrop-blur-sm">
                                    <div class="space-y-3 text-center w-full">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex flex-col gap-2">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="openCamera('photo_back')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                Câmera
                                            </button>
                                            <button type="button" class="w-full flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors" onclick="openGallery('photo_back')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Galeria
                                            </button>
                                        </div>
                                        <div id="photo_back_status" class="hidden">
                                            <span class="inline-flex items-center gap-2 bg-green-500 text-white px-3 py-2 rounded-lg font-medium">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Foto adicionada
                                            </span>
                                        </div>
                                        <input id="photo_back" name="photo_back" type="file" class="sr-only" accept="image/*" required>
                                    </div>
                                </div>
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
                <p>© {{ date('Y') }} FitManager - Todos os direitos reservados</p>
            </div>
            <div class="text-sm text-gray-400">
                <p>Desenvolvido com ❤️ para personal trainers</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Sistema de abas
    function switchTab(tabName) {
        // Ocultar todos os tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remover active de todos os botões
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-gray-400', 'text-white');
            btn.classList.add('border-transparent', 'text-gray-400');
        });
        
        // Mostrar tab selecionado
        document.getElementById(tabName).classList.remove('hidden');
        
        // Marcar botão como ativo
        const btnId = 'btn-' + tabName;
        const activeBtn = document.getElementById(btnId);
        activeBtn.classList.add('active', 'border-gray-400', 'text-white');
        activeBtn.classList.remove('border-transparent', 'text-gray-400');
    }

    // Preview simples do nome do arquivo (opcional)
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', (e) => {
            const fileName = e.target.files[0]?.name;
            if(fileName) {
                const label = e.target.closest('div').querySelector('span');
                label.innerText = fileName;
                label.classList.add('text-green-400');
            }
        });
    });

    // Funções para abrir câmera ou galeria
    function openCamera(inputId) {
        const input = document.getElementById(inputId);
        input.setAttribute('capture', 'environment');
        input.click();
    }

    function openGallery(inputId) {
        const input = document.getElementById(inputId);
        input.removeAttribute('capture');
        input.click();
    }

    // Indicador visual de foto adicionada
    function setupPhotoIndicators() {
        const photos = [
            { input: 'photo_front', status: 'photo_front_status', container: 'photo_front_container' },
            { input: 'photo_side', status: 'photo_side_status', container: 'photo_side_container' },
            { input: 'photo_back', status: 'photo_back_status', container: 'photo_back_container' }
        ];

        photos.forEach(photo => {
            const input = document.getElementById(photo.input);
            const statusBadge = document.getElementById(photo.status);
            const container = document.getElementById(photo.container);

            const updateStatus = () => {
                if (input.files.length > 0) {
                    // Mostrar badge verde
                    statusBadge.classList.remove('hidden');
                    // Mudar cor da borda para verde
                    container.classList.remove('border-gray-600', 'hover:border-indigo-400');
                    container.classList.add('border-green-500', 'hover:border-green-400');
                } else {
                    // Ocultar badge
                    statusBadge.classList.add('hidden');
                    // Voltar cor normal
                    container.classList.add('border-gray-600', 'hover:border-indigo-400');
                    container.classList.remove('border-green-500', 'hover:border-green-400');
                }
            };

            input.addEventListener('change', updateStatus);
        });
    }

    setupPhotoIndicators();
</script>
@endsection
