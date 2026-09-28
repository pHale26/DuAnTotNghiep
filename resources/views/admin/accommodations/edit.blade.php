@extends('layouts.admin')

@section('title', 'Sửa Lưu trú')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cập nhật Khách sạn/Resort: {{ $accommodation->name }}</h1>
        <a href="{{ route('admin.accommodations.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.accommodations.update', $accommodation->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Thông tin cơ bản -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin chung</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tên Khách sạn/Resort <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $accommodation->name) }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Điểm đến <span class="text-danger">*</span></label>
                        <select class="form-select" name="destination_id" id="destination_id" required>
                            <option value="">Chọn điểm đến</option>
                            @foreach($destinations as $dest)
                                <option value="{{ $dest->id }}" {{ old('destination_id', $accommodation->destination_id) == $dest->id ? 'selected' : '' }}>{{ $dest->name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Hạng sao <span class="text-danger">*</span></label>
                        <select class="form-select" name="star_rating" required>
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}" {{ old('star_rating', $accommodation->star_rating) == $i ? 'selected' : '' }}>{{ $i }} Sao</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Tour áp dụng <span class="text-muted">(Chỉ hiển thị các Tour thuộc Điểm đến đã chọn)</span></label>
                        <select class="form-select" name="tour_ids[]" id="tour_ids" multiple>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}" data-destination-id="{{ $tour->destination_id }}"
                                    {{ in_array($tour->id, $accommodation->tour_ids) || (is_array(old('tour_ids')) && in_array($tour->id, old('tour_ids'))) ? 'selected' : '' }}>
                                    {{ $tour->title ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Địa chỉ chi tiết</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address', $accommodation->address) }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Mô tả thêm</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $accommodation->description) }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" value="1" {{ old('is_active', $accommodation->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="isActive">Kích hoạt khách sạn này</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hạng phòng -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách Hạng phòng (Room Types)</h6>
                <button type="button" class="btn btn-sm btn-success" id="add-room-btn">
                    <i class="bi bi-plus-lg"></i> Thêm hạng phòng
                </button>
            </div>
            <div class="card-body" id="room-container">
                @foreach($accommodation->room_types as $index => $room)
                    <div class="room-item border rounded p-3 mb-3 position-relative bg-light">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-room-btn"><i class="bi bi-trash"></i></button>
                        <input type="hidden" name="rooms[{{ $index }}][id]" value="{{ $room->id }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold">Tên hạng phòng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="rooms[{{ $index }}][name]" value="{{ $room->name }}" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold">Sức chứa cơ bản <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="rooms[{{ $index }}][base_capacity]" value="{{ $room->base_capacity }}" min="1" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold">Sức chứa tối đa (Max) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="rooms[{{ $index }}][max_capacity]" value="{{ $room->max_capacity }}" min="1" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold">Số lượng phòng <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="rooms[{{ $index }}][total_rooms]" value="{{ $room->total_rooms }}" min="1" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold">Giá cơ bản (VND) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="rooms[{{ $index }}][base_price]" value="{{ intval($room->base_price) }}" required min="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold">Giá Giường phụ (VND)</label>
                                <input type="number" class="form-control" name="rooms[{{ $index }}][extra_bed_price]" value="{{ intval($room->extra_bed_price) }}" min="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label fw-bold">Phụ thu Trẻ em (VND)</label>
                                <input type="number" class="form-control" name="rooms[{{ $index }}][child_surcharge_price]" value="{{ intval($room->child_surcharge_price) }}" min="0">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-primary px-5 py-2">Lưu thông tin</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let roomIndex = {{ count($accommodation->room_types) }};
        const container = document.getElementById('room-container');
        const addBtn = document.getElementById('add-room-btn');

        updateRemoveButtons();

        addBtn.addEventListener('click', function() {
            const template = `
                <div class="room-item border rounded p-3 mb-3 position-relative bg-light">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-room-btn"><i class="bi bi-trash"></i></button>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-bold">Tên hạng phòng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="rooms[${roomIndex}][name]" placeholder="VD: Standard Double" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-bold">Sức chứa cơ bản <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="rooms[${roomIndex}][base_capacity]" value="2" min="1" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-bold">Sức chứa tối đa (Max) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="rooms[${roomIndex}][max_capacity]" value="3" min="1" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-bold">Số lượng phòng <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="rooms[${roomIndex}][total_rooms]" value="10" min="1" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold">Giá cơ bản (VND) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="rooms[${roomIndex}][base_price]" placeholder="1000000" required min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold">Giá Giường phụ (VND)</label>
                            <input type="number" class="form-control" name="rooms[${roomIndex}][extra_bed_price]" placeholder="400000" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold">Phụ thu Trẻ em (VND)</label>
                            <input type="number" class="form-control" name="rooms[${roomIndex}][child_surcharge_price]" placeholder="200000" min="0">
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            roomIndex++;
            updateRemoveButtons();
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-room-btn')) {
                const item = e.target.closest('.room-item');
                // Nếu item có hidden id thì cần logic xóa AJAX hoặc chỉ disable input, 
                // nhưng ở đây khi submit server sẽ xóa các room ko có trong mảng. Nên xóa DOM là đủ.
                item.remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const items = container.querySelectorAll('.room-item');
            items.forEach((item, index) => {
                const btn = item.querySelector('.remove-room-btn');
                if (items.length === 1) {
                    btn.style.display = 'none';
                } else {
                    btn.style.display = 'block';
                }
            });
        }

        // Initialize Select2 on Tour select
        $('#tour_ids').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Chọn Tour áp dụng --',
            allowClear: true,
            closeOnSelect: false
        });

        const tourSelect = $('#tour_ids');
        const originalTourOptions = tourSelect.find('option').clone();
        let isInitialLoad = true;

        function filterTours() {
            const destId = $('#destination_id').val();
            const currentVals = isInitialLoad ? (tourSelect.val() || []) : [];
            tourSelect.empty();
            
            if (destId) {
                originalTourOptions.each(function() {
                    const opt = $(this);
                    if (opt.data('destination-id') == destId) {
                        const clonedOpt = opt.clone();
                        if (isInitialLoad && currentVals.includes(clonedOpt.val())) {
                            clonedOpt.prop('selected', true);
                        }
                        tourSelect.append(clonedOpt);
                    }
                });
                tourSelect.prop('disabled', false);
            } else {
                tourSelect.prop('disabled', true);
            }
            
            tourSelect.trigger('change');
            isInitialLoad = false;
        }

        $('#destination_id').on('change', filterTours);
        // Run filter on initial load
        filterTours();
    });
</script>
@endsection
