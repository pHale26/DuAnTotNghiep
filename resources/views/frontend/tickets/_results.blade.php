<!-- Grid of Tickets -->
<style>
/* Ticket Card Styles - Refined and Beautiful */
.combo-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    border: 1px solid rgba(0,0,0,0.04);
}

.combo-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.12), 0 8px 10px rgba(0,0,0,0.08);
}

.combo-card-img-wrapper {
    position: relative;
    width: 100%;
    padding-top: 75%; /* 4:3 aspect ratio */
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

/* Grid adjustments */
.row.g-4 > * {
    padding-left: 12px;
    padding-right: 12px;
}

/* Responsive */
@media (max-width: 768px) {
    .combo-card-img-wrapper {
        padding-top: 66.67%; /* 3:2 aspect ratio on mobile */
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

<div class="row g-4">
    @forelse($tickets as $ticket)
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('frontend.tickets.show', $ticket->slug) }}" class="text-decoration-none h-100 d-block">
            <div class="combo-card h-100">
                <div class="combo-card-img-wrapper">
                    @php
                        $primaryImage = $ticket->ticket_images->where('is_primary', true)->first()
                                     ?? $ticket->ticket_images->first();
                        $fallbackImage = 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=800';
                        
                        $destName = mb_strtolower($ticket->destination->name ?? '', 'UTF-8');
                        if (str_contains($destName, 'nha trang') || str_contains($destName, 'phú quốc')) {
                            $fallbackImage = 'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=800';
                        } elseif (str_contains($destName, 'sapa') || str_contains($destName, 'đà nẵng') || str_contains($destName, 'hạ long')) {
                            $fallbackImage = 'https://images.unsplash.com/photo-1571509930722-df38ccfb8611?q=80&w=800';
                        }
                        
                        $ticketImage = $fallbackImage;
                        if ($primaryImage && !empty($primaryImage->image_url)) {
                            if (\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://'])) {
                                $ticketImage = $primaryImage->image_url;
                            } else {
                                $ticketImage = asset(ltrim($primaryImage->image_url, '/'));
                            }
                        }
                        
                        $minPrice = $ticket->ticket_options->min('price') ?? 0;
                        $minOriginalPrice = $ticket->ticket_options->min('original_price');
                        $hasDiscount = $minOriginalPrice && $minOriginalPrice > $minPrice;
                    @endphp



                    <img src="{{ $ticketImage }}" alt="{{ $ticket->title }}" 
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
                        <span class="btn btn-combo-detail">{{ __('Chọn vé') }}</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="mb-3 text-muted">
            <i class="bi bi-search" style="font-size: 3rem;"></i>
        </div>
        <h5 class="text-muted">{{ __('Không tìm thấy kết quả nào') }}</h5>
        <p class="text-muted">{{ __('Vui lòng thử điều chỉnh lại bộ lọc tìm kiếm.') }}</p>
        <a href="{{ route('frontend.tickets.search') }}" class="btn btn-outline-primary rounded-pill mt-2">
            <i class="bi bi-arrow-repeat me-1"></i>{{ __('Xóa bộ lọc') }}
        </a>
    </div>
    @endforelse
</div>

@if($tickets->hasPages())
<div class="mt-5 d-flex justify-content-center ajax-pagination">
    {{ $tickets->links('pagination::bootstrap-5') }}
</div>
@endif
