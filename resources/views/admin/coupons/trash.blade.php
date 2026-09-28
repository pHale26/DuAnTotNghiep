@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Thùng rác mã giảm giá</h2>

        <a href="{{ route('admin.coupons.index') }}" class="btn btn-primary">
            Quay lại
        </a>
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
                        <th>Ngày xóa</th>
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
                                {{ $coupon->deleted_at ? $coupon->deleted_at->format('d/m/Y H:i') : '' }}
                            </td>

                            <td class="text-center">
                                <form action="{{ route('admin.coupons.restore', $coupon->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </button>
                                </form>

                                <form action="{{ route('admin.coupons.forceDelete', $coupon->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Xóa vĩnh viễn mã {{ $coupon->code }}?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Xóa vĩnh viễn
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Thùng rác trống
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
