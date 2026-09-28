@extends('layouts.master')

@section('title', 'Travel Wonder - Đặt Tour & Vé Tham Quan')

@section('content')

<section class="hero-premium">
    <div class="hero-bg">
        @php
            $firstBanner = $banners->first();
            $bgImage = 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070';

            if ($firstBanner && $firstBanner->image_url) {
                $bgImage = \Illuminate\Support\Str::startsWith($firstBanner->image_url, ['http://', 'https://'])
                    ? $firstBanner->image_url
                    : asset(ltrim($firstBanner->image_url, '/'));
            }
        @endphp
        <img src="{{ $bgImage }}" alt="Hero Background">
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1 class="hero-title">{{ __('Khám Phá Thế Giới Cùng TravelWonder') }}</h1>
        <p class="hero-subtitle">
            {{ __('Hàng ngàn điểm đến tuyệt đẹp và trải nghiệm khó quên đang chờ đón bạn. Bắt đầu hành trình ngay hôm nay!') }}
        </p>
    </div>
</section>

<div class="container search-widget-wrapper">
    <div class="glass-panel search-glass">
        <ul class="nav nav-tabs search-tabs" id="searchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tour-tab" data-bs-toggle="tab" data-bs-target="#tour"
                    type="button" role="tab">
                    <i class="bi bi-briefcase-fill me-2"></i>{{ __('Tour Du Lịch') }}
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ai-tab" data-bs-toggle="tab" data-bs-target="#ai"
                    type="button" role="tab">
                    <i class="bi bi-robot me-2 text-primary"></i>{{ __('Gợi ý AI') }}
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <a href="{{ route('frontend.tours.calendar') }}" class="nav-link" style="color: #64748b; font-weight: 700; transition: all 0.2s;" onmouseover="this.style.color='#007CE8'" onmouseout="this.style.color='#64748b'">
                    <i class="bi bi-calendar2-week-fill me-2 text-success"></i>{{ __('Bảng lịch tour') }}
                </a>
            </li>
        </ul>

        <div class="tab-content px-3 pb-3" id="searchTabsContent">
            <div class="tab-pane fade show active" id="tour" role="tabpanel">
                <form action="{{ route('frontend.tours.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                        <div class="input-group autocomplete-wrapper">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text" name="keyword" data-dest-autocomplete
                                class="form-control search-form-control border-start-0 ps-0"
                                placeholder="{{ __('Bạn muốn đi đâu?') }}"
                                value="{{ request('keyword') }}"
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">{{ __('Ngày khởi hành') }}</label>
                        <input type="date" name="date" class="form-control search-form-control" value="{{ request('date') }}" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search-primary w-100">
                            <i class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}
                        </button>
                    </div>
                </form>
            </div>


            <div class="tab-pane fade" id="ai" role="tabpanel">
                <form action="{{ route('frontend.tours.ai_suggest') }}" method="GET" class="row g-3 align-items-end" onsubmit="document.getElementById('ai-loading').style.display='block';">
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold">{{ __('Cảm xúc hiện tại') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-emoji-smile"></i></span>
                            <input type="text" name="emotion" class="form-control search-form-control border-start-0 ps-0" placeholder="{{ __('VD: Vui vẻ, buồn chán, muốn đi biển...') }}" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold">{{ __('Tình hình sức khỏe') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-heart-pulse"></i></span>
                            <input type="text" name="health" class="form-control search-form-control border-start-0 ps-0" placeholder="{{ __('VD: Tốt, hơi mệt, say xe...') }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search-primary w-100">
                            <i class="bi bi-magic me-2"></i>{{ __('Gợi ý') }}
                        </button>
                    </div>
                    <div id="ai-loading" class="col-12 text-center mt-3" style="display: none;">
                        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                        <span class="text-muted ms-2">{{ __('AI đang phân tích và tìm tour phù hợp...') }}</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Destination Slider */
    .dest-slider-section { position: relative; }

    /* Outer: position:relative for abs buttons, NO overflow:hidden */
    .dest-slider-outer {
        position: relative;
        padding: 0 36px; /* space for the nav buttons */
    }
    /* Viewport: clips the sliding track */
    .dest-slider-viewport {
        overflow: hidden;
    }
    .dest-slider-track {
        display: flex;
        gap: 20px;
        will-change: transform;
    }
    .dest-slider-item {
        flex: 0 0 calc(25% - 15px);
        min-width: 0;
    }
    @media (max-width: 991px) {
        .dest-slider-item { flex: 0 0 calc(50% - 10px); }
    }
    @media (max-width: 575px) {
        .dest-slider-item { flex: 0 0 calc(100% - 0px); }
        .dest-slider-outer { padding: 0 28px; }
    }
    .dest-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 20;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: none;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #1e3a5f;
        cursor: pointer;
        transition: background 0.25s, box-shadow 0.25s, transform 0.25s;
        flex-shrink: 0;
    }
    .dest-slider-btn:hover {
        background: var(--primary-color, #007CE8);
        color: #fff;
        box-shadow: 0 6px 24px rgba(0,124,232,0.35);
        transform: translateY(-50%) scale(1.1);
    }
    .dest-slider-btn.prev { left: 0; }
    .dest-slider-btn.next { right: 0; }
    .dest-slider-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
    }
    .dest-slider-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .dest-slider-dot.active {
        background: var(--primary-color, #007CE8);
        width: 24px;
        border-radius: 4px;
    }
    .dest-slider-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 1.5rem;
    }

    /* Ticket Slider Styles */
    .ticket-slider-section { position: relative; }
    .ticket-slider-outer {
        position: relative;
        padding: 0 36px;
    }
    .ticket-slider-viewport {
        overflow: hidden;
    }
    .ticket-slider-track {
        display: flex;
        gap: 20px;
        will-change: transform;
    }
    .ticket-slider-item {
        flex: 0 0 calc(25% - 15px);
        min-width: 0;
    }
    @media (max-width: 991px) {
        .ticket-slider-item { flex: 0 0 calc(50% - 10px); }
    }
    @media (max-width: 575px) {
        .ticket-slider-item { flex: 0 0 calc(100% - 0px); }
        .ticket-slider-outer { padding: 0 28px; }
    }
    .ticket-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 20;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: none;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #1e3a5f;
        cursor: pointer;
        transition: background 0.25s, box-shadow 0.25s, transform 0.25s;
        flex-shrink: 0;
    }
    .ticket-slider-btn:hover {
        background: var(--primary-color, #007CE8);
        color: #fff;
        box-shadow: 0 6px 24px rgba(0,124,232,0.35);
        transform: translateY(-50%) scale(1.1);
    }
    .ticket-slider-btn.prev { left: 0; }
    .ticket-slider-btn.next { right: 0; }
    .ticket-slider-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
    }
    .ticket-slider-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .ticket-slider-dot.active {
        background: var(--primary-color, #007CE8);
        width: 24px;
        border-radius: 4px;
    }

    /* Tour Card Styles */
    .combo-card-img-wrapper {
        position: relative;
    }

    .tour-duration-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        z-index: 10;
        background: rgba(255, 255, 255, 0.95);
        color: #1e3a5f;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 6px 12px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .favorite-form {
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 9999;
        margin: 0;
    }

    .favorite-btn {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 50%;
        background: #ffffff;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(0,0,0,.15);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .favorite-btn i {
        font-size: 22px;
        line-height: 1;
    }

    .favorite-btn:hover {
        transform: scale(1.08);
    }

    .favorite-btn.active {
        color: #ff3366;
    }

    .favorite-btn.active i {
        color: #ff3366;
    }

    /* Tour Preview Overlay Styles */
    .tour-preview-wrapper {
        position: relative;
        cursor: pointer;
    }
</style>

<section class="container py-5 reveal-up dest-slider-section">
    <div class="dest-slider-header">
        <div>
            <h2 class="section-heading">{{ __('Điểm đến thịnh hành') }}</h2>
            <p class="section-subheading mb-0">{{ __('Khám phá những vùng đất được yêu thích nhất.') }}</p>
        </div>
        <a href="{{ route('frontend.destinations.index') }}" class="btn-login-premium text-decoration-none d-none d-md-inline-block"
            style="color:var(--primary-color); border-color:var(--primary-color);">
            {{ __('Xem tất cả') }} <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    @if($destinations->count() > 0)
    <div class="dest-slider-outer">
        <button class="dest-slider-btn prev" id="destPrev" aria-label="Trước">
            <i class="bi bi-chevron-left"></i>
        </button>

        <div class="dest-slider-viewport">
            <div class="dest-slider-track" id="destTrack">
                @foreach($destinations as $dest)
                    <div class="dest-slider-item">
                        <a href="{{ route('frontend.tours.index', ['destination_id' => $dest->id]) }}" class="text-decoration-none">
                            <div class="dest-card-premium">
                                <img src="{{ $dest->image_url ?? 'https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=600' }}"
                                    alt="{{ $dest->name ?? 'Điểm đến' }}"
                                    onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1599839619722-39751411ea63?q=80&w=600';"
                                    loading="lazy">
                                <div class="dest-overlay">
                                    <h5>{{ $dest->name ?? __('Điểm đến') }}</h5>
                                    <span class="dest-count">{{ $dest->tours_count ?? 0 }}+ {{ __('Tours') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <button class="dest-slider-btn next" id="destNext" aria-label="Tiếp">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <div class="dest-slider-dots" id="destDots"></div>
    @else
        <p class="text-muted">{{ __('Đang cập nhật điểm đến.') }}</p>
    @endif
</section>

<script>
(function () {
    const track   = document.getElementById('destTrack');
    const btnPrev = document.getElementById('destPrev');
    const btnNext = document.getElementById('destNext');
    const dotsEl  = document.getElementById('destDots');
    const section = track ? track.closest('.dest-slider-section') : null;

    if (!track) return;

    const GAP    = 20;
    const AUTO   = 3000;
    const DUR    = 450;
    let busy     = false;
    let timer    = null;

    function visible() {
        const w = window.innerWidth;
        if (w < 576) return 1;
        if (w < 992) return 2;
        return 4;
    }

    /* ── Clone all items once at each end for seamless wrap ── */
    const orig  = Array.from(track.querySelectorAll('.dest-slider-item'));
    const N     = orig.length; // real count

    // Append clones of all items at end
    orig.forEach(el => track.appendChild(el.cloneNode(true)));
    // Prepend clones of all items at start (reversed insert order)
    [...orig].reverse().forEach(el => track.insertBefore(el.cloneNode(true), track.firstChild));

    // Layout: [N clones_front] [N real] [N clones_back]
    // Index of first real item = N
    let idx = N;

    /* ── Move track ── */
    function move(i, anim) {
        const all   = track.querySelectorAll('.dest-slider-item');
        const itemW = all[0].offsetWidth;
        track.style.transition = anim ? `transform ${DUR}ms cubic-bezier(.25,.46,.45,.94)` : 'none';
        track.style.transform  = `translateX(-${i * (itemW + GAP)}px)`;
    }

    /* ── Dots ── */
    function realIdx() { return ((idx - N) % N + N) % N; }

    function buildDots() {
        dotsEl.innerHTML = '';
        for (let i = 0; i < N; i++) {
            const d = document.createElement('button');
            d.className = 'dest-slider-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('aria-label', 'Slide ' + (i + 1));
            d.addEventListener('click', () => {
                if (busy) return;
                idx = N + i;
                move(idx, true);
                syncDots();
                resetTimer();
            });
            dotsEl.appendChild(d);
        }
    }
    function syncDots() {
        const ri = realIdx();
        dotsEl.querySelectorAll('.dest-slider-dot').forEach((d, i) =>
            d.classList.toggle('active', i === ri)
        );
    }

    /* ── Navigate one step, wrap silently after animation ── */
    function step(dir) {
        if (busy) return;
        busy = true;
        idx += dir;
        move(idx, true);
        syncDots();

        setTimeout(() => {
            // Silently jump from clone zone back to real zone
            if (idx < N) {
                idx += N;
                move(idx, false);
            } else if (idx >= N * 2) {
                idx -= N;
                move(idx, false);
            }
            requestAnimationFrame(() => requestAnimationFrame(() => { busy = false; }));
        }, DUR + 16);
    }

    /* ── Auto-play ── */
    function startTimer() { stopTimer(); timer = setInterval(() => step(1), AUTO); }
    function stopTimer()  { clearInterval(timer); }
    function resetTimer() { stopTimer(); startTimer(); }

    /* ── Buttons – always enabled, loop infinitely ── */
    btnPrev.addEventListener('click', () => { step(-1); resetTimer(); });
    btnNext.addEventListener('click', () => { step(1);  resetTimer(); });

    /* ── Pause on hover ── */
    if (section) {
        section.addEventListener('mouseenter', stopTimer);
        section.addEventListener('mouseleave', startTimer);
    }

    /* ── Swipe support ── */
    let sx = 0;
    track.addEventListener('touchstart', e => { sx = e.touches[0].clientX; stopTimer(); }, { passive: true });
    track.addEventListener('touchend',   e => {
        const dx = e.changedTouches[0].clientX - sx;
        if (Math.abs(dx) > 50) step(dx < 0 ? 1 : -1);
        resetTimer();
    }, { passive: true });

    /* ── Recalc on resize ── */
    window.addEventListener('resize', () => move(idx, false));

    /* ── Init ── */
    buildDots();
    move(idx, false);
    startTimer();
})();
</script>

<section class="container py-5 reveal-up">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="section-heading">{{ __('Tour Du Lịch Trọn Gói') }}</h2>
            <p class="section-subheading mb-0">{{ __('Trải nghiệm dịch vụ 5 sao với giá ưu đãi.') }}</p>
        </div>

        <a href="{{ route('frontend.tours.index') }}" class="btn-login-premium text-decoration-none d-none d-md-inline-block"
            style="color:var(--primary-color); border-color:var(--primary-color);">
            {{ __('Xem tất cả') }} <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row g-4">
        @forelse($tours as $tour)
            @php
                $tourTitle = $tour->title
                        ?? $tour->name
                        ?? $tour->tour_name
                        ?? 'Chưa có tên tour';

                if (!$tourTitle || trim($tourTitle) === '') {
                    $tourTitle = 'Chưa có tên tour';
                }

                $primaryImage = $tour->tour_images->where('is_primary', 1)->first()
                    ?? $tour->tour_images->first();

                $tourImage = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';

                if ($primaryImage && !empty($primaryImage->image_url)) {
                    if (\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://'])) {
                        $tourImage = $primaryImage->image_url;
                    } else {
                        $tourImage = asset(ltrim($primaryImage->image_url, '/'));
                    }
                }

                $tourSlug = $tour->slug ?? $tour->id;
                $destinationName = optional($tour->destination)->name ?: 'Việt Nam';
            @endphp

            <div class="col-12 col-md-6 col-lg-3">
                <div class="tour-preview-wrapper h-100"
                     x-data="{ showPreview: false }"
                     @mouseenter="showPreview = true"
                     @mouseleave="showPreview = false">
                    
                    <a href="{{ route('frontend.tours.show', $tourSlug) }}" 
                       class="text-decoration-none h-100 d-block"
                       @mouseenter.stop>
                        <div class="combo-card h-100">
                            <div class="combo-card-img-wrapper">
                                @if($tour->duration_days)
                                <div class="tour-duration-badge">
                                    {{ $tour->duration_days }}N{{ $tour->duration_nights > 0 ? $tour->duration_nights . 'Đ' : '' }}
                                </div>
                                @endif

                                <img src="{{ $tourImage }}"
                                     alt="{{ $tourTitle }}"
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';">
                            </div>
                            <div class="combo-card-body">
                                <h3 class="combo-title">{{ $tourTitle }}</h3>
                                <div class="combo-stars">
                                    @php
                                        $stars = $tour->reviews()->avg('rating') ? round($tour->reviews()->avg('rating')) : 0;
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $stars ? 'bi-star-fill' : 'bi-star' }} text-warning"></i>
                                    @endfor
                                </div>
                                <div class="combo-location">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $destinationName }}</span>
                                </div>
                                <div class="combo-footer">
                                    <div>
                                        <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                                        <div class="combo-price-val">{{ format_currency($tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0)) }}</div>
                                    </div>
                                    <span class="btn btn-combo-detail">{{ __('Xem chi tiết') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Tour Preview Component -->
                    <x-tour-preview :tour="$tour" />
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">{{ __('Đang cập nhật tour.') }}</p>
            </div>
        @endforelse
    </div>
</section>

@if(isset($adBanners) && $adBanners->count() > 0)
<section class="container mb-5 reveal-up">
    <div class="position-relative">
        <!-- Navigation Buttons -->
        @if($adBanners->count() > 2)
        <button class="banner-nav-btn banner-nav-prev" type="button" onclick="scrollBanners('prev')">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="banner-nav-btn banner-nav-next" type="button" onclick="scrollBanners('next')">
            <i class="bi bi-chevron-right"></i>
        </button>
        @endif

        <!-- Banners Container -->
        <div class="banner-scroll-container" id="bannerScrollContainer">
            <div class="banner-scroll-wrapper">
                @foreach($adBanners as $ad)
                    @php
                        $adImgSrc = \Illuminate\Support\Str::startsWith($ad->image_url, ['http://', 'https://'])
                            ? $ad->image_url
                            : asset(ltrim($ad->image_url, '/'));
                    @endphp

                    <div class="banner-scroll-item">
                        <a href="{{ $ad->target_url ?? '#' }}"
                            class="d-block overflow-hidden rounded-4 shadow-sm position-relative banner-card"
                            style="height: 200px;">
                            <img src="{{ $adImgSrc }}"
                                alt="{{ $ad->title ?? 'Banner' }}"
                                class="w-100 h-100 object-fit-cover hover-scale"
                                style="transition: transform 0.4s ease;">
                            
                            @if($ad->coupon && $ad->coupon->valid_until >= now())
                                <div class="position-absolute top-0 start-0 m-3">
                                    <div class="coupon-badge bg-danger text-white px-3 py-2 rounded-3 shadow-lg">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-ticket-perforated-fill"></i>
                                            <div>
                                                <div class="fw-bold" style="font-size: 0.75rem;">MÃ: {{ $ad->coupon->code }}</div>
                                                <div style="font-size: 0.7rem;">
                                                    @if($ad->coupon->discount_type === 'percentage')
                                                        Giảm {{ $ad->coupon->discount_value }}%
                                                    @else
                                                        Giảm {{ format_currency($ad->coupon->discount_value) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
    .banner-scroll-container {
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .banner-scroll-container::-webkit-scrollbar {
        display: none;
    }

    .banner-scroll-wrapper {
        display: flex;
        gap: 1.5rem;
        padding: 0.5rem 0;
    }

    .banner-scroll-item {
        flex: 0 0 calc(50% - 0.75rem);
        min-width: 300px;
    }

    @media (min-width: 768px) {
        .banner-scroll-item {
            flex: 0 0 calc(33.333% - 1rem);
        }
    }

    @media (min-width: 1200px) {
        .banner-scroll-item {
            flex: 0 0 calc(25% - 1.125rem);
        }
    }

    .banner-card {
        display: block;
        transition: all 0.3s ease;
    }

    .banner-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }

    .hover-scale:hover {
        transform: scale(1.05);
    }
    
    .coupon-badge {
        backdrop-filter: blur(10px);
        animation: pulse-subtle 2s ease-in-out infinite;
        z-index: 2;
    }
    
    @keyframes pulse-subtle {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    /* Navigation Buttons */
    .banner-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: white;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
        font-size: 1.25rem;
    }

    .banner-nav-btn:hover {
        background: var(--primary-color, #007CE8);
        color: white;
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .banner-nav-prev {
        left: -20px;
    }

    .banner-nav-next {
        right: -20px;
    }

    @media (max-width: 768px) {
        .banner-nav-btn {
            width: 38px;
            height: 38px;
            font-size: 1rem;
        }

        .banner-nav-prev {
            left: -10px;
        }

        .banner-nav-next {
            right: -10px;
        }

        .banner-scroll-item {
            flex: 0 0 calc(80% - 0.75rem);
            min-width: 280px;
        }
    }
</style>

<script>
function scrollBanners(direction) {
    const container = document.getElementById('bannerScrollContainer');
    const items = container.querySelectorAll('.banner-scroll-item');
    
    if (items.length === 0) return;
    
    // Lấy chiều rộng của 1 banner item + gap
    const itemWidth = items[0].offsetWidth;
    const gap = 24; // 1.5rem = 24px
    const scrollAmount = itemWidth + gap;
    
    if (direction === 'prev') {
        container.scrollLeft -= scrollAmount;
    } else {
        container.scrollLeft += scrollAmount;
    }
}

// Auto-scroll on swipe for mobile
let startX = 0;
const container = document.getElementById('bannerScrollContainer');

if (container) {
    container.addEventListener('touchstart', (e) => {
        startX = e.touches[0].pageX;
    });

    container.addEventListener('touchend', (e) => {
        const endX = e.changedTouches[0].pageX;
        const diff = startX - endX;
        
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                scrollBanners('next');
            } else {
                scrollBanners('prev');
            }
        }
    });
}
</script>
@endif


@endsection
