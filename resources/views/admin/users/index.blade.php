@extends('admin.layout')

@section('page-title', 'Usuários')

@section('admin-content')
<style>
    .user-card {
        background: linear-gradient(135deg, rgba(16,185,129,0.04) 0%, rgba(6,182,212,0.04) 100%);
        border: 1px solid rgba(16,185,129,0.12);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.25s;
        text-decoration: none;
        color: inherit;
    }
    .user-card:hover {
        border-color: rgba(16,185,129,0.35);
        background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(6,182,212,0.06) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16,185,129,0.1);
    }
    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
    }
    .avatar-personal  { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .avatar-aluno     { background: linear-gradient(135deg, #a855f7, #7c3aed); }
    .avatar-admin     { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .avatar-nutri     { background: linear-gradient(135deg, #10b981, #059669); }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 100px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .role-personal  { background:rgba(6,182,212,0.15);   color:#67e8f9; }
    .role-aluno     { background:rgba(168,85,247,0.15);  color:#d8b4fe; }
    .role-admin     { background:rgba(239,68,68,0.15);   color:#fca5a5; }
    .role-nutri     { background:rgba(16,185,129,0.15);  color:#6ee7b7; }

    .sub-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 100px;
    }
    .sub-active    { background:rgba(16,185,129,0.15); color:#6ee7b7; }
    .sub-pending   { background:rgba(234,179,8,0.15);  color:#fde047; }
    .sub-suspended { background:rgba(239,68,68,0.15);  color:#fca5a5; }
    .sub-overdue   { background:rgba(249,115,22,0.15); color:#fdba74; }
    .sub-cancelled { background:rgba(107,114,128,0.15);color:#d1d5db; }

    .filter-input {
        background: rgba(10,15,27,0.6);
        border: 1.5px solid rgba(16,185,129,0.2);
        color: #e8eaed;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }
    .filter-input:focus { border-color: #10b981; }
    .filter-input::placeholder { color: #6b7280; }
    .filter-input option { background: #0f172a; }

    .stat-mini {
        background: rgba(16,185,129,0.06);
        border: 1px solid rgba(16,185,129,0.15);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        text-align: center;
    }
    .stat-mini-value { font-size: 1.6rem; font-weight: 800; color: #10b981; line-height: 1; }
    .stat-mini-label { font-size: 0.75rem; color: #6b7280; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.05em; }

    .btn-detail {
        background: rgba(16,185,129,0.1);
        border: 1px solid rgba(16,185,129,0.3);
        color: #10b981;
        border-radius: 8px;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-detail:hover {
        background: rgba(16,185,129,0.2);
        border-color: #10b981;
        color: #10b981;
    }
</style>

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.8rem;font-weight:800;color:#e8eaed;margin:0;">Gestão de Usuários</h1>
        <p style="color:#6b7280;margin:4px 0 0;font-size:0.9rem;">Gerencie todos os usuários da plataforma</p>
    </div>
</div>

{{-- Stats rápidas --}}
@php
    $totalUsers    = \App\Models\User::count();
    $totalPersonal = \App\Models\User::where('role','personal')->count();
    $totalAlunos   = \App\Models\User::where('role','aluno')->count();
    $totalAtivos   = \App\Models\User::where('is_active', true)->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-mini"><div class="stat-mini-value">{{ $totalUsers }}</div><div class="stat-mini-label">Total</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-mini" style="border-color:rgba(6,182,212,0.2);"><div class="stat-mini-value" style="color:#06b6d4;">{{ $totalPersonal }}</div><div class="stat-mini-label">Personals</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-mini" style="border-color:rgba(168,85,247,0.2);"><div class="stat-mini-value" style="color:#a855f7;">{{ $totalAlunos }}</div><div class="stat-mini-label">Alunos</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-mini"><div class="stat-mini-value">{{ $totalAtivos }}</div><div class="stat-mini-label">Ativos</div></div></div>
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body" style="padding:1.25rem 1.5rem;">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="d-flex flex-wrap gap-3 align-items-end">
                <div style="flex:1;min-width:200px;">
                    <label style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;display:block;margin-bottom:6px;">Buscar</label>
                    <input type="text" name="search" class="filter-input w-100" placeholder="Nome ou email..." value="{{ request('search') }}">
                </div>
                <div style="min-width:160px;">
                    <label style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;display:block;margin-bottom:6px;">Tipo</label>
                    <select name="role" class="filter-input w-100">
                        <option value="">Todos os tipos</option>
                        <option value="admin"    {{ request('role')==='admin'    ? 'selected':'' }}>Admin</option>
                        <option value="personal" {{ request('role')==='personal' ? 'selected':'' }}>Personal</option>
                        <option value="nutri"    {{ request('role')==='nutri'    ? 'selected':'' }}>Nutricionista</option>
                        <option value="aluno"    {{ request('role')==='aluno'    ? 'selected':'' }}>Aluno</option>
                    </select>
                </div>
                <div style="min-width:160px;">
                    <label style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;display:block;margin-bottom:6px;">Status</label>
                    <select name="status" class="filter-input w-100">
                        <option value="">Todos os status</option>
                        <option value="1" {{ request('status')==='1' ? 'selected':'' }}>Ativos</option>
                        <option value="0" {{ request('status')==='0' ? 'selected':'' }}>Inativos</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.25rem;white-space:nowrap;">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="padding:0.6rem 1rem;white-space:nowrap;">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Lista de usuários --}}
<div style="display:flex;flex-direction:column;gap:0.75rem;">
    @forelse ($users as $user)
        @php
            $initial    = strtoupper(substr($user->name, 0, 1));
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
            $sub = $user->professionalSubscription ?? null;
        @endphp
        <div class="user-card">
            {{-- Avatar --}}
            <div class="user-avatar {{ $avatarClass }}">{{ $initial }}</div>

            {{-- Info principal --}}
            <div style="flex:1;min-width:0;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span style="font-weight:700;color:#e8eaed;font-size:0.95rem;">{{ $user->name }}</span>
                    <span class="role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                    @if(!$user->is_active)
                        <span style="font-size:0.72rem;background:rgba(239,68,68,0.12);color:#fca5a5;padding:2px 8px;border-radius:100px;font-weight:600;">Inativo</span>
                    @endif
                </div>
                <div style="color:#6b7280;font-size:0.85rem;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $user->email }}</div>
            </div>

            {{-- Assinatura (só personals) --}}
            <div style="min-width:140px;text-align:center;" class="d-none d-md-block">
                @if($user->role === 'personal' && $sub)
                    @php
                        $subClass = match($sub->status) {
                            'active'    => 'sub-active',
                            'pending'   => 'sub-pending',
                            'suspended' => 'sub-suspended',
                            'overdue'   => 'sub-overdue',
                            default     => 'sub-cancelled',
                        };
                        $subLabel = match($sub->status) {
                            'active'    => 'Ativa',
                            'pending'   => 'Pendente',
                            'suspended' => 'Suspensa',
                            'overdue'   => 'Vencida',
                            'cancelled' => 'Cancelada',
                            default     => $sub->status,
                        };
                    @endphp
                    <span class="sub-badge {{ $subClass }}">
                        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                        {{ $subLabel }}
                    </span>
                    <div style="font-size:0.78rem;color:#6b7280;margin-top:4px;">{{ $sub->plan_name ?? '' }}</div>
                @elseif($user->role === 'personal')
                    <span style="font-size:0.8rem;color:#6b7280;">Sem assinatura</span>
                @else
                    <span style="color:#4b5563;font-size:0.85rem;">—</span>
                @endif
            </div>

            {{-- Data --}}
            <div style="min-width:90px;text-align:right;color:#6b7280;font-size:0.82rem;" class="d-none d-lg-block">
                {{ $user->created_at->format('d/m/Y') }}
            </div>

            {{-- Ação --}}
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn-detail">
                <i class="fas fa-eye me-1"></i> Detalhes
            </a>
        </div>
    @empty
        <div style="text-align:center;padding:4rem 2rem;background:rgba(16,185,129,0.03);border:1px dashed rgba(16,185,129,0.15);border-radius:16px;">
            <i class="fas fa-users" style="font-size:2.5rem;color:rgba(16,185,129,0.2);margin-bottom:1rem;display:block;"></i>
            <p style="color:#6b7280;margin:0;font-size:0.95rem;">Nenhum usuário encontrado.</p>
        </div>
    @endforelse
</div>

@if ($users->hasPages())
    <div style="margin-top:1.5rem;">{{ $users->links() }}</div>
@endif

@endsection
