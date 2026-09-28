<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
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

    /* Password checklist */
    #password-checklist {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px 14px;
        border: 1px solid #e9ecef;
    }
    .pw-req {
        font-size: 0.82rem;
        color: #6c757d;
        padding: 3px 0;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: color 0.25s;
    }
    .pw-req.valid {
        color: #198754;
        font-weight: 600;
    }
    .pw-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 0.7rem;
        font-weight: 700;
        background: #dee2e6;
        color: #6c757d;
        flex-shrink: 0;
        transition: background 0.25s, color 0.25s;
    }
    .pw-req.valid .pw-icon {
        background: #198754;
        color: #fff;
        content: '\2713';
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
                        <div class="text-center mb-4">
                            <h2 class="h4 text-dark mb-1">Tạo tài khoản mới</h2>
                            <p class="text-muted small">Khám phá các tour du lịch tuyệt vời</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                <div class="invalid-feedback" id="name-feedback">
                                    @error('name') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required autocomplete="username">
                                <div class="invalid-feedback" id="email-feedback">
                                    @error('email') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required autocomplete="new-password">
                                <div class="invalid-feedback" id="password-feedback">
                                    @error('password') {{ $message }} @enderror
                                </div>
                                <!-- Password requirement checklist -->
                                <div id="password-checklist" class="mt-2" style="display:none;">
                                    <div class="pw-req" id="req-length">
                                        <span class="pw-icon">&#10005;</span> Ít nhất 8 ký tự
                                    </div>
                                    <div class="pw-req" id="req-upper">
                                        <span class="pw-icon">&#10005;</span> Ít nhất 1 chữ hoa
                                    </div>
                                    <div class="pw-req" id="req-number">
                                        <span class="pw-icon">&#10005;</span> Ít nhất 1 chữ số
                                    </div>
                                    <div class="pw-req" id="req-special">
                                        <span class="pw-icon">&#10005;</span> Ít nhất 1 ký tự đặc biệt
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" name="password_confirmation" required
                                    autocomplete="new-password">
                                <div class="invalid-feedback" id="password_confirmation-feedback">
                                    @error('password_confirmation') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary fw-bold" id="btn-submit">Đăng ký tài khoản</button>
                            </div>

                            <div class="text-center mt-3 small">
                                <span class="text-muted">Đã có tài khoản?</span> <a href="{{ route('login') }}"
                                    class="text-decoration-none fw-bold">Đăng nhập tại đây</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('btn-submit');

        let emailTimeout = null;
        let isEmailValid = false;

        // If the fields are already populated by old values, validate them
        if (nameInput.value.trim() !== '') validateName();
        if (emailInput.value.trim() !== '') validateEmail();
        if (passwordInput.value !== '') validatePassword();
        if (passwordConfirmInput.value !== '') validatePasswordConfirmation();

        function setValid(input, feedbackEl, isValid, errorMsg = '') {
            if (isValid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                feedbackEl.textContent = '';
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                feedbackEl.textContent = errorMsg;
            }
            checkFormValidity();
        }

        function validateName() {
            const val = nameInput.value.trim();
            const feedback = document.getElementById('name-feedback');
            if (val === '') {
                setValid(nameInput, feedback, false, 'Vui lòng nhập họ và tên.');
                return false;
            } else if (val.length < 3) {
                setValid(nameInput, feedback, false, 'Họ và tên phải dài ít nhất 3 ký tự.');
                return false;
            } else {
                setValid(nameInput, feedback, true);
                return true;
            }
        }

        function validateEmail() {
            const val = emailInput.value.trim();
            const feedback = document.getElementById('email-feedback');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (val === '') {
                setValid(emailInput, feedback, false, 'Vui lòng nhập địa chỉ email.');
                isEmailValid = false;
                return;
            } else if (!emailRegex.test(val)) {
                setValid(emailInput, feedback, false, 'Địa chỉ email không đúng định dạng.');
                isEmailValid = false;
                return;
            }

            clearTimeout(emailTimeout);
            emailTimeout = setTimeout(() => {
                fetch(`/api/check-email?email=${encodeURIComponent(val)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            setValid(emailInput, feedback, false, 'Địa chỉ email này đã được sử dụng.');
                            isEmailValid = false;
                        } else {
                            setValid(emailInput, feedback, true);
                            isEmailValid = true;
                        }
                    })
                    .catch(() => {
                        setValid(emailInput, feedback, true);
                        isEmailValid = true;
                    });
            }, 500);
        }

        function updateChecklist(val) {
            const checklist = document.getElementById('password-checklist');
            checklist.style.display = val.length > 0 ? 'block' : 'none';

            const checks = [
                { id: 'req-length',  ok: val.length >= 8,              icon: '\u2713' },
                { id: 'req-upper',   ok: /[A-Z]/.test(val),            icon: '\u2713' },
                { id: 'req-number',  ok: /[0-9]/.test(val),            icon: '\u2713' },
                { id: 'req-special', ok: /[^A-Za-z0-9]/.test(val),     icon: '\u2713' },
            ];

            checks.forEach(function (c) {
                const el = document.getElementById(c.id);
                const iconEl = el.querySelector('.pw-icon');
                if (c.ok) {
                    el.classList.add('valid');
                    iconEl.innerHTML = '&#10003;';
                } else {
                    el.classList.remove('valid');
                    iconEl.innerHTML = '&#10005;';
                }
            });
        }

        function validatePassword() {
            const val = passwordInput.value;
            const feedback = document.getElementById('password-feedback');

            const hasUppercase = /[A-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const hasSpecial = /[^A-Za-z0-9]/.test(val);

            updateChecklist(val);

            if (val === '') {
                setValid(passwordInput, feedback, false, 'Vui lòng nhập mật khẩu.');
                return false;
            } else if (val.length < 8) {
                setValid(passwordInput, feedback, false, 'Mật khẩu phải dài ít nhất 8 ký tự.');
                return false;
            } else if (!hasUppercase) {
                setValid(passwordInput, feedback, false, 'Mật khẩu phải chứa ít nhất 1 chữ in hoa.');
                return false;
            } else if (!hasNumber) {
                setValid(passwordInput, feedback, false, 'Mật khẩu phải chứa ít nhất 1 chữ số.');
                return false;
            } else if (!hasSpecial) {
                setValid(passwordInput, feedback, false, 'Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt.');
                return false;
            } else {
                setValid(passwordInput, feedback, true);
                if (passwordConfirmInput.value !== '') {
                    validatePasswordConfirmation();
                }
                return true;
            }
        }

        function validatePasswordConfirmation() {
            const val = passwordConfirmInput.value;
            const passwordVal = passwordInput.value;
            const feedback = document.getElementById('password_confirmation-feedback');
            if (val === '') {
                setValid(passwordConfirmInput, feedback, false, 'Vui lòng xác nhận mật khẩu.');
                return false;
            } else if (val !== passwordVal) {
                setValid(passwordConfirmInput, feedback, false, 'Mật khẩu xác nhận không khớp.');
                return false;
            } else {
                setValid(passwordConfirmInput, feedback, true);
                return true;
            }
        }

        function checkFormValidity() {
            const val = passwordInput.value;
            const hasUppercase = /[A-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const hasSpecial = /[^A-Za-z0-9]/.test(val);

            const nameOk = nameInput.value.trim().length >= 3;
            const passOk = val.length >= 8 && hasUppercase && hasNumber && hasSpecial;
            const passConfirmOk = passwordConfirmInput.value === val && passwordConfirmInput.value !== '';
            
            const allOk = nameOk && isEmailValid && passOk && passConfirmOk;
            submitBtn.disabled = !allOk;
        }

        let isComposingName = false;
        let isComposingEmail = false;
        let isComposingPassword = false;
        let isComposingPasswordConfirm = false;

        nameInput.addEventListener('compositionstart', () => {
            isComposingName = true;
        });
        nameInput.addEventListener('compositionend', () => {
            isComposingName = false;
            checkFormValidity();
        });
        nameInput.addEventListener('input', (e) => {
            if (isComposingName || (e && e.isComposing)) return;
            checkFormValidity();
        });
        nameInput.addEventListener('blur', validateName);

        emailInput.addEventListener('compositionstart', () => {
            isComposingEmail = true;
        });
        emailInput.addEventListener('compositionend', () => {
            isComposingEmail = false;
            validateEmail();
        });
        emailInput.addEventListener('input', (e) => {
            if (isComposingEmail || (e && e.isComposing)) return;
            validateEmail();
        });
        emailInput.addEventListener('blur', validateEmail);

        passwordInput.addEventListener('compositionstart', () => {
            isComposingPassword = true;
        });
        passwordInput.addEventListener('compositionend', () => {
            isComposingPassword = false;
            validatePassword();
        });
        passwordInput.addEventListener('input', (e) => {
            if (isComposingPassword || (e && e.isComposing)) return;
            validatePassword();
        });
        passwordInput.addEventListener('blur', validatePassword);

        passwordConfirmInput.addEventListener('compositionstart', () => {
            isComposingPasswordConfirm = true;
        });
        passwordConfirmInput.addEventListener('compositionend', () => {
            isComposingPasswordConfirm = false;
            validatePasswordConfirmation();
        });
        passwordConfirmInput.addEventListener('input', (e) => {
            if (isComposingPasswordConfirm || (e && e.isComposing)) return;
            validatePasswordConfirmation();
        });
        passwordConfirmInput.addEventListener('blur', validatePasswordConfirmation);
    });
    </script>
</body>

</html>