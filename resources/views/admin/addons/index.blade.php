@extends('layouts.admin')

@section('page-title', 'Quản lý Dịch vụ Addon')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Danh sách Addons</h5>
        <div>
            <a href="{{ route('admin.addons.trash') }}" class="btn btn-admin btn-light border text-danger me-2">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
            <a href="{{ route('admin.addons.create') }}" class="btn-admin btn-admin-primary">
                <i class="bi bi-plus-lg me-1"></i> Thêm Addon
            </a>
        </div>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên dịch vụ</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($addons as $addon)
                    <tr>
                        <td>{{ $addon->id }}</td>
                        <td>
                            @if($addon->image_url)
                                <img src="{{ asset($addon->image_url) }}" alt="{{ $addon->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $addon->name }}</td>
                        <td>{{ number_format($addon->price, 0, ',', '.') }}đ</td>
                        <td>
                            @if($addon->is_active)
                                <span class="badge badge-soft-success">Hoạt động</span>
                            @else
                                <span class="badge badge-soft-secondary">Tạm ẩn</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.addons.edit', $addon->id) }}" class="btn btn-sm btn-action text-primary" title="Sửa">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.addons.destroy', $addon->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Chưa có dịch vụ Addon nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($addons->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $addons->links() }}
    </div>
    @endif
</div>
@endsection
