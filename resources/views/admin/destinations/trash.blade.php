@extends('layouts.admin')

@section('page-title', 'Thùng rác - Điểm đến đã xóa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Thùng rác Điểm đến</h4>
    <a href="{{ route('admin.destinations.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
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
                        <th class="ps-4 py-3 text-muted">Hình ảnh</th>
                        <th class="py-3 text-muted">Tên điểm đến</th>
                        <th class="py-3 text-muted">Thời gian xóa</th>
                        <th class="py-3 text-muted text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $dest)
                    <tr>
                        <td class="ps-4">
                            @if($dest->image_url)
                            <img src="{{ $dest->image_url }}" alt="{{ $dest->name }}" class="rounded shadow-sm"
                                style="width: 80px; height: 60px; object-fit: cover;">
                            @else
                            <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center"
                                style="width: 80px; height: 60px;">Trống</div>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">{{ $dest->name }}</td>
                        <td><span class="text-danger"><i class="bi bi-clock-history me-1"></i>{{ $dest->deleted_at->format('d/m/Y H:i') }}</span></td>
                        <td class="text-end pe-4">
                            <!-- Nút Khôi phục -->
                            <form action="{{ route('admin.destinations.restore', $dest->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success text-white shadow-sm" title="Khôi phục điểm đến này">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>

                            <!-- Nút Xóa vĩnh viễn -->
                            <form action="{{ route('admin.destinations.force-delete', $dest->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('CẢNH BÁO: Hành động này không thể hoàn tác! Toàn bộ dữ liệu của điểm đến này sẽ biến mất vĩnh viễn. Bạn có chắc chắn không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger text-white shadow-sm ms-1" title="Xóa vĩnh viễn">
                                    <i class="bi bi-x-circle"></i> Xóa luôn
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-trash fs-1 text-light mb-3 d-block"></i>
                            Thùng rác hiện đang trống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $destinations->links() }}
</div>
@endsection
