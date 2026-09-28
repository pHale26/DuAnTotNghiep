@extends('layouts.guide')

@section('page-title', 'Lịch trình Tour')

@section('content')
<style>
    .admin-card-title {
        font-weight: 700;
        color: var(--admin-sidebar);
    }
    @media (max-width: 767px) {
        /* Chuyển bảng sang dạng các thẻ card */
        .table-responsive {
            border: none;
            overflow: visible !important;
        }
        .table {
            border: none;
            display: block !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .table thead {
            display: none !important;
        }
        .table tbody {
            display: flex !important;
            flex-direction: column;
            gap: 16px;
            padding: 12px 4px;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .table tbody tr {
            display: block !important;
            background: #fff;
            border: 1px solid var(--admin-border) !important;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            padding: 16px;
            margin-bottom: 0;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 0.85rem;
            text-align: right;
        }
        
        .table td[data-label="ID"] {
            display: none !important;
        }
        
        .table td[data-label="Tên Tour"] {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
            padding-top: 0;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--admin-border);
            padding-right: 90px; /* Leave space for status badge */
        }
        .table td[data-label="Tên Tour"]::before {
            display: none !important;
        }
        .table td[data-label="Tên Tour"] strong {
            font-size: 1rem !important;
            font-weight: 700 !important;
            color: var(--admin-text-main) !important;
            line-height: 1.4;
        }
        .table td[data-label="Tên Tour"] .text-muted {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem !important;
            color: var(--admin-text-muted) !important;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .table td[data-label="Trạng thái"] {
            position: absolute;
            top: 16px;
            right: 16px;
            border-bottom: none;
            padding: 0;
            width: auto;
        }
        .table td[data-label="Trạng thái"]::before {
            display: none !important;
        }

        .table td[data-label="Hành động"] {
            width: 100%;
            border-bottom: none;
            padding-bottom: 0;
            padding-top: 12px;
            justify-content: center;
        }
        .table td[data-label="Hành động"]::before {
            display: none !important;
        }
        .table td[data-label="Hành động"] a {
            width: 100%;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem !important;
            font-weight: 600;
            border-radius: 8px;
        }

        .table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            margin-right: auto;
            text-align: left;
        }
        .table td > * {
            text-align: right;
            margin: 0 !important;
        }
        .table td.text-end {
            text-align: right;
            justify-content: space-between;
        }
    }
</style>
<div class="card border-0 shadow-sm">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Danh sách Lịch trình được phân công</h5>
    </div>
    
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Tour</th>
                        <th>Khởi hành</th>
                        <th>Kết thúc</th>
                        <th>Số khách / Chỗ trống</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $scheduleGuide)
                        @php
                            $tourSchedule = $scheduleGuide->tour_schedule;
                            $tour = $tourSchedule->tour;
                            
                            $departureDateTime = \Carbon\Carbon::parse($tourSchedule->departure_date->toDateString());
                            if ($tour && $tour->departure_time) {
                                $timeParts = explode(':', $tour->departure_time);
                                $hour = isset($timeParts[0]) ? (int) $timeParts[0] : 0;
                                $minute = isset($timeParts[1]) ? (int) $timeParts[1] : 0;
                                $second = isset($timeParts[2]) ? (int) $timeParts[2] : 0;
                                $departureDateTime->setTime($hour, $minute, $second);
                            }
                            
                            $statusClass = 'secondary';
                            $statusText = 'Chưa xác định';
                            $isClosedStyle = false;
                            
                            if ($tourSchedule->status === 'closed') {
                                $statusClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                                $statusText = 'Đã đóng';
                                $isClosedStyle = true;
                            } elseif ($tourSchedule->status === 'completed') {
                                $statusClass = 'bg-info bg-opacity-10 text-info border border-info';
                                $statusText = 'Đã hoàn thành';
                                $isClosedStyle = true;
                            } elseif ($tourSchedule->status === 'in_progress') {
                                $statusClass = 'success';
                                $statusText = 'Đang diễn ra';
                            } elseif ($departureDateTime > now()) {
                                $statusClass = 'primary';
                                $statusText = 'Sắp tới';
                            } elseif ($departureDateTime <= now() && \Carbon\Carbon::parse($tourSchedule->return_date)->endOfDay() >= now()) {
                                $statusClass = 'success';
                                $statusText = 'Đang diễn ra';
                            } else {
                                $statusClass = 'secondary';
                                $statusText = 'Đã kết thúc';
                            }
                        @endphp
                        <tr>
                            <td data-label="ID">#{{ $tourSchedule->id }}</td>
                            <td data-label="Tên Tour">
                                <strong class="text-md-start d-block">{{ $tour->title }}</strong>
                                <div class="text-muted small mt-1 text-md-start">Mã: {{ $tour->code }}</div>
                            </td>
                            <td data-label="Khởi hành">{{ \Carbon\Carbon::parse($tourSchedule->departure_date)->format('d/m/Y') }}</td>
                            <td data-label="Kết thúc">{{ \Carbon\Carbon::parse($tourSchedule->return_date)->format('d/m/Y') }}</td>
                            <td data-label="Số khách">
                                {{ $tourSchedule->bookings->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])->whereNotIn('booking_status', ['cancelled'])->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])->sum(fn($b) => $b->adults_count + $b->children_count) }} / {{ $tourSchedule->capacity }}
                            </td>
                            <td data-label="Trạng thái">
                                @if($isClosedStyle)
                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                @else
                                    <span class="badge badge-soft-{{ $statusClass }}">{{ $statusText }}</span>
                                @endif
                            </td>
                            <td data-label="Hành động" class="text-end">
                                <a href="{{ route('guide.schedules.show', $tourSchedule->id) }}" class="btn btn-sm btn-admin-primary">
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Bạn chưa được phân công dẫn tour nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($schedules->hasPages())
    <div class="card-footer bg-white border-top p-3">
        {{ $schedules->links() }}
    </div>
    @endif
</div>
@endsection
