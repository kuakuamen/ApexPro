@extends('admin.layout')

@section('admin-content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Alunos de {{ $user->name }}</h1>
    <a href="{{ route('admin.personals.show', $user->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<!-- Informações do Personal -->
<div class="alert alert-info">
    <strong>Personal:</strong> {{ $user->name }} | 
    <strong>Email:</strong> {{ $user->email }} | 
    <strong>Total de Alunos:</strong> {{ $students->total() }}
</div>

<!-- Tabela de Alunos -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Gênero</th>
                    <th>Status</th>
                    <th>Vinculado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>
                            <strong>{{ $student->name }}</strong>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->phone ?? '-' }}</td>
                        <td>
                            @if ($student->gender === 'M')
                                <i class="fas fa-mars text-primary"></i> Masculino
                            @elseif ($student->gender === 'F')
                                <i class="fas fa-venus text-danger"></i> Feminino
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($student->is_active)
                                <span class="badge badge-success">Ativo</span>
                            @else
                                <span class="badge badge-danger">Inativo</span>
                            @endif
                        </td>
                        <td>{{ $student->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="#" class="btn btn-outline-primary" title="Ver detalhes" disabled>
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Nenhum aluno vinculado a este personal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="card-footer">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection
