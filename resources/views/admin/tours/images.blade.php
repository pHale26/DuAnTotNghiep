@extends('layouts.admin')
@section('page-title', 'Thư viện ảnh: ' . $tour->title)

@section('content')
    <!-- Thêm Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.tours.index') }}" class="text-decoration-none fw-semibold">
                    <i class="bi bi-box-seam me-1"></i>Quản lý Tour
                </a>
            </li>
            <li class="breadcrumb-item text-muted">{{ \Illuminate\Support\Str::limit($tour->title, 40) }}</li>
            <li class="breadcrumb-item active fw-bold" aria-current="page">Thư viện ảnh</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Thêm ảnh mới (Nhập URL)</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger small">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success small">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('admin.tours.images.store', $tour->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn hình ảnh từ máy tính</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
                            <div class="form-text">Bạn có thể chọn nhiều ảnh cùng một lúc.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-upload me-2"></i> Tải ảnh lên
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Thư viện hiện tại</div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($tour->tour_images as $img)
                            <div class="col-md-6">
                                <div class="position-relative border rounded p-2">
                                    <img src="{{ $img->image_url }}" class="img-fluid rounded"
                                        style="height: 150px; width: 100%; object-fit: cover;">
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        @if($img->is_primary)
                                            <span class="badge bg-success"><i class="bi bi-star-fill"></i> Ảnh chính</span>
                                        @else
                                            <form action="{{ route('admin.tours.images.set-primary', [$tour->id, $img->id]) }}"
                                                method="POST">
                                                @csrf
                                                <button class="btn btn-sm btn-light border">Đặt làm ảnh chính</button>
                                            </form>
                                        @endif
                                        {{-- Nút xóa — mở modal thay vì dùng confirm() --}}
                                        <button type="button"
                                            class="btn btn-sm btn-danger text-white shadow-sm btn-delete-image" title="Xóa ảnh"
                                            data-action="{{ route('admin.tours.images.destroy', [$tour->id, $img->id]) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal xác nhận xóa ảnh --}}
    <div class="modal fade" id="deleteImageModal" tabindex="-1" aria-labelledby="deleteImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteImageModalLabel"><i class="bi bi-exclamation-triangle me-2"></i>Xác
                        nhận xóa ảnh</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa ảnh này khỏi thư viện? <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">
                        <i class="bi bi-trash me-1"></i> Xóa ảnh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden form dùng để submit DELETE request --}}
    <form id="delete-image-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Khi click nút xóa → lấy action URL → mở modal
        document.querySelectorAll('.btn-delete-image').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.getAttribute('data-action');
                document.getElementById('delete-image-form').action = action;
                const modal = new bootstrap.Modal(document.getElementById('deleteImageModal'));
                modal.show();
            });
        });

        // Khi xác nhận trong modal → submit form
        document.getElementById('btn-confirm-delete').addEventListener('click', function () {
            document.getElementById('delete-image-form').submit();
        });
    </script>
@endsection