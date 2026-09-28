@extends('layouts.master')

@section('title', __('Điểm đến') . ' - Travel Wonder')

@section('content')
<style>
    .dest-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0ea5e9 100%);
        padding: 80px 0 60px;
        position: relative;
        overflow: hidden;
    }
    .dest-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .dest-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .dest-hero-title {
        font-size: 42px;
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
    }
    .dest-hero-sub {
        color: rgba(255,255,255,0.75);
        font-size: 18px;
        max-width: 600px;
    }
    .dest-hero-stats {
        display: flex;
        gap: 40px;
        margin-top: 30px;
    }
    .dest-hero-stat {
        text-align: center;
    }
    .dest-hero-stat-num {
        font-size: 36px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }
    .dest-hero-stat-label {
        font-size: 14px;
        color: rgba(255,255,255,0.6);
        margin-top: 6px;
    }

    /* Card styles */
    .dest-showcase-card {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .dest-showcase-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    .dest-showcase-card img {
        width: 100%;
        height: 360px;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .dest-showcase-card:hover img {
        transform: scale(1.08);
    }
    .dest-showcase-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 30px;
        transition: background 0.3s ease;
    }
    .dest-showcase-card:hover .dest-showcase-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0) 100%);
    }
    .dest-showcase-name {
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 8px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .dest-showcase-desc {
        color: rgba(255,255,255,0.85);
        font-size: 14px;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 16px;
    }
    .dest-showcase-meta {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    .dest-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 30px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .dest-showcase-card:hover .dest-meta-badge {
        background: rgba(255,255,255,0.25);
    }
    .dest-showcase-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        opacity: 0;
        transform: translateX(10px);
        transition: all 0.3s ease;
    }
    .dest-showcase-card:hover .dest-showcase-btn {
        opacity: 1;
        transform: translateX(0);
    }

    /* Featured large card */
    .dest-featured-card img {
        height: 500px;
    }
    .dest-featured-card .dest-showcase-name {
        font-size: 36px;
    }

    @media (max-width: 768px) {
        .dest-hero-title {
            font-size: 28px;
        }
        .dest-hero-stats {
            gap: 24px;
        }
        .dest-hero-stat-num {
            font-size: 28px;
        }
        .dest-showcase-card img {
            height: 260px;
        }
        .dest-featured-card img {
            height: 300px;
        }
        .dest-showcase-name {
            font-size: 22px;
        }
        .dest-featured-card .dest-showcase-name {
            font-size: 26px;
        }
    }
</style>

<!-- Hero -->
<section class="dest-hero">
    <div class="container position-relative" style="z-index: 2;">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.6);">
                        {{ __('Trang chủ') }}
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: rgba(255,255,255,0.9);" aria-current="page">
                    {{ __('Điểm đến') }}
                </li>
            </ol>
        </nav>

        <h1 class="dest-hero-title mb-3">{{ __('Khám Phá Điểm Đến') }}</h1>
        <p class="dest-hero-sub mb-0">{{ __('Từ những bãi biển xanh ngắt đến những đỉnh núi hùng vĩ, chọn điểm đến yêu thích và bắt đầu hành trình của bạn.') }}</p>

        <div class="dest-hero-stats">
            <div class="dest-hero-stat">
                <div class="dest-hero-stat-num">{{ $destinations->count() }}</div>
                <div class="dest-hero-stat-label">{{ __('Điểm đến') }}</div>
            </div>
            <div class="dest-hero-stat">
                <div class="dest-hero-stat-num">{{ $destinations->sum('tours_count') }}+</div>
                <div class="dest-hero-stat-label">{{ __('Tour du lịch') }}</div>
            </div>
            <div class="dest-hero-stat">
                <div class="dest-hero-stat-num">{{ $destinations->sum('tickets_count') }}+</div>
                <div class="dest-hero-stat-label">{{ __('Vé tham quan') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Destinations Grid -->
<section class="py-5" style="background: #f6f8fb;">
    <div class="container">

        @if($destinations->count() >= 2)
            {{-- Featured row: first 2 destinations large --}}
            <div class="row g-4 mb-4">
                @foreach($destinations->take(2) as $dest)
                    <div class="col-md-6">
                        <a href="{{ route('frontend.tours.index', ['destination_id' => $dest->id]) }}" class="text-decoration-none">
                            <div class="dest-showcase-card dest-featured-card">
                                <img src="{{ $dest->image_url ?: 'https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=800' }}"
                                     alt="{{ $dest->name }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=800';"
                                     loading="lazy">
                                <div class="dest-showcase-overlay">
                                    <h2 class="dest-showcase-name">{{ $dest->name }}</h2>
                                    @if($dest->description)
                                        <p class="dest-showcase-desc">{{ $dest->description }}</p>
                                    @endif
                                    <div class="dest-showcase-meta">
                                        <span class="dest-meta-badge">
                                            <i class="bi bi-briefcase-fill"></i> {{ $dest->tours_count }} {{ __('Tours') }}
                                        </span>
                                        <span class="dest-meta-badge">
                                            <i class="bi bi-ticket-perforated-fill"></i> {{ $dest->tickets_count }} {{ __('Vé') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="dest-showcase-btn">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @if($destinations->count() > 2)
            {{-- Remaining destinations --}}
            <div class="row g-4">
                @foreach($destinations->skip(2) as $dest)
                    <div class="col-md-6 col-lg-3">
                        <a href="{{ route('frontend.tours.index', ['destination_id' => $dest->id]) }}" class="text-decoration-none">
                            <div class="dest-showcase-card">
                                <img src="{{ $dest->image_url ?: 'https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=800' }}"
                                     alt="{{ $dest->name }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=800';"
                                     loading="lazy">
                                <div class="dest-showcase-overlay">
                                    <h3 class="dest-showcase-name">{{ $dest->name }}</h3>
                                    @if($dest->description)
                                        <p class="dest-showcase-desc">{{ $dest->description }}</p>
                                    @endif
                                    <div class="dest-showcase-meta">
                                        <span class="dest-meta-badge">
                                            <i class="bi bi-briefcase-fill"></i> {{ $dest->tours_count }} {{ __('Tours') }}
                                        </span>
                                        <span class="dest-meta-badge">
                                            <i class="bi bi-ticket-perforated-fill"></i> {{ $dest->tickets_count }} {{ __('Vé') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="dest-showcase-btn">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</section>

@endsection
