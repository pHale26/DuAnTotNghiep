@extends('layouts.admin')

@section('page-title', 'Quản lý Đơn đặt chỗ')

@section('content')
@if(($invoiceRequestCount ?? 0) > 0)
    <a href="{{ route('admin.bookings.index', [
            'invoice_status' => 'requested'
        ]) }}"
       class="alert alert-warning d-flex align-items-center justify-content-between text-decoration-none shadow-sm">

        <div>
            <i class="bi bi-receipt-cutoff me-2"></i>

            Có
            <strong>{{ $invoiceRequestCount }}</strong>
            khách hàng đang yêu cầu xuất hóa đơn.
        </div>

        <span class="btn btn-sm btn-warning">
            Xem yêu cầu
        </span>
    </a>
@endif

<div class="admin-card border-0 mb-4">
    <div class="admin-card-body">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Tìm mã đơn, PNR, tên khách, số điện thoại..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="payment_status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- TT Thanh Toán --</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="paid_30" {{ request('payment_status') == 'paid_30' ? 'selected' : '' }}>Đã thanh toán 30% (Cọc)</option>
                    <option value="paid_100" {{ request('payment_status') == 'paid_100' ? 'selected' : '' }}>Đã thanh toán 100%</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tour_status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- TT Tour --</option>
                    <option value="upcoming" {{ request('tour_status') == 'upcoming' ? 'selected' : '' }}>Sắp khởi hành</option>
                    <option value="in_progress" {{ request('tour_status') == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                    <option value="checking_in" {{ request('tour_status') == 'checking_in' ? 'selected' : '' }}>Đang check in</option>
                    <option value="completed" {{ request('tour_status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled_by_customer" {{ request('tour_status') == 'cancelled_by_customer' ? 'selected' : '' }}>Hủy do khách</option>
                    <option value="cancelled_by_admin" {{ request('tour_status') == 'cancelled_by_admin' ? 'selected' : '' }}>Hủy do admin</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-admin btn-admin-primary w-100">Lọc</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-admin btn-light border w-100">Làm mới</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-card border-0">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-list-ul me-2"></i>Danh sách Đơn đặt chỗ</h5>
        <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-admin btn-admin-primary"><i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel</a>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive" style="min-height: 400px;">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Mã Đơn / Ngày</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Thanh Toán</th>
                        <th>TT Thanh Toán</th>
                        <th>TT Tour</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="booking-row" id="booking-row-{{ $booking->id }}" data-booking-id="{{ $booking->id }}" data-payment-status="{{ $booking->payment_status }}" data-tour-status="{{ $booking->tour_status }}">
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $booking->code }}</div>
                            <div class="small text-muted">{{ $booking->created_at->format('H:i d/m/Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $booking->customer_name ?? ($booking->user->name ?? 'Khách lẻ') }}</div>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $booking->customer_phone ?? ($booking->user->phone ?? 'N/A') }}</div>
                        </td>
                        <td>
                            <div class="fw-500 text-dark text-truncate" style="max-width: 220px;" title="{{ $booking->tour_schedule->tour->title ?? '' }}">
                                {{ $booking->tour_schedule->tour->title ?? 'N/A' }}
                            </div>
                            <div class="small text-primary mt-1"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">{{ number_format($booking->total_price, 0, ',', '.') }} ₫</div>
                            <div class="small text-muted mt-1">{{ $booking->adults_count + $booking->children_count }} khách</div>
                        </td>
                        <td id="payment-status-badge-{{ $booking->id }}">
                            @php
                            $paymentStatusMap = [
                                'pending' => ['badge-soft-warning', 'Chờ thanh toán'],
                                'paid_30' => ['badge-soft-info', 'Đã thanh toán 30% (Cọc)'],
                                'paid_100' => ['badge-soft-success', 'Đã thanh toán 100%'],
                                'failed' => ['badge-soft-danger', 'Thất bại']
                            ];
                            $ps = $paymentStatusMap[$booking->payment_status] ?? ['badge-soft-secondary', 'N/A'];
                            @endphp
                            <span class="badge-soft {{ $ps[0] }}">
                                {{ $ps[1] }}
                            </span>
                        </td>
                        <td id="tour-status-badge-{{ $booking->id }}">
                            @php
                            $tourStatusMap = [
                                'upcoming' => ['badge-soft-primary', 'Sắp bắt đầu'],
                                'in_progress' => ['badge-soft-warning', 'Đang thực hiện'],
                                'checking_in' => ['badge-soft-info', 'Đang check-in'],
                                'completed' => ['badge-soft-success', 'Hoàn thành'],
                                'cancelled_by_customer' => ['badge-soft-danger', 'Hủy (Khách)'],
                                'cancelled_by_admin' => ['badge-soft-danger', 'Hủy (Admin)']
                            ];
                            $ts = $tourStatusMap[$booking->tour_status] ?? ['badge-soft-secondary', 'N/A'];
                            @endphp
                            <span class="badge-soft {{ $ts[0] }}">
                                {{ $ts[1] }}
                            </span>
                            @if($booking->tour_status == 'checking_in' && $booking->current_checkin_step)
                                <div class="small mt-1 text-info">
                                    <i class="bi bi-geo-alt-fill me-1"></i> Điểm: {{ $booking->current_checkin_step }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @php
                            $tourStatusLocked = in_array($booking->tour_status, ['in_progress', 'checking_in', 'completed']);
                            @endphp
                            <div class="dropdown">
                                <button class="btn btn-action" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 200px;">
                                    <li>
                                        <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#bookingDetail{{ $booking->id }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#updateStatus{{ $booking->id }}">
                                            @if($tourStatusLocked)
                                                <i class="bi bi-credit-card me-2 text-info"></i> Cập nhật thanh toán
                                            @else
                                                <i class="bi bi-pencil-square me-2 text-warning"></i> Cập nhật trạng thái
                                            @endif
                                        </a>
                                    </li>
                                    <li>
    <hr class="dropdown-divider">
</li>

<li>
    <a class="dropdown-item py-2"
       href="{{ route('admin.bookings.invoice', $booking->id) }}">

        <i class="bi bi-receipt-cutoff me-2 text-success"></i>
        Xuất hóa đơn
    </a>
</li>
@if(($booking->invoice_status ?? 'none') === 'requested')
    <li>
        <hr class="dropdown-divider">
    </li>

    <li>
        <a class="dropdown-item py-2"
           href="{{ route('admin.bookings.invoice', $booking->id) }}">
            <i class="bi bi-receipt-cutoff me-2 text-warning"></i>
            Xử lý yêu cầu hóa đơn
        </a>
    </li>
@elseif(($booking->invoice_status ?? 'none') === 'sent')
    <li>
        <a class="dropdown-item py-2 disabled" href="#">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            Hóa đơn đã gửi
        </a>
    </li>
@endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào khớp với yêu cầu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $bookings->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

@foreach($bookings as $booking)
<div class="modal fade" id="bookingDetail{{ $booking->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3" style="background-color: var(--admin-sidebar); color: white;">
                <h5 class="modal-title fw-600">Chi Tiết Đơn Đặt Chỗ {{ $booking->code }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6 border-end pe-md-4">
                        <h6 class="text-primary mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-person-badge me-2"></i>Thông tin người đặt</h6>
                        <div class="mb-2"><span class="text-muted me-2">Họ tên:</span> <strong class="text-dark">{{ $booking->customer_name ?? ($booking->user->name ?? 'N/A') }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">SĐT:</span> <strong class="text-dark">{{ $booking->customer_phone ?? ($booking->user->phone ?? 'N/A') }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Email:</span> <strong class="text-dark">{{ $booking->customer_email ?? ($booking->user->email ?? 'N/A') }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Ngày đặt:</span> <strong class="text-dark">{{ $booking->created_at->format('H:i d/m/Y') }}</strong></div>
                        @if($booking->cancel_reason)
                            <div class="mb-2"><span class="text-danger me-2">Lý do hủy:</span> <strong class="text-danger">{{ $booking->cancel_reason }}</strong></div>
                        @endif
                    </div>

                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-primary mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-briefcase me-2"></i>Sản phẩm (Tour)</h6>
                        <div class="mb-2"><span class="text-muted me-2">Tên tour:</span> <strong class="text-dark">{{ $booking->tour_schedule->tour->title ?? 'N/A' }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Khởi hành:</span> <strong class="text-dark">{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Hành khách:</span> <strong class="text-dark">{{ $booking->adults_count }} NL, {{ $booking->children_count }} TE</strong></div>
                        <div class="mb-2"><span class="text-muted me-2">Vận chuyển:</span>
                            @if($booking->transport_type == 'flight')
                                <span class="badge bg-danger mb-2">Máy bay</span>
                                <form action="{{ route('admin.bookings.update_pnr', $booking->id) }}" method="POST" class="d-inline-flex align-items-center mt-1">
                                    @csrf
                                    <input type="text" name="pnr_code" value="{{ $booking->pnr_code }}" class="form-control form-control-sm me-2" placeholder="Nhập mã PNR..." style="max-width: 150px;">
                                    <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                                </form>
                            @elseif($booking->transport_type == 'bus')
                                <span class="badge bg-info text-white">Xe khách / Ô tô</span>
                            @else
                                <span class="text-muted">Tự túc</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-primary mt-3 mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-people me-2"></i>Danh Sách Hành Khách</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover border-light mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2">Họ và tên</th>
                                        <th class="py-2">Giới tính</th>
                                        <th class="py-2">Ngày sinh</th>
                                        <th class="py-2">CCCD / Hộ chiếu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($booking->booking_passengers as $passenger)
                                    <tr>
                                        <td class="fw-500">{{ $passenger->full_name }}</td>
                                        <td>{{ $passenger->gender == 'male' ? 'Nam' : 'Nữ' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($passenger->date_of_birth)->format('d/m/Y') }}</td>
                                        <td class="text-primary">{{ $passenger->identity_number }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có dữ liệu hành khách chi tiết.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(isset($booking->user->identity) && ($booking->user->identity->front_image_url || $booking->user->identity->back_image_url))
                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-primary mt-3 mb-3 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-card-image me-2"></i>Ảnh Định Danh Đại Diện</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small mb-2 text-center">Mặt trước CCCD</div>
                                @if($booking->user->identity->front_image_url)
                                    @php
                                        $fUrl = $booking->user->identity->front_image_url;
                                        $fSrc = str_starts_with($fUrl, 'private/') ? route('admin.bookings.identity.image', basename($fUrl)) : asset($fUrl);
                                    @endphp
                                    <img src="{{ $fSrc }}" class="img-fluid rounded border shadow-sm w-100 object-fit-cover" style="height: 150px;" alt="Mặt trước">
                                @else
                                    <div class="text-center text-muted border rounded p-4">Không có ảnh</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-2 text-center">Mặt sau CCCD</div>
                                @if($booking->user->identity->back_image_url)
                                    @php
                                        $bUrl = $booking->user->identity->back_image_url;
                                        $bSrc = str_starts_with($bUrl, 'private/') ? route('admin.bookings.identity.image', basename($bUrl)) : asset($bUrl);
                                    @endphp
                                    <img src="{{ $bSrc }}" class="img-fluid rounded border shadow-sm w-100 object-fit-cover" style="height: 150px;" alt="Mặt sau">
                                @else
                                    <div class="text-center text-muted border rounded p-4">Không có ảnh</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">Tổng cộng cần thanh toán:</span>
                    <span class="fs-4 text-danger mb-0 ms-2 fw-bold">{{ number_format($booking->total_price, 0, ',', '.') }} ₫</span>
                </div>
                <button type="button" class="btn btn-admin btn-light border" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cập nhật Trạng thái -->
<div class="modal fade" id="updateStatus{{ $booking->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h5 class="modal-title fw-bold text-dark">
                    Cập nhật trạng thái {{ $booking->code }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.bookings.update_status', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" {{ $booking->payment_status == 'pending' ? 'selected' : '' }}>Chưa thanh toán (Pending)</option>
                            <option value="paid_30" {{ $booking->payment_status == 'paid_30' ? 'selected' : '' }}>Đã cọc 30%</option>
                            <option value="paid_100" {{ $booking->payment_status == 'paid_100' ? 'selected' : '' }}>Đã thanh toán 100%</option>
                            <option value="failed" {{ $booking->payment_status == 'failed' ? 'selected' : '' }}>Thanh toán thất bại / Hủy</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        @php
                            $validNextStatuses = \App\Models\Booking::getValidNextStatuses($booking->tour_status);
                        @endphp
                        <label class="form-label fw-bold">Trạng thái Tour</label>
                        <select name="tour_status" class="form-select tour-status-select" data-booking-id="{{ $booking->id }}">
                            <option value="upcoming" {{ $booking->tour_status == 'upcoming' ? 'selected' : '' }}>Sắp bắt đầu</option>
                            <option value="in_progress" {{ $booking->tour_status == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                            <option value="checking_in" {{ $booking->tour_status == 'checking_in' ? 'selected' : '' }}>Đang ở điểm check-in</option>
                            <option value="completed" {{ $booking->tour_status == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                            <option value="cancelled_by_customer" {{ $booking->tour_status == 'cancelled_by_customer' ? 'selected' : '' }}>Khách hủy</option>
                            <option value="cancelled_by_admin" {{ $booking->tour_status == 'cancelled_by_admin' ? 'selected' : '' }}>Admin hủy</option>
                        </select>
                    </div>
                    <div class="mb-3 checkin-step-container" id="checkinStepContainer{{ $booking->id }}" style="display: {{ $booking->tour_status == 'checking_in' ? 'block' : 'none' }};">
                        <label class="form-label fw-bold">Điểm check-in hiện tại</label>
                        <input type="text" name="current_checkin_step" class="form-control" placeholder="VD: Sân bay, Trạm 1, Khách sạn..." value="{{ $booking->current_checkin_step }}">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-admin btn-light border" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-admin btn-admin-primary">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tour-status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            let bookingId = this.getAttribute('data-booking-id');
            let container = document.getElementById('checkinStepContainer' + bookingId);
            if(this.value === 'checking_in') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    });

    // Real-time Status Polling for Admin
    const paymentStatusMap = {
        'pending': ['badge-soft-warning', 'Chờ thanh toán'],
        'paid_30': ['badge-soft-info', 'Đã thanh toán 30% (Cọc)'],
        'paid_100': ['badge-soft-success', 'Đã thanh toán 100%'],
        'failed': ['badge-soft-danger', 'Thất bại']
    };

    const tourStatusMap = {
        'upcoming': ['badge-soft-primary', 'Sắp bắt đầu'],
        'in_progress': ['badge-soft-warning', 'Đang thực hiện'],
        'checking_in': ['badge-soft-info', 'Đang check-in'],
        'completed': ['badge-soft-success', 'Hoàn thành'],
        'cancelled_by_customer': ['badge-soft-danger', 'Hủy (Khách)'],
        'cancelled_by_admin': ['badge-soft-danger', 'Hủy (Admin)']
    };

    function pollAdminStatuses() {
        const rows = document.querySelectorAll('.booking-row');
        if (!rows.length) return;

        const ids = Array.from(rows).map(r => r.dataset.bookingId).join(',');

        fetch(`/admin/bookings/live-statuses?ids=${ids}`)
            .then(res => res.json())
            .then(data => {
                Object.keys(data).forEach(id => {
                    const row = document.getElementById(`booking-row-${id}`);
                    if (!row) return;

                    const info = data[id];
                    const currentPayment = row.dataset.paymentStatus;
                    const currentTour = row.dataset.tourStatus;

                    if (currentPayment !== info.payment_status || currentTour !== info.tour_status) {
                        row.dataset.paymentStatus = info.payment_status;
                        row.dataset.tourStatus = info.tour_status;

                        // Update Payment Badge
                        const pCell = document.getElementById(`payment-status-badge-${id}`);
                        if (pCell) {
                            const ps = paymentStatusMap[info.payment_status] || ['badge-soft-secondary', 'N/A'];
                            pCell.innerHTML = `<span class="badge-soft ${ps[0]}">${ps[1]}</span>`;
                        }

                        // Update Tour Badge
                        const tCell = document.getElementById(`tour-status-badge-${id}`);
                        if (tCell) {
                            const ts = tourStatusMap[info.tour_status] || ['badge-soft-secondary', 'N/A'];
                            let extra = '';
                            if (info.tour_status === 'checking_in' && info.current_checkin_step) {
                                extra = `<div class="small mt-1 text-info"><i class="bi bi-geo-alt-fill me-1"></i> Điểm: ${info.current_checkin_step}</div>`;
                            }
                            tCell.innerHTML = `<span class="badge-soft ${ts[0]}">${ts[1]}</span>` + extra;
                        }

                        // Highlight row
                        row.style.transition = 'background-color 0.5s ease';
                        row.style.backgroundColor = '#d1e7dd';
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                        }, 2500);
                    }
                });
            })
            .catch(err => console.log('Admin live polling error', err));
    }

    setInterval(pollAdminStatuses, 3000);
});
</script>

@endsection
