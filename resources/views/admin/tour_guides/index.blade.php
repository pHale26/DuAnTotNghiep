@extends('layouts.admin')

@section('page-title', 'Danh sách Hướng Dẫn Viên')

@section('content')
<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-person-badge me-2 text-primary"></i>Quản lý Hướng Dẫn Viên</h5>
        <div>
            <a href="{{ route('admin.tour_guides.create') }}" class="btn btn-admin btn-admin-primary">
                <i class="bi bi-plus-lg me-1"></i> Thêm Hướng Dẫn Viên
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
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Số Tour Đã Dẫn</th>
                        <th>KPI</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tourGuides as $guide)
                    <tr>
                        <td class="ps-4">#{{ $guide->id }}</td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                {{ $guide->name }}
                                @if($guide->is_blacklisted)
                                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">Blacklisted</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $guide->phone }}</td>
                        <td>{{ $guide->email ?? 'N/A' }}</td>
                        <td>
                            <span class="badge-soft badge-soft-primary px-3">
                                <i class="bi bi-briefcase me-1"></i>{{ $guide->schedule_guides_count }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-warning px-2"><i class="bi bi-star-fill text-warning me-1"></i>{{ $guide->kpi_score }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.tour_guides.edit', $guide->id) }}"
                                    class="btn btn-action text-primary bg-primary bg-opacity-10" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <form action="{{ route('admin.tour_guides.destroy', $guide->id) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa Hướng dẫn viên này?');">
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
                            Chưa có dữ liệu hướng dẫn viên nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $tourGuides->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
