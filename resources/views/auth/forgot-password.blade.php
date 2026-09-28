<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu</title>
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
                <!-- Session Status -->
                @if (session('status'))
                <div class="alert alert-success mb-4 text-center">
                    {{ session('status') }}
                </div>
                @endif
                <div class="card p-3">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="h4 text-dark mb-3">Quên mật khẩu?</h2>
                            <p class="text-muted small">Không sao cả. Chỉ cần cho chúng tôi biết địa chỉ email của bạn
                                và chúng tôi sẽ gửi cho bạn một liên kết đặt lại mật khẩu để bạn có thể chọn mật khẩu
                                mới.</p>
                        </div>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required autofocus>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary fw-bold">Gửi link đặt lại mật khẩu</button>
                            </div>

                            <div class="text-center mt-3 small">
                                <a href="{{ route('login') }}" class="text-decoration-none">Quay lại đăng nhập</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>