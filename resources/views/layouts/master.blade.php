<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Travel Wonder - Nền tảng du lịch hàng đầu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/premium-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tour-preview.css') }}">
    <style>
    #mainHeader {
        transition:
            background-color 0.25s ease,
            box-shadow 0.25s ease,
            backdrop-filter 0.25s ease;
    }

    #mainHeader.navbar-solid {
        background: #ffffff !important;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.12) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    #mainHeader.navbar-solid .nav-link,
    #mainHeader.navbar-solid .navbar-brand,
    #mainHeader.navbar-solid .dropdown-toggle {
        color: #1f2937 !important;
    }
</style>
    @vite(['resources/js/app.js'])
    <style>
        /* Unify pagination styling to circular style */
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
            color: #007CE8;
        }
        .pagination .page-item.active .page-link {
            background-color: #007CE8 !important;
            border-color: #007CE8 !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(0, 124, 232, 0.3);
            font-weight: 600;
        }
        .pagination .page-item.disabled .page-link {
            color: #cbd5e1 !important;
            background-color: #f8fafc !important;
            border-color: #f1f5f9 !important;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    @auth
        @hasanyrole('Admin|Staff|cskh|Guide')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light border shadow-sm" style="position: fixed; top: 12px; left: 20px; z-index: 999999; border-radius: 8px; font-weight: 500; font-family: 'Inter', sans-serif; display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #fff; border: 1px solid #dee2e6; color: #212529; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; transition: all 0.2s;" onmouseover="this.style.background='#f8f9fa'; this.style.borderColor='#c1c9d0';" onmouseout="this.style.background='#fff'; this.style.borderColor='#dee2e6';">
                <i class="bi bi-speedometer2 text-primary"></i> Quay lại quản trị
            </a>
        @endhasanyrole
    @endauth

    <nav id="mainHeader"
     data-home-page="{{ request()->is('/') ? '1' : '0' }}"
     class="navbar navbar-expand-lg navbar-premium fixed-top {{ request()->is('/') ? '' : 'navbar-solid' }} flex-column p-0">
        <!-- Top Row (Desktop Only) -->
        <div class="w-100 d-none d-lg-block border-bottom" style="border-color: rgba(128, 128, 128, 0.2) !important; background: rgba(0,0,0,0.03);">
            <div class="container d-flex justify-content-end py-1">
                <ul class="navbar-nav flex-row align-items-center gap-3" style="font-size: 0.9rem;">

                    <!-- Support -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center py-1" href="{{ route('frontend.support.index') }}" style="font-weight: 600;">
                            <i class="bi bi-question-circle me-1"></i> {{ __('Hỗ trợ') }}
                        </a>
                    </li>

                    <div class="vr mx-1 bg-secondary" style="width: 1px; opacity: 0.3; height: 16px; align-self: center;"></div>

                    @guest

                   <li class="nav-item"><a class="nav-link py-1" href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" style="font-weight: 600;">{{ __('Đăng nhập') }}</a></li>
                    <li class="nav-item">
                        <a class="nav-link py-1" href="{{ route('register') }}" style="font-weight: 600;">{{ __('Đăng ký') }}</a>
                    </li>
                    @else
                    <x-notification-bell />
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center py-1" href="#" id="userDropdownTop" role="button" data-bs-toggle="dropdown">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset(Auth::user()->avatar) }}" alt="avatar" class="rounded-circle me-2" style="width: 28px; height: 28px; object-fit: cover;">
                            @else
                                <i class="bi bi-person-circle me-1 fs-5"></i>
                            @endif
                            <span style="font-weight: 600;">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; margin-top: 5px;">
                            <li><a class="dropdown-item py-2" href="{{ route('user.profile') }}"><i class="bi bi-person me-2"></i> {{ __('Hồ sơ của tôi') }}</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('user.bookings') }}"><i class="bi bi-calendar-check me-2"></i> {{ __('Tour của tôi') }}</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('user.profile') }}?tab=wishlists"><i class="bi bi-heart me-2"></i> {{ __('Danh sách đã lưu') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> {{ __('Đăng xuất') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>

        <!-- Main Row -->
        <div class="w-100 py-3 py-lg-2">
            <div class="container d-flex flex-wrap align-items-center justify-content-between">
                <a class="navbar-brand mb-0" href="{{ url('/') }}">
                    <i class="bi bi-cursor-fill"></i> Travel<span>Wonder</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-1 gap-lg-4">
                        <li class="nav-item"><a class="nav-link fs-6" href="{{ url('/') }}">{{ __('Trang chủ') }}</a></li>
                        <li class="nav-item"><a class="nav-link fs-6" href="{{ route('frontend.destinations.index') }}">{{ __('Điểm đến') }}</a></li>
                        <li class="nav-item"><a class="nav-link fs-6" href="{{ route('frontend.tours.index') }}">{{ __('Tour trọn gói') }}</a></li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link fs-6" href="{{ route('user.profile') }}?tab=wishlists">
                                Tour đã lưu
                            </a>
                        </li>
                        @endauth
                        {{-- <li class="nav-item"><a class="nav-link fs-6" href="{{ route('frontend.tickets.index') }}">{{ __('Vé tham quan') }}</a></li> --}}
                    </ul>

                    <!-- Mobile Only Top Utilities -->
                    <ul class="navbar-nav d-lg-none mt-3 border-top pt-3 gap-2">

                        <li class="nav-item"><a class="nav-link" href="{{ route('frontend.support.index') }}"><i class="bi bi-question-circle me-2"></i> {{ __('Hỗ trợ') }}</a></li>

                        @guest
                        <li class="nav-item mt-2"><a class="btn-login-premium text-decoration-none d-block text-center" href="{{ route('login', ['redirect' => request()->fullUrl()]) }}">{{ __('Đăng nhập') }}</a></li>
                        <li class="nav-item"><a class="btn-register-premium text-decoration-none d-block text-center" href="{{ route('register') }}">{{ __('Đăng ký') }}</a></li>
                        @else
                        <x-notification-bell />
                        <li class="nav-item dropdown mt-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdownMobile" role="button" data-bs-toggle="dropdown">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset(Auth::user()->avatar) }}" alt="avatar" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;"><i class="bi bi-person-fill"></i></div>
                                @endif
                                <span class="fw-bold text-dark">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="bi bi-person me-2"></i> {{ __('Hồ sơ của tôi') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.bookings') }}"><i class="bi bi-calendar-check me-2"></i> {{ __('Tour của tôi') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.profile') }}?tab=wishlists"><i class="bi bi-heart me-2"></i> {{ __('Danh sách đã lưu') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> {{ __('Đăng xuất') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="{{ request()->is('/') ? '' : 'pt-5 mt-4' }}">
        @if(session('success') || session('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 110px; min-width: 300px;">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-lg border-0" role="alert" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span class="fw-500">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-lg border-0" role="alert" style="border-radius: 12px;">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <span class="fw-500">{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer-premium">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <a class="navbar-brand text-white text-decoration-none d-inline-block mb-3" href="{{ url('/') }}"
                        style="font-size: 28px;">
                        <i class="bi bi-cursor-fill text-primary"></i> Travel<span class="text-white">Wonder</span>
                    </a>
                    <p class="mb-4">Đối tác du lịch đáng tin cậy của bạn. Chúng tôi mang đến những trải nghiệm du lịch
                        tuyệt vời với mức giá tốt nhất, kết nối hàng ngàn điểm đến trên toàn thế giới.</p>
                    <div class="d-flex">
                        <a href="#" class="social-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="social-circle"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-title">{{ __('Về Travel') }}</h5>
                    <a href="{{ url('/') }}" class="footer-link">{{ __('Trang chủ') }}</a>
                    <a href="{{ route('frontend.destinations.index') }}" class="footer-link">{{ __('Điểm đến') }}</a>
                    <a href="#" class="footer-link">{{ __('Tuyển dụng') }}</a>
                    <a href="#" class="footer-link">{{ __('Liên hệ') }}</a>
                    @auth
                        <a href="{{ route('user.profile') }}" class="footer-link">{{ __('Hồ sơ của tôi') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="footer-link">{{ __('Đăng nhập') }}</a>
                    @endauth
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">{{ __('Sản phẩm') }}</h5>
                    <a href="{{ route('frontend.tours.index') }}" class="footer-link">{{ __('Tour trọn gói') }}</a>
                    {{-- <a href="{{ route('frontend.tickets.index') }}" class="footer-link">{{ __('Vé tham quan') }}</a> --}}
                    @auth
                        <a href="{{ route('user.profile') }}?tab=wishlists" class="footer-link">{{ __('Tour đã lưu') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="footer-link">{{ __('Tour đã lưu') }}</a>
                    @endauth
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-title">{{ __('Khác') }}</h5>
                    <a href="{{ route('frontend.support.index') }}" class="footer-link">{{ __('Trung tâm hỗ trợ') }}</a>
                    <a href="#" class="footer-link">{{ __('Chính sách bảo mật') }}</a>
                    <a href="#" class="footer-link">{{ __('Điều khoản sử dụng') }}</a>
                    <a href="#" class="footer-link">{{ __('Quy chế hoạt động') }}</a>
                    <a href="#" class="footer-link">{{ __('Đăng ký đối tác') }}</a>
                    <a href="#" class="footer-link">{{ __('Chương trình đại lý') }}</a>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="mb-0">&copy; {{ date('Y') }} Travel Wonder. Bảo lưu mọi quyền.</p>
            </div>
        </div>
    </footer>

    <!-- Chatbot Component -->
    @include('frontend.components.chatbot')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/animations.js') }}"></script>
    <script src="{{ asset('js/favorite-handler.js') }}"></script>

    {{-- Cookie Consent Banner --}}
    <x-cookie-consent />

    <x-chatbox />

<script>
    (function () {
        const header = document.getElementById('mainHeader');

        if (!header) {
            return;
        }

        const isHomePage = header.dataset.homePage === '1';

        function updateHeaderState() {
            const shouldBeSolid = !isHomePage || window.scrollY > 20;

            header.classList.toggle('navbar-solid', shouldBeSolid);
        }

        // Chạy ngay khi trình duyệt đọc tới đoạn script.
        updateHeaderState();

        // Chạy khi toàn bộ HTML đã tải.
        document.addEventListener('DOMContentLoaded', updateHeaderState);

        // Chạy khi ảnh, CSS và tài nguyên đã tải xong.
        window.addEventListener('load', updateHeaderState);

        // Xử lý khi tải lại trang tại vị trí đang cuộn.
        window.addEventListener('pageshow', function () {
            requestAnimationFrame(updateHeaderState);
            setTimeout(updateHeaderState, 100);
        });

        // Cập nhật khi người dùng cuộn.
        window.addEventListener('scroll', updateHeaderState, {
            passive: true
        });

        window.addEventListener('resize', updateHeaderState, {
            passive: true
        });

        requestAnimationFrame(updateHeaderState);
    })();
</script>

@stack('scripts')
</body>


</html>
