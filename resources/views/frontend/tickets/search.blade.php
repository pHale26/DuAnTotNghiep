@extends('layouts.master')

@section('title', 'Tìm kiếm Vé - Travel Wonder')

@section('content')

<!-- Page Header -->
<section class="hero-premium" style="height: 40vh; min-height: 400px;">
    <div class="hero-bg">
        @php
            $firstBanner = $banners->first();
            $bgImage = 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=2070';
            
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
        <h1 class="hero-title">{{ __('Vui Chơi Cùng TravelWonder') }}</h1>
        <p class="hero-subtitle">{{ __('Khám phá hàng ngàn công viên giải trí và hoạt động vui chơi hấp dẫn nhất.') }}</p>
    </div>
</section>

<!-- Search Widget -->
<div class="container search-widget-wrapper">
    <div class="glass-panel search-glass px-4 py-3">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <strong>{{ __('Lỗi!') }}</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('frontend.tickets.search') }}" method="GET" class="row g-3 align-items-end" id="searchForm">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">{{ __('Tìm kiếm') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="keyword" 
                           class="form-control search-form-control border-start-0 ps-0 @error('keyword') is-invalid @enderror" 
                           placeholder="{{ __('Tìm công viên, sự kiện...') }}" 
                           value="{{ old('keyword', request('keyword')) }}"
                           maxlength="255">
                </div>
                @error('keyword')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">{{ __('Điểm đến') }}</label>
                <select name="destination_id" class="form-select search-form-control @error('destination_id') is-invalid @enderror">
                    <option value="">{{ __('Tất cả điểm đến') }}</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ old('destination_id', request('destination_id')) == $dest->id ? 'selected' : '' }}>
                            {{ $dest->name }}
                        </option>
                    @endforeach
                </select>
                @error('destination_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">{{ __('Ngày sử dụng') }}</label>
                <input type="date" name="use_date" 
                       class="form-control search-form-control @error('use_date') is-invalid @enderror" 
                       value="{{ old('use_date', request('use_date')) }}" 
                       min="{{ date('Y-m-d') }}">
                @error('use_date')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-search-primary w-100">
                    <i class="bi bi-search me-1"></i>{{ __('Tìm vé') }}
                </button>
            </div>
        </form>

        @if(request()->hasAny(['keyword', 'destination_id', 'use_date']))
        <div class="d-flex align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
            <small class="text-muted me-2">
                <i class="bi bi-funnel me-1"></i>
                {{ __('Tìm thấy') }} <strong class="text-primary">{{ $tickets->total() }}</strong> {{ __('kết quả') }}
            </small>
            
            @if(request('keyword'))
                <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}" class="badge bg-secondary bg-opacity-10 text-secondary text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-search me-1"></i>{{ request('keyword') }} <i class="bi bi-x"></i>
                </a>
            @endif

            @if(request('destination_id'))
                @php $dest = $destinations->firstWhere('id', request('destination_id')); @endphp
                @if($dest)
                <a href="{{ request()->fullUrlWithQuery(['destination_id' => null]) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-geo-alt me-1"></i>{{ $dest->name }} <i class="bi bi-x"></i>
                </a>
                @endif
            @endif

            @if(request('use_date'))
                <a href="{{ request()->fullUrlWithQuery(['use_date' => null]) }}" class="badge bg-info bg-opacity-10 text-info text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse(request('use_date'))->format('d/m/Y') }} <i class="bi bi-x"></i>
                </a>
            @endif

            <a href="{{ route('frontend.tickets.search') }}" class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
                {{ __('Xóa bộ lọc') }}
            </a>
        </div>
        <style>
            .hover-opacity:hover { opacity: 0.8; }
        </style>
        @endif
    </div>
</div>

<!-- Search Layout -->
<section class="container reveal-up mb-5" style="background-color: transparent;">
    <div class="hot-deal-section">
        <div class="hot-deal-bg"></div>
        <div class="container position-relative z-index-1">
            <div id="results-container" class="px-3">
                @include('frontend.tickets._results')
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('searchForm');
        const container = document.getElementById('results-container');
        const keywordInput = form.querySelector('[name="keyword"]');
        const useDateInput = form.querySelector('[name="use_date"]');

        // Client-side validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessages = [];

            // Validate keyword length
            if (keywordInput.value.trim().length > 255) {
                isValid = false;
                errorMessages.push('Từ khóa tìm kiếm không được vượt quá 255 ký tự');
                keywordInput.classList.add('is-invalid');
            } else {
                keywordInput.classList.remove('is-invalid');
            }

            // Validate use_date if provided
            if (useDateInput.value) {
                const selectedDate = new Date(useDateInput.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate < today) {
                    isValid = false;
                    errorMessages.push('Ngày sử dụng phải từ hôm nay trở đi');
                    useDateInput.classList.add('is-invalid');
                } else {
                    useDateInput.classList.remove('is-invalid');
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert(errorMessages.join('\n'));
                return false;
            }
        });

        // Remove error on input
        keywordInput.addEventListener('input', function() {
            if (this.value.trim().length <= 255) {
                this.classList.remove('is-invalid');
            }
        });

        useDateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate >= today) {
                this.classList.remove('is-invalid');
            }
        });

        function fetchResults(url) {
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

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.ajax-pagination a');
            if (pageLink) {
                e.preventDefault();
                fetchResults(pageLink.href);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
</script>
@endpush
