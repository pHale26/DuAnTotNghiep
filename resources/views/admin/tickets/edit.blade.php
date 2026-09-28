@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Vé Tham Quan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Chỉnh sửa: {{ $ticket->title }}</h5>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data" id="ticketForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Thông tin cơ bản -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin cơ bản</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tên vé tham quan <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                               required placeholder="VD: Vé vào cửa Vinpearl Land Nha Trang" 
                                               value="{{ old('title', $ticket->title) }}">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Điểm đến <span class="text-danger">*</span></label>
                                        <select name="destination_id" class="form-select @error('destination_id') is-invalid @enderror" required>
                                            <option value="">-- Chọn điểm đến --</option>
                                            @foreach($destinations as $dest)
                                                <option value="{{ $dest->id }}" 
                                                    {{ old('destination_id', $ticket->destination_id) == $dest->id ? 'selected' : '' }}>
                                                    {{ $dest->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('destination_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Giá Người Lớn (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" name="adult_price" class="form-control" 
                                                   required placeholder="VD: 500000" min="0" value="{{ old('adult_price', $ticket->adult_price ?? 0) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Giá Trẻ Em (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" name="child_price" class="form-control" 
                                                   required placeholder="VD: 350000" min="0" value="{{ old('child_price', $ticket->child_price ?? 0) }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nhà cung cấp</label>
                                        <input type="text" name="provider_name" class="form-control" 
                                               placeholder="VD: Vinpearl, Sun World..." 
                                               value="{{ old('provider_name', $ticket->provider_name) }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Mô tả chi tiết</label>
                                        <textarea name="description" class="form-control" rows="4" 
                                                  placeholder="Mô tả về vé tham quan...">{{ old('description', $ticket->description) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Chính sách hủy vé</label>
                                        <textarea name="cancellation_policy" class="form-control" rows="3" 
                                                  placeholder="VD: Hoàn tiền 100% nếu hủy trước 24h...">{{ old('cancellation_policy', $ticket->cancellation_policy) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Hình ảnh -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>Hình ảnh</h6>
                                </div>
                                <div class="card-body">
                                    @if($ticket->ticket_images->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Hình ảnh hiện tại</label>
                                        <div class="row g-2">
                                            @foreach($ticket->ticket_images as $image)
                                            <div class="col-md-3 position-relative image-item" data-id="{{ $image->id }}">
                                                <img src="{{ asset($image->image_url) }}" class="img-fluid rounded" 
                                                     style="width: 100%; height: 150px; object-fit: cover;">
                                                <div class="position-absolute top-0 end-0 p-1">
                                                    <button type="button" class="btn btn-sm btn-danger delete-image" 
                                                            data-id="{{ $image->id }}" data-ticket-id="{{ $ticket->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                                @if($image->is_primary)
                                                <div class="position-absolute top-0 start-0 p-1">
                                                    <span class="badge bg-primary">Ảnh chính</span>
                                                </div>
                                                @else
                                                <div class="position-absolute bottom-0 start-0 p-1 w-100 text-center">
                                                    <button type="button" class="btn btn-sm btn-light btn-sm set-primary" 
                                                            data-id="{{ $image->id }}" data-ticket-id="{{ $ticket->id }}">
                                                        Đặt làm ảnh chính
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Thêm hình ảnh mới</label>
                                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                                        <div class="form-text">Tối đa 2MB/ảnh.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loại vé / Options -->
                            <div class="card mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Loại vé</h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="addOption">
                                        <i class="bi bi-plus-lg"></i> Thêm loại vé
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="optionsContainer">
                                        @foreach($ticket->ticket_options as $index => $option)
                                        <div class="option-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">Loại vé #{{ $index + 1 }}</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <input type="hidden" name="option_ids[]" value="{{ $option->id }}">
                                            
                                            <div class="mb-2">
                                                <label class="form-label fw-bold small">Tên loại vé <span class="text-danger">*</span></label>
                                                <input type="text" name="option_names[]" class="form-control form-control-sm" 
                                                       required placeholder="VD: Vé người lớn, Vé trẻ em..." 
                                                       value="{{ old('option_names.'.$index, $option->name) }}">
                                            </div>
                                            
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small">Giá bán <span class="text-danger">*</span></label>
                                                    <input type="number" name="option_prices[]" class="form-control form-control-sm" 
                                                           required min="0" placeholder="500000" 
                                                           value="{{ old('option_prices.'.$index, $option->price) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small">Giá gốc (nếu có)</label>
                                                    <input type="number" name="option_original_prices[]" class="form-control form-control-sm" 
                                                           min="0" placeholder="700000" 
                                                           value="{{ old('option_original_prices.'.$index, $option->original_price) }}">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <label class="form-label fw-bold small">Mô tả</label>
                                                <textarea name="option_descriptions[]" class="form-control form-control-sm" rows="2" 
                                                          placeholder="Mô tả loại vé...">{{ old('option_descriptions.'.$index, $option->description) }}</textarea>
                                            </div>
                                            
                                            <div class="mb-0">
                                                <label class="form-label fw-bold small">Điều kiện áp dụng</label>
                                                <textarea name="option_conditions[]" class="form-control form-control-sm" rows="2" 
                                                          placeholder="VD: Áp dụng cho trẻ em từ 1m-1m4...">{{ old('option_conditions.'.$index, $option->conditions) }}</textarea>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Liên kết Tour -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Liên kết Tour</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-text mb-2">Chọn các Tour được phép hiển thị vé này.</div>
                                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;" id="toursListContainer">
                                        <div id="noDestMsg" class="text-muted small mb-2" style="display: none;">Vui lòng chọn điểm đến để hiển thị danh sách Tour.</div>
                                        <div id="noToursMsg" class="text-muted small mb-2" style="display: none;">Không có Tour nào thuộc điểm đến này.</div>
                                        
                                        @forelse($tours as $tour)
                                        <div class="form-check mb-2 tour-item" data-destination="{{ $tour->destination_id }}">
                                            <input class="form-check-input" type="checkbox" name="tours[]" 
                                                   value="{{ $tour->id }}" id="tour_{{ $tour->id }}"
                                                   {{ in_array($tour->id, old('tours', $selectedTours)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tour_{{ $tour->id }}">
                                                {{ $tour->title }}
                                            </label>
                                        </div>
                                        @empty
                                        <div class="text-muted small empty-system-tours">Chưa có Tour nào trong hệ thống.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-light border">Hủy</a>
                        <button type="submit" class="btn btn-admin-primary">
                            <i class="bi bi-check-lg me-1"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    // Namespace để tránh init nhiều lần
    if (window.ticketEditInitialized) {
        return;
    }
    
    let optionIndex = {{ $ticket->ticket_options->count() }};

    const optionTemplate = (index) => `
        <div class="option-item border rounded p-3 mb-3" data-index="${index}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">Loại vé #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <input type="hidden" name="option_ids[]" value="">
            
            <div class="mb-2">
                <label class="form-label fw-bold small">Tên loại vé <span class="text-danger">*</span></label>
                <input type="text" name="option_names[]" class="form-control form-control-sm" 
                       required placeholder="VD: Vé người lớn, Vé trẻ em...">
            </div>
            
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Giá bán <span class="text-danger">*</span></label>
                    <input type="number" name="option_prices[]" class="form-control form-control-sm" 
                           required min="0" placeholder="500000">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Giá gốc (nếu có)</label>
                    <input type="number" name="option_original_prices[]" class="form-control form-control-sm" 
                           min="0" placeholder="700000">
                </div>
            </div>
            
            <div class="mb-2">
                <label class="form-label fw-bold small">Mô tả</label>
                <textarea name="option_descriptions[]" class="form-control form-control-sm" rows="2" 
                          placeholder="Mô tả loại vé..."></textarea>
            </div>
            
            <div class="mb-0">
                <label class="form-label fw-bold small">Điều kiện áp dụng</label>
                <textarea name="option_conditions[]" class="form-control form-control-sm" rows="2" 
                          placeholder="VD: Áp dụng cho trẻ em từ 1m-1m4..."></textarea>
            </div>
        </div>
    `;

    function initTicketEdit() {
        const addOptionBtn = document.getElementById('addOption');
        const optionsContainer = document.getElementById('optionsContainer');

        if (!addOptionBtn || !optionsContainer) {
            console.error('Required elements not found');
            return;
        }

        // Đánh dấu đã init
        window.ticketEditInitialized = true;

        // Thêm option mới
        addOptionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            optionsContainer.insertAdjacentHTML('beforeend', optionTemplate(optionIndex));
            optionIndex++;
        });

        // Xóa option
        optionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                const optionItems = document.querySelectorAll('.option-item');
                
                // Không cho xóa nếu chỉ còn 1 option
                if (optionItems.length <= 1) {
                    alert('Phải có ít nhất 1 loại vé!');
                    return;
                }
                
                const optionItem = e.target.closest('.option-item');
                optionItem.remove();
                
                // Cập nhật lại số thứ tự
                document.querySelectorAll('.option-item').forEach((item, idx) => {
                    item.querySelector('h6').textContent = `Loại vé #${idx + 1}`;
                });
            }
        });

        // Xóa ảnh
        document.querySelectorAll('.delete-image').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) return;
                
                const imageId = this.dataset.id;
                const ticketId = this.dataset.ticketId;
                const imageItem = this.closest('.image-item');
                
                fetch(`/admin/tickets/${ticketId}/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        imageItem.remove();
                        alert('Đã xóa ảnh thành công.');
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra khi xóa ảnh.');
                });
            });
        });

        // Đặt ảnh chính
        document.querySelectorAll('.set-primary').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.dataset.id;
                const ticketId = this.dataset.ticketId;
                
                fetch(`/admin/tickets/${ticketId}/images/${imageId}/set-primary`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra.');
                });
            });
        });

        // Lọc tour theo điểm đến
        const destSelect = document.querySelector('select[name="destination_id"]');
        const tourItems = document.querySelectorAll('.tour-item');
        const noDestMsg = document.getElementById('noDestMsg');
        const noToursMsg = document.getElementById('noToursMsg');
        const emptySystemMsg = document.querySelector('.empty-system-tours');

        function filterTours(isInitialLoad = false) {
            if (!destSelect || emptySystemMsg) return;
            const selectedDestId = destSelect.value;
            let visibleCount = 0;

            if (!selectedDestId) {
                tourItems.forEach(item => {
                    item.style.display = 'none';
                    if (!isInitialLoad) {
                        const cb = item.querySelector('input[type="checkbox"]');
                        if (cb) cb.checked = false;
                    }
                });
                if(noDestMsg) noDestMsg.style.display = 'block';
                if(noToursMsg) noToursMsg.style.display = 'none';
                return;
            }

            if(noDestMsg) noDestMsg.style.display = 'none';

            tourItems.forEach(item => {
                if (item.dataset.destination === selectedDestId) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                    if (!isInitialLoad) {
                        const cb = item.querySelector('input[type="checkbox"]');
                        if (cb) cb.checked = false;
                    }
                }
            });

            if (visibleCount === 0) {
                if(noToursMsg) noToursMsg.style.display = 'block';
            } else {
                if(noToursMsg) noToursMsg.style.display = 'none';
            }
        }

        if (destSelect) {
            destSelect.addEventListener('change', () => filterTours(false));
            filterTours(true);
        }
    }

    // Khởi tạo ngay lập tức
    initTicketEdit();
})();
</script>
@endpush
