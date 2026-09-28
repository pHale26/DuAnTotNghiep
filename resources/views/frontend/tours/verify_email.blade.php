@extends("layouts.master")
@section("title", "Xác nhận Email")
@section("content")
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <h4 class="mb-3 text-primary"><i class="bi bi-envelope-check"></i> Xác Thực Email</h4>
                    <p class="text-muted">Chúng tôi đã gửi một mã OTP gồm 6 chữ số đến email <strong>{{ $booking->customer_email }}</strong> của bạn. Vui lòng kiểm tra hộp thư (và thư mục Spam) để lấy mã.</p>
                    
                    @if(session("error"))
                        <div class="alert alert-danger">{{ session("error") }}</div>
                    @endif
                    
                    <form action="{{ route("frontend.tours.verify_email", $booking->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="otp" class="form-control form-control-lg text-center" placeholder="Nhập mã OTP (6 số)" required maxlength="6" style="letter-spacing: 5px; font-size: 24px; font-weight: bold;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Xác Nhận & Tiếp Tục Thanh Toán</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
