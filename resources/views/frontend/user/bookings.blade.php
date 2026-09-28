@extends('layouts.master')

@section('page-title', __('Đặt chỗ của tôi'))
@section('meta-description', __('Xem lịch sử và trạng thái các tour du lịch bạn đã đặt.'))

@section('content')
<style>
/* ===================== BOOKING PAGE STYLES ===================== */
.my-bookings-hero {
    background: linear-gradient(135deg, #0f2044 0%, #1a3a6e 40%, #007ce8 100%);
    padding: 72px 0 48px;
    position: relative;
    overflow: hidden;
}
.my-bookings-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.my-bookings-hero .hero-content { position: relative; z-index: 1; }

/* Stats row */
.booking-stat-pill {
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 50px;
    padding: 10px 22px;
    color: #fff;
    font-size: 0.88rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* Tab navigation */
.booking-tabs {
    background: #fff;
    border-bottom: 1px solid #e8edf5;
    position: sticky;
    top: 0;
    z-index: 50;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.booking-tab-btn {
    padding: 18px 28px;
    border: none;
    background: transparent;
    font-weight: 600;
    font-size: 0.92rem;
    color: #6b7280;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}
.booking-tab-btn.active {
    color: #007ce8;
    border-bottom-color: #007ce8;
    background: rgba(0,124,232,0.04);
}
.booking-tab-btn:hover:not(.active) {
    color: #374151;
    background: rgba(0,0,0,0.03);
}
.tab-count-badge {
    background: #e8f0fd;
    color: #007ce8;
    border-radius: 20px;
    padding: 2px 9px;
    font-size: 0.78rem;
    font-weight: 700;
}
.booking-tab-btn.active .tab-count-badge {
    background: #007ce8;
    color: #fff;
}

/* Booking Card */
.bk-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e8edf5;
    overflow: hidden;
    transition: all 0.25s ease;
    margin-bottom: 24px;
}
.bk-card:hover {
    border-color: rgba(0,124,232,0.25);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

/* Card Header */
.bk-card-header {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #e8edf5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.bk-order-id {
    font-size: 0.82rem;
    font-weight: 700;
    color: #374151;
    letter-spacing: 0.5px;
}
.bk-date-text {
    font-size: 0.8rem;
    color: #9ca3af;
    font-weight: 500;
}

/* Tour Status Badges */
.ts-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.3px;
}
.ts-upcoming    { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.ts-in_progress { background: #fff7ed; color: #d97706; border: 1px solid #fed7aa; }
.ts-checking_in { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.ts-completed   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.ts-cancelled   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

/* Tour Image */
.bk-tour-img {
    width: 90px;
    height: 90px;
    border-radius: 14px;
    object-fit: cover;
    flex-shrink: 0;
}
.bk-tour-img-placeholder {
    width: 90px;
    height: 90px;
    border-radius: 14px;
    background: linear-gradient(135deg, #dbeafe, #e0e7ff);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 2rem;
    color: #6366f1;
}

/* Payment section */
.payment-box {
    background: #f8fafc;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    padding: 20px;
}
.payment-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
    padding: 5px 0;
}
.payment-row .label { color: #6b7280; }
.payment-row .value { font-weight: 600; color: #111827; }
.payment-total-row {
    border-top: 1px dashed #d1d5db;
    margin-top: 10px;
    padding-top: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.payment-total-label { font-weight: 700; color: #111827; font-size: 0.9rem; }
.payment-total-value { font-weight: 800; color: #007ce8; font-size: 1.2rem; }

/* Payment status badge */
.ps-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 700;
    width: 100%;
    justify-content: center;
    margin-top: 12px;
}
.ps-pending  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.ps-paid30   { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.ps-paid100  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.ps-failed   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.ps-refunded { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }

/* Progress bar for deposit */
.deposit-progress {
    height: 8px;
    border-radius: 8px;
    background: #e5e7eb;
    overflow: hidden;
    margin: 8px 0;
}
.deposit-progress-fill {
    height: 100%;
    border-radius: 8px;
    background: linear-gradient(90deg, #007ce8, #38bdf8);
    transition: width 1s ease;
}

/* Action buttons */
.bk-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.bk-btn-primary { background: #007ce8; color: #fff; }
.bk-btn-primary:hover { background: #005bb5; color: #fff; transform: translateY(-1px); }
.bk-btn-info    { background: #0ea5e9; color: #fff; }
.bk-btn-info:hover { background: #0284c7; color: #fff; transform: translateY(-1px); }
.bk-btn-outline { background: transparent; color: #007ce8; border: 1.5px solid #007ce8; }
.bk-btn-outline:hover { background: #eff6ff; }
.bk-btn-danger  { background: #ef4444; color: #fff; }
.bk-btn-danger:hover { background: #dc2626; color: #fff; }

/* Passenger chips */
.passenger-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #eff6ff;
    color: #1d4ed8;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 0.76rem;
    font-weight: 600;
    border: 1px solid #bfdbfe;
}
.passenger-chip.child {
    background: #fdf4ff;
    color: #7c3aed;
    border-color: #e9d5ff;
}

/* Empty state */
.empty-bookings {
    text-align: center;
    padding: 80px 20px;
}
.empty-icon-circle {
    width: 110px; height: 110px;
    border-radius: 50%;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    font-size: 3rem;
    color: #3b82f6;
}

/* Checkin progress indicator */
.checkin-step-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(22,163,74,0.1);
    color: #16a34a;
    border: 1px solid rgba(22,163,74,0.25);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.78rem;
    font-weight: 600;
}

/* Timeline dot for status */
.status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.dot-blue   { background: #2563eb; }
.dot-orange { background: #d97706; }
.dot-green  { background: #16a34a; }
.dot-red    { background: #dc2626; }
.dot-gray   { background: #9ca3af; }

/* Pulsing dot for live status */
.status-dot.pulse {
    animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 0 rgba(234,179,8,0.5); }
    50% { box-shadow: 0 0 0 5px transparent; }
}

/* Tab content animation */
.tab-pane { animation: fadeInTab 0.3s ease; }
@keyframes fadeInTab {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .bk-tour-img, .bk-tour-img-placeholder { width: 70px; height: 70px; }
    .bk-card-header { padding: 12px 16px; }
    .bk-card .card-body-inner { padding: 16px !important; }
}
</style>

{{-- ===== HERO SECTION ===== --}}
<div class="my-bookings-hero">
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        🗺️
                    </div>
                    <div>
                        <h1 class="text-white fw-bold mb-0" style="font-size:clamp(1.5rem,4vw,2.2rem);">{{ __('Đặt chỗ của tôi') }}</h1>
                        <p class="text-white opacity-75 mb-0 small">{{ __('Quản lý tất cả hành trình và trạng thái thanh toán của bạn') }}</p>
                    </div>
                </div>
                {{-- Stats pills --}}
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <span class="booking-stat-pill">
                        <i class="bi bi-list-check"></i>
                        {{ $bookings->count() }} {{ __('Đơn tổng') }}
                    </span>
                    @if($activeBookings->count() > 0)
                    <span class="booking-stat-pill" style="background:rgba(234,179,8,0.2);border-color:rgba(234,179,8,0.3);">
                        <i class="bi bi-clock"></i>
                        {{ $activeBookings->count() }} {{ __('Đang diễn ra') }}
                    </span>
                    @endif
                    @if($bookings->where('payment_status', 'paid_30')->count() > 0)
                    <span class="booking-stat-pill" style="background:rgba(59,130,246,0.2);border-color:rgba(59,130,246,0.3);">
                        <i class="bi bi-credit-card"></i>
                        {{ $bookings->where('payment_status', 'paid_30')->count() }} {{ __('Chờ thanh toán nốt') }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div style="font-size:7rem;opacity:0.12;line-height:1;">✈️</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== TAB NAVIGATION ===== --}}
<div class="booking-tabs">
    <div class="container">
        <div class="d-flex overflow-auto" style="gap:0;">
            <button class="booking-tab-btn active" onclick="switchTab('all', this)" id="tab-all">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                {{ __('Tất cả') }}
                <span class="tab-count-badge">{{ $bookings->count() }}</span>
            </button>
            <button class="booking-tab-btn" onclick="switchTab('active', this)" id="tab-active">
                <i class="bi bi-play-circle-fill"></i>
                {{ __('Đang diễn ra') }}
                <span class="tab-count-badge">{{ $activeBookings->count() }}</span>
            </button>
            <button class="booking-tab-btn" onclick="switchTab('past', this)" id="tab-past">
                <i class="bi bi-archive-fill"></i>
                {{ __('Đã kết thúc') }}
                <span class="tab-count-badge">{{ $pastBookings->count() }}</span>
            </button>
            <button class="booking-tab-btn" onclick="switchTab('pending_payment', this)" id="tab-pending_payment">
                <i class="bi bi-credit-card-fill"></i>
                {{ __('Chờ thanh toán') }}
                <span class="tab-count-badge">{{ $bookings->whereIn('payment_status', ['pending','failed','paid_30'])->count() }}</span>
            </button>
        </div>
    </div>
</div>

{{-- ===== MAIN CONTENT ===== --}}
<div class="container py-5">
    @if(isset($pendingLinkedBookings) && $pendingLinkedBookings->count() > 0)
    <div class="alert alert-info shadow-sm mb-4" style="border-left: 4px solid #0dcaf0;">
        <h5 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i>Có đơn hàng đang chờ bạn nhận</h5>
        <p class="mb-0">Chúng tôi tìm thấy <strong>{{ $pendingLinkedBookings->count() }}</strong> đơn hàng (đã được tạo bằng email <strong>{{ Auth::user()->email }}</strong>) nhưng chưa được liên kết với tài khoản này.</p>
        <div class="mt-3">
            @foreach($pendingLinkedBookings as $pendingBooking)
                <div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-white rounded border">
                    <div>
                        <strong>{{ $pendingBooking->code }}</strong> - {{ $pendingBooking->tour_schedule->tour->title ?? 'Tour không xác định' }}
                        <span class="text-muted ms-2">{{ \Carbon\Carbon::parse($pendingBooking->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <form action="{{ route('frontend.user.bookings.link', $pendingBooking->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="action" value="accept">
                            <button class="btn btn-sm btn-primary">Nhận đơn hàng</button>
                        </form>
                        <form action="{{ route('frontend.user.bookings.link', $pendingBooking->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="action" value="ignore">
                            <button class="btn btn-sm btn-outline-secondary">Không phải của tôi</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($bookings->isEmpty())
    {{-- Empty State --}}
    <div class="empty-bookings reveal-up">
        <div class="empty-icon-circle">✈️</div>
        <h3 class="fw-bold text-dark mb-2">{{ __('Chưa có chuyến đi nào') }}</h3>
        <p class="text-muted mb-5" style="max-width:400px;margin:0 auto;">{{ __('Bạn chưa đặt tour nào. Hãy khám phá hàng trăm điểm đến tuyệt đẹp đang chờ bạn!') }}</p>
        <a href="{{ route('frontend.tours.index') }}" class="bk-btn bk-btn-primary" style="font-size:1rem;padding:14px 36px;">
            <i class="bi bi-compass"></i> {{ __('Khám phá Tours ngay') }}
        </a>
    </div>

    @else

    {{-- Booking list container --}}
    <div id="bookings-container">
        @foreach($bookings as $booking)
        @php
            $tour = $booking->tour_schedule->tour ?? null;
            $schedule = $booking->tour_schedule ?? null;
            $primaryImg = $tour?->primaryImage?->image_url
                        ?? $tour?->tour_images->firstWhere('is_primary', 1)?->image_url
                        ?? $tour?->tour_images->first()?->image_url
                        ?? null;
            $tourStatus = $booking->tour_status;
            $paymentStatus = $booking->payment_status ?? 'pending';
            $paymentMethod = $booking->payment_method ?? 'transfer';
            $paymentType   = $booking->payment_type ?? 'full';
            $isCancelled   = in_array($tourStatus, [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER]);
            $isActive      = in_array($tourStatus, [\App\Models\Booking::TOUR_UPCOMING, \App\Models\Booking::TOUR_IN_PROGRESS, \App\Models\Booking::TOUR_CHECKING_IN]);
            $isPendingPay  = in_array($paymentStatus, ['pending', 'failed', 'paid_30']);

            // Tour status config
            $tsConfig = [
                'upcoming'             => ['ts-upcoming',    'dot-blue',   'bi-calendar-check-fill', 'Sắp khởi hành'],
                'in_progress'          => ['ts-in_progress', 'dot-orange', 'bi-play-circle-fill',    'Đang thực hiện'],
                'checking_in'          => ['ts-checking_in', 'dot-green',  'bi-geo-alt-fill',        'Đang Check-in'],
                'completed'            => ['ts-completed',   'dot-green',  'bi-check-circle-fill',   'Hoàn thành'],
                'cancelled_by_customer'=> ['ts-cancelled',   'dot-red',    'bi-x-circle-fill',       'Đã hủy (Bạn)'],
                'cancelled_by_admin'   => ['ts-cancelled',   'dot-red',    'bi-x-circle-fill',       'Đã hủy (Admin)'],
            ];
            $tsCfg = $tsConfig[$tourStatus] ?? ['ts-upcoming', 'dot-gray', 'bi-question-circle', $tourStatus];

            // Data attributes for filtering
            $tabAttr = 'all';
            if ($isActive)     $tabAttr .= ' active';
            if (!$isActive)    $tabAttr .= ' past';
            if ($isPendingPay) $tabAttr .= ' pending_payment';

            // Deposit info
            $depositAmt  = $booking->deposit_amount;
            $remainAmt   = $booking->remaining_amount;
            $paidAmt     = (float)($booking->paid_amount ?? 0);
            $paidPercent = $booking->paid_percent;
        @endphp

        <div class="bk-card reveal-up" data-tab="{{ $tabAttr }}" data-booking-id="{{ $booking->id }}">

            {{-- ===== CARD HEADER ===== --}}
            <div class="bk-card-header">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <div class="bk-order-id">
                            <i class="bi bi-hash text-primary me-1"></i>{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="bk-date-text mt-1">
                            <i class="bi bi-clock me-1"></i>{{ $booking->created_at->format('H:i — d/m/Y') }}
                        </div>
                    </div>
                    @if($tourStatus === 'in_progress' || $tourStatus === 'checking_in')
                    <div style="width:8px;height:8px;border-radius:50%;background:#d97706;animation:pulse-dot 1.5s infinite;"></div>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Tour Status --}}
                    <span class="ts-badge {{ $tsCfg[0] }}">
                        <span class="status-dot {{ $tsCfg[1] }} {{ in_array($tourStatus,['in_progress','checking_in']) ? 'pulse' : '' }}"></span>
                        <i class="bi {{ $tsCfg[2] }}"></i>
                        {{ $tsCfg[3] }}
                        @if($tourStatus === 'checking_in' && $booking->current_checkin_step)
                            — {{ $booking->current_checkin_step }}
                        @endif
                    </span>

                    {{-- Payment type chip --}}
                    @if($paymentType === 'deposit')
                    <span style="font-size:0.75rem;font-weight:600;color:#7c3aed;background:#fdf4ff;border:1px solid #e9d5ff;border-radius:20px;padding:3px 10px;">
                        <i class="bi bi-layers-half me-1"></i>{{ __('Đặt cọc 30%') }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- ===== CARD BODY ===== --}}
            <div class="card-body-inner p-4 p-md-5" style="padding:28px;">
                <div class="row g-4">

                    {{-- LEFT: Tour Info --}}
                    <div class="col-lg-7">
                        <div class="d-flex gap-3 align-items-start">
                            {{-- Tour image --}}
                            @if($primaryImg)
                                <img src="{{ $primaryImg }}" alt="{{ $tour->title }}" class="bk-tour-img d-none d-sm-block">
                            @else
                                <div class="bk-tour-img-placeholder d-none d-sm-flex">🏔️</div>
                            @endif

                            <div class="flex-grow-1">
                                <div class="text-muted small fw-600 mb-1 text-uppercase" style="font-size:0.72rem;letter-spacing:1px;">
                                    {{ __('Thông tin Tour') }}
                                </div>
                                <h5 class="fw-bold text-dark mb-2 lh-sm" style="font-size:1.05rem;">
                                    {{ $tour->title ?? __('Tour không tồn tại') }}
                                </h5>

                                                                {{-- Tour meta --}}
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($tour?->destination)
                                        <span style="font-size:0.8rem;color:#6b7280;display:flex;align-items:center;gap:4px;">
                                            <i class="bi bi-geo-alt-fill text-danger"></i>
                                            {{ $tour->destination->name }}
                                        </span>
                                    @endif

                                    @if($schedule)
                                        <span style="font-size:0.8rem;color:#6b7280;display:flex;align-items:center;gap:4px;">
                                            <i class="bi bi-calendar-event-fill text-success"></i>

                                            {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}

                                            @if($tour && $tour->departure_time)
                                                ({{ \Carbon\Carbon::parse($tour->departure_time)->format('H\hi') }})
                                            @endif
                                        </span>

                                        @if($schedule->return_date)
                                            <span style="font-size:0.8rem;color:#6b7280;display:flex;align-items:center;gap:4px;">
                                                <i class="bi bi-arrow-return-left text-warning"></i>
                                                {{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                @php
                                    $meetingPoint = $booking->meeting_point
                                        ?: ($tour->meeting_point ?? null);
                                @endphp

                                {{-- Điểm tập kết --}}
                                <div class="d-flex align-items-start gap-2 mb-3"
                                    style="
                                        background:#f0fdf4;
                                        border:1px solid #bbf7d0;
                                        border-radius:10px;
                                        padding:9px 12px;
                                    ">

                                    <i class="bi bi-geo-alt-fill text-success mt-1"></i>

                                    <div class="flex-grow-1">
                                        <div class="text-muted"
                                            style="
                                                font-size:0.7rem;
                                                font-weight:700;
                                                text-transform:uppercase;
                                                letter-spacing:0.5px;
                                            ">
                                            {{ __('Điểm tập kết') }}
                                        </div>

                                        <div class="fw-semibold text-dark"
                                            style="font-size:0.84rem;word-break:break-word;">
                                            {{ $meetingPoint ?: __('Chưa cập nhật') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Passengers --}}
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="passenger-chip">
                                        <i class="bi bi-person-fill"></i>
                                        {{ $booking->adults_count }} {{ __('người lớn') }}
                                    </span>

                                    @if($booking->children_count > 0)
                                        <span class="passenger-chip child">
                                            <i class="bi bi-person-fill"></i>
                                            {{ $booking->children_count }} {{ __('trẻ em') }}
                                        </span>
                                    @endif
                                </div>
                                {{-- Transport --}}
                                <div class="d-flex align-items-center gap-2 small">
                                    @if($booking->transport_type === 'flight')
                                        <i class="bi bi-airplane-fill text-danger fs-5"></i>
                                        <span class="fw-600 text-dark">{{ __('Máy bay') }}</span>
                                        @if($booking->pnr_code)
                                            <span style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:6px;padding:2px 8px;font-size:0.76rem;font-weight:700;letter-spacing:1px;">
                                                PNR: {{ $booking->pnr_code }}
                                            </span>
                                        @else
                                            <span style="background:#fffbeb;color:#d97706;border:1px solid #fde68a;border-radius:6px;padding:2px 8px;font-size:0.76rem;font-weight:600;">
                                                <i class="bi bi-hourglass-split me-1"></i>{{ __('Chờ vé') }}
                                            </span>
                                        @endif
                                    @elseif($booking->transport_type === 'bus')
                                        <i class="bi bi-bus-front-fill text-info fs-5"></i>
                                        <span class="fw-600 text-dark">{{ __('Xe ô tô') }}</span>
                                    @else
                                        <i class="bi bi-car-front-fill text-muted fs-5"></i>
                                        <span class="fw-600 text-dark">{{ __('Tự túc') }}</span>
                                    @endif
                                </div>

                                {{-- Addon info --}}
                                @if($booking->addons->count() > 0)
                                <div class="mt-3">
                                    <div class="text-muted small fw-600 mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;">
                                        <i class="bi bi-plus-circle me-1 text-primary"></i>{{ __('Dịch vụ thêm') }}
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($booking->addons as $addon)
                                        <span style="background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd;border-radius:12px;padding:2px 10px;font-size:0.75rem;font-weight:600;">
                                            {{ $addon->pivot->addon_name }} ×{{ $addon->pivot->quantity }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Payment Info --}}
                    <div class="col-lg-5">
                        <div class="payment-box h-100">
                            <div class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size:0.88rem;">
                                <i class="bi bi-receipt text-primary"></i>
                                {{ __('Chi tiết thanh toán') }}
                            </div>

                            {{-- Price rows --}}

                            @if($booking->discount_amount > 0)
                            <div class="payment-row">
                                <span class="label">
                                    <i class="bi bi-tag-fill me-1 text-success"></i>{{ __('Giảm giá') }}
                                    @if($booking->coupon)
                                        <span style="font-size:0.72rem;background:#dcfce7;color:#16a34a;border-radius:6px;padding:1px 6px;margin-left:4px;">{{ $booking->coupon->code }}</span>
                                    @endif
                                </span>
                                <span class="value text-success">-{!! format_currency($booking->discount_amount) !!}</span>
                            </div>
                            @endif

                            <div class="payment-total-row">
                                <span class="payment-total-label">{{ __('Tổng cộng') }}</span>
                                <span class="payment-total-value">{!! format_currency($booking->total_price) !!}</span>
                            </div>

                            {{-- Deposit progress bar --}}
                            @if($paymentType === 'deposit')
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem;font-weight:600;color:#6b7280;">
                                    <span>{{ __('Tiến độ thanh toán') }}</span>
                                    <span class="text-primary">{{ $paidPercent }}%</span>
                                </div>
                                <div class="deposit-progress">
                                    <div class="deposit-progress-fill" style="width: {{ $paidPercent }}%;"></div>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:0.76rem;color:#9ca3af;">
                                    <span>{{ __('Đã trả') }}: {!! format_currency($paidAmt) !!}</span>
                                    @if($paidPercent < 100)
                                    <span>{{ __('Còn') }}: {!! format_currency($booking->total_price - $paidAmt) !!}</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Payment Status & CTA --}}
                            @if($paymentStatus === 'paid_100' || $paymentStatus === 'paid_30')
                                @if($isCancelled && $booking->refund_request)
                                    @if($booking->refund_request->status === 'completed')
                                        <div class="ps-badge ps-refunded">
                                            <i class="bi bi-arrow-return-left"></i>
                                            {{ __('Đã hoàn tiền') }}
                                        </div>
                                    @elseif($booking->refund_request->status === 'pending')
                                        <div class="ps-badge ps-pending">
                                            <i class="bi bi-hourglass-split"></i>
                                            {{ __('Chờ hoàn tiền') }}
                                        </div>
                                    @else
                                        <div class="ps-badge ps-failed">
                                            <i class="bi bi-x-circle-fill"></i>
                                            {{ __('Từ chối hoàn tiền') }}
                                        </div>
                                    @endif
                                @else
                                    @if($paymentStatus === 'paid_100')
                                        <div class="ps-badge ps-paid100">
                                            <i class="bi bi-check-circle-fill"></i>
                                            {{ __('Đã thanh toán 100%') }}
                                        </div>
                                    @else
                                        <div class="ps-badge ps-paid30">
                                            <i class="bi bi-pie-chart-fill"></i>
                                            {{ __('Đã thanh toán 30% (Cọc)') }}
                                        </div>
                                        @if(!$isCancelled)
                                        <a href="{{ route('user.bookings.detail', $booking->id) }}#pay70Section" class="bk-btn bk-btn-info w-100 justify-content-center mt-2">
                                            <i class="bi bi-credit-card-fill"></i>
                                            {{ __('Thanh toán 70% còn lại') }}
                                            <span style="opacity:0.8;font-size:0.78rem;">({!! format_currency($remainAmt) !!})</span>
                                        </a>
                                        @endif
                                    @endif
                                @endif
                            </div>

                            @elseif($paymentStatus === 'pending')
                                <div class="ps-badge ps-pending">
                                    <i class="bi bi-hourglass-split"></i>
                                    @if($paymentMethod === 'vnpay')
                                        {{ __('Chưa thanh toán') }}
                                    @else
                                        {{ __('Chờ xác nhận') }}
                                    @endif
                                </div>
                                @if(!$isCancelled && $paymentMethod === 'vnpay')
                                <a href="{{ route('frontend.bookings.pay_vnpay', $booking->id) }}" class="bk-btn bk-btn-primary w-100 justify-content-center mt-2">
                                    <i class="bi bi-credit-card-fill"></i>
                                    {{ __('Quay lại thanh toán') }}
                                    <span style="opacity:0.8;font-size:0.78rem;">
                                        ({!! format_currency($paymentType === 'deposit' ? $depositAmt : $booking->total_price) !!})
                                    </span>
                                </a>
                                @elseif(!$isCancelled && $paymentMethod === 'transfer')
                                <div class="mt-2 text-center small text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ __('Vui lòng chuyển khoản theo thông tin đã nhận qua email.') }}
                                </div>
                                @endif

                            @elseif($paymentStatus === 'failed')
                                <div class="ps-badge ps-failed">
                                    <i class="bi bi-x-circle-fill"></i>
                                    {{ __('Thanh toán thất bại') }}
                                </div>
                                @if(!$isCancelled)
                                <a href="{{ route('frontend.bookings.pay_vnpay', $booking->id) }}" class="bk-btn bk-btn-outline w-100 justify-content-center mt-2">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    {{ __('Quay lại thanh toán') }}
                                </a>
                                @endif
                            @endif

                            {{-- Payment method indicator --}}
                            <div class="mt-3 text-center" style="font-size:0.75rem;color:#9ca3af;">
                                @if($paymentMethod === 'vnpay')
                                    <i class="bi bi-shield-check me-1 text-success"></i>{{ __('Bảo mật bởi') }} <strong>VNPay</strong>
                                @else
                                    <i class="bi bi-bank me-1"></i>{{ __('Thanh toán chuyển khoản') }}
                                @endif
                            </div>
                        </div>
                    </div>

                </div>{{-- /row --}}
            </div>{{-- /card-body --}}

        </div>{{-- /bk-card --}}
        @endforeach
    </div>{{-- /bookings-container --}}
    @endif
</div>

<script>
function switchTab(tab, btn) {
    // Update active button
    document.querySelectorAll('.booking-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Filter cards
    const cards = document.querySelectorAll('.bk-card');
    cards.forEach(card => {
        const tabs = card.dataset.tab || '';
        if (tab === 'all' || tabs.includes(tab)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    // Show empty state if no visible cards
    const visibleCards = [...cards].filter(c => c.style.display !== 'none');
    let emptyMsg = document.getElementById('tab-empty-msg');
    if (visibleCards.length === 0) {
        if (!emptyMsg) {
            emptyMsg = document.createElement('div');
            emptyMsg.id = 'tab-empty-msg';
            emptyMsg.className = 'empty-bookings';
            emptyMsg.innerHTML = `
                <div class="empty-icon-circle" style="font-size:2rem;">📭</div>
                <h4 class="fw-bold text-dark mt-3 mb-2">Không có đơn nào</h4>
                <p class="text-muted">Không tìm thấy đơn đặt chỗ trong danh mục này.</p>
            `;
            document.getElementById('bookings-container')?.appendChild(emptyMsg);
        }
        emptyMsg.style.display = 'block';
    } else {
        if (emptyMsg) emptyMsg.style.display = 'none';
    }
}

// Animate deposit progress bars on load
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.deposit-progress-fill').forEach(bar => {
        const w = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = w; }, 300);
    });
});
</script>
@endsection
