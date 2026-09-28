@extends('layouts.master')

@section('title', 'Chi tiết đơn - Travel Wonder')

@section('content')
<style>
    .detail-card { border-radius: 20px; border: 1px solid #edf2f7; background: white; overflow: hidden; margin-bottom: 24px; }
    .detail-card-header { background: rgba(0,124,232,0.04); border-bottom: 1px solid #edf2f7; padding: 14px 24px; font-weight: 700; color: #374151; }
    .detail-card-body { padding: 24px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
    .info-row:last-child { border-bottom: none; }
    .status-badge { padding: 6px 14px; border-radius: 30px; font-weight: 600; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 5px; }
    .s-pending   { background: rgba(245,166,35,0.12); color: #d97706; }
    .s-confirmed { background: rgba(25,135,84,0.12);  color: #198754; }
    .s-paid      { background: rgba(13,110,253,0.12); color: #0d6efd; }
    .s-cancelled { background: rgba(220,53,69,0.12);  color: #dc3545; }
    .s-completed { background: rgba(108,117,125,0.12);color: #6c757d; }
    .star-btn { background: none; border: none; font-size: 2rem; cursor: pointer; color: #d1d5db; transition: color 0.15s; padding: 0 2px; line-height: 1; }
    .star-btn.lit { color: #f59e0b; }
    .step-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>

<div class="container py-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4 reveal-up">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-primary fw-500">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.bookings') }}" class="text-decoration-none text-primary fw-500">Đơn đặt tour</a></li>
            <li class="breadcrumb-item active fw-bold">{{ $booking->code }}</li>
        </ol>
    </nav>

    @php
        $status  = strtolower($booking->booking_status ?? 'pending');
        $sMap = [
            'pending'   => ['cls'=>'s-pending',   'label'=>'Chờ xác nhận', 'icon'=>'bi-clock'],
            'confirmed' => ['cls'=>'s-confirmed',  'label'=>'Đã xác nhận',  'icon'=>'bi-check-circle'],
            'paid'      => ['cls'=>'s-paid',       'label'=>'Đã thanh toán','icon'=>'bi-credit-card'],
            'cancelled' => ['cls'=>'s-cancelled',  'label'=>'Đã hủy',       'icon'=>'bi-x-circle'],
            'completed' => ['cls'=>'s-completed',  'label'=>'Hoàn thành',   'icon'=>'bi-patch-check'],
        ];
        $si   = $sMap[$status] ?? ['cls'=>'s-pending','label'=>ucfirst($status),'icon'=>'bi-circle'];
        $tour = $booking->tour_schedule->tour ?? null;
        $pImg = $tour?->tour_images->where('is_primary',1)->first();
        $img  = $pImg?->image_url ?? 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';
    @endphp

    {{-- Tiêu đề --}}
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start mb-4 reveal-up">
        <div>
            <h2 class="fw-bold text-dark mb-1">Chi tiết đơn đặt</h2>
            <span class="text-muted small">Mã: <strong>{{ $booking->code }}</strong> · {{ $booking->created_at->format('H:i d/m/Y') }}</span>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">

        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Thông tin tour --}}
            <div class="detail-card reveal-up">
                <div class="detail-card-header"><i class="bi bi-briefcase me-2 text-primary"></i>Thông tin tour</div>
                <div class="detail-card-body">
                    <div class="d-flex gap-3">
                        <img src="{{ $img }}" class="rounded-3 flex-shrink-0"
                            style="width:110px;height:75px;object-fit:cover;" alt="tour">
                        <div>
                            <h5 class="fw-bold text-dark mb-2">{{ $tour?->title ?? 'Không tìm thấy tour' }}</h5>
                            <div class="d-flex flex-wrap gap-3 text-muted small">
                                @if($tour?->destination)
                                    <span><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $tour->destination->name }}</span>
                                @endif
                                @if($tour)
                                    <span><i class="bi bi-clock me-1 text-warning"></i>{{ $tour->duration_days }} ngày{{ $tour->duration_nights > 0 ? ' ' . $tour->duration_nights . ' đêm' : '' }}</span>
                                @endif
                                @if($booking->tour_schedule)
                                    <span><i class="bi bi-calendar-event me-1 text-success"></i>{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}@if($tour?->departure_time) ({{ \Carbon\Carbon::parse($tour->departure_time)->format('H\hi') }})@endif</span>
                                    <span><i class="bi bi-calendar-check me-1 text-info"></i>Về: {{ \Carbon\Carbon::parse($booking->tour_schedule->return_date)->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thông tin đặt chỗ --}}
<div class="detail-card reveal-up">
    <div class="detail-card-header">
        <i class="bi bi-clipboard-check me-2 text-primary"></i>
        Chi tiết đặt chỗ
    </div>

    <div class="detail-card-body">
        {{-- Người lớn --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-people me-2"></i>
                Người lớn
            </span>

            <span class="fw-bold">
                {{ $booking->adults_count }} người
            </span>
        </div>

        {{-- Trẻ em --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-person-badge me-2"></i>
                Trẻ em
            </span>

            <span class="fw-bold">
                {{ $booking->children_count }} người
            </span>
        </div>

        @php
            $bAcc = $booking->booking_accommodations->first();
        @endphp
        @if($bAcc)
        {{-- Lưu trú --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-building me-2"></i>
                Lưu trú
            </span>
            <div class="text-end" style="max-width: 65%;">
                <span class="fw-bold">{{ $bAcc->accommodation_name_snapshot }}</span>
                <br><span class="small text-muted">{{ $bAcc->room_type->accommodation->address ?? '' }}</span>
                <br><span class="badge bg-secondary">{{ $bAcc->room_type_name_snapshot }}</span>
                @if($bAcc->single_rooms_count > 0 || $bAcc->extra_bed_qty > 0)
                    <br><span class="small text-muted">({{ $bAcc->single_rooms_count }} phòng, {{ $bAcc->extra_bed_qty }} giường phụ)</span>
                @endif
            </div>
        </div>
        @elseif($booking->accommodation_id && $booking->accommodation)
        {{-- Lưu trú cũ --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-building me-2"></i>
                Lưu trú
            </span>
            <div class="text-end" style="max-width: 65%;">
                <span class="fw-bold">{{ $booking->accommodation->name }}</span>
                <br><span class="small text-muted">{{ $booking->accommodation->address }}</span>
            </div>
        </div>
        @endif

        {{-- Điểm tập kết --}}
        <div class="info-row">
            <span class="text-muted small">
                <i class="bi bi-geo-alt-fill text-success me-2"></i>
                Điểm tập kết
            </span>

            <span class="fw-bold text-end"
                  style="max-width:65%; word-break:break-word;">
                {{ $booking->meeting_point
                    ?: ($booking->tour_schedule?->tour?->meeting_point ?? 'Chưa cập nhật') }}
            </span>
        </div>

        {{-- Giảm giá --}}
        @if($booking->discount_amount)
            <div class="info-row">
                <span class="text-muted small">
                    <i class="bi bi-tags me-2"></i>
                    Giảm giá
                </span>

                <span class="fw-bold text-success">
                    -{{ number_format($booking->discount_amount, 0, ',', '.') }}₫
                </span>
            </div>
        @endif

        {{-- Tổng thanh toán --}}
        <div class="info-row">
            <span class="fw-bold text-dark fs-6">
                <i class="bi bi-wallet2 me-2"></i>
                Tổng giá trị tour
            </span>

            <span class="fw-bold text-danger" style="font-size:1.3rem;">
                {{ number_format($booking->total_price, 0, ',', '.') }}₫
            </span>
        </div>

        @if($booking->payment_status === 'paid_30')
            <div class="info-row border-top pt-2">
                <span class="text-success small fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Đã cọc 30%
                </span>
                <span class="fw-bold text-success">
                    -{{ number_format($booking->paid_amount, 0, ',', '.') }}₫
                </span>
            </div>
            <div class="info-row">
                <span class="text-danger small fw-bold">
                    <i class="bi bi-exclamation-circle me-1"></i> Còn lại cần thanh toán (70%)
                </span>
                <span class="fw-bold text-danger fs-6">
                    {{ number_format($booking->remaining_amount, 0, ',', '.') }}₫
                </span>
            </div>
        @endif
    </div>
</div>

            {{-- Khối Thanh Toán 70% Còn Lại --}}
            @if($booking->payment_status === 'paid_30' && !in_array($booking->booking_status, ['cancelled', 'completed']))
                @php
                    $remainingAmount = $booking->remaining_amount;
                    $bankId = 'BIDV';
                    $accountNo = '0818802032';
                    $template = 'compact2';
                    $accountName = 'TRavelWondel';
                    $description = "TW{$booking->id}";
                    $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount=".round($remainingAmount)."&addInfo=".urlencode($description)."&accountName=".urlencode($accountName);
                @endphp

                <div id="pay70Section" class="detail-card reveal-up border border-primary border-2 shadow-sm">
                    <div class="detail-card-header bg-primary text-white d-flex align-items-center justify-content-between">
                        <div class="fw-bold">
                            <i class="bi bi-credit-card-2-front me-2"></i>Thanh Toán 70% Còn Lại ({{ number_format($remainingAmount, 0, ',', '.') }}₫)
                        </div>
                        <span class="badge bg-warning text-dark">Đã cọc 30%</span>
                    </div>
                    <div class="detail-card-body">
                        <p class="text-muted small mb-3">Vui lòng chọn hình thức thanh toán 70% còn lại trước ngày khởi hành để hoàn tất đơn tour của bạn:</p>

                        <ul class="nav nav-pills nav-justified mb-3 gap-2" id="pay70Tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill py-2 fw-bold" id="qr70-tab" data-bs-toggle="pill" data-bs-target="#qr70-pane" type="button" role="tab">
                                    <i class="bi bi-qr-code-scan me-1"></i> Quét mã VietQR
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill py-2 fw-bold" id="vnpay70-tab" data-bs-toggle="pill" data-bs-target="#vnpay70-pane" type="button" role="tab">
                                    <i class="bi bi-credit-card me-1"></i> Thanh toán Cổng VNPay
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pay70TabContent">
                            <!-- VIETQR TAB -->
                            <div class="tab-pane fade show active text-center" id="qr70-pane" role="tabpanel">
                                <div class="bg-light p-3 rounded-4 mx-auto" style="max-width: 420px;">
                                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-qr-code-scan me-1"></i>Chuyển khoản VietQR Tự Động</h6>
                                    <img src="{{ $qrUrl }}" alt="VietQR 70%" class="img-fluid rounded border bg-white p-2 mb-3 shadow-sm" style="max-width: 230px;">

                                    <div class="text-start bg-white p-3 rounded border small shadow-sm">
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                            <span class="text-muted">Ngân hàng:</span>
                                            <strong>{{ $bankId }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                            <span class="text-muted">Số tài khoản:</span>
                                            <strong>{{ $accountNo }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                            <span class="text-muted">Chủ tài khoản:</span>
                                            <strong>{{ $accountName }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                            <span class="text-muted">Số tiền 70% còn lại:</span>
                                            <strong class="text-danger fs-6">{{ number_format($remainingAmount, 0, ',', '.') }}₫</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-1">
                                            <span class="text-muted">Nội dung chuyển khoản:</span>
                                            <strong class="text-primary font-monospace bg-light px-2 py-1 rounded border">{{ $description }}</strong>
                                        </div>
                                    </div>
                                    <div class="alert alert-info small text-start mt-2 mb-0">
                                        <i class="bi bi-info-circle me-1"></i> Giữ nguyên nội dung <code>{{ $description }}</code> để hệ thống tự động duyệt ngay sau khi nhận được tiền.
                                    </div>
                                </div>
                            </div>

                            <!-- VNPAY TAB -->
                            <div class="tab-pane fade text-center py-4" id="vnpay70-pane" role="tabpanel">
                                <div class="bg-light p-4 rounded-4 mx-auto" style="max-width: 420px;">
                                    <i class="bi bi-shield-check text-success" style="font-size:3rem;"></i>
                                    <h6 class="fw-bold mt-2 mb-3">Thanh toán an toàn qua Cổng VNPAY</h6>
                                    <p class="text-muted small mb-3">Chấp nhận thẻ ATM nội địa, QR Banking của tất cả các ngân hàng, Visa/Mastercard.</p>
                                    <a href="{{ route('frontend.bookings.pay_vnpay', $booking->id) }}" class="btn btn-primary rounded-pill px-4 py-2 w-100 fw-bold">
                                        <i class="bi bi-box-arrow-up-right me-2"></i>Thanh toán {{ number_format($remainingAmount, 0, ',', '.') }}₫ qua VNPAY
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Hành khách --}}
            <div class="detail-card reveal-up">
                <div class="detail-card-header d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-person-lines-fill me-2 text-primary"></i>Hành khách</div>
                    @if(in_array($status, ['pending', 'paid', 'confirmed']))
                        <a href="{{ route('frontend.bookings.passengers', $booking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-pencil-square me-1"></i>Bổ sung / Sửa thông tin
                        </a>
                    @endif
                </div>
                <div class="detail-card-body">
                    @foreach($booking->booking_passengers as $p)
                    <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom':'' }}">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                            style="width:40px;height:40px;"><i class="bi bi-person text-primary"></i></div>
                        <div>
                            <div class="fw-bold text-dark">{{ $p->full_name }}</div>
                            <div class="text-muted small">
                                {{ $p->identity_number }} ·
                                {{ $p->gender==='male'?'Nam':($p->gender==='female'?'Nữ':'Khác') }} ·
                                {{ \Carbon\Carbon::parse($p->date_of_birth)->format('d/m/Y') }}
                            </div>
                        </div>
                        <span class="ms-auto badge bg-light text-muted rounded-3 small">{{ $p->passenger_type==='adult'?'Người lớn':'Trẻ em' }}</span>
                    </div>
                    @endforeach
                    @if($booking->booking_passengers->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-people" style="font-size:2rem;opacity:0.5;"></i>
                            <div class="mt-2">Bạn chưa cung cấp thông tin hành khách.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dịch vụ đính kèm & Vé tham quan --}}
            @if($booking->ticket_bookings->count() > 0 || $booking->addons->count() > 0)
            <div class="detail-card reveal-up">
                <div class="detail-card-header"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Dịch vụ đính kèm & Vé tham quan</div>
                <div class="detail-card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($booking->ticket_bookings as $tb)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <div>
                                    <div class="fw-bold">{{ $tb->ticket_option->ticket->title ?? '' }} - {{ $tb->ticket_option->name ?? 'Vé tham quan' }}</div>
                                    <div class="text-muted small">Số lượng: {{ $tb->quantity }}</div>
                                </div>
                                <div class="fw-bold text-dark">{{ number_format($tb->total_price, 0, ',', '.') }}₫</div>
                            </li>
                        @endforeach
                        @foreach($booking->addons as $addon)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <div>
                                    <div class="fw-bold">{{ $addon->pivot->addon_name }}</div>
                                    <div class="text-muted small">Số lượng: {{ $addon->pivot->quantity }}</div>
                                </div>
                                <div class="fw-bold text-dark">{{ number_format($addon->pivot->price * $addon->pivot->quantity, 0, ',', '.') }}₫</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Đánh giá --}}
            @if($booking->tour_status === 'completed' && $tour)
            <div class="detail-card reveal-up">
                <div class="detail-card-header"><i class="bi bi-star me-2 text-warning"></i>Đánh giá chuyến đi</div>
                <div class="detail-card-body">
                    @if($existingReview)
                        <div class="p-4 rounded-4" style="background:rgba(245,166,35,0.06);border:1px solid rgba(245,166,35,0.2);">
                            <div class="d-flex align-items-center mb-2 gap-1">
                                @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$existingReview->rating?'-fill':'' }} text-warning" style="font-size:1.2rem;"></i>@endfor
                                <span class="ms-2 text-muted small">Đánh giá chung của bạn</span>
                            </div>

                            @if($existingReview->guide_rating)
                            <div class="d-flex align-items-center mb-2 gap-1 mt-2 border-top pt-2">
                                @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$existingReview->guide_rating?'-fill':'' }} text-primary" style="font-size:1.2rem;"></i>@endfor
                                <span class="ms-2 text-muted small">Đánh giá Hướng dẫn viên ({{ $existingReview->tour_guide->name ?? 'HDV' }})</span>
                            </div>
                            @endif

                            @if($existingReview->comment)<p class="text-dark lh-lg mb-1 mt-3">{{ $existingReview->comment }}</p>@endif
                            <span class="text-muted small">{{ $existingReview->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @else
                        <form method="POST" action="{{ route('user.reviews.store') }}">
                            @csrf
                            <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                            <div class="mb-3">
                                <label class="form-label fw-600 text-dark mb-2">Đánh giá chuyến đi</label>
                                <div class="d-flex gap-1 starRow" data-input="ratingVal">
                                    @for($i=1;$i<=5;$i++)
                                    <button type="button" class="star-btn" data-v="{{ $i }}"><i class="bi bi-star-fill"></i></button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingVal" value="0" required>
                            </div>

                            @if($booking->tour_schedule && $booking->tour_schedule->schedule_guides->count() > 0)
                            <div class="mb-3 p-3 bg-light rounded border">
                                @php
                                    $mainGuide = $booking->tour_schedule->schedule_guides->where('is_backup', false)->first();
                                    $guide = $mainGuide ? $mainGuide->tour_guide : $booking->tour_schedule->schedule_guides->first()->tour_guide;
                                @endphp
                                <label class="form-label fw-600 text-dark mb-2">Đánh giá Hướng dẫn viên ({{ $guide->name }})</label>
                                <input type="hidden" name="guide_id" value="{{ $guide->id }}">
                                <div class="d-flex gap-1 starRow text-primary" data-input="guideRatingVal">
                                    @for($i=1;$i<=5;$i++)
                                    <button type="button" class="star-btn" data-v="{{ $i }}"><i class="bi bi-star-fill"></i></button>
                                    @endfor
                                </div>
                                <input type="hidden" name="guide_rating" id="guideRatingVal" value="0">
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-600 text-dark">Nhận xét chi tiết <span class="text-muted fw-normal">(không bắt buộc)</span></label>
                                <textarea class="form-control" name="comment" rows="3"
                                    style="border-radius:12px;resize:none;"
                                    placeholder="Chia sẻ trải nghiệm chuyến đi của bạn..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-register-premium px-4 py-2 mt-2">
                                <i class="bi bi-send me-2"></i>Gửi Đánh giá
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar Tiến trình đơn hàng --}}
        <div class="col-lg-4">
            <div class="detail-card reveal-up" style="position:sticky;top:100px;">
                <div class="detail-card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Tiến trình đơn hàng</span>
                    <span class="badge bg-light text-primary border rounded-pill small">
                        <span class="spinner-grow spinner-grow-sm text-primary me-1" style="width:0.45rem;height:0.45rem;" role="status"></span> Realtime
                    </span>
                </div>
                <div class="detail-card-body">
                    @php
                        $pStatus = $booking->payment_status;
                        $bStatus = $booking->booking_status;
                        $tStatus = $booking->tour_status;

                        $isCancelled = in_array($bStatus, ['cancelled']) || in_array($tStatus, ['cancelled_by_customer', 'cancelled_by_admin']) || $pStatus === 'failed';

                        $isDeparted = \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->startOfDay()->isPast()
                            || in_array($tStatus, ['in_progress', 'checking_in', 'completed', 'closed']);

                        $step1Done = true; // Tạo đơn thành công

                        $step2Done = in_array($pStatus, ['paid_30', 'paid_100', 'paid']);
                        $step2Label = $pStatus === 'paid_30' ? 'Đã cọc 30%' : ($pStatus === 'paid_100' || $pStatus === 'paid' ? 'Đã thanh toán 100%' : 'Chờ thanh toán');

                        $step3Done = $step2Done || in_array($bStatus, ['confirmed', 'paid', 'completed']);

                        $step4Done = in_array($tStatus, ['checking_in', 'in_progress', 'completed', 'closed']);
                        $step4Label = $tStatus === 'checking_in' 
                            ? ($booking->current_checkin_step ? 'Check-in: ' . $booking->current_checkin_step : 'Đang check-in') 
                            : ($tStatus === 'in_progress' ? 'Đang đi tour' : 'Chuyến đi tour');

                        $step5Done = in_array($tStatus, ['completed', 'closed']) || $bStatus === 'completed';

                        if ($step5Done) { $currentStep = 5; }
                        elseif ($step4Done) { $currentStep = 4; }
                        elseif ($step3Done) { $currentStep = 3; }
                        elseif ($step2Done) { $currentStep = 2; }
                        else { $currentStep = 1; }
                    @endphp

                    @if($isCancelled)
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-x-circle-fill text-danger" style="font-size:3.5rem;"></i>
                            </div>
                            <h5 class="fw-bold text-danger mb-1">Đơn hàng đã bị hủy</h5>
                            <p class="text-muted small mb-0">{{ $booking->cancel_reason ?? 'Đơn hàng đã quá hạn thanh toán hoặc bị hủy bởi hệ thống.' }}</p>
                        </div>
                    @else
                        <div class="order-steps-vertical">
                            {{-- Bước 1 --}}
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <div class="step-dot rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white bg-primary" style="width:30px;height:30px;">
                                    <i class="bi bi-check-lg fw-bold" style="font-size:0.85rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">Đặt tour thành công</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $booking->created_at->format('H:i d/m/Y') }}</div>
                                </div>
                                @if($currentStep == 1)<span class="ms-auto badge bg-primary text-white rounded-pill px-2 py-1 small">Hiện tại</span>@endif
                            </div>

                            {{-- Bước 2 --}}
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <div class="step-dot rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $step2Done ? 'bg-primary text-white' : ($currentStep == 2 ? 'bg-warning text-dark' : 'bg-light text-muted border') }}" style="width:30px;height:30px;">
                                    @if($step2Done)<i class="bi bi-check-lg fw-bold" style="font-size:0.85rem;"></i>
                                    @elseif($currentStep == 2)<i class="bi bi-clock-history" style="font-size:0.85rem;"></i>
                                    @else<i class="bi bi-circle" style="font-size:0.75rem;"></i>@endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $step2Label }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        @if($pStatus === 'paid_30') Đã cọc 30%, còn 70%
                                        @elseif($pStatus === 'paid_100' || $pStatus === 'paid') Đã nhận 100% tiền tour
                                        @else Vui lòng hoàn tất cọc/tiền @endif
                                    </div>
                                </div>
                                @if($currentStep == 2)<span class="ms-auto badge bg-warning text-dark rounded-pill px-2 py-1 small fw-bold">Hiện tại</span>@endif
                            </div>

                            {{-- Bước 3 --}}
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <div class="step-dot rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $step3Done ? 'bg-primary text-white' : ($currentStep == 3 ? 'bg-warning text-dark' : 'bg-light text-muted border') }}" style="width:30px;height:30px;">
                                    @if($step3Done)<i class="bi bi-check-lg fw-bold" style="font-size:0.85rem;"></i>
                                    @elseif($currentStep == 3)<i class="bi bi-clock-history" style="font-size:0.85rem;"></i>
                                    @else<i class="bi bi-circle" style="font-size:0.75rem;"></i>@endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">Xác nhận giữ chỗ</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Đã bảo lưu vị trí trên tour</div>
                                </div>
                                @if($currentStep == 3)<span class="ms-auto badge bg-primary text-white rounded-pill px-2 py-1 small">Hiện tại</span>@endif
                            </div>

                            {{-- Bước 4 --}}
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <div class="step-dot rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $step4Done ? 'bg-primary text-white' : ($currentStep == 4 ? 'bg-info text-white' : 'bg-light text-muted border') }}" style="width:30px;height:30px;">
                                    @if($step4Done)<i class="bi bi-check-lg fw-bold" style="font-size:0.85rem;"></i>
                                    @elseif($currentStep == 4)<i class="bi bi-geo-alt-fill" style="font-size:0.85rem;"></i>
                                    @else<i class="bi bi-circle" style="font-size:0.75rem;"></i>@endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $step4Label }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        @if($tStatus === 'checking_in')
                                            @if($booking->current_checkin_step)
                                                Đoàn đang có mặt tại <strong>{{ $booking->current_checkin_step }}</strong>
                                            @else
                                                HDV đang mở Check-in điểm tập trung
                                            @endif
                                        @elseif($tStatus === 'in_progress') Đoàn đang di chuyển/tham quan
                                        @elseif(in_array($tStatus, ['completed', 'closed']))
                                            Đã đi qua các điểm tham quan
                                        @else Ngày đi: {{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }} @endif
                                    </div>
                                </div>
                                @if($currentStep == 4)<span class="ms-auto badge bg-info text-dark rounded-pill px-2 py-1 small fw-bold">Hiện tại</span>@endif
                            </div>

                            {{-- Bước 5 --}}
                            <div class="d-flex align-items-center gap-3 py-2">
                                <div class="step-dot rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $step5Done ? 'bg-success text-white' : 'bg-light text-muted border' }}" style="width:30px;height:30px;">
                                    @if($step5Done)<i class="bi bi-trophy-fill" style="font-size:0.85rem;"></i>
                                    @else<i class="bi bi-circle" style="font-size:0.75rem;"></i>@endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">Hoàn thành chuyến đi</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Chuyến đi đã kết thúc tốt đẹp</div>
                                </div>
                                @if($currentStep == 5)<span class="ms-auto badge bg-success text-white rounded-pill px-2 py-1 small">Hoàn tất</span>@endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-3 d-grid gap-2">
                        @if(
    !$isCancelled
    && in_array($booking->payment_status, ['paid_30', 'paid_100'], true)
)
    <div class="mb-2">

        @if(($booking->invoice_status ?? 'none') === 'requested')
            <button type="button"
                    class="btn btn-warning w-100"
                    disabled>
                <i class="bi bi-clock-history me-1"></i>
                Yêu cầu hóa đơn đang chờ xử lý
            </button>

        @elseif(($booking->invoice_status ?? 'none') === 'sent')
            <button type="button"
                    class="btn btn-success w-100"
                    disabled>
                <i class="bi bi-check-circle-fill me-1"></i>
                Hóa đơn đã được gửi
            </button>

        @else
            <button type="button"
                    class="btn btn-primary w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#requestInvoiceModal">
                <i class="bi bi-receipt-cutoff me-1"></i>
                Yêu cầu xuất hóa đơn
            </button>
        @endif

    </div>
@endif

@if(!$isCancelled && !$isDeparted)
    <div class="mb-3">
        <button type="button"
                class="btn btn-outline-danger w-100 rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#cancelBookingModal">
            <i class="bi bi-x-circle me-1"></i>
            Yêu cầu hủy đơn hàng
        </button>
    </div>
@endif

@if($booking->refund_request)
    <div class="alert alert-info mt-3 mb-3 text-start small border-0 bg-info bg-opacity-10 rounded-4">
        <h6 class="fw-bold mb-1 text-info"><i class="bi bi-arrow-counterclockwise"></i> Trạng thái hoàn tiền:</h6>
        <p class="mb-0">
            Trạng thái: 
            @if($booking->refund_request->status === 'pending')
                <span class="badge bg-warning text-dark">Đang xử lý</span>
            @elseif($booking->refund_request->status === 'completed')
                <span class="badge bg-success">Đã hoàn tiền</span>
            @else
                <span class="badge bg-danger">Từ chối</span>
            @endif
            <br>
            Số tiền hoàn: <strong>{{ number_format($booking->refund_request->amount) }} VNĐ</strong>
        </p>
    </div>
@endif
                        <a href="{{ route('user.bookings') }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="bi bi-arrow-left me-2"></i>Danh sách đơn
                        </a>
                        @if($tour)
                        <a href="{{ route('frontend.tours.show',$tour->slug) }}" class="btn btn-outline-primary rounded-pill">
                            <i class="bi bi-eye me-2"></i>Xem thông tin tour
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FLOATING ADMIN DEMO TOOLBAR -->
@if((Auth::check() || config('app.debug')) && $booking->payment_status !== 'paid_100')
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
    const initialPaymentStatus = "{{ $booking->payment_status }}";
    const initialBookingStatus = "{{ $booking->booking_status }}";
    const initialTourStatus = "{{ $booking->tour_status }}";
    const csrfToken = "{{ csrf_token() }}";

    // Realtime Status Polling (Checks payment, booking & tour status)
    function checkStatus() {
        fetch(`/tours/booking-status/${bookingId}`)
            .then(res => res.json())
            .then(data => {
                if (
                    data.payment_status !== initialPaymentStatus ||
                    data.booking_status !== initialBookingStatus ||
                    data.tour_status !== initialTourStatus
                ) {
                    location.reload();
                }
            })
            .catch(err => console.log('Polling status error', err));
    }

    setInterval(checkStatus, 3000);

    var starRows = document.querySelectorAll('.starRow');

    starRows.forEach(function(row) {
        var stars = row.querySelectorAll('.star-btn');
        var inputId = row.dataset.input;
        var inp = document.getElementById(inputId);

        if (!stars.length) return;

        function paint(val) {
            stars.forEach(function(b){ b.classList.toggle('lit', parseInt(b.dataset.v) <= val); });
            if (inp) inp.value = val;
        }

        stars.forEach(function(b){
            b.addEventListener('click',    function(){ paint(parseInt(b.dataset.v)); });
            b.addEventListener('mouseover',function(){ paint(parseInt(b.dataset.v)); });
        });

        row.addEventListener('mouseleave', function(){ paint(parseInt(inp.value)||0); });
    });

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
@if(
    ($booking->invoice_status ?? 'none') === 'none'
    && in_array($booking->payment_status, ['paid_30', 'paid_100'], true)
)
    <div class="modal fade"
         id="requestInvoiceModal"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form action="{{ route('frontend.bookings.invoice.request', $booking->id) }}"
                      method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt-cutoff text-primary me-1"></i>
                            Yêu cầu xuất hóa đơn
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">
                                Mã đơn tour
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="BOOK-{{ $booking->created_at->format('Ymd') }}-{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Email nhận hóa đơn
                            </label>

                            <input type="email"
                                   name="invoice_email"
                                   class="form-control"
                                   value="{{ old('invoice_email', auth()->user()->email ?? '') }}"
                                   required>

                            <div class="form-text">
                                Hóa đơn PDF sẽ được gửi đến email này.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal">
                            Hủy
                        </button>

                        <button type="submit"
                                class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>
                            Gửi yêu cầu
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endif

@if(!$isCancelled && !$isDeparted)
    <div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('user.bookings.cancel', $booking->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Yêu cầu hủy đơn hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn hủy đơn hàng <strong>#{{ $booking->code }}</strong> không?</p>
                        
                        @php
                            $refundService = app(\App\Services\RefundService::class);
                            $refundCalc = $refundService->calculateRefund($booking);
                        @endphp

                        @if($booking->paid_amount > 0)
                            <div class="alert alert-warning mb-3">
                                <strong>Chính sách hoàn tiền:</strong><br>
                                - Trạng thái: {{ $refundCalc['reason'] }}<br>
                                - Số tiền đã thanh toán: {{ number_format($booking->paid_amount) }} VNĐ<br>
                                - Khấu trừ bảo hiểm (không hoàn lại): {{ number_format($refundCalc['insurance_cost']) }} VNĐ<br>
                                - Số tiền được hoàn ({{ $refundCalc['refund_percent'] }}%): <strong class="text-danger">{{ number_format($refundCalc['refund_amount']) }} VNĐ</strong>
                            </div>

                            @if($refundCalc['is_refundable'])
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Thông tin tài khoản nhận tiền hoàn</label>
                                    <p class="small text-muted mb-2">Vui lòng cung cấp chính xác thông tin tài khoản ngân hàng của bạn để kế toán tiến hành hoàn tiền.</p>
                                    
                                    <div class="mb-2">
                                        <input type="text" name="bank_name" class="form-control" placeholder="Tên Ngân hàng (VD: Vietcombank)" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="bank_account_name" class="form-control" placeholder="Tên chủ tài khoản" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="bank_account_number" class="form-control" placeholder="Số tài khoản" required>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-secondary">
                                Đơn hàng này chưa được thanh toán nên sẽ không phát sinh hoàn tiền.
                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">Xác nhận Hủy đơn</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection
