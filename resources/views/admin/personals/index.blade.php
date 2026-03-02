@extends('admin.layout')

@section('admin-content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Gerenciamento de Personals</h1>
    <a href="{{ route('admin.personals.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Personal
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.personals.index') }}" class="form-inline" style="gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, email ou telefone" 
                   value="{{ request('search') }}">

            <select name="status" class="form-control">
                <option value="">-- Todos os Status --</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativos</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="btn btn-outline-primary">
                <i class="fas fa-search"></i> Filtrar
            </button>
            <a href="{{ route('admin.personals.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-redo"></i> Limpar
            </a>
        </form>
    </div>
</div>

<!-- Tabela de Personals -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Profissão</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($personals as $personal)
                    <tr>
                        <td>
                            <strong>{{ $personal->name }}</strong>
                        </td>
                        <td>{{ $personal->email }}</td>
                        <td>{{ $personal->phone ?? '-' }}</td>
                        <td>{{ $personal->profession ?? '-' }}</td>
                        <td>
                            @if ($personal->is_active)
                                <span class="badge badge-success">Ativo</span>
                            @else
                                <span class="badge badge-danger">Inativo</span>
                            @endif
                        </td>
                        <td>{{ $personal->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.personals.show', $personal->id) }}" 
                                   class="btn btn-outline-primary" title="Ver detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.personals.edit', $personal->id) }}" 
                                   class="btn btn-outline-secondary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.personals.toggle', $personal->id) }}" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Tem certeza que deseja {{ $personal->is_active ? 'desativar' : 'ativar' }} este personal?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-warning" 
                                            title="{{ $personal->is_active ? 'Desativar' : 'Ativar' }}">
                                        <i class="fas fa-{{ $personal->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.personals.delete', $personal->id) }}" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Tem certeza que deseja deletar este personal? Esta ação é irreversível!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Deletar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Nenhum personal encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($personals->hasPages())
        <div class="card-footer">
            {{ $personals->links() }}
        </div>
    @endif
</div>

<style>
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection
