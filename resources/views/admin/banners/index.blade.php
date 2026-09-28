@extends('layouts.admin')

@section('page-title', 'Quản lý Banner Quảng Cáo')

@section('content')
<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-images me-2 text-primary"></i>Danh sách Banner</h5>
        <div>
            <a href="{{ route('admin.banners.trash') }}" class="btn btn-admin btn-light border text-danger me-2">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-admin btn-admin-primary">
                <i class="bi bi-plus-lg me-1"></i> Thêm Banner
            </a>
        </div>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Hình Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Vị trí</th>
                        <th>Đường dẫn đích (URL)</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr>
                        <td class="ps-4">
                            <div class="rounded shadow-sm overflow-hidden" style="width: 120px; height: 60px;">
                                @php
                                    $imgSrc = Str::startsWith($banner->image_url, ['http://', 'https://']) 
                                              ? $banner->image_url 
                                              : asset($banner->image_url);
                                @endphp
                                <img src="{{ $imgSrc }}" class="w-100 h-100 object-fit-cover" alt="Banner">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $banner->title }}</div>
                            <small class="text-muted">ID: #{{ $banner->id }}</small>
                        </td>
                        <td>
                            @if($banner->position == 'hero')
                                <span class="badge-soft badge-soft-primary px-2">Banner bìa</span>
                            @elseif($banner->position == 'home_ads')
                                <span class="badge-soft badge-soft-warning px-2">Quảng cáo ngang</span>
                            @else
                                <span class="badge-soft badge-soft-secondary px-2">Mặc định</span>
                            @endif
                        </td>
                        <td>
                            @if($banner->target_url)
                                <a href="{{ $banner->target_url }}" target="_blank" class="text-primary text-truncate d-inline-block" style="max-width: 200px;">
                                    {{ $banner->target_url }}
                                </a>
                            @else
                                <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-secondary px-2">{{ $banner->sort_order }}</span>
                        </td>
                        <td>
                            @if($banner->is_active)
                                <span class="badge-soft badge-soft-success px-3"><i class="bi bi-check-circle me-1"></i>Hiển thị</span>
                            @else
                                <span class="badge-soft badge-soft-danger px-3"><i class="bi bi-eye-slash me-1"></i>Đã ẩn</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-action text-primary bg-primary bg-opacity-10" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa Banner này?');">
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
                            <i class="bi bi-image fs-1 text-light mb-2 d-block"></i>
                            Chưa có banner nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
