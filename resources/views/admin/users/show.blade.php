@extends('layouts.admin')

@section('page-title', 'Chi tiết Tài Khoản')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Thông tin cơ bản -->
        <div class="admin-card border-0 mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="admin-card-header p-4" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border-bottom: none;">
                <div class="d-flex align-items-center">
                    <div class="position-relative me-3">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="Avatar" class="rounded-circle border border-4 border-white shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-4 border-white shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">{{ $user->name }}</h5>
                        <p class="text-muted mb-2 small">{{ $user->email }}</p>
                        @if($user->role === 'admin')
                            <span class="badge bg-secondary px-2 py-1 shadow-sm">Admin</span>
                        @elseif($user->role === 'staff')
                            <span class="badge bg-secondary px-2 py-1 shadow-sm">Nhân viên</span>
                        @elseif($user->role === 'cskh')
                            <span class="badge bg-secondary px-2 py-1 shadow-sm">Nhân viên CSKH</span>
                        @elseif($user->role === 'guide')
                            <span class="badge bg-secondary px-2 py-1 shadow-sm">Hướng dẫn viên</span>
                        @else
                            <span class="badge bg-secondary px-2 py-1 shadow-sm text-light">Khách hàng</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="admin-card-body p-0">
                <ul class="list-group list-group-flush" style="border-top: none;">
                    <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-0 border-bottom">
                        <span class="text-muted"><i class="bi bi-telephone me-2"></i> Số điện thoại</span>
                        <span class="fw-medium">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                    </li>
                    <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-0 border-bottom">
                        <span class="text-muted"><i class="bi bi-circle-fill {{ $user->is_active ? 'text-success' : 'text-danger' }} small me-2"></i> Trạng thái</span>
                        <span class="{{ $user->is_active ? 'text-success' : 'text-danger' }} fw-medium">
                            {{ $user->is_active ? 'Đang hoạt động' : 'Bị khóa' }}
                        </span>
                    </li>
                    <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-0 border-bottom">
                        <span class="text-muted"><i class="bi bi-clock-history me-2"></i> Hoạt động gần nhất</span>
                        <span class="fw-medium">{{ $user->last_seen_at ? $user->last_seen_at->format('d/m/Y H:i') : 'Chưa có' }}</span>
                    </li>
                    <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-0">
                        <span class="text-muted"><i class="bi bi-calendar-plus me-2"></i> Ngày tạo</span>
                        <span class="fw-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Thông tin Căn cước công dân -->
        <div class="admin-card border-0 mb-4">
            <div class="admin-card-header bg-white py-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="admin-card-title mb-1 fw-bold text-dark">Thông tin định danh</h5>
                        <div class="text-muted small">CCCD / Hộ chiếu được xác minh. Liên hệ hỗ trợ để thay đổi.</div>
                    </div>
                </div>
                <div>
                    @if($user->user_identity)
                        <span class="badge rounded-pill bg-success px-3 py-2 fw-medium shadow-sm"><i class="bi bi-check-circle-fill me-1"></i>Đã xác minh</span>
                    @else
                        <span class="badge rounded-pill bg-secondary px-3 py-2 fw-medium shadow-sm"><i class="bi bi-x-circle-fill me-1"></i>Chưa xác minh</span>
                    @endif
                </div>
            </div>
            <div class="admin-card-body">
                @if($user->user_identity)
                    <div class="d-flex flex-wrap gap-3">
                        <div class="border rounded p-3 bg-white flex-grow-1 shadow-sm" style="min-width: 200px; border-color: #e5e7eb !important;">
                            <div class="text-secondary small mb-2 text-uppercase fw-semibold" style="font-size: 0.75rem;">Số CCCD / Hộ chiếu</div>
                            <div class="fw-bold text-dark fs-6">{{ $user->user_identity->identity_number }}</div>
                        </div>
                        <div class="border rounded p-3 bg-white flex-grow-1 shadow-sm" style="min-width: 200px; border-color: #e5e7eb !important;">
                            <div class="text-secondary small mb-2 text-uppercase fw-semibold" style="font-size: 0.75rem;">Họ và tên đầy đủ</div>
                            <div class="fw-bold text-dark fs-6">{{ $user->user_identity->full_name }}</div>
                        </div>
                        <div class="border rounded p-3 bg-white flex-grow-1 shadow-sm" style="min-width: 150px; border-color: #e5e7eb !important;">
                            <div class="text-secondary small mb-2 text-uppercase fw-semibold" style="font-size: 0.75rem;">Ngày sinh</div>
                            <div class="fw-bold text-dark fs-6">{{ $user->user_identity->date_of_birth ? $user->user_identity->date_of_birth->format('d/m/Y') : 'N/A' }}</div>
                        </div>
                        <div class="border rounded p-3 bg-white flex-grow-1 shadow-sm" style="min-width: 150px; border-color: #e5e7eb !important;">
                            <div class="text-secondary small mb-2 text-uppercase fw-semibold" style="font-size: 0.75rem;">Giới tính</div>
                            <div class="fw-bold fs-6" style="color: #3b82f6;">
                                @if(strtolower($user->user_identity->gender) === 'nam' || strtolower($user->user_identity->gender) === 'male')
                                    <i class="bi bi-gender-male me-1"></i> Nam
                                @elseif(strtolower($user->user_identity->gender) === 'nữ' || strtolower($user->user_identity->gender) === 'nu' || strtolower($user->user_identity->gender) === 'female')
                                    <i class="bi bi-gender-female me-1"></i> Nữ
                                @else
                                    {{ $user->user_identity->gender ?? 'N/A' }}
                                @endif
                            </div>
                        </div>
                        <div class="border rounded p-3 bg-white flex-grow-1 shadow-sm" style="min-width: 200px; border-color: #e5e7eb !important;">
                            <div class="text-secondary small mb-2 text-uppercase fw-semibold" style="font-size: 0.75rem;">Ngày hết hạn giấy tờ</div>
                            <div class="fw-bold text-dark fs-6">{{ $user->user_identity->expiry_date ? $user->user_identity->expiry_date->format('d/m/Y') : 'N/A' }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-person-vcard fs-1 text-light mb-2 d-block"></i>
                        Người dùng này chưa cập nhật thông tin Căn cước công dân.
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-1"></i> Chỉnh sửa tài khoản</a>
        </div>
    </div>
</div>
@endsection
