@extends('layouts.master')

@section('title', 'Tour trọn gói - Travel Wonder')

@section('content')

    <!-- Page Header -->
    <section class="hero-premium" style="height: 40vh; min-height: 400px;">
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
            <h1 class="hero-title">{{ __('Tour Du Lịch Trọn Gói') }}</h1>
            <p class="hero-subtitle">{{ __('Trải nghiệm dịch vụ 5 sao với giá ưu đãi tốt nhất.') }}</p>
        </div>
    </section>

    <!-- Search Widget -->
    <div class="container search-widget-wrapper">
        <div class="glass-panel search-glass px-4 py-3">
            <form action="{{ route('frontend.tours.index') }}" id="searchForm" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                    <select name="destination_id"
                        class="form-select search-form-control {{ isset($filterErrors['destination_id']) ? 'is-invalid' : '' }}">
                        <option value="">{{ __('Tất cả điểm đến') }}</option>
                        @foreach($allDestinations as $dest)
                            <option value="{{ $dest->id }}" {{ request('destination_id') == $dest->id ? 'selected' : '' }}>
                                {{ $dest->name }}</option>
                        @endforeach
                    </select>
                    @if(isset($filterErrors['destination_id']))
                        <div class="text-danger small mt-1 position-absolute">{{ $filterErrors['destination_id'][0] }}</div>
                    @endif
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">{{ __('Ngày khởi hành vào') }}</label>
                    <input type="date" name="departure_date"
                        class="form-control search-form-control {{ isset($filterErrors['departure_date']) ? 'is-invalid' : '' }}"
                        value="{{ request('departure_date') ?? request('date') }}" min="{{ date('Y-m-d') }}">
                    @if(isset($filterErrors['departure_date']))
                        <div class="text-danger small mt-1 position-absolute">{{ $filterErrors['departure_date'][0] }}</div>
                    @endif
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">{{ __('Ngân sách') }}</label>
                    <select name="budget"
                        class="form-select search-form-control {{ isset($filterErrors['budget']) ? 'is-invalid' : '' }}">
                        <option value="all">{{ __('Tất cả mức giá') }}</option>
                        <option value="under_1m" {{ request('budget') == 'under_1m' ? 'selected' : '' }}>
                            {{ __('Dưới 1 triệu') }}</option>
                        <option value="1m_2m" {{ request('budget') == '1m_2m' ? 'selected' : '' }}>
                            {{ __('1 - 2 triệu') }}</option>
                        <option value="2m_4m" {{ request('budget') == '2m_4m' ? 'selected' : '' }}>
                            {{ __('2 - 4 triệu') }}</option>
                        <option value="over_4m" {{ request('budget') == 'over_4m' ? 'selected' : '' }}>
                            {{ __('Trên 4 triệu') }}</option>
                    </select>
                    @if(isset($filterErrors['budget']))
                        <div class="text-danger small mt-1 position-absolute">{{ $filterErrors['budget'][0] }}</div>
                    @endif
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">{{ __('Thời gian') }}</label>
                    <select name="duration"
                        class="form-select search-form-control">
                        <option value="">{{ __('Tất cả thời gian') }}</option>
                        <option value="2d1n" {{ request('duration') == '2d1n' ? 'selected' : '' }}>2N1Đ</option>
                        <option value="3d2n" {{ request('duration') == '3d2n' ? 'selected' : '' }}>3N2Đ</option>
                        <option value="4d3n" {{ request('duration') == '4d3n' ? 'selected' : '' }}>4N3Đ</option>
                        <option value="5d4n" {{ request('duration') == '5d4n' ? 'selected' : '' }}>5N4Đ</option>
                        <option value="6d5n" {{ request('duration') == '6d5n' ? 'selected' : '' }}>6N5Đ</option>
                        <option value="7d6n" {{ request('duration') == '7d6n' ? 'selected' : '' }}>7N6Đ</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-search-primary w-100">
                        <i class="bi bi-search me-2"></i>{{ __('Tìm kiếm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hot Deal Combo Tours -->
    <section class="container reveal-up mb-5">
        <div class="hot-deal-section">
            <div class="hot-deal-bg"></div>
            <div class="container position-relative z-index-1" id="results-container">
                @include('frontend.tours._results_list')
            </div>
        </div>
    </section>




    <style>
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

        /* Tour Preview Overlay Styles */
        .tour-preview-wrapper {
            position: relative;
            cursor: pointer;
        }

        .tour-preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10;
            background: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0.3) 0%,
                    rgba(0, 0, 0, 0.5) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .tour-preview-content {
            width: 100%;
            height: 100%;
            padding: 20px;
            color: #ffffff;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .tour-preview-content h5 {
            color: #ffffff !important;
            font-size: 0.95rem;
            line-height: 1.3;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .tour-preview-content .badge {
            font-size: 0.65rem;
            padding: 4px 8px;
            white-space: nowrap;
        }

        .tour-preview-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
        }

        .preview-item {
            display: flex;
            gap: 10px;
            align-items: start;
        }

        .preview-item i {
            font-size: 1rem;
            margin-top: 2px;
            flex-shrink: 0;
            color: #4ade80;
            opacity: 0.95;
        }

        .preview-item small {
            font-size: 0.65rem;
            line-height: 1.2;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 600;
        }

        .preview-item strong {
            font-size: 0.85rem;
            line-height: 1.3;
            color: #ffffff;
            font-weight: 600;
        }

        .preview-item .text-success {
            color: #4ade80 !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
        }

        .preview-item.border-top {
            border-top: 1px solid rgba(255, 255, 255, 0.15) !important;
            padding-top: 8px;
            margin-top: 4px;
        }

        /* Adjust combo card */
        .combo-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
        }

        .tour-preview-wrapper:hover .combo-card {
            transform: scale(1.03);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        /* Button styling in overlay */
        .tour-preview-content .btn-primary {
            background: linear-gradient(135deg, #4ade80 0%, #3b82f6 100%);
            color: #ffffff;
            border: none;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 10px 16px;
            transition: all 0.3s ease;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
            border-radius: 8px;
        }

        .tour-preview-content .btn-primary:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #4ade80 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 222, 128, 0.5);
        }

        /* Price styling */
        .preview-item .fs-5 {
            font-size: 1.15rem !important;
            color: #4ade80 !important;
            font-weight: 800;
            text-shadow: 0 2px 8px rgba(74, 222, 128, 0.3);
        }

        /* Smooth badge styling */
        .bg-primary-subtle {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Scrollbar for overlay content */
        .tour-preview-content::-webkit-scrollbar {
            width: 3px;
        }

        .tour-preview-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 2px;
        }

        .tour-preview-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        .tour-preview-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        /* Icon colors for different types */
        .preview-item:nth-child(1) i {
            color: #f59e0b;
        }

        /* Destination - amber */
        .preview-item:nth-child(2) i {
            color: #3b82f6;
        }

        /* Duration - blue */
        .preview-item:nth-child(3) i {
            color: #8b5cf6;
        }

        /* Departure - purple */
        .preview-item:nth-child(4) i {
            color: #ec4899;
        }

        /* Schedule - pink */
        .preview-item.border-top i {
            color: #4ade80;
        }

        /* Price - green */

        /* Mobile adjustments */
        @media (max-width: 767.98px) {
            .tour-preview-content {
                padding: 16px;
            }

            .tour-preview-content h5 {
                font-size: 0.9rem;
            }

            .preview-item {
                gap: 8px;
            }

            .preview-item i {
                font-size: 0.95rem;
            }

            .preview-item strong {
                font-size: 0.8rem;
            }

            .preview-item .fs-5 {
                font-size: 1rem !important;
            }
        }
    </style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('searchForm');
        const container = document.getElementById('results-container');

        if (!form || !container) return;

        function fetchResults(url) {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) overlay.classList.remove('d-none');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(html => {
                container.innerHTML = html;
                window.history.pushState({}, '', url);
            })
            .catch(err => {
                console.error('AJAX search error:', err);
                if (overlay) overlay.classList.add('d-none');
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            
            // Remove empty keys
            for (let [key, val] of formData.entries()) {
                if (!val || val === 'all') {
                    formData.delete(key);
                }
            }

            const params = new URLSearchParams(formData);
            const url = form.action + '?' + params.toString();
            fetchResults(url);
        });

        // Trigger submit on field changes
        form.addEventListener('change', function(e) {
            if (e.target.tagName === 'SELECT' || e.target.type === 'date') {
                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });

        // Category chips filtering
        document.addEventListener('click', function(e) {
            const catBtn = e.target.closest('[data-category-btn]');
            if (catBtn) {
                e.preventDefault();
                const catId = catBtn.getAttribute('data-category-btn');

                let catInput = form.querySelector('input[name="category_id"]');
                if (!catInput) {
                    catInput = document.createElement('input');
                    catInput.type = 'hidden';
                    catInput.name = 'category_id';
                    form.appendChild(catInput);
                }
                catInput.value = catId;

                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });

        // Remove filter badges
        document.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('[data-remove-filter]');
            if (removeBtn) {
                e.preventDefault();
                const filterName = removeBtn.getAttribute('data-remove-filter');

                if (filterName === 'category_id') {
                    const catInput = form.querySelector('input[name="category_id"]');
                    if (catInput) catInput.value = 'all';
                } else {
                    const input = form.querySelector(`[name="${filterName}"]`);
                    if (input) {
                        if (input.tagName === 'SELECT') {
                            input.value = filterName === 'budget' ? 'all' : '';
                        } else {
                            input.value = '';
                        }
                    }
                }
                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });

        // Clear all filters
        document.addEventListener('click', function(e) {
            const clearBtn = e.target.closest('#clear-all-filters') || e.target.closest('#view-all-tours-btn');
            if (clearBtn) {
                e.preventDefault();
                form.reset();
                const catInput = form.querySelector('input[name="category_id"]');
                if (catInput) catInput.value = 'all';

                form.querySelectorAll('select').forEach(sel => sel.value = sel.name === 'budget' ? 'all' : '');
                form.querySelectorAll('input[type="date"]').forEach(inp => inp.value = '');

                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });

        // AJAX Pagination
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.ajax-pagination a');
            if (pageLink) {
                e.preventDefault();
                fetchResults(pageLink.href);
                container.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>

@endsection