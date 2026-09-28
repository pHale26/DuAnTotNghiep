@extends('layouts.admin')

@section('page-title', 'Quản lý Vé Tham Quan')

@section('content')
<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Quản lý Vé Tham Quan</h5>
        <div>
            <a href="{{ route('admin.tickets.trash') }}" class="btn btn-admin btn-light border text-danger me-2">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
            <a href="{{ route('admin.tickets.create') }}" class="btn btn-admin btn-admin-primary no-pjax">
                <i class="bi bi-plus-lg me-1"></i> Thêm Vé Tham Quan
            </a>
        </div>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Hình ảnh</th>
                        <th>Thông tin Vé</th>
                        <th>Điểm đến</th>
                        <th>Số loại vé</th>
                        <th>Giá từ</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="ps-4">
                            @php
                                $primaryImage = $ticket->ticket_images->where('is_primary', true)->first() 
                                    ?? $ticket->ticket_images->first();
                            @endphp
                            <div class="rounded shadow-sm overflow-hidden" style="width: 64px; height: 64px;">
                                @if($primaryImage)
                                    <img src="{{ asset($primaryImage->image_url) }}" alt="{{ $ticket->title }}" 
                                         class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                        <i class="bi bi-ticket-perforated text-muted fs-4"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate" style="max-width: 300px;" title="{{ $ticket->title }}">
                                {{ $ticket->title }}
                            </div>
                            <small class="text-muted">
                                @if($ticket->provider_name)
                                    {{ $ticket->provider_name }}
                                @else
                                    ID: #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                @endif
                            </small>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-primary px-3">
                                <i class="bi bi-geo-alt me-1"></i>{{ $ticket->destination->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-secondary px-3">
                                <i class="bi bi-ticket-detailed me-1"></i>{{ $ticket->ticket_options->count() }} loại
                            </span>
                        </td>
                        <td>
                            @php
                                $minPrice = $ticket->ticket_options->min('price');
                            @endphp
                            @if($minPrice)
                                <span class="fw-bold text-danger fs-6">{{ number_format($minPrice, 0, ',', '.') }} ₫</span>
                            @else
                                <span class="text-muted">Chưa có giá</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.tickets.edit', $ticket->id) }}"
                                   class="btn btn-action text-primary bg-primary bg-opacity-10 no-pjax" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.tickets.destroy', $ticket->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa vé này? Tất cả loại vé và hình ảnh sẽ bị xóa.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-action text-danger bg-danger bg-opacity-10" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-ticket-perforated fs-1 text-light mb-2 d-block"></i>
                            Chưa có vé tham quan nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tickets->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
