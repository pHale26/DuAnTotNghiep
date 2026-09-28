@extends('layouts.master')

@section('title', $ticket->title . ' - Travel Wonder')

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

    .booking-sidebar {
        position: sticky;
        top: 100px;
        height: auto !important;
        min-height: auto !important;
    }

    .booking-sidebar.premium-card {
        height: auto !important;
        display: block !important;
    }

    .ticket-booking-notice {
        height: auto !important;
    }

    .booking-price {
        font-size: 34px;
        font-weight: 800;
        color: var(--dark-blue);
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

    @media (max-width: 768px) {
        .gallery-main {
            height: 280px;
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
    $fallbackImage = 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=2070';
    
    $destName = mb_strtolower($ticket->destination->name ?? '', 'UTF-8');
    if (str_contains($destName, 'nha trang') || str_contains($destName, 'phú quốc')) {
        $fallbackImage = 'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=2070';
    } elseif (str_contains($destName, 'sapa') || str_contains($destName, 'đà nẵng') || str_contains($destName, 'hạ long')) {
        $fallbackImage = 'https://images.unsplash.com/photo-1571509930722-df38ccfb8611?q=80&w=2070';
    }

    $ticketImages = $ticket->ticket_images ?? collect();
    $allImages = collect();

    // Primary image first
    $primaryImage = $ticketImages->where('is_primary', 1)->first() ?? $ticketImages->first();
    if ($primaryImage && !empty($primaryImage->image_url)) {
        $allImages->push(\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://']) ? $primaryImage->image_url : asset(ltrim($primaryImage->image_url, '/')));
    } else {
        $allImages->push($fallbackImage);
    }

    // Other images
    if ($primaryImage) {
        $otherImages = $ticketImages->where('id', '!=', $primaryImage->id);
        foreach ($otherImages as $img) {
            if ($img && !empty($img->image_url)) {
                $allImages->push(\Illuminate\Support\Str::startsWith($img->image_url, ['http://', 'https://']) ? $img->image_url : asset(ltrim($img->image_url, '/')));
            }
        }
    }

    $destinationName = $ticket->destination->name ?? __('Điểm đến');
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
                    <a href="{{ route('frontend.tickets.index') }}" class="text-decoration-none text-primary fw-500">
                        {{ __('Vé tham quan') }}
                    </a>
                </li>

                <li class="breadcrumb-item">
                    <span class="text-muted">
                        {{ $destinationName }}
                    </span>
                </li>

                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                    {{ $ticket->title }}
                </li>
            </ol>
        </nav>

        <div class="mb-5 reveal-up" style="transition-delay: 0.1s;">
            <h1 class="tour-title-detail mb-3">
                {{ $ticket->title }}
            </h1>

            <div class="d-flex flex-wrap gap-4 tour-info-line mb-4">
                <span class="d-flex align-items-center">
                    <i class="bi bi-geo-alt fs-5 me-2 text-danger"></i>
                    {{ __('Điểm đến:') }}
                    <strong class="ms-1 text-dark">
                        {{ $destinationName }}
                    </strong>
                </span>

                @if($ticket->provider_name)
                <span class="d-flex align-items-center">
                    <i class="bi bi-building fs-5 me-2 text-primary"></i>
                    {{ __('Nhà cung cấp:') }}
                    <strong class="ms-1 text-dark">
                        {{ $ticket->provider_name }}
                    </strong>
                </span>
                @endif

                <span class="d-flex align-items-center text-danger">
                    <i class="bi bi-x-circle-fill fs-5 me-2 text-danger"></i>
                    {{ $ticket->cancellation_policy ?? __('Vé không hỗ trợ hủy hoặc hoàn tiền') }}
                </span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Image Slider -->
                <div id="ticketImageCarousel" class="carousel slide mb-5 overflow-hidden-rounded shadow-sm" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($allImages as $index => $img)
                            <button type="button" data-bs-target="#ticketImageCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($allImages as $index => $img)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ $img }}" class="d-block w-100 gallery-main" alt="{{ $ticket->title }}" onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                            </div>
                        @endforeach
                    </div>
                    @if($allImages->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#ticketImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#ticketImageCarousel" data-bs-slide="next">
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
                                        {!! nl2br(e($ticket->description ?? __('Đang cập nhật mô tả vé tham quan.'))) !!}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Các loại vé -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-options">
                                <button class="accordion-button collapsed fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-options" aria-expanded="false" aria-controls="collapse-options">
                                    <i class="bi bi-ticket-perforated-fill text-success me-3 fs-4"></i>
                                    {{ __('Các loại vé') }}
                                </button>
                            </h2>
                            <div id="collapse-options" class="accordion-collapse collapse" aria-labelledby="heading-options" data-bs-parent="#masterAccordion">
                                <div class="accordion-body">
                                    @if($ticket->ticket_options->count() > 0)
                                        @foreach($ticket->ticket_options as $option)
                                            <div class="mb-4 pb-4 border-bottom last-border-none">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="flex-grow-1">
                                                        <h5 class="fw-bold text-dark mb-2">
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success me-2">
                                                                <i class="bi bi-ticket-perforated me-1"></i>
                                                            </span>
                                                            {{ $option->name }}
                                                        </h5>
                                                        @if($option->description)
                                                        <p class="tour-content-text mb-2">{!! nl2br(e($option->description)) !!}</p>
                                                        @endif
                                                        @if($option->conditions)
                                                        <div class="alert alert-info border-0 bg-opacity-10 py-2 px-3 mt-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                                            <i class="bi bi-info-circle-fill text-info me-2"></i>
                                                            <small class="text-dark">{{ $option->conditions }}</small>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-end ms-4" style="min-width: 140px;">
                                                        @if($option->original_price && $option->original_price > $option->price)
                                                        <div class="text-muted text-decoration-line-through small mb-1">
                                                            {{ format_currency($option->original_price) }}
                                                        </div>
                                                        @php
                                                            $discount = round((($option->original_price - $option->price) / $option->original_price) * 100);
                                                        @endphp
                                                        <span class="badge bg-danger mb-2">-{{ $discount }}%</span>
                                                        @endif
                                                        <div class="fs-3 fw-bold text-primary">
                                                            {{ format_currency($option->price) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">
                                            {{ __('Chưa có thông tin các loại vé.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin quan trọng -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-important">
                                <button class="accordion-button collapsed fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-important" aria-expanded="false" aria-controls="collapse-important">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-3 fs-4"></i>
                                    {{ __('Thông tin quan trọng') }}
                                </button>
                            </h2>
                            <div id="collapse-important" class="accordion-collapse collapse" aria-labelledby="heading-important" data-bs-parent="#masterAccordion">
                                <div class="accordion-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-3 d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-3 mt-1 fs-5"></i>
                                            <div>
                                                <strong class="text-dark d-block mb-1">{{ __('Giấy tờ tùy thân') }}</strong>
                                                <span class="tour-content-text">{{ __('Vui lòng mang theo CMND/CCCD hoặc Passport khi đến tham quan.') }}</span>
                                            </div>
                                        </li>
                                        <li class="mb-3 d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-3 mt-1 fs-5"></i>
                                            <div>
                                                <strong class="text-dark d-block mb-1">{{ __('Vé điện tử') }}</strong>
                                                <span class="tour-content-text">{{ __('Sau khi đặt vé thành công, bạn sẽ nhận được vé điện tử qua email.') }}</span>
                                            </div>
                                        </li>
                                        <li class="mb-3 d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-3 mt-1 fs-5"></i>
                                            <div>
                                                <strong class="text-dark d-block mb-1">{{ __('Thời gian sử dụng') }}</strong>
                                                <span class="tour-content-text">{{ __('Vé có hiệu lực trong thời gian được quy định tại mỗi loại vé.') }}</span>
                                            </div>
                                        </li>
                                        @if($ticket->provider_name)
                                        <li class="mb-0 d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-3 mt-1 fs-5"></i>
                                            <div>
                                                <strong class="text-dark d-block mb-1">{{ __('Nhà cung cấp') }}</strong>
                                                <span class="tour-content-text">{{ $ticket->provider_name }}</span>
                                            </div>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Chính sách hủy -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-policy">
                                <button class="accordion-button collapsed fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-policy" aria-expanded="false" aria-controls="collapse-policy">
                                    <i class="bi bi-x-octagon-fill text-danger me-3 fs-4"></i>
                                    {{ __('Chính sách hủy vé') }}
                                </button>
                            </h2>
                            <div id="collapse-policy" class="accordion-collapse collapse" aria-labelledby="heading-policy" data-bs-parent="#masterAccordion">
                                <div class="accordion-body">
                                    <div class="alert alert-danger mb-0 border-0" style="background-color: #fef2f2;">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-info-circle-fill text-danger me-3 mt-1 fs-5"></i>
                                            <div>
                                                <strong class="d-block mb-1 text-danger">{{ $ticket->cancellation_policy ?? __('Vé tham quan này không hỗ trợ hoàn hủy.') }}</strong>
                                                <p class="mb-0 tour-content-text text-dark">{{ __('Lưu ý: Tất cả các vé đã đặt và thanh toán thành công sẽ không được phép hủy, đổi ngày hoặc yêu cầu hoàn tiền dưới mọi hình thức.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Related Tours -->
                @if($ticket->tours && $ticket->tours->isNotEmpty())
                <div class="glass-panel p-4 p-md-5 reveal-up">
                    <h3 class="fw-bold mb-4 fs-4">
                        <i class="bi bi-briefcase me-2 text-info"></i>
                        {{ __('Tour liên quan') }}
                    </h3>
                    <div class="row g-3">
                        @foreach($ticket->tours->take(4) as $tour)
                        <div class="col-md-6">
                            <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm" style="transition: all 0.3s; border-radius: 12px;">
                                    <div class="row g-0">
                                        <div class="col-4">
                                            @php
                                                $tourImg = $tour->tour_images->where('is_primary', 1)->first()->image_url ?? 
                                                          $tour->tour_images->first()->image_url ?? 
                                                          'https://images.unsplash.com/photo-1488646953014-85cb44e25828?q=80&w=300';
                                            @endphp
                                            <img src="{{ $tourImg }}" 
                                                 alt="{{ $tour->title }}" 
                                                 class="w-100 h-100 object-fit-cover" 
                                                 style="border-radius: 12px 0 0 12px;">
                                        </div>
                                        <div class="col-8">
                                            <div class="card-body p-3">
                                                <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    {{ $tour->title }}
                                                </h6>
                                                <div class="text-muted small mb-2">
                                                    <i class="bi bi-calendar3 me-1"></i>{{ $tour->duration_days ?? 0 }}N{{ ($tour->duration_nights ?? 0) > 0 ? ($tour->duration_nights ?? 0) . 'Đ' : '' }}
                                                </div>
                                                <div class="fw-bold text-primary">{{ format_currency($tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0)) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="premium-card booking-sidebar p-4 reveal-up">
                    <div class="ticket-booking-notice">
                        <div class="d-flex flex-column mb-3 pb-3 border-bottom">
                            @php
                                $minPrice = $ticket->ticket_options->min('price') ?? 0;
                                $maxPrice = $ticket->ticket_options->max('price') ?? 0;
                            @endphp
                            <div class="d-flex align-items-end mb-2">
                                <div class="booking-price" id="displayPrice">
                                    {{ format_currency($minPrice) }}
                                </div>
                                @if($minPrice != $maxPrice)
                                <span class="ms-2 text-muted mb-2">
                                    - {{ format_currency($maxPrice) }}
                                </span>
                                @endif
                            </div>
                            <small class="text-muted">{{ __('Giá vé') }}</small>
                        </div>

                        <div class="alert alert-info rounded-3 mb-3">
                            <h5 class="alert-heading fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>{{ __('Lưu ý quan trọng') }}</h5>
                            <p class="mb-0">{{ __('Vé tham quan này hiện không hỗ trợ đặt lẻ. Bạn chỉ có thể mua kèm vé tham quan khi đặt các tour du lịch tương ứng tại hệ thống của chúng tôi.') }}</p>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('frontend.tours.index') }}" class="btn btn-primary btn-lg rounded-pill fw-bold py-3">
                                <i class="bi bi-search me-2"></i>{{ __('Tìm Tour Phù Hợp') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ticketOptionSelect = document.getElementById('ticketOptionSelect');
    const quantityInput = document.getElementById('quantity');
    const increaseBtn = document.getElementById('increaseQty');
    const decreaseBtn = document.getElementById('decreaseQty');
    const totalPriceEl = document.getElementById('totalPrice');
    const displayPriceEl = document.getElementById('displayPrice');
    const bookingBtn = document.getElementById('bookingBtn');
    const bookingForm = document.getElementById('ticketBookingForm');
    const visitDateInput = document.getElementById('visitDate');

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    function updateTotalPrice() {
        const selectedOption = ticketOptionSelect.options[ticketOptionSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price || 0);
        const quantity = parseInt(quantityInput.value || 0);
        const total = price * quantity;
        
        if (totalPriceEl) {
            totalPriceEl.textContent = formatCurrency(total);
        }
        
        if (displayPriceEl && price > 0) {
            displayPriceEl.textContent = formatCurrency(price);
        }

        // Enable/disable booking button
        if (bookingBtn) {
            const isValid = ticketOptionSelect.value && quantity >= 1 && visitDateInput.value;
            bookingBtn.disabled = !isValid;
            
            if (!isValid) {
                bookingBtn.classList.add('opacity-50');
            } else {
                bookingBtn.classList.remove('opacity-50');
            }
        }
    }

    function validateForm() {
        let isValid = true;
        let errorMessages = [];

        // Validate ticket option
        if (!ticketOptionSelect.value) {
            isValid = false;
            errorMessages.push('Vui lòng chọn loại vé');
            ticketOptionSelect.classList.add('is-invalid');
        } else {
            ticketOptionSelect.classList.remove('is-invalid');
        }

        // Validate visit date
        if (!visitDateInput.value) {
            isValid = false;
            errorMessages.push('Vui lòng chọn ngày sử dụng');
            visitDateInput.classList.add('is-invalid');
        } else {
            const selectedDate = new Date(visitDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                isValid = false;
                errorMessages.push('Ngày sử dụng phải từ hôm nay trở đi');
                visitDateInput.classList.add('is-invalid');
            } else {
                visitDateInput.classList.remove('is-invalid');
            }
        }

        // Validate quantity
        const quantity = parseInt(quantityInput.value);
        if (!quantity || quantity < 1) {
            isValid = false;
            errorMessages.push('Số lượng vé tối thiểu là 1');
        } else if (quantity > 20) {
            isValid = false;
            errorMessages.push('Số lượng vé tối đa là 20');
        }

        if (!isValid && errorMessages.length > 0) {
            alert(errorMessages.join('\n'));
        }

        return isValid;
    }

    // Form submit validation
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }

    if (ticketOptionSelect) {
        ticketOptionSelect.addEventListener('change', function() {
            updateTotalPrice();
            this.classList.remove('is-invalid');
        });
    }

    if (quantityInput) {
        quantityInput.addEventListener('input', updateTotalPrice);
    }

    if (visitDateInput) {
        visitDateInput.addEventListener('change', function() {
            updateTotalPrice();
            this.classList.remove('is-invalid');
        });
    }

    if (increaseBtn) {
        increaseBtn.addEventListener('click', function() {
            const current = parseInt(quantityInput.value);
            const max = parseInt(quantityInput.max);
            if (current < max) {
                quantityInput.value = current + 1;
                updateTotalPrice();
            }
        });
    }

    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', function() {
            const current = parseInt(quantityInput.value);
            const min = parseInt(quantityInput.min);
            if (current > min) {
                quantityInput.value = current - 1;
                updateTotalPrice();
            }
        });
    }

    // Set default visit date to tomorrow
    if (visitDateInput && !visitDateInput.value) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        visitDateInput.value = tomorrow.toISOString().split('T')[0];
    }

    // Initial calculation
    updateTotalPrice();
});
</script>
@endpush
