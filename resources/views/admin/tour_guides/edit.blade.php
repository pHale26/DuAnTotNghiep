@extends('layouts.admin')

@section('page-title', 'Cập nhật Hướng Dẫn Viên')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card border-0">
            <div class="admin-card-header bg-white py-3">
                <h5 class="admin-card-title"><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa thông tin HDV</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.tour_guides.update', $tourGuide->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Tài khoản liên kết</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                            <option value="">-- Không liên kết --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $tourGuide->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Chỉ danh sách tài khoản đã được cấp vai trò Hướng Dẫn Viên mới được hiển thị tại đây.</div>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $tourGuide->name) }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" required value="{{ old('phone', $tourGuide->phone) }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-500">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $tourGuide->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-500">Thẻ Hành Nghề</label>
                            <select name="guide_card_type" class="form-select">
                                <option value="">-- Chọn thẻ --</option>
                                <option value="Nội địa" {{ old('guide_card_type', $tourGuide->guide_card_type) == 'Nội địa' ? 'selected' : '' }}>Thẻ Nội Địa (Hồng)</option>
                                <option value="Quốc tế" {{ old('guide_card_type', $tourGuide->guide_card_type) == 'Quốc tế' ? 'selected' : '' }}>Thẻ Quốc Tế (Xanh)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-500">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $tourGuide->status) == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ old('status', $tourGuide->status) == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-500">Ngôn ngữ hỗ trợ (Chọn nhiều)</label>
                            @php
                                $selectedLangs = old('languages', $tourGuide->languages ?? []);
                                if(!is_array($selectedLangs)) $selectedLangs = json_decode($selectedLangs, true) ?? [];
                                $languagesList = ['Tiếng Việt', 'Tiếng Anh', 'Tiếng Trung', 'Tiếng Hàn', 'Tiếng Nhật'];
                            @endphp
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                @foreach($languagesList as $lang)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang }}" id="lang_edit_{{ $loop->index }}" {{ in_array($lang, $selectedLangs) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lang_edit_{{ $loop->index }}">
                                            {{ $lang }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_blacklisted" id="is_blacklisted" class="form-check-input" value="1" {{ old('is_blacklisted', $tourGuide->is_blacklisted) ? 'checked' : '' }}>
                        <label for="is_blacklisted" class="form-check-label text-danger fw-bold">Đưa vào danh sách đen (Blacklist)</label>
                        <div class="form-text">HDV bị blacklist sẽ có cảnh báo khi điều hành tour.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-500">Tiểu sử / Ghi chú (Bio)</label>
                        <textarea name="bio" class="form-control" rows="4">{{ old('bio', $tourGuide->bio) }}</textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.tour_guides.index') }}" class="btn btn-light border px-4 me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-admin-primary px-4"><i class="bi bi-save me-2"></i> Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
