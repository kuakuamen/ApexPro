@extends('admin.layout')

@section('page-title', $user->name)

@section('admin-content')
<style>
    .info-label { font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;margin-bottom:4px; }
    .info-value { font-size:0.95rem;color:#e8eaed;font-weight:500; }
    .section-card {
        background: rgba(16,185,129,0.03);
        border: 1px solid rgba(16,185,129,0.12);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .section-title {
        font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;
        color:#10b981;margin-bottom:1.25rem;padding-bottom:0.75rem;
        border-bottom:1px solid rgba(16,185,129,0.12);
    }
    .status-pill {
        display:inline-flex;align-items:center;gap:6px;
        font-size:0.82rem;font-weight:700;padding:5px 14px;border-radius:100px;
        text-transform:uppercase;letter-spacing:0.05em;
    }
    .pill-active   { background:rgba(16,185,129,0.15);color:#6ee7b7; }
    .pill-inactive { background:rgba(239,68,68,0.15);color:#fca5a5; }
    .pill-expired  { background:rgba(249,115,22,0.15);color:#fdba74; }
    .pill-pending  { background:rgba(234,179,8,0.15);color:#fde047; }
    .pill-suspended{ background:rgba(107,114,128,0.15);color:#d1d5db; }

    .sub-stat {
        background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.12);
        border-radius:10px;padding:0.9rem 1rem;text-align:center;
    }
    .sub-stat-value { font-size:1.3rem;font-weight:800;color:#10b981;line-height:1; }
    .sub-stat-label { font-size:0.72rem;color:#6b7280;margin-top:3px;text-transform:uppercase;letter-spacing:0.05em; }

    .btn-action {
        display:inline-flex;align-items:center;gap:6px;
        padding:0.5rem 1.1rem;border-radius:10px;font-size:0.85rem;font-weight:600;
        border:1px solid;transition:all 0.2s;cursor:pointer;text-decoration:none;
    }
    .btn-toggle-on  { background:rgba(234,179,8,0.1);border-color:rgba(234,179,8,0.3);color:#fde047; }
    .btn-toggle-on:hover  { background:rgba(234,179,8,0.2);color:#fde047; }
    .btn-toggle-off { background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.3);color:#6ee7b7; }
    .btn-toggle-off:hover { background:rgba(16,185,129,0.2);color:#6ee7b7; }

    .extend-input {
        background:rgba(10,15,27,0.6);border:1.5px solid rgba(16,185,129,0.2);
        color:#e8eaed;border-radius:8px;padding:0.5rem 0.85rem;font-size:0.9rem;
        outline:none;transition:border-color 0.2s;width:90px;
    }
    .extend-input:focus { border-color:#10b981; }
</style>

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div style="display:flex;align-items:center;gap:1rem;">
        <div style="width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg,#06b6d4,#0891b2);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.3rem;color:white;flex-shrink:0;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1 style="font-size:1.6rem;font-weight:800;color:#e8eaed;margin:0;">{{ $user->name }}</h1>
            <p style="color:#6b7280;margin:2px 0 0;font-size:0.88rem;">{{ $user->email }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.personals.edit', $user->id) }}" class="btn btn-primary" style="padding:0.55rem 1.1rem;font-size:0.9rem;">
            <i class="fas fa-edit me-1"></i> Editar
        </a>
        <a href="{{ route('admin.personals.index') }}" class="btn btn-secondary" style="padding:0.55rem 1.1rem;font-size:0.9rem;">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Coluna principal --}}
    <div class="col-lg-8">

        {{-- Informações Pessoais --}}
        <div class="section-card">
            <div class="section-title"><i class="fas fa-user me-2"></i>Informações Pessoais</div>
            <div class="row g-4">
                <div class="col-sm-6">
                    <div class="info-label">Nome completo</div>
                    <div class="info-value">{{ $user->name }}</div>
                </div>
                <div class="col-sm-6">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
                <div class="col-sm-6">
                    <div class="info-label">Telefone</div>
                    <div class="info-value">{{ $user->phone ?? '—' }}</div>
                </div>
                <div class="col-sm-6">
                    <div class="info-label">Profissão</div>
                    <div class="info-value">{{ $user->profession ?? '—' }}</div>
                </div>
                <div class="col-sm-6">
                    <div class="info-label">Data de Nascimento</div>
                    <div class="info-value">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : '—' }}</div>
                </div>
                <div class="col-sm-6">
                    <div class="info-label">Gênero</div>
                    <div class="info-value">
                        @if($user->gender==='M') Masculino
                        @elseif($user->gender==='F') Feminino
                        @else —
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Assinatura --}}
        @php $sub = $user->professionalSubscription; @endphp
        <div class="section-card">
            <div class="section-title"><i class="fas fa-credit-card me-2"></i>Assinatura</div>
            @if($sub)
                @php
                    $subExpired = $sub->expires_at && $sub->expires_at->isPast();
                    $pillClass = match(true) {
                        $subExpired && $sub->status==='active'  => 'pill-expired',
                        $sub->status==='active'                 => 'pill-active',
                        $sub->status==='pending'                => 'pill-pending',
                        $sub->status==='overdue'                => 'pill-expired',
                        $sub->status==='suspended'              => 'pill-suspended',
                        default                                 => 'pill-inactive',
                    };
                    $pillLabel = match(true) {
                        $subExpired && $sub->status==='active'  => 'Vencida',
                        $sub->status==='active'                 => 'Ativa',
                        $sub->status==='pending'                => 'Pendente',
                        $sub->status==='overdue'                => 'Vencida',
                        $sub->status==='suspended'              => 'Suspensa',
                        $sub->status==='cancelled'              => 'Cancelada',
                        default                                 => $sub->status,
                    };
                @endphp

                <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                    <span class="status-pill {{ $pillClass }}">
                        <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                        {{ $pillLabel }}
                    </span>
                    <span style="font-size:0.88rem;color:#6b7280;">{{ $sub->plan_name ?? '—' }}</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="sub-stat">
                            <div class="sub-stat-value">{{ $sub->max_students ?? '—' }}</div>
                            <div class="sub-stat-label">Max alunos</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sub-stat">
                            <div class="sub-stat-value" style="font-size:1rem;">{{ $sub->expires_at ? $sub->expires_at->format('d/m/Y') : '—' }}</div>
                            <div class="sub-stat-label">Vencimento</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sub-stat">
                            <div class="sub-stat-value" style="font-size:1rem;">{{ $sub->last_paid_at ? $sub->last_paid_at->format('d/m/Y') : '—' }}</div>
                            <div class="sub-stat-label">Último pagto</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sub-stat">
                            <div class="sub-stat-value">R$ {{ number_format($sub->price ?? 0, 2, ',', '.') }}</div>
                            <div class="sub-stat-label">Valor/mês</div>
                        </div>
                    </div>
                </div>

                {{-- Ativar manualmente --}}
                @if(!$sub->isActive())
                    <form method="POST" action="{{ route('admin.personals.activate-subscription', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-action btn-toggle-off" onclick="return confirm('Ativar assinatura por 30 dias?')">
                            <i class="fas fa-check-circle"></i> Ativar por 30 dias
                        </button>
                    </form>
                @endif

                {{-- Estender assinatura --}}
                <form method="POST" action="{{ route('admin.personals.extend-subscription', $user->id) }}" class="d-flex align-items-center gap-3 mt-3 flex-wrap">
                    @csrf
                    @method('PATCH')
                    <input type="number" name="days" class="extend-input" placeholder="Dias" min="1" max="365" required>
                    <button type="submit" class="btn-action" style="background:rgba(99,102,241,0.1);border-color:rgba(99,102,241,0.3);color:#a5b4fc;">
                        <i class="fas fa-calendar-plus"></i> Estender acesso
                    </button>
                </form>
            @else
                <p style="color:#6b7280;font-size:0.9rem;">Sem assinatura registrada.</p>
            @endif
        </div>

        {{-- Status e Acesso --}}
        <div class="section-card">
            <div class="section-title"><i class="fas fa-shield-alt me-2"></i>Status e Acesso</div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                @if($user->is_active)
                    <span class="status-pill pill-active">
                        <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                        Conta Ativa
                    </span>
                @else
                    <span class="status-pill pill-inactive">
                        <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                        Conta Inativa
                    </span>
                @endif

                <form method="POST" action="{{ route('admin.personals.toggle', $user->id) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="btn-action {{ $user->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}"
                        onclick="return confirm('{{ $user->is_active ? 'Desativar' : 'Ativar' }} conta de {{ addslashes($user->name) }}?')">
                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                        {{ $user->is_active ? 'Desativar conta' : 'Ativar conta' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Notas Administrativas --}}
        <div class="section-card">
            <div class="section-title"><i class="fas fa-sticky-note me-2"></i>Notas Administrativas</div>
            <form method="POST" action="{{ route('admin.personals.update', $user->id) }}">
                @csrf
                @method('PUT')
                <textarea name="admin_notes" rows="3"
                    style="width:100%;background:rgba(10,15,27,0.6);border:1.5px solid rgba(16,185,129,0.2);color:#e8eaed;border-radius:10px;padding:0.75rem 1rem;font-size:0.9rem;outline:none;resize:vertical;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='rgba(16,185,129,0.2)'"
                    placeholder="Observações internas...">{{ $user->admin_notes }}</textarea>
                <button type="submit" class="btn btn-primary mt-3" style="font-size:0.88rem;padding:0.5rem 1.1rem;">
                    <i class="fas fa-save me-1"></i> Salvar notas
                </button>
            </form>
        </div>
    </div>

    {{-- Coluna lateral --}}
    <div class="col-lg-4">

        {{-- Alunos --}}
        <div class="section-card text-center">
            <div class="section-title"><i class="fas fa-users me-2"></i>Alunos</div>
            <div style="font-size:3rem;font-weight:800;color:#10b981;line-height:1;margin-bottom:0.5rem;">{{ $studentCount }}</div>
            <div style="font-size:0.82rem;color:#6b7280;margin-bottom:1.25rem;">alunos vinculados</div>
            <a href="{{ route('admin.personals.students', $user->id) }}"
               style="display:inline-flex;align-items:center;gap:6px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:#10b981;padding:0.5rem 1.1rem;border-radius:10px;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all 0.2s;"
               onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                <i class="fas fa-eye"></i> Ver alunos
            </a>
        </div>

        {{-- Histórico --}}
        <div class="section-card">
            <div class="section-title"><i class="fas fa-history me-2"></i>Histórico</div>
            <div class="mb-3">
                <div class="info-label">Cadastrado em</div>
                <div class="info-value">{{ $user->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <div class="info-label">Atualizado em</div>
                <div class="info-value">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        {{-- Ações Perigosas --}}
        <div class="section-card" style="border-color:rgba(239,68,68,0.2);background:rgba(239,68,68,0.02);">
            <div class="section-title" style="color:#f87171;border-color:rgba(239,68,68,0.15);"><i class="fas fa-exclamation-triangle me-2"></i>Ações Perigosas</div>
            <form method="POST" action="{{ route('admin.personals.delete', $user->id) }}"
                  onsubmit="return confirm('ATENÇÃO: Deletar {{ addslashes($user->name) }} e todos seus dados? Esta ação é irreversível!')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    style="width:100%;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;padding:0.6rem 1rem;border-radius:10px;font-size:0.88rem;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:6px;"
                    onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                    <i class="fas fa-trash"></i> Deletar Personal
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
