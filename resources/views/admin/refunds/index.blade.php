@extends('layouts.admin')

@section('title', 'Quản lý yêu cầu hoàn tiền')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Yêu cầu hoàn tiền</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách yêu cầu</h6>
            <form action="{{ route('admin.refunds.index') }}" method="GET" class="d-flex">
                <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn tiền</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Số tiền hoàn</th>
                            <th>Thông tin ngân hàng</th>
                            <th>Ghi chú tính toán</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.index', ['search' => $refund->booking_id]) }}" class="fw-bold">
                                        #{{ $refund->booking->code }}
                                    </a>
                                </td>
                                <td><strong class="text-danger">{{ number_format($refund->amount) }} đ</strong></td>
                                <td>
                                    <div class="small">
                                        Ngân hàng: <strong>{{ $refund->bank_name ?? 'N/A' }}</strong><br>
                                        Chủ TK: <strong>{{ $refund->bank_account_name ?? 'N/A' }}</strong><br>
                                        Số TK: <strong>{{ $refund->bank_account_number ?? 'N/A' }}</strong>
                                    </div>
                                </td>
                                <td><span class="small">{{ $refund->reason }}</span></td>
                                <td>
                                    @if($refund->status === 'pending')
                                        <span class="badge bg-warning text-dark">Đang xử lý</span>
                                    @elseif($refund->status === 'completed')
                                        <span class="badge bg-success">Đã hoàn</span>
                                        <div class="small mt-1 text-muted">Mã GD: {{ $refund->transaction_reference }}</div>
                                    @else
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td>
                                    @if($refund->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $refund->id }}">
                                            <i class="bi bi-check-circle"></i> Duyệt
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $refund->id }}">
                                            <i class="bi bi-x-circle"></i> Từ chối
                                        </button>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $refund->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="action" value="approve">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Xác nhận đã chuyển khoản hoàn tiền</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Vui lòng chuyển khoản số tiền <strong class="text-danger">{{ number_format($refund->amount) }} đ</strong> tới:</p>
                                                            <ul class="mb-3">
                                                                <li>Ngân hàng: <strong>{{ $refund->bank_name }}</strong></li>
                                                                <li>Chủ TK: <strong>{{ $refund->bank_account_name }}</strong></li>
                                                                <li>Số TK: <strong>{{ $refund->bank_account_number }}</strong></li>
                                                            </ul>
                                                            <div class="mb-3">
                                                                <label>Mã giao dịch (Tham chiếu) <span class="text-danger">*</span></label>
                                                                <input type="text" name="transaction_reference" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success">Xác nhận Đã hoàn tiền</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $refund->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.refunds.process', $refund->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="action" value="reject">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger">Từ chối hoàn tiền</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label>Lý do từ chối <span class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-danger">Xác nhận Từ chối</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Không có yêu cầu hoàn tiền nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $refunds->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
