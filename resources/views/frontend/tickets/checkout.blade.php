@extends('layouts.master')

@section('title', __('Thanh toán vé') . ' - Travel Wonder')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Left: Booking Summary -->
        <div class="col-lg-8 mb-4">
            <div class="glass-panel p-4 p-md-5">
                <h2 class="fw-bold mb-4">
                    <i class="bi bi-ticket-perforated text-primary me-2"></i>
                    {{ __('Thông tin đặt vé') }}
                </h2>

                <!-- Ticket Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            @php
                                $primaryImage = $ticket->ticket_images->where('is_primary', true)->first();
                                $ticketImage = $primaryImage ? $primaryImage->image_url : 'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=600';
                            @endphp
                            <img src="{{ $ticketImage }}" alt="{{ $ticket->title }}" class="w-100 h-100 object-fit-cover rounded-start">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">{{ $ticket->title }}</h5>
                                <div class="mb-2">
                                    <i class="bi bi-geo-alt text-danger me-2"></i>
                                    <strong>{{ __('Điểm đến:') }}</strong> {{ $ticket->destination->name ?? '' }}
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-ticket-perforated text-success me-2"></i>
                                    <strong>{{ __('Loại vé:') }}</strong> {{ $ticketOption->name }}
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-calendar3 text-info me-2"></i>
                                    <strong>{{ __('Ngày sử dụng:') }}</strong> {{ \Carbon\Carbon::parse($visitDate)->format('d/m/Y') }}
                                </div>
                                <div class="mb-2">
                                    <i class="bi bi-people text-warning me-2"></i>
                                    <strong>{{ __('Số lượng:') }}</strong> {{ $quantity }} vé
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Info Form -->
                <form action="{{ route('frontend.tickets.book') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="ticket_option_id" value="{{ $ticketOption->id }}">
                    <input type="hidden" name="quantity" value="{{ $quantity }}">
                    <input type="hidden" name="visit_date" value="{{ $visitDate }}">

                    <h4 class="fw-bold mb-3">{{ __('Thông tin người đặt') }}</h4>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Họ và tên') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   name="customer_name" value="{{ old('customer_name', Auth::user()->name) }}" 
                                   required maxlength="255">
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Số điện thoại') }} <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                   name="customer_phone" value="{{ old('customer_phone', Auth::user()->phone) }}" 
                                   required pattern="[0-9]{10,11}" maxlength="20">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">10-11 chữ số</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                   name="customer_email" value="{{ old('customer_email', Auth::user()->email) }}" 
                                   required maxlength="255">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Coupon Code -->
                    <h4 class="fw-bold mb-3">{{ __('Mã giảm giá') }}</h4>
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" id="couponCode" name="coupon_code" 
                               placeholder="{{ __('Nhập mã giảm giá') }}">
                        <button type="button" class="btn btn-outline-primary" id="applyCouponBtn">
                            {{ __('Áp dụng') }}
                        </button>
                    </div>
                    <div id="couponMessage" class="mb-3"></div>

                    <!-- Payment Method -->
                    <h4 class="fw-bold mb-3">{{ __('Phương thức thanh toán') }}</h4>
                    @error('payment_method')
                        <div class="alert alert-danger mb-3">{{ $message }}</div>
                    @enderror
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-check border rounded p-3">
                                <input class="form-check-input @error('payment_method') is-invalid @enderror" 
                                       type="radio" name="payment_method" id="paymentVNPay" 
                                       value="vnpay" {{ old('payment_method', 'vnpay') == 'vnpay' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold w-100" for="paymentVNPay">
                                    <i class="bi bi-credit-card text-primary me-2"></i>
                                    {{ __('Thanh toán VNPay') }}
                                    <div class="text-muted small mt-1">{{ __('Thanh toán trực tuyến qua VNPay') }}</div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check border rounded p-3">
                                <input class="form-check-input @error('payment_method') is-invalid @enderror" 
                                       type="radio" name="payment_method" id="paymentTransfer" 
                                       value="transfer" {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold w-100" for="paymentTransfer">
                                    <i class="bi bi-bank text-success me-2"></i>
                                    {{ __('Chuyển khoản ngân hàng') }}
                                    <div class="text-muted small mt-1">{{ __('Chuyển khoản sau khi đặt vé') }}</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            {{ __('Tôi đồng ý với') }} 
                            <a href="#" class="text-primary">{{ __('Điều khoản và điều kiện') }}</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-3" id="submitBooking">
                        <i class="bi bi-check-circle me-2"></i>{{ __('Xác nhận đặt vé') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Price Summary -->
        <div class="col-lg-4">
            <div class="glass-panel p-4 sticky-top" style="top: 100px;">
                <h4 class="fw-bold mb-4">{{ __('Chi tiết thanh toán') }}</h4>
                
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Giá vé') }} (x{{ $quantity }})</span>
                        <span class="fw-bold">{{ format_currency($ticketOption->price) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Tạm tính') }}</span>
                        <span class="fw-bold" id="subtotalAmount">{{ format_currency($subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success" id="discountRow" style="display: none !important;">
                        <span>{{ __('Giảm giá') }}</span>
                        <span class="fw-bold" id="discountAmount">{{ format_currency(0) }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5 fw-bold">{{ __('Tổng cộng') }}</span>
                    <span class="fs-3 fw-bold text-primary" id="finalAmount">{{ format_currency($subtotal) }}</span>
                </div>

                <div class="alert alert-info border-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>{{ __('Bạn sẽ nhận được vé điện tử qua email sau khi thanh toán thành công.') }}</small>
                </div>

                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-3">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-x-circle-fill me-2 mt-1"></i>
                        <small><strong>{{ __('Chính sách hủy:') }}</strong> {{ $ticket->cancellation_policy ?? __('Vé không hỗ trợ hủy hoặc hoàn tiền sau khi đặt.') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const applyCouponBtn = document.getElementById('applyCouponBtn');
    const couponCodeInput = document.getElementById('couponCode');
    const couponMessage = document.getElementById('couponMessage');
    const discountRow = document.getElementById('discountRow');
    const discountAmount = document.getElementById('discountAmount');
    const finalAmount = document.getElementById('finalAmount');
    const bookingForm = document.getElementById('bookingForm');
    const subtotal = {{ $subtotal }};

    // Form validation
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessages = [];

            // Validate customer name
            const nameInput = bookingForm.querySelector('[name="customer_name"]');
            if (!nameInput.value.trim()) {
                isValid = false;
                errorMessages.push('Vui lòng nhập họ tên');
                nameInput.classList.add('is-invalid');
            } else {
                nameInput.classList.remove('is-invalid');
            }

            // Validate phone
            const phoneInput = bookingForm.querySelector('[name="customer_phone"]');
            const phonePattern = /^[0-9]{10,11}$/;
            if (!phoneInput.value.trim()) {
                isValid = false;
                errorMessages.push('Vui lòng nhập số điện thoại');
                phoneInput.classList.add('is-invalid');
            } else if (!phonePattern.test(phoneInput.value.trim())) {
                isValid = false;
                errorMessages.push('Số điện thoại phải có 10-11 chữ số');
                phoneInput.classList.add('is-invalid');
            } else {
                phoneInput.classList.remove('is-invalid');
            }

            // Validate email
            const emailInput = bookingForm.querySelector('[name="customer_email"]');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim()) {
                isValid = false;
                errorMessages.push('Vui lòng nhập email');
                emailInput.classList.add('is-invalid');
            } else if (!emailPattern.test(emailInput.value.trim())) {
                isValid = false;
                errorMessages.push('Email không hợp lệ');
                emailInput.classList.add('is-invalid');
            } else {
                emailInput.classList.remove('is-invalid');
            }

            // Validate payment method
            const paymentMethod = bookingForm.querySelector('[name="payment_method"]:checked');
            if (!paymentMethod) {
                isValid = false;
                errorMessages.push('Vui lòng chọn phương thức thanh toán');
            }

            // Validate terms
            const termsCheckbox = document.getElementById('agreeTerms');
            if (!termsCheckbox.checked) {
                isValid = false;
                errorMessages.push('Vui lòng đồng ý với điều khoản và điều kiện');
            }

            if (!isValid) {
                e.preventDefault();
                alert(errorMessages.join('\n'));
                return false;
            }
        });
    }

    // Coupon application
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const code = couponCodeInput.value.trim();
            
            if (!code) {
                showMessage('error', '{{ __("Vui lòng nhập mã giảm giá") }}');
                return;
            }

            applyCouponBtn.disabled = true;
            applyCouponBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("Đang xử lý...") }}';

            fetch('{{ route("frontend.tickets.apply_coupon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    coupon_code: code,
                    total_price: subtotal
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    discountRow.style.display = 'flex';
                    discountAmount.textContent = data.discount_formatted;
                    finalAmount.textContent = data.final_price_formatted;
                    couponCodeInput.disabled = true;
                } else {
                    showMessage('error', data.message);
                }
            })
            .catch(error => {
                showMessage('error', '{{ __("Có lỗi xảy ra. Vui lòng thử lại.") }}');
            })
            .finally(() => {
                applyCouponBtn.disabled = false;
                applyCouponBtn.innerHTML = '{{ __("Áp dụng") }}';
            });
        });
    }

    function showMessage(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        couponMessage.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        
        setTimeout(() => {
            couponMessage.innerHTML = '';
        }, 5000);
    }

    // Real-time validation
    const inputs = bookingForm.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush
@endsection
