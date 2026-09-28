@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Điểm đến và khởi hành: ' . $destination->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.destinations.index') }}"
                class="text-decoration-none fw-semibold"><i class="bi bi-geo-alt me-1"></i>Quản lý Điểm đến và khởi hành</a></li>
        <li class="breadcrumb-item text-muted">{{ \Illuminate\Support\Str::limit($destination->name, 30) }}</li>
        <li class="breadcrumb-item active fw-bold" aria-current="page">Chỉnh sửa</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('admin.destinations.update', $destination->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Tên điểm đến <span class="text-danger">*</span></label>
                        
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">Tiếng Việt</span>
                            <input type="text" class="form-control @error('name.vi') is-invalid @enderror" 
                                name="name[vi]" value="{{ old('name.vi', $destination->getTranslation('name', 'vi', false)) }}" required>
                        </div>
                        @error('name.vi') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">English</span>
                            <input type="text" class="form-control @error('name.en') is-invalid @enderror" 
                                name="name[en]" value="{{ old('name.en', $destination->getTranslation('name', 'en', false)) }}">
                        </div>
                        @error('name.en') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                        <div class="input-group mb-2">
                            <span class="input-group-text" style="width: 100px;">中文</span>
                            <input type="text" class="form-control @error('name.zh') is-invalid @enderror" 
                                name="name[zh]" value="{{ old('name.zh', $destination->getTranslation('name', 'zh', false)) }}">
                        </div>
                        @error('name.zh') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Mô tả (Tuỳ chọn)</label>
                        
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">Tiếng Việt</span>
                            <textarea class="form-control @error('description.vi') is-invalid @enderror" 
                                name="description[vi]" rows="3">{{ old('description.vi', $destination->getTranslation('description', 'vi', false)) }}</textarea>
                        </div>
                        
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">English</span>
                            <textarea class="form-control @error('description.en') is-invalid @enderror" 
                                name="description[en]" rows="3">{{ old('description.en', $destination->getTranslation('description', 'en', false)) }}</textarea>
                        </div>
                        
                        <div class="mb-2">
                            <span class="badge bg-secondary mb-1">中文</span>
                            <textarea class="form-control @error('description.zh') is-invalid @enderror" 
                                name="description[zh]" rows="3">{{ old('description.zh', $destination->getTranslation('description', 'zh', false)) }}</textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Ảnh đại diện mới (Bỏ trống nếu giữ nguyên ảnh
                            cũ)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                            name="image" accept="image/*">
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if($destination->image_url)
                        <div class="mt-3">
                            <div class="text-muted mb-2">Ảnh hiện tại:</div>
                            <img src="{{ $destination->image_url }}" alt="Current Image"
                                class="rounded border shadow-sm" style="max-height: 150px;">
                        </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.destinations.index') }}" class="btn btn-light border">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary">Cập nhật thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection