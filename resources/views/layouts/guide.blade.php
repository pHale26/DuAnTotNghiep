<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hệ thống Quản trị TravelWonder</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Chart.js (for dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js & Vite for Echo -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/js/app.js'])

    <style>
        :root {
            --admin-primary: #007CE8;
            --primary-50: #f0f9ff;
            --primary-100: #e0f2fe;
            --primary-200: #bae6fd;
            --primary-300: #7dd3fc;
            --primary-400: #38bdf8;
            --primary-500: #007CE8;
            --primary-600: #0284c7;
            --primary-700: #0369a1;
            --primary-800: #075985;
            --primary-900: #0c4a6e;
            
            --admin-secondary: #f8fafc;
            --admin-sidebar: #0f172a;
            --admin-text-main: #1e293b;
            --admin-text-muted: #475569;
            --admin-border: #cbd5e1;
            --font-family: 'Inter', sans-serif;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 12px 24px rgba(0,0,0,0.08);
            
            --success-base: #16a34a;
            --success-bg: #f0fdf4;
            --success-text: #14532d;
            
            --warning-base: #ca8a04;
            --warning-bg: #fefce8;
            --warning-text: #713f12;
            
            --danger-base: #dc2626;
            --danger-bg: #fef2f2;
            --danger-text: #7f1d1d;
            
            --info-base: #0891b2;
            --info-bg: #ecfeff;
            --info-text: #164e63;
        }

        body {
            background-color: var(--admin-secondary);
            font-family: var(--font-family);
            color: var(--admin-text-main);
            margin: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .min-w-0 {
            min-width: 0 !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 260px;
            background-color: var(--admin-sidebar);
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 15px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); }

        .admin-brand {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            text-align: left;
            padding: 0 25px;
            margin-bottom: 30px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }

        .admin-brand span {
            color: var(--admin-primary);
        }
        
        .admin-brand i {
            font-size: 24px;
            color: var(--admin-primary);
        }

        .sidebar .group-title {
            padding: 15px 25px 8px;
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 1px;
        }

        .sidebar .nav-item { margin-bottom: 2px; }

        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 10px 25px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            margin-right: 12px;
            color: #94a3b8;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .sidebar .nav-link:hover i { color: #fff; }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(0, 124, 232, 0.15);
            border-left: 3px solid var(--admin-primary);
            font-weight: 600;
        }

        .sidebar .nav-link.active i { color: var(--admin-primary); }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 24px 32px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: #fff;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--admin-border);
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--admin-sidebar);
            margin: 0;
        }

        /* Cards & Tables */
        .admin-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--admin-border);
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }
        
        .admin-card-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--admin-border);
            background: #fff;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--admin-sidebar);
        }

        .admin-card-body {
            padding: 24px;
        }

        .table-responsive {
            border-radius: 8px;
        }

        .table {
            margin-bottom: 0;
            vertical-align: middle;
        }

        .table th {
            background-color: #f8fafc;
            color: var(--admin-text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--admin-border);
        }

        .table td {
            padding: 16px;
            color: var(--admin-text-main);
            border-bottom: 1px solid var(--admin-border);
            font-size: 0.9rem;
        }

        .table tbody tr:hover { background-color: #f8fafc; }

        /* Badges */
        .badge-soft {
            padding: 6px 10px;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-soft-success { background: var(--success-bg); color: var(--success-text); border: 1px solid rgba(22, 163, 74, 0.25); }
        .badge-soft-warning { background: var(--warning-bg); color: var(--warning-text); border: 1px solid rgba(202, 138, 4, 0.25); }
        .badge-soft-danger { background: var(--danger-bg); color: var(--danger-text); border: 1px solid rgba(220, 38, 38, 0.25); }
        .badge-soft-primary { background: var(--primary-100); color: var(--primary-800); border: 1px solid rgba(0, 124, 232, 0.25); }
        .badge-soft-secondary { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-soft-info { background: var(--info-bg); color: var(--info-text); border: 1px solid rgba(8, 145, 178, 0.25); }

        /* Buttons */
        .btn-admin {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .btn-admin-primary {
            background-color: var(--admin-primary);
            color: white;
            border: none;
        }
        .btn-admin-primary:hover {
            background-color: #0066cc;
            color: white;
        }
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: var(--admin-text-muted);
            background: #f1f5f9;
            border: none;
            transition: all 0.2s;
        }
        .btn-action:hover {
            background: #e2e8f0;
            color: var(--admin-sidebar);
        }
        .btn-action.text-danger:hover {
            background: #fee2e2;
            color: #dc2626 !important;
        }
        .btn-action.text-primary:hover {
            background: #dbeafe;
            color: var(--admin-primary) !important;
        }

        /* Responsive Sidebar */
        @media (max-width: 991px) {
            .sidebar {
                left: -260px;
                transition: left 0.3s ease;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.4);
                z-index: 999;
                display: none;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        @media (max-width: 767px) {
            .sidebar {
                display: none !important;
            }
            #sidebar-toggle {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 12px 16px calc(70px + env(safe-area-inset-bottom, 0px)) 16px !important;
            }
            .topbar {
                position: sticky;
                top: 0;
                z-index: 998;
                margin-bottom: 16px;
                padding: 12px 16px;
                border-radius: 0;
                margin-left: -16px;
                margin-right: -16px;
                border-left: none;
                border-right: none;
                border-top: none;
                background-color: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(8px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            }
            
            /* Bottom Navigation Bar for Mobile */
            .bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 60px;
                background: #ffffff;
                border-top: 1px solid var(--admin-border);
                display: flex;
                justify-content: space-around;
                align-items: center;
                z-index: 1000;
                box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.08);
                padding-bottom: env(safe-area-inset-bottom, 0px);
            }
            
            .bottom-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: var(--neutral-500);
                text-decoration: none;
                font-size: 0.72rem;
                font-weight: 600;
                width: 100%;
                height: 100%;
                transition: all 0.2s ease;
                padding-top: 6px;
            }
            
            .bottom-nav-item i {
                font-size: 1.25rem;
                margin-bottom: 2px;
                transition: transform 0.2s;
            }
            
            .bottom-nav-item:active i {
                transform: scale(0.85);
            }
            
            .bottom-nav-item.active {
                color: var(--admin-primary);
            }
            
            .bottom-nav-item.active .nav-icon-wrapper {
                background: var(--primary-100);
                color: var(--admin-primary);
                padding: 4px 18px;
                border-radius: 16px;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 2px;
            }
            
            /* High contrast forms on mobile */
            input, select, textarea {
                font-size: 16px !important; /* Prevents iOS auto-zoom */
                padding: 10px 12px !important;
                border-color: #94a3b8 !important; /* High contrast borders */
            }
            
            .card {
                border-radius: var(--radius-md) !important;
                border: 1px solid var(--admin-border) !important;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="{{ url('/') }}" class="admin-brand">
            <i class="bi bi-cursor-fill"></i>
            <div>Travel<span>Wonder</span></div>
        </a>
        <div class="group-title">Bảng điều khiển</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('guide.dashboard') ? 'active' : '' }}" href="{{ route('guide.dashboard') }}">
                    <i class="bi bi-grid-1x2"></i> Tổng quan
                </a>
            </li>
        </ul>

        <div class="group-title">Nghiệp vụ Hướng dẫn viên</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('guide.schedules.*') ? 'active' : '' }}" href="{{ route('guide.schedules.index') }}">
                    <i class="bi bi-calendar-event me-2"></i>
                    Lịch trình Tour
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary d-lg-none" id="sidebar-toggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-none d-md-block">
                    <h1 class="page-title">@yield('page-title', 'Bảng Điều Khiển')</h1>
                </div>
                <div class="d-md-none fw-bold text-dark fs-5 d-flex align-items-center gap-2">
                    <i class="bi bi-cursor-fill text-primary"></i> Travel<span style="color: var(--admin-primary)">Wonder</span>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-light border d-none d-md-inline-flex align-items-center gap-1" title="Xem trang chủ">
                    <i class="bi bi-box-arrow-up-right"></i> <span>Xem Website</span>
                </a>
                
                @include('components.guide-notification-bell')
                
                <div class="dropdown">
                    <a class="text-decoration-none text-dark dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                        @if(Auth::user() && Auth::user()->avatar)
                            <img src="{{ asset(Auth::user()->avatar) }}" alt="avatar" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 35px; height: 35px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        @endif
                        <span class="fw-500 d-none d-md-inline">{{ Auth::user()->name ?? 'Quản trị viên' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width: 200px; border-radius: var(--radius-md);">
                        <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2 text-muted"></i> Hồ sơ cá nhân</a></li>
                        <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2 text-muted"></i> Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-10 text-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i> 
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i> 
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Content Yield -->
        <div class="flex-grow-1">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="text-center text-muted small mt-4 pt-4 border-top">
            &copy; {{ date('Y') }} Hệ thống Quản trị Travel Wonder. Bảo lưu mọi quyền.
        </div>

        <!-- Mobile Bottom Navigation (Moved inside main-content so PJAX updates it dynamically) -->
        <div class="bottom-nav d-flex d-md-none">
            <a class="bottom-nav-item {{ request()->routeIs('guide.dashboard') ? 'active' : '' }}" href="{{ route('guide.dashboard') }}">
                <div class="nav-icon-wrapper">
                    <i class="bi bi-grid-1x2"></i>
                </div>
                <span>Tổng quan</span>
            </a>
            <a class="bottom-nav-item {{ request()->routeIs('guide.schedules.*') ? 'active' : '' }}" href="{{ route('guide.schedules.index') }}">
                <div class="nav-icon-wrapper">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <span>Lịch trình Tour</span>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/pjax-navigation.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            
            if (toggle && sidebar) {
                const overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                document.body.appendChild(overlay);
                
                toggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>