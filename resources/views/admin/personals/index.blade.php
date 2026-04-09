@extends('admin.layout')

@section('page-title', 'Personals')

@section('admin-content')
<style>
    .personal-card {
        background: linear-gradient(135deg, rgba(16,185,129,0.04) 0%, rgba(6,182,212,0.04) 100%);
        border: 1px solid rgba(16,185,129,0.12);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.25s;
    }
    .personal-card:hover {
        border-color: rgba(16,185,129,0.35);
        background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(6,182,212,0.06) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16,185,129,0.1);
    }
    .personal-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
    }
    .status-badge {
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
    .status-active    { background:rgba(16,185,129,0.15);  color:#6ee7b7; }
    .status-expired   { background:rgba(249,115,22,0.15);  color:#fdba74; }
    .status-inactive  { background:rgba(239,68,68,0.15);   color:#fca5a5; }

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
        padding: 0.4rem 0.85rem;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-detail:hover { background: rgba(16,185,129,0.2); border-color: #10b981; color: #10b981; }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        border: 1px solid;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-icon-edit  { background:rgba(99,102,241,0.1); border-color:rgba(99,102,241,0.3); color:#a5b4fc; }
    .btn-icon-edit:hover  { background:rgba(99,102,241,0.2); color:#a5b4fc; }
    .btn-icon-del   { background:rgba(239,68,68,0.1); border-color:rgba(239,68,68,0.3); color:#fca5a5; }
    .btn-icon-del:hover   { background:rgba(239,68,68,0.2); color:#fca5a5; }
</style>

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 style="font-size:1.8rem;font-weight:800;color:#e8eaed;margin:0;">Personals</h1>
        <p style="color:#6b7280;margin:4px 0 0;font-size:0.9rem;">Gerencie os personal trainers da plataforma</p>
    </div>
    <a href="{{ route('admin.personals.create') }}" class="btn btn-primary" style="padding:0.6rem 1.25rem;font-weight:600;">
        <i class="fas fa-plus me-1"></i> Novo Personal
    </a>
</div>

{{-- Stats --}}
@php
    $total   = $personals->total();
    $ativos  = \App\Models\User::where('role','personal')->where('is_active',true)->count();
    $inativos = \App\Models\User::where('role','personal')->where('is_active',false)->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-4"><div class="stat-mini"><div class="stat-mini-value">{{ $total }}</div><div class="stat-mini-label">Total</div></div></div>
    <div class="col-4"><div class="stat-mini"><div class="stat-mini-value">{{ $ativos }}</div><div class="stat-mini-label">Ativos</div></div></div>
    <div class="col-4"><div class="stat-mini" style="border-color:rgba(239,68,68,0.2);"><div class="stat-mini-value" style="color:#ef4444;">{{ $inativos }}</div><div class="stat-mini-label">Inativos</div></div></div>
</div>

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body" style="padding:1.25rem 1.5rem;">
        <form method="GET" action="{{ route('admin.personals.index') }}">
            <div class="d-flex flex-wrap gap-3 align-items-end">
                <div style="flex:1;min-width:200px;">
                    <label style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;display:block;margin-bottom:6px;">Buscar</label>
                    <input type="text" name="search" class="filter-input w-100" placeholder="Nome, email ou telefone..." value="{{ request('search') }}">
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
                    <a href="{{ route('admin.personals.index') }}" class="btn btn-secondary" style="padding:0.6rem 1rem;">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Lista --}}
<div style="display:flex;flex-direction:column;gap:0.75rem;">
    @forelse ($personals as $personal)
        @php
            $initial = strtoupper(substr($personal->name, 0, 1));
            $pSub = $personal->professionalSubscription;
            $isActive = $pSub && $pSub->isActive();
            $isExpired = $pSub && ($pSub->isExpired() || in_array($pSub->status, ['overdue','suspended']));
        @endphp
        <div class="personal-card">
            {{-- Avatar --}}
            <div class="personal-avatar">{{ $initial }}</div>

            {{-- Info --}}
            <div style="flex:1;min-width:0;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span style="font-weight:700;color:#e8eaed;font-size:0.95rem;">{{ $personal->name }}</span>
                    @if($isExpired)
                        <span class="status-badge status-expired">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                            Vencido
                        </span>
                    @elseif($isActive)
                        <span class="status-badge status-active">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                            Ativo
                        </span>
                    @else
                        <span class="status-badge status-inactive">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                            Inativo
                        </span>
                    @endif
                </div>
                <div style="color:#6b7280;font-size:0.85rem;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $personal->email }}
                    @if($personal->phone)
                        <span style="margin-left:0.75rem;color:#4b5563;">{{ $personal->phone }}</span>
                    @endif
                </div>
            </div>

            {{-- Assinatura --}}
            <div style="min-width:130px;text-align:center;" class="d-none d-md-block">
                @if($pSub)
                    <div style="font-size:0.78rem;color:#6b7280;">Vence em</div>
                    <div style="font-size:0.85rem;font-weight:600;color:{{ $pSub->expires_at && $pSub->expires_at->isPast() ? '#fdba74' : '#e8eaed' }};">
                        {{ $pSub->expires_at ? $pSub->expires_at->format('d/m/Y') : '—' }}
                    </div>
                @else
                    <span style="font-size:0.8rem;color:#6b7280;">Sem assinatura</span>
                @endif
            </div>

            {{-- Cadastro --}}
            <div style="min-width:80px;text-align:right;color:#6b7280;font-size:0.82rem;" class="d-none d-lg-block">
                {{ $personal->created_at->format('d/m/Y') }}
            </div>

            {{-- Ações --}}
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.personals.show', $personal->id) }}" class="btn-detail">
                    <i class="fas fa-eye me-1"></i> Ver
                </a>
                <a href="{{ route('admin.personals.edit', $personal->id) }}" class="btn-icon btn-icon-edit" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <form method="POST" action="{{ route('admin.personals.delete', $personal->id) }}"
                      style="display:inline;"
                      onsubmit="return confirm('Deletar {{ addslashes($personal->name) }}? Esta ação é irreversível!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-icon btn-icon-del" title="Deletar">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:4rem 2rem;background:rgba(16,185,129,0.03);border:1px dashed rgba(16,185,129,0.15);border-radius:16px;">
            <i class="fas fa-user-tie" style="font-size:2.5rem;color:rgba(16,185,129,0.2);margin-bottom:1rem;display:block;"></i>
            <p style="color:#6b7280;margin:0;font-size:0.95rem;">Nenhum personal encontrado.</p>
        </div>
    @endforelse
</div>

@if ($personals->hasPages())
    <div style="margin-top:1.5rem;">{{ $personals->links() }}</div>
@endif

@endsection
