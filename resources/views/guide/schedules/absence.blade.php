@extends('layouts.guide')

@section('page-title', 'Báo bận Tour')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex mb-3">
                <a href="{{ route('guide.schedules.show', $schedule->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại chi tiết tour
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-calendar-x me-2 text-danger"></i>Yêu Cầu Báo Bận Tour
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info mb-4" style="font-size: 0.9rem;">
                        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-1"></i>Thông tin Tour báo bận:</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>Mã Tour:</strong> {{ $schedule->tour->code }}</li>
                            <li><strong>Tên Tour:</strong> {{ $schedule->tour->title }}</li>
                            <li><strong>Thời gian khởi hành:</strong> {{ \Carbon\Carbon::parse($schedule->departure_date)->format('H:i d/m/Y') }}</li>
                            <li><strong>Thời gian kết thúc:</strong> {{ \Carbon\Carbon::parse($schedule->return_date)->format('H:i d/m/Y') }}</li>
                        </ul>
                    </div>

                    <form action="{{ route('guide.schedules.absence.store', $schedule->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="reason_type" class="form-label fw-bold">Lý do báo bận <span class="text-danger">*</span></label>
                            <select name="reason_type" id="reason_type" class="form-select @error('reason_type') is-invalid @enderror" required onchange="toggleCustomReason(this.value)">
                                <option value="" disabled selected>-- Chọn lý do --</option>
                                <option value="ốm đau" {{ old('reason_type') == 'ốm đau' ? 'selected' : '' }}>Ốm đau / Sức khỏe</option>
                                <option value="trùng lịch" {{ old('reason_type') == 'trùng lịch' ? 'selected' : '' }}>Trùng lịch công tác / cá nhân</option>
                                <option value="việc gia đình" {{ old('reason_type') == 'việc gia đình' ? 'selected' : '' }}>Việc gia đình đột xuất</option>
                                <option value="khác" {{ old('reason_type') == 'khác' ? 'selected' : '' }}>Lý do khác (Nhập tự do)</option>
                            </select>
                            @error('reason_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="custom_reason_wrapper" style="display: {{ old('reason_type') == 'khác' ? 'block' : 'none' }};">
                            <label for="reason_custom" class="form-label fw-bold">Chi tiết lý do <span class="text-danger">*</span></label>
                            <textarea name="reason_custom" id="reason_custom" rows="4" class="form-control @error('reason_custom') is-invalid @enderror" placeholder="Vui lòng nhập lý do chi tiết..." required disabled>{{ old('reason_custom') }}</textarea>
                            @error('reason_custom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="attachment" class="form-label fw-bold">Ảnh/Tài liệu minh chứng <span class="text-muted fw-normal">(Không bắt buộc)</span></label>
                            <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="image/*,application/pdf">
                            <div class="form-text text-muted">
                                Chấp nhận ảnh hoặc file PDF có dung lượng dưới 5MB.
                            </div>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger px-4 fw-bold" onclick="return confirm('Bạn có chắc chắn muốn gửi yêu cầu báo bận cho tour này? Quyết định duyệt sẽ do Admin xem xét.')">
                                <i class="bi bi-send me-1"></i> Gửi Yêu Cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCustomReason(val) {
        const wrapper = document.getElementById('custom_reason_wrapper');
        const textarea = document.getElementById('reason_custom');
        if (val === 'khác') {
            wrapper.style.display = 'block';
            textarea.removeAttribute('disabled');
            textarea.setAttribute('required', 'required');
        } else {
            wrapper.style.display = 'none';
            textarea.setAttribute('disabled', 'disabled');
            textarea.removeAttribute('required');
        }
    }

    // Run on load in case of validation error redirection
    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomReason(document.getElementById('reason_type').value);
    });
</script>
@endsection
