@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Tài Khoản')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="admin-card border-0 mb-4">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa Tài Khoản: {{ $user->name }}</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu mới (Để trống nếu không đổi)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nhập lại Mật khẩu</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if(auth()->user()->role === 'admin')
                    <div class="mb-4">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                            <option value="guide" {{ old('role', $user->role) == 'guide' ? 'selected' : '' }}>Hướng dẫn viên</option>
                            <option value="cskh" {{ old('role', $user->role) == 'cskh' ? 'selected' : '' }}>Nhân viên CSKH</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @else
                        <!-- Staff cannot edit role -->
                        <div class="mb-4">
                            <label class="form-label">Vai trò</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                            <div class="form-text">Bạn không có quyền thay đổi vai trò của tài khoản này.</div>
                        </div>
                    @endif

                    <hr class="my-4">
                    <h6 class="mb-3 text-primary"><i class="bi bi-person-vcard me-2"></i>Thông tin Định danh (CCCD)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số CCCD</label>
                            <input type="text" name="identity_number" class="form-control @error('identity_number') is-invalid @enderror" value="{{ old('identity_number', optional($user->user_identity)->identity_number) }}">
                            @error('identity_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và Tên (Trên thẻ)</label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', optional($user->user_identity)->full_name) }}">
                            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', optional($user->user_identity)->date_of_birth ? $user->user_identity->date_of_birth->format('Y-m-d') : '') }}">
                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">-- Chọn --</option>
                                <option value="Nam" {{ in_array(strtolower(old('gender', optional($user->user_identity)->gender)), ['nam', 'male']) ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ in_array(strtolower(old('gender', optional($user->user_identity)->gender)), ['nữ', 'nu', 'female']) ? 'selected' : '' }}>Nữ</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày hết hạn giấy tờ</label>
                            <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date', optional($user->user_identity)->expiry_date ? $user->user_identity->expiry_date->format('Y-m-d') : '') }}">
                            @error('expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
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
                        <span class="fw-medium">{{ $user->last_seen_at ? $user->last_seen_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-0">
                        <span class="text-muted"><i class="bi bi-calendar-plus me-2"></i> Ngày tạo</span>
                        <span class="fw-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
