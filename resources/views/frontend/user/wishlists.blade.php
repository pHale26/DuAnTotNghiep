@extends('layouts.master')

@section('title', 'Tour yêu thích - Travel Wonder')

@section('content')
<style>
    /* ===== Shared Profile Layout ===== */
    .profile-page { background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%); min-height: 100vh; }

    .profile-hero {
        background: linear-gradient(135deg, #0B132B 0%, #1a2b4c 40%, #007CE8 100%);
        padding: 60px 0 120px; position: relative; overflow: hidden;
    }
    .profile-hero::before {
        content: ''; position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .profile-hero::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 80px;
        background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%);
        border-radius: 50% 50% 0 0 / 30px 30px 0 0;
    }
    .floating-orb { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.15; pointer-events: none; }
    .orb-1 { width: 300px; height: 300px; background: #007CE8; top: -100px; right: -50px; }
    .orb-2 { width: 200px; height: 200px; background: #F5A623; bottom: 20px; left: 10%; }

    .profile-card-main { margin-top: -80px; position: relative; z-index: 10; }

    /* Sidebar */
    .profile-avatar-section {
        background: white; border-radius: 28px; padding: 36px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.08); border: 1px solid rgba(255,255,255,0.8);
        text-align: center; position: relative; overflow: hidden;
    }
    .profile-avatar-section::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #007CE8, #00c6ff, #F5A623);
    }
    .avatar-ring {
        width: 80px; height: 80px; border-radius: 50%; padding: 3px;
        background: linear-gradient(135deg, #007CE8, #00c6ff, #F5A623);
        display: inline-flex; align-items: center; justify-content: center;
    }
    .avatar-inner { width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 3px solid white; background: white; }
    .avatar-inner img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-initials {
        width: 100%; height: 100%; border-radius: 50%;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; font-weight: 800; color: white;
    }
    .profile-stat-item {
        background: linear-gradient(135deg, #f8faff, #f0f4ff);
        border: 1px solid rgba(0,124,232,0.1); border-radius: 16px; padding: 14px 6px; text-align: center;
        transition: all 0.3s ease;
    }
    .profile-stat-item:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,124,232,0.15); }
    .profile-stat-value {
        font-size: 1.6rem; font-weight: 800;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1;
    }
    .profile-stat-label { font-size: 0.7rem; color: #718096; font-weight: 600; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .user-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, rgba(0,124,232,0.1), rgba(0,198,255,0.1));
        border: 1px solid rgba(0,124,232,0.2); color: #007CE8;
        padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; margin-bottom: 12px;
    }
    .joined-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(0,0,0,0.04); border-radius: 20px; padding: 4px 12px;
        font-size: 0.78rem; color: #6b7280; font-weight: 500;
    }
    .profile-nav-item {
        display: flex; align-items: center; gap: 12px; padding: 13px 18px; border-radius: 14px;
        color: #718096; text-decoration: none; font-weight: 600; font-size: 0.92rem;
        transition: all 0.25s ease; margin-bottom: 6px; position: relative; overflow: hidden;
    }
    .profile-nav-item::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(0,124,232,0.08), rgba(0,198,255,0.05));
        opacity: 0; transition: opacity 0.25s ease; border-radius: 14px;
    }
    .profile-nav-item:hover::before, .profile-nav-item.active::before { opacity: 1; }
    .profile-nav-item:hover, .profile-nav-item.active { color: #007CE8; transform: translateX(4px); }
    .profile-nav-item.active { box-shadow: 0 4px 15px rgba(0,124,232,0.15); }
    .profile-nav-item .nav-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
        background: rgba(0,124,232,0.08); color: #007CE8; transition: all 0.25s ease; flex-shrink: 0;
    }
    .profile-nav-item.active .nav-icon {
        background: linear-gradient(135deg, #007CE8, #0056b3); color: white;
        box-shadow: 0 4px 12px rgba(0,124,232,0.3);
    }

    /* ===== Wish Cards ===== */
    .wish-card {
        background: white; border-radius: 24px; overflow: hidden;
        border: 1px solid #f0f2f5; box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: all 0.35s ease; height: 100%;
        animation: slideInUp 0.4s ease both;
    }
    .wish-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,0.12); border-color: rgba(0,124,232,0.2); }

    @keyframes slideInUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform:translateY(0); } }

    .wish-img-wrap { position: relative; overflow: hidden; height: 200px; }
    .wish-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .wish-card:hover .wish-img-wrap img { transform: scale(1.08); }

    .wish-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 50%, rgba(11,19,43,0.6) 100%);
    }
    .wish-remove-btn {
        position: absolute; top: 14px; right: 14px;
        width: 38px; height: 38px; border-radius: 50%;
        background: rgba(239,68,68,0.9); color: white; border: 2px solid rgba(255,255,255,0.4);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 15px; transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }
    .wish-remove-btn:hover { background: #ef4444; transform: scale(1.1); box-shadow: 0 4px 15px rgba(239,68,68,0.5); }

    .wish-body { padding: 20px; }
    .dest-chip {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(0,124,232,0.08); border: 1px solid rgba(0,124,232,0.15);
        color: #007CE8; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
    }
    .duration-chip {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(0,0,0,0.04); border: 1px solid #e5e7eb;
        color: #6b7280; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
    }
    .wish-title {
        font-weight: 800; color: #0B132B; font-size: 0.95rem; line-height: 1.4;
        margin: 10px 0 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .wish-price { font-size: 1.2rem; font-weight: 800; background: linear-gradient(135deg, #ef4444, #dc2626); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .btn-view-tour {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, #007CE8, #0056b3);
        color: white; border: none; border-radius: 12px; padding: 9px 18px;
        font-weight: 700; font-size: 0.83rem; text-decoration: none;
        transition: all 0.3s ease; box-shadow: 0 4px 14px rgba(0,124,232,0.3);
    }
    .btn-view-tour:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,124,232,0.4); color: white; }

    .empty-state {
        background: white; border-radius: 28px; padding: 70px 40px; text-align: center;
        box-shadow: 0 4px 24px rgba(0,0,0,0.05); border: 1px solid #f0f2f5;
    }
    .empty-icon {
        width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 24px;
        background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(220,38,38,0.05));
        display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #ef4444;
    }

    /* Count badge */
    .content-header { background: white; border-radius: 20px; padding: 20px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid #f0f2f5; display: flex; align-items: center; justify-content: space-between; }
</style>

<div class="profile-page">
    {{-- Hero --}}
    <div class="profile-hero">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="container" style="position:relative;z-index:2;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,0.5);">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50 text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.profile') }}" class="text-white-50 text-decoration-none">Tài khoản</a></li>
                    <li class="breadcrumb-item text-white active">Yêu thích</li>
                </ol>
            </nav>
            <h1 class="fw-800 mb-1" style="color:white;font-size:2rem;"><i class="bi bi-heart-fill me-2" style="color:#ef4444;"></i>Tour yêu thích</h1>
            <p class="mb-0" style="color:rgba(255,255,255,0.7);">Các tour bạn đã lưu lại để xem sau</p>
        </div>
    </div>

    <div class="container profile-card-main pb-5">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="profile-avatar-section reveal-up">
                    <div class="mb-3">
                        <div class="avatar-ring">
                            <div class="avatar-inner">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="avatar">
                                @else
                                    <div class="avatar-initials">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="user-badge"><i class="bi bi-patch-check-fill"></i> Thành viên</div>
                    <h6 class="fw-800 mb-1" style="color:#0B132B;">{{ Auth::user()->name }}</h6>
                    <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>
                    <div class="joined-chip mb-4"><i class="bi bi-calendar3"></i> Tham gia {{ Auth::user()->created_at->format('m/Y') }}</div>

                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ Auth::user()->bookings->count() }}</div>
                                <div class="profile-stat-label">Đơn đặt</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ $wishlists->count() }}</div>
                                <div class="profile-stat-label">Yêu thích</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat-item">
                                <div class="profile-stat-value">{{ Auth::user()->reviews->count() }}</div>
                                <div class="profile-stat-label">Đánh giá</div>
                            </div>
                        </div>
                    </div>

                    <nav>
                        <a href="{{ route('user.profile') }}" class="profile-nav-item">
                            <span class="nav-icon"><i class="bi bi-person-fill"></i></span> Thông tin cá nhân
                        </a>
                        <a href="{{ route('user.bookings') }}" class="profile-nav-item">
                            <span class="nav-icon"><i class="bi bi-bag-check-fill"></i></span> Đơn đặt tour
                        </a>
                        <a href="{{ route('user.wishlists') }}" class="profile-nav-item active">
                            <span class="nav-icon"><i class="bi bi-heart-fill"></i></span> Danh sách yêu thích
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Nội dung --}}
            <div class="col-lg-9">

                {{-- Header bar --}}
                <div class="content-header reveal-up">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#ef4444,#dc2626);display:flex;align-items:center;justify-content:center;color:white;font-size:1.2rem;box-shadow:0 6px 16px rgba(239,68,68,0.3);">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-800 mb-0" style="color:#0B132B;">Tour đã lưu</h5>
                            <p class="text-muted small mb-0">{{ $wishlists->count() }} tour yêu thích</p>
                        </div>
                    </div>
                    @if($wishlists->isNotEmpty())
                    <a href="{{ url('/') }}" class="btn-view-tour" style="background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 4px 14px rgba(16,185,129,0.3);">
                        <i class="bi bi-plus-circle"></i> Thêm tour
                    </a>
                    @endif
                </div>

                @if($wishlists->isNotEmpty())
                <div class="row g-4">
                    @foreach($wishlists as $i => $item)
                    @php
                        $t   = $item->tour;
                        $pi  = $t?->tour_images?->where('is_primary', 1)->first();
                        $img = $pi?->image_url ?? 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=600';
                    @endphp
                    <div class="col-md-6 col-xl-4 reveal-up" style="animation-delay: {{ $i * 0.07 }}s">
                        <div class="wish-card">
                            <div class="wish-img-wrap">
                                <img src="{{ $img }}" alt="{{ $t?->title }}">
                                <div class="wish-overlay"></div>
                                <form method="POST" action="{{ route('user.wishlists.remove') }}"
                                    onsubmit="return confirm('Xóa tour này khỏi danh sách yêu thích?')">
                                    @csrf
                                    <input type="hidden" name="tour_id" value="{{ $t?->id }}">
                                    <button type="submit" class="wish-remove-btn" title="Xóa khỏi yêu thích">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="wish-body">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @if($t?->destination)
                                    <span class="dest-chip"><i class="bi bi-geo-alt-fill"></i>{{ $t->destination->name }}</span>
                                    @endif
                                    @if($t)
                                    <span class="duration-chip"><i class="bi bi-clock"></i>{{ $t->duration_days }} ngày</span>
                                    @endif
                                </div>
                                <div class="wish-title">{{ $t?->title ?? 'Tour không còn tồn tại' }}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Từ</div>
                                        <div class="wish-price">{{ format_currency($t?->getBasePrice() > 0 ? $t->getBasePrice() : ($t?->base_price ?? 0)) }}</div>
                                    </div>
                                    @if($t)
                                    <a href="{{ route('frontend.tours.show', $t->slug) }}" class="btn-view-tour">
                                        <i class="bi bi-arrow-right-circle"></i> Xem tour
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state reveal-up">
                    <div class="empty-icon">
                        <i class="bi bi-heart"></i>
                    </div>
                    <h4 class="fw-800 mb-2" style="color:#0B132B;">Chưa có tour yêu thích</h4>
                    <p class="text-muted mb-4">Lưu những tour bạn thích để tìm lại dễ dàng hơn!</p>
                    <a href="{{ url('/') }}" class="btn-view-tour" style="display:inline-flex;">
                        <i class="bi bi-compass"></i> Khám phá tour ngay
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.reveal-up').forEach(el => {
        new IntersectionObserver(entries => {
            entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('active'); } });
        }, {threshold:0.08}).observe(el);
    });
});
</script>
@endsection
