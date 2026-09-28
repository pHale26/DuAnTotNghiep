@extends('layouts.admin')

@section('page-title', 'Thêm Dịch vụ Addon')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Thông tin Addon</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.addons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên dịch vụ <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="VD: Thuê xe điện, Lặn biển...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Giá dịch vụ <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" required min="0" placeholder="VD: 500000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Mô tả về dịch vụ..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hình ảnh</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Liên kết Tour</label>
                        <div class="form-text mb-2">Chọn các Tour được phép hiển thị dịch vụ Addon này.</div>
                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                            @forelse($tours as $tour)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="tours[]" value="{{ $tour->id }}" id="tour_{{ $tour->id }}">
                                <label class="form-check-label" for="tour_{{ $tour->id }}">
                                    {{ $tour->title }}
                                </label>
                            </div>
                            @empty
                            <div class="text-muted small">Chưa có Tour nào trong hệ thống.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked value="1">
                            <label class="form-check-label fw-bold" for="isActive">Kích hoạt (Hiển thị cho khách hàng)</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.addons.index') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-admin-primary">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
