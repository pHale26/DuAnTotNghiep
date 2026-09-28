<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
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
                            <h2 class="h4 text-dark mb-1">Đăng nhập</h2>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            @if(request()->filled('redirect'))
                                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                            @endif
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required autocomplete="current-password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                <label class="form-check-label" for="remember_me">Ghi nhớ đăng nhập</label>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary fw-bold">Đăng nhập</button>
                            </div>

                            <div class="text-center mt-3 small">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none d-block mb-2">Quên
                                        mật khẩu?</a>
                                @endif
                                <span class="text-muted">Chưa có tài khoản?</span> <a href="{{ route('register') }}"
                                    class="text-decoration-none fw-bold">Đăng ký</a>
                            </div>
                        </form>

                        <div class="position-relative text-center my-3">
                            <hr>
                            <span
                                class="position-absolute top-50 start-50 translate-middle px-2 bg-white text-muted small">hoặc</span>
                        </div>

                        <a href="{{ route('auth.google') }}"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                                <path fill="#EA4335"
                                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                <path fill="#4285F4"
                                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                <path fill="#FBBC05"
                                    d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                <path fill="#34A853"
                                    d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.93 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                                <path fill="none" d="M0 0h48v48H0z" />
                            </svg>
                            Đăng nhập bằng Google
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
