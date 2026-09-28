@extends('layouts.admin')

@section('page-title', 'Thêm Hướng Dẫn Viên Mới')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card border-0">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title"><i class="bi bi-person-plus me-2 text-primary"></i>Thông tin Hướng Dẫn Viên</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.tour_guides.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Tài khoản liên kết</label>
                        <select name="user_id" id="user_id_select" class="form-select @error('user_id') is-invalid @enderror">
                            <option value="">-- Không liên kết --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-phone="{{ $user->phone }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Chỉ danh sách tài khoản đã được cấp vai trò Hướng Dẫn Viên mới được hiển thị tại đây.</div>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="input_name" class="form-control" required value="{{ old('name') }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="input_phone" class="form-control @error('phone') is-invalid @enderror" required value="{{ old('phone') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Email</label>
                            <input type="email" name="email" id="input_email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-500">Thẻ Hành Nghề</label>
                            <select name="guide_card_type" class="form-select">
                                <option value="">-- Chọn thẻ --</option>
                                <option value="Nội địa" {{ old('guide_card_type') == 'Nội địa' ? 'selected' : '' }}>Thẻ Nội Địa (Hồng)</option>
                                <option value="Quốc tế" {{ old('guide_card_type') == 'Quốc tế' ? 'selected' : '' }}>Thẻ Quốc Tế (Xanh)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-500">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-500">Ngôn ngữ hỗ trợ (Chọn nhiều)</label>
                            @php
                                $languagesList = ['Tiếng Việt', 'Tiếng Anh', 'Tiếng Trung', 'Tiếng Hàn', 'Tiếng Nhật'];
                                $oldLanguages = old('languages', []);
                            @endphp
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                @foreach($languagesList as $lang)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang }}" id="lang_create_{{ $loop->index }}" {{ in_array($lang, $oldLanguages) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lang_create_{{ $loop->index }}">
                                            {{ $lang }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_blacklisted" id="is_blacklisted" class="form-check-input" value="1" {{ old('is_blacklisted') ? 'checked' : '' }}>
                        <label for="is_blacklisted" class="form-check-label text-danger fw-bold">Đưa vào danh sách đen (Blacklist)</label>
                        <div class="form-text">HDV bị blacklist sẽ có cảnh báo khi điều hành tour.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-500">Tiểu sử / Ghi chú (Bio)</label>
                        <textarea name="bio" class="form-control" rows="4">{{ old('bio') }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.tour_guides.index') }}" class="btn btn-light border px-4 me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-admin-primary px-4"><i class="bi bi-save me-2"></i> Lưu Thông Tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_id_select');
        const nameInput = document.getElementById('input_name');
        const phoneInput = document.getElementById('input_phone');
        const emailInput = document.getElementById('input_email');

        userSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                nameInput.value = selectedOption.dataset.name || '';
                emailInput.value = selectedOption.dataset.email || '';
                phoneInput.value = selectedOption.dataset.phone || '';
            }
        });
    });
</script>
@endpush
