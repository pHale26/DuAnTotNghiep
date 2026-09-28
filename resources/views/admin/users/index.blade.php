@extends('layouts.admin')

@section('page-title', 'Quản lý Tài Khoản')

@section('content')
<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-people me-2 text-primary"></i>Danh sách Tài Khoản</h5>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-admin btn-admin-primary">
                <i class="bi bi-plus-lg me-1"></i> Thêm Tài Khoản
            </a>
        </div>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 80px;">ID</th>
                        <th>Họ và Tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">#{{ $user->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge bg-danger">Admin</span>
                            @elseif($user->role === 'staff')
                                <span class="badge bg-warning text-dark">Nhân viên</span>
                            @elseif($user->role === 'cskh')
                                <span class="badge bg-primary">Nhân viên CSKH</span>
                            @elseif($user->role === 'guide')
                                <span class="badge bg-info text-dark">Hướng dẫn viên</span>
                            @else
                                <span class="badge bg-secondary">Khách hàng</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="btn btn-action text-info bg-info bg-opacity-10" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="btn btn-action text-primary bg-primary bg-opacity-10" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa Tài khoản này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-action text-danger bg-danger bg-opacity-10" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 text-light mb-2 d-block"></i>
                            Chưa có dữ liệu tài khoản nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
