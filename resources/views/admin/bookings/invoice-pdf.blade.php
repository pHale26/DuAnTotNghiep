<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <meta http-equiv="Content-Type"
          content="text/html; charset=utf-8">

    <title>Hóa đơn {{ $booking->id }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>

<body>
    @include('admin.bookings.partials.invoice-content', [
        'isPdf' => true
    ])
</body>
</html>
