@extends('admin.layout')

@section('admin-content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Todos os Usuários</h1>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline" style="gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou email" 
                   value="{{ request('search') }}">

            <select name="role" class="form-control">
                <option value="">-- Todos os Tipos --</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="personal" {{ request('role') === 'personal' ? 'selected' : '' }}>Personal</option>
                <option value="nutri" {{ request('role') === 'nutri' ? 'selected' : '' }}>Nutricionista</option>
                <option value="aluno" {{ request('role') === 'aluno' ? 'selected' : '' }}>Aluno</option>
            </select>

            <select name="status" class="form-control">
                <option value="">-- Todos os Status --</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativos</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="btn btn-outline-primary">
                <i class="fas fa-search"></i> Filtrar
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-redo"></i> Limpar
            </a>
        </form>
    </div>
</div>

<!-- Tabela de Usuários -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Criado em</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @switch($user->role)
                                @case('admin')
                                    <span class="badge badge-danger">Admin</span>
                                    @break
                                @case('personal')
                                    <span class="badge badge-primary">Personal</span>
                                    @break
                                @case('nutri')
                                    <span class="badge badge-success">Nutricionista</span>
                                    @break
                                @case('aluno')
                                    <span class="badge badge-info">Aluno</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge badge-success">Ativo</span>
                            @else
                                <span class="badge badge-danger">Inativo</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Nenhum usuário encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
