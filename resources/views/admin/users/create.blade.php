@extends('layouts.admin')

@section('page-title', 'Thêm Tài Khoản')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="admin-card border-0 mb-4">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>Thêm Tài Khoản Mới</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nhập lại Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if(auth()->user()->role === 'admin')
                    <div class="mb-4">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                            <option value="guide" {{ old('role') == 'guide' ? 'selected' : '' }}>Hướng dẫn viên</option>
                            <option value="cskh" {{ old('role') == 'cskh' ? 'selected' : '' }}>Nhân viên CSKH</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @else
                        <!-- Staff creating user, default is customer -->
                        <div class="mb-4 alert alert-info">
                            <i class="bi bi-info-circle me-1"></i> Tài khoản mới sẽ được gán quyền Khách hàng mặc định.
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu Tài Khoản</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
