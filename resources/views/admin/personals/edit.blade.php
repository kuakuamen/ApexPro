@extends('admin.layout')

@section('admin-content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Personal - {{ $user->name }}</h1>
    <a href="{{ route('admin.personals.show', $user->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.personals.update', $user->id) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Nome -->
                    <div class="form-group mb-3">
                        <label for="name">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- CPF -->
                    <div class="form-group mb-3">
                        <label for="cpf">CPF <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                               id="cpf" name="cpf" value="{{ old('cpf', $user->cpf) }}" required placeholder="000.000.000-00">
                        @error('cpf')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data de Nascimento -->
                    <div class="form-group mb-3">
                        <label for="birth_date">Data de Nascimento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                               id="birth_date" name="birth_date" 
                               value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}" required>
                        @error('birth_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gênero -->
                    <div class="form-group mb-3">
                        <label for="gender">Gênero <span class="text-danger">*</span></label>
                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                            <option value="">Selecione</option>
                            <option value="Masculino" {{ old('gender', $user->gender) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Feminino" {{ old('gender', $user->gender) == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                            <option value="Outro" {{ old('gender', $user->gender) == 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Endereço -->
                    <div class="form-group mb-3">
                        <label for="address">Endereço</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                               id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="Rua, número, bairro...">
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Telefone -->
                    <div class="form-group mb-3">
                        <label for="phone">Telefone</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="(00) 00000-0000">
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Profissão -->
                    <div class="form-group mb-3">
                        <label for="profession">Profissão</label>
                        <input type="text" class="form-control @error('profession') is-invalid @enderror" 
                               id="profession" name="profession" value="{{ old('profession', $user->profession) }}" 
                               placeholder="Ex: Personal Trainer">
                        @error('profession')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data de Expiração da Licença -->
                    <div class="form-group mb-3">
                        <label for="license_expires_at">Data de Expiração da Licença</label>
                        <input type="date" class="form-control @error('license_expires_at') is-invalid @enderror" 
                               id="license_expires_at" name="license_expires_at" 
                               value="{{ old('license_expires_at', $user->license_expires_at ? $user->license_expires_at->format('Y-m-d') : '') }}">
                        @error('license_expires_at')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notas Administrativas -->
                    <div class="form-group mb-3">
                        <label for="admin_notes">Notas Administrativas</label>
                        <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                  id="admin_notes" name="admin_notes" rows="3">{{ old('admin_notes', $user->admin_notes) }}</textarea>
                        <small class="form-text text-muted">Adicione observações sobre este personal se necessário.</small>
                        @error('admin_notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informações de Conta (Somente Leitura) -->
                    <hr>
                    <div class="alert alert-info">
                        <small><strong>Informações da Conta:</strong> ID: {{ $user->id }} | Criado em: {{ $user->created_at->format('d/m/Y H:i') }} | Atualizado em: {{ $user->updated_at->format('d/m/Y H:i') }}</small>
                    </div>

                    <!-- Botões -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                        <a href="{{ route('admin.personals.show', $user->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
