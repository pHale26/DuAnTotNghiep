<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo hủy đơn đặt tour - Travel Wonder</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; background-color: #f1f5f9; padding: 40px 20px; }
        .wrapper { max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden; }
        .header { padding: 40px 40px 10px; background: #ffffff; border-bottom: 1px solid #f1f5f9; }
        .body { padding: 32px 40px 40px 40px; }
        .greeting { font-size: 15px; margin-bottom: 8px; color: #0f172a; }
        .intro { font-size: 14px; color: #64748b; margin-bottom: 24px; }
        .cancel-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 20px; margin-bottom: 30px; }
        .cancel-box h4 { font-size: 15px; color: #991b1b; margin-bottom: 6px; font-weight: 600; }
        .cancel-box p { color: #7f1d1d; font-size: 13.5px; line-height: 1.5; }
        .section { margin-bottom: 28px; }
        .section-title { font-size: 14px; font-weight: 600; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #0f172a; display: block; }
        .info-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .info-table td { padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .info-table .t-label { color: #64748b; width: 180px; }
        .info-table .t-value { color: #0f172a; font-weight: 500; }
        .rebook-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 24px; text-align: center; margin-top: 24px; }
        .btn-rebook { display: inline-block; background: #2563eb; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; margin-top: 12px; }
        .footer { background: #0f172a; color: #94a3b8; text-align: center; padding: 32px 40px; font-size: 12px; line-height: 1.8; }
    </style>
</head>
<body>
<div class="wrapper">
    {{-- HEADER --}}
    <div class="header">
        <div style="margin-bottom: 20px;">
            <span style="font-size: 22px; font-weight: 700;">
                <span style="color: #0ea5e9;">Travel</span><span style="color: #334155;">Wonder</span>
            </span>
        </div>
        <h1 style="font-size: 22px; color: #dc2626; margin: 0 0 6px 0; font-weight: 600;">Thông Báo Tự Động Hủy Đơn Đặt Tour</h1>
        <p style="font-size: 14px; color: #64748b; margin: 0;">Đơn hàng {{ $booking->code }} đã bị hủy do hết thời hạn thanh toán 15 phút.</p>
    </div>

    {{-- BODY --}}
    <div class="body">
        <p class="greeting">Kính gửi Quý khách <strong>{{ $booking->customer_name ?? ($booking->user->name ?? 'Quý khách') }}</strong>,</p>
        <p class="intro">
            Hệ thống Travel Wonder xin thông báo đơn đặt tour <strong>{{ $booking->code }}</strong> của Quý khách đã bị tự động hủy theo quy định.
        </p>

        <div class="cancel-box">
            <h4>⚠️ Lý do hủy đơn:</h4>
            <p>Đơn hàng quá hạn thanh toán (vượt quá 15 phút mà chưa hoàn tất cọc/thanh toán). Số ghế đã giữ đã được trả lại tự động cho hệ thống để phục vụ khách hàng khác.</p>
        </div>

        <div class="section">
            <span class="section-title">Chi Tiết Đơn Đã Hủy</span>
            <table class="info-table">
                <tr>
                    <td class="t-label">Mã đơn hàng:</td>
                    <td class="t-value">{{ $booking->code }}</td>
                </tr>
                <tr>
                    <td class="t-label">Tên tour:</td>
                    <td class="t-value">{{ $booking->tour_schedule->tour->title ?? 'Chưa xác định' }}</td>
                </tr>
                <tr>
                    <td class="t-label">Ngày khởi hành:</td>
                    <td class="t-value">{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date ?? now())->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="t-label">Số lượng khách:</td>
                    <td class="t-value">{{ $booking->adults_count }} Người lớn @if($booking->children_count > 0)+ {{ $booking->children_count }} Trẻ em @endif</td>
                </tr>
                <tr>
                    <td class="t-label">Tổng tiền đơn cũ:</td>
                    <td class="t-value" style="color: #dc2626;">{{ number_format($booking->total_price, 0, ',', '.') }}₫</td>
                </tr>
            </table>
        </div>

        <div class="rebook-box">
            <h4 style="color: #1e3a8a; font-size: 15px; margin-bottom: 6px;">Quý khách vẫn muốn tham gia chuyến đi này?</h4>
            <p style="color: #475569; font-size: 13.5px; margin-bottom: 12px;">Đừng lo lắng! Quý khách có thể bấm vào nút dưới đây để đặt lại đơn nhanh chóng với đầy đủ thông tin số lượng khách đã chọn trước đó:</p>
            <a href="{{ $rebookUrl }}" class="btn-rebook">👉 Đặt Lại Tour Này Ngay</a>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div style="color: #ffffff; font-weight: 600; font-size: 13px; margin-bottom: 4px;">CÔNG TY DU LỊCH TRAVEL WONDER</div>
        <div>Hotline: 1900 1234 · Email: support@travelwonder.com</div>
        <div>Cảm ơn Quý khách và hân hạnh được phục vụ!</div>
    </div>
</div>
</body>
</html>
