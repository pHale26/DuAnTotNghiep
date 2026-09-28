@extends('layouts.admin')

@section('page-title', 'Sửa mã giảm giá')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark">Sửa mã giảm giá</h4>

    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
        Quay lại
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mã giảm giá</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                        value="{{ old('code', $coupon->code) }}" placeholder="VD: SALE25" maxlength="30"
                        style="text-transform: uppercase">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Chỉ chứa chữ in hoa (A-Z), số (0-9) và dấu gạch dưới (_). Từ 3 đến 30 ký tự.</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Loại giảm giá</label>
                    <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                        <option value="percent" {{ old('discount_type', $coupon->discount_type) == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Giảm tiền mặt (VNĐ)</option>
                    </select>
                    @error('discount_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá trị giảm</label>
                    <input type="number" name="discount_value" id="discount_value"
                        class="form-control @error('discount_value') is-invalid @enderror"
                        value="{{ old('discount_value', $coupon->discount_value) }}" placeholder="VD: 25 hoặc 100000"
                        step="0.01" min="0.01">
                    @error('discount_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Giá trị đơn tối thiểu</label>
                    <input type="number" name="min_order_value"
                        class="form-control @error('min_order_value') is-invalid @enderror"
                        value="{{ old('min_order_value', $coupon->min_order_value) }}" placeholder="VD: 500000"
                        step="1000" min="0">
                    @error('min_order_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3" id="max_discount_wrapper">
                    <label class="form-label">Giảm tối đa</label>
                    <input type="number" name="max_discount" id="max_discount"
                        class="form-control @error('max_discount') is-invalid @enderror"
                        value="{{ old('max_discount', $coupon->max_discount) }}" placeholder="VD: 200000"
                        step="1000" min="0">
                    @error('max_discount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Số lượt sử dụng</label>
                    <input type="number" name="usage_limit"
                        class="form-control @error('usage_limit') is-invalid @enderror"
                        value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="VD: 100"
                        min="1" step="1">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="valid_from" id="valid_from" class="form-control @error('valid_from') is-invalid @enderror"
                        value="{{ old('valid_from', $coupon->valid_from?->format('Y-m-d')) }}">
                    @error('valid_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="valid_until" id="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
                        value="{{ old('valid_until', $coupon->valid_until?->format('Y-m-d')) }}">
                    @error('valid_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Hạn dùng tối đa 1 năm kể từ ngày bắt đầu.</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại tour áp dụng</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                    <option value="">-- Áp dụng cho tất cả loại tour --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $coupon->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                Cập nhật mã giảm giá
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const discountTypeSelect = document.getElementById('discount_type');
        const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
        const maxDiscountInput = document.getElementById('max_discount');
        const validFrom = document.getElementById('valid_from');
        const validUntil = document.getElementById('valid_until');

        function toggleMaxDiscount() {
            if (!discountTypeSelect || !maxDiscountWrapper) return;
            if (discountTypeSelect.value === 'fixed') {
                maxDiscountWrapper.style.display = 'none';
                if (maxDiscountInput) {
                    maxDiscountInput.value = '';
                }
            } else {
                maxDiscountWrapper.style.display = 'block';
            }
        }

        if (discountTypeSelect) {
            discountTypeSelect.addEventListener('change', toggleMaxDiscount);
            toggleMaxDiscount();
        }

        function updateValidUntilRange() {
            if (!validFrom.value) return;
            const fromDate = new Date(validFrom.value);
            const minDate = new Date(fromDate);
            minDate.setDate(minDate.getDate() + 1);
            const maxDate = new Date(fromDate);
            maxDate.setFullYear(maxDate.getFullYear() + 1);

            validUntil.min = minDate.toISOString().split('T')[0];
            validUntil.max = maxDate.toISOString().split('T')[0];

            if (validUntil.value && (validUntil.value <= validFrom.value || validUntil.value > validUntil.max)) {
                validUntil.value = '';
            }
        }

        if (validFrom && validUntil) {
            validFrom.addEventListener('change', updateValidUntilRange);
            if (validFrom.value) updateValidUntilRange();
        }
    })();
</script>
@endpush
