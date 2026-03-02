@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Cabeçalho -->
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white">Cadastrar Novo Aluno</h1>
            <p class="mt-1 text-gray-400">Crie o acesso para seu aluno.</p>
        </div>
        
        <div class="p-6">
    
    <div class="p-6">
        <form action="{{ route('personal.students.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nome Completo</label>
                    <input type="text" name="name" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Telefone (WhatsApp)</label>
                    <input type="text" name="phone" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="(00) 00000-0000">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Data de Nascimento <span class="text-red-400">*</span></label>
                    <input type="date" name="birth_date" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Gênero <span class="text-red-400">*</span></label>
                    <select name="gender" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                        <option value="" class="bg-gray-700">Selecione...</option>
                        <option value="M" class="bg-gray-700">Masculino</option>
                        <option value="F" class="bg-gray-700">Feminino</option>
                        <option value="O" class="bg-gray-700">Outro</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Senha Inicial <span class="text-red-400">*</span></label>
                    <input type="password" name="password" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required minlength="6">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                <a href="{{ route('personal.dashboard') }}" class="inline-flex items-center px-6 py-3 border border-gray-600 rounded-lg text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500 transition-colors shadow-lg shadow-indigo-500/25">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Salvar Aluno
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
