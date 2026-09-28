<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác minh địa chỉ Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat fixed;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .bg-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: -1;
    }

    .card {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        border: none;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.95);
    }
    </style>
</head>

<body class="position-relative py-4">
    <div class="bg-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-3">
                    <div class="card-body">
                        <div class="mb-4 text-muted small text-center">
                            Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn có thể xác minh địa chỉ email của mình bằng
                            cách nhấp vào liên kết chúng tôi vừa gửi cho bạn không? Nếu bạn không nhận được email, chúng
                            tôi sẽ vui lòng gửi lại cho bạn một email khác.
                        </div>

                        @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mb-4 text-center small">
                            Một liên kết xác minh mới đã được gửi đến địa chỉ email bạn đã cung cấp khi đăng ký.
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm fw-bold">Gửi lại Email xác
                                    minh</button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none text-muted btn-sm">Đăng
                                    xuất</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>