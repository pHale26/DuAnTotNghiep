@if(request()->hasAny(['destination_id', 'departure_date', 'date', 'budget', 'keyword', 'duration', 'category_id']))
    <div class="d-flex align-items-center flex-wrap gap-2 mt-4 pt-3 border-top mb-4 px-3">
        <small class="text-muted me-2">
            <i class="bi bi-funnel me-1"></i>
            {{ __('Tìm thấy') }} <strong class="text-primary">{{ $tours->total() }}</strong>
            {{ __('tour phù hợp') }}
        </small>

        @if(request('destination_id') && !isset($filterErrors['destination_id']))
            @php $dest = $allDestinations->firstWhere('id', request('destination_id')); @endphp
            @if($dest)
                <a href="#" data-remove-filter="destination_id"
                    class="badge bg-primary bg-opacity-10 text-primary text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-geo-alt me-1"></i>{{ $dest->name }} <i class="bi bi-x"></i>
                </a>
            @endif
        @endif

        @if((request('departure_date') || request('date')) && !isset($filterErrors['departure_date']))
            @php
                $selectedDate = request('departure_date') ?? request('date');
            @endphp
            <a href="#" data-remove-filter="departure_date"
                class="badge bg-info bg-opacity-10 text-info text-decoration-none p-2 rounded-pill hover-opacity">
                <i class="bi bi-calendar me-1"></i>Từ {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }} <i
                    class="bi bi-x"></i>
            </a>
        @endif

        @if(request('budget') && request('budget') !== 'all' && !isset($filterErrors['budget']))
            @php
                $budgetLabels = [
                    'under_1m' => 'Dưới 1 triệu',
                    '1m_2m' => '1 - 2 triệu',
                    '2m_4m' => '2 - 4 triệu',
                    'over_4m' => 'Trên 4 triệu',
                ];
            @endphp
            <a href="#" data-remove-filter="budget"
                class="badge bg-success bg-opacity-10 text-success text-decoration-none p-2 rounded-pill hover-opacity">
                <i class="bi bi-currency-dollar me-1"></i>{{ $budgetLabels[request('budget')] ?? '' }} <i
                    class="bi bi-x"></i>
            </a>
        @endif

        @if(request('duration'))
            @php
                $durationLabels = [
                    '2d1n' => '2N1Đ',
                    '3d2n' => '3N2Đ',
                    '4d3n' => '4N3Đ',
                    '5d4n' => '5N4Đ',
                    '6d5n' => '6N5Đ',
                    '7d6n' => '7N6Đ',
                ];
            @endphp
            <a href="#" data-remove-filter="duration"
                class="badge bg-warning bg-opacity-10 text-warning text-decoration-none p-2 rounded-pill hover-opacity">
                <i class="bi bi-clock me-1"></i>{{ $durationLabels[request('duration')] ?? '' }} <i
                    class="bi bi-x"></i>
            </a>
        @endif

        @if(request('keyword'))
            <a href="#" data-remove-filter="keyword"
                class="badge bg-secondary bg-opacity-10 text-secondary text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-search me-1"></i>{{ request('keyword') }} <i class="bi bi-x"></i>
            </a>
        @endif

        @if(request('category_id') && request('category_id') !== 'all')
            @php $cat = $categories->firstWhere('id', request('category_id')); @endphp
            @if($cat)
                <a href="#" data-remove-filter="category_id"
                    class="badge bg-dark bg-opacity-10 text-dark text-decoration-none p-2 rounded-pill hover-opacity">
                    <i class="bi bi-tag me-1"></i>{{ $cat->name }} <i class="bi bi-x"></i>
                </a>
            @endif
        @endif

        <a href="#" id="clear-all-filters" class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
            {{ __('Xóa bộ lọc') }}
        </a>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 px-3">
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" data-category-btn="all"
            class="filter-chip btn border-0 {{ !request('category_id') || request('category_id') === 'all' ? 'active' : '' }}">
            {{ __('Tất cả') }}
        </button>
        @foreach($categories as $cat)
            <button type="button" data-category-btn="{{ $cat->id }}"
                class="filter-chip btn border-0 {{ request('category_id') == $cat->id ? 'active' : '' }}">
                {{ $cat->name }}
            </button>
        @endforeach
    </div>
</div>

<div class="row g-4 px-3 position-relative">
    <div id="loading-overlay" class="position-absolute w-100 h-100 d-none d-flex justify-content-center align-items-center" style="background: rgba(255,255,255,0.7); z-index: 10; top: 0; left: 0; min-height: 200px; border-radius: 16px;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    @forelse($tours as $tour)
        <div class="col-12 col-md-6 col-lg-3">
            <div class="tour-preview-wrapper h-100" x-data="{ showPreview: false }"
                @mouseenter="showPreview = true" @mouseleave="showPreview = false">

                <a href="{{ route('frontend.tours.show', $tour->slug) }}"
                    class="text-decoration-none h-100 d-block" @mouseenter.stop>
                    <div class="combo-card h-100">
                        <div class="combo-card-img-wrapper">

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
                            <img src="{{ $tourImage }}" alt="{{ $tour->title }}"
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
                                    <div class="combo-price-val">{{ format_currency($tour->getBasePrice() > 0 ? $tour->getBasePrice() : ($tour->base_price ?? 0)) }}
                                    </div>
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
        <div class="col-12 text-center py-5">
            <div class="mb-3 text-muted">
                <i class="bi bi-search" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-muted">{{ __('Không tìm thấy tour phù hợp với bộ lọc của bạn') }}</h5>
            <p class="text-muted">{{ __('Gợi ý: Thử bỏ bớt điều kiện lọc để xem thêm tour.') }}</p>
            <a href="#" id="view-all-tours-btn" class="btn btn-primary rounded-pill mt-2">
                <i class="bi bi-arrow-repeat me-1"></i>{{ __('Xem tất cả tour') }}
            </a>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-5 ajax-pagination">
    {{ $tours->links('pagination::bootstrap-5') }}
</div>
