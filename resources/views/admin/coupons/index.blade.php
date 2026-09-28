@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Quản lý mã giảm giá</h2>

    <div>
        <a href="{{ route('admin.coupons.trash') }}" class="btn btn-admin btn-light border text-danger me-2">
            <i class="bi bi-trash"></i> Thùng rác
        </a>

        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm mã giảm giá
        </a>
    </div>
</div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã giảm giá</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Đã dùng</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td><strong>{{ $coupon->code }}</strong></td>

                            <td>
                                {{ $coupon->discount_type == 'percent' ? 'Phần trăm' : 'Tiền mặt' }}
                            </td>

                            <td>
                                @if($coupon->discount_type == 'percent')
                                    {{ $coupon->discount_value }}%
                                @else
                                    {{ number_format($coupon->discount_value, 0, ',', '.') }}đ
                                @endif
                            </td>

                            <td>
                                {{ $coupon->valid_from ? $coupon->valid_from->format('d/m/Y') : '' }}
                            </td>

                            <td>
                                {{ $coupon->valid_until ? $coupon->valid_until->format('d/m/Y') : '' }}
                            </td>

                            <td>
                                {{ $coupon->used_count ?? 0 }}
                                @if($coupon->usage_limit)
                                    / {{ $coupon->usage_limit }}
                                @endif
                            </td>

                            <td>
                                @if($coupon->valid_until && $coupon->valid_until->isPast())
                                    <span class="badge bg-danger">Hết hạn</span>
                                @else
                                    <span class="badge bg-success">Hoạt động</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>

                                <form action="{{ route('admin.coupons.destroy', $coupon) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa mã {{ $coupon->code }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Chưa có mã giảm giá nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
