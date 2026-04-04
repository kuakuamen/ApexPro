@extends('admin.layout')

@section('title', 'Gestão de Planos')
@section('page-title', 'Gestão de Planos')

@section('admin-content')
<style>
    .plan-card {
        background: linear-gradient(135deg, rgba(16,185,129,0.07) 0%, rgba(6,182,212,0.03) 100%);
        border: 1.5px solid rgba(16,185,129,0.2);
        border-radius: 18px;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
        position: relative;
    }
    .plan-card:hover {
        border-color: rgba(16,185,129,0.45);
        box-shadow: 0 10px 40px rgba(16,185,129,0.18);
        transform: translateY(-3px);
    }
    .plan-card.inactive {
        opacity: 0.55;
        border-color: rgba(107,114,128,0.25);
    }
    .plan-card-header {
        padding: 1.6rem 1.8rem 1.2rem;
        border-bottom: 1px solid rgba(16,185,129,0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .plan-color-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        box-shadow: 0 0 8px currentColor;
    }
    .plan-name {
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #e8eaed;
    }
    .plan-price-block {
        text-align: right;
    }
    .plan-price {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -1px;
        color: #10b981;
    }
    .plan-price-original {
        font-size: 1rem;
        font-weight: 600;
        color: #9ca3af;
        text-decoration: line-through;
        margin-right: 0.3rem;
    }
    .discount-badge {
        display: inline-block;
        background: linear-gradient(135deg, #ef4444, #f97316);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }
    .plan-card-body {
        padding: 1.4rem 1.8rem;
    }
    .plan-meta {
        display: flex;
        gap: 1.2rem;
        margin-bottom: 1.2rem;
        flex-wrap: wrap;
    }
    .plan-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #9ca3af;
    }
    .plan-meta-item i {
        color: #10b981;
        font-size: 0.85rem;
    }
    .features-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .features-list li {
        font-size: 0.88rem;
        color: #b0b8c1;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        line-height: 1.5;
    }
    .features-list li::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        margin-top: 0.45rem;
        flex-shrink: 0;
    }
    .plan-actions {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
        padding: 1.2rem 1.8rem;
        border-top: 1px solid rgba(16,185,129,0.1);
        background: rgba(0,0,0,0.1);
    }
    .btn-plan-edit {
        background: rgba(16,185,129,0.15);
        border: 1px solid rgba(16,185,129,0.35);
        color: #10b981;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
    }
    .btn-plan-edit:hover {
        background: rgba(16,185,129,0.25);
        color: #10b981;
    }
    .btn-plan-discount {
        background: rgba(249,115,22,0.12);
        border: 1px solid rgba(249,115,22,0.3);
        color: #fb923c;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-plan-discount:hover {
        background: rgba(249,115,22,0.22);
    }
    .btn-plan-remove-discount {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.25);
        color: #f87171;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-plan-remove-discount:hover {
        background: rgba(239,68,68,0.2);
    }
    .btn-plan-toggle {
        background: rgba(107,114,128,0.12);
        border: 1px solid rgba(107,114,128,0.25);
        color: #9ca3af;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-plan-toggle:hover {
        background: rgba(107,114,128,0.22);
        color: #e8eaed;
    }
    .btn-plan-toggle.active-plan {
        background: rgba(239,68,68,0.1);
        border-color: rgba(239,68,68,0.25);
        color: #f87171;
    }
    .btn-plan-toggle.active-plan:hover {
        background: rgba(239,68,68,0.2);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.3rem 0.7rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-badge.active {
        background: rgba(16,185,129,0.15);
        color: #6ee7b7;
    }
    .status-badge.inactive {
        background: rgba(107,114,128,0.15);
        color: #9ca3af;
    }
    /* Modal */
    .modal-dark .modal-content {
        background: linear-gradient(135deg, #0d1b2a 0%, #0a0f1b 100%);
        border: 1.5px solid rgba(16,185,129,0.25);
        border-radius: 16px;
        color: #e8eaed;
    }
    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(16,185,129,0.15);
        padding: 1.5rem 2rem;
    }
    .modal-dark .modal-title {
        font-weight: 700;
        color: #10b981;
    }
    .modal-dark .modal-body {
        padding: 1.8rem 2rem;
    }
    .modal-dark .modal-footer {
        border-top: 1px solid rgba(16,185,129,0.12);
        padding: 1.2rem 2rem;
    }
    .modal-dark .btn-close {
        filter: invert(1);
        opacity: 0.6;
    }
    .form-dark .form-label {
        font-weight: 600;
        color: #b0b8c1;
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }
    .form-dark .form-control {
        background: rgba(255,255,255,0.06);
        border: 1.5px solid rgba(16,185,129,0.2);
        border-radius: 10px;
        color: #e8eaed;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }
    .form-dark .form-control:focus {
        background: rgba(255,255,255,0.09);
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
        color: #e8eaed;
        outline: none;
    }
    .discount-expiry-note {
        font-size: 0.82rem;
        color: #6b7280;
        margin-top: 0.3rem;
    }
    .discount-info-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(249,115,22,0.12);
        border: 1px solid rgba(249,115,22,0.25);
        border-radius: 20px;
        color: #fb923c;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        margin-top: 0.4rem;
    }
</style>

@if (session('info'))
    <div class="alert" style="background:rgba(6,182,212,0.12);border-left:4px solid #06b6d4;color:#67e8f9;">
        <i class="fas fa-info-circle"></i> {{ session('info') }}
    </div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <p style="color:#9ca3af;margin:0;font-size:0.95rem;">Configure preços, descontos e status dos planos de assinatura.</p>
    </div>
</div>

<div class="row g-4">
    @forelse($plans as $plan)
    <div class="col-lg-4 col-md-6">
        <div class="plan-card {{ !$plan->is_active ? 'inactive' : '' }}">
            <!-- Header -->
            <div class="plan-card-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="plan-color-dot" style="background:{{ $plan->color ?? '#10b981' }};box-shadow:0 0 8px {{ $plan->color ?? '#10b981' }}44;"></span>
                    <span class="plan-name">{{ $plan->name }}</span>
                    <span class="status-badge {{ $plan->is_active ? 'active' : 'inactive' }}">
                        <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                        {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <div class="plan-price-block">
                    @if($plan->hasActiveDiscount())
                        <div>
                            <span class="plan-price-original">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 justify-content-end">
                            <span class="plan-price" style="color:#f97316;">R$ {{ number_format($plan->effectivePrice(), 2, ',', '.') }}</span>
                            <span class="discount-badge">-{{ $plan->discount_percent }}%</span>
                        </div>
                        @if($plan->discount_expires_at)
                            <div style="font-size:0.75rem;color:#9ca3af;text-align:right;margin-top:0.2rem;">
                                até {{ $plan->discount_expires_at->format('d/m/Y') }}
                            </div>
                        @else
                            <div style="font-size:0.75rem;color:#fb923c;text-align:right;margin-top:0.2rem;">Sem validade</div>
                        @endif
                    @else
                        <span class="plan-price">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                        <div style="font-size:0.78rem;color:#9ca3af;text-align:right;">/mês</div>
                    @endif
                </div>
            </div>

            <!-- Body -->
            <div class="plan-card-body">
                <div class="plan-meta">
                    <div class="plan-meta-item">
                        <i class="fas fa-users"></i>
                        <span>Até {{ $plan->max_students }} alunos</span>
                    </div>
                    <div class="plan-meta-item">
                        <i class="fas fa-list-check"></i>
                        <span>{{ count($plan->features ?? []) }} funcionalidades</span>
                    </div>
                </div>

                <ul class="features-list">
                    @foreach($plan->features ?? [] as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Actions -->
            <div class="plan-actions">
                <a href="{{ route('admin.plans.edit', $plan->plan_id) }}" class="btn-plan-edit">
                    <i class="fas fa-pen"></i> Editar
                </a>

                <button type="button" class="btn-plan-discount"
                    data-bs-toggle="modal"
                    data-bs-target="#discountModal{{ $loop->index }}"
                    title="Aplicar desconto">
                    <i class="fas fa-percent"></i> Desconto
                </button>

                @if($plan->hasActiveDiscount())
                <form method="POST" action="{{ route('admin.plans.remove-discount', $plan->plan_id) }}" style="display:contents;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-plan-remove-discount" onclick="return confirm('Remover desconto do plano {{ $plan->name }}?')">
                        <i class="fas fa-times-circle"></i> Remover Desconto
                    </button>
                </form>
                @endif

                <form method="POST" action="{{ route('admin.plans.toggle', $plan->plan_id) }}" style="display:contents;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-plan-toggle {{ $plan->is_active ? 'active-plan' : '' }}"
                        onclick="return confirm('{{ $plan->is_active ? 'Desativar' : 'Ativar' }} plano {{ $plan->name }}?')">
                        @if($plan->is_active)
                            <i class="fas fa-toggle-on"></i> Desativar
                        @else
                            <i class="fas fa-toggle-off"></i> Ativar
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Discount Modal -->
    <div class="modal fade modal-dark" id="discountModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-percent me-2"></i> Aplicar Desconto — {{ $plan->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.plans.discount', $plan->plan_id) }}">
                    @csrf @method('PATCH')
                    <div class="modal-body form-dark">
                        <div class="mb-3">
                            <label class="form-label">Percentual de Desconto (%)</label>
                            <input type="number" name="discount_percent" class="form-control"
                                min="1" max="99" required
                                value="{{ $plan->discount_percent ?? '' }}"
                                placeholder="Ex: 20">
                            <div class="discount-expiry-note">
                                Insira um valor entre 1 e 99. O preço com desconto será calculado automaticamente.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Validade do Desconto <span style="color:#6b7280;">(opcional)</span></label>
                            <input type="datetime-local" name="discount_expires_at" class="form-control"
                                value="{{ $plan->discount_expires_at ? $plan->discount_expires_at->format('Y-m-d\TH:i') : '' }}">
                            <div class="discount-expiry-note">
                                Deixe em branco para desconto sem validade.
                            </div>
                        </div>
                        @if($plan->hasActiveDiscount())
                        <div>
                            <span class="discount-info-chip">
                                <i class="fas fa-fire"></i>
                                Desconto atual: {{ $plan->discount_percent }}% — Preço: R$ {{ number_format($plan->effectivePrice(), 2, ',', '.') }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-check me-1"></i> Aplicar Desconto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-tags" style="font-size:3rem;color:#374151;margin-bottom:1rem;display:block;"></i>
                <p style="color:#6b7280;font-size:1.1rem;">Nenhum plano encontrado.</p>
                <p style="color:#4b5563;font-size:0.9rem;">Execute o seeder para criar os planos padrão: <code>php artisan db:seed --class=PlanConfigSeeder</code></p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
