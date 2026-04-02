@extends('layouts.app')
@section('title', 'Meu Perfil')

@section('content')
<div class="py-6 space-y-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        @if($user->profile_photo_url)
            <img src="{{ $user->profile_photo_url }}" alt="Foto de perfil de {{ $user->name }}" class="h-16 w-16 rounded-2xl object-cover border border-cyan-400/30 shadow-lg shadow-cyan-950/30">
        @else
            <div class="h-16 w-16 rounded-2xl bg-cyan-500/15 border border-cyan-400/30 flex items-center justify-center text-cyan-200 font-bold text-2xl select-none">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h1 class="text-2xl font-extrabold text-white">{{ $user->name }}</h1>
            <p class="text-sm text-gray-400">{{ $user->profession ?? 'Personal Trainer' }} @if($user->cref) &middot; <span class="text-gray-500">{{ $user->cref }}</span>@endif</p>
        </div>
    </div>

    <form method="POST" action="{{ route('personal.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-6 space-y-5">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M3 19h18M5 7h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2zm3 2a2 2 0 100 4 2 2 0 000-4zm8 5l-2.5-3-2 2.5L10 12l-3 4h9z"/></svg>
                Foto de Perfil
            </h2>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                <div class="flex-shrink-0">
                    @if($user->profile_photo_url)
                        <img id="profile-photo-preview" src="{{ $user->profile_photo_url }}" alt="Pré-visualização da foto de perfil" class="h-28 w-28 rounded-2xl object-cover border border-white/10 bg-gray-900/60">
                    @else
                        <div id="profile-photo-placeholder" class="h-28 w-28 rounded-2xl bg-cyan-500/15 border border-cyan-400/30 flex items-center justify-center text-cyan-200 font-bold text-4xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <img id="profile-photo-preview" src="" alt="Pré-visualização da foto de perfil" class="hidden h-28 w-28 rounded-2xl object-cover border border-white/10 bg-gray-900/60">
                    @endif
                </div>

                <div class="flex-1 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Selecionar foto</label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                            class="block w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-sm text-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-teal-600 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-teal-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('profile_photo') border-red-500 @enderror">
                        <p class="mt-2 text-xs text-gray-500">PNG, JPG ou WEBP com até 5 MB.</p>
                        @error('profile_photo')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    @if($user->profile_photo_url)
                        <label class="inline-flex items-center gap-2 text-sm text-gray-300">
                            <input type="checkbox" name="remove_profile_photo" value="1" class="rounded border-white/10 bg-gray-900/60 text-teal-500 focus:ring-teal-500">
                            Remover foto atual
                        </label>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Card 1: Dados Pessoais ──────────────────────────────────────── --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-6 space-y-5">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Dados Pessoais
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Nome --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Nome completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">E-mail *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Telefone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Telefone</label>
                    <input type="text" name="phone" id="phone_input" value="{{ old('phone', $user->phone) }}" placeholder="(00) 00000-0000"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- CPF --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">CPF</label>
                    <input type="text" value="{{ $user->cpf ?? '—' }}" disabled
                        class="w-full rounded-lg bg-gray-900/30 border border-white/5 px-4 py-2.5 text-gray-500 cursor-not-allowed">
                    <input type="hidden" name="cpf" value="{{ $user->cpf }}">
                </div>

                {{-- Gênero --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Gênero</label>
                    <select name="gender" class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('gender') border-red-500 @enderror">
                        @php
                            $g = strtolower(old('gender', $user->gender ?? ''));
                            $gNorm = match($g) { 'm','masculino' => 'M', 'f','feminino' => 'F', 'o','outro' => 'O', default => '' };
                        @endphp
                        <option value="">Selecione</option>
                        <option value="M" @selected($gNorm === 'M')>Masculino</option>
                        <option value="F" @selected($gNorm === 'F')>Feminino</option>
                        <option value="O" @selected($gNorm === 'O')>Outro</option>
                    </select>
                    @error('gender')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Data de Nascimento --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Data de Nascimento</label>
                    <input type="text" value="{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') : '—' }}" disabled
                        class="w-full rounded-lg bg-gray-900/30 border border-white/5 px-4 py-2.5 text-gray-500 cursor-not-allowed">
                    <input type="hidden" name="birth_date" value="{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '' }}">
                </div>
            </div>
        </div>

        {{-- ── Card 2: Dados Profissionais ─────────────────────────────────── --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-6 space-y-5">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Dados Profissionais
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Profissão</label>
                    <select name="profession" class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                        <option value="">Selecione</option>
                        @foreach(['Personal Trainer','Educador Físico','Nutricionista','Fisioterapeuta','Outro'] as $p)
                            <option value="{{ $p }}" @selected(old('profession', $user->profession) === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">CREF / CRN</label>
                    <input type="text" value="{{ $user->cref ?? '—' }}" disabled
                        class="w-full rounded-lg bg-gray-900/30 border border-white/5 px-4 py-2.5 text-gray-500 cursor-not-allowed">
                    <input type="hidden" name="cref" value="{{ $user->cref }}">
                </div>
            </div>
        </div>

        {{-- ── Card 3: Endereço ─────────────────────────────────────────────── --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md p-6 space-y-5">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Endereço
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-6 gap-4">
                {{-- CEP --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">CEP</label>
                    <div class="relative">
                        <input type="text" name="address_cep" id="cep_input" value="{{ old('address_cep', $user->address_cep) }}" placeholder="00000-000" maxlength="9"
                            class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 pr-10 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                        <span id="cep_loading" class="hidden absolute right-3 top-3 text-gray-400">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </div>
                </div>

                {{-- Rua --}}
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Rua / Avenida</label>
                    <input type="text" name="address_street" id="address_street" value="{{ old('address_street', $user->address_street) }}"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                </div>

                {{-- Número --}}
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Número</label>
                    <input type="text" name="address_number" id="address_number" value="{{ old('address_number', $user->address_number) }}" maxlength="30"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                </div>

                {{-- Bairro --}}
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Bairro</label>
                    <input type="text" name="address_neighborhood" id="address_neighborhood" value="{{ old('address_neighborhood', $user->address_neighborhood) }}"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                </div>

                {{-- Cidade --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Cidade</label>
                    <input type="text" name="address_city" id="address_city" value="{{ old('address_city', $user->address_city) }}"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                </div>

                {{-- Estado --}}
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">UF</label>
                    <input type="text" name="address_state" id="address_state" value="{{ old('address_state', $user->address_state) }}" maxlength="2" placeholder="PR"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition uppercase">
                </div>
            </div>
        </div>

        {{-- ── Card 4: Alterar Senha ────────────────────────────────────────── --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/50 backdrop-blur-md overflow-hidden">
            <button type="button" id="toggle_password"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-white/5 transition-colors">
                <span class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    Alterar Senha
                </span>
                <svg id="toggle_password_icon" class="h-4 w-4 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="password_section" class="px-6 pb-6 space-y-4 border-t border-gray-700/50 pt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Nova Senha</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition @error('password') border-red-500 @enderror"
                        placeholder="Mínimo 8 caracteres">
                    @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full rounded-lg bg-gray-900/60 border border-white/10 px-4 py-2.5 text-white placeholder-gray-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition"
                        placeholder="Repita a nova senha">
                </div>
            </div>
        </div>

        {{-- Botão Salvar --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('personal.dashboard') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-600 bg-gray-700/50 hover:bg-gray-600/50 text-gray-300 px-5 py-2.5 text-sm font-medium transition-all">
                Voltar
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-teal-600 hover:bg-teal-500 text-white px-6 py-2.5 text-sm font-semibold shadow-lg shadow-teal-900/30 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    // ── Data de nascimento ────────────────────────────────────────────────────
    const birthYear  = document.getElementById('birth_year');
    const birthHidden = document.getElementById('birth_date_hidden');

    function updateBirth() {
        const d = birthDay.value, m = birthMonth.value, y = birthYear.value;
        birthHidden.value = (d && m && y) ? y + '-' + String(m).padStart(2,'0') + '-' + String(d).padStart(2,'0') : '';
    }

    if (birthHidden.value) {
        const p = birthHidden.value.split('-');
        if (p.length === 3) {
            birthYear.value  = p[0];
            birthMonth.value = parseInt(p[1]).toString();
            birthDay.value   = parseInt(p[2]).toString();
        }
    }
    [birthDay, birthMonth, birthYear].forEach(el => el.addEventListener('change', updateBirth));

    // ── CEP → ViaCEP ─────────────────────────────────────────────────────────
    const cepInput  = document.getElementById('cep_input');
    const cepLoader = document.getElementById('cep_loading');

    function maskCep(v) { return v.replace(/\D/g,'').substring(0,8).replace(/^(\d{5})(\d)/,'$1-$2'); }
    cepInput.addEventListener('input', function() { this.value = maskCep(this.value); });

    cepInput.addEventListener('blur', async function () {
        const raw = this.value.replace(/\D/g, '');
        if (raw.length !== 8) return;
        cepLoader.classList.remove('hidden');
        try {
            const res  = await fetch('https://viacep.com.br/ws/' + raw + '/json/');
            const data = await res.json();
            if (!data.erro) {
                document.getElementById('address_street').value       = data.logradouro || '';
                document.getElementById('address_neighborhood').value = data.bairro     || '';
                document.getElementById('address_city').value         = data.localidade || '';
                document.getElementById('address_state').value        = data.uf         || '';
                document.getElementById('address_number').focus();
            }
        } catch (e) {}
        cepLoader.classList.add('hidden');
    });

    // ── CPF mask ──────────────────────────────────────────────────────────────
    const cpfField = document.getElementById('cpf_field');
    if (cpfField) {
        cpfField.addEventListener('input', function () {
            let v = this.value.replace(/\D/g,'').substring(0,11);
            v = v.replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2');
            this.value = v;
        });
    }

    // ── Telefone mask ─────────────────────────────────────────────────────────
    const phoneField = document.getElementById('phone_input');
    if (phoneField) {
        phoneField.addEventListener('input', function () {
            let v = this.value.replace(/\D/g,'').substring(0,11);
            if (v.length > 6) v = v.replace(/^(\d{2})(\d{5})(\d{0,4})/,'($1) $2-$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d*)/,'($1) $2');
            this.value = v;
        });
    }

    // ── Toggle senha colapsável ───────────────────────────────────────────────
    const profilePhotoInput = document.getElementById('profile_photo');
    const profilePhotoPreview = document.getElementById('profile-photo-preview');
    const profilePhotoPlaceholder = document.getElementById('profile-photo-placeholder');
    if (profilePhotoInput && profilePhotoPreview) {
        profilePhotoInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            profilePhotoPreview.src = URL.createObjectURL(file);
            profilePhotoPreview.classList.remove('hidden');

            if (profilePhotoPlaceholder) {
                profilePhotoPlaceholder.classList.add('hidden');
            }
        });
    }

    document.getElementById('toggle_password').addEventListener('click', function () {
        const section = document.getElementById('password_section');
        const icon    = document.getElementById('toggle_password_icon');
        section.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    });

    // Abrir seção se houver erro de validação na senha
    @error('password') document.getElementById('password_section').classList.remove('hidden');
    document.getElementById('toggle_password_icon').classList.add('rotate-180'); @enderror
})();
</script>

@endsection
