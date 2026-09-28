@extends('layouts.master')

@section('content')
<style>
    .form-section-title {
        color: var(--dark-blue);
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-section-title i {
        background: rgba(0, 124, 232, 0.1);
        color: var(--primary-color);
        padding: 8px 12px;
        border-radius: 12px;
        font-size: 1.25rem;
    }

    .transport-option {
        border-radius: 20px;
        border: 2px solid #e2e8f0;
        transition: var(--transition-fast);
        background: white;
    }

    .btn-check:checked+.transport-option {
        border-color: var(--primary-color);
        background: rgba(0, 124, 232, 0.03);
        box-shadow: 0 4px 15px rgba(0, 124, 232, 0.15);
    }

    .btn-check:checked+.transport-option i {
        color: var(--primary-color) !important;
    }

    /* Wizard Styles */
    .wizard-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }

    .wizard-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 10%;
        width: 80%;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }

    .wizard-step {
        position: relative;
        z-index: 2;
        text-align: center;
        background: white;
        padding: 0 10px;
        flex: 1;
    }

    .wizard-step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin: 0 auto 8px auto;
        transition: all 0.3s;
        border: 4px solid white;
    }

    .wizard-step.active .wizard-step-circle {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 0 0 4px rgba(0, 124, 232, 0.2);
    }

    .wizard-step.completed .wizard-step-circle {
        background: #10b981;
        color: white;
    }

    .wizard-step-title {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .wizard-step.active .wizard-step-title {
        color: var(--primary-color);
        font-weight: 600;
    }

    .wizard-panel {
        display: none;
    }

    .wizard-panel.active {
        display: block;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .coupon-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: var(--primary-color) !important;
    }

    .qty-input {
        -moz-appearance: textfield;
    }

    .qty-input:focus {
        box-shadow: none !important;
        border-color: #6c757d !important;
        z-index: 1 !important;
    }

    .btn-qty-minus,
    .btn-qty-plus,
    .btn-ticket-qty-minus,
    .btn-ticket-qty-plus,
    .btn-addon-qty-minus,
    .btn-addon-qty-plus {
        background-color: #f8f9fa;
        border-color: #ced4da;
    }

    .btn-qty-minus:hover,
    .btn-qty-plus:hover,
    .btn-ticket-qty-minus:hover,
    .btn-ticket-qty-plus:hover,
    .btn-addon-qty-minus:hover,
    .btn-addon-qty-plus:hover {
        background-color: #e9ecef;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<div class="container py-5">
    <div class="row g-5">
        <!-- Main Booking Form -->
        <div class="col-lg-8 reveal-up">
            <div class="premium-card p-4 p-md-5 border-0">
                <!-- Header -->
                <div class="mb-5 border-bottom pb-4">
                    <h2 class="section-heading mb-2">{{ __('Hoàn Tất Đặt Tour') }}</h2>
                    <p class="text-muted fw-500 mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-check text-primary me-2"></i> {{ __('Hành trình:') }}
                        {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }} -
                        {{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }}
                    </p>
                    @if($holidaySurcharge > 0)
                    <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                        <div>
                            <strong>Lưu ý phụ thu dịp Lễ:</strong> Tour khởi hành vào dịp Lễ áp dụng phụ thu
                            {{ $holidaySurcharge }}%. Giá trên đã bao gồm phụ thu.
                        </div>
                    </div>
                    @endif
                    <div class="alert alert-info mt-3 mb-0 d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bi bi-stopwatch fs-4 me-2 text-info"></i>
                            <strong>Thời gian giữ chỗ:</strong> Vui lòng hoàn tất biểu mẫu trong <span
                                id="seatHoldTimer" class="fw-bold text-danger fs-5 ms-1">05:00</span>
                        </div>
                    </div>
                </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <strong>Vui lòng kiểm tra lại thông tin:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('frontend.tours.store') }}" method="POST" id="checkout-form"
                        enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="adults" value="{{ $adults }}">
                    <input type="hidden" name="children" value="{{ $children }}">
                    <input type="hidden" name="total_price" id="input_total_price" value="{{ $totalPrice }}">
                    <input type="hidden" name="transport_price" id="input_transport_price" value="0">
                    <input type="hidden" name="transport_data" id="input_transport_data" value="">

                    <!-- Wizard Progress -->
                    <div class="wizard-steps">
                        <div class="wizard-step active" id="step-nav-1">
                            <div class="wizard-step-circle">1</div>
                            <div class="wizard-step-title">Hành Khách</div>
                        </div>
                        <div class="wizard-step" id="step-nav-2">
                            <div class="wizard-step-circle">2</div>
                            <div class="wizard-step-title">Dịch Vụ</div>
                        </div>
                        <div class="wizard-step" id="step-nav-3">
                            <div class="wizard-step-circle">3</div>
                            <div class="wizard-step-title">Thanh Toán</div>
                        </div>
                    </div>

                    <!-- WIZARD STEP 1 -->
                    <div class="wizard-panel active" id="step-panel-1">
                        <!-- Section 1: Thông Tin Người Đặt -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-person-badge"></i>
                                {{ __('Thông Tin Người Đặt') }}
                            </h4>

                            @if(!Auth::check())
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <div>
                                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                    Bạn đang đặt tour với tư cách Khách (Đặt nhanh không cần tài khoản).
                                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                                        class="fw-bold alert-link ms-2">Đăng nhập</a> hoặc đăng ký để dễ dàng quản lý
                                    đơn hàng.
                                </div>
                            </div>
                            @endif

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-600 text-dark">{{ __('Họ và Tên') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" id="customer_name"
                                        class="form-control search-form-control"
                                        value="{{ $identity->full_name ?? $user?->name }}" required
                                        placeholder="{{ __('Nhập tên đầy đủ (khớp với CCCD/Hộ chiếu)') }}"
                                        oninput="document.getElementById('hidden_adult_name').value = this.value">
                                    <input type="hidden" name="passengers[adult][0][full_name]" id="hidden_adult_name"
                                        value="{{ $identity->full_name ?? $user?->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 text-dark">{{ __('Số Điện Thoại') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" name="customer_phone" class="form-control search-form-control"
                                        value="{{ $user?->phone ?? '' }}" required placeholder="+84 (0)...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600 text-dark">{{ __('Email') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" id="customer_email" class="form-control search-form-control @error('customer_email') is-invalid @enderror"
                                        value="{{ old('customer_email', $user?->email) }}" required placeholder="email@example.com">
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="email_suggestion_banner" class="alert alert-warning mt-2 mb-0 p-2 d-none" style="font-size: 0.85rem; border-left: 4px solid #ffc107;">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Có phải ý bạn là <strong id="email_suggestion_text"></strong>? <a href="#" id="email_suggestion_btn" class="fw-bold text-dark">Sửa lại</a>
                                    </div>
                                    <div id="email_exists_banner" class="alert alert-info mt-2 mb-0 p-2 d-none" style="font-size: 0.85rem; border-left: 4px solid #0dcaf0;">
                                        <i class="bi bi-info-circle me-1"></i> Email này đã có tài khoản. Đăng nhập để tự động điền thông tin và theo dõi đơn hàng dễ hơn?
                                        <div class="mt-2 d-flex gap-2">
                                            <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="btn btn-sm btn-primary">Đăng nhập</a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('email_exists_banner').classList.add('d-none')">Tiếp tục không cần đăng nhập</button>
                                        </div>
                                    </div>
                                </div>
                                @if(!$user)
                                <div class="col-md-6">
                                    <label class="form-label fw-600 text-dark">{{ __('Nhập lại Email') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="customer_email_confirmation" id="customer_email_confirmation" class="form-control search-form-control @error('customer_email_confirmation') is-invalid @enderror"
                                        value="{{ old('customer_email_confirmation') }}" required placeholder="Nhập lại email để xác nhận" oninput="checkEmailMatch()">
                                    @error('customer_email_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="email_match_error" class="invalid-feedback" style="display:none;">Email nhập lại không khớp!</div>
                                </div>
                                @else
                                    <input type="hidden" name="customer_email_confirmation" id="customer_email_confirmation" value="{{ $user->email }}">
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            const emailInput = document.getElementById("customer_email");
                                            const confirmInput = document.getElementById("customer_email_confirmation");
                                            if (emailInput && confirmInput) {
                                                emailInput.addEventListener("input", function() {
                                                    confirmInput.value = this.value;
                                                });
                                                // Đảm bảo đồng bộ ngay khi load trang phòng trường hợp trình duyệt autofill
                                                setTimeout(() => {
                                                    if (emailInput.value && emailInput.value !== confirmInput.value) {
                                                        confirmInput.value = emailInput.value;
                                                    }
                                                }, 100);
                                            }
                                        });
                                    </script>
                                @endif
                                <input type="hidden" name="meeting_point" id="meeting_point"
                                    value="{{ old('meeting_point', $schedule->tour->meeting_point ?? 'Theo thông báo') }}">


                                {{-- Số CCCD/Hộ chiếu --}}
                                <div class="col-md-4">
                                    <label for="identity_number" class="form-label fw-600 text-dark">
                                        {{ __('Số CCCD/Hộ Chiếu') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text" name="passengers[adult][0][identity_number]" id="identity_number"
                                        class="form-control search-form-control
                  @error('passengers.adult.0.identity_number') is-invalid @enderror" value="{{ old(
               'passengers.adult.0.identity_number',
               $identity->identity_number ?? ''
           ) }}" placeholder="{{ __('Nhập số CCCD/Passport') }}" required>

                                    @error('passengers.adult.0.identity_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                {{-- Ngày sinh --}}
                                <div class="col-md-4">
                                    <label for="date_of_birth" class="form-label fw-600 text-dark">
                                        {{ __('Ngày Sinh') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="date" name="passengers[adult][0][date_of_birth]" id="date_of_birth"
                                        class="form-control search-form-control
                  @error('passengers.adult.0.date_of_birth') is-invalid @enderror" value="{{ old(
               'passengers.adult.0.date_of_birth',
               isset($identity->date_of_birth) ? \Carbon\Carbon::parse($identity->date_of_birth)->format('Y-m-d') : ''
           ) }}" max="{{ \Carbon\Carbon::today()->subYears(18)->format('Y-m-d') }}" required oninvalid="
               if (this.validity.valueMissing) {
                   this.setCustomValidity('Vui lòng nhập ngày sinh.');
               } else if (this.validity.rangeOverflow) {
                   this.setCustomValidity('Bạn phải đủ 18 tuổi mới được đặt tour.');
               } else {
                   this.setCustomValidity('Ngày sinh không hợp lệ.');
               }
           " oninput="this.setCustomValidity('')">

                                    @error('passengers.adult.0.date_of_birth')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                    <small class="text-muted d-block mt-1">
                                        Người đặt tour phải đủ 18 tuổi trở lên.
                                    </small>
                                </div>

                                {{-- Giới tính --}}
                                <div class="col-md-4">
                                    <label for="gender" class="form-label fw-600 text-dark">
                                        {{ __('Giới Tính') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="passengers[adult][0][gender]" id="gender" class="form-select search-form-control
                   @error('passengers.adult.0.gender') is-invalid @enderror" required>

                                        <option value="">
                                            {{ __('-- Chọn --') }}
                                        </option>

                                        <option value="male" {{ old(
                'passengers.adult.0.gender',
                $identity->gender ?? ''
            ) === 'male' ? 'selected' : '' }}>
                                            {{ __('Nam') }}
                                        </option>

                                        <option value="female" {{ old(
                'passengers.adult.0.gender',
                $identity->gender ?? ''
            ) === 'female' ? 'selected' : '' }}>
                                            {{ __('Nữ') }}
                                        </option>

                                        <option value="other" {{ old(
                'passengers.adult.0.gender',
                $identity->gender ?? ''
            ) === 'other' ? 'selected' : '' }}>
                                            {{ __('Khác') }}
                                        </option>
                                    </select>

                                    @error('passengers.adult.0.gender')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <!-- Hidden identity details -->
                                <input type="hidden" name="issue_date" id="issue_date"
                                    value="{{ $identity->issue_date ?? '2020-01-01' }}">
                                <input type="hidden" name="expiry_date" id="expiry_date"
                                    value="{{ $identity->expiry_date ?? '2040-01-01' }}">
                                <input type="hidden" name="issue_place" id="issue_place"
                                    value="{{ $identity->issue_place ?? 'Hà Nội' }}">

                                @php
                                    $isMultiDayTour = (($schedule->tour->duration_nights ?? $tour->duration_nights ?? 0) > 0)
                                        || (($schedule->tour->duration_days ?? $tour->duration_days ?? 0) > 1);
                                @endphp
                                @if($isMultiDayTour)
                                <div class="col-12 mt-3">
                                    <div class="p-3 bg-light rounded border">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="upload_cccd_check" {{ (isset($identity) && ($identity->front_image_url || $identity->back_image_url)) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-600 text-dark" for="upload_cccd_check">
                                                {{ __('Tôi muốn tải lên ảnh CCCD/Hộ chiếu để hỗ trợ làm thủ tục lưu trú (Tùy chọn)') }}
                                            </label>
                                        </div>
                                        <div class="row g-2 align-items-end" id="cccd_upload_fields" style="display: {{ (isset($identity) && ($identity->front_image_url || $identity->back_image_url)) ? 'flex' : 'none' }};">
                                            @if(isset($identity) && ($identity->front_image_url || $identity->back_image_url))
                                            <div class="col-12 mb-1">
                                                <div class="alert alert-success py-2 px-3 mb-0 small d-flex align-items-center">
                                                    <i class="bi bi-check-circle-fill me-2"></i>
                                                    {{ __('Bạn đã có ảnh CCCD/Hộ chiếu trong hồ sơ. Tải lên ảnh mới bên dưới nếu muốn cập nhật.') }}
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-6">
                                                <label for="front_image" class="form-label small text-muted mb-1"><i class="bi bi-card-image me-1"></i>{{ __('Ảnh mặt trước') }}</label>
                                                <input type="file" name="front_image" id="front_image" class="form-control" accept="image/*" placeholder="Mặt trước">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="back_image" class="form-label small text-muted mb-1"><i class="bi bi-card-image me-1"></i>{{ __('Ảnh mặt sau') }}</label>
                                                <input type="file" name="back_image" id="back_image" class="form-control" accept="image/*" placeholder="Mặt sau">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const check = document.getElementById('upload_cccd_check');
                                        const fields = document.getElementById('cccd_upload_fields');
                                        if (check && fields) {
                                            check.addEventListener('change', function() {
                                                fields.style.display = this.checked ? 'flex' : 'none';
                                                if (!this.checked) {
                                                    const front = document.getElementById('front_image');
                                                    const back = document.getElementById('back_image');
                                                    if (front) front.value = '';
                                                    if (back) back.value = '';
                                                }
                                            });
                                        }
                                    });
                                    function checkEmailMatch() {
        const email = document.getElementById("customer_email")?.value;
        const confirm = document.getElementById("customer_email_confirmation");
        const error = document.getElementById("email_match_error");
        
        if (confirm && error) {
            if (confirm.value && email !== confirm.value) {
                confirm.classList.add("is-invalid");
                error.style.display = "block";
                confirm.setCustomValidity("Email nhập lại không khớp!");
            } else {
                confirm.classList.remove("is-invalid");
                error.style.display = "none";
                confirm.setCustomValidity("");
            }
        }
    }
    
    document.getElementById("customer_email")?.addEventListener("input", checkEmailMatch);
</script>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary px-5 py-2 btn-next" data-next="2">
                                Tiếp tục <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div> <!-- END WIZARD STEP 1 -->

                    <!-- WIZARD STEP 2 -->
                    <div class="wizard-panel" id="step-panel-2">
                        <!-- Section 3: Phương Thức Vận Chuyển -->

                        <input type="hidden" name="transport_type" id="transport_self" value="self">

                        <!-- Vùng hiển thị kết quả phương tiện (AJAX) -->
                        <div id="transport_options_container" style="display: none;"
                            class="p-4 bg-light rounded border">
                            <div id="transport_results"></div>
                        </div>


                        <!-- Vé tham quan đã gộp vào base_price, không hiển thị lựa chọn nữa -->

                        <!-- Section: Lưu trú (Đã chỉ định sẵn) -->
                        @if($schedule->tour->accommodation_tiers && $schedule->tour->accommodation_tiers->isNotEmpty())
                        @php
                            $firstTier = $schedule->tour->accommodation_tiers->first();
                            $room = $firstTier ? $firstTier->room_type : null;
                            $acc = $room ? $room->accommodation : null;
                        @endphp
                        <div class="mb-5">
                            @if($acc && $acc->is_active)
                            <h4 class="form-section-title">
                                <i class="bi bi-building"></i>
                                {{ __('Thông tin Nơi lưu trú') }}
                            </h4>

                            <!-- Hiển thị thông tin lưu trú đã chỉ định -->
                            <div class="row g-4 mb-4">
                                <div class="col-12">
                                    <div class="card shadow-sm border border-primary bg-primary bg-opacity-10 w-100 accommodation-label" style="transition: all 0.2s;">
                                        <div class="card-body p-3 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                                            <div class="d-flex align-items-center">
                                                <!-- Input ẩn radio để javascript vẫn nhận diện và tính toán giá phòng -->
                                                <input class="form-check-input accommodation-radio d-none" style="transform: scale(1.3); margin-right: 15px;" type="radio"
                                                    name="accommodation_id" value="{{ $room->id }}"
                                                    data-name="{{ $room->name }} ({{ $acc->name }})"
                                                    checked required
                                                    data-base-capacity="{{ $room->base_capacity }}"
                                                    data-max-capacity="{{ $room->max_capacity }}"
                                                    data-base-price="{{ $room->base_price }}"
                                                    data-extra-price="{{ $room->extra_bed_price }}"
                                                    data-child-price="{{ $room->child_surcharge_price }}">
                                                
                                                @if($acc->image_url)
                                                    <img src="{{ asset($acc->image_url) }}" alt="{{ $acc->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-building text-muted fs-3"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-grow-1 w-100 mt-2 mt-md-0">
                                                <h6 class="mb-1 fw-bold text-primary fs-5">{{ $acc->name }} <span class="badge bg-warning text-dark">{{ $acc->star_rating }} Sao</span></h6>
                                                <div class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> {{ $acc->name }} - {{ $acc->address }}</div>
                                                <div class="text-dark small">Hạng phòng chỉ định: <strong>{{ $room->name }}</strong></div>
                                                
                                                <div class="d-flex flex-wrap gap-2 mb-2 mt-2">
                                                    @php
                                                        $desc = strtolower($acc->description ?? '');
                                                    @endphp
                                                    @if(str_contains($desc, 'bơi')) <span class="badge bg-light text-dark border"><i class="bi bi-water"></i> Hồ bơi</span> @endif
                                                    @if(str_contains($desc, 'gym')) <span class="badge bg-light text-dark border"><i class="bi bi-bicycle"></i> Gym</span> @endif
                                                    @if(str_contains($desc, 'buffet') || str_contains($desc, 'sáng')) <span class="badge bg-light text-dark border"><i class="bi bi-cup-hot"></i> Buffet sáng</span> @endif
                                                    @if(str_contains($desc, 'spa')) <span class="badge bg-light text-dark border"><i class="bi bi-flower1"></i> Spa</span> @endif
                                                </div>
                                            </div>
                                            
                                            <div class="text-start text-md-end mt-2 mt-md-0 w-100" style="max-width: 200px;">
                                                <div class="text-danger fw-bold fs-5">
                                                    {{ format_currency($room->base_price) }}
                                                </div>
                                                <small class="text-muted d-block">/ phòng / {{ $schedule->tour->duration_nights ?? 1 }} đêm<br>(sức chứa cơ bản: {{ $room->base_capacity }} người)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Tùy chọn xếp phòng thông minh -->
                            <div class="card bg-light border-0 rounded-3 p-3 mt-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-magic text-warning"></i> Gợi ý Xếp Phòng Tự Động</h6>
                                    <div class="small bg-white px-2 py-1 rounded border shadow-sm">
                                        Tổng: <b class="text-primary">{{ $adults }}</b> người lớn, <b class="text-info">{{ $children }}</b> trẻ em
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <button type="button" class="btn btn-sm btn-primary room-strategy-btn" data-strategy="economy">
                                        <i class="bi bi-piggy-bank"></i> Tiết kiệm (Ưu tiên ghép giường phụ)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary room-strategy-btn" data-strategy="comfort">
                                        <i class="bi bi-star"></i> Thoải mái (Ưu tiên phòng đơn)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('manual_room_controls').classList.toggle('d-none')">
                                        <i class="bi bi-pencil-square"></i> Tùy chỉnh tay
                                    </button>
                                </div>

                                <div id="room_visualizer" class="row g-2 mb-2">
                                    <!-- Rendered by JS -->
                                </div>
                                
                                <!-- Manual Controls -->
                                <div id="manual_room_controls" class="d-none mt-3 p-3 bg-white border rounded shadow-sm">
                                    <h6 class="fw-bold small text-muted mb-3"><i class="bi bi-sliders"></i> Tùy chỉnh chi tiết (Ghi đè gợi ý)</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-label text-dark fw-600 mb-0">Số lượng phòng</label>
                                            </div>
                                            <div class="small text-muted mb-2">
                                                Tổng số phòng sẽ ở.
                                            </div>
                                            <div class="input-group" style="width: 130px;">
                                                <button class="btn btn-outline-secondary border-end-0 btn-room-qty-minus" type="button" data-target="single_rooms_count">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" name="single_rooms_count" id="single_rooms_count"
                                                    class="form-control text-center border-secondary border-start-0 border-end-0 fw-bold bg-white qty-input"
                                                    value="1" min="1" max="{{ max(1, $adults) }}">
                                                <button class="btn btn-outline-secondary border-start-0 btn-room-qty-plus" type="button" data-target="single_rooms_count">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-label text-dark fw-600 mb-0">Giường phụ (Extra bed)</label>
                                            </div>
                                            <div class="small text-muted mb-2">Thêm giường vào phòng đôi</div>
                                            <div class="input-group" style="width: 130px;">
                                                <button class="btn btn-outline-secondary border-end-0 btn-room-qty-minus" type="button" data-target="extra_beds_count">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" name="extra_beds_count" id="extra_beds_count"
                                                    class="form-control text-center border-secondary border-start-0 border-end-0 fw-bold bg-white qty-input"
                                                    value="0" min="0" max="{{ $adults + $children }}">
                                                <button class="btn btn-outline-secondary border-start-0 btn-room-qty-plus" type="button" data-target="extra_beds_count">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($schedule->tour->tickets && $schedule->tour->tickets->isNotEmpty())
                        <!-- Section: Vé tham quan kèm theo tour -->
                        <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                <h4 class="h5 fw-bold mb-0 text-primary">
                                    <i class="bi bi-ticket-perforated me-2"></i>Vé tham quan (Đã bao gồm trong giá Tour)
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <ul class="list-group list-group-flush">
                                    @foreach($schedule->tour->tickets as $includedTicket)
                                    <li class="list-group-item px-0 py-3 d-flex align-items-center">
                                        <div class="me-3">
                                            @if($includedTicket->primaryImage)
                                                <img src="{{ $includedTicket->primaryImage->image_url }}" alt="{{ $includedTicket->title }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-ticket h3 mb-0"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">{{ $includedTicket->title }}</h6>
                                            <div class="small text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $includedTicket->destination->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                <i class="bi bi-check-circle-fill me-1"></i> Đã bao gồm
                                            </span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                        @if($schedule->tour->addons && $schedule->tour->addons->where('type', 'extra')->isNotEmpty())
                        <!-- Section: Dịch vụ Addon -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-plus-circle-dotted"></i>
                                {{ __('Dịch vụ bổ sung (Add-ons)') }}
                            </h4>

                            <div class="row g-4">
                                @php
                                $extraAddons = $schedule->tour->addons->where('type', 'extra');
                                @endphp
                                @foreach($extraAddons as $addon)
                                @if($addon->is_active)
                                <div class="col-12">
                                    <div class="card border shadow-sm">
                                        <div
                                            class="card-body p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($addon->image_url)
                                                <img src="{{ asset($addon->image_url) }}" alt="{{ $addon->name }}"
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                <div
                                                    style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1 fw-bold text-primary">{{ $addon->name }}</h6>
                                                    <div class="text-danger fw-bold"
                                                        id="addon_price_display_{{ $addon->id }}"
                                                        data-base-price="{{ $addon->price }}">
                                                        {{ format_currency($addon->price) }}
                                                    </div>
                                                    @if($addon->description)
                                                    <small
                                                        class="text-muted d-block mt-1">{{ $addon->description }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <div>
                                                    <label class="small text-muted mb-1">Ngày sử dụng</label>
                                                    <input type="date" name="addons[{{ $addon->id }}][usage_date]"
                                                        class="form-control form-control-sm addon-date-input"
                                                        value="{{ \Carbon\Carbon::parse($schedule->departure_date)->format('Y-m-d') }}"
                                                        min="{{ \Carbon\Carbon::parse($schedule->departure_date)->format('Y-m-d') }}"
                                                        max="{{ \Carbon\Carbon::parse($schedule->return_date)->format('Y-m-d') }}"
                                                        data-addon-id="{{ $addon->id }}">
                                                </div>
                                                <div>
                                                    <label class="small text-muted mb-1">Số lượng</label>
                                                    <div class="input-group input-group-sm" style="width: 110px;">
                                                        <button
                                                            class="btn btn-outline-secondary border-end-0 btn-addon-qty-minus"
                                                            type="button" data-target="addon-{{ $addon->id }}">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" name="addons[{{ $addon->id }}][qty]"
                                                            id="addon-{{ $addon->id }}"
                                                            class="form-control form-control-sm addon-qty-input text-center border-secondary border-start-0 border-end-0 fw-bold bg-white qty-input"
                                                            value="0" min="0" max="99" data-addon-id="{{ $addon->id }}">
                                                        <button
                                                            class="btn btn-outline-secondary border-start-0 btn-addon-qty-plus"
                                                            type="button" data-target="addon-{{ $addon->id }}">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 btn-prev" data-prev="1">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </button>
                            <button type="button" class="btn btn-primary px-5 py-2 btn-next" data-next="3">
                                Tiếp tục <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div> <!-- END WIZARD STEP 2 -->

                    <!-- WIZARD STEP 3 -->
                    <div class="wizard-panel" id="step-panel-3">
                        <!-- Section 4: Hình Thức Thanh Toán (100% or 30%) -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-cash-coin"></i>
                                {{ __('Hình Thức Thanh Toán') }}
                            </h4>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_type" id="payment_type_full"
                                        value="full" checked>
                                    <label class="transport-option w-100 p-4 text-start" for="payment_type_full">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check2-all text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __('Thanh toán 100%') }}</div>
                                                <div class="small text-muted mt-1">
                                                    {{ __('Thanh toán toàn bộ giá trị đơn hàng') }}
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_type" id="payment_type_deposit"
                                        value="deposit">
                                    <label class="transport-option w-100 p-4 text-start" for="payment_type_deposit">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-pie-chart text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __('Đặt cọc 30% giữ chỗ') }}
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    {{ __('Phần còn lại thanh toán sau') }}
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Phương Thức Thanh Toán -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-credit-card"></i>
                                {{ __('Phương Thức Thanh Toán') }}
                            </h4>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="payment_transfer"
                                        value="transfer" checked>
                                    <label class="transport-option w-100 p-4 text-start" for="payment_transfer">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-bank text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __(' Chuyển khoản') }}</div>
                                                <div class="small text-muted mt-1">
                                                    {{ __('Chuyển khoản thủ công qua QR ') }}
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="payment_vnpay"
                                        value="vnpay">
                                    <label class="transport-option w-100 p-4 text-start" for="payment_vnpay">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-qr-code-scan text-muted" style="font-size: 32px;"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-5 text-dark">{{ __('Thanh toán qua VNPay') }}
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    {{ __('Cổng thanh toán điện tử an toàn') }}
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Mã Khuyến Mãi -->
                        <div class="mb-5">
                            <h4 class="form-section-title">
                                <i class="bi bi-tags"></i>
                                {{ __('Mã Khuyến Mãi') }}
                            </h4>
                            <div class="card border-0 bg-light p-4 rounded-4">
                                <div
                                    class="d-flex align-items-center justify-content-between bg-white p-3 rounded-3 border">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 44px; height: 44px;">
                                            <i class="bi bi-ticket-perforated-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" id="selected_coupon_display_code">Chưa chọn
                                                mã giảm giá</div>
                                            <div class="small text-muted" id="selected_coupon_display_desc">Chọn mã để
                                                nhận ưu đãi cho chuyến đi</div>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-primary px-3 fw-600 btn-sm" type="button"
                                        data-bs-toggle="modal" data-bs-target="#couponSelectModal"
                                        id="btn_open_coupon_modal">
                                        {{ __('Chọn mã giảm giá') }}
                                    </button>
                                </div>
                                <div id="coupon_message" class="small mt-2" style="display: none;"></div>
                                <input type="hidden" name="coupon_code" id="coupon_code_input" value="">
                                <input type="hidden" name="discount_amount" id="input_discount_amount" value="0">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 btn-prev" data-prev="2">
                                <i class="bi bi-arrow-left me-2"></i> Quay lại
                            </button>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-register-premium px-5 py-2 fs-5">
                                <i class="bi bi-shield-check me-2"></i> {{ __('Xác Nhận Thanh Toán') }}
                            </button>
                        </div>
                    </div> <!-- END WIZARD STEP 3 -->
                </form>
            </div>
        </div>

        <!-- Booking Summary Sidebar -->
        <div class="col-lg-4 reveal-up" style="transition-delay: 0.2s;">
            <div class="glass-panel p-4 p-md-5 sticky-top" style="top: 100px;">
                <h4 class="fw-bold mb-4">{{ __('Tóm Tắt Đơn Hàng') }}</h4>

                <!-- Tour Info -->
                <div class="mb-4 pb-4 border-bottom">
                    <h6 class="fw-bold text-dark fs-5 mb-3 lh-base">{{ $schedule->tour->title }}</h6>
                    <div class="d-flex align-items-center text-muted fw-500 mb-2">
                        <i class="bi bi-geo-alt fs-5 text-danger me-3"></i>
                        {{ $schedule->tour->destination->name ?? __('Đang cập nhật') }}
                    </div>
                    <div class="d-flex align-items-center text-muted fw-500 mb-2">
                        <i class="bi bi-calendar-event fs-5 text-primary me-3"></i>
                        {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                    </div>
                    @if($schedule->tour->departure_time)
                    <div class="d-flex align-items-center text-muted fw-500 mb-2">
                        <i class="bi bi-alarm fs-5 text-info me-3"></i>
                        {{ \Carbon\Carbon::parse($schedule->tour->departure_time)->format('H\hi') }}
                    </div>
                    @endif
                    @if($schedule->tour->meeting_point)
                    <div class="d-flex align-items-center text-muted fw-500">
                        <i class="bi bi-geo fs-5 text-success me-3"></i>
                        {{ $schedule->tour->meeting_point }}
                    </div>
                    @endif
                </div>

                <!-- Passenger Count -->
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-500">{{ __('Người lớn:') }}</span>
                        <strong class="text-dark">{{ $adults }} × {{ format_currency($basePrice) }}
                            @if($holidaySurcharge > 0)
                            <span class="badge bg-danger ms-1 px-1 py-0"
                                style="font-size:0.6rem">+{{ $holidaySurcharge }}% Lễ</span>
                            @endif
                        </strong>
                    </div>
                    @if($children > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-500">{{ __('Trẻ em:') }}</span>
                        <strong class="text-dark">{{ $children }} × {{ format_currency($childPrice) }}
                            @if($holidaySurcharge > 0)
                            <span class="badge bg-danger ms-1 px-1 py-0"
                                style="font-size:0.6rem">+{{ $holidaySurcharge }}% Lễ</span>
                            @endif
                        </strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-muted fw-500">{{ __('Tổng khách:') }}</span>
                        <strong class="fs-5">{{ $totalPersons }} {{ __('người') }}</strong>
                    </div>
                </div>

                @if($schedule->tour->accommodation_tiers && $schedule->tour->accommodation_tiers->isNotEmpty())
                <!-- Cấu hình phòng (Room Configuration) -->
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                            <div>
                                <h6 class="mb-0 fw-bold">Số lượng phòng</h6>
                                <small class="text-muted">Phòng tiêu chuẩn</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle btn-room-qty-minus" data-target="single_rooms_count" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" name="single_rooms_count" id="sidebar_single_rooms_count" class="form-control form-control-sm text-center mx-2 room-qty-input fw-bold" value="1" min="1" max="10" style="width: 50px; background: white;" readonly>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle btn-room-qty-plus" data-target="single_rooms_count" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Total Price -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                        id="ticket_fee_row" style="display: none !important;">
                        <span class="text-muted fw-500">{{ __('Vé tham quan:') }}</span>
                        <strong class="text-dark" id="display_ticket_price">0 đ</strong>
                    </div>
                    <div class="mb-2 pb-2 border-bottom" id="accommodation_fee_row" style="display: none !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted fw-500">{{ __('Phòng & Lưu trú:') }}</span>
                            <strong class="text-dark" id="display_accommodation_price">0 đ</strong>
                        </div>
                        <div id="accommodation_breakdown" class="small text-muted ps-3 border-start border-2 ms-1 mt-2">
                            <!-- Breakdown goes here -->
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                        id="addon_fee_row" style="display: none !important;">
                        <span class="text-muted fw-500">{{ __('Dịch vụ thêm:') }}</span>
                        <strong class="text-dark" id="display_addon_price">0 đ</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                        id="coupon_fee_row" style="display: none !important;">
                        <span class="text-success fw-500"><i
                                class="bi bi-tag-fill me-1"></i>{{ __('Giảm giá:') }}</span>
                        <strong class="text-success" id="display_coupon_discount">0 đ</strong>
                    </div>
                    <div class="text-muted fw-500 mb-2">{{ __('Tổng Tiền Đơn Hàng:') }}</div>
                    <div class="text-danger fw-bold lh-1" style="font-size: 2rem;" id="display_total_price">
                        {!! format_currency($totalPrice) !!}
                    </div>

                    <div id="deposit_amount_row" style="display: none;"
                        class="mt-4 pt-3 border-top border-primary border-2">
                        <div class="text-primary fw-500 mb-2"><i
                                class="bi bi-check2-circle me-1"></i>{{ __('Cần Thanh Toán Ngay (Cọc 30%):') }}</div>
                        <div class="text-primary fw-bold lh-1" style="font-size: 1.8rem;" id="display_deposit_price">
                            0 đ
                        </div>
                        <div class="text-muted small mt-2">{{ __('Còn lại (70%) thanh toán sau:') }} <span
                                class="fw-bold text-dark" id="display_remaining_price">0 đ</span></div>
                    </div>
                </div>

                <div class="d-flex align-items-start text-muted small lh-lg mt-4 pt-4 border-top">
                    <i class="bi bi-shield-lock text-success fs-5 me-2 mt-n1"></i>
                    {{ __('Thông tin của bạn được bảo mật tuyệt đối an toàn.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chọn Mã Giảm Giá -->
<div class="modal fade" id="couponSelectModal" tabindex="-1" aria-labelledby="couponSelectModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h5 class="modal-title fw-600" id="couponSelectModalLabel"><i
                        class="bi bi-tags-fill text-primary me-2"></i>Chọn Mã Giảm Giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #f8fafc;">
                <!-- Nhập mã giảm giá thủ công -->
                <div class="bg-white p-3 rounded-3 border mb-3">
                    <label class="form-label fw-600 small text-dark mb-2">Nhập mã giảm giá khác</label>
                    <div class="input-group">
                        <input type="text" class="form-control border-primary text-uppercase" id="manual_coupon_input"
                            placeholder="Nhập mã giảm giá...">
                        <button class="btn btn-primary" type="button" id="btn_apply_manual_coupon">Áp dụng</button>
                    </div>
                    <div id="manual_coupon_error" class="text-danger small mt-1" style="display: none;"></div>
                </div>

                <label class="form-label fw-bold text-secondary small mb-2"><i
                        class="bi bi-gift-fill text-warning me-1"></i>Mã giảm giá dành cho bạn</label>
                <div class="d-flex flex-column gap-3" id="coupons_list_container"
                    style="max-height: 380px; overflow-y: auto; padding: 2px;">
                    <!-- Sẽ được điền bằng JS -->
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    const DRAFT_KEY = 'tour_draft_booking_{{ $schedule->id }}_{{ $adults }}_{{ $children }}';



    // TRANSPORT DYNAMIC LOGIC
    @php
    $iataMap = [
        'Đà Nẵng' => 'DAD',
        'Thành Phố Hồ Chí Minh' => 'SGN',
        'Hà Nội' => 'HAN',
        'Phú Quốc' => 'PQC',
        'Nha Trang' => 'CXR',
        'Huế' => 'HUI',
        'Vinh' => 'VII',
        'Đà Lạt' => 'DLI',
        'Hải Phòng' => 'HPH',
    ];
    $departureLoc = $schedule->tour->departure_location->name ?? '';
    $tourDepartureCode = $iataMap[$departureLoc] ?? 'SGN';
    $departureDate = \Carbon\Carbon::parse($schedule->departure_date)->format('Y-m-d');
    @endphp

    const transportRadios = document.querySelectorAll('input[name="transport_type"]');
    const transportContainer = document.getElementById('transport_options_container');
    const transportLoading = document.getElementById('transport_loading');
    const transportResults = document.getElementById('transport_results');


    const inputTransportPrice = document.getElementById('input_transport_price');
    const inputTransportData = document.getElementById('input_transport_data');
    const inputTotalPrice = document.getElementById('input_total_price');

    const displayTotalPrice = document.getElementById('display_total_price');

    const displayTicketPrice = document.getElementById('display_ticket_price');
    const ticketFeeRow = document.getElementById('ticket_fee_row');

    const baseTourPrice = {{ $totalPrice }};
    const totalPersonsCount = {{ $totalPersons }};
    const DEPOSIT_RATE = {{ config('booking.deposit_rate') }};
    const CHILD_PRICE_RATE = {{ config('booking.child_price_rate') }};

    let currentTransportPrice = 0;
    let currentTicketPrice = 0;
    let currentAddonPrice = 0;
    let currentCouponDiscount = 0;

    const currency = '{{ Session::get("currency", "VND") }}';
    let rate = 1;
    let symbol = ' VNĐ';
    let prefix = false;

    switch (currency) {
        case 'USD':
            rate = 25000;
            symbol = '$';
            prefix = true;
            break;
        case 'EUR':
            rate = 27000;
            symbol = '€';
            prefix = true;
            break;
        case 'CNY':
            rate = 3500;
            symbol = '¥';
            prefix = true;
            break;
        case 'VND':
        default:
            rate = 1;
            symbol = ' VNĐ';
            prefix = false;
            break;
    }

    function formatCurrencyDynamic(amount) {
        const converted = amount / rate;
        let formatted = new Intl.NumberFormat(currency === 'VND' ? 'vi-VN' : 'en-US', {
            minimumFractionDigits: currency === 'VND' ? 0 : 2,
            maximumFractionDigits: currency === 'VND' ? 0 : 2
        }).format(converted);
        return prefix ? symbol + formatted : formatted + symbol;
    }

    let currentAccommodationPrice = 0;

    function updateTotalDisplay(transportPrice = currentTransportPrice, ticketPrice = currentTicketPrice) {
        currentTransportPrice = transportPrice;
        currentTicketPrice = ticketPrice;
        const finalPrice = Math.max(0, baseTourPrice + transportPrice + ticketPrice + currentAddonPrice +
            currentAccommodationPrice - currentCouponDiscount);

        inputTransportPrice.value = transportPrice;
        inputTotalPrice.value = finalPrice;

        if (ticketPrice > 0) {
            ticketFeeRow.style.setProperty('display', 'flex', 'important');
            displayTicketPrice.textContent = formatCurrencyDynamic(ticketPrice);
        } else {
            ticketFeeRow.style.setProperty('display', 'none', 'important');
        }

        const displayAddonPrice = document.getElementById('display_addon_price');
        const addonFeeRow = document.getElementById('addon_fee_row');
        if (currentAddonPrice > 0) {
            addonFeeRow.style.setProperty('display', 'flex', 'important');
            displayAddonPrice.textContent = formatCurrencyDynamic(currentAddonPrice);
        } else {
            addonFeeRow.style.setProperty('display', 'none', 'important');
        }
        
        const displayAccommodationPrice = document.getElementById('display_accommodation_price');
        const accommodationFeeRow = document.getElementById('accommodation_fee_row');
        if (accommodationFeeRow && displayAccommodationPrice) {
            if (currentAccommodationPrice > 0) {
                accommodationFeeRow.style.setProperty('display', 'flex', 'important');
                displayAccommodationPrice.textContent = formatCurrencyDynamic(currentAccommodationPrice);
            } else {
                accommodationFeeRow.style.setProperty('display', 'none', 'important');
            }
        }

        displayTotalPrice.textContent = formatCurrencyDynamic(finalPrice);
        
        // Highlight effect
        displayTotalPrice.classList.remove('text-danger');
        displayTotalPrice.classList.add('text-success');
        displayTotalPrice.style.transform = 'scale(1.05)';
        displayTotalPrice.style.transition = 'all 0.3s ease';
        
        setTimeout(() => {
            displayTotalPrice.classList.remove('text-success');
            displayTotalPrice.classList.add('text-danger');
            displayTotalPrice.style.transform = 'scale(1)';
        }, 500);
    }

    // Xử lý khi thay đổi số lượng vé tham quan
    const ticketInputs = document.querySelectorAll('.ticket-qty-input');

    function updateTicketTotal() {
        let totalTicket = 0;
        ticketInputs.forEach(inp => {
            let qty = parseInt(inp.value);
            if (isNaN(qty) || qty < 0) qty = 0;
            totalTicket += qty * parseFloat(inp.dataset.price);
        });
        updateTotalDisplay(currentTransportPrice, totalTicket);
    }

    ticketInputs.forEach(input => {
        input.addEventListener('change', function() {
            let val = parseInt(this.value);
            const min = parseInt(this.getAttribute('min')) || 0;
            const max = parseInt(this.getAttribute('max')) || 99;
            if (isNaN(val) || val < min) val = min;
            if (val > max) val = max;
            this.value = val;
            updateTicketTotal();
        });
        input.addEventListener('blur', updateTicketTotal);
        input.addEventListener('keyup', updateTicketTotal);
    });

    document.querySelectorAll('.btn-ticket-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            let val = parseInt(input.value) || 0;
            const min = parseInt(input.getAttribute('min')) || 0;
            if (val > min) {
                input.value = val - 1;
                updateTicketTotal();
            }
        });
    });

    document.querySelectorAll('.btn-ticket-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            let val = parseInt(input.value) || 0;
            const max = parseInt(input.getAttribute('max')) || 99;
            if (val < max) {
                input.value = val + 1;
                updateTicketTotal();
            }
        });
    });

    // Xử lý Dịch vụ Addon
    const holidaysData = @json($holidays);
    // currentAddonPrice already declared at top

    function getHolidaySurcharge(dateStr) {
        let maxSurcharge = 0;
        holidaysData.forEach(holiday => {
            if (dateStr >= holiday.start_date && dateStr <= holiday.end_date) {
                if (parseFloat(holiday.price_increase_percentage) > maxSurcharge) {
                    maxSurcharge = parseFloat(holiday.price_increase_percentage);
                }
            }
        });
        return maxSurcharge;
    }

    const singleRoomsInput = document.getElementById('single_rooms_count');
    const extraBedsInput = document.getElementById('extra_beds_count');
    const accommodationRadios = document.querySelectorAll('.accommodation-radio');

    function updateAccommodationTotal() {
        if (!singleRoomsInput) return;

        let singleRooms = parseInt(singleRoomsInput.value) || 0;
        let extraBeds = parseInt(extraBedsInput.value) || 0;
        let adults = parseInt("{{ $adults }}") || 0;
        let children = parseInt("{{ $children }}") || 0;

        // Reset radio active states
        accommodationRadios.forEach(radio => {
            const label = radio.closest('.accommodation-label');
            if (radio.checked) {
                label.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
            } else {
                label.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            }
        });

        // Update button states
        const maxSingle = adults;
        const maxExtra = adults + children;

        document.querySelectorAll('.btn-room-qty-minus[data-target="single_rooms_count"]').forEach(btn => btn.disabled = singleRooms <= (adults == 1 ? 1 : 0));
        document.querySelectorAll('.btn-room-qty-plus[data-target="single_rooms_count"]').forEach(btn => btn.disabled = singleRooms >= maxSingle);
        
        document.querySelectorAll('.btn-room-qty-minus[data-target="extra_beds_count"]').forEach(btn => btn.disabled = extraBeds <= 0);
        document.querySelectorAll('.btn-room-qty-plus[data-target="extra_beds_count"]').forEach(btn => btn.disabled = extraBeds >= maxExtra);

        let selectedAcc = document.querySelector('.accommodation-radio:checked');
        if (selectedAcc) {
            let basePrice = parseFloat(selectedAcc.dataset.basePrice) || 0;
            let singlePrice = parseFloat(selectedAcc.dataset.singlePrice) || 0;
            let extraPrice = parseFloat(selectedAcc.dataset.extraPrice) || 0;
            let childPrice = parseFloat(selectedAcc.dataset.childPrice) || 0;
            let name = selectedAcc.dataset.name || 'Phòng';

            let baseTotal = basePrice * singleRooms;
            let childTotal = childPrice * children;
            let singleTotal = 0; // Not used anymore in new architecture
            let extraTotal = extraPrice * extraBeds;

            currentAccommodationPrice = baseTotal + singleTotal + extraTotal + childTotal;
            
            // Build breakdown
            const breakdownContainer = document.getElementById('accommodation_breakdown');
            if (breakdownContainer) {
                let html = '';
                if (baseTotal > 0) html += `<div class="d-flex justify-content-between mb-1"><span>Tiền phòng (x${singleRooms}):</span> <span>${formatCurrencyDynamic(baseTotal)}</span></div>`;
                if (extraTotal > 0) html += `<div class="d-flex justify-content-between mb-1"><span>Giường phụ (x${extraBeds}):</span> <span>${formatCurrencyDynamic(extraTotal)}</span></div>`;
                if (childTotal > 0) html += `<div class="d-flex justify-content-between mb-1"><span>Phụ thu trẻ em (x${children}):</span> <span>${formatCurrencyDynamic(childTotal)}</span></div>`;
                breakdownContainer.innerHTML = html;
            }

            updateTotalDisplay();
            // Sync sidebar room count display
            const sidebarSR = document.getElementById('sidebar_single_rooms_count');
            if (sidebarSR) sidebarSR.value = singleRooms;
        }
    }

    // --- AUTO ROOM SUGGESTION LOGIC ---
    function generateRoomAllocation(strategy = 'economy') {
        let A = parseInt("{{ $adults }}") || 0;
        let C = parseInt("{{ $children }}") || 0;
        
        let singleRoomsCount = 1;
        let extraBedsCount = 0;
        
        let selectedAcc = document.querySelector('.accommodation-radio:checked');
        if (selectedAcc) {
            let baseCap = parseInt(selectedAcc.dataset.baseCapacity) || 2;
            let maxCap = parseInt(selectedAcc.dataset.maxCapacity) || 3;
            
            if (strategy === 'comfort') {
                // Thoải mái: Mỗi người lớn 1 giường riêng nếu có thể, hạn chế ghép
                // Cứ 2 người (bất kể lớn bé) xếp 1 phòng
                singleRoomsCount = Math.ceil((A + C) / baseCap);
                extraBedsCount = 0; // Không dùng giường phụ
                
                // Đảm bảo không vượt quá sức chứa tối đa nếu bắt buộc ghép (ví dụ 5 người -> 3 phòng)
                if (singleRoomsCount * baseCap < (A + C)) {
                    extraBedsCount = (A + C) - (singleRoomsCount * baseCap);
                }
            } else {
                // Tiết kiệm (mặc định): Tối thiểu số phòng, dùng tối đa giường phụ
                singleRoomsCount = Math.ceil(A / baseCap);
                
                // Đảm bảo đủ sức chứa tối đa cho cả trẻ em
                if (singleRoomsCount * maxCap < (A + C)) {
                    singleRoomsCount = Math.ceil((A + C) / maxCap);
                }
                
                // Tính số giường phụ
                let overflow = (A + C) - (singleRoomsCount * baseCap);
                if (overflow > 0) {
                    extraBedsCount = Math.min(overflow, singleRoomsCount * (maxCap - baseCap));
                }
            }
        }
        
        const singleRoomsInput = document.getElementById('single_rooms_count');
        const extraBedsInput = document.getElementById('extra_beds_count');

        if (singleRoomsInput) {
            singleRoomsInput.value = singleRoomsCount;
            extraBedsInput.value = extraBedsCount;
            updateAccommodationTotal();
        }
        
        // Render visualizer
        const container = document.getElementById('room_visualizer');
        if (container) {
            let html = '';
            // Generate visual cards for each room
            for (let i = 0; i < singleRoomsCount; i++) {
                // Phân bổ giường phụ: Ưu tiên nhét vào các phòng đầu tiên (1 giường phụ / phòng)
                let hasExtraBed = (i < extraBedsCount);
                
                html += `
                    <div class="col-md-3 col-6">
                        <div class="p-2 border rounded bg-white text-center h-100 shadow-sm" style="transition: all 0.2s;">
                            <div class="text-primary mb-1"><i class="bi bi-door-open fs-4"></i></div>
                            <div class="fw-bold small">Phòng ${i+1}</div>
                            <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-people"></i> Tiêu chuẩn
                                ${hasExtraBed ? '<br><span class="text-warning"><i class="bi bi-plus-circle"></i> Kèm giường phụ</span>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            }
            container.innerHTML = html;
            container.style.display = 'flex';
        }
        
        const manualControls = document.getElementById('manual_room_controls');
        if (manualControls) {
            manualControls.classList.remove('d-none');
        }
    }

    if (singleRoomsInput) {
        // Init strategy buttons
        const strategyBtns = document.querySelectorAll('.room-strategy-btn');
        strategyBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                strategyBtns.forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                generateRoomAllocation(this.dataset.strategy);
            });
        });
        
        // Initial auto-suggest run if not restoring
        if (!localStorage.getItem(DRAFT_KEY)) {
            generateRoomAllocation('economy');
        }
        if (singleRoomsInput) {
            singleRoomsInput.addEventListener('input', function() {
                document.getElementById('room_visualizer').innerHTML = '<div class="col-12"><div class="alert alert-secondary small mb-0"><i class="bi bi-info-circle"></i> Đang sử dụng thiết lập số lượng phòng thủ công.</div></div>';
                updateAccommodationTotal();
            });
        }
        if (extraBedsInput) {
            extraBedsInput.addEventListener('input', function() {
                document.getElementById('room_visualizer').innerHTML = '<div class="col-12"><div class="alert alert-secondary small mb-0"><i class="bi bi-info-circle"></i> Đang sử dụng thiết lập số lượng phòng thủ công.</div></div>';
                updateAccommodationTotal();
            });
        }
        accommodationRadios.forEach(radio => {
            radio.addEventListener('change', updateAccommodationTotal);
        });
        
        document.querySelectorAll('.btn-room-qty-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                let val = parseInt(input.value) || 0;
                const min = parseInt(input.getAttribute('min')) || 0;
                if (val > min) {
                    input.value = val - 1;
                    document.getElementById('room_visualizer').innerHTML = '<div class="col-12"><div class="alert alert-secondary small mb-0"><i class="bi bi-info-circle"></i> Đang sử dụng thiết lập số lượng phòng thủ công.</div></div>';
                    updateAccommodationTotal();
                }
            });
        });

        document.querySelectorAll('.btn-room-qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                let val = parseInt(input.value) || 0;
                const max = parseInt(input.getAttribute('max')) || 99;
                if (val < max) {
                    input.value = val + 1;
                    document.getElementById('room_visualizer').innerHTML = '<div class="col-12"><div class="alert alert-secondary small mb-0"><i class="bi bi-info-circle"></i> Đang sử dụng thiết lập số lượng phòng thủ công.</div></div>';
                    updateAccommodationTotal();
                }
            });
        });
        
        updateAccommodationTotal();
    }

    function updateAddonsTotal() {
        let totalAddons = 0;
        const addonRows = document.querySelectorAll('.addon-qty-input');
        addonRows.forEach(input => {
            let qty = parseInt(input.value);
            if (isNaN(qty) || qty < 0) qty = 0;

            if (qty > 0) {
                const addonId = input.dataset.addonId;
                const dateInput = document.querySelector(`.addon-date-input[data-addon-id="${addonId}"]`);
                const usageDate = dateInput.value;
                const priceDisplay = document.getElementById(`addon_price_display_${addonId}`);
                const basePrice = parseFloat(priceDisplay.dataset.basePrice);

                const surcharge = getHolidaySurcharge(usageDate);
                const finalPrice = basePrice * (1 + surcharge / 100);

                totalAddons += finalPrice * qty;

                if (surcharge > 0) {
                    priceDisplay.innerHTML =
                        `${formatCurrencyDynamic(finalPrice)} <span class="badge bg-danger ms-1 px-1 py-0" style="font-size:0.6rem">+${surcharge}% Lễ</span>`;
                } else {
                    priceDisplay.innerHTML = formatCurrencyDynamic(basePrice);
                }
            } else {
                const addonId = input.dataset.addonId;
                const priceDisplay = document.getElementById(`addon_price_display_${addonId}`);
                const basePrice = parseFloat(priceDisplay.dataset.basePrice);
                priceDisplay.innerHTML = formatCurrencyDynamic(basePrice);
            }
        });

        currentAddonPrice = totalAddons;
        updateTotalDisplay(currentTransportPrice, currentTicketPrice);
    }

    const addonInputs = document.querySelectorAll('.addon-qty-input');
    const addonDateInputs = document.querySelectorAll('.addon-date-input');

    addonDateInputs.forEach(input => {
        input.addEventListener('change', updateAddonsTotal);
    });

    addonInputs.forEach(input => {
        input.addEventListener('change', function() {
            let val = parseInt(this.value);
            const min = parseInt(this.getAttribute('min')) || 0;
            const max = parseInt(this.getAttribute('max')) || 99;
            if (isNaN(val) || val < min) val = min;
            if (val > max) val = max;
            this.value = val;
            updateAddonsTotal();
        });
        input.addEventListener('blur', updateAddonsTotal);
        input.addEventListener('keyup', updateAddonsTotal);
    });

    document.querySelectorAll('.btn-addon-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            let val = parseInt(input.value) || 0;
            const min = parseInt(input.getAttribute('min')) || 0;
            if (val > min) {
                input.value = val - 1;
                updateAddonsTotal();
            }
        });
    });

    document.querySelectorAll('.btn-addon-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            let val = parseInt(input.value) || 0;
            const max = parseInt(input.getAttribute('max')) || 99;
            if (val < max) {
                input.value = val + 1;
                updateAddonsTotal();
            }
        });
    });

    window.selectTransportOption = function(price, dataStr) {
        // Parse data
        let data = JSON.parse(decodeURIComponent(dataStr));
        inputTransportData.value = JSON.stringify(data);

        // Highlight selected
        document.querySelectorAll('.transport-item-card').forEach(el => {
            el.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            const icon = el.querySelector('.selected-icon');
            if (icon) icon.style.display = 'none';
        });

        event.currentTarget.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        const icon = event.currentTarget.querySelector('.selected-icon');
        if (icon) icon.style.display = 'block';

        // Update price
        updateTotalDisplay(parseFloat(price));
    };



    // DOB Dropdowns Logic
    const dobDay = document.querySelector('.dob-day');
    const dobMonth = document.querySelector('.dob-month');
    const dobYear = document.querySelector('.dob-year');
    const dobHidden = document.getElementById('date_of_birth');

    if (dobDay && dobMonth && dobYear) {
        // Populate days
        for (let i = 1; i <= 31; i++) {
            let option = document.createElement('option');
            option.value = i.toString().padStart(2, '0');
            option.text = i;
            dobDay.appendChild(option);
        }
        // Populate months
        for (let i = 1; i <= 12; i++) {
            let option = document.createElement('option');
            option.value = i.toString().padStart(2, '0');
            option.text = i;
            dobMonth.appendChild(option);
        }
        // Populate years
        const currentYear = new Date().getFullYear();
        const defaultYear = currentYear - 18;
        for (let i = currentYear; i >= currentYear - 100; i--) {
            let option = document.createElement('option');
            option.value = i;
            option.text = i;
            dobYear.appendChild(option);
        }

        // Initialize from hidden input
        if (dobHidden.value) {
            const parts = dobHidden.value.split('-');
            if (parts.length === 3) {
                dobYear.value = parts[0];
                dobMonth.value = parts[1];
                dobDay.value = parts[2];
            }
        } else {
            // Default to youngest 18-year-old
            dobYear.value = defaultYear;
        }

        const updateHiddenDob = () => {
            if (dobYear.value && dobMonth.value && dobDay.value) {
                dobHidden.value = `${dobYear.value}-${dobMonth.value}-${dobDay.value}`;
            } else {
                dobHidden.value = '';
            }
            const dobError = document.getElementById('dob-error');
            if (dobError) dobError.style.display = 'none';
        };

        dobDay.addEventListener('change', updateHiddenDob);
        dobMonth.addEventListener('change', updateHiddenDob);
        dobYear.addEventListener('change', updateHiddenDob);

        // Sync back when restored from localstorage
        dobHidden.addEventListener('change', function() {
            if (this.value) {
                const parts = this.value.split('-');
                if (parts.length === 3) {
                    dobYear.value = parts[0];
                    dobMonth.value = parts[1];
                    dobDay.value = parts[2];
                }
            }
        });

        // Override formatDob in CCCD scan to populate dropdowns
        const originalFormatDob = window.formatDob;
        window.formatDob = function(dobStr) {
            if (!dobStr) return '';
            const parts = dobStr.split('/');
            if (parts.length === 3) {
                dobDay.value = parts[0];
                dobMonth.value = parts[1];
                dobYear.value = parts[2];
                updateHiddenDob();
                return `${parts[2]}-${parts[1]}-${parts[0]}`;
            }
            return dobStr;
        };
    }

    // WIZARD LOGIC
    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function() {
            // Validate current step
            const currentStep = this.closest('.wizard-panel');
            const inputs = currentStep.querySelectorAll('input[required], select[required]');
            let isValid = true;
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    isValid = false;
                }
            });

            if (isValid) {
                // Add validation for age >= 18 in Step 1
                if (currentStep.id === 'step-panel-1') {
                    const dobInput = document.getElementById('date_of_birth');
                    if (dobInput && dobInput.value) {
                        const dobDate = new Date(dobInput.value);
                        const today = new Date();
                        let age = today.getFullYear() - dobDate.getFullYear();
                        const m = today.getMonth() - dobDate.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < dobDate.getDate())) {
                            age--;
                        }
                        if (age < 18) {
                            dobInput.setCustomValidity('Bạn phải đủ 18 tuổi mới được đặt tour.');
                            dobInput.reportValidity();
                            return;
                        } else {
                            dobInput.setCustomValidity('');
                        }
                    }
                }

                const nextId = this.dataset.next;

                // Add validation for Transport selection in Step 2 if flight/bus options exist and are selected
                if (currentStep.id === 'step-panel-2') {
                    const checkedTransport = document.querySelector('input[name="transport_type"]:checked');
                    const selectedTransport = checkedTransport ? checkedTransport.value : 'self';
                    if ((selectedTransport === 'flight' || selectedTransport === 'bus') &&
                        typeof inputTransportData !== 'undefined' && !inputTransportData.value) {
                        alert(
                            'Vui lòng click chọn một chuyến bay/xe khách cụ thể hoặc chọn phương thức Tự Túc trước khi tiếp tục.'
                        );
                        return;
                    }
                }

                document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
                document.getElementById('step-panel-' + nextId).classList.add('active');
                document.querySelectorAll('.wizard-step').forEach(s => {
                    const stepNum = parseInt(s.id.replace('step-nav-', ''));
                    s.classList.remove('active');
                    if (stepNum < nextId) s.classList.add('completed');
                    if (stepNum == nextId) s.classList.add('active');
                });
                window.scrollTo(0, 0);
            }
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', function() {
            const prevId = this.dataset.prev;
            document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('step-panel-' + prevId).classList.add('active');
            document.querySelectorAll('.wizard-step').forEach(s => {
                const stepNum = parseInt(s.id.replace('step-nav-', ''));
                s.classList.remove('active');
                if (stepNum >= prevId) s.classList.remove('completed');
                if (stepNum == prevId) s.classList.add('active');
            });
            window.scrollTo(0, 0);
        });
    });
    // currentCouponDiscount already declared at top
    const availableCoupons = @json($coupons);

    function getSubtotal() {
        return baseTourPrice + currentTransportPrice + currentTicketPrice + currentAddonPrice + currentAccommodationPrice;
    }

    function renderCouponsList() {
        const subtotal = getSubtotal();
        const container = document.getElementById('coupons_list_container');
        if (!container) return;

        // Phân loại mã đủ điều kiện và không đủ điều kiện
        const eligibleCoupons = [];
        const ineligibleCoupons = [];

        availableCoupons.forEach(coupon => {
            const minOrder = parseFloat(coupon.min_order_value || 0);
            if (subtotal >= minOrder) {
                eligibleCoupons.push(coupon);
            } else {
                ineligibleCoupons.push(coupon);
            }
        });

        let html = '';
        eligibleCoupons.forEach(coupon => {
            html += renderCouponCardHtml(coupon, true, subtotal);
        });
        ineligibleCoupons.forEach(coupon => {
            html += renderCouponCardHtml(coupon, false, subtotal);
        });

        if (availableCoupons.length === 0) {
            html =
                '<div class="text-center py-4 text-muted small"><i class="bi bi-inbox fs-2 mb-2 d-block text-light"></i>Không có mã giảm giá nào khả dụng.</div>';
        }
        container.innerHTML = html;
    }

    function renderCouponCardHtml(coupon, isEligible, subtotal) {
        let discountStr = coupon.discount_type === 'percent' ? `Giảm ${coupon.discount_value}%` :
            `Giảm ${formatCurrencyDynamic(coupon.discount_value)}`;
        if (coupon.discount_type === 'percent' && coupon.max_discount) {
            discountStr += ` (Tối đa ${formatCurrencyDynamic(coupon.max_discount)})`;
        }
        const minOrderVal = parseFloat(coupon.min_order_value || 0);
        const minOrderStr = minOrderVal > 0 ? `Đơn tối thiểu: ${formatCurrencyDynamic(minOrderVal)}` : 'Đơn tối thiểu: 0 đ';
        const currentSelectedCode = document.getElementById('coupon_code_input').value;
        const isSelected = currentSelectedCode === coupon.code;

        let buttonHtml = '';
        if (isEligible) {
            buttonHtml =
                `<button type="button" class="btn ${isSelected ? 'btn-danger' : 'btn-primary'} btn-sm px-3 fw-bold rounded-pill">${isSelected ? 'Hủy chọn' : 'Áp dụng'}</button>`;
        } else {
            const diffAmount = minOrderVal - subtotal;
            buttonHtml = `
                <div class="text-end">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 mb-1" style="font-size: 10px; font-weight: 500;">Không đủ điều kiện</span>
                    <div class="text-muted" style="font-size: 10px;">Mua thêm ${formatCurrencyDynamic(diffAmount)}</div>
                </div>
            `;
        }

        const cardClass = isEligible ? (isSelected ? 'border-primary bg-primary bg-opacity-5' :
            'bg-white border-light-subtle') : 'bg-light opacity-50';
        const clickAttr = isEligible ? `onclick="applyCouponCode('${coupon.code}')"` : '';

        return `
            <div class="card border shadow-sm rounded-3 transition-all ${cardClass}" style="${isEligible ? 'cursor: pointer;' : 'cursor: not-allowed;'} padding: 12px;" ${clickAttr}>
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center ${isEligible ? 'bg-primary bg-opacity-10 text-primary' : 'bg-secondary bg-opacity-10 text-secondary'}" style="width: 44px; height: 44px;"><i class="bi bi-gift-fill fs-5"></i></div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">${coupon.code}</div>
                        <div class="fw-bold text-danger text-truncate" style="font-size: 0.85rem; margin-top: 1px;">${discountStr}</div>
                        <div class="text-muted text-truncate" style="font-size: 0.75rem; margin-top: 2px;">${minOrderStr}</div>
                    </div>
                    <div class="flex-shrink-0">${buttonHtml}</div>
                </div>
            </div>`;
    }

    window.applyCouponCode = function(code) {
        const input = document.getElementById('coupon_code_input');
        const displayCode = document.getElementById('selected_coupon_display_code');
        const displayDesc = document.getElementById('selected_coupon_display_desc');
        const msg = document.getElementById('coupon_message');


        if (input.value === code) {
            resetCouponSelection();
            const modalEl = document.getElementById('couponSelectModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();
            return;
        }

        input.value = code;
        const subtotal = getSubtotal();
        const scheduleId = document.querySelector('input[name="schedule_id"]').value;

        fetch('/api/coupons/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    order_value: subtotal,
                    schedule_id: scheduleId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    msg.style.display = 'block';
                    msg.className = 'small mt-2 text-success fw-bold';
                    msg.textContent = `Áp dụng thành công! Giảm ${formatCurrencyDynamic(data.discount_amount)}`;

                    displayCode.textContent = code;
                    displayCode.className = 'fw-bold text-primary';

                    let descStr = '';
                    const matchedCoupon = availableCoupons.find(c => c.code === code);
                    if (matchedCoupon) {
                        if (matchedCoupon.discount_type === 'percent') {
                            descStr =
                                `Giảm ${matchedCoupon.discount_value}% (Tối đa ${formatCurrencyDynamic(data.discount_amount)})`;
                        } else {
                            descStr = `Giảm ${formatCurrencyDynamic(data.discount_amount)}`;
                        }
                    } else {
                        descStr = `Giảm ${formatCurrencyDynamic(data.discount_amount)}`;
                    }
                    displayDesc.textContent = descStr;

                    currentCouponDiscount = data.discount_amount;
                    document.getElementById('input_discount_amount').value = currentCouponDiscount;
                    document.getElementById('coupon_fee_row').style.setProperty('display', 'flex', 'important');
                    document.getElementById('display_coupon_discount').textContent = '- ' + formatCurrencyDynamic(
                        currentCouponDiscount);

                    updateTotalDisplay();

                    const modalEl = document.getElementById('couponSelectModal');
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();
                } else {
                    msg.style.display = 'block';
                    msg.className = 'small mt-2 text-danger';
                    msg.textContent = data.message;
                    resetCouponSelection();
                }
            })
            .catch(err => {
                msg.style.display = 'block';
                msg.className = 'small mt-2 text-danger';
                msg.textContent = 'Lỗi kết nối. Vui lòng thử lại.';
                resetCouponSelection();
            });
    };

    window.resetCouponSelection = function() {
        document.getElementById('coupon_code_input').value = '';
        document.getElementById('selected_coupon_display_code').textContent = 'Chưa chọn mã giảm giá';
        document.getElementById('selected_coupon_display_code').className = 'fw-bold text-dark';
        document.getElementById('selected_coupon_display_desc').textContent = 'Chọn mã để nhận ưu đãi cho chuyến đi';

        currentCouponDiscount = 0;
        document.getElementById('input_discount_amount').value = 0;
        document.getElementById('coupon_fee_row').style.setProperty('display', 'none', 'important');

        const msg = document.getElementById('coupon_message');
        msg.style.display = 'none';
        msg.textContent = '';

        updateTotalDisplay();
        renderCouponsList();
    };

    // Sự kiện mở modal -> render danh sách
    const modalSelectEl = document.getElementById('couponSelectModal');
    if (modalSelectEl) {
        modalSelectEl.addEventListener('show.bs.modal', function() {
            renderCouponsList();
            document.getElementById('manual_coupon_input').value = '';
            document.getElementById('manual_coupon_error').style.display = 'none';
        });
    }

    // Nhập mã giảm giá thủ công trong modal
    const btnApplyManual = document.getElementById('btn_apply_manual_coupon');
    if (btnApplyManual) {
        btnApplyManual.addEventListener('click', function() {
            const code = document.getElementById('manual_coupon_input').value.trim().toUpperCase();
            const errorEl = document.getElementById('manual_coupon_error');
            if (!code) {
                errorEl.style.display = 'block';
                errorEl.textContent = 'Vui lòng nhập mã giảm giá.';
                return;
            }
            errorEl.style.display = 'none';

            const matched = availableCoupons.find(c => c.code.toUpperCase() === code);
            if (matched) {
                const subtotal = getSubtotal();
                const minOrder = parseFloat(matched.min_order_value || 0);
                if (subtotal >= minOrder) {
                    applyCouponCode(matched.code);
                } else {
                    errorEl.style.display = 'block';
                    errorEl.textContent =
                        `Không đủ điều kiện sử dụng mã. Cần tối thiểu ${formatCurrencyDynamic(minOrder)}`;
                }
            } else {
                const subtotal = getSubtotal();
                const scheduleId = document.querySelector('input[name="schedule_id"]').value;
                fetch('/api/coupons/apply', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            code: code,
                            order_value: subtotal,
                            schedule_id: scheduleId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            applyCouponCode(code);
                        } else {
                            errorEl.style.display = 'block';
                            errorEl.textContent = data.message || 'Mã không hợp lệ hoặc đã hết hạn.';
                        }
                    })
                    .catch(err => {
                        errorEl.style.display = 'block';
                        errorEl.textContent = 'Lỗi kết nối. Vui lòng thử lại sau.';
                    });
            }
        });
    }

    // Override updateTotalDisplay to include discount
    const originalUpdateTotalDisplay = updateTotalDisplay;
    updateTotalDisplay = function(transportPrice = currentTransportPrice, ticketPrice = currentTicketPrice) {
        currentTransportPrice = transportPrice;
        currentTicketPrice = ticketPrice;
        const subtotal = baseTourPrice + currentTransportPrice + currentTicketPrice + currentAddonPrice +
            currentAccommodationPrice;

        // Kiểm tra nếu mã giảm giá đã chọn không còn đủ điều kiện do thay đổi giá trị đơn hàng
        const currentSelectedCode = document.getElementById('coupon_code_input').value;
        if (currentSelectedCode) {
            const matchedCoupon = availableCoupons.find(c => c.code === currentSelectedCode);
            if (matchedCoupon) {
                const minOrder = parseFloat(matchedCoupon.min_order_value || 0);
                if (subtotal < minOrder) {
                    alert(
                        `Đã huỷ mã giảm giá ${currentSelectedCode} do đơn hàng không còn đủ giá trị tối thiểu (${formatCurrencyDynamic(minOrder)}).`
                    );
                    resetCouponSelection();
                    return;
                }
            }
        }

        const finalTotal = Math.max(0, subtotal - currentCouponDiscount);
        originalUpdateTotalDisplay(transportPrice, ticketPrice);
        document.getElementById('display_total_price').innerHTML = formatCurrencyDynamic(finalTotal);

        const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
        const depositRow = document.getElementById('deposit_amount_row');
        if (paymentType === 'deposit') {
            const depositAmount = finalTotal * DEPOSIT_RATE;
            const remainingAmount = finalTotal - depositAmount;
            document.getElementById('display_deposit_price').innerHTML = formatCurrencyDynamic(depositAmount);
            document.getElementById('display_remaining_price').innerHTML = formatCurrencyDynamic(remainingAmount);
            depositRow.style.setProperty('display', 'block', 'important');
        } else {
            depositRow.style.setProperty('display', 'none', 'important');
        }
    };

    // Listen to payment type change
    document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateTotalDisplay();
            document.querySelectorAll('input[name="payment_type"]').forEach(el => {
                el.nextElementSibling.classList.remove('border-primary', 'bg-primary',
                    'bg-opacity-10');
            });
            this.nextElementSibling.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        });
    });

    // Listen to payment method change
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="payment_method"]').forEach(el => {
                el.nextElementSibling.classList.remove('border-primary', 'bg-primary',
                    'bg-opacity-10');
            });
            this.nextElementSibling.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        });
    });

    document.querySelector('input[name="payment_type"]:checked').nextElementSibling.classList.add('border-primary',
        'bg-primary', 'bg-opacity-10');
    document.querySelector('input[name="payment_method"]:checked').nextElementSibling.classList.add('border-primary',
        'bg-primary', 'bg-opacity-10');

    // === SAVE / RESTORE DRAFT BOOKING PROGRESS ===
    const checkoutForm = document.getElementById('checkout-form');
    let isRestoring = false;

    function saveProgress() {
        if (isRestoring) return;
        const formData = new FormData(checkoutForm);
        const dataToSave = {};
        formData.forEach((value, key) => {
            if (key === '_token' || key.includes('image') || ['schedule_id', 'adults', 'children', 'total_price']
                .includes(key)) return;
            if (dataToSave[key]) {
                if (!Array.isArray(dataToSave[key])) {
                    dataToSave[key] = [dataToSave[key]];
                }
                dataToSave[key].push(value);
            } else {
                dataToSave[key] = value;
            }
        });
        localStorage.setItem(DRAFT_KEY, JSON.stringify(dataToSave));
    }

    function restoreProgress() {
        const savedData = localStorage.getItem(DRAFT_KEY);
        if (!savedData) return;

        try {
            isRestoring = true;
            const data = JSON.parse(savedData);
            let restoredCount = 0;

            for (const key in data) {
                const value = data[key];
                const inputs = checkoutForm.querySelectorAll(`[name="${key}"]`);
                if (!inputs.length) continue;

                const type = inputs[0].type;
                if (type === 'radio') {
                    const el = checkoutForm.querySelector(`[name="${key}"][value="${value}"]`);
                    if (el && !el.checked) {
                        el.checked = true;
                        el.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                        restoredCount++;
                    }
                } else if (type === 'checkbox') {
                    // Xử lý nếu có checkbox (hiện tại form ít dùng checkbox mảng)
                } else {
                    let valToRestore = value;
                    if (inputs[0].type === 'date') {
                        const minVal = inputs[0].getAttribute('min');
                        const maxVal = inputs[0].getAttribute('max');
                        if (minVal && valToRestore < minVal) {
                            valToRestore = minVal;
                        }
                        if (maxVal && valToRestore > maxVal) {
                            valToRestore = maxVal;
                        }
                    }
                    if (inputs[0].value !== valToRestore) {
                        inputs[0].value = valToRestore;
                        inputs[0].dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                        inputs[0].dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                        restoredCount++;
                    }
                }
            }

            isRestoring = false;

            if (restoredCount > 0) {
                if (typeof updateTotalDisplay === 'function') updateTotalDisplay();

                if (typeof toastr !== 'undefined') {
                    toastr.info('Tiến trình đặt chỗ của bạn đã được khôi phục tự động.', 'Thông báo');
                } else {
                    const toastDiv = document.createElement('div');
                    toastDiv.className = 'alert alert-info position-fixed bottom-0 end-0 m-3 shadow-lg';
                    toastDiv.style.zIndex = 9999;
                    toastDiv.innerHTML =
                        '<i class="bi bi-info-circle me-2"></i>Tiến trình đặt chỗ của bạn đã được khôi phục.';
                    document.body.appendChild(toastDiv);
                    setTimeout(() => toastDiv.remove(), 4000);
                }
            }
        } catch (e) {
            console.error('Lỗi khi khôi phục tiến trình:', e);
            isRestoring = false;
        }
    }

    checkoutForm.addEventListener('input', saveProgress);
    checkoutForm.addEventListener('change', saveProgress);
    checkoutForm.addEventListener('click', function(e) {
        if (e.target.closest('.btn-ticket-qty-minus') || e.target.closest('.btn-ticket-qty-plus') ||
            e.target.closest('.btn-addon-qty-minus') || e.target.closest('.btn-addon-qty-plus')) {
            setTimeout(saveProgress, 100);
        }
    });

    checkoutForm.addEventListener('submit', function(e) {
        const allInputs = checkoutForm.querySelectorAll('input, select, textarea');
        let firstInvalid = null;
        for (const input of allInputs) {
            if (input.checkValidity && !input.checkValidity()) {
                if (!firstInvalid) {
                    firstInvalid = input;
                }
            }
        }

        if (firstInvalid) {
            e.preventDefault();
            const panel = firstInvalid.closest('.wizard-panel');
            if (panel) {
                const panelId = panel.id.replace('step-panel-', '');
                document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
                panel.classList.add('active');
                document.querySelectorAll('.wizard-step').forEach(s => {
                    const stepNum = parseInt(s.id.replace('step-nav-', ''));
                    s.classList.remove('active');
                    if (stepNum < panelId) s.classList.add('completed');
                    if (stepNum == panelId) s.classList.add('active');
                });
            }
            setTimeout(() => {
                firstInvalid.focus();
                if (typeof firstInvalid.reportValidity === 'function') {
                    firstInvalid.reportValidity();
                }
            }, 100);
            return false;
        }

        localStorage.removeItem(DRAFT_KEY);
    });

    restoreProgress();
    
    // Enforce single room logic for 1 pax after restore
    if (parseInt("{{ $adults }}") === 1) {
        const srInput = document.getElementById('single_rooms_count');
        if (srInput) {
            srInput.value = 1;
            srInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    // Seat hold countdown timer
    let remainingSeconds = {{ $remainingSeconds ?? 300 }};
    const timerEl = document.getElementById('seatHoldTimer');
    const updateTimer = () => {
        if (remainingSeconds <= 0) {
            timerEl.textContent = "00:00";
            alert('Phiên giữ chỗ của bạn đã hết hạn do quá thời gian quy định (5 phút). Vui lòng đặt lại.');
            window.location.href = "{{ route('frontend.tours.show', $schedule->tour->slug) }}";
            return;
        }

        const m = Math.floor(remainingSeconds / 60).toString().padStart(2, '0');
        const s = (remainingSeconds % 60).toString().padStart(2, '0');
        timerEl.textContent = m + ':' + s;

        if (remainingSeconds < 60) {
            timerEl.classList.remove('text-danger');
            timerEl.classList.add('text-warning', 'blink_me'); // Option to add blink effect if desired
        }
        remainingSeconds--;
        setTimeout(updateTimer, 1000);
    };
    updateTimer();
    function checkEmailMatch() {
        const email = document.getElementById("customer_email")?.value;
        const confirm = document.getElementById("customer_email_confirmation");
        const error = document.getElementById("email_match_error");
        
        if (confirm && error) {
            if (confirm.value && email !== confirm.value) {
                confirm.classList.add("is-invalid");
                error.style.display = "block";
                confirm.setCustomValidity("Email nhập lại không khớp!");
            } else {
                confirm.classList.remove("is-invalid");
                error.style.display = "none";
                confirm.setCustomValidity("");
            }
        }
    }
    
    document.getElementById("customer_email")?.addEventListener("input", checkEmailMatch);
</script>
<style>
    .blink_me {
        animation: blinker 1s linear infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const emailInput = document.getElementById("customer_email");
        const banner = document.getElementById("email_exists_banner");
        let timeout = null;
        
        function validateEmailField() {
            clearTimeout(timeout);
            const email = emailInput.value.trim();
            
            if (!email || !email.includes("@") || !email.includes(".")) {
                banner.classList.add("d-none");
                document.getElementById("email_suggestion_banner").classList.add("d-none");
                emailInput.setCustomValidity("");
                emailInput.classList.remove("is-invalid");
                return;
            }
            
            // Block typos logic
            const parts = email.split("@");
            if (parts.length === 2) {
                const domain = parts[1].toLowerCase();
                let suggestion = null;
                // Strict block for bad domains
                const blockedDomains = ["gmial.com", "gmal.com", "gamil.com", "gmail.con", "yaho.com", "yahoo.con", "hotmal.com", "hotmail.con"];
                
                if (blockedDomains.includes(domain)) {
                    emailInput.setCustomValidity("Tên miền email không hợp lệ. Vui lòng kiểm tra lại.");
                    emailInput.classList.add("is-invalid");
                    
                    if (domain.includes("gm")) suggestion = "gmail.com";
                    else if (domain.includes("yah")) suggestion = "yahoo.com";
                    else if (domain.includes("hot")) suggestion = "hotmail.com";
                } else {
                    emailInput.setCustomValidity("");
                    emailInput.classList.remove("is-invalid");
                }
                
                const suggBanner = document.getElementById("email_suggestion_banner");
                if (suggestion) {
                    const fullSugg = parts[0] + "@" + suggestion;
                    document.getElementById("email_suggestion_text").innerText = fullSugg;
                    suggBanner.classList.remove("d-none");
                    document.getElementById("email_suggestion_btn").onclick = function(e) {
                        e.preventDefault();
                        emailInput.value = fullSugg;
                        suggBanner.classList.add("d-none");
                        emailInput.setCustomValidity("");
                        emailInput.classList.remove("is-invalid");
                        emailInput.dispatchEvent(new Event("input"));
                    };
                } else {
                    suggBanner.classList.add("d-none");
                }
            }
            
            timeout = setTimeout(() => {
                fetch(`/api/check-email?email=${encodeURIComponent(email)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists && !"{{ $user ? 'logged_in' : '' }}") {
                            banner.classList.remove("d-none");
                        } else {
                            banner.classList.add("d-none");
                        }
                    })
                    .catch(err => console.error(err));
            }, 500);
        }

        if (emailInput && banner) {
            emailInput.addEventListener("input", validateEmailField);
            // Run on load in case of old() values
            if (emailInput.value) {
                validateEmailField();
            }
        }
    });
</script>
@endsection