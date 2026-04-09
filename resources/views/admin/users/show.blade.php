@extends('admin.layout')

@section('page-title', 'Detalhes do Usuário')

@section('admin-content')
<style>
    /* ── Avatar ── */
    .user-avatar-xl {
        width: 80px; height: 80px;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 2.2rem; color: #fff;
        flex-shrink: 0;
    }
    .avatar-personal { background: linear-gradient(135deg,#06b6d4,#0891b2); }
    .avatar-aluno    { background: linear-gradient(135deg,#a855f7,#7c3aed); }
    .avatar-admin    { background: linear-gradient(135deg,#ef4444,#dc2626); }
    .avatar-nutri    { background: linear-gradient(135deg,#10b981,#059669); }

    /* ── Badges ── */
    .role-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 0.78rem; font-weight: 700;
        padding: 4px 12px; border-radius: 100px;
        text-transform: uppercase; letter-spacing: 0.06em;
    }
    .role-personal  { background:rgba(6,182,212,0.15);  color:#67e8f9; }
    .role-aluno     { background:rgba(168,85,247,0.15); color:#d8b4fe; }
    .role-admin     { background:rgba(239,68,68,0.15);  color:#fca5a5; }
    .role-nutri     { background:rgba(16,185,129,0.15); color:#6ee7b7; }

    .status-dot {
        width: 8px; height: 8px; border-radius: 50%;
        display: inline-block; background: currentColor;
    }

    /* ── Info rows ── */
    .info-row {
        display: flex; align-items: center;
        padding: 0.9rem 0;
        border-bottom: 1px solid rgba(16,185,129,0.08);
        gap: 0.75rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label {
        font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #6b7280; min-width: 130px; flex-shrink: 0;
    }
    .info-value { color: #e8eaed; font-size: 0.95rem; font-weight: 500; }

    /* ── Stat mini ── */
    .stat-box {
        background: rgba(16,185,129,0.06);
        border: 1px solid rgba(16,185,129,0.15);
        border-radius: 14px; padding: 1.1rem 1.25rem;
        text-align: center;
    }
    .stat-box-value { font-size: 2rem; font-weight: 900; line-height: 1; color: #10b981; }
    .stat-box-label { font-size: 0.72rem; color: #6b7280; margin-top: 5px; text-transform: uppercase; letter-spacing: 0.06em; }

    /* ── Section card ── */
    .section-card {
        background: linear-gradient(135deg, rgba(16,185,129,0.04) 0%, rgba(6,182,212,0.03) 100%);
        border: 1px solid rgba(16,185,129,0.12);
        border-radius: 16px;
        overflow: hidden;
    }
    .section-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(16,185,129,0.1);
        display: flex; align-items: center; gap: 0.6rem;
        font-size: 0.85rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; color: #9ca3af;
    }
    .section-card-header i { color: #10b981; font-size: 0.9rem; }
    .section-card-body { padding: 1.5rem; }

    /* ── Action panels ── */
    .action-tile {
        border-radius: 14px; padding: 1.25rem;
        display: flex; flex-direction: column; gap: 0.75rem;
    }
    .action-tile-green   { background:rgba(16,185,129,0.07); border:1px solid rgba(16,185,129,0.2); }
    .action-tile-red     { background:rgba(239,68,68,0.07);  border:1px solid rgba(239,68,68,0.2); }
    .action-tile-yellow  { background:rgba(234,179,8,0.07);  border:1px solid rgba(234,179,8,0.2); }
    .action-tile-blue    { background:rgba(6,182,212,0.07);  border:1px solid rgba(6,182,212,0.2); }
    .action-tile-title { font-size: 0.82rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; }
    .action-tile-desc  { font-size: 0.82rem; color: #9ca3af; line-height: 1.45; }

    /* ── Inputs ── */
    .dark-input {
        background: rgba(10,15,27,0.6);
        border: 1.5px solid rgba(16,185,129,0.25);
        color: #e8eaed; border-radius: 10px;
        padding: 0.65rem 1rem; font-size: 0.9rem;
        width: 100%; transition: border-color 0.2s; outline: none;
    }
    .dark-input:focus { border-color: #10b981; background: rgba(10,15,27,0.85); color: #e8eaed; }
    .dark-input::placeholder { color: #6b7280; }

    /* ── Buttons ── */
    .btn-red {
        color: #fca5a5; border: 1.5px solid rgba(239,68,68,0.45);
        background: rgba(239,68,68,0.1); font-weight: 700;
        border-radius: 10px; padding: 0.65rem 1.25rem;
        font-size: 0.88rem; cursor: pointer; transition: all 0.2s; width: 100%;
    }
    .btn-red:hover { background: rgba(239,68,68,0.2); border-color: rgba(239,68,68,0.7); }

    .btn-yellow {
        color: #fde047; border: 1.5px solid rgba(234,179,8,0.45);
        background: rgba(234,179,8,0.1); font-weight: 700;
        border-radius: 10px; padding: 0.65rem 1.25rem;
        font-size: 0.88rem; cursor: pointer; transition: all 0.2s;
    }
    .btn-yellow:hover { background: rgba(234,179,8,0.2); border-color: rgba(234,179,8,0.7); }

    /* ── Transactions ── */
    .txn-row {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.85rem 0; border-bottom: 1px solid rgba(16,185,129,0.08);
        flex-wrap: wrap;
    }
    .txn-row:last-child { border-bottom: none; }
    .txn-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem; flex-shrink: 0;
    }
    .txn-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 0.72rem; font-weight: 700; padding: 3px 10px;
        border-radius: 100px; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .txn-approved { background:rgba(16,185,129,0.15); color:#6ee7b7; }
    .txn-pending  { background:rgba(234,179,8,0.15);  color:#fde047; }
    .txn-rejected { background:rgba(239,68,68,0.15);  color:#fca5a5; }
    .txn-refunded { background:rgba(107,114,128,0.15);color:#d1d5db; }

    /* ── Sub status ── */
    .sub-pill {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.82rem; font-weight: 700; padding: 5px 14px;
        border-radius: 100px; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .sub-active    { background:rgba(16,185,129,0.18); color:#6ee7b7; }
    .sub-pending   { background:rgba(234,179,8,0.18);  color:#fde047; }
    .sub-suspended { background:rgba(239,68,68,0.18);  color:#fca5a5; }
    .sub-overdue   { background:rgba(249,115,22,0.18); color:#fdba74; }
    .sub-cancelled { background:rgba(107,114,128,0.18);color:#d1d5db; }

    /* ── Progress ── */
    .progress-track {
        background: rgba(255,255,255,0.07);
        border-radius: 20px; height: 8px; overflow: hidden;
    }
    .progress-fill {
        height: 100%; border-radius: 20px;
        background: linear-gradient(90deg,#10b981,#06b6d4);
        transition: width 0.5s ease;
    }
</style>

{{-- Breadcrumb --}}
<div class="mb-4" style="color:#6b7280;font-size:0.88rem;display:flex;align-items:center;gap:6px;">
    <a href="{{ route('admin.users.index') }}" style="color:#10b981;text-decoration:none;display:flex;align-items:center;gap:5px;">
        <i class="fas fa-users" style="font-size:0.8rem;"></i> Usuários
    </a>
    <i class="fas fa-chevron-right" style="font-size:0.65rem;color:#4b5563;"></i>
    <span style="color:#e8eaed;">{{ $user->name }}</span>
</div>

@php
    $avatarClass = match($user->role) {
        'personal' => 'avatar-personal',
        'aluno'    => 'avatar-aluno',
        'admin'    => 'avatar-admin',
        'nutri'    => 'avatar-nutri',
        default    => 'avatar-personal',
    };
    $roleClass = match($user->role) {
        'personal' => 'role-personal',
        'aluno'    => 'role-aluno',
        'admin'    => 'role-admin',
        'nutri'    => 'role-nutri',
        default    => 'role-personal',
    };
    $roleLabel = match($user->role) {
        'personal' => 'Personal',
        'aluno'    => 'Aluno',
        'admin'    => 'Admin',
        'nutri'    => 'Nutricionista',
        default    => $user->role,
    };
    $roleIcon = match($user->role) {
        'personal' => 'fa-dumbbell',
        'aluno'    => 'fa-user-graduate',
        'admin'    => 'fa-shield-alt',
        'nutri'    => 'fa-leaf',
        default    => 'fa-user',
    };
@endphp

{{-- Hero header --}}
<div class="section-card mb-4">
    <div class="section-card-body">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="user-avatar-xl {{ $avatarClass }}">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div style="flex:1;min-width:0;">
                <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                    <h2 style="font-size:1.75rem;font-weight:900;color:#e8eaed;margin:0;">{{ $user->name }}</h2>
                    <span class="role-badge {{ $roleClass }}">
                        <i class="fas {{ $roleIcon }}" style="font-size:0.7rem;"></i> {{ $roleLabel }}
                    </span>
                    @if($user->is_active)
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:700;padding:4px 12px;border-radius:100px;background:rgba(16,185,129,0.12);color:#6ee7b7;text-transform:uppercase;letter-spacing:0.05em;">
                            <span class="status-dot" style="color:#10b981;"></span> Ativo
                        </span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:700;padding:4px 12px;border-radius:100px;background:rgba(239,68,68,0.12);color:#fca5a5;text-transform:uppercase;letter-spacing:0.05em;">
                            <span class="status-dot" style="color:#ef4444;"></span> Inativo
                        </span>
                    @endif
                </div>
                <div style="color:#9ca3af;font-size:0.95rem;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-envelope" style="font-size:0.8rem;color:#6b7280;"></i>
                    {{ $user->email }}
                </div>
                <div style="color:#6b7280;font-size:0.82rem;margin-top:6px;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-calendar-alt" style="font-size:0.75rem;"></i>
                    Cadastrado em {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="white-space:nowrap;">
                <i class="fas fa-arrow-left me-2"></i> Voltar
            </a>
        </div>
    </div>
</div>

{{-- Main grid --}}
<div class="row g-4 mb-4">

    {{-- Dados pessoais --}}
    <div class="col-lg-{{ $user->role === 'personal' ? '4' : '6' }}">
        <div class="section-card h-100">
            <div class="section-card-header">
                <i class="fas fa-id-card"></i> Dados Pessoais
            </div>
            <div class="section-card-body">
                @if($user->phone)
                <div class="info-row">
                    <span class="info-label">Telefone</span>
                    <span class="info-value">{{ $user->phone }}</span>
                </div>
                @endif
                @if($user->cpf)
                <div class="info-row">
                    <span class="info-label">CPF</span>
                    <span class="info-value">{{ $user->cpf }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Tipo</span>
                    <span class="role-badge {{ $roleClass }}" style="font-size:0.72rem;">
                        <i class="fas {{ $roleIcon }}" style="font-size:0.65rem;"></i> {{ $roleLabel }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    @if($user->is_active)
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.78rem;font-weight:700;padding:3px 10px;border-radius:100px;background:rgba(16,185,129,0.12);color:#6ee7b7;">
                            <span class="status-dot" style="color:#10b981;width:6px;height:6px;"></span> Ativo
                        </span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.78rem;font-weight:700;padding:3px 10px;border-radius:100px;background:rgba(239,68,68,0.12);color:#fca5a5;">
                            <span class="status-dot" style="color:#ef4444;width:6px;height:6px;"></span> Inativo
                        </span>
                    @endif
                </div>
                <div class="info-row">
                    <span class="info-label">Cadastrado</span>
                    <span class="info-value">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($user->last_login_at ?? null)
                <div class="info-row">
                    <span class="info-label">Último acesso</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($user->role === 'personal')
    {{-- Assinatura --}}
    <div class="col-lg-4">
        <div class="section-card h-100">
            <div class="section-card-header">
                <i class="fas fa-credit-card"></i> Assinatura
            </div>
            <div class="section-card-body">
                @if($user->professionalSubscription)
                    @php $sub = $user->professionalSubscription; @endphp
                    @php
                        $subExpired = $sub->expires_at && $sub->expires_at->isPast();
                        $subClass = match(true) {
                            $subExpired && $sub->status === 'active' => 'sub-overdue',
                            $sub->status === 'active'    => 'sub-active',
                            $sub->status === 'pending'   => 'sub-pending',
                            $sub->status === 'suspended' => 'sub-suspended',
                            $sub->status === 'overdue'   => 'sub-overdue',
                            default                      => 'sub-cancelled',
                        };
                        $subLabel = match(true) {
                            $subExpired && $sub->status === 'active' => 'Vencida',
                            $sub->status === 'active'    => 'Ativa',
                            $sub->status === 'pending'   => 'Pendente',
                            $sub->status === 'suspended' => 'Suspensa',
                            $sub->status === 'overdue'   => 'Vencida',
                            $sub->status === 'cancelled' => 'Cancelada',
                            default                      => $sub->status,
                        };
                    @endphp
                    <div class="info-row">
                        <span class="info-label">Plano</span>
                        <span class="info-value" style="font-weight:700;">{{ $sub->plan_name ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="sub-pill {{ $subClass }}">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                            {{ $subLabel }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Início</span>
                        <span class="info-value">{{ $sub->starts_at ? $sub->starts_at->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Expira em</span>
                        <span class="info-value" style="{{ $sub->expires_at && $sub->expires_at->isPast() ? 'color:#fca5a5;' : 'color:#6ee7b7;' }}">
                            {{ $sub->expires_at ? $sub->expires_at->format('d/m/Y') : '—' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Último pagamento</span>
                        <span class="info-value">{{ $sub->last_paid_at ? $sub->last_paid_at->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Limite de alunos</span>
                        <span class="info-value" style="font-weight:700;color:#06b6d4;">{{ $sub->max_students ?? '—' }}</span>
                    </div>
                @else
                    <div style="text-align:center;padding:2.5rem 1rem;">
                        <i class="fas fa-credit-card" style="font-size:2rem;color:rgba(16,185,129,0.15);display:block;margin-bottom:0.75rem;"></i>
                        <p style="color:#6b7280;margin:0;font-size:0.9rem;">Sem assinatura cadastrada</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Estatísticas --}}
    <div class="col-lg-4">
        <div class="section-card h-100">
            <div class="section-card-header">
                <i class="fas fa-chart-bar"></i> Estatísticas
            </div>
            <div class="section-card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="stat-box">
                            <div class="stat-box-value">{{ $studentCount }}</div>
                            <div class="stat-box-label">Total alunos</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box" style="border-color:rgba(6,182,212,0.2);">
                            <div class="stat-box-value" style="color:#06b6d4;">{{ $activeStudentCount }}</div>
                            <div class="stat-box-label">Ativos</div>
                        </div>
                    </div>
                </div>

                @if($user->professionalSubscription)
                    @php
                        $maxSt = $user->professionalSubscription->max_students ?? 1;
                        $pct   = $maxSt > 0 ? min(100, round(($studentCount / $maxSt) * 100)) : 0;
                        $pctColor = $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : '#10b981');
                    @endphp
                    <div style="margin-top:0.5rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <span style="font-size:0.75rem;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;">Uso do plano</span>
                            <span style="font-size:0.82rem;color:{{ $pctColor }};font-weight:700;">{{ $studentCount }} / {{ $maxSt }}</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,{{ $pctColor }},{{ $pctColor }}aa);"></div>
                        </div>
                        <div style="font-size:0.75rem;color:#6b7280;margin-top:6px;text-align:right;">{{ $pct }}% utilizado</div>
                    </div>
                @endif

                @if($studentCount > $activeStudentCount)
                <div style="margin-top:1rem;padding:0.75rem 1rem;background:rgba(107,114,128,0.08);border:1px solid rgba(107,114,128,0.15);border-radius:10px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:0.82rem;color:#9ca3af;">Inativos</span>
                    <span style="font-size:0.95rem;font-weight:700;color:#9ca3af;">{{ $studentCount - $activeStudentCount }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>

@if($user->role === 'personal')
{{-- Gerenciar Assinatura --}}
<div class="section-card mb-4">
    <div class="section-card-header">
        <i class="fas fa-sliders-h"></i> Gerenciar Assinatura
    </div>
    <div class="section-card-body">
        <div class="row g-3">

            {{-- Ativar --}}
            <div class="col-md-4">
                <div class="action-tile action-tile-green">
                    <div>
                        <div class="action-tile-title" style="color:#10b981;"><i class="fas fa-play-circle me-1"></i> Ativar / Renovar</div>
                        <p class="action-tile-desc mb-0 mt-1">Ativa a assinatura por 30 dias a partir de hoje.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.users.subscription.activate', $user->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary w-100"
                                style="padding:0.65rem 1rem;font-size:0.88rem;"
                                onclick="return confirm('Ativar assinatura por 30 dias?')">
                            <i class="fas fa-play me-1"></i> Ativar por 30 dias
                        </button>
                    </form>
                </div>
            </div>

            {{-- Estender --}}
            <div class="col-md-4">
                <div class="action-tile action-tile-yellow">
                    <div>
                        <div class="action-tile-title" style="color:#fde047;"><i class="fas fa-calendar-plus me-1"></i> Estender Acesso</div>
                        <p class="action-tile-desc mb-0 mt-1">Adiciona dias extras ao período atual de acesso.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.users.subscription.extend', $user->id) }}">
                        @csrf @method('PATCH')
                        <div class="d-flex gap-2">
                            <input type="number" name="days" min="1" max="365" value="30"
                                   class="dark-input"
                                   style="width:85px;flex-shrink:0;padding:0.65rem 0.75rem;text-align:center;"
                                   placeholder="Dias">
                            <button type="submit" class="btn-yellow flex-grow-1">
                                <i class="fas fa-plus me-1"></i> Estender
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Suspender --}}
            <div class="col-md-4">
                <div class="action-tile action-tile-red">
                    <div>
                        <div class="action-tile-title" style="color:#fca5a5;"><i class="fas fa-ban me-1"></i> Suspender</div>
                        <p class="action-tile-desc mb-0 mt-1">Bloqueia o acesso do personal imediatamente.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.users.subscription.suspend', $user->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-red"
                                onclick="return confirm('Tem certeza que deseja suspender esta assinatura?')">
                            <i class="fas fa-pause me-1"></i> Suspender Acesso
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endif

{{-- Redefinir Senha --}}
<div class="section-card mb-4">
    <div class="section-card-header">
        <i class="fas fa-key"></i> Redefinir Senha
    </div>
    <div class="section-card-body">
        <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}"
              class="d-flex gap-3 align-items-end flex-wrap">
            @csrf @method('PATCH')
            <div style="flex:1;min-width:240px;">
                <label style="font-size:0.75rem;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;display:block;margin-bottom:7px;">
                    Nova senha
                </label>
                <input type="password" name="password"
                       class="dark-input"
                       placeholder="Mínimo 6 caracteres"
                       autocomplete="new-password"
                       required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary"
                    style="padding:0.65rem 1.5rem;white-space:nowrap;"
                    onclick="return confirm('Confirma a redefinição de senha?')">
                <i class="fas fa-key me-1"></i> Redefinir Senha
            </button>
        </form>
    </div>
</div>

@if($user->role === 'personal' && $recentTransactions->isNotEmpty())
{{-- Transações --}}
<div class="section-card mb-4">
    <div class="section-card-header">
        <i class="fas fa-receipt"></i> Transações Recentes
    </div>
    <div class="section-card-body" style="padding:0.5rem 1.5rem;">
        @foreach($recentTransactions as $txn)
            @php
                $txnClass = match($txn->status) {
                    'approved'  => 'txn-approved',
                    'pending'   => 'txn-pending',
                    'rejected', 'cancelled' => 'txn-rejected',
                    'refunded'  => 'txn-refunded',
                    default     => 'txn-pending',
                };
                $txnLabel = match($txn->status) {
                    'approved'  => 'Aprovado',
                    'pending'   => 'Pendente',
                    'rejected'  => 'Rejeitado',
                    'cancelled' => 'Cancelado',
                    'refunded'  => 'Estornado',
                    default     => $txn->status,
                };
                $methodIcon = match($txn->payment_method ?? '') {
                    'pix'         => 'fa-qrcode',
                    'credit_card' => 'fa-credit-card',
                    default       => 'fa-receipt',
                };
                $iconBg = match($txn->status) {
                    'approved' => 'rgba(16,185,129,0.15)',
                    'pending'  => 'rgba(234,179,8,0.15)',
                    default    => 'rgba(239,68,68,0.15)',
                };
                $iconColor = match($txn->status) {
                    'approved' => '#10b981',
                    'pending'  => '#f59e0b',
                    default    => '#ef4444',
                };
            @endphp
            <div class="txn-row">
                <div class="txn-icon" style="background:{{ $iconBg }};color:{{ $iconColor }};">
                    <i class="fas {{ $methodIcon }}"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;color:#e8eaed;font-size:0.9rem;">{{ $txn->plan_id ?? 'Assinatura' }}</div>
                    <div style="color:#6b7280;font-size:0.8rem;">{{ $txn->created_at->format('d/m/Y H:i') }} · {{ ucfirst($txn->payment_method ?? '—') }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:800;color:#e8eaed;font-size:0.95rem;">R$ {{ number_format($txn->amount, 2, ',', '.') }}</div>
                    <span class="txn-badge {{ $txnClass }}" style="margin-top:3px;">{{ $txnLabel }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection
