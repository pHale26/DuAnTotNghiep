@extends('layouts.admin')

@section('page-title', 'Quản lý Điểm đến và khởi hành')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-dark fs-5 fw-bold">Danh sách Điểm đến và khởi hành</div>
    <div>
        <a href="{{ route('admin.destinations.trash') }}" class="btn btn-admin btn-light border text-danger me-2">
            <i class="bi bi-trash"></i> Thùng rác
        </a>
        <a href="{{ route('admin.destinations.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm Điểm đến và khởi hành
        </a>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted">ID</th>
                        <th class="py-3 text-muted">Hình ảnh</th>
                        <th class="py-3 text-muted">Tên điểm</th>
                        <th class="py-3 text-muted">Mô tả</th>
                        <th class="py-3 text-muted text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $dest)
                    <tr>
                        <td class="ps-4">{{ $dest->id }}</td>
                        <td>
                            @if($dest->image_url)
                            <img src="{{ $dest->image_url }}" alt="{{ $dest->name }}" class="rounded shadow-sm"
                                style="width: 80px; height: 60px; object-fit: cover;">
                            @else
                            <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center"
                                style="width: 80px; height: 60px;">Trống</div>
                            @endif
                        </td>
                        <td class="text-dark fw-bold">{{ $dest->name }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($dest->description, 50) }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.destinations.edit', $dest->id) }}"
                                class="btn btn-sm btn-white border text-primary" title="Chỉnh sửa">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.destinations.destroy', $dest->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Anh có chắc chắn muốn xóa điểm này? Các tour thuộc điểm này có thể bị ảnh hưởng.');">
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
                            Chưa có điểm đến nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Hiển thị {{ $destinations->firstItem() }} – {{ $destinations->lastItem() }} / {{ $destinations->total() }} điểm đến
            </small>
            <div>
                {{ $destinations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection