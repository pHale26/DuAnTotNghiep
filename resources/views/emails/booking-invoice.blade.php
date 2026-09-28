<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn đặt tour</title>
</head>

<body style="
    margin:0;
    padding:30px;
    background:#f4f7fb;
    font-family:Arial,sans-serif;
    color:#1e293b;
">
    <div style="
        max-width:650px;
        margin:0 auto;
        padding:30px;
        background:#ffffff;
        border:1px solid #e2e8f0;
        border-radius:12px;
    ">
        <h2 style="color:#0756b8;">
            Hóa đơn đặt tour Travel Wonder
        </h2>

        <p>
            Xin chào
            <strong>{{ $booking->user?->name ?? 'Quý khách' }}</strong>,
        </p>

        <p>
            Travel Wonder gửi đến quý khách hóa đơn đặt tour.
        </p>

        <p>
            <strong>Mã đơn:</strong>
            BOOK-{{ $booking->created_at->format('Ymd') }}-{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}
        </p>

        <p>
            <strong>Mã hóa đơn:</strong>
            {{ $invoiceCode }}
        </p>

        <p>
            <strong>Tổng thanh toán:</strong>
            <span style="color:#dc2626;">
                {{ number_format($booking->total_price, 0, ',', '.') }} ₫
            </span>
        </p>

        <p>
            Hóa đơn PDF được đính kèm trong email này.
        </p>

        <p>
            Cảm ơn quý khách đã sử dụng dịch vụ của Travel Wonder.
        </p>

        <p>
            Trân trọng,<br>
            <strong>Travel Wonder</strong>
        </p>
    </div>
</body>
</html>
