@extends('layouts.master')

@section('content')
@php
$familyName = '';
$givenName = '';
if(isset($passenger) && $passenger->full_name) {
    $nameParts = explode(' ', trim($passenger->full_name));
    $familyName = array_shift($nameParts);
    $givenName = implode(' ', $nameParts);
}
@endphp

<div class="container py-5 reveal-up">
    <div class="premium-card mx-auto border-0" style="max-width: 800px;">
        <div class="card-body p-4 p-md-5">
            <div class="mb-5 border-bottom pb-4 text-center">
                <i class="bi bi-airplane-engines text-primary fs-1 mb-3 d-inline-block"></i>
                <h2 class="section-heading mb-2 fs-3">Xác Nhận Hành Khách</h2>
                <p class="text-muted fw-500 mb-0">Vui lòng kiểm tra kỹ thông tin xuất vé máy bay</p>
            </div>

            <form action="{{ route('frontend.flights.book') }}" method="POST">
                @csrf
                <input type="hidden" name="offer_id" value="{{ $offerId }}">
                <input type="hidden" name="passenger_id" value="{{ $passengerId }}">
                <input type="hidden" name="tour_booking_id" value="{{ $tourBookingId }}">
                <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                <input type="hidden" name="total_currency" value="{{ $totalCurrency }}">

                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-600 text-dark">Danh Xưng</label>
                        <select name="title" class="form-select search-form-control" required>
                            <option value="mr" {{ ($passenger->gender ?? '') == 'male' ? 'selected' : '' }}>Ông</option>
                            <option value="ms" {{ ($passenger->gender ?? '') == 'female' ? 'selected' : '' }}>Bà/Cô</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 text-dark">Họ (Không dấu)</label>
                        <input type="text" name="family_name" class="form-control search-form-control text-uppercase"
                            value="{{ \Illuminate\Support\Str::ascii($familyName) }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-600 text-dark">Tên đệm & Tên (Không dấu)</label>
                        <input type="text" name="given_name" class="form-control search-form-control text-uppercase"
                            value="{{ \Illuminate\Support\Str::ascii($givenName) }}" required>
                    </div>

                    <div class="col-md-4 mt-4">
                        <label class="form-label fw-600 text-dark">Ngày Sinh</label>
                        <!-- Thêm \Carbon\Carbon::parse() để đảm bảo xuất ra đúng chuẩn YYYY-MM-DD cho thẻ input date -->
                        <input type="date" name="born_on" class="form-control search-form-control"
                            value="{{ isset($passenger->date_of_birth) ? \Carbon\Carbon::parse($passenger->date_of_birth)->format('Y-m-d') : '' }}"
                            required max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 mt-4">
                        <label class="form-label fw-600 text-dark">Giới Tính</label>
                        <select name="gender" class="form-select search-form-control" required>
                            <option value="m" {{ ($passenger->gender ?? '') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="f" {{ ($passenger->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                        </select>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label class="form-label fw-600 text-dark">Số Điện Thoại</label>
                        <input type="text" name="phone_number" class="form-control search-form-control"
                            value="+84{{ ltrim($user->phone ?? '', '0') }}" required placeholder="+84...">
                    </div>

                    <div class="col-md-12 mt-4">
                        <label class="form-label fw-600 text-dark">Email Nhận Vé Điện Tử</label>
                        <input type="email" name="email" class="form-control search-form-control" value="{{ $user->email ?? '' }}" required>
                    </div>
                </div>

                <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-4 p-4 mt-4 d-flex align-items-start">
                    <i class="bi bi-shield-check text-primary fs-4 me-3 mt-n1"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Dữ liệu đã được đồng bộ</h6>
                        <p class="mb-0 small text-muted">Dữ liệu hành khách đã được tự động đồng bộ và loại bỏ dấu tiếng Việt từ hồ sơ đặt tour trước đó để phù hợp với chuẩn vé máy bay quốc tế.</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                    <div>
                        <div class="text-muted small fw-500 mb-1">Tổng cộng phí xuất vé:</div>
                        <div class="text-danger fw-bold fs-3">{{ number_format($totalAmount, 0, ',', '.') }}<span class="fs-5 ms-1">{{ $totalCurrency }}</span></div>
                    </div>
                    <button type="submit" class="btn btn-register-premium px-5 py-3 fs-5">
                        <i class="bi bi-ticket-detailed me-2"></i> Xuất Vé Máy Bay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection