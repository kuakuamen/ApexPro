@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
        <div class="mt-8 text-2xl font-bold text-gray-900">
            Nova Avaliação Física com IA
        </div>
        <div class="mt-4 text-gray-500">
            Envie as fotos do aluno (Frente, Lado e Costas) para que nossa Inteligência Artificial analise a postura e sugira os pontos de correção.
        </div>
    </div>

    <div class="p-6 sm:px-20 bg-gray-50 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-8">
        <form action="{{ route('assessments.store') }}" method="POST" enctype="multipart/form-data" class="col-span-2">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Foto Frente -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto de Frente</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para enviar</span></p>
                                <p class="text-xs text-gray-500">PNG, JPG (MAX. 5MB)</p>
                            </div>
                            <input type="file" name="front_image" class="hidden" required />
                        </label>
                    </div>
                </div>

                <!-- Foto Lado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto de Lado (Perfil)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para enviar</span></p>
                                <p class="text-xs text-gray-500">PNG, JPG (MAX. 5MB)</p>
                            </div>
                            <input type="file" name="side_image" class="hidden" required />
                        </label>
                    </div>
                </div>

                <!-- Foto Costas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto de Costas</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para enviar</span></p>
                                <p class="text-xs text-gray-500">PNG, JPG (MAX. 5MB)</p>
                            </div>
                            <input type="file" name="back_image" class="hidden" required />
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Observações Adicionais (Opcional)</label>
                <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-primary focus:border-primary mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2" placeholder="Ex: Sinto dores na lombar ao correr..."></textarea>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    Analisar com IA
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
