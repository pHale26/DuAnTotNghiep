@extends('layouts.admin')

@section('page-title', 'Quản lý Danh mục Tour')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Danh sách Danh mục</h4>
    <div>
        <a href="{{ route('admin.categories.trash') }}" class="btn btn-admin btn-light border text-danger me-2">
            <i class="bi bi-trash"></i> Thùng rác
        </a>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm Danh mục mới
        </a>
    </div>
</div>

<!-- Hiển thị thông báo thành công -->
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-600">ID</th>
                        <th class="py-3 text-muted fw-600">Tên danh mục</th>
                        <th class="py-3 text-muted fw-600">Đường dẫn (Slug)</th>
                        <th class="py-3 text-muted fw-600">Ngày tạo</th>
                        <th class="py-3 text-muted fw-600 text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="ps-4">{{ $cat->id }}</td>
                        <td class="fw-bold text-dark">{{ $cat->name }}</td>
                        <td><span class="badge bg-light text-secondary border">{{ $cat->slug }}</span></td>
                        <td>{{ $cat->created_at->format('d/m/Y') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.categories.edit', $cat->id) }}"
                                class="btn btn-sm btn-white border text-primary" title="Chỉnh sửa">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này? Các tour thuộc danh mục này sẽ bị mất liên kết.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-white border text-danger" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            Chưa có danh mục nào. Hãy thêm danh mục mới!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $categories->links() }}
</div>
@endsection