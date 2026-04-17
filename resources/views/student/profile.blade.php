@extends('layouts.app')

@section('content')
<style>
    .profile-bg { background: #0d0f1a; min-height: 100vh; padding-bottom: 8px; }

    /* Avatar */
    .avatar-ring {
        width: 88px; height: 88px; border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 36px; font-weight: 900; color: #fff;
        border: 3px solid rgba(99,102,241,0.4);
    }

    /* Info card */
    .info-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
    }
    .info-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .info-value { font-size: 14px; color: #e2e8f0; font-weight: 600; text-align: right; max-width: 60%; word-break: break-word; }

    /* Stat mini */
    .mini-stat {
        flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;
        padding: 14px 8px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 16px;
    }
    .mini-val { font-size: 22px; font-weight: 900; color: #fff; }
    .mini-lbl { font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase; }

    /* Avatar foto */
    .avatar-wrap { position: relative; display: inline-block; cursor: pointer; }
    .avatar-wrap .avatar-overlay {
        position: absolute; inset: 0; border-radius: 50%;
        background: rgba(0,0,0,0.5);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.2s;
    }
    .avatar-wrap:hover .avatar-overlay { opacity: 1; }
    .avatar-img { width:88px; height:88px; border-radius:50%; object-fit:cover; border:3px solid rgba(99,102,241,0.4); }

    /* Danger btn */
    .logout-btn {
        width: 100%; padding: 15px; border-radius: 16px; font-size: 15px; font-weight: 700;
        background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
        color: #f87171; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
</style>

<div class="profile-bg pt-5 space-y-5">

    {{-- Avatar + nome --}}
    <div class="flex flex-col items-center gap-3 pt-2 pb-4">
        <form method="POST" action="{{ route('student.profile.photo') }}" enctype="multipart/form-data" id="photo-form">
            @csrf
            <input type="file" name="profile_photo" id="photo-input" accept="image/*" class="hidden">
            <div class="avatar-wrap" onclick="document.getElementById('photo-input').click()">
                @if($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" class="avatar-img" alt="Foto de perfil">
                @else
                    <div class="avatar-ring">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
                <div class="avatar-overlay">
                    <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </form>
        <div class="text-center">
            <h1 class="text-white font-extrabold text-xl">{{ $user->name }}</h1>
            <p class="text-slate-500 text-sm mt-0.5">{{ $user->email }}</p>
        </div>

        {{-- Stats mini row --}}
        <div class="flex gap-3 w-full mt-2">
            <div class="mini-stat">
                <span class="mini-val" style="color:#818cf8;">{{ $totalWorkouts }}</span>
                <span class="mini-lbl">Treinos</span>
            </div>
            @if($latestMeasurement)
            <div class="mini-stat">
                <span class="mini-val" style="color:#34d399;">{{ $latestMeasurement->weight ?? '—' }}</span>
                <span class="mini-lbl">Kg atual</span>
            </div>
            <div class="mini-stat">
                <span class="mini-val" style="color:#f97316;">{{ $latestMeasurement->body_fat ? number_format($latestMeasurement->body_fat,1).'%' : '—' }}</span>
                <span class="mini-lbl">Gordura</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Informações pessoais --}}
    <div class="info-card">
        <div style="padding:14px 18px 10px;">
            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#6366f1;">Informações</p>
        </div>
        <div class="info-row">
            <span class="info-label">Nome</span>
            <span class="info-value">{{ $user->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">E-mail</span>
            <span class="info-value">{{ $user->email }}</span>
        </div>
        @if($user->phone)
        <div class="info-row">
            <span class="info-label">Telefone</span>
            <span class="info-value">{{ $user->phone }}</span>
        </div>
        @endif
        @if($user->birth_date)
        <div class="info-row">
            <span class="info-label">Nascimento</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') }}</span>
        </div>
        @endif
    </div>

    {{-- Personal Trainer --}}
    @if($professional)
    <div class="info-card">
        <div style="padding:14px 18px 10px;">
            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#6366f1;">Personal Trainer</p>
        </div>
        <div class="info-row">
            <span class="info-label">Nome</span>
            <span class="info-value">{{ $professional->name }}</span>
        </div>
        @if($professional->email)
        <div class="info-row">
            <span class="info-label">E-mail</span>
            <span class="info-value">{{ $professional->email }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- Última avaliação --}}
    @if($latestMeasurement)
    <div class="info-card">
        <div style="padding:14px 18px 10px;">
            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#6366f1;">Última Avaliação</p>
        </div>
        <div class="info-row">
            <span class="info-label">Data</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($latestMeasurement->date)->format('d/m/Y') }}</span>
        </div>
        @if($latestMeasurement->height)
        <div class="info-row">
            <span class="info-label">Altura</span>
            <span class="info-value">{{ $latestMeasurement->height }} cm</span>
        </div>
        @endif
        @if($latestMeasurement->weight)
        <div class="info-row">
            <span class="info-label">Peso</span>
            <span class="info-value">{{ $latestMeasurement->weight }} kg</span>
        </div>
        @endif
        <div style="padding:12px 18px;">
            <a href="{{ route('student.evolution') }}"
               style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;border-radius:12px;font-size:13px;font-weight:700;color:#818cf8;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2);">
                Ver evolução completa →
            </a>
        </div>
    </div>
    @endif

    {{-- Sucesso foto --}}
    @if(session('success'))
    <div style="background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.3);border-radius:12px;padding:12px 16px;text-align:center;font-size:13px;font-weight:600;color:#34d399;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Sair do Aplicativo
        </button>
    </form>

</div>

<script>
    document.getElementById('photo-input').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('photo-form').submit();
        }
    });
</script>
@endsection
