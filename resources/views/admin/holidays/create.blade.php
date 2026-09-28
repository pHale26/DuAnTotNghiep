@extends('layouts.admin')

@section('page-title', 'Thêm Ngày Lễ Mới')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Thông tin Ngày lễ</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.holidays.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên ngày lễ <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="VD: Tết Nguyên Đán">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="holiday_start_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="holiday_end_date" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Phụ thu (%) <span class="text-danger">*</span></label>
                        <input type="number" name="price_increase_percentage" id="holiday_price_increase_percentage" class="form-control" value="20" step="0.01" min="0" max="100" required>
                        <div class="form-text">Mức phụ thu áp dụng (tính theo %). Ví dụ: 20 có nghĩa là tăng 20% giá gốc.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.holidays.index') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-admin-primary">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const startDate = document.getElementById('holiday_start_date');
        const endDate = document.getElementById('holiday_end_date');
        const surchargeInput = document.getElementById('holiday_price_increase_percentage');

        if (startDate && endDate) {
            function updateEndDateMin() {
                if (startDate.value) {
                    const date = new Date(startDate.value);
                    date.setDate(date.getDate() + 1);
                    const yyyy = date.getFullYear();
                    const mm = String(date.getMonth() + 1).padStart(2, '0');
                    const dd = String(date.getDate()).padStart(2, '0');
                    const minDateStr = `${yyyy}-${mm}-${dd}`;
                    
                    endDate.min = minDateStr;
                    if (!endDate.value || endDate.value <= startDate.value) {
                        endDate.value = minDateStr;
                    }
                }
            }

            startDate.addEventListener('change', updateEndDateMin);
        }

        if (surchargeInput) {
            surchargeInput.addEventListener('input', function () {
                const val = parseFloat(this.value);
                if (val > 100) {
                    this.value = 100;
                } else if (val < 0) {
                    this.value = 0;
                }
            });
        }
    })();
</script>
@endpush
