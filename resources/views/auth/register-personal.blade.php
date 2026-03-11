@extends('layouts.guest_plans')

@section('content')
<div class="py-12 sm:py-16 bg-background-main min-h-screen flex items-center justify-center">
    <div class="w-full max-w-3xl px-6 lg:px-8">
        
        <!-- Progress Steps (Redesigned) -->
        <div class="mb-10 mx-auto max-w-4xl">
            <nav aria-label="Progress">
                <ol role="list" class="divide-y divide-white/10 rounded-2xl border border-white/10 bg-background-card md:flex md:divide-y-0">
                    <li class="relative md:flex md:flex-1">
                        <a href="#" class="group flex w-full items-center">
                            <span class="flex items-center px-6 py-4 text-sm font-medium">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-500 group-hover:bg-primary-600">
                                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm font-medium text-text-primary">Planos</span>
                            </span>
                        </a>
                        <!-- Arrow separator for md screens -->
                        <div class="absolute right-0 top-0 hidden h-full w-5 md:block" aria-hidden="true">
                            <svg class="h-full w-full text-white/10" viewBox="0 0 22 80" fill="none" preserveAspectRatio="none">
                                <path d="M0 -2L20 40L0 82" vector-effect="non-scaling-stroke" stroke="currentcolor" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </li>

                    <li class="relative md:flex md:flex-1">
                        <a href="#" class="group flex w-full items-center">
                            <span class="flex items-center px-6 py-4 text-sm font-medium">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-500 group-hover:bg-primary-600">
                                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm font-medium text-text-primary">Pagamento</span>
                            </span>
                        </a>
                        <div class="absolute right-0 top-0 hidden h-full w-5 md:block" aria-hidden="true">
                            <svg class="h-full w-full text-white/10" viewBox="0 0 22 80" fill="none" preserveAspectRatio="none">
                                <path d="M0 -2L20 40L0 82" vector-effect="non-scaling-stroke" stroke="currentcolor" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </li>

                    <li class="relative md:flex md:flex-1">
                        <a href="#" class="group flex w-full items-center">
                            <span class="flex items-center px-6 py-4 text-sm font-medium">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 border-primary-500">
                                    <span class="text-primary-500">03</span>
                                </span>
                                <span class="ml-4 text-sm font-medium text-primary-500">Cadastro</span>
                            </span>
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-background-card rounded-2xl border border-white/5 p-8 sm:p-12 shadow-2xl backdrop-blur-sm">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Finalize seu Cadastro</h2>
                <p class="mt-2 text-sm text-text-tertiary">Preencha seus dados para acessar o plano <span class="text-primary-400 font-semibold">{{ $plan['name'] }}</span></p>
            </div>
            
            <form method="POST" action="{{ route('subscription.store') }}">
                @csrf
                
                <div class="space-y-8">
                    <!-- Section: Personal Info -->
                    <div>
                        <h3 class="text-lg font-medium text-text-primary border-b border-white/5 pb-2 mb-4">Dados Pessoais</h3>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium leading-6 text-text-secondary">Nome Completo <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <input type="text" name="name" id="name" required class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all placeholder:text-text-tertiary" placeholder="Seu nome completo" value="{{ old('name') }}">
                                    @error('name') <span class="text-status-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-medium leading-6 text-text-secondary">Email <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <input type="email" name="email" id="email" required class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all placeholder:text-text-tertiary" placeholder="seu@email.com" value="{{ old('email') }}">
                                    @error('email') <span class="text-status-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="cpf" class="block text-sm font-medium leading-6 text-text-secondary">CPF <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <input type="text" name="cpf" id="cpf" required placeholder="000.000.000-00" class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all placeholder:text-text-tertiary" value="{{ old('cpf') }}" x-mask="999.999.999-99">
                                    @error('cpf') <span class="text-status-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="birth_date" class="block text-sm font-medium leading-6 text-text-secondary">Data de Nascimento <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <input type="date" name="birth_date" id="birth_date" required class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all [color-scheme:dark]" value="{{ old('birth_date') }}">
                                    @error('birth_date') <span class="text-status-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium leading-6 text-text-secondary">Gênero <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <select name="gender" id="gender" required class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all [&>option]:bg-zinc-800">
                                        <option value="">Selecione</option>
                                        <option value="Masculino" {{ old('gender') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Feminino" {{ old('gender') == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                                        <option value="Outro" {{ old('gender') == 'Outro' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                    @error('gender') <span class="text-status-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium leading-6 text-text-secondary">Whatsapp <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <input type="text" name="phone" id="phone" required placeholder="(00) 00000-0000" class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all placeholder:text-text-tertiary" value="{{ old('phone') }}">
                                    @error('phone') <span class="text-status-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Professional Info -->
                    <div>
                        <h3 class="text-lg font-medium text-text-primary border-b border-white/5 pb-2 mb-4">Dados Profissionais</h3>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-sm font-medium leading-6 text-text-secondary">Endereço</label>
                                <div class="mt-1.5">
                                    <input type="text" name="address" id="address" class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all placeholder:text-text-tertiary" placeholder="Rua, Número, Bairro, Cidade - UF" value="{{ old('address') }}">
                                </div>
                            </div>

                            <div>
                                <label for="profession" class="block text-sm font-medium leading-6 text-text-secondary">Profissão</label>
                                <div class="mt-1.5">
                                    <input type="text" name="profession" id="profession" class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all placeholder:text-text-tertiary" placeholder="Ex: Personal Trainer" value="{{ old('profession') }}">
                                </div>
                            </div>
                            <div>
                                <label for="cref" class="block text-sm font-medium leading-6 text-text-secondary">CREF / CRN</label>
                                <div class="mt-1.5">
                                    <input type="text" name="cref" id="cref" class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all placeholder:text-text-tertiary" placeholder="Registro profissional" value="{{ old('cref') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Security -->
                    <div>
                        <h3 class="text-lg font-medium text-text-primary border-b border-white/5 pb-2 mb-4">Segurança</h3>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                            <div>
                                <label for="password" class="block text-sm font-medium leading-6 text-text-secondary">Senha <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <input type="password" name="password" id="password" required class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all">
                                    @error('password') <span class="text-status-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium leading-6 text-text-secondary">Confirmar Senha <span class="text-status-error">*</span></label>
                                <div class="mt-1.5">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required class="block w-full rounded-lg border-0 bg-white/5 px-4 py-3 text-text-primary shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-white/10">
                    <button type="submit" class="w-full rounded-xl bg-primary-500 px-4 py-3.5 text-center text-sm font-bold text-white shadow-lg shadow-primary-500/20 hover:bg-primary-600 hover:shadow-primary-500/40 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        Criar Conta e Acessar Painel
                    </button>
                    <p class="mt-4 text-center text-xs text-text-tertiary">Ao criar sua conta, você concorda com nossos Termos de Uso e Política de Privacidade.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes progress {
        0% { width: 0%; }
        100% { width: 100%; }
    }
    .animate-progress {
        animation: progress 1s ease-out forwards;
    }
</style>

<!-- Simple Mask Script -->
<script>
    function formatCPF(value) {
        return value.replace(/\D/g, '')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1-$2')
            .replace(/(-\d{2})\d+?$/, '$1');
    }

    function formatPhone(value) {
        return value.replace(/\D/g, '')
            .replace(/^(\d{2})(\d)/g, '($1) $2')
            .replace(/(\d)(\d{4})$/, '$1-$2');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        const phoneInput = document.getElementById('phone');

        if (cpfInput.value) {
            cpfInput.value = formatCPF(cpfInput.value);
        }

        if (phoneInput.value) {
            phoneInput.value = formatPhone(phoneInput.value);
        }

        cpfInput.addEventListener('input', function(e) {
            e.target.value = formatCPF(e.target.value);
        });

        phoneInput.addEventListener('input', function(e) {
            e.target.value = formatPhone(e.target.value);
        });
    });
</script>
@endsection
