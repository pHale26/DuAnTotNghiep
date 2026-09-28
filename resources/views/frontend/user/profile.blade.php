@extends('layouts.master')

@section('title', 'Tài khoản cá nhân - Travel Wonder')

@section('content')
    <style>
        /* ===== Profile Page Styles ===== */
        .profile-page {
            background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%);
            min-height: 100vh;
        }

        /* Hero Banner */
        .profile-hero {
            background: linear-gradient(135deg, #0B132B 0%, #1a2b4c 40%, #007CE8 100%);
            padding: 60px 0 120px;
            position: relative;
            overflow: hidden;
        }

        .profile-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .profile-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 50%, #f0f9ff 100%);
            border-radius: 50% 50% 0 0 / 30px 30px 0 0;
        }

        .floating-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            pointer-events: none;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: #007CE8;
            top: -100px;
            right: -50px;
        }

        .orb-2 {
            width: 200px;
            height: 200px;
            background: #F5A623;
            bottom: 20px;
            left: 10%;
        }

        /* Profile Card */
        .profile-card-main {
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .profile-avatar-section {
            background: white;
            border-radius: 28px;
            padding: 36px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-avatar-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #007CE8, #00c6ff, #F5A623);
        }

        .avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .avatar-ring {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, #007CE8, #00c6ff, #F5A623);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid white;
            background: white;
        }

        .avatar-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .avatar-ring:hover .avatar-inner img {
            transform: scale(1.05);
        }

        .avatar-initials {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #007CE8, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            font-weight: 800;
            color: white;
            letter-spacing: -1px;
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007CE8, #0056b3);
            color: white;
            border: 3px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 124, 232, 0.4);
        }

        .avatar-edit-btn:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 6px 20px rgba(0, 124, 232, 0.5);
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, rgba(0, 124, 232, 0.1), rgba(0, 198, 255, 0.1));
            border: 1px solid rgba(0, 124, 232, 0.2);
            color: #007CE8;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .profile-stat-item {
            background: linear-gradient(135deg, #f8faff, #f0f4ff);
            border: 1px solid rgba(0, 124, 232, 0.1);
            border-radius: 16px;
            padding: 16px 8px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .profile-stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 124, 232, 0.15);
            border-color: rgba(0, 124, 232, 0.3);
        }

        .profile-stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #007CE8, #0056b3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .profile-stat-label {
            font-size: 0.72rem;
            color: #718096;
            font-weight: 600;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Sidebar Nav */
        .profile-nav {
            margin-top: 24px;
        }

        .profile-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 18px;
            border-radius: 14px;
            color: #718096;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.25s ease;
            margin-bottom: 6px;
            position: relative;
            overflow: hidden;
        }

        .profile-nav-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 124, 232, 0.08), rgba(0, 198, 255, 0.05));
            opacity: 0;
            transition: opacity 0.25s ease;
            border-radius: 14px;
        }

        .profile-nav-item:hover::before,
        .profile-nav-item.active::before {
            opacity: 1;
        }

        .profile-nav-item:hover,
        .profile-nav-item.active {
            color: #007CE8;
            transform: translateX(4px);
        }

        .profile-nav-item.active {
            box-shadow: 0 4px 15px rgba(0, 124, 232, 0.15);
        }

        .profile-nav-item .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            background: rgba(0, 124, 232, 0.08);
            color: #007CE8;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }

        .profile-nav-item.active .nav-icon {
            background: linear-gradient(135deg, #007CE8, #0056b3);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 124, 232, 0.3);
        }

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.4s ease forwards;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f2f5;
        }

        .header-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #007CE8, #0056b3);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 6px 16px rgba(0, 124, 232, 0.3);
            flex-shrink: 0;
        }

        .header-icon.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
        }

        .header-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        /* Form Styles */
        .form-label-custom {
            font-weight: 700;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-field {
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 12px 16px;
            font-weight: 500;
            color: #1f2937;
            transition: all 0.25s ease;
            background: #fafbff;
            font-size: 0.95rem;
            width: 100%;
            box-sizing: border-box;
        }

        .input-field:focus {
            border-color: #007CE8;
            background: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 124, 232, 0.1);
        }

        .input-field.is-invalid {
            border-color: #ef4444;
            background: #fff5f5;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.25s ease;
        }

        .input-with-icon .input-field {
            padding-left: 44px;
        }

        .input-with-icon:focus-within .field-icon {
            color: #007CE8;
        }

        .input-with-icon .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0;
        }

        .input-with-icon .toggle-password:hover {
            color: #007CE8;
        }

        /* Buttons */
        .btn-save {
            background: linear-gradient(135deg, #007CE8, #0056b3);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 13px 32px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 6px 20px rgba(0, 124, 232, 0.3);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(0, 124, 232, 0.4);
            color: white;
        }

        .btn-save:active {
            transform: translateY(0);
        }

        .btn-danger-outline {
            background: transparent;
            color: #ef4444;
            border: 2px solid #fecaca;
            border-radius: 14px;
            padding: 12px 32px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-danger-outline:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        /* Password Strength */
        .password-strength {
            height: 4px;
            border-radius: 4px;
            background: #e5e7eb;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.4s ease, background 0.4s ease;
            width: 0%;
        }

        .strength-text {
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 5px;
        }

        /* Identity Card */
        .identity-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .identity-field {
            background: #f8faff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .identity-field-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .identity-field-value {
            font-weight: 700;
            color: #1a2b4c;
            font-size: 0.95rem;
        }

        /* Avatar Upload Modal */
        .avatar-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .avatar-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .avatar-modal {
            background: white;
            border-radius: 28px;
            padding: 40px;
            max-width: 460px;
            width: 90%;
            text-align: center;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s ease;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.2);
        }

        .avatar-modal-overlay.active .avatar-modal {
            transform: scale(1) translateY(0);
        }

        .avatar-preview-wrap {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 4px solid #007CE8;
            overflow: hidden;
            margin: 0 auto 20px;
            box-shadow: 0 8px 30px rgba(0, 124, 232, 0.2);
        }

        .avatar-preview-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .drop-zone {
            border: 2px dashed #d1d5db;
            border-radius: 16px;
            padding: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafbff;
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: #007CE8;
            background: rgba(0, 124, 232, 0.04);
        }

        /* Alert Styles */
        .alert-custom {
            border: none;
            border-radius: 14px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .alert-success-custom {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
            border-left: 4px solid #10b981;
            color: #065f46;
        }

        .alert-danger-custom {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
            border-left: 4px solid #ef4444;
            color: #7f1d1d;
        }

        /* Joined date */
        .joined-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 0, 0, 0.04);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-hero {
                padding: 40px 0 100px;
            }

            .content-card {
                padding: 20px;
            }

            .profile-avatar-section {
                padding: 24px;
            }
        }
    </style>

    {{-- Avatar Upload Modal --}}
    <div class="avatar-modal-overlay" id="avatarModalOverlay">
        <div class="avatar-modal">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Cập nhật ảnh đại diện</h5>
                <button class="btn btn-sm btn-light rounded-circle" id="closeAvatarModal"
                    style="width:32px;height:32px;padding:0;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="avatar-preview-wrap mb-3" id="previewWrap">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Preview" id="avatarPreview">
                @else
                    <div class="avatar-initials" id="avatarPreview" style="border-radius:50%">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('user.avatar.update') }}" enctype="multipart/form-data"
                id="avatarUploadForm">
                @csrf
                <div class="drop-zone mb-4" id="dropZone">
                    <i class="bi bi-cloud-arrow-up fs-2 text-primary mb-2 d-block"></i>
                    <p class="mb-1 fw-600">Kéo thả ảnh vào đây hoặc</p>
                    <label class="btn btn-sm btn-outline-primary rounded-pill px-4 cursor-pointer mb-0">
                        <i class="bi bi-folder2-open me-1"></i> Chọn ảnh
                        <input type="file" name="avatar" id="avatarFileInput" accept="image/*" class="d-none">
                    </label>
                    <p class="text-muted small mt-2 mb-0">PNG, JPG, GIF tối đa 10MB</p>
                </div>

                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn-danger-outline" id="cancelAvatarBtn">Hủy</button>
                    <button type="submit" class="btn-save" id="uploadAvatarBtn" disabled>
                        <i class="bi bi-check2"></i> Lưu ảnh
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="profile-page">
        {{-- Hero Banner --}}
        <div class="profile-hero">
            <div class="floating-orb orb-1"></div>
            <div class="floating-orb orb-2"></div>
            <div class="container" style="position: relative; z-index: 2;">
                <div class="text-white">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color: rgba(255,255,255,0.5);">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}"
                                    class="text-white-50 text-decoration-none">Trang chủ</a></li>
                            <li class="breadcrumb-item text-white active">Tài khoản của tôi</li>
                        </ol>
                    </nav>
                    <h1 class="fw-800 mb-1" style="color:white; font-size:2rem;">Tài khoản của tôi</h1>
                    <p class="mb-0" style="color: rgba(255,255,255,0.7);">Quản lý thông tin cá nhân và bảo mật tài khoản</p>
                </div>
            </div>
        </div>

        <div class="container profile-card-main pb-5">
            <div class="row g-4">

                {{-- ======= SIDEBAR ======= --}}
                <div class="col-lg-3">
                    <div class="profile-avatar-section reveal-up">

                        {{-- Avatar --}}
                        <div class="avatar-container mb-1">
                            <div class="avatar-ring">
                                <div class="avatar-inner" id="mainAvatarWrapper" data-user-avatar>
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="Ảnh đại diện" id="mainAvatarImg">
                                    @else
                                        <div class="avatar-initials">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <button class="avatar-edit-btn" id="openAvatarModal" title="Đổi ảnh đại diện">
                                <i class="bi bi-camera-fill"></i>
                            </button>
                        </div>

                        <div class="user-badge">
                            <i class="bi bi-patch-check-fill"></i> Thành viên
                        </div>

                        <h5 class="fw-800 mb-1" style="color: #0B132B;">{{ $user->name }}</h5>
                        <p class="text-muted small mb-1">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-muted small mb-3">
                                <i class="bi bi-telephone-fill me-1 text-primary"></i>{{ $user->phone }}
                            </p>
                        @endif

                        <div class="joined-chip mb-4">
                            <i class="bi bi-calendar3"></i>
                            Tham gia {{ $user->created_at->format('m/Y') }}
                        </div>

                        {{-- Thống kê --}}
                        <div class="row g-2 mb-4">
                            <div class="col-4">
                                <div class="profile-stat-item">
                                    <div class="profile-stat-value">{{ $user->bookings->count() }}</div>
                                    <div class="profile-stat-label">Đơn đặt</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="profile-stat-item">
                                    <div class="profile-stat-value">{{ $user->wishlists->count() }}</div>
                                    <div class="profile-stat-label">Yêu thích</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="profile-stat-item">
                                    <div class="profile-stat-value">{{ $user->reviews->count() }}</div>
                                    <div class="profile-stat-label">Đánh giá</div>
                                </div>
                            </div>
                        </div>

                        {{-- Nav --}}
                        <nav class="profile-nav">
                            <a href="#" class="profile-nav-item" id="nav-info" onclick="switchProfileTab('info', event)">
                                <span class="nav-icon"><i class="bi bi-person-fill"></i></span>
                                Thông tin cá nhân
                            </a>
                            <a href="#" class="profile-nav-item" id="nav-bookings"
                                onclick="switchProfileTab('bookings', event)">
                                <span class="nav-icon"><i class="bi bi-bag-check-fill"></i></span>
                                Đơn đặt tour
                                <span class="ms-auto badge rounded-pill"
                                    style="background:#007CE8;color:white;font-size:0.72rem;">{{ $bookings->count() }}</span>
                            </a>

                            <a href="#" class="profile-nav-item" id="nav-wishlists"
                                onclick="switchProfileTab('wishlists', event)">
                                <span class="nav-icon"><i class="bi bi-heart-fill"></i></span>
                                Tour đã lưu
                                <span class="ms-auto badge rounded-pill"
                                    style="background:#ef4444;color:white;font-size:0.72rem;">{{ $wishlists->count() }}</span>
                            </a>
                            <a href="#" class="profile-nav-item" id="nav-notifications"
                                onclick="switchProfileTab('notifications', event)">
                                <span class="nav-icon"><i class="bi bi-bell-fill"></i></span>
                                Thông báo
                                @php
                                    $unread = auth()->user()->unreadNotifications->count();
                                @endphp
                                @if($unread > 0)
                                    <span class="ms-auto badge rounded-pill bg-danger"
                                        style="color:white;font-size:0.72rem;">{{ $unread }}</span>
                                @endif
                            </a>
                        </nav>
                    </div>
                </div>

                {{-- ======= NỘI DUNG CHÍNH ======= --}}
                <div class="col-lg-9">

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert-custom alert-success-custom reveal-up">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert-custom alert-danger-custom reveal-up">
                            <i class="bi bi-exclamation-circle-fill fs-5"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif


                    {{-- ===== TAB: THÔNG TIN CÁ NHÂN ===== --}}
                    <div id="tab-info">
                        <div class="content-card reveal-up">
                            <div class="content-card-header">
                                <div class="header-icon">
                                    <i class="bi bi-person-vcard-fill"></i>
                                </div>
                                <div>
                                    <h5 class="fw-800 mb-0" style="color:#0B132B;">Thông tin cơ bản</h5>
                                    <p class="text-muted small mb-0">Cập nhật tên, email và số điện thoại của bạn</p>
                                </div>
                            </div>

                            @if($errors->any())
                                <div class="alert-custom alert-danger-custom mb-4">
                                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                    <div>
                                        @foreach($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('user.profile.update') }}">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Họ và tên <span
                                                class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-person field-icon"></i>
                                            <input type="text" name="name"
                                                class="input-field @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user->name) }}" placeholder="Nguyễn Văn A" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Số điện thoại</label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-phone field-icon"></i>
                                            <input type="tel" name="phone"
                                                class="input-field @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $user->phone) }}" placeholder="0912 345 678">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-custom">Email <span class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-envelope field-icon"></i>
                                            <input type="email" name="email"
                                                class="input-field @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}" placeholder="example@email.com"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end pt-2">
                                        <button type="submit" class="btn-save">
                                            <i class="bi bi-check2-circle"></i>
                                            Lưu thay đổi
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- ===== Thông tin định danh ===== --}}
                        @if($user->identity)
                            <div class="content-card reveal-up">
                                <div class="content-card-header">
                                    <div class="header-icon success">
                                        <i class="bi bi-shield-fill-check"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-800 mb-0" style="color:#0B132B;">Thông tin định danh</h5>
                                        <p class="text-muted small mb-0">CCCD / Hộ chiếu được xác minh. Liên hệ hỗ trợ để thay
                                            đổi.
                                        </p>
                                    </div>
                                    <span class="badge ms-auto"
                                        style="background: linear-gradient(135deg,#10b981,#059669); border-radius: 10px; padding: 6px 14px;">
                                        <i class="bi bi-patch-check-fill me-1"></i>Đã xác minh
                                    </span>
                                </div>

                                <div class="identity-info-grid">
                                    <div class="identity-field">
                                        <div class="identity-field-label">Số CCCD / Hộ chiếu</div>
                                        <div class="identity-field-value">{{ $user->identity->identity_number }}</div>
                                    </div>
                                    <div class="identity-field">
                                        <div class="identity-field-label">Họ và tên đầy đủ</div>
                                        <div class="identity-field-value">{{ $user->identity->full_name }}</div>
                                    </div>
                                    <div class="identity-field">
                                        <div class="identity-field-label">Ngày sinh</div>
                                        <div class="identity-field-value">{{ $user->identity->date_of_birth->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="identity-field">
                                        <div class="identity-field-label">Giới tính</div>
                                        <div class="identity-field-value">
                                            @if($user->identity->gender === 'male') <i
                                                class="bi bi-gender-male text-primary me-1"></i>Nam
                                            @elseif($user->identity->gender === 'female') <i
                                                class="bi bi-gender-female text-danger me-1"></i>Nữ
                                            @else <i class="bi bi-gender-ambiguous text-secondary me-1"></i>Khác
                                            @endif
                                        </div>
                                    </div>
                                    <div class="identity-field">
                                        <div class="identity-field-label">Ngày hết hạn giấy tờ</div>
                                        <div class="identity-field-value">{{ $user->identity->expiry_date->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ===== Đổi mật khẩu ===== --}}
                        <div class="content-card reveal-up">
                            <div class="content-card-header">
                                <div class="header-icon warning">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>
                                <div>
                                    <h5 class="fw-800 mb-0" style="color:#0B132B;">Thay đổi mật khẩu</h5>
                                    <p class="text-muted small mb-0">Sử dụng mật khẩu mạnh để bảo vệ tài khoản</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('user.password.change') }}" id="passwordForm">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label-custom">Mật khẩu hiện tại <span
                                                class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-lock field-icon"></i>
                                            <input type="password" name="current_password" id="currentPassword"
                                                class="input-field" placeholder="Nhập mật khẩu hiện tại" required>
                                            <button type="button" class="toggle-password" data-target="currentPassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Mật khẩu mới <span
                                                class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-lock-fill field-icon"></i>
                                            <input type="password" name="password" id="newPassword" class="input-field"
                                                placeholder="Tối thiểu 8 ký tự" required>
                                            <button type="button" class="toggle-password" data-target="newPassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="password-strength mt-2">
                                            <div class="password-strength-bar" id="strengthBar"></div>
                                        </div>
                                        <div class="strength-text" id="strengthText"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Xác nhận mật khẩu mới <span
                                                class="text-danger">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-lock-fill field-icon"></i>
                                            <input type="password" name="password_confirmation" id="confirmPassword"
                                                class="input-field" placeholder="Nhập lại mật khẩu mới" required>
                                            <button type="button" class="toggle-password" data-target="confirmPassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="strength-text" id="matchText"></div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end pt-2">
                                        <button type="submit" class="btn-save"
                                            style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 6px 20px rgba(245,158,11,0.3);">
                                            <i class="bi bi-shield-check"></i>
                                            Cập nhật mật khẩu
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>{{-- end tab-info --}}

                    {{-- ===== TAB: ĐƠN HÀNG (BOOKINGS) ===== --}}
                    <div id="tab-bookings" style="display:none;">
                        @if(isset($pendingLinkedBookings) && $pendingLinkedBookings->count() > 0)
                        <div class="alert alert-info shadow-sm mb-4" style="border-left: 4px solid #0dcaf0;">
                            <h5 class="alert-heading"><i class="bi bi-info-circle-fill me-2"></i>Có đơn hàng đang chờ bạn nhận</h5>
                            <p class="mb-0">Chúng tôi tìm thấy <strong>{{ $pendingLinkedBookings->count() }}</strong> đơn hàng (đã được tạo bằng email <strong>{{ Auth::user()->email }}</strong>) nhưng chưa được liên kết với tài khoản này.</p>
                            <div class="mt-3">
                                @foreach($pendingLinkedBookings as $pendingBooking)
                                    <div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-white rounded border">
                                        <div>
                                            <strong>{{ $pendingBooking->code }}</strong> - {{ $pendingBooking->tour_schedule->tour->title ?? 'Tour không xác định' }}
                                            <span class="text-muted ms-2">{{ \Carbon\Carbon::parse($pendingBooking->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div>
                                            <form action="{{ route('frontend.user.bookings.link', $pendingBooking->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="accept">
                                                <button class="btn btn-sm btn-primary">Nhận đơn hàng</button>
                                            </form>
                                            <form action="{{ route('frontend.user.bookings.link', $pendingBooking->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="ignore">
                                                <button class="btn btn-sm btn-outline-secondary">Không phải của tôi</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <style>
                            /* Booking tab inline styles */
                            .bk-card {
                                background: #fff;
                                border-radius: 20px;
                                border: 1px solid #e8edf5;
                                overflow: hidden;
                                transition: box-shadow 0.25s ease, transform 0.25s ease, border-color 0.25s ease;
                                margin-bottom: 24px;
                            }

                            .bk-card:hover {
                                border-color: rgba(0, 124, 232, 0.25);
                                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                                transform: translateY(-2px);
                            }

                            .bk-card-header {
                                padding: 16px 24px;
                                background: #f8fafc;
                                border-bottom: 1px solid #e8edf5;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                flex-wrap: wrap;
                                gap: 12px;
                            }

                            .bk-order-id {
                                font-size: 0.82rem;
                                font-weight: 700;
                                color: #374151;
                                letter-spacing: 0.5px;
                            }

                            .bk-date-text {
                                font-size: 0.8rem;
                                color: #9ca3af;
                                font-weight: 500;
                            }

                            .ts-badge {
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                padding: 5px 14px;
                                border-radius: 50px;
                                font-size: 0.78rem;
                                font-weight: 700;
                                letter-spacing: 0.3px;
                            }

                            .ts-upcoming {
                                background: #eff6ff;
                                color: #2563eb;
                                border: 1px solid #bfdbfe;
                            }

                            .ts-in_progress {
                                background: #fff7ed;
                                color: #d97706;
                                border: 1px solid #fed7aa;
                            }

                            .ts-checking_in {
                                background: #f0fdf4;
                                color: #16a34a;
                                border: 1px solid #bbf7d0;
                            }

                            .ts-completed {
                                background: #f0fdf4;
                                color: #15803d;
                                border: 1px solid #bbf7d0;
                            }

                            .ts-cancelled {
                                background: #fef2f2;
                                color: #dc2626;
                                border: 1px solid #fecaca;
                            }

                            .bk-tour-img {
                                width: 90px;
                                height: 90px;
                                border-radius: 14px;
                                object-fit: cover;
                                flex-shrink: 0;
                            }

                            .bk-tour-img-placeholder {
                                width: 90px;
                                height: 90px;
                                border-radius: 14px;
                                background: linear-gradient(135deg, #dbeafe, #e0e7ff);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-shrink: 0;
                                font-size: 2rem;
                                color: #6366f1;
                            }

                            .payment-box {
                                background: #f8fafc;
                                border: 1px solid #e8edf5;
                                border-radius: 16px;
                                padding: 20px;
                            }

                            .payment-row {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                font-size: 0.85rem;
                                padding: 5px 0;
                            }

                            .payment-row .label {
                                color: #6b7280;
                            }

                            .payment-row .value {
                                font-weight: 600;
                                color: #111827;
                            }

                            .payment-total-row {
                                border-top: 1px dashed #d1d5db;
                                margin-top: 10px;
                                padding-top: 12px;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                            }

                            .payment-total-label {
                                font-weight: 700;
                                color: #111827;
                                font-size: 0.9rem;
                            }

                            .payment-total-value {
                                font-weight: 800;
                                color: #007ce8;
                                font-size: 1.2rem;
                            }

                            .ps-badge {
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                padding: 8px 16px;
                                border-radius: 50px;
                                font-size: 0.82rem;
                                font-weight: 700;
                                width: 100%;
                                justify-content: center;
                                margin-top: 12px;
                            }

                            .ps-pending {
                                background: #fffbeb;
                                color: #92400e;
                                border: 1px solid #fde68a;
                            }

                            .ps-paid30 {
                                background: #eff6ff;
                                color: #1d4ed8;
                                border: 1px solid #bfdbfe;
                            }

                            .ps-paid100 {
                                background: #f0fdf4;
                                color: #15803d;
                                border: 1px solid #bbf7d0;
                            }

                            .ps-refunded {
                                background: #f3e8ff;
                                color: #7e22ce;
                                border: 1px solid #e9d5ff;
                            }

                            .ps-failed {
                                background: #fef2f2;
                                color: #b91c1c;
                                border: 1px solid #fecaca;
                            }

                            .deposit-progress {
                                height: 8px;
                                border-radius: 8px;
                                background: #e5e7eb;
                                overflow: hidden;
                                margin: 8px 0;
                            }

                            .deposit-progress-fill {
                                height: 100%;
                                border-radius: 8px;
                                background: linear-gradient(90deg, #007ce8, #38bdf8);
                                transition: width 1s ease;
                            }

                            .bk-btn {
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                padding: 9px 18px;
                                border-radius: 50px;
                                font-size: 0.82rem;
                                font-weight: 700;
                                text-decoration: none;
                                border: none;
                                cursor: pointer;
                                transition: all 0.2s;
                                white-space: nowrap;
                            }

                            .bk-btn-primary {
                                background: #007ce8;
                                color: #fff;
                            }

                            .bk-btn-primary:hover {
                                background: #005bb5;
                                color: #fff;
                                transform: translateY(-1px);
                            }

                            .bk-btn-info {
                                background: #0ea5e9;
                                color: #fff;
                            }

                            .bk-btn-info:hover {
                                background: #0284c7;
                                color: #fff;
                                transform: translateY(-1px);
                            }

                            .bk-btn-outline {
                                background: transparent;
                                color: #007ce8;
                                border: 1.5px solid #007ce8;
                            }

                            .bk-btn-outline:hover {
                                background: #eff6ff;
                            }

                            .passenger-chip {
                                display: inline-flex;
                                align-items: center;
                                gap: 5px;
                                background: #eff6ff;
                                color: #1d4ed8;
                                border-radius: 20px;
                                padding: 3px 10px;
                                font-size: 0.76rem;
                                font-weight: 600;
                                border: 1px solid #bfdbfe;
                            }

                            .passenger-chip.child {
                                background: #fdf4ff;
                                color: #7c3aed;
                                border-color: #e9d5ff;
                            }

                            .status-dot {
                                width: 8px;
                                height: 8px;
                                border-radius: 50%;
                                display: inline-block;
                                flex-shrink: 0;
                            }

                            .dot-blue {
                                background: #2563eb;
                            }

                            .dot-orange {
                                background: #d97706;
                            }

                            .dot-green {
                                background: #16a34a;
                            }

                            .dot-red {
                                background: #dc2626;
                            }

                            .dot-gray {
                                background: #9ca3af;
                            }

                            @keyframes pulse-dot {

                                0%,
                                100% {
                                    box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.5);
                                }

                                50% {
                                    box-shadow: 0 0 0 5px transparent;
                                }
                            }

                            .status-dot.pulse {
                                animation: pulse-dot 1.5s infinite;
                            }

                            .bk-filter-tabs {
                                display: flex;
                                gap: 8px;
                                flex-wrap: wrap;
                                margin-bottom: 24px;
                            }

                            .bk-filter-btn {
                                padding: 8px 18px;
                                border: 1.5px solid #e5e7eb;
                                border-radius: 50px;
                                font-size: 0.82rem;
                                font-weight: 600;
                                color: #6b7280;
                                background: white;
                                cursor: pointer;
                                transition: all 0.2s;
                            }

                            .bk-filter-btn.active {
                                border-color: #007ce8;
                                color: #007ce8;
                                background: #eff6ff;
                            }

                            .bk-filter-btn:hover:not(.active) {
                                border-color: #9ca3af;
                                color: #374151;
                            }

                            .bk-stat-pills {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 10px;
                                margin-bottom: 24px;
                            }

                            .bk-stat-pill {
                                background: linear-gradient(135deg, #f0f4ff, #fafbff);
                                border: 1px solid rgba(0, 124, 232, 0.15);
                                border-radius: 14px;
                                padding: 12px 18px;
                                display: flex;
                                align-items: center;
                                gap: 10px;
                            }

                            .bk-stat-pill .pill-val {
                                font-size: 1.4rem;
                                font-weight: 800;
                                background: linear-gradient(135deg, #007CE8, #0056b3);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                            }

                            .bk-stat-pill .pill-lbl {
                                font-size: 0.75rem;
                                color: #718096;
                                font-weight: 600;
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                            }
                        </style>

                        {{-- Stats --}}
                        <div class="bk-stat-pills">
                            <div class="bk-stat-pill">
                                <div>
                                    <div class="pill-val">{{ $bookings->count() }}</div>
                                    <div class="pill-lbl">Đơn tổng</div>
                                </div>
                            </div>
                            <div class="bk-stat-pill">
                                <div>
                                    <div class="pill-val">{{ $activeBookings->count() }}</div>
                                    <div class="pill-lbl">Đang diễn ra</div>
                                </div>
                            </div>
                            <div class="bk-stat-pill">
                                <div>
                                    <div class="pill-val">{{ $bookings->where('payment_status', 'paid_30')->count() }}</div>
                                    <div class="pill-lbl">Chờ thanh toán nốt</div>
                                </div>
                            </div>
                        </div>

                        {{-- Filter buttons --}}
                        @if($bookings->isNotEmpty())
                            <div class="bk-filter-tabs">
                                <button class="bk-filter-btn active" onclick="filterBkCards('all', this)">Tất cả <span
                                        style="background:#e8f0fd;color:#007ce8;border-radius:12px;padding:1px 8px;font-size:0.76rem;font-weight:700;">{{ $bookings->count() }}</span></button>
                                <button class="bk-filter-btn" onclick="filterBkCards('active', this)">Đang diễn ra <span
                                        style="background:#e8f0fd;color:#007ce8;border-radius:12px;padding:1px 8px;font-size:0.76rem;font-weight:700;">{{ $activeBookings->count() }}</span></button>
                                <button class="bk-filter-btn" onclick="filterBkCards('past', this)">Đã kết thúc <span
                                        style="background:#e8f0fd;color:#007ce8;border-radius:12px;padding:1px 8px;font-size:0.76rem;font-weight:700;">{{ $pastBookings->count() }}</span></button>
                                <button class="bk-filter-btn" onclick="filterBkCards('pending_payment', this)">Chờ thanh toán
                                    <span
                                        style="background:#e8f0fd;color:#007ce8;border-radius:12px;padding:1px 8px;font-size:0.76rem;font-weight:700;">{{ $bookings->whereIn('payment_status', ['pending', 'failed', 'paid_30'])->count() }}</span></button>
                            </div>
                        @endif

                        @if($bookings->isEmpty())
                            <div class="content-card" style="text-align:center;padding:60px 20px;">
                                <div
                                    style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#eff6ff,#dbeafe);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;font-size:2.5rem;color:#3b82f6;">
                                    ✈️</div>
                                <h4 class="fw-bold text-dark mb-2">Chưa có chuyến đi nào</h4>
                                <p class="text-muted mb-4">Hãy khám phá và đặt tour ngay để bắt đầu hành trình!</p>
                                <a href="{{ route('frontend.tours.index') }}" class="bk-btn bk-btn-primary"
                                    style="font-size:0.95rem;padding:12px 30px;display:inline-flex;">
                                    <i class="bi bi-compass"></i> Khám phá Tours
                                </a>
                            </div>
                        @else
                            <div id="bk-cards-container">
                                @foreach($bookings as $booking)
                                                @php
                                                    $tour = $booking->tour_schedule->tour ?? null;
                                                    $schedule = $booking->tour_schedule ?? null;
                                                    $primaryImg = $tour?->primaryImage?->image_url
                                                        ?? $tour?->tour_images->firstWhere('is_primary', 1)?->image_url
                                                        ?? $tour?->tour_images->first()?->image_url
                                                        ?? null;
                                                    $tourStatus = $booking->tour_status;
                                                    $paymentStatus = $booking->payment_status ?? 'pending';
                                                    $paymentMethod = $booking->payment_method ?? 'transfer';
                                                    $paymentType = $booking->payment_type ?? 'full';
                                                    $isCancelled = in_array($tourStatus, [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER]);
                                                    $isActive = in_array($tourStatus, [\App\Models\Booking::TOUR_UPCOMING, \App\Models\Booking::TOUR_IN_PROGRESS, \App\Models\Booking::TOUR_CHECKING_IN]);
                                                    $isPendingPay = in_array($paymentStatus, ['pending', 'failed', 'paid_30']);
                                                    $tsConfig = [
                                                        'upcoming' => ['ts-upcoming', 'dot-blue', 'bi-calendar-check-fill', 'Sắp khởi hành'],
                                                        'in_progress' => ['ts-in_progress', 'dot-orange', 'bi-play-circle-fill', 'Đang thực hiện'],
                                                        'checking_in' => ['ts-checking_in', 'dot-green', 'bi-geo-alt-fill', 'Đang Check-in'],
                                                        'completed' => ['ts-completed', 'dot-green', 'bi-check-circle-fill', 'Hoàn thành'],
                                                        'cancelled_by_customer' => ['ts-cancelled', 'dot-red', 'bi-x-circle-fill', 'Đã hủy (Bạn)'],
                                                        'cancelled_by_admin' => ['ts-cancelled', 'dot-red', 'bi-x-circle-fill', 'Đã hủy (Admin)'],
                                                    ];
                                                    $tsCfg = $tsConfig[$tourStatus] ?? ['ts-upcoming', 'dot-gray', 'bi-question-circle', $tourStatus];
                                                    $tabAttr = 'all';
                                                    if ($isActive)
                                                        $tabAttr .= ' active';
                                                    if (!$isActive)
                                                        $tabAttr .= ' past';
                                                    if ($isPendingPay)
                                                        $tabAttr .= ' pending_payment';
                                                    $depositAmt = $booking->deposit_amount;
                                                    $remainAmt = $booking->remaining_amount;
                                                    $paidAmt = (float) ($booking->paid_amount ?? 0);
                                                    $paidPercent = $booking->paid_percent;
                                                @endphp
                                                <div class="bk-card" data-bk-tab="{{ $tabAttr }}">
                                                    <div class="bk-card-header">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div>
                                                                <div class="bk-order-id"><i
                                                                        class="bi bi-hash text-primary me-1"></i>{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                                                </div>
                                                                <div class="bk-date-text mt-1"><i
                                                                        class="bi bi-clock me-1"></i>{{ $booking->created_at->format('H:i — d/m/Y') }}
                                                                </div>
                                                            </div>
                                                            @if($tourStatus === 'in_progress' || $tourStatus === 'checking_in')
                                                                <div
                                                                    style="width:8px;height:8px;border-radius:50%;background:#d97706;animation:pulse-dot 1.5s infinite;">
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <span class="ts-badge {{ $tsCfg[0] }}">
                                                                <span
                                                                    class="status-dot {{ $tsCfg[1] }} {{ in_array($tourStatus, ['in_progress', 'checking_in']) ? 'pulse' : '' }}"></span>
                                                                <i class="bi {{ $tsCfg[2] }}"></i>
                                                                {{ $tsCfg[3] }}
                                                            </span>
                                                            @if($paymentType === 'deposit')
                                                                <span
                                                                    style="font-size:0.75rem;font-weight:600;color:#7c3aed;background:#fdf4ff;border:1px solid #e9d5ff;border-radius:20px;padding:3px 10px;">
                                                                    <i class="bi bi-layers-half me-1"></i>Đặt cọc 30%
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="p-4" style="padding:28px !important;">
                                                        <div class="row g-4">
                                                            <div class="col-lg-7">
                                                                <div class="d-flex gap-3 align-items-start">
                                                                    @if($primaryImg)
                                                                        <img src="{{ $primaryImg }}" alt="{{ $tour->title }}"
                                                                            class="bk-tour-img d-none d-sm-block">
                                                                    @else
                                                                        <div class="bk-tour-img-placeholder d-none d-sm-flex">🏔️</div>
                                                                    @endif
                                                                    <div class="flex-grow-1">
                                                                        <div class="text-muted small fw-600 mb-1"
                                                                            style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">
                                                                            Thông tin Tour</div>
                                                                        <h6 class="fw-bold text-dark mb-2">
                                                                            {{ $tour->title ?? 'Tour không tồn tại' }}</h6>
                                                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                                                            @if($tour?->destination)
                                                                                <span
                                                                                    style="font-size:0.8rem;color:#6b7280;display:flex;align-items:center;gap:4px;">
                                                                                    <i
                                                                                        class="bi bi-geo-alt-fill text-danger"></i>{{ $tour->destination->name }}
                                                                                </span>
                                                                            @endif
                                                                            @if($schedule)
                                                                                <span
                                                                                    style="font-size:0.8rem;color:#6b7280;display:flex;align-items:center;gap:4px;">
                                                                                    <i
                                                                                        class="bi bi-calendar-event-fill text-success"></i>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                                                                                    @if($tour?->departure_time)
                                                                                        ({{ \Carbon\Carbon::parse($tour->departure_time)->format('H\hi') }})
                                                                                    @endif
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                                                            <span class="passenger-chip"><i
                                                                                    class="bi bi-person-fill"></i>{{ $booking->adults_count }}
                                                                                người lớn</span>
                                                                            @if($booking->children_count > 0)
                                                                                <span class="passenger-chip child"><i
                                                                                        class="bi bi-person-fill"></i>{{ $booking->children_count }}
                                                                                    trẻ em</span>
                                                                            @endif
                                                                        </div>
                                                                        {{-- Điểm tập kết --}}
                                                                        <div class="d-flex align-items-start gap-2 mb-2 small">
                                                                            <i class="bi bi-geo-alt-fill text-success fs-5"></i>

                                                                            <div>
                                                                                <span class="fw-600 text-dark">
                                                                                    Điểm tập kết:
                                                                                </span>

                                                                                <span class="text-muted">
                                                                                    {{ $booking->meeting_point
                                    ?: ($booking->tour_schedule?->tour?->meeting_point ?? 'Chưa cập nhật') }}
                                                                                </span>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Nút xem chi tiết --}}
                                                                        <div class="mt-2">
                                                                            <a href="{{ route('user.bookings.detail', $booking->id) }}"
                                                                                class="bk-btn bk-btn-outline"
                                                                                style="font-size:0.78rem;padding:6px 14px;">
                                                                                <i class="bi bi-eye"></i>
                                                                                Xem chi tiết
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-5">
                                                                <div class="payment-box h-100">
                                                                    <div class="fw-bold text-dark mb-3 d-flex align-items-center gap-2"
                                                                        style="font-size:0.88rem;">
                                                                        <i class="bi bi-receipt text-primary"></i>Chi tiết thanh toán
                                                                    </div>
                                                                    @if($booking->discount_amount > 0)
                                                                        <div class="payment-row">
                                                                            <span class="label"><i class="bi bi-tag-fill me-1 text-success"></i>Giảm
                                                                                giá</span>
                                                                            <span
                                                                                class="value text-success">-{!! format_currency($booking->discount_amount) !!}</span>
                                                                        </div>
                                                                    @endif
                                                                    <div class="payment-total-row">
                                                                        <span class="payment-total-label">Tổng cộng</span>
                                                                        <span
                                                                            class="payment-total-value">{!! format_currency($booking->total_price) !!}</span>
                                                                    </div>
                                                                    @if($paymentType === 'deposit')
                                                                        <div class="mt-3">
                                                                            <div class="d-flex justify-content-between mb-1"
                                                                                style="font-size:0.78rem;font-weight:600;color:#6b7280;">
                                                                                <span>Tiến độ thanh toán</span>
                                                                                <span class="text-primary">{{ $paidPercent }}%</span>
                                                                            </div>
                                                                            <div class="deposit-progress">
                                                                                <div class="deposit-progress-fill"
                                                                                    style="width:{{ $paidPercent }}%;"></div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    @if($paymentStatus === 'paid_100' || $paymentStatus === 'paid_30')
                                                                        @if($isCancelled && $booking->refund_request)
                                                                            @if($booking->refund_request->status === 'completed')
                                                                                <div class="ps-badge ps-refunded"><i class="bi bi-arrow-return-left"></i>Đã hoàn tiền</div>
                                                                            @elseif($booking->refund_request->status === 'pending')
                                                                                <div class="ps-badge ps-pending"><i class="bi bi-hourglass-split"></i>Chờ hoàn tiền</div>
                                                                            @else
                                                                                <div class="ps-badge ps-failed"><i class="bi bi-x-circle-fill"></i>Từ chối hoàn tiền</div>
                                                                            @endif
                                                                        @else
                                                                            @if($paymentStatus === 'paid_100')
                                                                                <div class="ps-badge ps-paid100"><i class="bi bi-check-circle-fill"></i>Đã thanh toán 100%</div>
                                                                            @else
                                                                                <div class="ps-badge ps-paid30"><i class="bi bi-pie-chart-fill"></i>Đã thanh toán 30% (Cọc)</div>
                                                                                @if(!$isCancelled)
                                                                                    <a href="{{ route('user.bookings.detail', $booking->id) }}#pay70Section" class="bk-btn bk-btn-info w-100 justify-content-center mt-2">
                                                                                        <i class="bi bi-credit-card-fill"></i>Thanh toán 70% còn lại
                                                                                    </a>
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    @elseif($paymentStatus === 'pending')
                                                                        <div class="ps-badge ps-pending">
                                                                            <i class="bi bi-hourglass-split"></i>
                                                                            {{ $paymentMethod === 'vnpay' ? 'Chưa thanh toán' : 'Chờ xác nhận' }}
                                                                        </div>
                                                                        @if(!$isCancelled && $paymentMethod === 'vnpay')
                                                                            <a href="{{ route('frontend.bookings.pay_vnpay', $booking->id) }}"
                                                                                class="bk-btn bk-btn-primary w-100 justify-content-center mt-2">
                                                                                <i class="bi bi-credit-card-fill"></i>
                                                                                {{ $paymentType === 'deposit' ? 'Đặt cọc 30%' : 'Thanh toán ngay' }}
                                                                            </a>
                                                                        @elseif(!$isCancelled && $paymentMethod === 'transfer')
                                                                            <div class="mt-2 text-center small text-muted"><i
                                                                                    class="bi bi-info-circle me-1"></i>Vui lòng chuyển khoản theo thông
                                                                                tin đã nhận qua email.</div>
                                                                        @endif
                                                                    @elseif($paymentStatus === 'failed')
                                                                        <div class="ps-badge ps-failed"><i class="bi bi-x-circle-fill"></i>Thanh
                                                                            toán thất bại</div>
                                                                        @if(!$isCancelled)
                                                                            <a href="{{ route('frontend.bookings.pay_vnpay', $booking->id) }}"
                                                                                class="bk-btn bk-btn-outline w-100 justify-content-center mt-2">
                                                                                <i class="bi bi-arrow-clockwise"></i>Thử lại thanh toán
                                                                            </a>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-center mt-4" id="booking-pagination"></div>
                        @endif
                    </div>{{-- end tab-bookings --}}

                    {{-- ===== TAB: TOUR ĐÃ LƯU ===== --}}
                    <div id="tab-wishlists" style="display:none;">
                        <style>
                            #tab-wishlists .favorite-form {
                                position: absolute !important;
                                top: 12px !important;
                                right: 12px !important;
                                left: auto !important;
                                z-index: 9999 !important;
                                margin: 0 !important;
                            }

                            #tab-wishlists .favorite-btn {
                                width: 42px !important;
                                height: 42px !important;
                                border: none !important;
                                border-radius: 50% !important;
                                background: #ffffff !important;
                                color: #ff3366 !important;
                                display: flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
                                cursor: pointer !important;
                                padding: 0 !important;
                                outline: none !important;
                                transition: all 0.3s ease !important;
                            }

                            #tab-wishlists .favorite-btn i {
                                font-size: 20px !important;
                                line-height: 1 !important;
                                display: flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                            }

                            #tab-wishlists .favorite-btn:hover {
                                background: #ff3366 !important;
                                color: #ffffff !important;
                            }

                            #tab-wishlists .favorite-btn.active {
                                background: #ffffff !important;
                                color: #ff3366 !important;
                            }

                            #tab-wishlists .favorite-btn.active:hover {
                                background: #ff3366 !important;
                                color: #ffffff !important;
                            }

                            /* Custom Pagination Styles */
                            .pagination {
                                margin-bottom: 0;
                                gap: 5px;
                            }

                            .pagination .page-link {
                                border-radius: 50% !important;
                                margin: 0 2px;
                                width: 38px;
                                height: 38px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #4b5563;
                                font-size: 0.88rem;
                                transition: all 0.2s;
                                background: #fff;
                                border: 1px solid #e5e7eb;
                            }

                            .pagination .page-link:hover {
                                background-color: #f3f4f6;
                                color: #007CE8;
                                border-color: #e5e7eb;
                            }

                            .pagination .page-item.active .page-link {
                                background: #007CE8 !important;
                                color: #fff !important;
                                border-color: #007CE8 !important;
                                box-shadow: 0 4px 10px rgba(0, 124, 232, 0.3);
                            }

                            .pagination .page-item.disabled .page-link {
                                background: #f9fafb;
                                color: #d1d5db;
                                border-color: #e5e7eb;
                                cursor: not-allowed;
                            }
                        </style>

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h5 class="fw-800 mb-1" style="color:#0B132B;">Tour đã lưu</h5>
                                <p class="text-muted small mb-0">{{ $wishlists->count() }} tour yêu thích của bạn</p>
                            </div>
                            <a href="{{ route('frontend.tours.index') }}"
                                class="btn btn-sm btn-primary rounded-pill px-4 fw-600">
                                <i class="bi bi-plus-circle me-1"></i> Khám phá thêm
                            </a>
                        </div>

                        @if($wishlists->isEmpty())
                            <div class="content-card" style="text-align:center;padding:60px 20px;">
                                <div
                                    style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,rgba(239,68,68,0.08),rgba(220,38,38,0.05));display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;font-size:2.5rem;color:#ef4444;">
                                    &#9825;</div>
                                <h4 class="fw-800 mb-2" style="color:#0B132B;">Chưa có tour yêu thích</h4>
                                <p class="text-muted mb-4">Lưu những tour bạn thích để tìm lại dễ dàng hơn!</p>
                                <a href="{{ route('frontend.tours.index') }}"
                                    class="btn btn-primary rounded-pill px-4 py-2 fw-600">
                                    <i class="bi bi-compass me-1"></i> Khám phá tour ngay
                                </a>
                            </div>
                        @else
                                <div class="row g-4" id="wishlist-cards-container">
                                    @foreach($wishlists as $item)
                                            @php
                                                $t = $item->tour;
                                                $tourTitle = $t?->title ?? 'Tour không còn tồn tại';
                                                $primaryImage = $t?->tour_images?->where('is_primary', 1)->first()
                                                    ?? $t?->tour_images?->first();
                                                $tourImage = 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';
                                                if ($primaryImage && !empty($primaryImage->image_url)) {
                                                    if (\Illuminate\Support\Str::startsWith($primaryImage->image_url, ['http://', 'https://'])) {
                                                        $tourImage = $primaryImage->image_url;
                                                    } else {
                                                        $tourImage = asset(ltrim($primaryImage->image_url, '/'));
                                                    }
                                                }
                                                $destinationName = optional($t?->destination)->name ?: 'Việt Nam';
                                                $tourSlug = $t?->slug ?? $t?->id;
                                                $stars = ($t && $t->reviews()->avg('rating')) ? round($t->reviews()->avg('rating')) : 0;
                                            @endphp
                                            <div class="col-12 col-md-6 col-lg-4 wishlist-card-item">
                                                {{-- Wrap link for card navigation --}}
                                                @if($t)
                                                    <a href="{{ route('frontend.tours.show', $tourSlug) }}"
                                                        class="text-decoration-none h-100 d-block">
                                                @else
                                                        <div class="h-100 d-block">
                                                    @endif
                                                        <div class="combo-card" style="overflow:visible;">
                                                            <div class="combo-card-img-wrapper">
                                                                {{-- Remove button (unfavorite) --}}
                                                                <form action="{{ $t ? route('frontend.favorites.destroy', $t->id) : '#' }}"
                                                                    method="POST" class="favorite-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="favorite-btn active" title="Bỏ lưu"
                                                                        onclick="event.stopPropagation(); event.preventDefault(); if(confirm('Bỏ lưu tour này?')) { this.closest('form').submit(); }">
                                                                        <i class="bi bi-heart-fill"></i>
                                                                    </button>
                                                                </form>

                                                                <img src="{{ $tourImage }}" alt="{{ $tourTitle }}"
                                                                    onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800';">
                                                            </div>
                                                            <div class="combo-card-body">
                                                                <h3 class="combo-title">{{ $tourTitle }}</h3>
                                                                <div class="combo-stars">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <i
                                                                            class="bi {{ $i <= $stars ? 'bi-star-fill' : 'bi-star' }} text-warning"></i>
                                                                    @endfor
                                                                </div>
                                                                <div class="combo-location">
                                                                    <i class="bi bi-geo-alt"></i>
                                                                    <span>{{ $destinationName }}</span>
                                                                </div>
                                                                <div class="combo-footer">
                                                                    <div>
                                                                        <div class="combo-price-label">Giá từ:</div>
                                                                        <div class="combo-price-val">
                                                                            {{ format_currency($t?->getBasePrice() > 0 ? $t->getBasePrice() : ($t?->base_price ?? 0)) }}</div>
                                                                    </div>
                                                                    <span class="btn-combo-detail">Xem chi tiết</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($t)
                                                            </a>
                                                        @else
                                                    </div>
                                                @endif
                                        </div>
                                    @endforeach
                            </div>
                            <div class="d-flex justify-content-center mt-4" id="wishlist-pagination"></div>
                        @endif
                </div>{{-- end tab-wishlists --}}

                {{-- ===== TAB: THÔNG BÁO ===== --}}
                <div id="tab-notifications" style="display: none;" x-data="notificationComponent()" x-init="init()">
                    <div class="content-card reveal-up">
                        <div class="content-card-header d-flex flex-wrap justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                                <div class="header-icon">
                                    <i class="bi bi-bell-fill"></i>
                                </div>
                                <div>
                                    <h5 class="fw-800 mb-0" style="color:#0B132B;">Thông báo của bạn</h5>
                                    <p class="text-muted small mb-0">Cập nhật các thông tin mới nhất</p>
                                </div>
                            </div>
                            <button @click="markAllAsRead()" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" x-show="unreadCount > 0" x-cloak>
                                <i class="bi bi-check-all me-1"></i> Đánh dấu tất cả đã đọc
                            </button>
                        </div>

                        <div class="notifications-list mt-3">
                            <template x-if="notifications.length === 0">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-bell-slash fs-1 d-block mb-3" style="opacity: 0.5;"></i>
                                    <p class="fw-bold">Bạn chưa có thông báo nào.</p>
                                </div>
                            </template>
                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="p-3 border-bottom d-flex align-items-start gap-3 rounded mb-2 transition-all" 
                                     :class="{'bg-light border': !notification.read_at, 'border-0': notification.read_at}"
                                     style="transition: all 0.3s ease;">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" 
                                         style="width: 45px; height: 45px; font-size: 1.2rem;" 
                                         :class="getIconBgClass(notification.data ? notification.data.type : notification.type)">
                                        <i :class="getIconClass(notification.data ? notification.data.type : notification.type)"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0" style="font-weight: 700; color: #1a2b4c;" 
                                                x-text="notification.data ? (notification.data.title || 'Thông báo mới') : (notification.title || 'Thông báo mới')"></h6>
                                            <small class="text-muted" x-text="new Date(notification.created_at).toLocaleString('vi-VN')"></small>
                                        </div>
                                        <p class="mb-2 text-secondary" style="font-size: 0.95rem; line-height: 1.4;" 
                                           x-text="notification.data ? notification.data.message : notification.message"></p>
                                        
                                        <div class="d-flex gap-2 mt-2">
                                            <a :href="notification.data ? (notification.data.link || '#') : (notification.link || '#')" 
                                               @click="markAsRead(notification.id)"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" style="font-size: 0.8rem;"
                                               x-show="(notification.data && notification.data.link) || notification.link">
                                                Xem chi tiết
                                            </a>
                                            <button @click="markAsRead(notification.id)" 
                                                    class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-secondary" 
                                                    style="font-size: 0.8rem;" 
                                                    x-show="!notification.read_at">
                                                Đánh dấu đã đọc
                                            </button>
                                        </div>
                                    </div>
                                    <div x-show="!notification.read_at" class="ms-2">
                                        <span class="p-1 bg-primary rounded-circle d-inline-block shadow-sm"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>{{-- end tab-notifications --}}

            </div>{{-- end col-lg-9 --}}
        </div>{{-- end row --}}
    </div>{{-- end container --}}
    </div>{{-- end profile-page --}}

    <script>
        // ===== Profile Tab Switching =====
        function switchProfileTab(tab, e) {
            if (e) e.preventDefault();
            const tabs = ['info', 'bookings', 'wishlists', 'notifications'];
            tabs.forEach(t => {
                const el = document.getElementById('tab-' + t);
                const nav = document.getElementById('nav-' + t);
                if (el) el.style.display = (t === tab) ? '' : 'none';
                if (nav) {
                    if (t === tab) {
                        nav.classList.add('active');
                    } else {
                        nav.classList.remove('active');
                    }
                }
            });
            // Update URL without reload
            const url = new URL(window.location);
            if (tab === 'info') {
                url.searchParams.delete('tab');
            } else {
                url.searchParams.set('tab', tab);
            }
            history.replaceState({}, '', url);

            // Re-apply booking pagination when switching to bookings tab
            if (tab === 'bookings' && typeof window.bookingApply === 'function') {
                bookingCurrentPage = 1;
                currentBookingFilter = 'all';
                // Reset filter button UI
                document.querySelectorAll('.bk-filter-btn').forEach((b, i) => {
                    b.classList.toggle('active', i === 0);
                });
                window.bookingApply(1);
            }
        }

        // ===== Booking & Wishlist JS Pagination =====
        let wishlistCurrentPage = 1;
        const wishlistItemsPerPage = 6;

        function initWishlistPagination() {
            const container = document.getElementById('wishlist-cards-container');
            if (!container) return;
            const items = Array.from(container.querySelectorAll('.wishlist-card-item'));
            if (items.length === 0) return;

            const totalPages = Math.ceil(items.length / wishlistItemsPerPage);

            function showPage(page) {
                wishlistCurrentPage = page;
                const start = (page - 1) * wishlistItemsPerPage;
                const end = start + wishlistItemsPerPage;

                items.forEach((item, index) => {
                    item.style.display = (index >= start && index < end) ? '' : 'none';
                });

                renderPagination();
            }

            function renderPagination() {
                const paginationContainer = document.getElementById('wishlist-pagination');
                if (!paginationContainer) return;

                if (totalPages <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                let html = '<nav><ul class="pagination justify-content-center mb-0">';
                html += `<li class="page-item ${wishlistCurrentPage === 1 ? 'disabled' : ''}">`;
                html += `<a class="page-link" href="#" onclick="window.changeWishlistPage(${wishlistCurrentPage - 1}, event)"><i class="bi bi-chevron-left"></i></a></li>`;

                for (let i = 1; i <= totalPages; i++) {
                    html += `<li class="page-item ${wishlistCurrentPage === i ? 'active' : ''}">`;
                    html += `<a class="page-link fw-bold" href="#" onclick="window.changeWishlistPage(${i}, event)">${i}</a></li>`;
                }

                html += `<li class="page-item ${wishlistCurrentPage === totalPages ? 'disabled' : ''}">`;
                html += `<a class="page-link" href="#" onclick="window.changeWishlistPage(${wishlistCurrentPage + 1}, event)"><i class="bi bi-chevron-right"></i></a></li>`;
                html += '</ul></nav>';
                paginationContainer.innerHTML = html;
            }

            window.changeWishlistPage = function (page, e) {
                if (e) e.preventDefault();
                if (page >= 1 && page <= totalPages) showPage(page);
            };

            showPage(1);
        }

        let bookingCurrentPage = 1;
        const bookingItemsPerPage = 4;
        let currentBookingFilter = 'all';

        function initBookingPagination() {
            const tabEl = document.getElementById('tab-bookings');
            if (!tabEl) return;
            const allCards = Array.from(tabEl.querySelectorAll('.bk-card'));
            if (allCards.length === 0) return;

            function applyPagination(page) {
                page = page || 1;
                bookingCurrentPage = page;

                const visibleCards = allCards.filter(card => {
                    const bkTab = card.dataset.bkTab || '';
                    return currentBookingFilter === 'all' || bkTab.includes(currentBookingFilter);
                });

                const totalPages = Math.max(1, Math.ceil(visibleCards.length / bookingItemsPerPage));

                // Hide all cards
                allCards.forEach(card => { card.style.display = 'none'; });

                // Show only cards on current page
                const start = (page - 1) * bookingItemsPerPage;
                visibleCards.slice(start, start + bookingItemsPerPage).forEach(card => {
                    card.style.display = 'block';
                });

                // Render pagination controls
                const paginationEl = document.getElementById('booking-pagination');
                if (!paginationEl) return;

                if (totalPages <= 1) {
                    paginationEl.innerHTML = '';
                    return;
                }

                let html = '<nav aria-label="Booking pagination"><ul class="pagination justify-content-center mb-0">';
                html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">`;
                html += `<a class="page-link" href="#" onclick="window.changeBookingPage(${page - 1}, event)"><i class="bi bi-chevron-left"></i></a></li>`;

                for (let i = 1; i <= totalPages; i++) {
                    html += `<li class="page-item ${page === i ? 'active' : ''}">`;
                    html += `<a class="page-link fw-bold" href="#" onclick="window.changeBookingPage(${i}, event)">${i}</a></li>`;
                }

                html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">`;
                html += `<a class="page-link" href="#" onclick="window.changeBookingPage(${page + 1}, event)"><i class="bi bi-chevron-right"></i></a></li>`;
                html += '</ul></nav>';
                paginationEl.innerHTML = html;
            }

            // Expose globally
            window.bookingApply = applyPagination;

            window.changeBookingPage = function (page, e) {
                if (e) e.preventDefault();
                const visibleCount = allCards.filter(c =>
                    currentBookingFilter === 'all' || (c.dataset.bkTab || '').includes(currentBookingFilter)
                ).length;
                const total = Math.max(1, Math.ceil(visibleCount / bookingItemsPerPage));
                if (page >= 1 && page <= total) applyPagination(page);
            };

            window.filterBkCards = function (tab, btn) {
                document.querySelectorAll('.bk-filter-btn').forEach(b => b.classList.remove('active'));
                if (btn) btn.classList.add('active');
                currentBookingFilter = tab;
                applyPagination(1);
            };

            applyPagination(1);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // ===== Init tab from URL param FIRST =====
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'info';
            switchProfileTab(activeTab, null);

            // ===== Initialize JS Pagination AFTER tab is visible =====
            initWishlistPagination();
            initBookingPagination();

            // ===== Scroll reveal =====
            const revealEls = document.querySelectorAll('.reveal-up');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08 });
            revealEls.forEach(el => observer.observe(el));

            // ===== Avatar Modal =====
            const overlay = document.getElementById('avatarModalOverlay');
            const openBtn = document.getElementById('openAvatarModal');
            const closeBtn = document.getElementById('closeAvatarModal');
            const cancelBtn = document.getElementById('cancelAvatarBtn');
            const fileInput = document.getElementById('avatarFileInput');
            const uploadBtn = document.getElementById('uploadAvatarBtn');
            const dropZone = document.getElementById('dropZone');
            const preview = document.getElementById('avatarPreview');

            function openModal() { overlay.classList.add('active'); }
            function closeModal() {
                overlay.classList.remove('active');
                fileInput.value = '';
                uploadBtn.disabled = true;
                // Reset preview to original
                resetPreview();
            }

            function resetPreview() {
                @if($user->avatar)
                    if (preview.tagName === 'IMG') {
                        preview.src = "{{ $user->avatar }}";
                    }
                @endif
            }

            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) closeModal();
            });

            function updateUserAvatars(imageSrc) {
                document.querySelectorAll('[data-user-avatar]').forEach(function (el) {
                    el.innerHTML = '<img src="' + imageSrc + '" alt="Ảnh đại diện" class="rounded-circle w-100 h-100" style="object-fit:cover;width:100%;height:100%;">';
                });
            }

            // File input change
            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Ảnh quá lớn! Vui lòng chọn ảnh dưới 10MB.');
                        this.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewWrap = document.getElementById('previewWrap');
                        // Xóa nội dung cũ (img hoặc div initials) và thay bằng ảnh preview
                        previewWrap.innerHTML = '';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Preview';
                        img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
                        previewWrap.appendChild(img);
                        updateUserAvatars(e.target.result);
                    };
                    reader.readAsDataURL(file);
                    uploadBtn.disabled = false;
                }
            });

            // Drag & Drop
            dropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            dropZone.addEventListener('dragleave', function () {
                this.classList.remove('dragover');
            });
            dropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });

            // ===== Toggle Password Visibility =====
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.dataset.target;
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    }
                });
            });

            // ===== Password Strength =====
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const matchText = document.getElementById('matchText');

            function checkPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.length >= 12) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                return strength;
            }

            newPasswordInput.addEventListener('input', function () {
                const val = this.value;
                if (!val) {
                    strengthBar.style.width = '0';
                    strengthText.textContent = '';
                    return;
                }
                const s = checkPasswordStrength(val);
                const levels = [
                    { w: '20%', c: '#ef4444', t: '⚠️ Rất yếu', tc: '#ef4444' },
                    { w: '40%', c: '#f97316', t: '⚠️ Yếu', tc: '#f97316' },
                    { w: '60%', c: '#eab308', t: '👍 Trung bình', tc: '#eab308' },
                    { w: '80%', c: '#22c55e', t: '✅ Mạnh', tc: '#22c55e' },
                    { w: '100%', c: '#16a34a', t: '🔒 Rất mạnh', tc: '#16a34a' },
                ];
                const lvl = levels[Math.min(s - 1, 4)];
                strengthBar.style.width = lvl.w;
                strengthBar.style.background = lvl.c;
                strengthText.textContent = lvl.t;
                strengthText.style.color = lvl.tc;
            });

            confirmPasswordInput.addEventListener('input', function () {
                const pw = newPasswordInput.value;
                const cpw = this.value;
                if (!cpw) { matchText.textContent = ''; return; }
                if (pw === cpw) {
                    matchText.textContent = '✅ Mật khẩu khớp';
                    matchText.style.color = '#22c55e';
                } else {
                    matchText.textContent = '❌ Mật khẩu không khớp';
                    matchText.style.color = '#ef4444';
                }
            });
        });
    </script>
@endsection