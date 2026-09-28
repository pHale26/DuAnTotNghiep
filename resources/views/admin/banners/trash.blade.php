@extends('layouts.admin')

@section('page-title', 'Thùng rác - Banner đã xóa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Thùng rác Banner Quảng Cáo</h4>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
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
                        <th class="py-3 text-muted">Tiêu đề</th>
                        <th class="py-3 text-muted">Vị trí</th>
                        <th class="py-3 text-muted">Thời gian xóa</th>
                        <th class="py-3 text-muted text-end pe-4">Thao tác</th>
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
                        <td><span class="text-danger"><i class="bi bi-clock-history me-1"></i>{{ $banner->deleted_at->format('d/m/Y H:i') }}</span></td>
                        <td class="text-end pe-4">
                            <!-- Nút Khôi phục -->
                            <form action="{{ route('admin.banners.restore', $banner->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success text-white shadow-sm" title="Khôi phục banner này">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>

                            <!-- Nút Xóa vĩnh viễn -->
                            <form action="{{ route('admin.banners.force-delete', $banner->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('CẢNH BÁO: Hành động này không thể hoàn tác! Toàn bộ dữ liệu của banner này sẽ biến mất vĩnh viễn. Bạn có chắc chắn không?');">
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
                        <td colspan="5" class="text-center py-5 text-muted">
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
@endsection
