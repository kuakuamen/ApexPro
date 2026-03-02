@extends('admin.layout')

@section('admin-content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Logs do Sistema</h1>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Logs do sistema em desenvolvimento. Será implementado com banco de dados dedicado.
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Atividades Recentes</h5>
            </div>
            <div class="card-body text-center text-muted">
                <p>Nenhuma atividade registrada ou logs não inicializados.</p>
                <small>Implemente um sistema de logging para rastrear atividades administrativas.</small>
            </div>
        </div>
    </div>
</div>
@endsection
