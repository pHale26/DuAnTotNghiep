@extends('layouts.master')

@php
    $tourTitle = $tour->title ?? $tour->name ?? $tour->tour_name ?? 'Chi tiết tour';

    if (!$tourTitle || trim($tourTitle) === '') {
        $tourTitle = 'Chi tiết tour';
    }
@endphp

@section('title', $tourTitle)

@push('scripts')
    @vite(['resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Echo) {
                // Listen to all schedules of this tour
                @foreach($tour->tour_schedules as $schedule)
                    window.Echo.channel('tour-schedule.{{ $schedule->id }}')
                        .listen('SeatAvailabilityUpdated', (e) => {
                            const label = document.querySelector(`label[for="schedule-${e.scheduleId}"]`);
                            if (label) {
                                const seatBadge = label.querySelector('.badge.bg-light span');
                                if (seatBadge) {
                                    seatBadge.textContent = e.availableSeats;
                                    seatBadge.className = e.availableSeats < 5 ? 'text-danger fw-bold' : 'text-success fw-bold';
                                }

                                const radio = document.getElementById(`schedule-${e.scheduleId}`);
                                if (e.availableSeats <= 0 && radio) {
                                    radio.disabled = true;
                                    radio.checked = false;
                                    // Thay label trống bằng Hết chỗ
                                    const textEnd = label.querySelector('.text-end');
                                    if(textEnd) {
                                        textEnd.innerHTML = `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Hết chỗ</span>`;
                                    }
                                }
                            }
                        });
                @endforeach
            }
        });
    </script>

@endpush
@section('content')
<style>
    .gallery-main {
        height: 500px;
        object-fit: cover;
        border-radius: 24px;
        transition: var(--transition-slow);
        background: #eef2f7;
    }

    .gallery-main:hover {
        transform: scale(1.02);
    }

    .gallery-sub {
        height: 240px;
        object-fit: cover;
        border-radius: 20px;
        transition: var(--transition-normal);
        cursor: pointer;
        background: #eef2f7;
    }

    .gallery-sub:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-hover);
    }

    .overflow-hidden-rounded {
        overflow: hidden;
        border-radius: 24px;
    }

    .tour-detail-wrapper {
        background: #f6f8fb;
    }

    .tour-title-detail {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        font-size: 32px;
        font-weight: 800;
        color: var(--dark-blue);
        line-height: 1.3;
    }

    .tour-info-line {
        color: var(--text-muted);
        font-weight: 500;
    }

    .tour-content-text {
        color: var(--text-muted);
        line-height: 1.8;
        font-size: 16px;
    }

    .tour-nav-tabs .nav-link {
        color: var(--text-muted);
        background: transparent;
        border: none;
        padding: 16px 24px;
        font-weight: 600;
        border-radius: 12px;
        transition: var(--transition-fast);
        text-align: left;
    }

    .tour-nav-tabs .nav-link:hover {
        background: rgba(0, 124, 232, 0.05);
        color: var(--primary-color);
    }

    .tour-nav-tabs .nav-link.active {
        color: white;
        background: var(--primary-color);
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.3);
    }

    .booking-sidebar {
        position: sticky;
        top: 85px;
        height: auto !important;
        min-height: auto !important;
        max-height: calc(100vh - 110px);
        overflow-y: auto;
    }

    .floating-booking-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 -5px 25px rgba(0, 0, 0, 0.05);
        padding: 14px 24px;
        z-index: 1040;
        display: none;
        align-items: center;
        justify-content: space-between;
    }

    @media (max-height: 780px), (max-width: 991.98px) {
        .booking-sidebar {
            position: static !important;
            height: auto !important;
            max-height: none !important;
            overflow-y: visible !important;
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
            margin-top: 30px;
        }
        
        .floating-booking-bar {
            display: flex;
        }
        
        .live-chatbox {
            bottom: 80px !important;
        }
        
        #ai-chatbot-widget {
            bottom: 155px !important;
        }
        
        body {
            padding-bottom: 85px;
        }
    }

    .booking-price {
        font-size: 34px;
        font-weight: 800;
        color: var(--dark-blue);
    }

    .booking-total {
        font-size: 28px;
        font-weight: 800;
        color: #dc3545;
    }

    .schedule-btn {
        border-radius: 16px;
        padding: 12px 16px;
        border: 2px solid transparent;
        background: var(--light-gray);
        color: var(--text-muted);
        transition: var(--transition-fast);
        cursor: pointer;
        text-align: center;
        min-width: 100px;
    }

    .btn-check:checked + .schedule-btn {
        background: rgba(0, 124, 232, 0.1);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .activity-simple-item {
        padding: 18px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid #eef2f7;
        margin-bottom: 14px;
    }

    .custom-accordion .accordion-item {
        border: none;
        background-color: transparent;
        margin-bottom: 16px;
    }
    .custom-accordion .accordion-header {
        background-color: transparent;
    }
    .custom-accordion .accordion-button {
        background-color: #fff;
        border: 1px solid #eef2f7;
        border-radius: 16px !important;
        padding: 16px 24px;
        color: var(--dark-blue);
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }
    .custom-accordion .accordion-button:not(.collapsed) {
        background-color: rgba(0, 124, 232, 0.05);
        color: var(--primary-color);
        border-color: rgba(0, 124, 232, 0.1);
        box-shadow: none;
        border-bottom-left-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .custom-accordion .accordion-button:focus {
        box-shadow: none;
    }
    .custom-accordion .accordion-collapse {
        background-color: #fff;
        border: 1px solid rgba(0, 124, 232, 0.1);
        border-top: none;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }
    .custom-accordion .accordion-body {
        padding: 24px;
        color: var(--text-muted);
        line-height: 1.8;
    }
    .last-border-none:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    .qty-input {
        -moz-appearance: textfield;
    }
    .qty-input:focus {
        box-shadow: none !important;
        border-color: #6c757d !important;
        z-index: 1 !important;
    }
    .btn-qty-minus, .btn-qty-plus {
        background-color: #f8f9fa;
    }
    .btn-qty-minus:hover, .btn-qty-plus:hover {
        background-color: #e9ecef;
    }
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    @media (max-width: 768px) {
        .gallery-main {
            height: 280px;
        }

        .gallery-sub {
            height: 160px;
        }

        .tour-title-detail {
            font-size: 24px;
        }

        .booking-sidebar {
            position: static;
        }
    }
</style>

@php
    $fallbackImage = asset('uploads/images/no-image.jpg');

    $tourImages = $tour->tour_images ?? collect();
    $allImages = collect();

    // Primary image first
    $primaryImage = $tourImages->where('is_primary', 1)->first() ?? $tourImages->first();
    if ($primaryImage && !empty($primaryImage->image_url)) {
        $allImages->push(\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://']) ? $primaryImage->image_url : asset(ltrim($primaryImage->image_url, '/')));
    } else {
        $allImages->push($fallbackImage);
    }

    // Other images
    if ($primaryImage) {
        $otherImages = $tourImages->where('id', '!=', $primaryImage->id);
        foreach ($otherImages as $img) {
            if ($img && !empty($img->image_url)) {
                $allImages->push(\Illuminate\Support\Str::startsWith($img->image_url, ['http://', 'https://']) ? $img->image_url : asset(ltrim($img->image_url, '/')));
            }
        }
    }

    $destinationName = $tour->destination->name ?? __('Điểm đến');
    $departureName = $tour->departure_location->name ?? __('Đang cập nhật');
@endphp

<div class="tour-detail-wrapper">
    <div class="container py-5">

        <nav aria-label="breadcrumb" class="mb-4 reveal-up">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-decoration-none text-primary fw-500">
                        {{ __('Trang chủ') }}
                    </a>
                </li>

                <li class="breadcrumb-item">
                    <span class="text-muted">
                        {{ $destinationName }}
                    </span>
                </li>

                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                    {{ $tourTitle }}
                </li>
            </ol>
        </nav>

        <div class="mb-5 reveal-up" style="transition-delay: 0.1s;">
            <h1 class="tour-title-detail mb-3">
                {{ $tourTitle }}
            </h1>

            <div class="d-flex flex-wrap gap-4 tour-info-line mb-4">
                <span class="d-flex align-items-center">
                    <i class="bi bi-geo-alt fs-5 me-2 text-danger"></i>
                    {{ __('Điểm tập kết:') }}
                    <strong class="ms-1 text-dark">
                        {{ $tour->meeting_point ?? __('Đang cập nhật') }}
                    </strong>
                </span>

                <span class="d-flex align-items-center">
                    <i class="bi bi-clock fs-5 me-2 text-warning"></i>
                    {{ $tour->duration_days ?? 0 }} {{ __('ngày') }}{{ ($tour->duration_nights ?? 0) > 0 ? ' ' . ($tour->duration_nights ?? 0) . ' ' . __('đêm') : '' }}
                </span>

                @if($tour->departure_time)
                <span class="d-flex align-items-center">
                    <i class="bi bi-alarm fs-5 me-2 text-info"></i>
                    {{ __('Giờ khởi hành:') }}
                    <strong class="ms-1 text-dark">
                        {{ \Carbon\Carbon::parse($tour->departure_time)->format('H\hi') }}
                    </strong>
                </span>
                @endif

                <span class="d-flex align-items-center text-success fw-600 bg-success bg-opacity-10 px-3 py-1 rounded-pill" style="font-size: 0.9rem;">
                    <i class="bi bi-shield-check fs-5 me-2"></i>
                    {{ __('Đã bao gồm ') }} <span style="cursor: pointer; text-decoration: underline;" class="ms-1" data-bs-toggle="modal" data-bs-target="#insuranceModal">{{ __('bảo hiểm tour') }}</span>
                </span>


            </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Image Slider -->
                <div id="tourImageCarousel" class="carousel slide mb-5 overflow-hidden-rounded shadow-sm position-relative" data-bs-ride="carousel">
                    
                    
                    <div class="carousel-indicators">
                        @foreach($allImages as $index => $img)
                            <button type="button" data-bs-target="#tourImageCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($allImages as $index => $img)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ $img }}" class="d-block w-100 gallery-main" alt="{{ $tourTitle }}" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                            </div>
                        @endforeach
                    </div>
                    @if($allImages->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#tourImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#tourImageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>

                <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
                    <div class="accordion custom-accordion" id="masterAccordion">

                        <!-- Tổng Quan -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-overview">
                                <button class="accordion-button fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-overview" aria-expanded="true" aria-controls="collapse-overview">
                                    <i class="bi bi-info-circle-fill text-primary me-3 fs-4"></i>
                                    {{ __('Tổng quan') }}
                                </button>
                            </h2>
                            <div id="collapse-overview" class="accordion-collapse collapse show" aria-labelledby="heading-overview" data-bs-parent="#masterAccordion">
                                <div class="accordion-body">
                                    <p class="tour-content-text mb-0">
                                        {!! nl2br(e($tour->description ?? __('Đang cập nhật mô tả tour.'))) !!}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Lịch trình chi tiết -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-itinerary">
                                <button class="accordion-button collapsed fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-itinerary" aria-expanded="false" aria-controls="collapse-itinerary">
                                    <i class="bi bi-map-fill text-success me-3 fs-4"></i>
                                    {{ __('Lịch trình chi tiết') }}
                                </button>
                            </h2>
                            <div id="collapse-itinerary" class="accordion-collapse collapse" aria-labelledby="heading-itinerary" data-bs-parent="#masterAccordion">
                                <div class="accordion-body">
                                    @if($tour->tour_itineraries->count())
                                        <div class="itinerary-list">
                                            @foreach($tour->tour_itineraries as $index => $itinerary)
                                                <div class="mb-4 pb-4 border-bottom last-border-none">
                                                    <h5 class="fw-bold text-dark mb-3">
                                                        <span class="badge bg-primary me-2">{{ __('Ngày') }} {{ $itinerary->day_number }}</span>
                                                        {{ $itinerary->title }}
                                                    </h5>
                                                    <p class="mb-3 tour-content-text">{!! nl2br(e($itinerary->description ?? __('Đang cập nhật lịch trình.'))) !!}</p>

                                                    @if($itinerary->activities && $itinerary->activities->count())
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($itinerary->activities as $activity)
                                                                <li class="mb-3 d-flex align-items-start">
                                                                    <i class="bi bi-check-circle-fill text-success me-3 mt-1 fs-5"></i>
                                                                    <div>
                                                                        <strong class="text-dark d-block mb-1">{{ $activity->title }}</strong>
                                                                        <span class="small">{{ $activity->description }}</span>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">
                                            {{ __('Chưa có lịch trình chi tiết.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Các hoạt động nổi bật -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-activities">
                                <button class="accordion-button collapsed fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-activities" aria-expanded="false" aria-controls="collapse-activities">
                                    <i class="bi bi-star-fill text-warning me-3 fs-4"></i>
                                    {{ __('Các hoạt động nổi bật') }}
                                </button>
                            </h2>
                            <div id="collapse-activities" class="accordion-collapse collapse" aria-labelledby="heading-activities" data-bs-parent="#masterAccordion">
                                <div class="accordion-body">
                                    @if(isset($groupedActivities) && $groupedActivities->isNotEmpty())
                                        @foreach($groupedActivities as $type => $activities)
                                            <div class="mb-4 last-border-none border-bottom pb-4">
                                                <h6 class="fw-bold text-dark text-uppercase mb-4" style="letter-spacing: 1px;">
                                                    {{ $type ?: __('Hoạt động') }}
                                                </h6>

                                                @foreach($activities as $activity)
                                                    <div class="mb-3 d-flex align-items-start gap-3">
                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
                                                            <i class="bi bi-activity text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark mb-1">{{ $activity->title }}</div>
                                                            <div class="small">{{ $activity->description }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">
                                            {{ __('Chưa có thông tin hoạt động chi tiết.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Dịch vụ bổ sung và Vé tham quan -->
                        @if((isset($tour->tickets) && $tour->tickets->isNotEmpty()) || (isset($tour->addons) && $tour->addons->isNotEmpty()))
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-extras">
                                    <button class="accordion-button collapsed fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-extras" aria-expanded="false" aria-controls="collapse-extras">
                                        <i class="bi bi-ticket-perforated-fill text-danger me-3 fs-4"></i>
                                        {{ __('Vé tham quan & Dịch vụ đi kèm') }}
                                    </button>
                                </h2>
                                <div id="collapse-extras" class="accordion-collapse collapse" aria-labelledby="heading-extras" data-bs-parent="#masterAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-info rounded-4 mb-4">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            <strong>{{ __('Lưu ý:') }}</strong> {{ __('Các dịch vụ này sẽ được chọn và thêm vào ở bước tiếp theo khi bạn tiến hành Đặt tour (Bước 2). Dưới đây là thông tin tham khảo các dịch vụ có sẵn cho tour này.') }}
                                        </div>

                                        @if(isset($tour->tickets) && $tour->tickets->isNotEmpty())
                                            <div class="mb-4">
                                                <h5 class="fw-bold text-dark mb-3">
                                                    <i class="bi bi-ticket-detailed me-2 text-primary"></i>{{ __('Vé tham quan') }}
                                                </h5>
                                                <div class="row g-3">
                                                    @foreach($tour->tickets as $ticket)
                                                        <div class="col-md-6">
                                                            <div class="activity-simple-item h-100 mb-0 shadow-sm">
                                                                <div class="fw-bold text-dark mb-2 fs-6">{{ $ticket->title }}</div>
                                                                @if($ticket->description)
                                                                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($ticket->description, 100) }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($tour->addons) && $tour->addons->isNotEmpty())
                                            @php
                                                $includedAddons = $tour->addons->where('type', 'included');
                                                $extraAddons = $tour->addons->where('type', 'extra');
                                            @endphp
                                            
                                            @if($includedAddons->isNotEmpty())
                                            <div class="mb-4">
                                                <h5 class="fw-bold text-dark mb-3">
                                                    <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ __('Dịch vụ đi kèm (Đã bao gồm)') }}
                                                </h5>
                                                <div class="row g-3">
                                                    @foreach($includedAddons as $addon)
                                                        <div class="col-md-6">
                                                            <div class="activity-simple-item h-100 mb-0 shadow-sm d-flex flex-column border-success border-start border-4">
                                                                <div class="fw-bold text-dark mb-1 fs-6">{{ $addon->name }}</div>
                                                                @if($addon->description)
                                                                    <div class="small text-muted mb-2 flex-grow-1">{{ \Illuminate\Support\Str::limit($addon->description, 100) }}</div>
                                                                @endif
                                                                <div class="fw-bold text-success mt-auto">
                                                                    Miễn phí
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            @if($extraAddons->isNotEmpty())
                                            <div class="mb-3">
                                                <h5 class="fw-bold text-dark mb-3">
                                                    <i class="bi bi-box2-heart me-2 text-primary"></i>{{ __('Dịch vụ bổ sung (Tùy chọn)') }}
                                                </h5>
                                                <div class="row g-3">
                                                    @foreach($extraAddons as $addon)
                                                        <div class="col-md-6">
                                                            <div class="activity-simple-item h-100 mb-0 shadow-sm d-flex flex-column">
                                                                <div class="fw-bold text-dark mb-1 fs-6">{{ $addon->name }}</div>
                                                                @if($addon->description)
                                                                    <div class="small text-muted mb-2 flex-grow-1">{{ \Illuminate\Support\Str::limit($addon->description, 100) }}</div>
                                                                @endif
                                                                <div class="fw-bold text-info mt-auto">
                                                                    {{ format_currency($addon->price) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Nhận xét và đánh giá -->
                @if(isset($tour->reviews) && $tour->reviews->isNotEmpty())
                <div class="glass-panel p-4 p-md-5 mb-5 reveal-up mt-4">
                    <h3 class="fw-bold text-dark mb-4">
                        <i class="bi bi-star-fill text-warning me-2"></i> Đánh giá từ khách hàng
                    </h3>

                    @php
                        $avgRating = round($tour->reviews->avg('rating'), 1);
                        $totalReviews = $tour->reviews->count();
                    @endphp

                    <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                        <div class="display-4 fw-bold text-dark me-3">{{ $avgRating }}</div>
                        <div>
                            <div class="text-warning fs-5 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $avgRating)
                                        <i class="bi bi-star-fill"></i>
                                    @elseif($i - 0.5 <= $avgRating)
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-muted">{{ $totalReviews }} đánh giá</div>
                        </div>
                    </div>

                    <!-- Khối Tóm tắt bằng AI -->
                    <div class="ai-summary-block mb-4 p-4 rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(0,124,232,0.05) 0%, rgba(138,43,226,0.05) 100%); border: 1px solid rgba(138,43,226,0.1);">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-stars fs-4 me-2" style="color: #8a2be2;"></i>
                            <h5 class="fw-bold mb-0 text-dark" style="background: -webkit-linear-gradient(45deg, #007CE8, #8a2be2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Tóm tắt đánh giá bởi AI</h5>
                        </div>

                        <!-- Skeleton Loader -->
                        <div id="aiSummaryLoader">
                            <div class="placeholder-glow">
                                <span class="placeholder col-12 rounded-2 mb-2"></span>
                                <span class="placeholder col-11 rounded-2 mb-2"></span>
                                <span class="placeholder col-8 rounded-2"></span>
                            </div>
                            <div class="small mt-3 text-muted">
                                <i class="bi bi-arrow-repeat spin"></i> AI đang tổng hợp ý kiến từ {{ $totalReviews }} đánh giá...
                            </div>
                        </div>

                        <!-- Nội dung thực -->
                        <div id="aiSummaryContent" class="d-none">
                            <p class="tour-content-text mb-0 text-dark fw-500" style="line-height: 1.6;" id="aiSummaryText"></p>
                            <div class="small mt-3 text-muted d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-info-circle me-1"></i>Được tạo tự động bởi hệ thống AI</span>
                                <span class="badge bg-light text-muted border">Gemini</span>
                            </div>
                        </div>
                    </div>

                    <div class="review-list" id="reviewList">
                        @foreach($tour->reviews as $index => $review)
                            <div class="review-item mb-4 pb-4 border-bottom last-border-none {{ $index >= 3 ? 'd-none' : '' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">
                                        @if($review->user && $review->user->avatar)
                                            <img src="{{ asset($review->user->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover" width="48" height="48">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px;">
                                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $review->user->name ?? 'Người dùng ẩn danh' }}</h6>
                                            <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                        </div>
                                        <div class="text-warning small mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="tour-content-text mb-0">{{ $review->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($totalReviews > 3)
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4" id="btnLoadMoreReviews">
                                Xem thêm đánh giá <i class="bi bi-chevron-down ms-1"></i>
                            </button>
                        </div>
                    @endif
                </div>
                @endif

            </div>

            <div class="col-lg-4">
                <div class="premium-card booking-sidebar p-4 reveal-up">
                    <div class="d-flex flex-column mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-end mb-2">
                            <div class="booking-price">
                                {{ format_currency($tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0)) }}
                            </div>
                            <span class="ms-2 text-muted mb-2" data-bs-toggle="tooltip" title="Bao gồm: Vận chuyển ({{ format_currency($tour->cost_transport ?? 0) }}), Ăn uống ({{ format_currency($tour->cost_meal ?? 0) }}), Bảo hiểm ({{ format_currency($tour->cost_insurance ?? 0) }}), Phí dịch vụ ({{ format_currency($tour->cost_service_fee ?? 0) }}). Giá CHƯA bao gồm chi phí lưu trú.">
                                / {{ __('người lớn') }} <i class="bi bi-info-circle"></i>
                            </span>
                        </div>
                        <div class="d-flex align-items-end">
                            <div class="fs-5 fw-bold text-info">
                                {{ format_currency( ($tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0)) * config('booking.child_price_rate') ) }}
                            </div>
                            <span class="ms-2 text-muted">
                                / {{ __('trẻ em') }}
                            </span>
                        </div>
                    </div>

                    <h5 class="mb-4 fw-bold text-dark fs-5">
                        {{ __('Chọn lịch trình khởi hành') }}
                    </h5>

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4 rounded-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($tour->tour_schedules->isEmpty())
                        <div class="alert alert-warning mb-4 rounded-4">
                            {{ __('Hiện tại tour này chưa có lịch khởi hành mới. Vui lòng quay lại sau!') }}
                        </div>

                        <button type="button" class="btn btn-secondary w-100 py-3 fs-5" disabled>
                            {{ __('Tạm ngưng nhận khách') }}
                        </button>
                    @else
                        <form action="{{ route('frontend.tours.checkout') }}" method="GET">

<style>
    .schedule-card-label {
        border: 2px solid #e9ecef;
        border-radius: 14px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        background-color: #fff;
    }
    .schedule-card-label:hover {
        border-color: #b8daff;
        background-color: #f8fbff;
    }
    .btn-check:checked + .schedule-card-label {
        border-color: var(--primary-color);
        background-color: rgba(0, 124, 232, 0.04);
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.12);
    }
    .btn-check:disabled + .schedule-card-label {
        opacity: 0.7;
        cursor: not-allowed;
        background-color: #f8f9fa;
    }
    .schedule-wrapper {
        max-height: 340px;
        overflow-y: auto;
        padding-right: 6px;
    }
    /* Custom scrollbar */
    .schedule-wrapper::-webkit-scrollbar {
        width: 6px;
    }
    .schedule-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .schedule-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    .schedule-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

                            <div class="schedule-wrapper d-flex flex-column gap-3 mb-4 pb-4 border-bottom">
                                @php
                                    $hasAvailableSchedule = false;
                                @endphp
                                @foreach($tour->tour_schedules as $index => $schedule)
                                    @php
                                        \Carbon\Carbon::setLocale('vi');
                                        $date = \Carbon\Carbon::parse($schedule->departure_date);
                                        $dayOfWeek = ucfirst($date->translatedFormat('l'));
                                        $formattedDate = $date->format('d/m/Y');

                                        $isFull = $schedule->available_seats <= 0;
                                        $seatClass = $schedule->available_seats < 5 ? 'text-danger' : 'text-success';

                                        if (!$isFull && !$hasAvailableSchedule) {
                                            $isChecked = true;
                                            $hasAvailableSchedule = true;
                                        } else {
                                            $isChecked = false;
                                        }
                                    @endphp

                                    @php
                                        $surcharge = \App\Models\Holiday::getIncreasePercentage($schedule->departure_date);
                                    @endphp
                                    <div class="position-relative">
                                        <input type="radio"
                                               class="btn-check schedule-radio"
                                               name="schedule_id"
                                               id="schedule-{{ $schedule->id }}"
                                               value="{{ $schedule->id }}"
                                               data-surcharge="{{ $surcharge }}"
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $isFull ? 'disabled' : '' }}
                                               required>

                                        <label class="schedule-card-label w-100 p-3 m-0 d-flex justify-content-between align-items-center"
                                               for="schedule-{{ $schedule->id }}">

                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 text-center me-3 border" style="min-width: 65px;">
                                                    <div class="fw-bold fs-4 text-primary lh-1">{{ $date->format('d') }}</div>
                                                    <div class="small text-muted mt-1 fw-500">{{ __('Thg') }} {{ $date->format('m') }}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ $dayOfWeek }}</div>
                                                                                  <div class="small text-muted mt-1">
                                                        <i class="bi bi-calendar2-check text-primary me-1"></i> {{ __('Khởi hành:') }} {{ $formattedDate }}
                                                        @if($tour->departure_time)
                                                            <span class="ms-2 text-warning fw-bold"><i class="bi bi-clock-fill me-1"></i>{{ \Carbon\Carbon::parse($tour->departure_time)->format('H\hi') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end ms-2">
                                                @if($surcharge > 0)
                                                    <div class="mb-1"><span class="badge bg-danger" style="font-size:0.65rem">+{{ $surcharge }}% Lễ</span></div>
                                                @endif
                                                @if($isFull)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>{{ __('Hết chỗ') }}</span>
                                                @else
                                                    <div class="small text-muted mb-1">{{ __('Số chỗ trống') }}</div>
                                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6"><span class="{{ $seatClass }} fw-bold">{{ $schedule->available_seats }}</span></span>
                                                @endif
                                            </div>
                                        </label>

                                        @if($isFull)
                                            <div class="position-absolute top-0 start-0 w-100 h-100 rounded-3" style="background: rgba(255,255,255,0.5); z-index: 1;"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="row g-3 mb-4 pb-4 border-bottom">
                                <div class="col-6">
                                    <label class="form-label mb-2 fw-600 text-dark">
                                        {{ __('Người lớn') }}
                                    </label>

                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary border-end-0 btn-qty-minus" type="button" data-target="adults">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number"
                                               class="form-control text-center border-secondary border-start-0 border-end-0 fw-bold bg-white qty-input"
                                               name="adults"
                                               id="adults"
                                               value="1"
                                               min="1"
                                               max="10">
                                        <button class="btn btn-outline-secondary border-start-0 btn-qty-plus" type="button" data-target="adults">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="form-label mb-2 fw-600 text-dark">
                                        {{ __('Trẻ em') }}
                                    </label>

                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary border-end-0 btn-qty-minus" type="button" data-target="children">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number"
                                               class="form-control text-center border-secondary border-start-0 border-end-0 fw-bold bg-white qty-input"
                                               name="children"
                                               id="children"
                                               value="0"
                                               min="0"
                                               max="10">
                                        <button class="btn btn-outline-secondary border-start-0 btn-qty-plus" type="button" data-target="children">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="text-muted fw-600">
                                    {{ __('Tổng cộng:') }}
                                </span>

                                <span class="booking-total" id="totalPrice">
                                    {{ format_currency($tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0)) }}
                                </span>
                            </div>

                            <button type="submit" class="btn btn-register-premium w-100 py-3 fs-5">
                                {{ __('Đặt Ngay Chuyến Đi') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        </div> <!-- Close mb-5 reveal-up -->

        @if(isset($relatedTours) && $relatedTours->count() > 0)
        <div class="mt-5 pt-4 border-top reveal-up">
            <h3 class="fw-bold text-dark mb-4">{{ __('Tour cùng danh mục') }}</h3>
            <div class="row g-4">
                @foreach($relatedTours as $relatedTour)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="tour-preview-wrapper h-100" x-data="{ showPreview: false }"
                             @mouseenter="showPreview = true" @mouseleave="showPreview = false">
                            <a href="{{ route('frontend.tours.show', $relatedTour->slug) }}" class="text-decoration-none h-100 d-block" @mouseenter.stop>
                                <div class="combo-card h-100">
                                    <div class="combo-card-img-wrapper">
                                        @if($relatedTour->duration_days)
                                            <div class="tour-duration-badge">
                                                {{ $relatedTour->duration_days }}N{{ $relatedTour->duration_nights > 0 ? $relatedTour->duration_nights . 'Đ' : '' }}
                                            </div>
                                        @endif

                                        @php
                                            $rPrimaryImage = $relatedTour->tour_images->where('is_primary', 1)->first()
                                                         ?? $relatedTour->tour_images->first();
                                            $rTourImage = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';
                                            if ($rPrimaryImage && !empty($rPrimaryImage->image_url)) {
                                                if (\Illuminate\Support\Str::startsWith($rPrimaryImage->image_url, ['http://', 'https://'])) {
                                                    $rTourImage = $rPrimaryImage->image_url;
                                                } else {
                                                    $rTourImage = asset(ltrim($rPrimaryImage->image_url, '/'));
                                                }
                                            }
                                            $rDestinationName = optional($relatedTour->destination)->name ?: 'Việt Nam';
                                            $rStars = $relatedTour->reviews()->where('is_hidden', false)->avg('rating') ? round($relatedTour->reviews()->where('is_hidden', false)->avg('rating')) : 0;
                                        @endphp
                                        <img src="{{ $rTourImage }}"
                                             alt="{{ $relatedTour->title }}"
                                             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';">
                                    </div>
                                    <div class="combo-card-body">
                                        <h3 class="combo-title">{{ $relatedTour->title }}</h3>
                                        <div class="combo-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi {{ $i <= $rStars ? 'bi-star-fill' : 'bi-star' }} text-warning"></i>
                                            @endfor
                                        </div>
                                        <div class="combo-location">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ $rDestinationName }}</span>
                                        </div>
                                        <div class="combo-footer">
                                            <div>
                                                <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                                                <div class="combo-price-val">{{ format_currency($relatedTour->getBasePrice() > 0 ? $relatedTour->getBasePrice() : ($relatedTour->base_price ?? 0)) }}</div>
                                            </div>
                                            <span class="btn btn-combo-detail">{{ __('Xem chi tiết') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <!-- Tour Preview Component -->
                            <x-tour-preview :tour="$relatedTour" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let basePriceVND = {{ $tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0) }};
        const CHILD_PRICE_RATE = {{ config('booking.child_price_rate') }};
        let childPriceVND = basePriceVND * CHILD_PRICE_RATE;
        const originalBasePrice = basePriceVND;
        const originalChildPrice = childPriceVND;
        const currency = '{{ Session::get("currency", "VND") }}';

        let rate = 1;
        let symbol = ' VNĐ';
        let prefix = false;

        switch (currency) {
            case 'USD':
                rate = 25000;
                symbol = '$';
                prefix = true;
                break;

            case 'EUR':
                rate = 27000;
                symbol = '€';
                prefix = true;
                break;

            case 'CNY':
                rate = 3500;
                symbol = '¥';
                prefix = true;
                break;

            case 'VND':
            default:
                rate = 1;
                symbol = ' VNĐ';
                prefix = false;
                break;
        }

        function formatCurrency(amount) {
            const converted = amount / rate;
            let formatted = new Intl.NumberFormat(currency === 'VND' ? 'vi-VN' : 'en-US', {
                minimumFractionDigits: currency === 'VND' ? 0 : 2,
                maximumFractionDigits: currency === 'VND' ? 0 : 2
            }).format(converted);
            return prefix ? symbol + formatted : formatted + symbol;
        }

        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');
        const totalPriceSpan = document.getElementById('totalPrice');
        const scheduleRadios = document.querySelectorAll('input[name="schedule_id"]');

        function updatePriceWithSurcharge() {
            const selected = document.querySelector('input[name="schedule_id"]:checked');
            if(selected) {
                const surcharge = parseFloat(selected.dataset.surcharge || 0);
                basePriceVND = originalBasePrice * (1 + surcharge / 100);
                childPriceVND = originalChildPrice * (1 + surcharge / 100);

                document.querySelector('.booking-price').innerHTML = formatCurrency(basePriceVND) + (surcharge > 0 ? ` <span class="badge bg-danger fs-6 align-middle ms-2">+${surcharge}% Lễ</span>` : '');
                const childPriceElem = document.querySelector('.fs-5.fw-bold.text-info');
                if(childPriceElem) childPriceElem.innerHTML = formatCurrency(childPriceVND) + (surcharge > 0 ? ` <span class="badge bg-danger ms-2" style="font-size:0.65rem">+${surcharge}% Lễ</span>` : '');
            }
            updateTotalPrice();
        }

        function updateTotalPrice() {
            if (!adultsInput || !childrenInput || !totalPriceSpan) {
                return;
            }

            const adults = parseInt(adultsInput.value) || 0;
            const children = parseInt(childrenInput.value) || 0;
            const totalVND = (basePriceVND * adults) + (childPriceVND * children);

            totalPriceSpan.textContent = formatCurrency(totalVND);
        }

        if (adultsInput && childrenInput) {
            function validateQty(input) {
                let val = parseInt(input.value);
                const min = parseInt(input.getAttribute('min')) || 0;
                const max = parseInt(input.getAttribute('max')) || 10;
                if (isNaN(val) || val < min) val = min;
                if (val > max) val = max;
                input.value = val;
                updateTotalPrice();
            }

            adultsInput.addEventListener('change', function() { validateQty(this); });
            adultsInput.addEventListener('blur', function() { validateQty(this); });
            adultsInput.addEventListener('keyup', function() { updateTotalPrice(); });

            childrenInput.addEventListener('change', function() { validateQty(this); });
            childrenInput.addEventListener('blur', function() { validateQty(this); });
            childrenInput.addEventListener('keyup', function() { updateTotalPrice(); });

            // Handle plus/minus buttons
            document.querySelectorAll('.btn-qty-minus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    let val = parseInt(input.value) || 0;
                    const min = parseInt(input.getAttribute('min')) || 0;
                    if (val > min) {
                        input.value = val - 1;
                        validateQty(input);
                    }
                });
            });

            document.querySelectorAll('.btn-qty-plus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    let val = parseInt(input.value) || 0;
                    const max = parseInt(input.getAttribute('max')) || 10;
                    if (val < max) {
                        input.value = val + 1;
                        validateQty(input);
                    }
                });
            });
        }

        if (scheduleRadios) {
            scheduleRadios.forEach(radio => radio.addEventListener('change', updatePriceWithSurcharge));
            // Trigger calculation for initially selected
            updatePriceWithSurcharge();
        }

        const btnLoadMoreReviews = document.getElementById('btnLoadMoreReviews');
        if (btnLoadMoreReviews) {
            btnLoadMoreReviews.addEventListener('click', function() {
                const hiddenReviews = document.querySelectorAll('.review-item.d-none');
                let count = 0;
                for (let i = 0; i < hiddenReviews.length; i++) {
                    hiddenReviews[i].classList.remove('d-none');
                    count++;
                    if (count === 3) break;
                }

                const remainingHidden = document.querySelectorAll('.review-item.d-none');
                if (remainingHidden.length === 0) {
                    btnLoadMoreReviews.style.display = 'none';
                }
            });
        }

        // Fetch AI Summary
        const aiSummaryLoader = document.getElementById('aiSummaryLoader');
        const aiSummaryContent = document.getElementById('aiSummaryContent');
        const aiSummaryText = document.getElementById('aiSummaryText');

        if (aiSummaryLoader && aiSummaryContent && aiSummaryText) {
            window.fetchAISummary = function() {
                aiSummaryLoader.classList.remove('d-none');
                aiSummaryContent.classList.add('d-none');
                aiSummaryText.innerHTML = '';
                
                fetch(`/tours/{{ $tour->id }}/ai-summary`)
                    .then(response => response.json())
                    .then(data => {
                        aiSummaryLoader.classList.add('d-none');
                        aiSummaryContent.classList.remove('d-none');

                        if (data.success && data.summary) {
                            let summaryHtml = data.summary.replace(/\n/g, '<br>');
                            
                            // Check if the summary returned is actually an error message from the controller
                            if (data.summary.includes('Không thể kết nối đến dịch vụ AI') || data.summary.includes('Không thể tạo tóm tắt vào lúc này')) {
                                summaryHtml += ' <button class="btn btn-sm btn-outline-primary ms-2 py-0 px-2 rounded-pill" onclick="window.fetchAISummary()" style="font-size: 0.8rem;"><i class="bi bi-arrow-clockwise"></i> Thử lại</button>';
                            }
                            
                            aiSummaryText.innerHTML = summaryHtml;
                        } else {
                            aiSummaryText.innerHTML = '<span class="text-muted"><i class="bi bi-exclamation-triangle me-1"></i> Không thể tạo tóm tắt vào lúc này. Vui lòng thử lại sau.</span> <button class="btn btn-sm btn-outline-primary ms-2 py-0 px-2 rounded-pill" onclick="window.fetchAISummary()" style="font-size: 0.8rem;"><i class="bi bi-arrow-clockwise"></i> Thử lại</button>';
                        }
                    })
                    .catch(error => {
                        aiSummaryLoader.classList.add('d-none');
                        aiSummaryContent.classList.remove('d-none');
                        aiSummaryText.innerHTML = '<span class="text-muted"><i class="bi bi-exclamation-triangle me-1"></i> Lỗi kết nối khi tải tóm tắt AI.</span> <button class="btn btn-sm btn-outline-primary ms-2 py-0 px-2 rounded-pill" onclick="window.fetchAISummary()" style="font-size: 0.8rem;"><i class="bi bi-arrow-clockwise"></i> Thử lại</button>';
                    });
            };
            
            window.fetchAISummary();
        }
    });
</script>
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spin {
        display: inline-block;
        animation: spin 1s linear infinite;
    }
</style>

<div class="floating-booking-bar" id="floatingBookingBar">
    <div class="d-flex flex-column">
        <div class="small text-muted mb-1" style="font-size: 0.75rem;">{{ __('Giá từ:') }}</div>
        <div class="d-flex align-items-baseline gap-1">
            <strong class="fs-4 text-primary" style="font-weight: 800;">{{ format_currency($tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0)) }}</strong>
            <span class="small text-muted" style="font-size: 0.75rem;">/ {{ __('người lớn') }}</span>
        </div>
    </div>
    <div>
        <button type="button" class="btn btn-primary px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2 shadow-sm" onclick="scrollToBookingSection()">
            <i class="bi bi-calendar-check"></i> {{ __('Chọn lịch trình & Đặt') }}
        </button>
    </div>
</div>

<script>
    function scrollToBookingSection() {
        const sidebar = document.querySelector('.booking-sidebar');
        if (sidebar) {
            sidebar.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Flash/highlight the booking sidebar card to draw attention
            sidebar.style.transition = 'all 0.5s ease';
            sidebar.style.transform = 'scale(1.03)';
            sidebar.style.boxShadow = '0 10px 30px rgba(0, 124, 232, 0.25)';
            sidebar.style.borderColor = 'var(--primary-color)';
            
            setTimeout(() => {
                sidebar.style.transform = 'scale(1)';
                sidebar.style.boxShadow = '';
                sidebar.style.borderColor = '';
            }, 800);
        }
    }
</script>

<!-- Modal Bảo Hiểm Tour -->
<div class="modal fade" id="insuranceModal" tabindex="-1" aria-labelledby="insuranceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                <h5 class="modal-title fw-bold text-primary" id="insuranceModalLabel">
                    <i class="bi bi-shield-check fs-4 me-2"></i>Quyền Lợi Bảo Hiểm Tour
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5" style="background-color: #fff;">

                
                <h6 class="fw-bold text-dark fs-5 mb-4">Bạn sẽ nhận được những quyền lợi gì?</h6>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 mt-1">
                                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-heart-pulse-fill text-success"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">Bồi thường y tế</h6>
                                <p class="text-muted small mb-0">Chi trả các chi phí y tế phát sinh do ốm đau, tai nạn bất ngờ trong suốt hành trình chuyến đi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 mt-1">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-person-check-fill text-warning"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">Trách nhiệm cá nhân</h6>
                                <p class="text-muted small mb-0">Bảo hiểm bồi thường thiệt hại về tài sản hoặc thương tật đối với bên thứ ba do sơ suất của bạn trong tour.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 mt-1">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-suitcase-fill text-danger"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">Bảo vệ hành lý</h6>
                                <p class="text-muted small mb-0">Hỗ trợ bồi thường trong trường hợp hành lý và tài sản cá nhân bị thất lạc, hỏng hóc hoặc mất cắp.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 mt-1">
                                <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-telephone-fill text-info"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-bold mb-1">Hỗ trợ khẩn cấp 24/7</h6>
                                <p class="text-muted small mb-0">Luôn có mặt để cứu trợ y tế khẩn cấp hoặc hỗ trợ pháp lý bất cứ khi nào bạn cần trong chuyến đi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3 d-flex justify-content-center">
                <button type="button" class="btn btn-primary px-5 py-2 rounded-pill fw-600" data-bs-dismiss="modal">Tôi đã hiểu</button>
            </div>
        </div>
    </div>
</div>
@endsection
