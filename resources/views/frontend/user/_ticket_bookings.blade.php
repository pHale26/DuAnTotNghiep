<!-- Ticket Bookings Tab Content -->
<div class="tab-pane fade" id="ticket-bookings" role="tabpanel">
    <div class="row">
        @forelse($ticketBookings ?? [] as $ticketBooking)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm hover-lift" style="transition: all 0.3s;">
                @php
                    $ticket = $ticketBooking->ticket_option->ticket ?? null;
                    $primaryImage = $ticket?->ticket_images->where('is_primary', true)->first();
                    $ticketImage = $primaryImage ? $primaryImage->image_url : 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=600';
                @endphp
                
                <!-- Ticket Image -->
                <div class="position-relative" style="height: 200px; overflow: hidden;">
                    <img src="{{ $ticketImage }}" alt="{{ $ticket?->title }}" 
                         class="w-100 h-100 object-fit-cover">
                    <div class="position-absolute top-0 start-0 end-0 bottom-0" 
                         style="background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.6));"></div>
                    
                    <!-- Status Badge -->
                    <div class="position-absolute top-0 end-0 m-3">
                        @if($ticketBooking->booking_status === 'confirmed')
                            <span class="badge bg-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Đã xác nhận') }}
                            </span>
                        @elseif($ticketBooking->booking_status === 'pending')
                            <span class="badge bg-warning px-3 py-2">
                                <i class="bi bi-clock me-1"></i>{{ __('Chờ thanh toán') }}
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">
                                {{ ucfirst($ticketBooking->booking_status) }}
                            </span>
                        @endif
                    </div>

                    <!-- Booking ID -->
                    <div class="position-absolute bottom-0 start-0 m-3 text-white">
                        <small class="fw-bold">#{{ $ticketBooking->id }}</small>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <h5 class="fw-bold mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $ticket?->title ?? __('Vé tham quan') }}
                    </h5>

                    <div class="mb-2">
                        <i class="bi bi-geo-alt text-danger me-1"></i>
                        <small class="text-muted">{{ $ticket?->destination->name ?? '' }}</small>
                    </div>

                    <div class="mb-2">
                        <i class="bi bi-ticket-perforated text-success me-1"></i>
                        <small class="text-muted">{{ $ticketBooking->ticket_option->name }}</small>
                    </div>

                    <div class="mb-2">
                        <i class="bi bi-calendar3 text-info me-1"></i>
                        <small class="text-muted">{{ $ticketBooking->visit_date->format('d/m/Y') }}</small>
                    </div>

                    <div class="mb-3">
                        <i class="bi bi-people text-warning me-1"></i>
                        <small class="text-muted">{{ $ticketBooking->quantity }} vé</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div>
                            <small class="text-muted d-block">{{ __('Tổng tiền') }}</small>
                            <strong class="text-primary fs-5">{{ format_currency($ticketBooking->total_price) }}</strong>
                        </div>

                        @if($ticketBooking->booking_status === 'confirmed')
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="alert('Tính năng đang được nâng cấp')">
                                <i class="bi bi-qr-code me-1"></i>{{ __('Xem vé') }}
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" onclick="alert('Không thể thanh toán vé lẻ lúc này')">
                                <i class="bi bi-info-circle me-1"></i>{{ __('Chờ xử lý') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-ticket-perforated text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">{{ __('Chưa có đơn đặt vé nào') }}</h5>
                <p class="text-muted">{{ __('Khám phá và đặt vé tham quan ngay!') }}</p>
                {{-- <a href="{{ route('frontend.tickets.index') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                    <i class="bi bi-search me-2"></i>{{ __('Tìm vé tham quan') }}
                </a> --}}
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
.hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
}
</style>
