@extends('layouts.admin')

@section('content')
<style>
    .collapse-icon {
        transition: transform 0.2s ease;
    }
    .card-header:not(.collapsed) .collapse-icon {
        transform: rotate(90deg);
    }
</style>
<div class="mb-4">
    <a href="{{ route('admin.tours.index') }}" class="text-decoration-none">Quay lại danh sách Tour</a>
    <div class="mt-2 text-dark fs-5">Lịch trình: {{ $tour->title }}</div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="text-dark fs-6 mb-3">Thêm ngày mới</div>
                <form action="{{ route('admin.tours.itineraries.store', $tour->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ngày thứ</label>
                        <input type="number" name="day_number"
                            class="form-control @error('day_number') is-invalid @enderror" required min="1"
                            max="{{ $tour->duration_days }}" value="{{ old('day_number') }}">
                        @error('day_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-flex align-items-center">
                            <span>Tiêu đề ngày</span>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-3 py-0 px-2" onclick="translateField('title', '', this)" id="btn-translate-title">
                                <i class="bi bi-robot me-1"></i>Dịch bằng AI
                            </button>
                        </label>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">Tiếng Việt</span>
                            <input type="text" name="title[vi]" class="form-control form-control-sm" placeholder="VD: Khám phá đảo ngọc" required>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">English</span>
                            <input type="text" name="title[en]" class="form-control form-control-sm" placeholder="VD: Discover the pearl island">
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">中文</span>
                            <input type="text" name="title[zh]" class="form-control form-control-sm" placeholder="VD: 探索珍珠岛">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-flex align-items-center">
                            <span>Mô tả tổng quan</span>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-3 py-0 px-2" onclick="translateField('description', '', this)" id="btn-translate-description">
                                <i class="bi bi-robot me-1"></i>Dịch bằng AI
                            </button>
                        </label>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">Tiếng Việt</span>
                            <textarea name="description[vi]" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">English</span>
                            <textarea name="description[en]" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">中文</span>
                            <textarea name="description[zh]" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lưu ngày</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @forelse($itineraries as $day)
        <div class="card shadow-sm border-0 mb-4" id="day-card-{{ $day->id }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center collapsed"
                style="cursor: pointer;"
                data-bs-toggle="collapse"
                data-bs-target="#day-collapse-{{ $day->id }}"
                aria-expanded="false"
                aria-controls="day-collapse-{{ $day->id }}">
                <div class="text-dark fs-6 fw-semibold d-flex align-items-center">
                    <i class="bi bi-chevron-right me-2 text-muted collapse-icon"></i>
                    Ngày {{ $day->day_number }}: {{ $day->title }}
                </div>
                <div onclick="event.stopPropagation();" class="d-flex gap-2">
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary edit-day-btn"
                            data-day-id="{{ $day->id }}"
                            data-day-number="{{ $day->day_number }}"
                            data-title-vi="{{ $day->getTranslation('title', 'vi') }}"
                            data-title-en="{{ $day->getTranslation('title', 'en') }}"
                            data-title-zh="{{ $day->getTranslation('title', 'zh') }}"
                            data-desc-vi="{{ $day->getTranslation('description', 'vi') }}"
                            data-desc-en="{{ $day->getTranslation('description', 'en') }}"
                            data-desc-zh="{{ $day->getTranslation('description', 'zh') }}">
                        Sửa
                    </button>
                    <form action="{{ route('admin.itineraries.destroy', $day->id) }}" method="POST" class="delete-day-form d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa ngày</button>
                    </form>
                </div>
            </div>
            <div id="day-collapse-{{ $day->id }}" class="collapse">
                <div class="card-body">
                    <p class="text-muted small">{{ $day->description }}</p>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60px;">Ảnh</th>
                                    <th>Loại</th>
                                    <th>Thời gian</th>
                                    <th>Hoạt động</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($day->activities as $act)
                                <tr id="activity-row-{{ $act->id }}">
                                    <td>
                                        @if($act->image_url)
                                        <img src="{{ $act->image_url }}" alt="Ảnh" class="rounded shadow-sm"
                                            style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                        <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 45px; height: 45px; font-size: 11px;">Trống</div>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $act->activity_type_label }}</span></td>
                                    <td>{{ $act->start_time ? \Carbon\Carbon::parse($act->start_time)->format('H:i') : '--:--' }}
                                    </td>
                                    <td>{{ $act->title }}</td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" 
                                                    class="btn btn-sm text-primary border-0 bg-transparent edit-act-btn"
                                                    data-act-id="{{ $act->id }}"
                                                    data-act-type="{{ $act->activity_type }}"
                                                    data-act-time="{{ $act->start_time ? \Carbon\Carbon::parse($act->start_time)->format('H:i') : '' }}"
                                                    data-act-image="{{ $act->image_url }}"
                                                    data-title-vi="{{ $act->getTranslation('title', 'vi') }}"
                                                    data-title-en="{{ $act->getTranslation('title', 'en') }}"
                                                    data-title-zh="{{ $act->getTranslation('title', 'zh') }}">
                                                Sửa
                                            </button>
                                            <form action="{{ route('admin.activities.destroy', $act->id) }}" method="POST" class="delete-activity-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm text-danger border-0 bg-transparent">Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <form action="{{ route('admin.itineraries.activities.store', $day->id) }}" method="POST"
                        enctype="multipart/form-data" class="border-top pt-3">
                        @csrf
                        <div class="row g-2 align-items-end mb-2">
                            <div class="col-md-3">
                                <label class="form-label small">Phân loại</label>
                                <select name="activity_type" class="form-select form-select-sm" required>
                                    <option value="Entertainment">Giải trí</option>
                                    <option value="Dining">Ẩm thực</option>
                                    <option value="Attractions">Điểm tham quan</option>
                                    <option value="Transportation">Di chuyển</option>
                                    <option value="Others">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Giờ bắt đầu</label>
                                <input type="time" name="start_time" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small">Ảnh tải lên</label>
                                <input type="file" name="image_upload" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small">Tên HĐ (VI)</label>
                                <input type="text" name="title[vi]" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Tên HĐ (EN)</label>
                                <input type="text" name="title[en]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Tên HĐ (ZH)</label>
                                <input type="text" name="title[zh]" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm btn-success w-100">Thêm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-muted text-center py-5 bg-white rounded shadow-sm">Chưa có lịch trình nào. Hãy thêm ở cột bên
            trái.</div>
        @endforelse
    </div>
</div>
<!-- Modal Sửa Hoạt Động -->
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editActivityModalLabel">Sửa hoạt động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editActivityForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">Phân loại</label>
                        <select name="activity_type" id="edit_activity_type" class="form-select form-select-sm" required>
                            <option value="Entertainment">Giải trí</option>
                            <option value="Dining">Ẩm thực</option>
                            <option value="Attractions">Điểm tham quan</option>
                            <option value="Transportation">Di chuyển</option>
                            <option value="Others">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Giờ bắt đầu</label>
                        <input type="time" name="start_time" id="edit_start_time" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Ảnh hoạt động</label>
                        <input type="file" name="image_upload" class="form-control form-control-sm" accept="image/*">
                        <div id="edit_activity_image_preview" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tên HĐ (VI)</label>
                        <input type="text" name="title[vi]" id="edit_title_vi" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tên HĐ (EN)</label>
                        <input type="text" name="title[en]" id="edit_title_en" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tên HĐ (ZH)</label>
                        <input type="text" name="title[zh]" id="edit_title_zh" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-sm btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Ngày -->
<div class="modal fade" id="editDayModal" tabindex="-1" aria-labelledby="editDayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDayModalLabel">Sửa ngày lịch trình</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDayForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">Ngày thứ</label>
                        <input type="number" name="day_number" id="edit_day_number" class="form-control form-control-sm" required min="1" max="{{ $tour->duration_days }}">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small mb-0">Tiêu đề ngày (VI)</label>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.7rem;" onclick="translateField('title', 'edit_day_', this)" id="btn-translate-edit_day_title">
                                <i class="bi bi-robot"></i> Dịch
                            </button>
                        </div>
                        <input type="text" name="title[vi]" id="edit_day_title_vi" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tiêu đề ngày (EN)</label>
                        <input type="text" name="title[en]" id="edit_day_title_en" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Tiêu đề ngày (ZH)</label>
                        <input type="text" name="title[zh]" id="edit_day_title_zh" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small mb-0">Mô tả tổng quan (VI)</label>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.7rem;" onclick="translateField('description', 'edit_day_desc_', this)" id="btn-translate-edit_day_desc">
                                <i class="bi bi-robot"></i> Dịch
                            </button>
                        </div>
                        <textarea name="description[vi]" id="edit_day_desc_vi" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Mô tả tổng quan (EN)</label>
                        <textarea name="description[en]" id="edit_day_desc_en" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Mô tả tổng quan (ZH)</label>
                        <textarea name="description[zh]" id="edit_day_desc_zh" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-sm btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. AJAX Xóa Hoạt Động (Không tải lại trang)
    document.querySelectorAll('.delete-activity-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('Anh có chắc muốn xóa hoạt động này?')) {
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '...';

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = this.closest('tr');
                    if (row) {
                        row.remove();
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Xóa';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi xảy ra khi kết nối máy chủ.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Xóa';
            });
        });
    });

    // 2. AJAX Xóa Ngày Lịch Trình (Không tải lại trang)
    document.querySelectorAll('.delete-day-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('Xóa ngày này sẽ xóa luôn tất cả các hoạt động bên trong?')) {
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '...';

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = this.closest('.card');
                    if (card) {
                        card.remove();
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Xóa ngày';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi xảy ra khi kết nối máy chủ.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Xóa ngày';
            });
        });
    });

    // 3. Modal Sửa Ngày Lịch Trình
    const editDayModalEl = document.getElementById('editDayModal');
    const editDayModal = new bootstrap.Modal(editDayModalEl);
    const editDayForm = document.getElementById('editDayForm');

    document.querySelectorAll('.edit-day-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const dayId = this.getAttribute('data-day-id');
            editDayForm.action = `/admin/itineraries/${dayId}`;
            
            document.getElementById('edit_day_number').value = this.getAttribute('data-day-number');
            document.getElementById('edit_day_title_vi').value = this.getAttribute('data-title-vi');
            document.getElementById('edit_day_title_en').value = this.getAttribute('data-title-en');
            document.getElementById('edit_day_title_zh').value = this.getAttribute('data-title-zh');
            document.getElementById('edit_day_desc_vi').value = this.getAttribute('data-desc-vi');
            document.getElementById('edit_day_desc_en').value = this.getAttribute('data-desc-en');
            document.getElementById('edit_day_desc_zh').value = this.getAttribute('data-desc-zh');
            
            editDayModal.show();
        });
    });

    // 4. Modal Sửa Hoạt Động
    const editActivityModalEl = document.getElementById('editActivityModal');
    const editActivityModal = new bootstrap.Modal(editActivityModalEl);
    const editActivityForm = document.getElementById('editActivityForm');

    document.querySelectorAll('.edit-act-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const actId = this.getAttribute('data-act-id');
            editActivityForm.action = `/admin/activities/${actId}`;
            
            document.getElementById('edit_activity_type').value = this.getAttribute('data-act-type');
            document.getElementById('edit_start_time').value = this.getAttribute('data-act-time');
            document.getElementById('edit_title_vi').value = this.getAttribute('data-title-vi');
            document.getElementById('edit_title_en').value = this.getAttribute('data-title-en');
            document.getElementById('edit_title_zh').value = this.getAttribute('data-title-zh');
            
            const imagePreview = document.getElementById('edit_activity_image_preview');
            const imageUrl = this.getAttribute('data-act-image');
            if (imageUrl) {
                imagePreview.innerHTML = `<img src="${imageUrl}" alt="Ảnh hoạt động" class="rounded shadow-sm" style="width: 65px; height: 65px; object-fit: cover;">`;
            } else {
                imagePreview.innerHTML = '';
            }
            
            editActivityModal.show();
        });
    });
});

async function translateField(field, prefix = '', btn) {
    const viInput = document.getElementById(`${prefix}${field}_vi`) || document.querySelector(`[name="${field}[vi]"]`);
    const enInput = document.getElementById(`${prefix}${field}_en`) || document.querySelector(`[name="${field}[en]"]`);
    const zhInput = document.getElementById(`${prefix}${field}_zh`) || document.querySelector(`[name="${field}[zh]"]`);
    
    if (!viInput || !viInput.value.trim()) {
        alert('Vui lòng nhập nội dung Tiếng Việt trước khi dịch!');
        return;
    }

    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    btn.disabled = true;

    try {
        const response = await fetch('{{ route("admin.ai-translate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text: viInput.value.trim() })
        });

        const result = await response.json();

        if (result.success && result.data) {
            if (enInput) enInput.value = result.data.en || '';
            if (zhInput) zhInput.value = result.data.zh || '';
        } else {
            alert(result.message || 'Có lỗi xảy ra khi dịch.');
        }
    } catch (error) {
        console.error(error);
        alert('Có lỗi hệ thống khi kết nối tới AI.');
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}
</script>
@endpush
@endsection