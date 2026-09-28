@extends('layouts.master')

@section('title', 'Tìm kiếm Tour - Travel Wonder')

@section('content')

<!-- Hero Section -->
<section class="hero-premium">
    <div class="hero-bg">
        @php
            $firstBanner = $banners->first();
            $bgImage = 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070'; // fallback
            
            if ($firstBanner && $firstBanner->image_url) {
                $bgImage = Str::startsWith($firstBanner->image_url, ['http://', 'https://']) 
                           ? $firstBanner->image_url 
                           : asset($firstBanner->image_url);
            }
        @endphp
        <img src="{{ $bgImage }}" alt="Hero Background">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">{{ __('Khám Phá Thế Giới Cùng TravelWonder') }}</h1>
        <p class="hero-subtitle">{{ __('Hàng ngàn điểm đến tuyệt đẹp và trải nghiệm khó quên đang chờ đón bạn. Bắt đầu hành trình ngay hôm nay!') }}</p>
    </div>
</section>

<!-- Search Widget -->
<div class="container search-widget-wrapper">
    <div class="glass-panel search-glass">
        <ul class="nav nav-tabs search-tabs" id="searchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tour-tab" data-bs-toggle="tab" data-bs-target="#tour"
                    type="button" role="tab"><i class="bi bi-briefcase-fill me-2"></i>{{ __('Tour Du Lịch') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ticket-tab" data-bs-toggle="tab" data-bs-target="#ticket" type="button"
                    role="tab"><i class="bi bi-ticket-perforated-fill me-2"></i>{{ __('Vé Tham Quan') }}</button>
            </li>
        </ul>
        <div class="tab-content px-3 pb-3" id="searchTabsContent">
            <div class="tab-pane fade show active" id="tour" role="tabpanel">
                <form action="{{ route('frontend.tours.search') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                        <div class="input-group autocomplete-wrapper">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
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
                        <button type="submit" class="btn btn-search-primary w-100"><i
                                class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}</button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="ticket" role="tabpanel">
                <form action="#" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label class="form-label text-muted small fw-bold">{{ __('Tìm công viên giải trí, sự kiện') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="keyword" class="form-control search-form-control border-start-0 ps-0"
                                placeholder="{{ __('Tìm kiếm hoạt động vui chơi...') }}" value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">{{ __('Ngày sử dụng') }}</label>
                        <input type="date" name="date" class="form-control search-form-control" value="{{ request('date') }}" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search-primary w-100">{{ __('Tìm vé') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Search Layout -->
<section class="py-5" style="background-color: #f8fafc;">
    <div class="container">
        <form action="{{ route('frontend.tours.search') }}" method="GET" id="searchForm">
            <div class="row g-4">
                
<!-- Left Sidebar Filters -->
                <div class="col-lg-3">
                    <div class="sidebar-filter">
                        <div class="d-flex justify-content-between align-items-center mb-4 gap-2">
                            <div class="fw-bold text-nowrap" style="font-size: 0.95rem;"><i class="bi bi-sliders me-2"></i>{{ __('Tìm kiếm nâng cao') }}</div>
                            <a href="{{ route('frontend.tours.search') }}" class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1 text-nowrap px-2 py-1" style="font-size: 0.75rem; border-color: #dee2e6;"><i class="bi bi-arrow-counterclockwise"></i> {{ __('Đặt lại') }}</a>
                        </div>

                        {{-- Active filter summary --}}
                        @php
                            $activeFilters = array_filter([
                                'keyword'        => request('keyword'),
                                'transport'      => request('transport'),
                                'departure_id'   => request('departure_id'),
                                'destination_id' => request('destination_id'),
                                'date'           => request('date'),
                                'budget'         => request('budget'),
                                'duration'       => request('duration'),
                            ]);
                        @endphp
                        @if(count($activeFilters) > 0)
                        <div class="mb-3 d-flex flex-wrap gap-1">
                            @if(request('keyword'))
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-normal py-1 px-2 rounded-pill" style="font-size:0.75rem;">
                                    <i class="bi bi-geo-alt me-1"></i>{{ request('keyword') }}
                                </span>
                            @endif
                            @if(request('transport'))
                                <span class="badge bg-info bg-opacity-10 text-info fw-normal py-1 px-2 rounded-pill" style="font-size:0.75rem;">
                                    <i class="bi bi-{{ request('transport') === 'bay' ? 'airplane' : 'car-front' }} me-1"></i>{{ request('transport') === 'bay' ? 'Chuyến bay' : 'Xe' }}
                                </span>
                            @endif
                            @if(request('budget'))
                                @php $bl = ['under_5m'=>'< ₫5M','5m_to_10m'=>'₫5-10M','10m_to_20m'=>'₫10-20M','over_20m'=>'> ₫20M']; @endphp
                                <span class="badge bg-success bg-opacity-10 text-success fw-normal py-1 px-2 rounded-pill" style="font-size:0.75rem;">
                                    {{ $bl[request('budget')] ?? '' }}
                                </span>
                            @endif
                            @if(request('duration'))
                                @php $dl = ['2d1n'=>'2N1Đ','3d2n'=>'3N2Đ','4d3n'=>'4N3Đ','5d4n'=>'5N4Đ','6d5n'=>'6N5Đ','7d6n'=>'7N6Đ']; @endphp
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-normal py-1 px-2 rounded-pill" style="font-size:0.75rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $dl[request('duration')] ?? '' }}
                                </span>
                            @endif
                        </div>
                        @endif



                        <!-- Điểm đến -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Điểm đến') }}</div>
                            <select class="form-select" name="destination_id" id="destination-select">
                                <option value="">{{ __('Tất cả') }}</option>
                                @foreach($destinations as $destination)
                                    <option value="{{ $destination->id }}" {{ request('destination_id') == $destination->id ? 'selected' : '' }}>
                                        {{ $destination->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ngày đi từ -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Ngày đi từ') }}</div>
                            <input type="date" class="form-control" name="date"
                                value="{{ request('date') }}" min="{{ date('Y-m-d') }}">
                        </div>
  
                       <!-- Ngân sách -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Ngân sách') }}</div>
                            <div class="filter-btn-grid">
                                <input type="radio" class="btn-check" name="budget" id="budget0" value="" {{ !request('budget') ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget0">{{ __('Tất cả') }}</label>

                                <input type="radio" class="btn-check" name="budget" id="budget1" value="under_5m" {{ request('budget') == 'under_5m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget1">{{ __('Dưới ₫5M') }}</label>

                                <input type="radio" class="btn-check" name="budget" id="budget2" value="5m_to_10m" {{ request('budget') == '5m_to_10m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget2">{{ __('₫5M - ₫10M') }}</label>

                                <input type="radio" class="btn-check" name="budget" id="budget3" value="10m_to_20m" {{ request('budget') == '10m_to_20m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget3">{{ __('₫10M - ₫20M') }}</label>

                                <input type="radio" class="btn-check" name="budget" id="budget4" value="over_20m" {{ request('budget') == 'over_20m' ? 'checked' : '' }}>
                                <label class="filter-btn" for="budget4">{{ __('Trên ₫20M') }}</label>
                            </div>
                        </div>

                        <!-- Thời gian -->
                        <div class="mb-4">
                            <div class="filter-section-title">{{ __('Thời gian') }}</div>
                            <div class="filter-btn-grid">
                                <input type="radio" class="btn-check" name="duration" id="duration0" value="" {{ !request('duration') ? 'checked' : '' }}>
                                <label class="filter-btn" for="duration0">{{ __('Tất cả') }}</label>

                                <input type="radio" class="btn-check" name="duration" id="duration_2d1n" value="2d1n" {{ request('duration') == '2d1n' ? 'checked' : '' }}>
                                <label class="filter-btn" for="duration_2d1n">{{ __('2N1Đ') }}</label>

                                <input type="radio" class="btn-check" name="duration" id="duration_3d2n" value="3d2n" {{ request('duration') == '3d2n' ? 'checked' : '' }}>
                                <label class="filter-btn" for="duration_3d2n">{{ __('3N2Đ') }}</label>

                                <input type="radio" class="btn-check" name="duration" id="duration_4d3n" value="4d3n" {{ request('duration') == '4d3n' ? 'checked' : '' }}>
                                <label class="filter-btn" for="duration_4d3n">{{ __('4N3Đ') }}</label>

                                <input type="radio" class="btn-check" name="duration" id="duration_5d4n" value="5d4n" {{ request('duration') == '5d4n' ? 'checked' : '' }}>
                                <label class="filter-btn" for="duration_5d4n">{{ __('5N4Đ') }}</label>

                                <input type="radio" class="btn-check" name="duration" id="duration_6d5n" value="6d5n" {{ request('duration') == '6d5n' ? 'checked' : '' }}>
                                <label class="filter-btn" for="duration_6d5n">{{ __('6N5Đ') }}</label>

                                <input type="radio" class="btn-check" name="duration" id="duration_7d6n" value="7d6n" {{ request('duration') == '7d6n' ? 'checked' : '' }}>
                                <label class="filter-btn" for="duration_7d6n">{{ __('7N6Đ') }}</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">
                            <i class="bi bi-funnel me-2"></i>{{ __('Áp dụng bộ lọc') }}
                        </button>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-lg-9">
                    <div id="results-container" class="position-relative">
                        @include('frontend.tours._results')
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<style>
    .ts-control {
        border-radius: 8px;
        padding: 8px 12px;
        border-color: #dee2e6;
    }
    .ts-dropdown {
        border-radius: 8px;
    }
    .tour-duration-badge {
        position: absolute;
        top: 12px;
        left: 12px;
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

    /* Favorite button styling copy-pasted from index.blade.php */
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
        box-shadow: 0 6px 18px rgba(0, 0, 0, .15);
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
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('destination-select')) {
            new TomSelect('#destination-select', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "{{ __('Chọn điểm đến...') }}",
                onChange: function(value) {
                    const form = document.getElementById('searchForm');
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                    }
                }
            });
        }
        const form = document.getElementById('searchForm');
        const container = document.getElementById('results-container');

        function fetchResults(url) {
            const loading = document.getElementById('loading-overlay');
            if(loading) loading.classList.remove('d-none');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error fetching data:', error));
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const url = form.action + '?' + params.toString();
            fetchResults(url);
        });

        form.addEventListener('change', function(e) {
            if (e.target.name === 'sort' || e.target.type === 'radio' || e.target.type === 'checkbox' || e.target.tagName === 'SELECT' || e.target.type === 'date') {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'sortSelect') {
                // Remove the old hidden sort input if exists
                let oldSort = form.querySelector('input[name="sort"][type="hidden"]');
                if (oldSort) oldSort.remove();
                
                // Add new hidden sort input
                const hiddenSort = document.createElement('input');
                hiddenSort.type = 'hidden';
                hiddenSort.name = 'sort';
                hiddenSort.value = e.target.value;
                form.appendChild(hiddenSort);
                
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        });

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.ajax-pagination a');
            if (pageLink) {
                e.preventDefault();
                fetchResults(pageLink.href);
                // Scroll to top of results
                document.querySelector('.search-results-header').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
@endsection
