<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhắc nhở lịch khởi hành Tour - Travel Wonder</title>
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
        .order-meta-row1 { display: flex; justify-content: space-between; align-items: flex-start; }
        .order-id { font-size: 18px; font-weight: 700; color: #0f172a; letter-spacing: 0.5px; }
        .order-date { font-size: 12px; color: #64748b; margin-top: 4px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 500; letter-spacing: 0.2px; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-success { background: #dcfce7; color: #15803d; }

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

        /* ===== PRICE BOX ===== */
        .price-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 20px 24px; }

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
        <h1 style="font-size: 24px; color: #0f172a; margin: 0 0 6px 0; font-weight: 600; letter-spacing: -0.5px;">Thông Báo Khởi Hành Tour</h1>
        <p style="font-size: 14.5px; color: #64748b; margin: 0;">Chỉ còn 3 ngày nữa là chuyến hành trình của bạn bắt đầu!</p>
    </div>

    {{-- ===== BODY ===== --}}
    <div class="body">

        <p class="greeting">Kính gửi Quý khách <strong>{{ $customerName }}</strong>,</p>
        <p class="intro">
            Thay mặt Travel Wonder, chúng tôi xin trân trọng thông báo chuyến du lịch của Quý khách sẽ chính thức khởi hành vào ngày <strong>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}</strong>. 
            Quý khách vui lòng kiểm tra lại toàn bộ thông tin chi tiết về chuyến đi dưới đây để có sự chuẩn bị tốt nhất.
        </p>

        {{-- ===== MÃ ĐƠN HÀNG ===== --}}
        <div class="order-meta">
            <div class="order-meta-row1">
                <div>
                    <div class="order-id">MÃ ĐƠN: {{ $booking->code }}</div>
                    <div class="order-date">Trạng thái thanh toán: 
                        @if($booking->payment_status == 'pending')
                            <span style="color: #d97706; font-weight: 600;">Chờ thanh toán</span>
                        @elseif($booking->payment_status == 'paid_30')
                            <span style="color: #0369a1; font-weight: 600;">Đã cọc 30%</span>
                        @else
                            <span style="color: #15803d; font-weight: 600;">Đã thanh toán 100%</span>
                        @endif
                    </div>
                </div>
                <div style="text-align: right;">
                    <a href="{{ route('user.bookings.detail', $booking->id) }}" style="display: inline-block; background: #2563eb; color: #ffffff !important; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600; text-align: center;">
                        Xem Chi Tiết Đơn
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== THÔNG TIN TOUR ===== --}}
        @php
            $tour = $schedule->tour;
            $durationDays = $tour->duration_days ?? \Carbon\Carbon::parse($schedule->departure_date)->diffInDays(\Carbon\Carbon::parse($schedule->return_date)) + 1;
            $durationNights = $tour->duration_nights ?? max(0, $durationDays - 1);
        @endphp

        <div class="section">
            <span class="section-title">Thông Tin Chuyến Đi</span>
            <table class="tour-info-table">
                <tr>
                    <td class="t-label">Tên tour</td>
                    <td class="t-value"><span class="em">{{ $tour->getTranslation('title', 'vi') ?? $tour->title }}</span></td>
                </tr>
                <tr>
                    <td class="t-label">Ngày khởi hành</td>
                    <td class="t-value">
                        <span class="em">{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}</span>
                        @if($tour->departure_time)
                            lúc <span class="em">{{ \Carbon\Carbon::parse($tour->departure_time)->format('H:i') }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="t-label">Ngày trở về</td>
                    <td class="t-value">{{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }} <span style="color:#64748b;">(Hành trình: {{ $durationDays }} ngày {{ $durationNights }} đêm)</span></td>
                </tr>
                <tr>
                    <td class="t-label">Điểm đón / tập trung</td>
                    <td class="t-value"><span class="em" style="color: #1e3a8a;">{{ $schedule->checkin_location ?? ($tour->meeting_point ?? 'Theo thông báo của hướng dẫn viên') }}</span></td>
                </tr>

                @if($schedule->schedule_guides && $schedule->schedule_guides->count() > 0)
                <tr>
                    <td class="t-label">Hướng dẫn viên phụ trách</td>
                    <td class="t-value">
                        @foreach($schedule->schedule_guides as $sg)
                            <strong>{{ $sg->guide->name ?? ($sg->guide->user->name ?? 'HDV Travel Wonder') }}</strong>
                            @if(isset($sg->guide->user) && $sg->guide->user->phone) – Hotline HDV: <span class="em">{{ $sg->guide->user->phone }}</span>@endif
                            @if(!$loop->last)<br>@endif
                        @endforeach
                    </td>
                </tr>
                @endif
            </table>
        </div>

        {{-- ===== DANH SÁCH HÀNH KHÁCH ĐI CÙNG ===== --}}
        @if($booking->booking_passengers && $booking->booking_passengers->count() > 0)
        <div class="section">
            <span class="section-title">Danh Sách Hành Khách Đi Cùng</span>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th>Họ và tên</th>
                        <th>Loại vé</th>
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
                        <td>{{ $pax->identity_number ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ===== LƯU Ý TRƯỚC KHI KHỞI HÀNH ===== --}}
        <div class="verify-box">
            <h4><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg> Một số lưu ý quan trọng trước ngày đi:</h4>
            <ul style="padding-left: 18px; margin-top: 8px; color: #78350f; line-height: 1.6;">
                <li><strong>Giấy tờ tùy thân:</strong> Quý khách và các thành viên đi cùng vui lòng mang theo bản gốc Hộ chiếu / CCCD (đối với người lớn) và Giấy khai sinh bản sao/chính (đối với trẻ em).</li>
                <li><strong>Thời gian tập trung:</strong> Xin vui lòng có mặt tại điểm đón/tập trung trước giờ khởi hành ít nhất <strong>30 phút</strong>.</li>
                <li><strong>Hành lý:</strong> Hãy đóng gói hành lý gọn gàng và tuân thủ các quy định về hành lý xách tay/ký gửi (nếu đi bằng máy bay).</li>
                <li><strong>Sức khỏe:</strong> Đảm bảo sức khỏe tốt và mang theo các loại thuốc cá nhân đặc trị cần thiết cho chuyến hành trình.</li>
            </ul>
        </div>

        {{-- ===== CHĂM SÓC KHÁCH HÀNG ===== --}}
        <div class="support-box">
            <h4>Hỗ Trợ Khách Hàng 24/7</h4>
            <div class="support-item">Nếu có bất kỳ thắc mắc hoặc yêu cầu hỗ trợ gấp nào, Quý khách vui lòng liên hệ:</div>
            <div class="support-item">Tổng đài chăm sóc khách hàng: <span class="hotline">1900 6868</span> (Nhánh số 1)</div>
            <div class="support-item">Email liên hệ: <a href="mailto:cskh@travelwonder.com" style="color: #1e3a8a; text-decoration: underline;">cskh@travelwonder.com</a></div>
        </div>

    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        <div class="company">Công ty Cổ phần Du lịch Travel Wonder Việt Nam</div>
        <div>Địa chỉ: Tòa nhà Wonder, số 12 Tôn Thất Thuyết, Cầu Giấy, Hà Nội</div>
        <div>Hotline: 1900 6868 | Website: www.travelwonder.com</div>
        <div class="disclaimer">Đây là email thông báo tự động từ hệ thống. Quý khách vui lòng không phản hồi trực tiếp (no-reply) vào địa chỉ email này.</div>
    </div>

</div>
</body>
</html>
