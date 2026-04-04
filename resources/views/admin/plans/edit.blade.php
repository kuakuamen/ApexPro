@extends('admin.layout')

@section('title', 'Editar Plano — ' . $plan->name)
@section('page-title', 'Editar Plano')

@section('admin-content')
<style>
    .edit-card {
        background: linear-gradient(135deg, rgba(16,185,129,0.07) 0%, rgba(6,182,212,0.03) 100%);
        border: 1.5px solid rgba(16,185,129,0.2);
        border-radius: 18px;
        overflow: hidden;
        max-width: 680px;
    }
    .edit-card-header {
        background: rgba(16,185,129,0.1);
        border-bottom: 1px solid rgba(16,185,129,0.15);
        padding: 1.6rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .plan-color-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .edit-card-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #10b981;
        margin: 0;
    }
    .edit-card-body {
        padding: 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        font-weight: 600;
        color: #b0b8c1;
        font-size: 0.9rem;
        margin-bottom: 0.45rem;
        display: block;
    }
    .form-label span {
        font-weight: 400;
        color: #6b7280;
        font-size: 0.82rem;
    }
    .form-control {
        background: rgba(255,255,255,0.06);
        border: 1.5px solid rgba(16,185,129,0.2);
        border-radius: 10px;
        color: #e8eaed;
        padding: 0.8rem 1rem;
        font-size: 0.95rem;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus {
        background: rgba(255,255,255,0.09);
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
        color: #e8eaed;
        outline: none;
    }
    .form-control::placeholder {
        color: #4b5563;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 200px;
        font-family: inherit;
        line-height: 1.6;
    }
    .form-hint {
        font-size: 0.82rem;
        color: #6b7280;
        margin-top: 0.35rem;
    }
    .input-prefix-group {
        display: flex;
        align-items: stretch;
    }
    .input-prefix {
        background: rgba(16,185,129,0.1);
        border: 1.5px solid rgba(16,185,129,0.2);
        border-right: none;
        border-radius: 10px 0 0 10px;
        padding: 0.8rem 1rem;
        color: #10b981;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }
    .input-prefix + .form-control {
        border-radius: 0 10px 10px 0;
    }
    .edit-card-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid rgba(16,185,129,0.1);
        background: rgba(0,0,0,0.1);
        display: flex;
        gap: 0.8rem;
        align-items: center;
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-size: 0.95rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        transition: all 0.25s;
    }
    .btn-save:hover {
        background: linear-gradient(135deg, #059669 0%, #0891b2 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16,185,129,0.4);
    }
    .btn-cancel {
        background: rgba(107,114,128,0.12);
        border: 1px solid rgba(107,114,128,0.25);
        color: #9ca3af;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    .btn-cancel:hover {
        background: rgba(107,114,128,0.2);
        color: #e8eaed;
    }
    .plan-id-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(16,185,129,0.08);
        border: 1px solid rgba(16,185,129,0.15);
        border-radius: 20px;
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.25rem 0.7rem;
        font-family: monospace;
    }
    .row-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
    }
    @media(max-width:520px) {
        .row-2col { grid-template-columns: 1fr; }
        .edit-card-body { padding: 1.25rem; }
        .edit-card-footer { flex-direction: column; }
        .btn-save, .btn-cancel { width: 100%; justify-content: center; }
    }
</style>

<div class="mb-4">
    <a href="{{ route('admin.plans.index') }}" style="color:#10b981;text-decoration:none;font-size:0.9rem;display:inline-flex;align-items:center;gap:0.4rem;">
        <i class="fas fa-arrow-left"></i> Voltar para Planos
    </a>
</div>

<div class="edit-card">
    <div class="edit-card-header">
        <span class="plan-color-dot" style="background:{{ $plan->color ?? '#10b981' }};box-shadow:0 0 8px {{ $plan->color ?? '#10b981' }}55;"></span>
        <h2>Editar — {{ $plan->name }}</h2>
        <span class="plan-id-chip ms-auto">{{ $plan->plan_id }}</span>
    </div>

    <form method="POST" action="{{ route('admin.plans.update', $plan->plan_id) }}">
        @csrf @method('PUT')

        <div class="edit-card-body">

            <div class="form-group">
                <label class="form-label">Nome do Plano</label>
                <input type="text" name="name" class="form-control"
                    value="{{ old('name', $plan->name) }}"
                    placeholder="Ex: Starter" required maxlength="100">
                @error('name') <div class="form-hint" style="color:#f87171;">{{ $message }}</div> @enderror
            </div>

            <div class="row-2col">
                <div class="form-group">
                    <label class="form-label">Preço Mensal (R$)</label>
                    <div class="input-prefix-group">
                        <span class="input-prefix">R$</span>
                        <input type="number" name="price" class="form-control"
                            value="{{ old('price', $plan->price) }}"
                            step="0.01" min="0" required>
                    </div>
                    @error('price') <div class="form-hint" style="color:#f87171;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Máximo de Alunos</label>
                    <input type="number" name="max_students" class="form-control"
                        value="{{ old('max_students', $plan->max_students) }}"
                        min="1" required>
                    @error('max_students') <div class="form-hint" style="color:#f87171;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Funcionalidades
                    <span>— uma por linha</span>
                </label>
                <textarea name="features" class="form-control" required
                    placeholder="Até X alunos ativos&#10;Prescrição de treinos completa&#10;...">{{ old('features', implode("\n", $plan->features ?? [])) }}</textarea>
                <div class="form-hint">
                    <i class="fas fa-info-circle" style="color:#10b981;"></i>
                    Cada linha vira um item da lista de funcionalidades exibida para o usuário.
                </div>
                @error('features') <div class="form-hint" style="color:#f87171;">{{ $message }}</div> @enderror
            </div>

        </div>

        <div class="edit-card-footer">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
            <a href="{{ route('admin.plans.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
