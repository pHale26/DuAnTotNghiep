@extends('layouts.master')

@section('title', 'Vé Tham Quan - Travel Wonder')

@section('content')

    <!-- Page Header -->
    <section class="hero-premium" style="height: 40vh; min-height: 400px;">
        <div class="hero-bg">
            <img src="https://images.unsplash.com/photo-1464037866556-6812c9d1c72e?q=80&w=2070" alt="Tickets Hero">
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">{{ __('Vé Tham Quan & Vui Chơi') }}</h1>
            <p class="hero-subtitle">{{ __('Hàng ngàn địa điểm vui chơi giải trí đang chờ bạn khám phá') }}</p>
        </div>
    </section>

    <!-- Search Widget -->
    <div class="container search-widget-wrapper">
        <div class="glass-panel search-glass px-4 py-3">
            <form action="{{ route('frontend.tickets.search') }}" method="GET" class="row g-3 align-items-end"
                id="indexSearchForm">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">{{ __('Tìm kiếm') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="keyword" class="form-control search-form-control border-start-0 ps-0"
                            placeholder="{{ __('Tìm công viên, sự kiện...') }}" value="{{ request('keyword') }}"
                            maxlength="255">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                    <select name="destination_id" class="form-select search-form-control">
                        <option value="">{{ __('Tất cả điểm đến') }}</option>
                        @php
                            $destinations = \App\Models\Destination::withCount('tickets')->having('tickets_count', '>', 0)->orderBy('name')->get();
                        @endphp
                        @foreach($destinations as $dest)
                            <option value="{{ $dest->id }}" {{ request('destination_id') == $dest->id ? 'selected' : '' }}>
                                {{ $dest->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">{{ __('Ngày sử dụng') }}</label>
                    <input type="date" name="use_date" class="form-control search-form-control"
                        value="{{ request('use_date') }}" min="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-search-primary w-100">
                        <i class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}
                    </button>
                </div>
            </form>

            @if(request()->hasAny(['keyword', 'destination_id', 'use_date']))
                <div class="d-flex align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
                    <small class="text-muted me-2">
                        <i class="bi bi-funnel me-1"></i>
                        {{ __('Đang lọc kết quả') }}
                    </small>

                    @if(request('keyword'))
                        <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}"
                            class="badge bg-secondary bg-opacity-10 text-secondary text-decoration-none p-2 rounded-pill hover-opacity">
                            <i class="bi bi-search me-1"></i>{{ request('keyword') }} <i class="bi bi-x"></i>
                        </a>
                    @endif

                    @if(request('destination_id'))
                        @php $dest = $destinations->firstWhere('id', request('destination_id')); @endphp
                        @if($dest)
                            <a href="{{ request()->fullUrlWithQuery(['destination_id' => null]) }}"
                                class="badge bg-primary bg-opacity-10 text-primary text-decoration-none p-2 rounded-pill hover-opacity">
                                <i class="bi bi-geo-alt me-1"></i>{{ $dest->name }} <i class="bi bi-x"></i>
                            </a>
                        @endif
                    @endif

                    @if(request('use_date'))
                        <a href="{{ request()->fullUrlWithQuery(['use_date' => null]) }}"
                            class="badge bg-info bg-opacity-10 text-info text-decoration-none p-2 rounded-pill hover-opacity">
                            <i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse(request('use_date'))->format('d/m/Y') }} <i
                                class="bi bi-x"></i>
                        </a>
                    @endif

                    <a href="{{ route('frontend.tickets.index') }}"
                        class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
                        {{ __('Xóa bộ lọc') }}
                    </a>
                </div>
                <style>
                    .hover-opacity:hover {
                        opacity: 0.8;
                    }
                </style>
            @endif
        </div>
    </div>

    <!-- Hot Deal Tickets -->
    <section class="container reveal-up mb-5">
        <div class="hot-deal-section">
            <div class="hot-deal-bg"></div>
            <div class="container position-relative z-index-1">
                <div class="text-center mb-5">
                </div>

                <div class="row g-4 px-3">
                    @forelse($tickets as $ticket)
                        <div class="col-12 col-md-6 col-lg-3">
                            <a href="{{ route('frontend.tickets.show', $ticket->slug) }}"
                                class="text-decoration-none h-100 d-block">
                                <div class="combo-card h-100">
                                    <div class="combo-card-img-wrapper">
                                        @php
                                            $primaryImage = $ticket->ticket_images->where('is_primary', true)->first();
                                            $firstImage = $primaryImage ?? $ticket->ticket_images->first();
                                            $fallbackImage = 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=800';

                                            $destName = mb_strtolower($ticket->destination->name ?? '', 'UTF-8');
                                            if (str_contains($destName, 'nha trang') || str_contains($destName, 'phú quốc')) {
                                                $fallbackImage = 'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=800';
                                            } elseif (str_contains($destName, 'sapa') || str_contains($destName, 'đà nẵng')) {
                                                $fallbackImage = 'https://images.unsplash.com/photo-1571509930722-df38ccfb8611?q=80&w=800';
                                            }

                                            $imageUrl = $firstImage ? $firstImage->image_url : $fallbackImage;

                                            $minPrice = $ticket->ticket_options->min('price') ?? 0;
                                            $minOriginalPrice = $ticket->ticket_options->min('original_price');
                                            $hasDiscount = $minOriginalPrice && $minOriginalPrice > $minPrice;
                                          @endphp



                                        <img src="{{ $imageUrl }}" alt="{{ $ticket->title }}"
                                            onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                                    </div>
                                    <div class="combo-card-body">
                                        <h3 class="combo-title">{{ $ticket->title }}</h3>
                                        <div class="combo-location">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ $ticket->destination->name ?? 'Việt Nam' }}</span>
                                        </div>
                                        <div class="combo-footer">
                                            <div>
                                                <div class="combo-price-label">{{ __('Giá từ:') }}</div>
                                                <div class="combo-price-val">{{ format_currency($minPrice) }}</div>
                                            </div>
                                            <span class="btn btn-combo-detail">{{ __('Xem chi tiết') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="mb-3 text-muted">
                                <i class="bi bi-ticket-perforated" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-muted">{{ __('Chưa có vé tham quan nào') }}</h5>
                            <p class="text-muted">{{ __('Vui lòng quay lại sau.') }}</p>
                        </div>
                    @endforelse
                </div>

                @if($tickets->hasPages())
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $tickets->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <style>
        /* Ticket Card Styles - Refined and Beautiful */
        .combo-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .combo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12), 0 8px 10px rgba(0, 0, 0, 0.08);
        }

        .combo-card-img-wrapper {
            position: relative;
            width: 100%;
            padding-top: 75%;
            /* 4:3 aspect ratio */
            overflow: hidden;
            background: #f0f0f0;
        }

        .combo-card-img-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .combo-card:hover .combo-card-img-wrapper img {
            transform: scale(1.08);
        }

        /* Discount Badge */
        .tour-duration-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
            background: linear-gradient(135deg, #FF4858 0%, #FF3346 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 6px 14px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(255, 72, 88, 0.4);
            line-height: 1;
        }

        .combo-card-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .combo-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 48px;
        }

        .combo-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #718096;
            font-size: 0.875rem;
            margin: 0;
        }

        .combo-location i {
            color: #4299e1;
            font-size: 1rem;
        }

        .combo-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        .combo-price-label {
            font-size: 0.75rem;
            color: #a0aec0;
            margin-bottom: 2px;
            font-weight: 500;
        }

        .combo-price-val {
            font-size: 1.125rem;
            font-weight: 700;
            color: #f59e0b;
            line-height: 1.2;
        }

        .btn-combo-detail {
            background: #3b82f6;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-combo-detail:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            color: #ffffff;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .combo-card-img-wrapper {
                padding-top: 66.67%;
                /* 3:2 aspect ratio on mobile */
            }

            .combo-title {
                font-size: 0.9375rem;
                min-height: 44px;
            }

            .combo-price-val {
                font-size: 1rem;
            }

            .btn-combo-detail {
                padding: 6px 14px;
                font-size: 0.8125rem;
            }
        }

        @media (max-width: 576px) {
            .combo-card-body {
                padding: 14px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('indexSearchForm');
            if (!form) return;

            const keywordInput = form.querySelector('[name="keyword"]');
            const useDateInput = form.querySelector('[name="use_date"]');

            // Client-side validation
            form.addEventListener('submit', function (e) {
                let isValid = true;
                let errorMessages = [];

                // Validate keyword length
                if (keywordInput && keywordInput.value.trim().length > 255) {
                    isValid = false;
                    errorMessages.push('Từ khóa tìm kiếm không được vượt quá 255 ký tự');
                }

                // Validate use_date if provided
                if (useDateInput && useDateInput.value) {
                    const selectedDate = new Date(useDateInput.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate < today) {
                        isValid = false;
                        errorMessages.push('Ngày sử dụng phải từ hôm nay trở đi');
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessages.join('\n'));
                    return false;
                }
            });
        });
    </script>
@endpush