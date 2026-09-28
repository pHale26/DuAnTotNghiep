<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xác nhận đặt tour</title>
</head>
<body style="font-family: sans-serif; padding: 20px; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 8px;">
        <h2 style="color: #0ea5e9;">Mã Xác Nhận Đặt Tour</h2>
        <p>Kính chào Quý khách,</p>
        <p>Có một đơn đặt tour vừa được tạo với email này. Nếu là bạn, vui lòng sử dụng mã OTP dưới đây để xác nhận:</p>
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; padding: 15px 30px; font-size: 24px; font-weight: bold; background: #e0f2fe; color: #0284c7; border-radius: 8px; letter-spacing: 5px;">{{ $booking->email_verify_token }}</span>
        </div>
        <p>Nếu không phải bạn, vui lòng bỏ qua email này.</p>
        
        <hr style="border: 0; border-top: 1px solid #ddd; margin: 30px 0;">
        <p style="font-size: 12px; color: #777; font-style: italic;">
            Nếu bạn nhận được email này nhưng không đặt tour tại Travel Wonder, vui lòng bỏ qua hoặc <a href="{{ url("/report-wrong-email/" . $booking->code) }}" style="color: #ef4444;">Bấm vào đây để báo cáo nhầm lẫn</a> để chúng tôi hỗ trợ hủy bỏ.
        </p>
    </div>
</body>
</html>
