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
                    <input type="text" name="name" value="{{ old('name') }}" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                    @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                    @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Telefone (WhatsApp)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="(00) 00000-0000">
                    @error('phone') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Data de Nascimento <span class="text-red-400">*</span></label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                    @error('birth_date') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">CPF <span class="text-red-400">*</span></label>
                    <input type="text" name="cpf" id="cpf_input" value="{{ old('cpf') }}" maxlength="14" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="000.000.000-00" required>
                    @error('cpf') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Endereço</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Rua, Número, Bairro...">
                    @error('address') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Gênero <span class="text-red-400">*</span></label>
                    <select name="gender" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                        <option value="" class="bg-gray-700">Selecione...</option>
                        <option value="M" class="bg-gray-700" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" class="bg-gray-700" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino</option>
                        <option value="O" class="bg-gray-700" {{ old('gender') == 'O' ? 'selected' : '' }}>Outro</option>
                    </select>
                    @error('gender') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Senha Inicial <span class="text-red-400">*</span></label>
                    <input type="password" name="password" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required minlength="6">
                    @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">Recorrência da Avaliação Física <span class="text-red-400">*</span></label>
                <select name="assessment_frequency" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                    <option value="15" class="bg-gray-700" {{ old('assessment_frequency') == 15 ? 'selected' : '' }}>A cada 15 dias</option>
                    <option value="30" class="bg-gray-700" {{ old('assessment_frequency', 30) == 30 ? 'selected' : '' }}>A cada 30 dias (Padrão)</option>
                    <option value="60" class="bg-gray-700" {{ old('assessment_frequency') == 60 ? 'selected' : '' }}>A cada 60 dias</option>
                    <option value="90" class="bg-gray-700" {{ old('assessment_frequency') == 90 ? 'selected' : '' }}>A cada 90 dias</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Define quando o sistema alertará sobre avaliação atrasada.</p>
                @error('assessment_frequency') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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

<script>
document.getElementById('cpf_input').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
    
    if (value.length > 11) value = value.slice(0, 11); // Limita a 11 dígitos
    
    // Aplica a máscara: 000.000.000-00
    if (value.length > 9) {
        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, '$1.$2.$3-$4');
    } else if (value.length > 6) {
        value = value.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
    } else if (value.length > 3) {
        value = value.replace(/^(\d{3})(\d{0,3}).*/, '$1.$2');
    }
    
    e.target.value = value;
});
</script>
@endsection
