@extends('layouts.admin')

@section('page-title', 'Quản lý Đánh giá')

@section('content')
<div class="admin-card">
    <div class="admin-card-header d-flex justify-content-between align-items-center">
        <h3 class="admin-card-title m-0">Danh sách Đánh giá từ Khách hàng</h3>
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <select name="rating" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả số sao</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Sao</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Sao</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Sao</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Sao</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Sao</option>
            </select>
        </form>
    </div>
    
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>Khách hàng</th>
                        <th>Tour / Điểm đến</th>
                        <th>Đánh giá</th>
                        <th>Nội dung</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($review->user && $review->user->avatar)
                                        <img src="{{ asset($review->user->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover" width="32" height="32">
                                    @else
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="fw-medium text-dark">{{ $review->user->name ?? 'Khách ẩn danh' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark text-truncate" style="max-width: 200px;" title="{{ $review->tour->title ?? '' }}">
                                    {{ $review->tour->title ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-warning small" style="white-space: nowrap;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;" title="{{ $review->comment }}">
                                    {{ $review->comment }}
                                </div>
                            </td>
                            <td class="text-muted small">
                                {{ $review->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center">
                                @if($review->is_hidden)
                                    <span class="badge badge-soft-danger"><i class="bi bi-eye-slash me-1"></i>Đã ẩn</span>
                                @else
                                    <span class="badge badge-soft-success"><i class="bi bi-eye me-1"></i>Hiển thị</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-action text-info" title="Xem chi tiết" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $review->id }}">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                <form action="{{ route('admin.reviews.toggle-hidden', $review->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-action {{ $review->is_hidden ? 'text-success' : 'text-danger' }}" 
                                            title="{{ $review->is_hidden ? 'Hiện đánh giá' : 'Ẩn đánh giá' }}"
                                            onclick="return confirm('Bạn có chắc chắn muốn {{ $review->is_hidden ? 'hiện' : 'ẩn' }} đánh giá này?')">
                                        <i class="bi {{ $review->is_hidden ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Chi tiết đánh giá -->
                        <div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $review->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reviewModalLabel{{ $review->id }}">Chi tiết đánh giá</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3 d-flex align-items-center gap-3">
                                            @if($review->user && $review->user->avatar)
                                                <img src="{{ asset($review->user->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover" width="48" height="48">
                                            @else
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 20px;">
                                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $review->user->name ?? 'Khách ẩn danh' }}</h6>
                                                <div class="text-muted small">{{ $review->created_at->format('d/m/Y H:i:s') }}</div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Tour / Điểm đến:</strong> 
                                            <span class="text-dark">{{ $review->tour->title ?? 'N/A' }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Đánh giá chung:</strong>
                                            <span class="text-warning ms-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endfor
                                            </span>
                                            ({{ $review->rating }} sao)
                                        </div>
                                        @if($review->guide_rating)
                                        <div class="mb-3">
                                            <strong>Đánh giá HDV ({{ $review->tour_guide->name ?? 'HDV' }}):</strong>
                                            <span class="text-primary ms-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi {{ $i <= $review->guide_rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endfor
                                            </span>
                                            ({{ $review->guide_rating }} sao)
                                        </div>
                                        @endif
                                        <div class="mb-0">
                                            <strong>Nội dung chi tiết:</strong>
                                            <p class="mt-2 text-dark" style="white-space: pre-wrap;">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <div class="mb-3"><i class="bi bi-chat-square-text fs-1 opacity-50"></i></div>
                                Chưa có đánh giá nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reviews->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
