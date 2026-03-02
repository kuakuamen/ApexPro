@extends('admin.layout')

@section('admin-content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $user->name }}</h1>
    <div>
        <a href="{{ route('admin.personals.edit', $user->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('admin.personals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Informações Pessoais</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Nome</p>
                        <p class="h6">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Email</p>
                        <p class="h6">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Telefone</p>
                        <p class="h6">{{ $user->phone ?? 'Não informado' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Profissão</p>
                        <p class="h6">{{ $user->profession ?? 'Não informada' }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Data de Nascimento</p>
                        <p class="h6">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'Não informada' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Gênero</p>
                        <p class="h6">
                            @if ($user->gender === 'M') Masculino
                            @elseif ($user->gender === 'F') Feminino
                            @else Não informado
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Status e Acesso</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Status da Conta</p>
                        <div>
                            @if ($user->is_active)
                                <span class="badge badge-success badge-lg">Ativo</span>
                            @else
                                <span class="badge badge-danger badge-lg">Inativo</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Role/Tipo</p>
                        <p class="h6">{{ $user->role }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.personals.toggle', $user->id) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}" 
                            onclick="return confirm('Tem certeza?')">
                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                        {{ $user->is_active ? 'Desativar' : 'Ativar' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Licença -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Licença</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.personals.license', $user->id) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="form-group mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="licenseActive" name="license_active" 
                                   value="1" {{ $user->license_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="licenseActive">
                                Licença Ativa
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="licenseExpires">Data de Expiração da Licença</label>
                        <input type="date" class="form-control" id="licenseExpires" name="license_expires_at" 
                               value="{{ $user->license_expires_at ? $user->license_expires_at->format('Y-m-d') : '' }}">
                        <small class="form-text text-muted">
                            @if ($user->license_expires_at)
                                Expira em: {{ $user->license_expires_at->format('d/m/Y') }}
                                @if ($user->license_expires_at->isPast())
                                    <span class="badge badge-danger">Expirada</span>
                                @endif
                            @endif
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">Salvar Licença</button>
                </form>
            </div>
        </div>

        <!-- Notas Administrativas -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Notas Administrativas</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.personals.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <textarea class="form-control" name="admin_notes" rows="4">{{ $user->admin_notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Notas</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Direito -->
    <div class="col-md-4">
        <!-- Alunos do Personal -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Alunos</h5>
            </div>
            <div class="card-body text-center">
                <h2 class="text-primary">{{ $studentCount }}</h2>
                <p class="text-muted">Alunos vinculados</p>
                <a href="{{ route('admin.personals.students', $user->id) }}" class="btn btn-sm btn-outline-primary">
                    Ver Alunos
                </a>
            </div>
        </div>

        <!-- Datas Importantes -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Histórico</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-1">Cadastrado em</p>
                <p class="h6">{{ $user->created_at->format('d/m/Y H:i') }}</p>

                <p class="text-muted mb-1 mt-3">Atualizado em</p>
                <p class="h6">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Ações -->
        <div class="card border-danger">
            <div class="card-header bg-light border-danger">
                <h5 class="mb-0">Ações Perigosas</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.personals.delete', $user->id) }}" 
                      onsubmit="return confirm('ATENÇÃO: Esta ação é irreversível! Este personal e todos seus dados serão deletados. Tem certeza?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Deletar Personal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
