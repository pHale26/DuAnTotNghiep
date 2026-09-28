@extends('layouts.admin')

@section('page-title', 'Thêm Banner Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card border-0">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Thông tin Banner</h5>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-500">Tiêu đề Banner <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Nhập tiêu đề (dùng để quản lý)">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-500">Hình ảnh <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="image_type" id="typeUpload" value="upload" checked onchange="toggleImageInput()">
                                <label class="form-check-label" for="typeUpload">Tải ảnh lên</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="image_type" id="typeUrl" value="url" onchange="toggleImageInput()">
                                <label class="form-check-label" for="typeUrl">Sử dụng URL</label>
                            </div>
                        </div>
                        
                        <div id="uploadInput">
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted mt-1 d-block">Định dạng hỗ trợ: JPG, PNG, WEBP. Tối đa 5MB. Kích thước khuyên dùng: 1920x800 px.</small>
                        </div>
                        
                        <div id="urlInput" class="d-none">
                            <input type="url" name="image_url" class="form-control" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Đường dẫn đích (Tùy chọn)</label>
                            <input type="text" name="target_url" class="form-control" value="{{ old('target_url') }}" placeholder="Khi click vào banner sẽ chuyển đến link này">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Mã giảm giá (Tùy chọn)</label>
                            <select name="coupon_id" class="form-select">
                                <option value="">-- Không chọn mã giảm giá --</option>
                                @foreach($coupons as $coupon)
                                    <option value="{{ $coupon->id }}" {{ old('coupon_id') == $coupon->id ? 'selected' : '' }}>
                                        {{ $coupon->code }} - 
                                        @if($coupon->discount_type === 'percentage')
                                            {{ $coupon->discount_value }}%
                                        @else
                                            {{ number_format($coupon->discount_value) }}đ
                                        @endif
                                        (HSD: {{ $coupon->valid_until->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mã giảm giá sẽ hiển thị trên banner</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-500">Vị trí hiển thị <span class="text-danger">*</span></label>
                            <select name="position" class="form-select" required>
                                <option value="hero" {{ old('position') == 'hero' ? 'selected' : '' }}>Banner bìa (Hero)</option>
                                <option value="home_ads" {{ old('position') == 'home_ads' ? 'selected' : '' }}>Quảng cáo ngang (Ads)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Thứ tự hiển thị</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                            <small class="text-muted">Số nhỏ hơn sẽ hiển thị trước.</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="isActive" name="is_active" value="1" checked>
                                <label class="form-check-label ms-2 fw-500" for="isActive">Hiển thị ngoài trang chủ</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-light border px-4">Hủy</a>
                        <button type="submit" class="btn btn-admin-primary px-4"><i class="bi bi-save me-2"></i> Lưu Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleImageInput() {
    const isUpload = document.getElementById('typeUpload').checked;
    if (isUpload) {
        document.getElementById('uploadInput').classList.remove('d-none');
        document.getElementById('urlInput').classList.add('d-none');
    } else {
        document.getElementById('uploadInput').classList.add('d-none');
        document.getElementById('urlInput').classList.remove('d-none');
    }
}
</script>
@endsection
