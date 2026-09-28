<!-- Top Bar -->
<div class="search-results-header">
    <div class="search-results-count">
        {{ __('Kết quả:') }} <span>{{ $tours->total() }} {{ __('gói combo') }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small text-nowrap">{{ __('Sắp xếp theo:') }}</span>
        <select class="form-select border-0 bg-transparent fw-medium" name="sort" id="sortSelect" style="width: auto;">
            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Mới nhất') }}</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Giá từ thấp đến cao') }}</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Giá từ cao đến thấp') }}</option>
        </select>
    </div>
</div>

<!-- Grid of Tours -->
<div class="row g-4 position-relative">
    <div id="loading-overlay" class="position-absolute w-100 h-100 d-none" style="background: rgba(255,255,255,0.7); z-index: 10; top: 0; left: 0;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    
    @forelse($tours as $tour)
    <div class="col-12 col-md-6 col-lg-4">
        <a href="{{ route('frontend.tours.show', $tour->slug) }}" class="text-decoration-none h-100 d-block">
            <div class="combo-card h-100">
                <div class="combo-card-img-wrapper" style="height: 240px;">
                    @if($tour->duration_days)
                    <div class="tour-duration-badge">
                        {{ $tour->duration_days }}N{{ $tour->duration_nights > 0 ? $tour->duration_nights . 'Đ' : '' }}
                    </div>
                    @endif

                    @php
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
                        $destinationName = optional($tour->destination)->name ?: 'Việt Nam';
                        $stars = $tour->reviews()->where('is_hidden', false)->avg('rating') ? round($tour->reviews()->where('is_hidden', false)->avg('rating')) : 0;
                    @endphp
                    <img src="{{ $tourImage }}"
                         alt="{{ $tour->title }}"
                         class="w-100 h-100 object-fit-cover"
                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';">
                </div>
                <div class="combo-card-body">
                    <h3 class="combo-title">{{ $tour->title }}</h3>
                    <div class="combo-stars">
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
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center py-5 rounded-4 bg-white border-0 shadow-sm">
            <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
            <h5 class="fw-bold">{{ __('Không tìm thấy kết quả nào') }}</h5>
            <p class="text-muted">{{ __('Vui lòng thử điều chỉnh lại bộ lọc tìm kiếm.') }}</p>
            <a href="{{ route('frontend.tours.search') }}" class="btn btn-outline-primary rounded-pill mt-2">{{ __('Xóa bộ lọc') }}</a>
        </div>
    </div>
    @endforelse
</div>

@if($tours->hasPages())
<div class="mt-4 d-flex justify-content-center ajax-pagination">
    {{ $tours->links('pagination::bootstrap-5') }}
</div>
@endif
