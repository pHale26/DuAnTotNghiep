@extends('layouts.master')

@section('title', __('Đặt vé thành công') . ' - Travel Wonder')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h1 class="fw-bold mb-3">{{ __('Đặt vé thành công!') }}</h1>
                <p class="text-muted fs-5">
                    @if($booking->booking_status === 'confirmed')
                        {{ __('Thanh toán đã được xác nhận. Vé điện tử đã được gửi đến email của bạn.') }}
                    @else
                        {{ __('Đơn đặt vé của bạn đã được ghi nhận. Vui lòng thanh toán để hoàn tất.') }}
                    @endif
                </p>
            </div>

            <!-- Booking Details -->
            <div class="glass-panel p-4 p-md-5 mb-4">
                <h4 class="fw-bold mb-4">
                    <i class="bi bi-ticket-perforated text-primary me-2"></i>
                    {{ __('Thông tin đặt vé') }}
                </h4>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">{{ __('Mã đặt vé') }}</small>
                            <strong class="fs-5 text-primary">#{{ $booking->id }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">{{ __('Trạng thái') }}</small>
                            @if($booking->booking_status === 'confirmed')
                                <span class="badge bg-success fs-6">{{ __('Đã xác nhận') }}</span>
                            @else
                                <span class="badge bg-warning fs-6">{{ __('Chờ thanh toán') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Ticket Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">{{ __('Thông tin vé') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Tên vé') }}</small>
                                <strong>{{ $booking->ticket_option->ticket->title }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Loại vé') }}</small>
                                <strong>{{ $booking->ticket_option->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Ngày sử dụng') }}</small>
                                <strong>{{ $booking->visit_date->format('d/m/Y') }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">{{ __('Số lượng') }}</small>
                                <strong>{{ $booking->quantity }} vé</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                @if($booking->qr_code_url && $booking->booking_status === 'confirmed')
                <div class="text-center mb-4">
                    <h6 class="fw-bold mb-3">{{ __('Mã QR vé điện tử') }}</h6>
                    <div class="p-3 bg-white rounded shadow-sm d-inline-block">
                        <img src="{{ asset($booking->qr_code_url) }}" alt="QR Code" style="width: 250px; height: 250px;">
                    </div>
                    <p class="text-muted small mt-3">{{ __('Vui lòng xuất trình mã QR này khi vào cổng') }}</p>
                </div>
                @endif

                <!-- Payment Summary -->
                <div class="bg-light rounded p-4">
                    <h6 class="fw-bold mb-3">{{ __('Thanh toán') }}</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Tạm tính') }}</span>
                        <span>{{ format_currency($booking->total_price + ($booking->discount_amount ?? 0)) }}</span>
                    </div>
                    @if($booking->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>{{ __('Giảm giá') }}</span>
                        <span>-{{ format_currency($booking->discount_amount) }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold">{{ __('Tổng cộng') }}</span>
                        <span class="fs-3 fw-bold text-primary">{{ format_currency($booking->total_price) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-3">
                @if($booking->booking_status !== 'confirmed')
                <a href="{{ route('frontend.tickets.checkout', [
                    'ticket_id' => $booking->ticket_option->ticket->id,
                    'ticket_option_id' => $booking->ticket_option_id,
                    'quantity' => $booking->quantity,
                    'visit_date' => $booking->visit_date->format('Y-m-d')
                ]) }}" class="btn btn-primary btn-lg rounded-pill fw-bold">
                    <i class="bi bi-credit-card me-2"></i>{{ __('Thanh toán ngay') }}
                </a>
                @else
                <a href="{{ asset($booking->qr_code_url) }}" download class="btn btn-success btn-lg rounded-pill fw-bold">
                    <i class="bi bi-download me-2"></i>{{ __('Tải mã QR') }}
                </a>
                @endif
                
                <a href="{{ route('user.profile', ['tab' => 'bookings']) }}" class="btn btn-outline-primary btn-lg rounded-pill fw-bold">
                    <i class="bi bi-list-ul me-2"></i>{{ __('Xem đơn đặt của tôi') }}
                </a>
                
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg rounded-pill">
                    <i class="bi bi-house me-2"></i>{{ __('Về trang chủ') }}
                </a>
            </div>

            <!-- Help Section -->
            <div class="text-center mt-5 p-4 bg-light rounded">
                <h6 class="fw-bold mb-3">{{ __('Cần hỗ trợ?') }}</h6>
                <p class="text-muted mb-3">{{ __('Liên hệ với chúng tôi nếu bạn cần trợ giúp') }}</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="tel:1900xxxx" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-telephone me-2"></i>1900-xxxx
                    </a>
                    <a href="mailto:support@travelwonder.com" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-envelope me-2"></i>Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
