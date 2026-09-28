<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
        <h2 style="color: #007CE8; text-align: center;">Travel Wonder - Vé Điện Tử</h2>

        <p>Xin chào {{ $passengerName }},</p>
        <p>Cảm ơn anh/chị đã sử dụng dịch vụ của Travel Wonder. Đơn đặt tour và vé máy bay của anh/chị đã được xử lý
            thành công.</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <h3 style="margin-top: 0;">Mã chuẩn chi (PNR): <span
                    style="color: #e53e3e; font-size: 24px;">{{ $pnrCode }}</span></h3>
            <p>Mã đơn tour: #{{ $booking->id }}</p>
            <p><strong>Tổng tiền thanh toán:</strong> {!! format_currency($booking->total_price) !!}</p>
        </div>

        <p style="margin-top: 20px;">Anh/chị vui lòng lưu lại mã PNR này và xuất trình tại quầy thủ tục của hãng hàng
            không khi đến sân bay.</p>

        <p>Chúc anh/chị có một chuyến đi vui vẻ!</p>
        <p>Trân trọng,<br>Đội ngũ Travel Wonder</p>
    </div>
</body>

</html>