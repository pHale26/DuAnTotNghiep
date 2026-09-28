@extends('layouts.admin')

@section('page-title', 'Thêm Tour Du Lịch Mới')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
        @endif
        <form action="{{ route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label text-muted">Tên Tour <span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">Tiếng Việt</span>
                            <input type="text" name="title[vi]" class="form-control" placeholder="Nhập tên tour hiển thị..." required>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">English</span>
                            <input type="text" name="title[en]" class="form-control" placeholder="Tour name...">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">中文</span>
                            <input type="text" name="title[zh]" class="form-control" placeholder="旅游名称...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Mô tả chi tiết</label>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">Tiếng Việt</span>
                            <textarea name="description[vi]" class="form-control" rows="3" placeholder="Viết vài dòng mô tả về trải nghiệm của tour này..."></textarea>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">English</span>
                            <textarea name="description[en]" class="form-control" rows="3" placeholder="Tour description..."></textarea>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">中文</span>
                            <textarea name="description[zh]" class="form-control" rows="3" placeholder="旅游描述..."></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Chi phí vận chuyển (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="cost_transport" class="form-control cost-input" value="{{ old('cost_transport', 0) }}" min="0" step="1000" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Chi phí ăn uống (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="cost_meal" class="form-control cost-input" value="{{ old('cost_meal', 0) }}" min="0" step="1000" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Chi phí bảo hiểm (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="cost_insurance" class="form-control cost-input" value="{{ old('cost_insurance', 0) }}" min="0" step="1000" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Phí dịch vụ (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="cost_service_fee" class="form-control cost-input" value="{{ old('cost_service_fee', 0) }}" min="0" step="1000" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Giá cơ bản (Người lớn)</label>
                            <input type="text" class="form-control" placeholder="Tự động tính toán (readonly)" readonly>
                            <small class="text-muted">Giá sẽ được tự động cộng dồn sau khi lưu.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Giá trẻ em (VNĐ)</label>
                            <input type="text" class="form-control" placeholder="Tự động tính toán (readonly)" readonly>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label text-muted">Số ngày</label>
                            <input type="number" name="duration_days" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label text-muted">Số đêm</label>
                            <input type="number" name="duration_nights" class="form-control" value="0" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label text-muted">Ảnh đại diện Tour</label>
                        <input type="file" name="primary_image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="form-label fw-bold">Điểm tập kết <span class="text-danger">*</span></label>
                        <input type="text" name="meeting_point" class="form-control" placeholder="VD: Cổng phụ công viên Thống Nhất..." value="{{ old('meeting_point') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Giờ khởi hành</label>
                        <div class="d-flex gap-2">
                            <div class="flex-grow-1">
                                <select name="departure_hour" class="form-select">
                                    <option value="">Giờ</option>
                                    @for($h = 0; $h < 24; $h++)
                                        <option value="{{ $h }}" {{ old('departure_hour') !== null && old('departure_hour') == $h ? 'selected' : '' }}>
                                            {{ sprintf('%02dh', $h) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <select name="departure_minute" class="form-select">
                                    <option value="">Phút</option>
                                    @for($m = 0; $m < 60; $m++)
                                        <option value="{{ $m }}" {{ old('departure_minute') !== null && old('departure_minute') == $m ? 'selected' : '' }}>
                                            {{ sprintf('%02d', $m) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="form-label fw-bold">Điểm đến <span class="text-danger">*</span></label>
                        <select name="destination_id" id="destination_id" class="form-select destination-select" required>
                            <option value="">-- Chọn điểm đến --</option>
                            @foreach($destinations as $dest)
                            <option value="{{ $dest->id }}" {{ old('destination_id') == $dest->id ? 'selected' : '' }}>
                                {{ $dest->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Danh mục Tour</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($categories as $cat)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]"
                                    value="{{ $cat->id }}" id="cat_{{ $cat->id }}">
                                <label class="form-check-label" for="cat_{{ $cat->id }}">
                                    {{ $cat->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <h4 class="h5 mb-3 text-primary">Dịch vụ đi kèm Tour</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted fw-bold">Vé tham quan</label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @foreach($tickets as $ticket)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="tickets[]"
                                value="{{ $ticket->id }}" id="ticket_{{ $ticket->id }}">
                            <label class="form-check-label" for="ticket_{{ $ticket->id }}">
                                {{ $ticket->title }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted fw-bold">Điểm lưu trú</label>
                    <select name="accommodation_id" id="accommodation_id" class="form-select">
                        <option value="">-- Chọn điểm lưu trú --</option>
                        @foreach($accommodations as $acc)
                            <option value="{{ $acc->id }}" data-destination-id="{{ $acc->destination_id }}" {{ old('accommodation_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }} ({{ $acc->star_rating }} Sao)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted fw-bold">Dịch vụ bổ sung (Addons)</label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @foreach($addons as $addon)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="addons[]"
                                value="{{ $addon->id }}" id="addon_{{ $addon->id }}">
                            <label class="form-check-label" for="addon_{{ $addon->id }}">
                                {{ $addon->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="text-end">
                <button type="reset" class="btn btn-light me-2">Nhập lại</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="btn-text">Lưu và Tiếp tục thêm lịch trình</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast notification -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="toastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const spinner = submitBtn.querySelector('.spinner-border');
    const successToast = new bootstrap.Toast(document.getElementById('successToast'));

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable button và hiển thị spinner
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Đang tạo tour...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị toast thông báo
                document.getElementById('toastMessage').textContent = data.message;
                successToast.show();

                // Đợi 1 giây rồi chuyển sang trang thêm lịch trình
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tạo tour!');
            // Enable lại button nếu có lỗi
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Lưu và Tiếp tục thêm lịch trình';
        });
    });

    // Initialize Select2
    $('#destination_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Chọn điểm đến --'
    });

    $('#accommodation_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Chọn điểm lưu trú --'
    });

    const accommodationSelect = $('#accommodation_id');
    const originalOptions = accommodationSelect.find('option').clone();

    function filterAccommodations() {
        const destId = $('#destination_id').val();
        const currentVal = accommodationSelect.val();
        accommodationSelect.empty();
        accommodationSelect.append('<option value="">-- Chọn điểm lưu trú --</option>');
        
        let hasValidOption = false;
        if (destId) {
            originalOptions.each(function() {
                const opt = $(this);
                if (opt.data('destination-id') == destId) {
                    const clonedOpt = opt.clone();
                    if (clonedOpt.val() == currentVal) {
                        clonedOpt.prop('selected', true);
                        hasValidOption = true;
                    }
                    accommodationSelect.append(clonedOpt);
                }
            });
            accommodationSelect.prop('disabled', false);
        } else {
            accommodationSelect.prop('disabled', true);
        }
        
        if (!hasValidOption) {
            accommodationSelect.val('').trigger('change');
        } else {
            accommodationSelect.trigger('change');
        }
    }

    $('#destination_id').on('change', filterAccommodations);
    // Run filter on initial load
    filterAccommodations();
});
</script>
@endpush
