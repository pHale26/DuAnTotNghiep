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

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        :root {
            --admin-primary: #007CE8;
            --admin-secondary: #f8fafc;
            --admin-sidebar: #0f172a;
            --admin-text-main: #334155;
            --admin-text-muted: #64748b;
            --admin-border: #e2e8f0;
            --font-family: 'Inter', sans-serif;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        body {
            background-color: var(--admin-secondary);
            font-family: var(--font-family);
            color: var(--admin-text-main);
            margin: 0;
            overflow-x: hidden;
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
            font-weight: 500;
            font-size: 0.75rem;
            border-radius: 6px;
        }
        .badge-soft-success { background: #dcfce7; color: #166534; }
        .badge-soft-warning { background: #fef9c3; color: #854d0e; }
        .badge-soft-danger { background: #fee2e2; color: #991b1b; }
        .badge-soft-primary { background: #dbeafe; color: #1e40af; }
        .badge-soft-secondary { background: #f1f5f9; color: #475569; }

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

        /* Pagination */
        .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0;
            padding: 0;
            list-style: none;
        }
        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            padding: 0 !important;
            border-radius: 50% !important;
            font-size: 0.9rem;
            font-weight: 500;
            color: #475569;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            box-sizing: border-box;
        }
        .pagination .page-link:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--admin-primary);
        }
        .pagination .page-item.disabled .page-link {
            color: #cbd5e1 !important;
            background-color: #f8fafc !important;
            border-color: #f1f5f9 !important;
            cursor: not-allowed;
        }
    </style>
    @stack('styles')
</head>

<body>
@php
    $unreadChatCount = 0;
    if(Auth::check() && Auth::user()->hasAnyRole(['Admin', 'cskh', 'Staff'])) {
        $user = Auth::user();
        if ($user->hasAnyRole(['Admin', 'Staff'])) {
            $unreadChatCount = \App\Models\Message::whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->whereHas('sender', function($q) {
                    $q->whereDoesntHave('roles', function($r) {
                        $r->whereIn('name', ['Admin', 'cskh', 'Staff']);
                    });
                })->count();
        } elseif ($user->hasAnyRole(['cskh'])) {
            $unreadChatCount = \App\Models\Message::whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->whereHas('sender', function($q) {
                    $q->whereDoesntHave('roles', function($r) {
                        $r->whereIn('name', ['Admin', 'cskh', 'Staff']);
                    });
                })
                ->whereHas('conversation', function($q) use ($user) {
                    $q->where('status', 'open')
                      ->where(function($sq) use ($user) {
                          $sq->where('cskh_id', $user->id)
                            ->orWhereNull('cskh_id');
                      });
                })->count();
        }
    }

    $unassignedToursCount = 0;
    $pendingReportsCount = 0;
    if(Auth::check() && Auth::user()->hasAnyRole(['Admin', 'Staff'])) {
        $unassignedToursCount = \App\Models\TourSchedule::where('departure_date', '>=', now()->startOfDay())
            ->where('departure_date', '<=', now()->startOfDay()->addDays(7))
            ->doesntHave('schedule_guides')
            ->count();
            
        $pendingReportsCount = \App\Models\TourReport::where('status', 'pending')->count() +
            \App\Models\TourAbsenceRequest::whereIn('status', ['pending_review', 'pending_review_urgent'])->count();
    }
@endphp

    <div class="sidebar">
        <a href="{{ url('/') }}" class="admin-brand">
            <i class="bi bi-cursor-fill"></i>
            <div>Travel<span>Wonder</span></div>
        </a>

        <div class="group-title">Bảng điều khiển</div>
        <ul class="nav flex-column mb-3">
            @hasanyrole('Admin|Staff')
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2"></i> Tổng quan
                </a>
            </li>
            @endhasanyrole
            @hasanyrole('Admin|cskh')
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
                    <div><i class="bi bi-chat-dots"></i> Live Chat</div>
                    @if(isset($unreadChatCount) && $unreadChatCount > 0)
                        <span id="admin-chat-badge" class="badge bg-danger rounded-pill">{{ $unreadChatCount }}</span>
                    @else
                        <span id="admin-chat-badge" class="badge bg-danger rounded-pill" style="display: none;">0</span>
                    @endif
                </a>
            </li>
            @endhasanyrole
        </ul>

        @hasanyrole('Admin|Staff')
        <div class="group-title">Nghiệp vụ kinh doanh</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                    <i class="bi bi-calendar-check me-2"></i>
                    Quản lý Booking
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}" href="{{ route('admin.refunds.index') }}">
                    <i class="bi bi-cash-coin me-2"></i>
                    Yêu cầu hoàn tiền
                    @php
                        $pendingRefunds = \App\Models\RefundRequest::where('status', 'pending')->count();
                    @endphp
                    @if($pendingRefunds > 0)
                        <span class="badge bg-danger ms-auto rounded-pill">{{ $pendingRefunds }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <div class="group-title">Quản lý chung</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/tours*') ? 'active' : '' }}" href="{{ route('admin.tours.index') }}">
                    <i class="bi bi-briefcase"></i> Sản phẩm Tour
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/addons*') ? 'active' : '' }}" href="{{ route('admin.addons.index') }}">
                    <i class="bi bi-plus-circle"></i> Dịch vụ Addon
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/ongoing-tours*') ? 'active' : '' }}" href="{{ route('admin.ongoing_tours.index') }}">
                    <div><i class="bi bi-compass"></i> Điều hành Tour</div>
                    @if(isset($unassignedToursCount) && $unassignedToursCount > 0)
                        <span class="badge bg-warning text-dark rounded-pill" title="Sắp khởi hành chưa có HDV">{{ $unassignedToursCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/tour-reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <div><i class="bi bi-file-earmark-check"></i> Báo cáo Tour</div>
                    @if(isset($pendingReportsCount) && $pendingReportsCount > 0)
                        <span class="badge bg-warning text-dark rounded-pill" title="Báo cáo chờ duyệt">{{ $pendingReportsCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/tour-guides*') ? 'active' : '' }}" href="{{ route('admin.tour_guides.index') }}">
                    <i class="bi bi-person-badge"></i> Hướng dẫn viên
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/tickets*') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                    <i class="bi bi-ticket-perforated"></i> Vé tham quan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.coupons.index') }}" class="nav-link">
                    <i class="bi bi-percent"></i>
                    <span>Mã giảm giá</span>
                </a>
            </li>
        </ul>

        <div class="group-title">Cấu hình hệ thống</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                    <i class="bi bi-sliders"></i> Cấu hình chung
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/destinations*') ? 'active' : '' }}" href="{{ route('admin.destinations.index') }}">
                    <i class="bi bi-geo-alt"></i> Điểm đến
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/accommodations*') ? 'active' : '' }}" href="{{ route('admin.accommodations.index') }}">
                    <i class="bi bi-building"></i> Lưu trú (Khách sạn)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i> Danh mục Tour
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/banners*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                    <i class="bi bi-images"></i> Banner quảng cáo
                </a>
            </li>
            @hasanyrole('Admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> Tài khoản & Phân quyền
                </a>
            </li>
            @endhasanyrole
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/holidays*') ? 'active' : '' }}" href="{{ route('admin.holidays.index') }}">
                    <i class="bi bi-calendar-event"></i> Quản lý ngày lễ
                </a>
            </li>
        </ul>

        <div class="group-title">Tiện ích</div>
        <ul class="nav flex-column mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                    <i class="bi bi-star"></i> Đánh giá khách hàng
                </a>
            </li>
        </ul>
        @endhasanyrole

        @role('Guide')
        <div class="group-title">Quản lý chung</div>
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/ongoing-tours*') ? 'active' : '' }}" href="{{ route('admin.ongoing_tours.index') }}">
                    <i class="bi bi-compass"></i> Điều hành Tour
                </a>
            </li>
        </ul>
        @endrole
    </div>

    <div class="main-content">
        <div class="topbar">
            <h1 class="page-title">@yield('page-title', 'Bảng Điều Khiển')</h1>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-light border" title="Xem trang chủ">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Xem Website
                </a>
                
                @include('components.admin-notification-bell')

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
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="min-width: 200px;">
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

        @yield('scripts')
        @stack('scripts')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/pjax-navigation.js') }}"></script>

    @hasanyrole('Admin|cskh|Staff')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lắng nghe realtime tin nhắn mới để cập nhật badge
            setTimeout(() => {
                if (window.Echo) {
                    window.Echo.private('admin.chat')
                        .listen('MessageSent', (e) => {
                            // Nếu người gửi không phải là admin/staff
                            if (e.message && !e.message.is_admin_sender) {
                                const badge = document.getElementById('admin-chat-badge');
                                if (badge) {
                                    badge.style.display = 'inline-block';
                                    let currentCount = parseInt(badge.innerText) || 0;
                                    // Nếu đang không ở trang chat thì mới tăng (trang chat sẽ tự reset count)
                                    if (!window.location.href.includes('/admin/chat')) {
                                        badge.innerText = currentCount + 1;
                                    }
                                }
                            }
                        });
                }
            }, 1000); // Đợi Echo init
        });
    </script>
    @endhasanyrole
</body>

</html>
