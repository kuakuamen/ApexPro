<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - ApexPro</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#06b6d4">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #0a0f1b 0%, #0d1b2a 50%, #0a1628 100%);
            color: #e8eaed;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            background: linear-gradient(180deg, #0d1b2a 0%, #0a0f1b 100%);
            border-right: 2px solid #10b981;
            min-height: 100vh;
            padding: 2.5rem 0;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(16, 185, 129, 0.15);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.5);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin-bottom: 2.5rem;
            gap: 12px;
            border-radius: 12px;
            margin: 0 1rem 2.5rem 1rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .sidebar-brand img {
            height: 50px;
            filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.5));
        }

        .sidebar-brand span {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .sidebar-menu-item {
            margin: 0;
        }

        .sidebar-menu-link {
            color: #b0b8c1;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            border-radius: 0;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            margin: 0;
        }

        .sidebar-menu-link:hover {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-left-color: #10b981;
            padding-left: calc(1.5rem - 3px);
        }

        .sidebar-menu-link.active {
            background: rgba(16, 185, 129, 0.25);
            color: #10b981;
            border-left-color: #10b981;
            padding-left: calc(1.5rem - 3px);
            font-weight: 600;
        }

        .sidebar-menu-link i {
            margin-right: 14px;
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-section {
            margin-top: 2.5rem;
            padding: 2rem 1.5rem 0;
            border-top: 1px solid rgba(16, 185, 129, 0.15);
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #6b7680;
            margin-bottom: 1rem;
            letter-spacing: 0.15em;
        }

        /* MAIN CONTENT */
        main {
            margin-left: 260px;
            padding: 3rem;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }

        .mobile-menu-btn {
            display: none;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            border: 1px solid rgba(16, 185, 129, 0.35);
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-menu-btn:hover {
            background: rgba(16, 185, 129, 0.2);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 150;
        }

        .topbar-title {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -1.5px;
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            background: rgba(16, 185, 129, 0.1);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .topbar-user span {
            font-weight: 600;
            color: #e8eaed;
            font-size: 1rem;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        /* ALERTS */
        .alert {
            border: none;
            border-left: 4px solid;
            border-radius: 8px;
            margin-bottom: 2rem;
            padding: 1.2rem 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border-left-color: #10b981;
            color: #6ee7b7;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border-left-color: #ef4444;
            color: #fca5a5;
        }

        /* CARDS */
        .card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(6, 182, 212, 0.04) 100%);
            border: 1.5px solid rgba(16, 185, 129, 0.2);
            border-radius: 16px;
            color: #e8eaed;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .card:hover {
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.15);
            transform: translateY(-2px);
        }

        .card-header {
            background: rgba(16, 185, 129, 0.1);
            border-bottom: 1.5px solid rgba(16, 185, 129, 0.15);
            color: #10b981;
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.3px;
        }

        .card-body {
            padding: 2rem;
        }

        /* BUTTONS */
        .btn {
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #0891b2 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-secondary {
            background: rgba(107, 114, 128, 0.15);
            border: 1px solid rgba(107, 114, 128, 0.3);
            color: #d1d5db;
        }

        .btn-secondary:hover {
            background: rgba(107, 114, 128, 0.25);
            border-color: rgba(107, 114, 128, 0.5);
            color: #e8eaed;
        }

        .btn-outline-primary {
            color: #10b981;
            border: 1.5px solid #10b981;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        /* TABLE */
        table {
            color: #e8eaed;
            font-weight: 500;
        }

        thead {
            background: rgba(16, 185, 129, 0.1);
        }

        thead th {
            color: #10b981;
            font-weight: 700;
            border: none;
            padding: 1.2rem;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-top: 1px solid rgba(16, 185, 129, 0.1);
            transition: background 0.3s ease;
        }

        tbody tr:hover {
            background: rgba(16, 185, 129, 0.08);
        }

        tbody td {
            padding: 1.2rem;
            vertical-align: middle;
        }

        .badge {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.6rem 1rem;
            border-radius: 20px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: min(88vw, 320px);
                transition: transform 0.3s ease;
                z-index: 200;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            main {
                margin-left: 0;
                padding: 1.5rem 1rem;
            }

            .topbar {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
                margin-bottom: 2rem;
            }

            .topbar .d-flex {
                align-items: center;
            }

            .topbar-title {
                font-size: 2rem;
                line-height: 1.1;
                letter-spacing: -1px;
            }

            .mobile-menu-btn {
                display: inline-flex;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .topbar-user {
                width: 100%;
                justify-content: space-between;
                padding: 0.7rem 1rem;
            }

            .card-body {
                padding: 1.25rem;
            }

            .stat-value {
                font-size: 2.4rem;
            }
        }

        /* CUSTOM */
        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2), transparent);
            border-radius: 50%;
        }

        .stat-value {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -2px;
            margin: 0.5rem 0;
        }

        .stat-label {
            font-size: 0.95rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('storage/photos/Gemini_Generated_Image_.png') }}" alt="ApexPro">
            <span>ApexPro</span>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-menu-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-link {{ request()->route()->getName() === 'admin.dashboard' ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('admin.personals.index') }}" class="sidebar-menu-link {{ str_starts_with(request()->route()->getName(), 'admin.personals') ? 'active' : '' }}">
                    <i class="fas fa-dumbbell"></i> Personals
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('admin.users.index') }}" class="sidebar-menu-link {{ str_starts_with(request()->route()->getName(), 'admin.users') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Usuários
                </a>
            </li>
            <li class="sidebar-menu-item">
                <a href="{{ route('admin.logs') }}" class="sidebar-menu-link {{ request()->route()->getName() === 'admin.logs' ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Logs
                </a>
            </li>
        </ul>

        <div class="sidebar-section">
            <p class="sidebar-section-title">Conta</p>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ route('logout') }}" class="sidebar-menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </li>
            </ul>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <main>
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Abrir menu">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="topbar-title mb-0">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="topbar-user">
                <span>{{ auth()->user()->name }}</span>
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-circle"></i> Erros encontrados:</strong>
                <ul class="mb-0" style="margin-top: 0.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @yield('admin-content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (!sidebar || !mobileMenuBtn || !sidebarOverlay) return;

            const closeSidebar = () => {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            };

            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
            });

            sidebarOverlay.addEventListener('click', closeSidebar);

            sidebar.querySelectorAll('.sidebar-menu-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        })();
    </script>
</body>
</html>
