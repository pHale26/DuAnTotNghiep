<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đặt tour - Travel Wonder</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; background-color: #f1f5f9; padding: 40px 20px; }
        .wrapper { max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; }

        /* ===== HEADER ===== */
        .header { padding: 40px 40px 32px 40px; border-bottom: 1px solid #f1f5f9; }
        .logo-box { margin-bottom: 24px; }
        .logo-text .name { font-size: 20px; font-weight: 700; color: #1e3a8a; letter-spacing: -0.5px; }
        .logo-text .tagline { font-size: 11px; color: #64748b; letter-spacing: 0.5px; text-transform: uppercase; margin-top: 2px; }
        .header-banner h1 { font-size: 24px; font-weight: 600; color: #0f172a; letter-spacing: -0.5px; margin-bottom: 6px; }
        .header-banner p { font-size: 14px; color: #64748b; }

        /* ===== BODY ===== */
        .body { padding: 32px 40px 40px 40px; }
        .greeting { font-size: 15px; margin-bottom: 8px; color: #0f172a; }
        .greeting strong { color: #1e3a8a; font-weight: 600; }
        .intro { font-size: 14px; color: #64748b; margin-bottom: 32px; }

        /* ===== ORDER META & CTA ===== */
        .order-meta { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 24px; margin-bottom: 32px; }
        .order-meta-row1 { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .order-id { font-size: 18px; font-weight: 700; color: #0f172a; letter-spacing: 0.5px; }
        .order-date { font-size: 12px; color: #64748b; margin-top: 4px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 500; letter-spacing: 0.2px; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-success { background: #dcfce7; color: #15803d; }
        
        .order-meta-pay-row { border-top: 1px solid #e2e8f0; padding-top: 16px; }
        .order-meta-pay-row p { font-size: 13px; color: #64748b; margin-bottom: 16px; line-height: 1.5; }
        .btn-pay-inline { display: inline-block; background: #2563eb; color: #ffffff !important; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; transition: background 0.15s ease; text-align: center; }
        .btn-pay-inline:hover { background: #1d4ed8; }

        /* ===== SECTIONS ===== */
        .section { margin-bottom: 32px; }
        .section-title { font-size: 14px; font-weight: 600; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #0f172a; display: block; }

        /* ===== TABLES ===== */
        .tour-info-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .tour-info-table td { padding: 10px 0; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .tour-info-table tr:last-child td { border-bottom: none; }
        .tour-info-table .t-label { color: #64748b; width: 180px; }
        .tour-info-table .t-value { color: #0f172a; }
        .tour-info-table .t-value .em { font-weight: 600; color: #1e3a8a; }

        .table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
        .table th { color: #64748b; font-weight: 500; text-align: left; padding: 10px 12px; border-bottom: 1px solid #cbd5e1; background: #f8fafc; }
        .table td { padding: 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; color: #334155; }
        .badge-adult { color: #2563eb; font-weight: 500; }
        .badge-child { color: #16a34a; font-weight: 500; }

        /* ===== ITINERARY ===== */
        .itinerary-box { padding: 4px 0; }
        .itinerary-day { margin-bottom: 14px; font-size: 14px; }
        .itinerary-day:last-child { margin-bottom: 0; }
        .itinerary-day .day-head { color: #0f172a; font-weight: 600; display: inline-block; margin-right: 4px; }
        .itinerary-day .day-detail { color: #334155; }
        .itinerary-day .day-desc { color: #64748b; font-size: 13px; margin-top: 2px; padding-left: 0; }

        /* ===== PRICE BOX ===== */
        .price-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 20px 24px; }
        .price-line { display: flex; justify-content: space-between; font-size: 13.5px; margin-bottom: 10px; color: #475569; }
        .price-line-value { font-weight: 500; color: #0f172a; }
        .price-line.discount { color: #16a34a; }
        .price-line.discount .price-line-value { color: #16a34a; }
        .price-line.total { border-top: 1px dashed #cbd5e1; padding-top: 12px; margin-top: 12px; font-size: 15px; color: #0f172a; }
        .price-line.total .price-line-label { font-weight: 600; }
        .price-line.total .price-line-value { font-weight: 700; font-size: 18px; color: #1e3a8a; }
        .price-line.paid { color: #16a34a; }
        .price-line.paid .price-line-value { color: #16a34a; font-weight: 500; }
        .price-line.remaining { border-top: 1px solid #cbd5e1; padding-top: 12px; margin-top: 12px; font-size: 14px; color: #b45309; }
        .price-line.remaining .price-line-label { font-weight: 600; }
        .price-line.remaining .price-line-value { font-weight: 700; font-size: 16px; }

        /* ===== NOTICES & SUPPORT ===== */
        .verify-box { background: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; padding: 20px; font-size: 13.5px; margin-bottom: 16px; }
        .verify-box h4 { font-size: 14px; color: #92400e; margin-bottom: 6px; font-weight: 600; }
        .verify-box p { color: #78350f; line-height: 1.5; }
        
        .notes-box { background: #fdf2f8; border: 1px solid #fce7f3; border-radius: 6px; padding: 20px; font-size: 13.5px; }
        .notes-box h4 { font-size: 14px; color: #9d174d; margin-bottom: 10px; font-weight: 600; }
        .notes-box ul { padding-left: 16px; color: #831843; }
        .notes-box li { margin-bottom: 6px; line-height: 1.5; }

        .support-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 20px; font-size: 13.5px; margin-top: 32px; color: #1e40af; }
        .support-box h4 { color: #1e3a8a; font-size: 14px; margin-bottom: 8px; font-weight: 600; }
        .support-item { margin-bottom: 4px; }
        .support-item:last-child { margin-bottom: 0; }
        .support-item .hotline { font-weight: 700; color: #2563eb; }

        /* ===== FOOTER ===== */
        .footer { background: #0f172a; color: #94a3b8; text-align: center; padding: 32px 40px; font-size: 12px; line-height: 1.8; }
        .footer .company { color: #ffffff; font-weight: 600; font-size: 13px; margin-bottom: 6px; letter-spacing: 0.3px; }
        .footer .disclaimer { margin-top: 16px; opacity: 0.5; border-top: 1px solid #334155; padding-top: 16px; font-style: italic; }
    </style>
</head>
<body>
<div class="wrapper">

{{-- ===== HEADER ===== --}}
<div style="padding: 40px 40px 10px; background: #ffffff; border-radius: 8px 8px 0 0;">
    <div style="margin-bottom: 30px;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
            <tr>
                <td style="padding-right: 8px; padding-top: 2px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22 11 13 2 9l20-7z"/></svg>
                </td>
                <td style="font-size: 22px; font-weight: 500; letter-spacing: -0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    <span style="color: #0ea5e9;">Travel</span><span style="color: #334155;">Wonder</span>
                </td>
            </tr>
        </table>
    </div>
    @if($booking->payment_status === 'paid_30')
        <h1 style="font-size: 24px; color: #0f172a; margin: 0 0 6px 0; font-weight: 600; letter-spacing: -0.5px;">Xác Nhận Cọc 30% Tour Thành Công</h1>
        <p style="font-size: 14.5px; color: #16a34a; margin: 0; font-weight: 500;">Cảm ơn Quý khách đã hoàn tất khoản cọc 30%. Chỗ của Quý khách đã được giữ thành công!</p>
    @elseif(in_array($booking->payment_status, ['paid_100', 'paid']))
        <h1 style="font-size: 24px; color: #0f172a; margin: 0 0 6px 0; font-weight: 600; letter-spacing: -0.5px;">Xác Nhận Thanh Toán 100% Tour</h1>
        <p style="font-size: 14.5px; color: #16a34a; margin: 0; font-weight: 500;">Cảm ơn Quý khách đã hoàn tất 100% giá trị tour. Chúc Quý khách có chuyến đi vui vẻ!</p>
    @else
        <h1 style="font-size: 24px; color: #0f172a; margin: 0 0 6px 0; font-weight: 600; letter-spacing: -0.5px;">Xác Nhận Đặt Tour</h1>
        <p style="font-size: 14.5px; color: #64748b; margin: 0;">Vui lòng thanh toán trong vòng 15 phút để bảo lưu chỗ của Quý khách.</p>
    @endif
</div>

{{-- ===== BODY ===== --}}
<div class="body">

    <p class="greeting">Kính gửi Quý khách <strong>{{ $customerName }}</strong>,</p>
    @if($linkedUser ?? false)
    <div style="background-color: #e0f2fe; color: #0369a1; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #0ea5e9;">
        Đơn hàng này đã được liên kết với tài khoản <strong>{{ $booking->customer_email }}</strong> của bạn. <a href="{{ route('login') }}" style="color: #0284c7; font-weight: 600; text-decoration: none;">[Đăng nhập để xem chi tiết]</a>
    </div>
    @endif
    <p class="intro">
        @if($booking->payment_status === 'paid_30')
            Travel Wonder đã nhận được khoản đặt cọc 30% cho đơn tour <strong>{{ $booking->code }}</strong>. Vị trí trên chuyến đi của Quý khách đã được bảo lưu an toàn.
        @elseif(in_array($booking->payment_status, ['paid_100', 'paid']))
            Travel Wonder xác nhận đơn tour <strong>{{ $booking->code }}</strong> của Quý khách đã được thanh toán toàn bộ 100%. Toàn bộ dịch vụ đã được chuẩn bị sẵn sàng.
        @else
            Travel Wonder xin trân trọng cảm ơn Quý khách đã lựa chọn dịch vụ của chúng tôi. Đơn đặt tour đã được hệ thống ghi nhận. Vui lòng hoàn tất thanh toán để giữ chỗ.
        @endif
    </p>

    {{-- ===== MÃ ĐƠN + TRẠNG THÁI + NÚT THANH TOÁN ===== --}}
    <div class="order-meta">
        <div class="order-meta-row1">
            <div>
                <div class="order-id">MÃ ĐƠN: {{ $booking->code }}</div>
                <div class="order-date">Thời gian đặt: {{ $booking->created_at->format('H:i, d/m/Y') }}</div>
            </div>
            <div style="text-align: right;">
                @if($booking->payment_status == 'pending')
                    <span class="badge badge-warning">Chờ thanh toán (15 phút)</span>
                @elseif($booking->payment_status == 'paid_30')
                    <span class="badge badge-info">Đã cọc 30%</span>
                @else
                    <span class="badge badge-success">Đã thanh toán 100%</span>
                @endif
                <div style="margin-top: 8px; font-size: 13px; color: #475569;">
                    Hình thức: <strong>{{ $booking->payment_method == 'vnpay' ? 'VNPay' : ($booking->payment_method == 'transfer' ? 'Chuyển khoản QR' : 'Tiền mặt') }} ({{ $booking->payment_type == 'deposit' ? 'Đặt cọc 30%' : 'Thanh toán 100%' }})</strong>
                </div>
            </div>
        </div>

        {{-- NÚT THANH TOÁN KÈM THÔNG BÁO --}}
        @if($booking->payment_status === 'pending')
        <div class="order-meta-pay-row">
            <p style="margin-bottom: 16px; color: #b45309;">
                ⚠️ Quý khách vui lòng hoàn tất quá trình thanh toán trong vòng <strong>15 phút</strong> kể từ khi đặt tour để hệ thống xác nhận giữ chỗ. Sau 15 phút chưa thanh toán, đơn hàng sẽ <strong>tự động bị hủy</strong>.
            </p>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('user.bookings.detail', $booking->id) }}" style="display: inline-block; background: #f1f5f9; color: #0f172a; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1;">
                    Xem Chi Tiết Đơn
                </a>
                <a href="{{ route('frontend.tours.booking_success', $booking->id) }}" class="btn-pay-inline">
                    Thanh Toán Ngay
                </a>
            </div>
        </div>
        @elseif($booking->payment_status === 'paid_30')
        <div class="order-meta-pay-row">
            <p style="margin-bottom: 16px; color: #0369a1;">
                ✅ Đơn hàng đã được xác nhận cọc 30% (<strong>{{ number_format($booking->paid_amount, 0, ',', '.') }}₫</strong>). Số tiền 70% còn lại là <strong>{{ number_format($booking->remaining_amount, 0, ',', '.') }}₫</strong>. Vui lòng thanh toán trước ngày khởi hành.
            </p>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('user.bookings.detail', $booking->id) }}" style="display: inline-block; background: #f1f5f9; color: #0f172a; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1;">
                    Xem Chi Tiết Đơn
                </a>
                <a href="{{ route('user.bookings.detail', $booking->id) }}#pay70Section" class="btn-pay-inline">
                    Thanh Toán 70% Còn Lại
                </a>
            </div>
        </div>
        @else
        <div style="border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 16px; display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('user.bookings.detail', $booking->id) }}" style="display: inline-block; background: #f1f5f9; color: #0f172a; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1;">
                Xem Chi Tiết Đơn
            </a>
            <span style="color: #15803d; font-weight: 500; font-size: 13px;">Đơn hàng đã thanh toán đầy đủ 100%. Chúc Quý khách có chuyến đi tuyệt vời!</span>
        </div>
        @endif
    </div>

    {{-- ===== THÔNG TIN TOUR ===== --}}
    @php
        $tour = $schedule->tour;
        $durationDays = $tour->duration_days ?? \Carbon\Carbon::parse($schedule->departure_date)->diffInDays(\Carbon\Carbon::parse($schedule->return_date)) + 1;
        $durationNights = $tour->duration_nights ?? max(0, $durationDays - 1);
        $totalMeals = $durationDays * 3;
        $breakfasts = $durationDays;
        $lunches = max(0, $durationDays - 1);
        $dinners = max(0, $durationDays - 1);
    @endphp

    <div class="section">
        <span class="section-title">Thông Tin Tour</span>
        <table class="tour-info-table">
            <tr>
                <td class="t-label">Tên tour</td>
                <td class="t-value"><span class="em">{{ $tour->title ?? 'Chưa có tên' }}</span></td>
            </tr>
            <tr>
                <td class="t-label">Mã tour</td>
                <td class="t-value">{{ $tour->code }}</td>
            </tr>
            <tr>
                <td class="t-label">Thời gian</td>
                <td class="t-value">
                    <span class="em">{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}</span>
                    đến {{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }}
                    <span style="color:#64748b;">({{ $durationDays }} Ngày{{ $durationNights > 0 ? ' ' . $durationNights . ' Đêm' : '' }})</span>
                </td>
            </tr>
            <tr>
                <td class="t-label">Điểm tập kết</td>
                <td class="t-value">{{ $tour->meeting_point ?? $schedule->checkin_location ?? 'Theo lịch trình' }}</td>
            </tr>
            <tr>
                <td class="t-label">Phương tiện</td>
                <td class="t-value">
                    @if($booking->transport_type == 'flight') Máy bay khứ hồi + Ô tô đời mới tại điểm đến
                    @elseif($booking->transport_type == 'bus') Xe khách (Ô tô đời mới)
                    @else Tự túc phương tiện @endif
                </td>
            </tr>
            @if($tour->departure_time)
            <tr>
                <td class="t-label">Giờ khởi hành</td>
                <td class="t-value">{{ \Carbon\Carbon::parse($tour->departure_time)->format('H:i') }}</td>
            </tr>
            @endif
            <tr>
                <td class="t-label">Bữa ăn</td>
                <td class="t-value">
                    {{ ($breakfasts + $lunches + $dinners) }} bữa 
                    <span style="color:#64748b;">({{ $breakfasts }} sáng, {{ $lunches }} trưa, {{ $dinners }} tối)</span>
                </td>
            </tr>
            @if($booking->booking_accommodations && $booking->booking_accommodations->isNotEmpty())
            @php $bAcc = $booking->booking_accommodations->first(); @endphp
            <tr>
                <td class="t-label">Hạng lưu trú</td>
                <td class="t-value">
                    <span class="em">{{ $bAcc->accommodation_name_snapshot }}</span>
                    <br><span style="color:#64748b;">Hạng phòng: {{ $bAcc->room_type_name_snapshot }}</span>
                    <br><span style="color:#64748b;">(Số phòng: {{ $bAcc->single_rooms_count }}, Giường phụ: {{ $bAcc->extra_bed_qty }})</span>
                </td>
            </tr>
            @endif
            @if($schedule->schedule_guides && $schedule->schedule_guides->count() > 0)
            <tr>
                <td class="t-label">Hướng dẫn viên</td>
                <td class="t-value">
                    @foreach($schedule->schedule_guides as $sg)
                        {{ $sg->tour_guide->name ?? ($sg->tour_guide->user->name ?? 'Chưa phân công') }}
                        @if(isset($sg->tour_guide->user) && $sg->tour_guide->user->phone) – {{ $sg->tour_guide->user->phone }}@endif
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </td>
            </tr>
            @endif
            <tr>
                <td class="t-label">Trạng thái tour</td>
                <td class="t-value">
                    @if($booking->tour_status == 'upcoming') Sắp khởi hành
                    @elseif($booking->tour_status == 'in_progress') Đang diễn ra
                    @elseif($booking->tour_status == 'completed') Đã hoàn thành
                    @elseif($booking->tour_status == 'cancelled_by_customer') Đã hủy (khách hàng)
                    @elseif($booking->tour_status == 'cancelled_by_admin') Đã hủy (hệ thống)
                    @else {{ $booking->tour_status }} @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== LỊCH TRÌNH TÓM TẮT ===== --}}
    @if($tour->tour_itineraries && $tour->tour_itineraries->count() > 0)
    <div class="section">
        <span class="section-title">Lịch Trình Tóm Tắt</span>
        <div class="itinerary-box">
            @foreach($tour->tour_itineraries as $itin)
            @php
                $dayDate = \Carbon\Carbon::parse($schedule->departure_date)->addDays($itin->day_number - 1);
            @endphp
            <div class="itinerary-day">
                <span class="day-head">Ngày {{ $itin->day_number }} ({{ $dayDate->format('d/m') }}):</span>
                <span class="day-detail">{{ $itin->title }}</span>
                @if($itin->description)
                <div class="day-desc">{{ $itin->description }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== THÔNG TIN NGƯỜI ĐẶT ===== --}}
    <div class="section">
        <span class="section-title">Thông Tin Người Đặt Tour</span>
        <table class="tour-info-table">
            <tr><td class="t-label">Họ và tên</td><td class="t-value"><span class="em">{{ $customerName }}</span></td></tr>
            <tr><td class="t-label">Điện thoại</td><td class="t-value">{{ $customerPhone }}</td></tr>
            <tr><td class="t-label">Email</td><td class="t-value">{{ $booking->user->email ?? '—' }}</td></tr>
            <tr><td class="t-label">Số CCCD / Hộ chiếu</td><td class="t-value">{{ $booking->booking_passengers->where('passenger_type', 'adult')->first()?->identity_number ?? 'Chưa cập nhật' }}</td></tr>
        </table>
    </div>

    {{-- ===== DANH SÁCH HÀNH KHÁCH ===== --}}
    <div class="section">
        <span class="section-title">Danh Sách Hành Khách ({{ $booking->adults_count }} Người lớn @if($booking->children_count > 0)+ {{ $booking->children_count }} Trẻ em @endif)</span>
        
        @if(!$booking->is_passenger_list_submitted)
        <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
            <div style="color: #b45309; font-size: 14px; font-weight: 600; margin-bottom: 8px;">
                <i class="bi bi-info-circle-fill"></i> Quý khách chưa điền đủ danh sách hành khách!
            </div>
            <div style="color: #92400e; font-size: 13px; margin-bottom: 12px;">
                Để hoàn tất thủ tục, vui lòng bổ sung thông tin hành khách trước ngày khởi hành 24 tiếng.
            </div>
            <a href="{{ route('frontend.bookings.passengers', $booking->id) }}" style="display: inline-block; background: #ea580c; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-size: 13px; font-weight: 600;">
                Bổ Sung Danh Sách Hành Khách
            </a>
        </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">STT</th>
                    <th>Họ và tên</th>
                    <th>Loại khách</th>
                    <th>Ngày sinh</th>
                    <th>Số CCCD / Hộ chiếu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->booking_passengers as $index => $pax)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $pax->full_name }}</strong></td>
                    <td>
                        @if($pax->passenger_type == 'adult')
                            <span class="badge-adult">Người lớn</span>
                        @else
                            <span class="badge-child">Trẻ em</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($pax->date_of_birth)->format('d/m/Y') }}</td>
                    <td>{{ $pax->identity_number ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ===== DỊCH VỤ GIA TĂNG & VÉ THAM QUAN ===== --}}
    @if(($booking->addons && $booking->addons->count() > 0) || ($booking->ticket_bookings && $booking->ticket_bookings->count() > 0))
    <div class="section">
        <span class="section-title">Dịch Vụ Gia Tăng & Vé Tham Quan</span>
        <table class="table">
            <thead>
                <tr>
                    <th>Tên dịch vụ</th>
                    <th style="width: 60px; text-align: center;">SL</th>
                    <th style="text-align: right;">Đơn giá</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->ticket_bookings as $tb)
                <tr>
                    <td>{{ $tb->ticket_option->ticket->title ?? '' }} - {{ $tb->ticket_option->name ?? 'Vé tham quan' }}</td>
                    <td style="text-align: center;">{{ $tb->quantity }}</td>
                    <td style="text-align: right;">{!! format_currency($tb->total_price / $tb->quantity) !!}</td>
                    <td style="text-align: right;"><strong>{!! format_currency($tb->total_price) !!}</strong></td>
                </tr>
                @endforeach
                @foreach($booking->addons as $addon)
                <tr>
                    <td>{{ $addon->pivot->addon_name ?? $addon->name }}</td>
                    <td style="text-align: center;">{{ $addon->pivot->quantity }}</td>
                    <td style="text-align: right;">{!! format_currency($addon->pivot->price) !!}</td>
                    <td style="text-align: right;"><strong>{!! format_currency($addon->pivot->price * $addon->pivot->quantity) !!}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ===== BẢNG KÊ CHI PHÍ ===== --}}
    <div class="section">
        <span class="section-title">Bảng Kê Chi Tiết Chi Phí</span>
        <div class="price-box">
            @php
                $bk = $booking->price_breakdown ?? [];
                $baseTourPrice = array_sum([
                    $bk['transport'] ?? 0,
                    $bk['meal'] ?? 0,
                    $bk['insurance'] ?? 0,
                    $bk['service_fee'] ?? 0,
                    $bk['accommodation_base'] ?? 0,
                    $bk['accommodation_single_supplement'] ?? 0,
                    $bk['accommodation_extra_bed'] ?? 0,
                    $bk['accommodation_child'] ?? 0,
                    $bk['ticket'] ?? 0,
                ]);
            @endphp
            <div class="price-line">
                <div class="price-line-label">Giá tour cơ bản</div>
                <div class="price-line-value">{!! format_currency($baseTourPrice) !!}</div>
            </div>
            @if($booking->price_breakdown)
                @php $bk = $booking->price_breakdown; @endphp
                <div style="font-size: 12px; color: #64748b; margin-left: 20px; margin-bottom: 10px;">
                    - Vận chuyển: {!! format_currency($bk['transport'] ?? 0) !!}<br>
                    - Ăn uống: {!! format_currency($bk['meal'] ?? 0) !!}<br>
                    - Lưu trú: {!! format_currency($bk['accommodation_base'] ?? 0) !!}
                      @if(($bk['accommodation_single_supplement'] ?? 0) > 0) + Phụ thu phòng đơn {!! format_currency($bk['accommodation_single_supplement']) !!} @endif
                      @if(($bk['accommodation_extra_bed'] ?? 0) > 0) + Giường phụ {!! format_currency($bk['accommodation_extra_bed']) !!} @endif
                      @if(($bk['accommodation_child'] ?? 0) > 0) + Phụ thu trẻ em {!! format_currency($bk['accommodation_child']) !!} @endif<br>
                    - Bảo hiểm: {!! format_currency($bk['insurance'] ?? 0) !!}<br>
                    - Phí dịch vụ: {!! format_currency($bk['service_fee'] ?? 0) !!}<br>
                    @if(($bk['ticket'] ?? 0) > 0)
                    - Vé tham quan (Đã bao gồm): {!! format_currency($bk['ticket']) !!}
                      @if($booking->tour_schedule && $booking->tour_schedule->tour && $booking->tour_schedule->tour->tickets->isNotEmpty())
                          <br><span style="margin-left: 10px; color: #94a3b8;">+ Gồm: {{ $booking->tour_schedule->tour->tickets->pluck('title')->join(', ') }}</span>
                      @endif
                    @endif
                </div>
            @endif
            @if(($booking->transport_price ?? 0) > 0)
            <div class="price-line">
                <div class="price-line-label">Phụ thu phương tiện ({{ $booking->transport_type == 'flight' ? 'Vé máy bay' : 'Xe khách' }})</div>
                <div class="price-line-value">{!! format_currency($booking->transport_price) !!}</div>
            </div>
            @endif
            @if($booking->addons && $booking->addons->count() > 0)
            <div class="price-line">
                <div class="price-line-label">Dịch vụ bổ sung (Addons)</div>
                <div class="price-line-value">{!! format_currency($booking->addon_total) !!}</div>
            </div>
                <div style="font-size: 12px; color: #64748b; margin-left: 20px; margin-bottom: 10px;">
                    @foreach($booking->addons as $addon)
                        - {{ $addon->pivot->addon_name ?? $addon->name }} (x{{ $addon->pivot->quantity }}): {!! format_currency($addon->pivot->price * $addon->pivot->quantity) !!}<br>
                    @endforeach
                </div>
            @endif
            @if(($booking->discount_amount ?? 0) > 0)
            <div class="price-line discount">
                <div class="price-line-label">Giảm giá (Voucher / Khuyến mãi)</div>
                <div class="price-line-value">- {!! format_currency($booking->discount_amount) !!}</div>
            </div>
            @endif
            <div class="price-line total">
                <div class="price-line-label">TỔNG GIÁ TRỊ ĐƠN HÀNG</div>
                <div class="price-line-value">{!! format_currency($booking->total_price) !!}</div>
            </div>
            @if(($booking->paid_amount ?? 0) > 0)
            <div class="price-line paid" style="margin-top: 10px; background: #dcfce7; padding: 12px; border-radius: 6px;">
                <div class="price-line-label" style="color: #15803d;">Số tiền đã thanh toán ({{ $booking->payment_status == 'paid_30' ? 'Cọc 30%' : '100%' }})</div>
                <div class="price-line-value" style="color: #15803d; font-size: 16px;">{!! format_currency($booking->paid_amount) !!}</div>
            </div>
            @endif
            @if($booking->total_price > ($booking->paid_amount ?? 0))
            <div class="price-line remaining">
                <div class="price-line-label">SỐ TIỀN CÒN LẠI CẦN THANH TOÁN</div>
                <div class="price-line-value">{!! format_currency($booking->remaining_amount) !!}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== KIỂM TRA THÔNG TIN ===== --}}
    <div class="verify-box">
        <h4>Vui Lòng Kiểm Tra Kỹ Thông Tin</h4>
        <p>
            Quý khách vui lòng đối chiếu toàn bộ thông tin hành khách, lịch trình và chi phí trong email này với thông tin đã cung cấp lúc đặt tour.
            Nếu phát hiện bất kỳ sai sót nào, xin vui lòng liên hệ ngay với chúng tôi qua Hotline: 1900 8888 hoặc email cskh@travelwonder.com trước khi khởi hành.
        </p>
    </div>

    {{-- ===== LƯU Ý KHÁCH HÀNG ===== --}}
    <div class="notes-box">
        <h4>Lưu Ý Quan Trọng Dành Cho Quý Khách</h4>
        <ul>
            <li>Mang theo CCCD / Hộ chiếu bản gốc của tất cả hành khách trong suốt chuyến đi.</li>
            <li>Có mặt tại điểm tập kết trước giờ khởi hành ít nhất 30 phút. Tour khởi hành đúng giờ.</li>
            <li>Hoàn tất thanh toán trước ngày khởi hành để xác nhận chỗ và xuất vé.</li>
            <li>Trường hợp hủy tour: áp dụng mức phí theo chính sách hiện hành của Travel Wonder.</li>
            <li>Vui lòng chuẩn bị hành lý gọn gàng và mang theo các vật dụng cá nhân phù hợp thời tiết.</li>
        </ul>
    </div>

    {{-- ===== THÔNG TIN HỖ TRỢ ===== --}}
    <div class="support-box">
        <h4>Tổng Đài Hỗ Trợ Khách Hàng</h4>
        <div class="support-item">Hotline 24/7 (miễn cước): <span class="hotline">1900 8888</span></div>
        <div class="support-item">Zalo OA: Travel Wonder Official</div>
        <div class="support-item">Email: cskh@travelwonder.com</div>
    </div>

</div>

{{-- ===== FOOTER ===== --}}
<div class="footer">
    <div class="company">CÔNG TY CỔ PHẦN DU LỊCH TRAVEL WONDER</div>
    <div>Trụ sở: Số 123 Đường Du Lịch, Quận 1, TP. Hồ Chí Minh</div>
    <div>Hotline: 1900 8888 &nbsp;|&nbsp; Email: cskh@travelwonder.com</div>
    <div>MST: 0123456789 &nbsp;|&nbsp; GPLH: 01-1234/2026/TCDL-GP</div>
    <div class="disclaimer">Email này được gửi tự động từ hệ thống. Quý khách vui lòng không trả lời trực tiếp email này.</div>
</div>

    <div class="footer" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center;">
        <p style="font-size: 12px; color: #64748b; font-style: italic;">
            Nếu bạn nhận được email này nhưng không đặt tour tại Travel Wonder, vui lòng bỏ qua hoặc <a href="{{ url('/report-wrong-email/' . $booking->code) }}" style="color: #ef4444; text-decoration: underline;">Bấm vào đây để báo cáo nhầm lẫn</a> để chúng tôi hỗ trợ.
        </p>
    </div>
</body>
</html>