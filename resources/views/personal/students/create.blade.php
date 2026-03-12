@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white">Cadastrar Novo Aluno</h1>
            <p class="mt-1 text-gray-400">Crie o acesso para seu aluno.</p>
        </div>
        
        @php
            $activeTab = old('_active_tab', 'dados');
        @endphp
        <div class="p-6">
            <form action="{{ route('personal.students.store') }}" method="POST" id="student-create-form">
                @csrf

                <div class="mb-6">
                    <div class="inline-flex w-full sm:w-auto rounded-xl bg-gray-800/40 p-1 border border-gray-700">
                        <button type="button" id="tab-dados" class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $activeTab === 'dados' ? 'bg-gray-700/70 text-white' : 'text-gray-300 hover:text-white' }}">
                            Dados
                        </button>
                        <button type="button" id="tab-endereco" class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $activeTab === 'endereco' ? 'bg-gray-700/70 text-white' : 'text-gray-300 hover:text-white' }}">
                            Endereço
                        </button>
                    </div>
                    <input type="hidden" name="_active_tab" id="_active_tab" value="{{ $activeTab }}">
                </div>

                <div id="tab-panel-dados" class="{{ $activeTab === 'dados' ? '' : 'hidden' }}">
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Senha Inicial <span class="text-red-400">*</span></label>
                            <input type="password" name="password" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required minlength="6">
                            @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                    </div>
                </div>

                <div id="tab-panel-endereco" class="{{ $activeTab === 'endereco' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">CEP <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <input type="text" name="address_cep" id="address_cep" value="{{ old('address_cep') }}" inputmode="numeric" autocomplete="postal-code" placeholder="00000-000" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 pr-10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required pattern="\d{5}-\d{3}">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <div id="cep_loading" class="hidden h-4 w-4 border-2 border-gray-300/30 border-t-gray-200 rounded-full animate-spin"></div>
                                </div>
                            </div>
                            <p id="cep_error" class="hidden text-red-400 text-xs mt-1"></p>
                            @error('address_cep') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Estado <span class="text-red-400">*</span></label>
                            <input type="text" name="address_state" id="address_state" value="{{ old('address_state') }}" maxlength="2" autocomplete="address-level1" placeholder="UF" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors uppercase" required>
                            @error('address_state') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Cidade <span class="text-red-400">*</span></label>
                            <input type="text" name="address_city" id="address_city" value="{{ old('address_city') }}" autocomplete="address-level2" placeholder="Cidade" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            @error('address_city') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Bairro <span class="text-red-400">*</span></label>
                            <input type="text" name="address_neighborhood" id="address_neighborhood" value="{{ old('address_neighborhood') }}" autocomplete="address-level3" placeholder="Bairro" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            @error('address_neighborhood') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Rua <span class="text-red-400">*</span></label>
                            <input type="text" name="address_street" id="address_street" value="{{ old('address_street') }}" autocomplete="address-line1" placeholder="Rua" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            @error('address_street') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Número <span class="text-red-400">*</span></label>
                            <input type="text" name="address_number" id="address_number" value="{{ old('address_number') }}" autocomplete="address-line2" placeholder="Número" class="block w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            @error('address_number') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
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
</div>

<script>
function setStudentTab(tab) {
    var tabDados = document.getElementById('tab-dados');
    var tabEndereco = document.getElementById('tab-endereco');
    var panelDados = document.getElementById('tab-panel-dados');
    var panelEndereco = document.getElementById('tab-panel-endereco');
    var activeTabEl = document.getElementById('_active_tab');

    if (!tabDados || !tabEndereco || !panelDados || !panelEndereco || !activeTabEl) {
        return;
    }

    activeTabEl.value = tab;

    if (tab === 'endereco') {
        panelDados.classList.add('hidden');
        panelEndereco.classList.remove('hidden');
        tabDados.classList.remove('bg-gray-700/70', 'text-white');
        tabDados.classList.add('text-gray-300');
        tabEndereco.classList.add('bg-gray-700/70', 'text-white');
        tabEndereco.classList.remove('text-gray-300');
        return;
    }

    panelEndereco.classList.add('hidden');
    panelDados.classList.remove('hidden');
    tabEndereco.classList.remove('bg-gray-700/70', 'text-white');
    tabEndereco.classList.add('text-gray-300');
    tabDados.classList.add('bg-gray-700/70', 'text-white');
    tabDados.classList.remove('text-gray-300');
}

function formatCep(value) {
    var digits = String(value || '').replace(/\D/g, '').slice(0, 8);
    if (digits.length <= 5) return digits;
    return digits.slice(0, 5) + '-' + digits.slice(5);
}

function setCepError(message) {
    var el = document.getElementById('cep_error');
    if (!el) return;
    if (!message) {
        el.classList.add('hidden');
        el.textContent = '';
        return;
    }
    el.textContent = message;
    el.classList.remove('hidden');
}

function setCepLoading(isLoading) {
    var el = document.getElementById('cep_loading');
    if (!el) return;
    if (isLoading) {
        el.classList.remove('hidden');
        return;
    }
    el.classList.add('hidden');
}

function toUpperUf(value) {
    return String(value || '').toUpperCase().slice(0, 2);
}

function fetchCep(digits) {
    setCepError('');
    setCepLoading(true);

    return fetch('https://viacep.com.br/ws/' + digits + '/json/', { headers: { 'Accept': 'application/json' } })
        .then(function (response) {
            if (!response.ok) throw new Error('http_error');
            return response.json();
        })
        .then(function (data) {
            if (data && data.erro) {
                setCepError('CEP não encontrado.');
                return;
            }

            var ufEl = document.getElementById('address_state');
            var cityEl = document.getElementById('address_city');
            var streetEl = document.getElementById('address_street');
            var neighborhoodEl = document.getElementById('address_neighborhood');

            if (ufEl) ufEl.value = toUpperUf(data.uf);
            if (cityEl) cityEl.value = String(data.localidade || '');
            if (streetEl) streetEl.value = String(data.logradouro || '');
            if (neighborhoodEl) neighborhoodEl.value = String(data.bairro || '');
        })
        .catch(function () {
            setCepError('Erro ao consultar CEP. Tente novamente.');
        })
        .finally(function () {
            setCepLoading(false);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    var tabDados = document.getElementById('tab-dados');
    var tabEndereco = document.getElementById('tab-endereco');
    var activeTabEl = document.getElementById('_active_tab');

    if (tabDados) tabDados.addEventListener('click', function () { setStudentTab('dados'); });
    if (tabEndereco) tabEndereco.addEventListener('click', function () { setStudentTab('endereco'); });
    if (activeTabEl && (activeTabEl.value === 'endereco' || activeTabEl.value === 'dados')) {
        setStudentTab(activeTabEl.value);
    } else {
        setStudentTab('dados');
    }

    var cepEl = document.getElementById('address_cep');
    if (cepEl) {
        cepEl.value = formatCep(cepEl.value);
        cepEl.addEventListener('input', function (e) {
            var masked = formatCep(e.target.value);
            if (masked !== e.target.value) e.target.value = masked;
            setCepError('');

            var digits = masked.replace(/\D/g, '');
            if (digits.length === 8) {
                fetchCep(digits);
            }
        });
    }

    var ufEl = document.getElementById('address_state');
    if (ufEl) {
        ufEl.addEventListener('input', function (e) {
            e.target.value = toUpperUf(e.target.value);
        });
    }

    var form = document.getElementById('student-create-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (form.checkValidity()) return;
            e.preventDefault();
            var firstInvalid = form.querySelector(':invalid');
            if (!firstInvalid) return;

            var panelEndereco = document.getElementById('tab-panel-endereco');
            if (panelEndereco && panelEndereco.contains(firstInvalid)) {
                setStudentTab('endereco');
            } else {
                setStudentTab('dados');
            }

            setTimeout(function () {
                form.reportValidity();
                try { firstInvalid.focus(); } catch (err) {}
            }, 0);
        });
    }
});

var cpfEl = document.getElementById('cpf_input');
if (cpfEl) {
    cpfEl.addEventListener('input', function (e) {
        var value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        if (value.length > 9) {
            value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, '$1.$2.$3-$4');
        } else if (value.length > 6) {
            value = value.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
        } else if (value.length > 3) {
            value = value.replace(/^(\d{3})(\d{0,3}).*/, '$1.$2');
        }
        e.target.value = value;
    });
}
</script>
@endsection
