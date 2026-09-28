@extends('layouts.admin')

@section('page-title', 'Danh sách Tour Du Lịch')

@section('content')
<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-briefcase me-2 text-primary"></i>Quản lý Sản phẩm Tour</h5>
        <div>
            <a href="{{ route('admin.tours.trash') }}" class="btn btn-admin btn-light border text-danger me-2">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
            <a href="{{ route('admin.tours.create') }}" class="btn btn-admin btn-admin-primary">
                <i class="bi bi-plus-lg me-1"></i> Thêm Tour mới
            </a>
        </div>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Hình Ảnh</th>
                        <th>Thông tin Tour</th>
                        <th>Điểm đến</th>
                        <th>Thời lượng</th>
                        <th>Giá cơ bản</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tours as $tour)
                    <tr>
                        <td class="ps-4">
                            @php $primaryImage = $tour->tour_images->first(); @endphp
                            <div class="rounded shadow-sm overflow-hidden" style="width: 64px; height: 64px;">
                                <img src="{{ $primaryImage ? $primaryImage->image_url : 'https://via.placeholder.com/64' }}"
                                    class="w-100 h-100 object-fit-cover" alt="Tour image">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate" style="max-width: 300px;" title="{{ $tour->title }}">{{ $tour->title }}</div>
                            <small class="text-muted">
                                ID: {{ $tour->code }}
                            </small>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-primary px-3">
                                <i class="bi bi-geo-alt me-1"></i>{{ $tour->destination->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-secondary px-3">
                                <i class="bi bi-clock me-1"></i>{{ $tour->duration_days }} Ngày {{ $tour->duration_nights > 0 ? $tour->duration_nights . ' Đêm' : '' }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-danger fs-6">{{ number_format($tour->base_price, 0, ',', '.') }} ₫</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.tours.schedules', $tour->id) }}"
                                    class="btn btn-action text-warning bg-warning bg-opacity-10" title="Ngày khởi hành">
                                    <i class="bi bi-calendar3"></i>
                                </a>

                                <a href="{{ route('admin.tours.itineraries.index', $tour->id) }}"
                                    class="btn btn-action text-success bg-success bg-opacity-10" title="Lịch trình chi tiết">
                                    <i class="bi bi-list-task"></i>
                                </a>

                                <a href="{{ route('admin.tours.images', $tour->id) }}"
                                    class="btn btn-action text-info bg-info bg-opacity-10" title="Thư viện ảnh">
                                    <i class="bi bi-images"></i>
                                </a>
                                
                                <a href="{{ route('admin.tours.edit', $tour->id) }}"
                                    class="btn btn-action text-primary bg-primary bg-opacity-10" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <form action="{{ route('admin.tours.destroy', $tour->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn chuyển tour này vào thùng rác?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-action text-danger bg-danger bg-opacity-10" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 text-light mb-2 d-block"></i>
                            Chưa có dữ liệu tour nào được tạo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $tours->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection