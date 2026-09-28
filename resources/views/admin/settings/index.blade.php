@extends('layouts.admin')

@section('page-title', 'Cấu hình hệ thống')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="admin-card border-0 mb-4">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title mb-0">
                    <i class="bi bi-sliders me-2 text-primary"></i>Cấu hình chung hệ thống
                </h5>
            </div>
            
            <div class="admin-card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS" class="form-label fw-bold text-dark">
                            Ngưỡng thời gian khẩn cấp báo bận (Giờ)
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   name="ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS" 
                                   id="ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS" 
                                   class="form-control @error('ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS') is-invalid @enderror" 
                                   value="{{ old('ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS', $threshold) }}" 
                                   min="1" 
                                   max="168" 
                                   required>
                            <span class="input-group-text">giờ</span>
                            @error('ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Nếu thời gian từ lúc Hướng dẫn viên gửi yêu cầu báo bận đến lúc khởi hành của tour 
                            <strong>nhỏ hơn</strong> ngưỡng này, yêu cầu sẽ tự động được đánh dấu là 
                            <span class="text-danger fw-semibold">Khẩn cấp</span> (Urgent) và gửi cảnh báo đỏ cho Admin.
                        </div>
                    </div>
                    
                    <hr class="my-4 text-muted opacity-25">
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">
                            <i class="bi bi-save me-2"></i>Lưu cấu hình
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
