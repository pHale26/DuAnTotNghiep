<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cảm ơn bạn đã đồng hành cùng Travel Wonder</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f7f6; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { background-color: #4a6ee0; color: white; text-align: center; padding: 30px 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .btn { display: inline-block; background-color: #ff6b6b; color: white; text-decoration: none; padding: 12px 25px; border-radius: 4px; font-weight: bold; margin-top: 20px; text-align: center; }
        .btn:hover { background-color: #e55a5a; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 14px; color: #6c757d; }
        .tour-info { background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #4a6ee0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cảm ơn bạn đã tin chọn Travel Wonder!</h1>
        </div>
        <div class="content">
            <p>Chào <strong>{{ $booking->customer_name }}</strong>,</p>
            <p>Chuyến hành trình của bạn vừa khép lại, và chúng tôi hy vọng bạn đã có những trải nghiệm tuyệt vời cùng Travel Wonder.</p>
            
            <div class="tour-info">
                <strong>Mã đơn:</strong> {{ $booking->code }}<br>
                <strong>Tour:</strong> {{ $booking->tour_schedule->tour->title ?? 'Tour' }}
            </div>

            <p>Sự hài lòng của bạn là động lực lớn nhất để chúng tôi tiếp tục hoàn thiện dịch vụ. Bạn có sẵn lòng dành ra 1 phút để đánh giá chất lượng chuyến đi cũng như mức độ hài lòng về <strong>Hướng dẫn viên</strong> không?</p>

            <div style="text-align: center;">
                <a href="{{ route('user.bookings.detail', $booking->id) }}" class="btn">⭐ Đánh giá chuyến đi ngay</a>
            </div>
            
            <p style="margin-top: 30px;">Hẹn gặp lại bạn trong những hành trình tiếp theo!</p>
            <p>Trân trọng,<br><strong>Đội ngũ Travel Wonder</strong></p>
        </div>
        <div class="footer">
            Đây là email tự động. Vui lòng không trả lời email này.<br>
            &copy; {{ date('Y') }} Travel Wonder. All rights reserved.
        </div>
    </div>
</body>
</html>
