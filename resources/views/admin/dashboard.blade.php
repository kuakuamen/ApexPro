@extends('admin.layout')

@section('page-title', 'Dashboard')

@section('admin-content')
<!-- Stats Grid -->
<div class="row g-4 mb-4">
    <!-- Total Personals -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Personals Totais</p>
                        <h2 class="stat-value" style="color: #10b981;">{{ $stats['total_personals'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #10b981;">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Personals -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Personals Ativos</p>
                        <h2 class="stat-value" style="color: #06b6d4;">{{ $stats['active_personals'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #06b6d4;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inactive Personals -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Personals Inativos</p>
                        <h2 class="stat-value" style="color: #f59e0b;">{{ $stats['inactive_personals'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #f59e0b;">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Students -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Total de Alunos</p>
                        <h2 class="stat-value" style="color: #8b5cf6;">{{ $stats['total_students'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #8b5cf6;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Nutritionists -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Total de Nutricionistas</p>
                        <h2 class="stat-value" style="color: #ec4899;">{{ $stats['total_nutritionists'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #ec4899;">
                        <i class="fas fa-apple-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Assessments -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Avaliações Realizadas</p>
                        <h2 class="stat-value" style="color: #3b82f6;">{{ $stats['total_assessments'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #3b82f6;">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Workouts -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Planos de Treino</p>
                        <h2 class="stat-value" style="color: #f43f5e;">{{ $stats['total_workouts'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #f43f5e;">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Diets -->
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Planos Alimentares</p>
                        <h2 class="stat-value" style="color: #14b8a6;">{{ $stats['total_diets'] }}</h2>
                    </div>
                    <div class="stat-icon" style="color: #14b8a6;">
                        <i class="fas fa-utensils"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-bolt"></i> Ações Rápidas
    </div>
    <div class="card-body">
        <div class="d-flex gap-3 flex-wrap">
            <a href="{{ route('admin.personals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Personal
            </a>
            <a href="{{ route('admin.personals.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Gerenciar Personals
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-users"></i> Ver Usuários
            </a>
        </div>
    </div>
</div>
@endsection
