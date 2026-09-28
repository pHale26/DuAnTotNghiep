@extends('layouts.admin')

@section('page-title', 'Thùng rác - Tour đã xóa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Thùng rác Tour</h4>
    <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-600">Thông tin Tour</th>
                        <th class="py-3 text-muted fw-600">Điểm đến</th>
                        <th class="py-3 text-muted fw-600">Thời gian xóa</th>
                        <th class="py-3 text-muted fw-600 text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tours as $tour)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $tour->title }}</div>
                            <small class="text-muted">
                                {{ number_format($tour->base_price, 0, ',', '.') }}đ
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $tour->destination->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-danger"><i
                                    class="bi bi-clock-history me-1"></i>{{ $tour->deleted_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <!-- Nút Khôi phục -->
                            <form action="{{ route('admin.tours.restore', $tour->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success text-white shadow-sm"
                                    title="Khôi phục tour này">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>

                            <!-- Nút Xóa vĩnh viễn -->
                            <form action="{{ route('admin.tours.force-delete', $tour->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('CẢNH BÁO: Hành động này không thể hoàn tác! Toàn bộ dữ liệu của tour này sẽ biến mất vĩnh viễn khỏi database. Anh có chắc chắn không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger text-white shadow-sm ms-1"
                                    title="Xóa vĩnh viễn">
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
    {{ $tours->links() }}
</div>
@endsection