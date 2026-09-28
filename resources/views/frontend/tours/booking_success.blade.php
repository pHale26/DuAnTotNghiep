@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 reveal-up">
            <div class="card border-0 shadow-sm rounded-4" id="bookingSuccessCard">
                <div class="card-body p-5 text-center">
                    
                    <div id="statusHeader">
                        @if($booking->payment_status === 'paid_30')
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-3 text-success">Đã Thanh Toán Đặt Cọc (30%) Thành Công!</h2>
                            <p class="text-muted mb-4">Cảm ơn bạn! Đơn hàng <strong>{{ $booking->code }}</strong> đã được bảo lưu giữ chỗ thành công với số tiền cọc <strong>{{ format_currency($booking->paid_amount) }}</strong>. Số tiền còn lại (<strong>{{ format_currency($booking->total_price - $booking->paid_amount) }}</strong>) vui lòng hoàn tất trước ngày khởi hành.</p>
                        @elseif(in_array($booking->payment_status, ['paid', 'paid_100']))
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-3 text-success">Đã Thanh Toán Thành Công!</h2>
                            <p class="text-muted mb-4">Cảm ơn bạn! Đơn hàng <strong>{{ $booking->code }}</strong> của bạn đã được xác nhận thanh toán đầy đủ 100%.</p>
                        @elseif($booking->booking_status === 'cancelled')
                            <div class="mb-4">
                                <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-3 text-danger">Đơn Hàng Đã Bị Hủy!</h2>
                            <p class="text-muted mb-4">{{ $booking->cancel_reason ?? 'Đơn hàng đã tự động hủy do quá hạn thanh toán.' }}</p>
                        @else
                            <div class="mb-4">
                                <i class="bi bi-clock-history text-warning" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-3">Đặt Tour Thành Công!</h2>
                            <p class="text-muted mb-4">Vui lòng hoàn tất thanh toán trong thời gian giữ chỗ để bảo lưu vé của bạn.</p>

                            <!-- Timer Countdown Banner -->
                            <div class="alert alert-info d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill mb-4 border-0 shadow-sm" id="countdownBanner">
                                <i class="bi bi-hourglass-split text-info fs-5"></i>
                                <span>Thời gian giữ chỗ còn lại: <strong class="fs-5 text-danger font-monospace" id="countdownTimer">15:00</strong></span>
                            </div>
                        @endif
                    </div>

                    @if($booking->payment_method === 'transfer' && !in_array($booking->payment_status, ['paid', 'paid_30', 'paid_100']) && $booking->booking_status !== 'cancelled')
                        @php
                            $amount = $booking->payment_type === 'deposit' ? ($booking->total_price * 0.3) : $booking->total_price;
                            $bankId = 'BIDV';
                            $accountNo = '0818802032';
                            $template = 'compact2';
                            $accountName = 'TRavelWondel';
                            $description = "TW{$booking->id}";
                            
                            $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount=".round($amount)."&addInfo=".urlencode($description)."&accountName=".urlencode($accountName);
                        @endphp
                        
                        <div id="transferQrSection" class="bg-light p-4 rounded-4 mb-4 text-center mx-auto" style="max-width: 450px;">
                            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-qr-code-scan me-2"></i>Quét mã QR để thanh toán tự động</h5>
                            <img src="{{ $qrUrl }}" alt="VietQR" class="img-fluid rounded border bg-white p-2 mb-3 shadow-sm" style="max-width: 250px;">
                            
                            <div class="text-start bg-white p-3 rounded border shadow-sm">
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Ngân hàng:</span>
                                    <strong class="text-dark">{{ $bankId }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Số tài khoản:</span>
                                    <strong class="text-dark fs-6">{{ $accountNo }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Chủ tài khoản:</span>
                                    <strong class="text-dark">{{ $accountName }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                    <span class="text-muted">Số tiền:</span>
                                    <strong class="text-danger fs-5">{{ format_currency($amount) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between pt-1">
                                    <span class="text-muted">Nội dung chuyển khoản:</span>
                                    <strong class="text-primary fs-6 font-monospace bg-light px-2 py-1 rounded border">{{ $description }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning text-start">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Lưu ý:</strong> Vui lòng giữ nguyên nội dung <code>{{ $description }}</code> để hệ thống tự động xác nhận ngay sau khi nhận được tiền.
                        </div>
                    @endif

                    @if(is_null($booking->user_id) && in_array($booking->payment_status, ['paid', 'paid_30', 'paid_100']))
                        <div class="bg-primary bg-opacity-10 p-4 rounded-4 my-4 text-start border border-primary border-opacity-25">
                            <h5 class="fw-bold text-primary mb-2"><i class="bi bi-person-plus-fill me-2"></i>Tạo tài khoản để theo dõi đơn hàng dễ dàng!</h5>
                            <p class="text-muted mb-3">Bạn vừa đặt tour với tư cách Khách. Tạo tài khoản ngay bây giờ với email <strong>{{ $booking->customer_email }}</strong> để lưu ưu đãi và dễ dàng xem lịch trình.</p>
                            <form action="{{ route('frontend.tours.create_account', $booking->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="email" value="{{ $booking->customer_email }}">
                                <input type="hidden" name="name" value="{{ $booking->customer_name }}">
                                <input type="hidden" name="phone" value="{{ $booking->customer_phone }}">
                                <div class="input-group mb-3" style="max-width: 400px;">
                                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu tự chọn" required minlength="6">
                                    <button type="submit" class="btn btn-primary px-4">
                                        Đăng ký nhanh
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="mt-4 d-flex gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                            <i class="bi bi-house me-2"></i> Về trang chủ
                        </a>
                        @if($booking->user_id || Auth::check())
                            <a href="{{ route('user.profile', ['tab' => 'bookings']) }}" class="btn btn-primary px-4 py-2 rounded-pill">
                                Xem lịch sử đặt tour <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FLOATING ADMIN DEMO TOOLBAR -->
@if(Auth::check() || config('app.debug'))
<div id="adminDemoBar" class="position-fixed bottom-0 start-50 translate-middle-x mb-3 z-3 bg-dark text-white p-3 rounded-4 shadow-lg border border-secondary d-flex align-items-center gap-3" style="max-width: 90vw;">
    <div class="d-flex align-items-center me-2 text-warning fw-bold small">
        <i class="bi bi-sliders me-1 fs-5"></i> DEMO TOOLBAR
    </div>
    <button type="button" id="btnSimulatePay" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
        <i class="bi bi-lightning-charge-fill"></i>
        <span>⚡ Demo: Giả lập Tiền về (Pay Now)</span>
    </button>
    <button type="button" id="btnFastForwardCancel" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
        <i class="bi bi-fast-forward-fill"></i>
        <span>⏩ Demo: Tua nhanh (Hết hạn & Tự Hủy)</span>
    </button>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bookingId = {{ $booking->id }};
    const csrfToken = "{{ csrf_token() }}";
    
    @php
        $remainingSeconds = max(0, $booking->created_at->addMinutes(15)->timestamp - now()->timestamp);
    @endphp
    
    let remainingSeconds = {{ $remainingSeconds }};

    function updateTimer() {
        const timerEl = document.getElementById('countdownTimer');
        if (!timerEl) return;

        if (remainingSeconds <= 0) {
            timerEl.innerText = "00:00";
            timerEl.classList.add('text-muted');
            if (!window.hasShownExpireAlert) {
                window.hasShownExpireAlert = true;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hết thời gian thanh toán!',
                        text: 'Giao dịch của bạn đã quá hạn 15 phút và sẽ bị hệ thống tự động hủy. Vui lòng đặt lại tour mới.',
                        icon: 'error',
                        confirmButtonText: 'Đóng',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = "{{ route('user.bookings') }}";
                    });
                } else {
                    alert('Hết thời gian thanh toán! Giao dịch của bạn đã quá hạn và sẽ tự động bị hủy.');
                    window.location.href = "{{ route('user.bookings') }}";
                }
            }
            return;
        } else {
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            timerEl.innerText = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            remainingSeconds--;
        }
    }

    setInterval(updateTimer, 1000);
    updateTimer();

    // Realtime Status Polling (Every 3 seconds)
    const initialPaymentStatus = "{{ $booking->payment_status }}";
    const initialBookingStatus = "{{ $booking->booking_status }}";

    function checkStatus() {
        if (initialPaymentStatus !== 'pending' && initialBookingStatus !== 'pending') {
            return;
        }

        fetch(`/tours/booking-status/${bookingId}`)
            .then(res => res.json())
            .then(data => {
                if (data.payment_status !== initialPaymentStatus || data.booking_status !== initialBookingStatus) {
                    location.reload();
                }
            })
            .catch(err => console.log('Polling status error', err));
    }

    if (initialPaymentStatus === 'pending' || initialBookingStatus === 'pending') {
        setInterval(checkStatus, 3000);
    }

    // Demo Toolbar Event Listeners
    const btnPay = document.getElementById('btnSimulatePay');
    const btnCancel = document.getElementById('btnFastForwardCancel');

    if (btnPay) {
        btnPay.addEventListener('click', function () {
            btnPay.disabled = true;
            btnPay.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...';
            
            fetch(`/demo/bookings/${bookingId}/simulate-payment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                location.reload();
            })
            .catch(err => {
                alert('Lỗi giả lập thanh toán: ' + err);
                btnPay.disabled = false;
            });
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', function () {
            btnCancel.disabled = true;
            btnCancel.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang tua nhanh...';

            fetch(`/demo/bookings/${bookingId}/fast-forward-cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                location.reload();
            })
            .catch(err => {
                alert('Lỗi tua nhanh tự hủy: ' + err);
                btnCancel.disabled = false;
            });
        });
    }
});
</script>
@endsection

