@extends('layouts.guide')

@section('page-title', 'Chi tiết Lịch trình Tour')

@section('content')
<style>
    @keyframes pulseText {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.05); }
        100% { opacity: 1; transform: scale(1); }
    }
    .pulse-text {
        animation: pulseText 1.2s infinite ease-in-out;
        font-weight: 800 !important;
        display: inline-block;
    }
    /* Tab Styling Overrides */
    @media (max-width: 767px) {
        .nav-tabs .nav-link {
            min-height: 48px;
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px !important;
        }
    }
    /* Collapsible Tour Info panel styles */
    @media (min-width: 992px) {
        #tourInfoCollapse {
            display: block !important;
        }
        [data-bs-target="#tourInfoCollapse"] {
            cursor: default !important;
            pointer-events: none;
        }
    }
    
    @media (max-width: 991px) {
        #tourInfoCollapse.collapse:not(.show) {
            display: none;
        }
        #tourInfoCollapse.collapse.show {
            display: block;
        }
        #tourInfoChevron {
            transition: transform 0.2s;
        }
        [data-bs-toggle="collapse"][aria-expanded="true"] #tourInfoChevron {
            transform: rotate(180deg);
        }
    }

    /* Sticky Bottom Actions Bar for Mobile */
    @media (max-width: 767px) {
        #mobile-sticky-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--admin-border);
            padding: 8px 16px;
            padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 8px) !important;
            display: flex !important;
            gap: 12px;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
            z-index: 1030;
        }
        body {
            padding-bottom: 70px !important;
        }
        
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
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            padding: 16px;
            margin-bottom: 0;
            transition: transform 0.2s, box-shadow 0.2s;
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
            border-bottom: 1px dashed #f1f5f9;
            font-size: 0.85rem;
            text-align: right;
        }
        .table td:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .table td:first-child {
            padding-top: 0;
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
        .table td > div, .table td > span, .table td > button, .table td > input {
            text-align: right;
        }
        .table td.text-center {
            text-align: right;
            justify-content: space-between;
            align-items: center;
        }
        .table td.text-center > div {
            margin: 0 !important;
        }

        /* Dịch vụ nhóm mua thêm (màu vàng) trên mobile */
        .table tbody tr.table-warning {
            background-color: #fff3cd !important;
            border: 1px solid #ffe69c !important;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 8px;
        }
        .table tbody tr.table-warning td {
            display: block;
            padding: 0;
            border: none;
            text-align: left;
        }
        .table tbody tr.table-warning td::before {
            display: none;
        }
        .table tbody tr.table-warning td > div {
            text-align: left !important;
        }
        .table tbody tr.table-warning td > div > div.d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 8px !important;
        }

        /* Vùng chạm tối thiểu 44x44px - Chỉ định cho các nút trực tiếp của ô bảng, tránh nút trong modal */
        .table td > button, .table td > a, .table td > form > button {
            min-height: 44px;
            min-width: 44px;
            padding: 10px 16px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem !important;
        }

        /* Modal Điểm danh dạng Full-screen Sheet */
        #activityRollCallModal.modal {
            padding: 0 !important;
        }
        #activityRollCallModal .modal-dialog {
            margin: 0;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
        }
        #activityRollCallModal .modal-content {
            height: 100%;
            border-radius: 0;
            display: flex;
            flex-direction: column;
        }
        #activityRollCallModal .modal-header {
            position: relative;
            flex-direction: column;
            align-items: stretch !important;
            padding: 16px !important;
            background-color: #ffffff !important;
            border-bottom: 1px solid var(--admin-border);
        }
        #activityRollCallModal .modal-header h5 {
            font-size: 1.1rem;
            margin-bottom: 12px !important;
            text-align: center;
            width: 100%;
            padding-right: 24px;
        }
        #activityRollCallModal .modal-header .btn-close {
            position: absolute;
            top: 18px;
            right: 16px;
            margin: 0 !important;
            font-size: 1.1rem;
        }
        #activityRollCallModal .modal-header .d-flex.gap-2 {
            display: flex !important;
            width: 100%;
            justify-content: space-between;
            gap: 10px !important;
        }
        #activityRollCallModal .modal-header .d-flex.gap-2 button {
            flex: 1;
            font-size: 0.85rem !important;
            padding: 10px 12px !important;
            min-height: 40px !important;
        }
        #activityRollCallModal .modal-body {
            overflow-y: auto;
            flex-grow: 1;
        }
        #activityRollCallModal .table-responsive {
            overflow: visible;
        }
        #activityRollCallModal table,
        #activityRollCallModal thead,
        #activityRollCallModal tbody,
        #activityRollCallModal tr,
        #activityRollCallModal td {
            display: block;
            width: 100%;
        }
        #activityRollCallModal thead {
            display: none;
        }
        #activityRollCallModal tbody {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background-color: #f8fafc;
        }
        /* Style card danh sách khách trong modal */
        #activityRollCallModal tr[id^="rollcall-row-"] {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 0;
            display: grid;
            grid-template-columns: 1fr auto;
            grid-template-rows: auto auto;
            align-items: center;
            gap: 8px;
        }
        #activityRollCallModal tr[id^="rollcall-row-"].table-success {
            border-color: #a3cfbb !important;
            background-color: #f1fdf7 !important;
        }
        #activityRollCallModal tr[id^="rollcall-row-"].table-warning {
            border-color: #ffe69c !important;
            background-color: #fffaf0 !important;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td {
            padding: 0;
            border: none;
            text-align: left;
            display: block;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td:nth-child(1) {
            grid-column: 1;
            grid-row: 1;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td:nth-child(2) {
            grid-column: 1;
            grid-row: 2;
            text-align: left;
            margin-top: 4px;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td:nth-child(3) {
            grid-column: 2;
            grid-row: 1 / span 2;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 4px;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td:nth-child(3) .form-check {
            margin: 0;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td:nth-child(4) {
            grid-column: 1 / span 2;
            grid-row: 3;
            border-top: 1px dashed #f1f5f9;
            padding-top: 10px;
            margin-top: 4px;
            display: flex;
            justify-content: stretch;
            width: 100%;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td:nth-child(4) button {
            width: 100%;
            min-height: 44px;
        }

        /* Form Tách đoàn xếp dọc 1 cột trên mobile */
        #activityRollCallModal tr[id^="extend-row-"],
        #activityRollCallModal tr[id^="free-time-row-"] {
            border: 1px solid var(--admin-border);
            border-top: none;
            border-radius: 0 0 12px 12px;
            margin-top: -16px;
            margin-bottom: 8px;
            background-color: #f8fafc;
            padding: 16px;
        }
        #activityRollCallModal tr[id^="extend-row-"].d-none,
        #activityRollCallModal tr[id^="free-time-row-"].d-none {
            display: none !important;
        }
        #activityRollCallModal tr[id^="extend-row-"] td,
        #activityRollCallModal tr[id^="free-time-row-"] td {
            padding: 0 !important;
            border: none !important;
        }
        #activityRollCallModal tr[id^="extend-row-"] .row,
        #activityRollCallModal tr[id^="free-time-row-"] .row {
            margin: 0 !important;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        #activityRollCallModal tr[id^="extend-row-"] .col-md-3,
        #activityRollCallModal tr[id^="extend-row-"] .col-md-4,
        #activityRollCallModal tr[id^="extend-row-"] .col-md-5,
        #activityRollCallModal tr[id^="free-time-row-"] .col-md-4 {
            width: 100% !important;
            padding: 0 !important;
        }
        #activityRollCallModal tr[id^="extend-row-"] input,
        #activityRollCallModal tr[id^="extend-row-"] textarea,
        #activityRollCallModal tr[id^="free-time-row-"] input,
        #activityRollCallModal tr[id^="free-time-row-"] textarea {
            font-size: 16px !important;
            padding: 10px 12px !important;
        }
        #activityRollCallModal tr[id^="free-time-row-"] .btn-save-free-time-ajax {
            width: 100%;
            min-height: 44px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #activityRollCallModal tr[id^="free-time-row-"] .btn-save-free-time-ajax::after {
            content: " Lưu thông tin tách đoàn";
            margin-left: 6px;
            font-weight: 600;
        }

        /* 2 nút Gia hạn/Khách đã quay lại xếp ngang chia đôi, màu tương phản */
        #activityRollCallModal tr[id^="rollcall-row-"] .btn-extend-guest,
        #activityRollCallModal tr[id^="rollcall-row-"] .btn-return-guest {
            display: inline-block !important;
            width: 48% !important;
            margin-top: 8px !important;
            min-height: 44px;
            font-size: 0.85rem !important;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] .btn-extend-guest {
            float: left;
            background-color: #ffc107 !important;
            color: #212529 !important;
            border-color: #ffc107 !important;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] .btn-return-guest {
            float: right;
            background-color: #198754 !important;
            color: #ffffff !important;
            border-color: #198754 !important;
        }
        #activityRollCallModal tr[id^="rollcall-row-"] td:nth-child(1)::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Nút đóng sticky bottom trong modal */
        #activityRollCallModal .modal-footer {
            position: sticky;
            bottom: 0;
            z-index: 10;
            background: #fff !important;
            padding: 16px !important;
            border-top: 1px solid var(--admin-border);
            margin-top: auto;
        }
        #activityRollCallModal .modal-footer button {
            width: 100%;
            min-height: 44px;
            font-size: 0.95rem !important;
            font-weight: 600;
        }
    }
</style>
@php
    $tourSchedule = $scheduleGuide->tour_schedule;
    $tour = $tourSchedule->tour;

    $firstBooking = $tourSchedule->bookings
        ->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])
        ->whereIn('payment_status', ['paid_30', 'paid_100'])
        ->first();
    if (!$firstBooking) {
        $firstBooking = $tourSchedule->bookings
            ->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])
            ->first();
    }
    $groupStatus = $firstBooking ? $firstBooking->tour_status : 'upcoming';
    if ($tourSchedule->status === 'closed') {
        $groupStatus = 'closed';
    } elseif ($tourSchedule->status === 'completed') {
        $groupStatus = 'completed';
    }

    $tourStatusMap = [
        'upcoming' => ['badge-soft-primary', 'Sắp bắt đầu'],
        'in_progress' => ['badge-soft-warning', 'Đang thực hiện'],
        'checking_in' => ['badge-soft-info', 'Đang check-in'],
        'completed' => ['badge-soft-success', 'Hoàn thành'],
        'closed' => ['bg-secondary bg-opacity-10 text-secondary border border-secondary', 'Đã đóng'],
        'cancelled_by_customer' => ['badge-soft-danger', 'Hủy (Khách)'],
        'cancelled_by_admin' => ['badge-soft-danger', 'Hủy (Admin)']
    ];
    $ts = $tourStatusMap[$groupStatus] ?? ['badge-soft-secondary', 'N/A'];

    $scheduleStatusMap = [
        'pending' => ['badge-soft-primary', 'Sắp bắt đầu'],
        'operating' => ['badge-soft-warning', 'Đang vận hành'],
        'completed' => ['badge-soft-success', 'Đã kết thúc'],
        'closed' => ['bg-secondary bg-opacity-10 text-secondary border border-secondary', 'Đã đóng']
    ];
    $ss = $scheduleStatusMap[$tourSchedule->status ?? 'pending'] ?? ['badge-soft-secondary', 'N/A'];

    $allPassengers = $tourSchedule->bookings
        ->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])
        ->whereNotIn('booking_status', ['cancelled'])
        ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
        ->flatMap(fn($b) => $b->booking_passengers);
    $checkedInCount = $allPassengers->where('checked_in', true)->count();
    $totalCount = $allPassengers->count();
    
    $passengersArray = $tourSchedule->bookings
        ->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])
        ->whereNotIn('booking_status', ['cancelled'])
        ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
        ->flatMap(fn($b) => $b->booking_passengers)
        ->map(function($p) {
        $activeSplit = $p->group_splits->whereIn('status', ['ON_TIME', 'OVERDUE', 'UNREACHABLE'])->first();
        $extensions = $activeSplit ? $activeSplit->extensions->map(function($e) {
            return [
                'old_end_time' => $e->old_end_time->format('Y-m-d H:i'),
                'new_end_time' => $e->new_end_time->format('Y-m-d H:i'),
                'extend_reason' => $e->extend_reason,
                'confirmed_by_guide_name' => $e->confirmed_by_guide_name,
                'created_at' => $e->created_at->format('Y-m-d H:i'),
            ];
        })->toArray() : [];
        if ($activeSplit) {
            $activeSplitArray = $activeSplit->toArray();
            $activeSplitArray['extensions'] = $extensions;
        } else {
            $activeSplitArray = null;
        }
        return [
            'id' => $p->id,
            'full_name' => $p->full_name,
            'passenger_type' => $p->passenger_type,
            'activity_checkins' => $p->activity_checkins->pluck('tour_activity_id')->toArray(),
            'active_split' => $activeSplitArray,
            'is_free_time' => $activeSplit ? true : false,
            'free_time_location' => $activeSplit ? $activeSplit->split_location : null,
            'free_time_end' => $activeSplit ? $activeSplit->end_time->format('Y-m-d H:i:s') : null,
        ];
    })->values()->all();

    $isLocked = ($groupStatus === 'completed' || $tourSchedule->status === 'closed');
@endphp

<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('guide.schedules.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
    
    @if($tourSchedule->status === 'completed' || $groupStatus === 'completed')
        @php
            $report = \App\Models\TourReport::where('tour_schedule_id', $tourSchedule->id)->first();
        @endphp
        @if(!$report)
            <a href="{{ route('guide.reports.create', $tourSchedule->id) }}" class="btn btn-sm btn-primary fw-bold">
                <i class="bi bi-file-earmark-text"></i> Viết Báo Cáo Tour
            </a>
        @else
            <span class="badge bg-success bg-opacity-10 text-success border border-success p-2">
                <i class="bi bi-check-circle-fill me-1"></i>Đã nộp báo cáo
            </span>
        @endif
    @endif
</div>

<div class="row">
    <!-- Cột thông tin Tour -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="admin-card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#tourInfoCollapse" aria-expanded="false" aria-controls="tourInfoCollapse" style="cursor: pointer;">
                <h5 class="admin-card-title mb-0">Thông tin Tour</h5>
                <i class="bi bi-chevron-down d-lg-none fs-5" id="tourInfoChevron"></i>
            </div>
            <div class="collapse" id="tourInfoCollapse">
                <div class="card-body">
                    @php
                        $tourGuide = auth()->user()->tour_guide;
                        $pendingAbsenceRequest = $tourGuide ? \App\Models\TourAbsenceRequest::where('tour_schedule_id', $tourSchedule->id)
                            ->where('main_guide_id', $tourGuide->id)
                            ->whereIn('status', ['pending_review', 'pending_review_urgent'])
                            ->first() : null;
                    @endphp

                    @if($tour->primary_image)
                        <img src="{{ Storage::url($tour->primary_image) }}" alt="{{ $tour->title }}" class="img-fluid rounded mb-3 w-100" style="object-fit: cover; height: 180px;">
                    @endif
                    <h5 class="fw-bold">{{ $tour->title }}</h5>
                    <p class="text-muted small mb-3">Mã Tour: {{ $tour->code }}</p>

                    <ul class="list-group list-group-flush border-top pt-3">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Khởi hành:</span>
                            <strong>{{ \Carbon\Carbon::parse($tourSchedule->departure_date)->format('d/m/Y') }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Kết thúc:</span>
                            <strong>{{ \Carbon\Carbon::parse($tourSchedule->return_date)->format('d/m/Y') }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Trạng thái Tour:</span>
                            <span class="badge {{ $ts[0] }}">{{ $ts[1] }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Vai trò của bạn:</span>
                            @if($scheduleGuide->is_backup)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-2 py-1">HDV Dự phòng</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1">HDV Chính</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tổng khách:</span>
                            <strong>{{ $tourSchedule->bookings->whereNotIn('tour_status', [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER])->whereNotIn('booking_status', ['cancelled'])->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])->sum(fn($b) => $b->adults_count + $b->children_count) }} / {{ $tourSchedule->capacity }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Điểm danh:</span>
                            <span class="fw-bold text-success" id="checkin-counter">{{ $checkedInCount }} / {{ $totalCount }}</span>
                        </li>
                    </ul>

                    @if(!$scheduleGuide->is_backup && $groupStatus === 'upcoming' && \Carbon\Carbon::parse($tourSchedule->departure_date)->isFuture())
                        @if($pendingAbsenceRequest)
                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning mt-3 mb-0 text-center py-2 fw-medium" style="font-size: 0.85rem;">
                                <i class="bi bi-hourglass-split me-1"></i> Yêu cầu đang chờ admin duyệt
                            </div>
                        @else
                            <a href="{{ route('guide.schedules.absence', $tourSchedule->id) }}" class="btn btn-outline-danger w-100 mt-3 fw-bold">
                                <i class="bi bi-calendar-x me-1"></i> Báo bận tour
                            </a>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Cột danh sách hành khách và điểm tham quan -->
    <div class="col-lg-8 mb-4">
        <!-- Tabs Nav -->
        <ul class="nav nav-tabs mb-3 border-bottom-0" id="scheduleTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark border-0 border-bottom border-3 border-warning bg-transparent tab-btn" id="passengers-tab" data-bs-toggle="tab" data-bs-target="#passengers" type="button" role="tab" aria-controls="passengers" aria-selected="true">
                    <i class="bi bi-people-fill me-1 text-warning"></i>Danh sách Hành khách
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-muted border-0 bg-transparent tab-btn" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities" type="button" role="tab" aria-controls="activities" aria-selected="false">
                    <i class="bi bi-geo-alt-fill me-1 text-info"></i>Điểm Tham Quan
                </button>
            </li>
            @if($tourSchedule->status === 'closed')
                <li class="ms-auto my-auto d-flex align-items-center">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary p-2 fw-bold" style="font-size: 0.85rem;">
                        <i class="bi bi-lock-fill me-1"></i>Tour đã đóng
                    </span>
                </li>
            @elseif($tourSchedule->status === 'completed' || $groupStatus === 'completed')
                <li class="ms-auto my-auto d-flex align-items-center">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success p-2 fw-bold" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Tour đã hoàn thành
                    </span>
                </li>
            @else
                @php
                    $totalActivitiesCount = $tour->tour_itineraries->flatMap(fn($i) => $i->activities)->count();
                    $checkedInActivitiesCount = $tourSchedule->activity_checkins->count();
                    $allActivitiesChecked = ($totalActivitiesCount > 0 && $checkedInActivitiesCount === $totalActivitiesCount);
                @endphp
                <li class="ms-auto my-auto d-flex align-items-center">
                    <form action="{{ route('guide.schedules.update_group_status', $tourSchedule->id) }}" method="POST" id="complete-tour-form" onsubmit="return confirm('Xác nhận hoàn thành tour này? Trạng thái sẽ được cập nhật đồng loạt cho cả đoàn.')">
                        @csrf
                        <input type="hidden" name="tour_status" value="completed">
                        <button type="submit" class="btn btn-success btn-sm fw-bold shadow-sm" id="btn-complete-tour" {{ !$allActivitiesChecked ? 'disabled' : '' }} title="{{ !$allActivitiesChecked ? 'Bạn cần check-in toàn bộ các điểm tham quan để hoàn thành tour' : 'Xác nhận hoàn thành tour' }}">
                            <i class="bi bi-check-circle-fill me-1"></i>Tour đã hoàn thành
                        </button>
                    </form>
                </li>
            @endif
        </ul>

        <div class="tab-content" id="scheduleTabContent">
            <!-- Tab Passengers -->
            <div class="tab-pane fade show active" id="passengers" role="tabpanel" aria-labelledby="passengers-tab">
                <div class="card border-0 shadow-sm">
                    <div>
                        <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="admin-card-title mb-0">Danh sách Hành khách</h5>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill">
                                    Tổng cộng: <span class="total-count-val" id="total-count-val">{{ $totalCount }}</span> khách
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th>Họ tên</th>
                                        <th>Loại vé</th>
                                        <th class="text-center" style="width: 100px;">Chi tiết</th>
                                        <th class="text-center" style="width: 90px;">Ghi chú</th>
                                        <th class="text-center" style="width: 100px;">Sửa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $stt = 1; @endphp
                                    @forelse($tourSchedule->bookings as $booking)
                                        @if(in_array($booking->payment_status, ['pending', 'paid_30', 'paid_100']) && !in_array($booking->tour_status, [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER]) && $booking->booking_status !== 'cancelled')
                                            
                                            {{-- Hiển thị Dịch vụ mua thêm (Vé / Addons) của cả nhóm --}}
                                            @if($booking->ticket_bookings->isNotEmpty() || $booking->addons->isNotEmpty())
                                                <tr class="table-warning bg-warning bg-opacity-10 border-bottom border-warning border-2">
                                                    <td colspan="6" class="py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="fw-bold text-dark"><i class="bi bi-gift-fill text-warning me-1"></i> Dịch vụ nhóm mua thêm (Mã #{{ $booking->id }} - {{ $booking->user->name ?? 'Khách' }}):</span>
                                                                <div class="d-flex gap-2 flex-wrap">
                                                                    @foreach($booking->ticket_bookings as $tb)
                                                                        <span class="badge bg-warning text-dark border border-warning" style="font-size: 0.8rem;">
                                                                            <i class="bi bi-ticket-detailed"></i> {{ $tb->ticket_option->ticket->title ?? 'Vé' }} ({{ $tb->ticket_option->name ?? '' }}): <strong class="fs-6">{{ $tb->quantity }}</strong> vé
                                                                        </span>
                                                                    @endforeach
                                                                    @foreach($booking->addons as $addon)
                                                                        <span class="badge bg-info text-dark border border-info" style="font-size: 0.8rem;">
                                                                            <i class="bi bi-plus-circle"></i> {{ $addon->pivot->addon_name ?? $addon->name }}: <strong class="fs-6">{{ $addon->pivot->quantity }}</strong>
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif

                                            @foreach($booking->booking_passengers as $passenger)
                                                <tr id="row-{{ $passenger->id }}" class="{{ !empty($passenger->special_note) ? 'table-warning' : '' }}">
                                                    <td data-label="STT">{{ $stt++ }}</td>
                                                    <td data-label="Họ tên">
                                                        <div class="fw-bold text-md-start text-end">{{ $passenger->full_name }}</div>
                                                        <div class="small text-muted text-md-start text-end">
                                                            {{ $passenger->gender == 'male' ? 'Nam' : ($passenger->gender == 'female' ? 'Nữ' : 'Khác') }}
                                                        </div>
                                                    </td>
                                                    <td data-label="Loại vé">
                                                        @if($passenger->passenger_type == 'adult')
                                                            <span class="badge badge-soft-primary">Người lớn</span>
                                                        @elseif($passenger->passenger_type == 'child')
                                                            <span class="badge badge-soft-warning">Trẻ em</span>
                                                        @else
                                                            <span class="badge badge-soft-secondary">Em bé</span>
                                                        @endif
                                                    </td>
                                                    <td data-label="Chi tiết" class="text-center">
                                                         <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 text-end" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#passengerDetailModal-{{ $passenger->id }}" title="Xem chi tiết khách hàng">
                                                             <i class="bi bi-info-circle"></i> Chi tiết
                                                         </button>
                                                    </td>
                                                    <td data-label="Ghi chú" class="text-center">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-warning note-btn"
                                                            data-id="{{ $passenger->id }}"
                                                            data-name="{{ $passenger->full_name }}"
                                                            data-note="{{ $passenger->special_note ?? '' }}"
                                                            data-url="{{ route('guide.passengers.update_note', $passenger) }}"
                                                            title="Ghi chú đặc biệt"
                                                            {{ $isLocked ? 'disabled' : '' }}
                                                        >
                                                            @if($passenger->special_note)
                                                                <i class="bi bi-sticky-fill text-warning"></i>
                                                            @else
                                                                <i class="bi bi-sticky"></i>
                                                            @endif
                                                        </button>
                                                    </td>
                                                    <td data-label="Sửa" class="text-center">
                                                        @if($loop->first)
                                                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#addPassengerModal-{{ $booking->id }}" title="Quản lý danh sách khách" {{ $isLocked ? 'disabled' : '' }}>
                                                                <i class="bi bi-people-fill me-1"></i>Sửa
                                                            </button>
                                                        @else
                                                            <span class="text-muted small">#{{ $booking->id }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                                Chưa có hành khách nào đặt chỗ cho lịch trình này.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Tab Activities -->
            <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                <div class="card border-0 shadow-sm">
                    <div class="admin-card-header">
                        <h5 class="admin-card-title mb-0">Lịch trình & Các điểm tham quan</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="accordion accordion-flush" id="itineraryAccordion">
                            @forelse($tour->tour_itineraries as $itinerary)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-day-{{ $itinerary->id }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-day-{{ $itinerary->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-day-{{ $itinerary->id }}">
                                            Ngày {{ $itinerary->day_number }}: {{ $itinerary->title }}
                                        </button>
                                    </h2>
                                    <div id="collapse-day-{{ $itinerary->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-day-{{ $itinerary->id }}" data-bs-parent="#itineraryAccordion">
                                        <div class="accordion-body bg-light">
                                            @if($itinerary->activities->isNotEmpty())
                                                <div class="list-group list-group-flush rounded">
                                                    @foreach($itinerary->activities as $activity)
                                                        @php
                                                            $checkin = $tourSchedule->activity_checkins->firstWhere('tour_activity_id', $activity->id);
                                                            $isChecked = $checkin ? true : false;
                                                        @endphp
                                                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                            <div>
                                                                <h6 class="mb-1 fw-bold text-dark">
                                                                    <i class="bi bi-check-circle-fill text-{{ $isChecked ? 'success' : 'secondary' }} me-2" id="icon-act-{{ $activity->id }}"></i>
                                                                    {{ $activity->title }}
                                                                </h6>
                                                                <p class="mb-1 text-muted small"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }} | <span class="badge bg-secondary">{{ $activity->activity_type_label }}</span></p>
                                                                <p class="mb-0 text-success small fw-semibold mt-1" id="time-act-{{ $activity->id }}" style="display:{{ $isChecked ? 'block' : 'none' }};">
                                                                    Đã check-in lúc {{ $isChecked ? $checkin->checked_in_at->format('H:i d/m/Y') : '' }}
                                                                </p>
                                                            </div>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-sm btn-{{ $isChecked ? 'outline-secondary' : 'success' }} fw-bold px-3 btn-checkin-activity" 
                                                                    data-id="{{ $activity->id }}" 
                                                                    data-url="{{ route('guide.activities.toggle_checkin', [$tourSchedule->id, $activity->id]) }}"
                                                                    id="btn-act-{{ $activity->id }}"
                                                                    data-is-checked="{{ $isChecked ? '1' : '0' }}"
                                                                    {{ $isLocked ? 'disabled' : '' }}>
                                                                    @if($isChecked)
                                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Hủy
                                                                    @else
                                                                        <i class="bi bi-geo-alt-fill me-1"></i>Check-in
                                                                    @endif
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 btn-activity-rollcall"
                                                                    data-activity-id="{{ $activity->id }}"
                                                                    data-activity-title="{{ $activity->title }}"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#activityRollCallModal"
                                                                    {{ $isLocked ? 'disabled' : '' }}>
                                                                    <i class="bi bi-people-fill me-1"></i>Điểm danh
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted small mb-0">Không có hoạt động chi tiết nào.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-map fs-1 d-block mb-2"></i>
                                    Chưa có lịch trình chi tiết.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal Ghi chú đặc biệt -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="noteModalLabel">
                        <i class="bi bi-sticky-fill text-warning me-2"></i>Ghi chú đặc biệt
                    </h5>
                    <p class="text-muted small mb-0" id="note-passenger-name"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="note-textarea" class="form-label text-muted small">
                        Nhập ghi chú (thông tin sức khỏe, yêu cầu đặc biệt, lưu ý...):
                    </label>
                    <textarea
                        id="note-textarea"
                        class="form-control"
                        rows="5"
                        maxlength="1000"
                        placeholder="Ví dụ: Khách bị dị ứng hải sản, cần chỗ ngồi đầu xe, mang theo thuốc tiểu đường..."
                    ></textarea>
                    <div class="text-end small text-muted mt-1">
                        <span id="note-char-count">0</span>/1000
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning fw-bold px-4" id="save-note-btn">
                    <i class="bi bi-floppy me-1"></i>Lưu ghi chú
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Activity Roll Call -->
<div class="modal fade" id="activityRollCallModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="bi bi-people-fill text-primary me-2"></i>Điểm danh: <span id="activity-rollcall-title" class="text-primary"></span>
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm btn-success fw-bold" id="btn-checkin-all-activity" {{ $isLocked ? 'disabled' : '' }}>
                        <i class="bi bi-check-all me-1"></i>Chọn tất cả
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold" id="btn-uncheck-all-activity" {{ $isLocked ? 'disabled' : '' }}>
                        <i class="bi bi-x-circle me-1"></i>Hủy tất cả
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Họ tên</th>
                                <th>Loại vé</th>
                                <th class="text-center" style="width: 100px;">Trạng thái</th>
                                <th class="text-center" style="width: 120px;">Tách đoàn</th>
                            </tr>
                        </thead>
                        <tbody id="activity-rollcall-body">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Free Time -->
<div class="modal fade" id="freeTimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="" id="freeTimeForm">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Tách đoàn (Tự do tham quan)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">Hành khách được đánh dấu tách đoàn sẽ không bị yêu cầu điểm danh trong khoảng thời gian này.</div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_free_time" id="is_free_time" value="1">
                        <label class="form-check-label fw-bold" for="is_free_time">Cho phép tách đoàn</label>
                    </div>
                    
                    <div id="freeTimeDates" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label small">Từ thời gian</label>
                            <input type="datetime-local" name="free_time_start" id="free_time_start" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Đến thời gian</label>
                            <input type="datetime-local" name="free_time_end" id="free_time_end" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Địa điểm tách đoàn</label>
                            <input type="text" name="free_time_location" id="free_time_location" class="form-control" placeholder="VD: Khách sạn, siêu thị...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Lưu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Manage Passengers per Booking -->
@foreach($tourSchedule->bookings as $b)
@if(in_array($b->payment_status, ['pending', 'paid_30', 'paid_100']) && !in_array($b->tour_status, [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER]) && $b->booking_status !== 'cancelled')
<div class="modal fade" id="addPassengerModal-{{ $b->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Quản lý hành khách (Booking #{{ $b->id }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="bookingTab-{{ $b->id }}" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" id="manual-tab-{{ $b->id }}" data-bs-toggle="tab" data-bs-target="#manual-pane-{{ $b->id }}" type="button">Nhập thủ công</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="excel-tab-{{ $b->id }}" data-bs-toggle="tab" data-bs-target="#excel-pane-{{ $b->id }}" type="button">Excel</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="manual-pane-{{ $b->id }}">
                        <form action="{{ route('guide.passengers.manual', [$tourSchedule->id, $b->id]) }}" method="POST">
                            @csrf
                            <div class="alert alert-warning small">Sửa danh sách này sẽ xóa các khách (trừ khách số 1) hiện tại của booking và lưu lại.</div>
                            
                            @for($i = 0; $i < $b->adults_count; $i++)
                            @php $p = $b->booking_passengers->where('passenger_type', 'adult')->values()->get($i); @endphp
                            <div class="p-2 border rounded mb-2 bg-light">
                                <div class="fw-bold small mb-1">Người lớn {{ $i+1 }}</div>
                                <input type="hidden" name="passengers[{{ $i }}][passenger_type]" value="adult">
                                <div class="row g-2">
                                    <div class="col-md-3"><input type="text" name="passengers[{{ $i }}][full_name]" class="form-control form-control-sm" placeholder="Họ tên" value="{{ $p ? $p->full_name : '' }}" required></div>
                                    <div class="col-md-3"><input type="text" name="passengers[{{ $i }}][identity_number]" class="form-control form-control-sm" placeholder="CCCD" value="{{ $p ? $p->identity_number : '' }}"></div>
                                    <div class="col-md-3"><input type="date" name="passengers[{{ $i }}][date_of_birth]" class="form-control form-control-sm" value="{{ $p && $p->date_of_birth ? $p->date_of_birth->format('Y-m-d') : '' }}"></div>
                                    <div class="col-md-3">
                                        <select name="passengers[{{ $i }}][gender]" class="form-select form-select-sm">
                                            <option value="male" {{ $p && $p->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ $p && $p->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                            <option value="other" {{ $p && $p->gender == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endfor

                            @for($i = 0; $i < $b->children_count; $i++)
                            @php 
                                $idx = $b->adults_count + $i; 
                                $p = $b->booking_passengers->where('passenger_type', 'child')->values()->get($i);
                            @endphp
                            <div class="p-2 border rounded mb-2 bg-light">
                                <div class="fw-bold small mb-1">Trẻ em {{ $i+1 }}</div>
                                <input type="hidden" name="passengers[{{ $idx }}][passenger_type]" value="child">
                                <div class="row g-2">
                                    <div class="col-md-4"><input type="text" name="passengers[{{ $idx }}][full_name]" class="form-control form-control-sm" placeholder="Họ tên" value="{{ $p ? $p->full_name : '' }}" required></div>
                                    <div class="col-md-4"><input type="date" name="passengers[{{ $idx }}][date_of_birth]" class="form-control form-control-sm" value="{{ $p && $p->date_of_birth ? $p->date_of_birth->format('Y-m-d') : '' }}"></div>
                                    <div class="col-md-4">
                                        <select name="passengers[{{ $idx }}][gender]" class="form-select form-select-sm">
                                            <option value="male" {{ $p && $p->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ $p && $p->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endfor
                            <div class="text-end mt-3"><button type="submit" class="btn btn-primary btn-sm">Lưu danh sách</button></div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="excel-pane-{{ $b->id }}">
                        <form action="{{ route('guide.passengers.import', [$tourSchedule->id, $b->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Chọn file Excel hành khách</label>
                                <input type="file" name="excel_file" accept=".xls,.xlsx" class="form-control" required>
                            </div>
                            <div class="text-end"><button type="submit" class="btn btn-success btn-sm">Upload Excel</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Toast thông báo -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="toast-msg" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toast-text"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let totalCount = {{ $totalCount }};
    let checkedCount = {{ $checkedInCount }};
    const isLocked = {{ $isLocked ? 'true' : 'false' }};
    const scheduleId = {{ $tourSchedule->id }};
    let passengersData = @json($passengersArray);

    // ─── Toast helper ─────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-msg');
        const toastText = document.getElementById('toast-text');
        toast.className = `toast align-items-center text-white border-0 bg-${type}`;
        toastText.textContent = message;
        new bootstrap.Toast(toast, { delay: 2500 }).show();
    }

    // ─── Update progress bar & counters ───────────────────────────────
    function updateProgress() {
        const pct = totalCount > 0 ? Math.round(checkedCount / totalCount * 100) : 0;
        const counterEl = document.getElementById('checkin-counter');
        if (counterEl) counterEl.textContent = `${checkedCount} / ${totalCount}`;
        
        document.querySelectorAll('.selected-count-val').forEach(function(el) {
            el.textContent = checkedCount;
        });

        const bar = document.getElementById('checkin-progress');
        const pctEl = document.getElementById('checkin-pct');
        if (bar) bar.style.width = pct + '%';
        if (pctEl) pctEl.textContent = pct + '%';
    }

    // ─── Check-in toggle ──────────────────────────────────────────────
    document.querySelectorAll('.checkin-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const passengerId = this.dataset.id;
            const row = document.getElementById('row-' + passengerId);

            if (this.checked) {
                row.classList.add('table-success');
                checkedCount++;
            } else {
                row.classList.remove('table-success');
                checkedCount--;
            }
            updateProgress();
        });
    });


    document.querySelectorAll('.tour-status-select-guide').forEach(function(select) {
        select.addEventListener('change', function() {
            let bookingId = this.getAttribute('data-booking-id');
            let container = document.getElementById('checkinStepContainerGuide' + bookingId);
            if(this.value === 'checking_in') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    });

    // ─── Note modal ───────────────────────────────────────────────────
    let currentNoteBtn = null;

    document.querySelectorAll('.note-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentNoteBtn = this;
            const name = this.dataset.name;
            const note = this.dataset.note;

            const notePassengerName = document.getElementById('note-passenger-name');
            const noteTextarea = document.getElementById('note-textarea');
            const noteCharCount = document.getElementById('note-char-count');
            const saveNoteBtn = document.getElementById('save-note-btn');
            
            notePassengerName.textContent = name;
            noteTextarea.value = note;
            noteCharCount.textContent = note.length;

            new bootstrap.Modal(document.getElementById('noteModal')).show();
        });
    });

    // ─── Free Time logic ──────────────────────────────────────────────
    const freeTimeModalEl = document.getElementById('freeTimeModal');
    if (freeTimeModalEl) {
        const freeTimeModal = new bootstrap.Modal(freeTimeModalEl);
        const freeTimeForm = document.getElementById('freeTimeForm');
        const isFreeTimeCheck = document.getElementById('is_free_time');
        const freeTimeDates = document.getElementById('freeTimeDates');

        isFreeTimeCheck.addEventListener('change', function() {
            if(this.checked) {
                freeTimeDates.style.display = 'block';
            } else {
                freeTimeDates.style.display = 'none';
            }
        });

        document.querySelectorAll('.free-time-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const url = this.getAttribute('data-url');
                const start = this.getAttribute('data-start');
                const end = this.getAttribute('data-end');
                const location = this.getAttribute('data-location') || '';

                freeTimeForm.action = url;
                
                if(start || end || this.classList.contains('btn-success')) {
                    isFreeTimeCheck.checked = true;
                    freeTimeDates.style.display = 'block';
                    document.getElementById('free_time_start').value = start;
                    document.getElementById('free_time_end').value = end;
                    document.getElementById('free_time_location').value = location;
                } else {
                    isFreeTimeCheck.checked = false;
                    freeTimeDates.style.display = 'none';
                    document.getElementById('free_time_start').value = '';
                    document.getElementById('free_time_end').value = '';
                    document.getElementById('free_time_location').value = '';
                }

                freeTimeModal.show();
            });
        });
    }

    // Character counter
    const noteTextarea = document.getElementById('note-textarea');
    if (noteTextarea) {
        noteTextarea.addEventListener('input', function () {
            document.getElementById('note-char-count').textContent = this.value.length;
        });
    }

    // Save note
    const saveNoteBtn = document.getElementById('save-note-btn');
    if (saveNoteBtn) {
        saveNoteBtn.addEventListener('click', function () {
            if (!currentNoteBtn) return;

            const url = currentNoteBtn.dataset.url;
            const passengerId = currentNoteBtn.dataset.id;
            const note = document.getElementById('note-textarea').value;
            const saveBtn = this;

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang lưu...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ note: note }),
            })
            .then(res => res.json())
            .then(data => {
                // Update button dataset so re-opening shows updated note
                currentNoteBtn.dataset.note = note;

                // Toggle icon to filled if has note
                const icon = currentNoteBtn.querySelector('i');
                const row = document.getElementById('row-' + passengerId);
                if (note.trim()) {
                    icon.className = 'bi bi-sticky-fill text-warning';
                    if (row) row.classList.add('table-warning');
                } else {
                    icon.className = 'bi bi-sticky';
                    if (row) row.classList.remove('table-warning');
                }

                bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
                showToast(data.message, 'success');
            })
            .catch(() => {
                showToast('Có lỗi xảy ra, vui lòng thử lại.', 'danger');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-floppy me-1"></i>Lưu ghi chú';
            });
        });
    }
    // Tab switching styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('show.bs.tab', function (e) {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('text-dark', 'border-bottom', 'border-3', 'border-warning');
                b.classList.add('text-muted');
            });
            e.target.classList.remove('text-muted');
            e.target.classList.add('text-dark', 'border-bottom', 'border-3', 'border-warning');
        });
    });

    // Hàm cập nhật khóa tuần tự cho điểm đến
    function updateActivityLocks() {
        if (typeof isLocked !== 'undefined' && isLocked) return;
        
        const activityButtons = document.querySelectorAll('.btn-checkin-activity');
        const rollcallButtons = document.querySelectorAll('.btn-activity-rollcall');
        
        let previousCheckedIn = true; // Điểm đầu tiên luôn luôn được mở khóa
        
        activityButtons.forEach((btn, index) => {
            const isChecked = btn.getAttribute('data-is-checked') === '1';
            const rollcallBtn = rollcallButtons[index];
            
            if (previousCheckedIn) {
                btn.disabled = false;
                if (rollcallBtn) rollcallBtn.disabled = false;
            } else {
                btn.disabled = true;
                if (rollcallBtn) rollcallBtn.disabled = true;
            }
            
            // Điều kiện để điểm tiếp theo mở là điểm HIỆN TẠI phải đã check-in
            previousCheckedIn = isChecked;
        });
    }

    function updateCompleteTourButton() {
        const btn = document.getElementById('btn-complete-tour');
        if (!btn) return;
        
        const activityButtons = document.querySelectorAll('.btn-checkin-activity');
        const total = activityButtons.length;
        const checked = Array.from(activityButtons).filter(btnEl => btnEl.getAttribute('data-is-checked') === '1').length;
        
        if (total > 0 && checked === total) {
            btn.disabled = false;
            btn.removeAttribute('title');
        } else {
            btn.disabled = true;
            btn.setAttribute('title', 'Bạn cần check-in toàn bộ các điểm tham quan để hoàn thành tour');
        }
    }

    // Chạy khi load trang
    setTimeout(() => {
        updateActivityLocks();
        updateCompleteTourButton();
    }, 100);

    // Toggle Activity Checkin
    document.querySelectorAll('.btn-checkin-activity').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const actId = this.dataset.id;
            const url = this.dataset.url;
            const selfBtn = this;
            const iconEl = document.getElementById('icon-act-' + actId);
            const timeEl = document.getElementById('time-act-' + actId);

            selfBtn.disabled = true;
            let originalHtml = selfBtn.innerHTML;
            selfBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw err; });
                }
                return res.json();
            })
            .then(data => {
                showToast(data.message, data.checked_in ? 'success' : 'secondary');
                
                if (data.checked_in) {
                    selfBtn.className = 'btn btn-sm btn-outline-secondary fw-bold px-3 btn-checkin-activity';
                    selfBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i>Hủy';
                    selfBtn.setAttribute('data-is-checked', '1');
                    iconEl.className = 'bi bi-check-circle-fill text-success me-2';
                    timeEl.style.display = 'block';
                    timeEl.textContent = 'Đã check-in lúc ' + data.time;
                } else {
                    selfBtn.className = 'btn btn-sm btn-success fw-bold px-3 btn-checkin-activity';
                    selfBtn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i>Check-in';
                    selfBtn.setAttribute('data-is-checked', '0');
                    iconEl.className = 'bi bi-check-circle-fill text-secondary me-2';
                    timeEl.style.display = 'none';
                    timeEl.textContent = '';
                }
                updateActivityLocks();
                updateCompleteTourButton();
            })
            .catch((err) => {
                showToast(err.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'danger');
                selfBtn.innerHTML = originalHtml;
                selfBtn.disabled = false;
            });
        });
    });

    // Countdown state map
    let countdownIntervals = {};
    let pollingInterval = null;

    function formatDateTimeString(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const hh = String(d.getHours()).padStart(2, '0');
        const mm = String(d.getMinutes()).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        return `${hh}:${mm} (${day}/${month})`;
    }

    function getNowForDatetimeInput(offsetHours = 0) {
        const now = new Date();
        if (offsetHours !== 0) {
            now.setHours(now.getHours() + offsetHours);
        }
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function renderSplitBadge(split, passengerId, isHistoryOpen = false) {
        if (!split) return '';
        let badgeHtml = '';
        let returnBtnHtml = '';
        let extendBtnHtml = '';
        let historyHtml = '';
        
        const startFormatted = split.start_time ? formatDateTimeString(split.start_time) : '';
        const endFormatted = split.end_time ? formatDateTimeString(split.end_time) : '';
        
        const now = new Date().getTime();
        const endTime = split.end_time ? new Date(split.end_time).getTime() : 0;
        const isLate = endTime > 0 && (endTime - now < 0);

        if (split.status === 'ON_TIME') {
            if (isLate) {
                badgeHtml = `<span id="badge-status-${passengerId}" class="badge bg-danger text-white"><i class="bi bi-exclamation-triangle-fill me-1"></i>Đã quá giờ</span>`;
            } else {
                badgeHtml = `<span id="badge-status-${passengerId}" class="badge bg-primary text-white"><i class="bi bi-clock-history me-1"></i>Đang tách đoàn (<span id="countdown-${passengerId}">--:--</span>)</span>`;
            }
        } else if (split.status === 'OVERDUE') {
            badgeHtml = `<span id="badge-status-${passengerId}" class="badge bg-danger text-white"><i class="bi bi-exclamation-triangle-fill me-1"></i>Đã quá giờ</span>`;
        } else if (split.status === 'UNREACHABLE') {
            badgeHtml = `<span id="badge-status-${passengerId}" class="badge" style="background-color: #8b0000; color: white; font-weight: bold;"><i class="bi bi-exclamation-octagon-fill me-1"></i>Không liên lạc được</span>`;
        } else if (split.status === 'RETURNED') {
            badgeHtml = `<span id="badge-status-${passengerId}" class="badge bg-success text-white"><i class="bi bi-check-circle-fill me-1"></i>Đã quay lại đoàn</span>`;
        }

        let splitDetailsHtml = `
            <div class="mt-2 p-3 rounded border text-start small split-countdown-card" style="border-color: #ffe69c; background-color: #fffaf0; border-width: 1.5px;">
                <div class="fw-bold mb-2" style="font-size: 0.9rem; color: var(--admin-text-main);"><i class="bi bi-clock-fill me-1"></i>Thời gian: ${startFormatted} <i class="bi bi-arrow-right text-muted mx-1"></i> ${endFormatted}</div>
                ${split.reason ? `<div class="mb-1"><span class="text-muted"><i class="bi bi-chat-left-text me-1"></i>Lý do:</span> <strong class="text-dark">${split.reason}</strong></div>` : ''}
                ${split.phone_number ? `<div class="mb-1"><span class="text-muted"><i class="bi bi-telephone me-1"></i>SĐT:</span> <a href="tel:${split.phone_number}" class="fw-bold text-decoration-none" style="font-size: 0.95rem; color: var(--admin-primary);"><i class="bi bi-telephone-outbound-fill me-1"></i>${split.phone_number}</a></div>` : ''}
                ${split.split_location ? `<div class="mb-1"><span class="text-muted"><i class="bi bi-geo-alt me-1"></i>Điểm tách:</span> <strong class="text-dark">${split.split_location}</strong></div>` : ''}
                ${split.return_location ? `<div class="mb-0"><span class="text-muted"><i class="bi bi-geo-fill me-1"></i>Điểm quay lại:</span> <strong class="text-dark">${split.return_location}</strong></div>` : ''}
            </div>
        `;

        if (['ON_TIME', 'OVERDUE'].includes(split.status)) {
            extendBtnHtml = `<button type="button" class="btn btn-sm btn-outline-warning mt-2 d-block w-100 fw-bold btn-extend-guest" data-passenger-id="${passengerId}" data-split-id="${split.id}" style="min-height: 44px;"><i class="bi bi-hourglass-split me-1"></i>Gia hạn</button>`;
        }

        if (['ON_TIME', 'OVERDUE', 'UNREACHABLE'].includes(split.status)) {
            returnBtnHtml = `<button type="button" class="btn btn-sm btn-success mt-2 d-block w-100 fw-bold btn-return-guest" data-passenger-id="${passengerId}" data-split-id="${split.id}" style="min-height: 48px; background-color: var(--success-base); border-color: var(--success-base);"><i class="bi bi-person-check-fill me-1"></i>Khách đã quay lại</button>`;
        }

        if (split.extensions && split.extensions.length > 0) {
            let listHtml = split.extensions.map(e => `
                <div class="small border-bottom border-secondary pb-1 mb-1 border-opacity-25">
                    <div><span class="text-muted"><i class="bi bi-arrow-right-short"></i></span> ${formatDateTimeString(e.old_end_time)} <i class="bi bi-arrow-right text-warning mx-1"></i> <strong>${formatDateTimeString(e.new_end_time)}</strong></div>
                    <div class="text-muted" style="font-size: 0.75rem;">Lý do: ${e.extend_reason}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">HDV xác nhận: ${e.confirmed_by_guide_name}</div>
                </div>
            `).join('');
            historyHtml = `
                <div class="mt-2 text-start">
                    <a class="text-decoration-none text-muted small ${isHistoryOpen ? '' : 'collapsed'} d-inline-block fw-semibold" data-bs-toggle="collapse" href="#history-${passengerId}" aria-expanded="${isHistoryOpen ? 'true' : 'false'}">
                        <i class="bi bi-clock-history"></i> Lịch sử gia hạn (${split.extensions.length}) <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse ${isHistoryOpen ? 'show' : ''} mt-2 bg-light p-2 rounded" id="history-${passengerId}">
                        ${listHtml}
                    </div>
                </div>
            `;
        }

        return badgeHtml + splitDetailsHtml + historyHtml + extendBtnHtml + returnBtnHtml;
    }

    function startCountdown(passengerId, endTimeStr, splitStatus) {
        if (countdownIntervals[passengerId]) clearInterval(countdownIntervals[passengerId]);
        
        const endTime = new Date(endTimeStr).getTime();
        
        const update = () => {
            const el = document.getElementById(`countdown-${passengerId}`);
            const badgeEl = document.getElementById(`badge-status-${passengerId}`);
            
            const now = new Date().getTime();
            const distance = endTime - now;
            const isLate = distance < 0;

            if (isLate) {
                if (badgeEl) {
                    badgeEl.className = 'badge bg-danger text-white';
                    badgeEl.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i>Đã quá giờ`;
                }
                clearInterval(countdownIntervals[passengerId]);
                return;
            }

            if (el) {
                const absDistance = Math.abs(distance);
                const hours = Math.floor((absDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((absDistance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((absDistance % (1000 * 60)) / 1000);
                
                let timeStr = "";
                if (hours > 0) timeStr += hours + "h ";
                timeStr += String(minutes).padStart(2, '0') + "m " + String(seconds).padStart(2, '0') + "s";
                
                el.innerHTML = timeStr;
                
                // Color transition & pulse under 5 minutes
                const parentCard = el.closest('.split-countdown-card');
                if (distance < 300000) { // < 5 mins
                    el.style.color = '#dc2626';
                    el.classList.add('pulse-text');
                    if (parentCard) {
                        parentCard.style.borderColor = '#dc2626';
                        parentCard.style.backgroundColor = '#fef2f2';
                    }
                } else {
                    el.style.color = 'var(--warning-text)';
                    el.classList.remove('pulse-text');
                    if (parentCard) {
                        parentCard.style.borderColor = '#ffe69c';
                        parentCard.style.backgroundColor = '#fffaf0';
                    }
                }
            }
        };

        update();
        countdownIntervals[passengerId] = setInterval(update, 1000);
    }

    function checkAllValid(passengerId) {
        let isValid = true;
        
        const reasonEl = document.getElementById(`reason-ft-${passengerId}`);
        if (!reasonEl || reasonEl.value.trim().length < 5) isValid = false;
        
        const phoneEl = document.getElementById(`phone-ft-${passengerId}`);
        if (!phoneEl || !/^(0|\+84)[3|5|7|8|9][0-9]{8}$/.test(phoneEl.value.trim())) isValid = false;
        
        const locEl = document.getElementById(`loc-ft-${passengerId}`);
        if (!locEl || !locEl.value.trim()) isValid = false;

        const retLocEl = document.getElementById(`ret-loc-ft-${passengerId}`);
        if (!retLocEl || !retLocEl.value.trim()) isValid = false;
        
        let startEl = document.getElementById(`start-ft-${passengerId}`);
        let endEl = document.getElementById(`end-ft-${passengerId}`);
        if (!startEl || !startEl.value) isValid = false;
        if (!endEl || !endEl.value) isValid = false;
        if (startEl && endEl && startEl.value && endEl.value && new Date(endEl.value) <= new Date(startEl.value)) isValid = false;
        
        const saveBtn = document.getElementById(`save-btn-${passengerId}`);
        if (saveBtn) saveBtn.disabled = !isValid;
    }

    function checkExtendValid(passengerId) {
        let isValid = true;
        
        const reasonEl = document.getElementById(`extend-reason-${passengerId}`);
        if (!reasonEl || reasonEl.value.trim().length < 1) isValid = false;
        
        const timeEl = document.getElementById(`extend-time-${passengerId}`);
        const p = passengersData.find(x => x.id == passengerId);
        if (!timeEl || !timeEl.value) {
            isValid = false;
        } else if (p && p.active_split && new Date(timeEl.value) <= new Date(p.active_split.end_time)) {
            isValid = false;
        }
        
        const saveBtn = document.getElementById(`save-extend-btn-${passengerId}`);
        if (saveBtn) saveBtn.disabled = !isValid;
    }

    window.validateField = function(el, type, passengerId) {
        let errEl = document.getElementById(`err-${el.id.replace(`-${passengerId}`, '')}-${passengerId}`);
        let isFieldValid = true;
        let errorMsg = '';
        
        if (type === 'reason') {
            if (el.value.trim().length < 5) { isFieldValid = false; errorMsg = 'Vui lòng nhập ít nhất 5 ký tự.'; }
        } else if (type === 'phone') {
            if (!/^(0|\+84)[3|5|7|8|9][0-9]{8}$/.test(el.value.trim())) { isFieldValid = false; errorMsg = 'SĐT không hợp lệ.'; }
        } else if (type === 'required') {
            if (!el.value.trim()) { isFieldValid = false; errorMsg = 'Không để trống.'; }
        } else if (type === 'end_time') {
            let startVal = document.getElementById(`start-ft-${passengerId}`).value;
            if (!el.value) { isFieldValid = false; errorMsg = 'Vui lòng chọn.'; }
            else if (startVal && new Date(el.value) <= new Date(startVal)) { isFieldValid = false; errorMsg = 'Phải sau giờ bắt đầu.'; }
        } else if (type === 'extend_reason') {
            if (!el.value.trim()) { isFieldValid = false; errorMsg = 'Vui lòng nhập lý do.'; }
        } else if (type === 'extend_time') {
            const p = passengersData.find(x => x.id == passengerId);
            if (!el.value) {
                isFieldValid = false; errorMsg = 'Vui lòng chọn thời gian.';
            } else if (p && p.active_split && new Date(el.value) <= new Date(p.active_split.end_time)) {
                isFieldValid = false; errorMsg = 'Phải lớn hơn thời gian hiện tại.';
            }
        }
        
        if (!isFieldValid) {
            el.classList.add('is-invalid');
            if(errEl) errEl.textContent = errorMsg;
        } else {
            el.classList.remove('is-invalid');
            if(errEl) errEl.textContent = '';
        }
        
        if (type.startsWith('extend')) {
            checkExtendValid(passengerId);
        } else {
            checkAllValid(passengerId);
        }
    };

    function updatePassengerRowUI(passengerId) {
        const p = passengersData.find(x => x.id == passengerId);
        if (!p) return;
        
        const tr = document.getElementById(`rollcall-row-${passengerId}`);
        const badgeContainer = document.getElementById(`free-time-info-${passengerId}`);
        const checkbox = tr.querySelector('.activity-passenger-checkbox');
        const ftBtn = tr.querySelector('.btn-modal-free-time');
        const ftRow = document.getElementById(`free-time-row-${passengerId}`);
        const extRow = document.getElementById(`extend-row-${passengerId}`);

        const activeSplit = p.active_split;
        const isChecked = p.activity_checkins.includes(parseInt(currentActivityId));

        let isHistoryOpen = false;
        const historyEl = document.getElementById(`history-${passengerId}`);
        if (historyEl && historyEl.classList.contains('show')) {
            isHistoryOpen = true;
        }

        if (activeSplit) {
            badgeContainer.innerHTML = renderSplitBadge(activeSplit, passengerId, isHistoryOpen);
            tr.className = 'table-warning text-muted';
            checkbox.checked = false;
            checkbox.disabled = true;
            ftBtn.className = 'btn btn-sm btn-success btn-modal-free-time';
            ftBtn.innerHTML = '<i class="bi bi-clock-history"></i> Đang tách';
            ftBtn.disabled = true;
            ftRow.classList.add('d-none');
            
            if (activeSplit.status !== 'UNREACHABLE') {
                startCountdown(passengerId, activeSplit.end_time, activeSplit.status);
            }
        } else {
            if (countdownIntervals[passengerId]) clearInterval(countdownIntervals[passengerId]);
            badgeContainer.innerHTML = '';
            tr.className = isChecked ? 'table-success' : '';
            checkbox.checked = isChecked;
            checkbox.disabled = isLocked;
            ftBtn.className = 'btn btn-sm btn-outline-secondary btn-modal-free-time';
            ftBtn.innerHTML = '<i class="bi bi-clock-history"></i> Tách đoàn';
            ftBtn.disabled = isLocked;
            if (extRow) extRow.classList.add('d-none');
        }
    }

    const activityRollCallModalEl = document.getElementById('activityRollCallModal');
    let currentActivityId = null;

    if (activityRollCallModalEl) {
        const activityRollCallModal = new bootstrap.Modal(activityRollCallModalEl);

        activityRollCallModalEl.addEventListener('show.bs.modal', function () {
            pollingInterval = setInterval(() => {
                if (!currentActivityId) return;
                fetch(`/guide/group-splits?schedule_id=${scheduleId}&per_page=100`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    const splits = data.data;
                    passengersData.forEach(p => {
                        const s = splits.find(x => x.guest_id === p.id && ['ON_TIME', 'OVERDUE', 'UNREACHABLE'].includes(x.status));
                        if (s) {
                            p.active_split = s;
                            updatePassengerRowUI(p.id);
                        } else if (p.active_split) {
                            p.active_split = null;
                            updatePassengerRowUI(p.id);
                        }
                    });
                }).catch(err => console.error(err));
            }, 5000);
        });

        activityRollCallModalEl.addEventListener('hide.bs.modal', function () {
            if (pollingInterval) clearInterval(pollingInterval);
        });

        document.querySelectorAll('.btn-activity-rollcall').forEach(btn => {
            btn.addEventListener('click', function() {
                currentActivityId = this.getAttribute('data-activity-id');
                const title = this.getAttribute('data-activity-title');
                document.getElementById('activity-rollcall-title').textContent = title;

                const tbody = document.getElementById('activity-rollcall-body');
                tbody.innerHTML = '';

                passengersData.forEach(p => {
                    const isChecked = p.activity_checkins.includes(parseInt(currentActivityId));
                    const typeLabel = p.passenger_type === 'adult' ? '<span class="badge badge-soft-primary">Người lớn</span>' : 
                                      (p.passenger_type === 'child' ? '<span class="badge badge-soft-warning">Trẻ em</span>' : '<span class="badge badge-soft-secondary">Em bé</span>');
                    
                    const activeSplit = p.active_split;
                    
                    const checkedHtml = (isChecked && !activeSplit) ? 'checked' : '';
                    const disabledHtml = (isLocked || activeSplit) ? 'disabled' : '';
                    const checkboxDisabled = (isLocked || activeSplit) ? 'disabled' : '';

                    const tr = document.createElement('tr');
                    tr.id = `rollcall-row-${p.id}`;
                    if (activeSplit) {
                        tr.className = 'table-warning text-muted';
                    } else {
                        tr.className = checkedHtml ? 'table-success' : '';
                    }

                    tr.innerHTML = `
                        <td>
                            <div class="fw-bold text-dark">${p.full_name}</div>
                            <div class="small text-muted mt-1" id="free-time-info-${p.id}">${renderSplitBadge(activeSplit, p.id)}</div>
                        </td>
                        <td>${typeLabel}</td>
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input activity-passenger-checkbox" type="checkbox" 
                                    data-passenger-id="${p.id}" ${checkedHtml} ${checkboxDisabled}
                                    style="width: 1.3em; height: 1.3em; cursor: pointer;">
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm ${activeSplit ? 'btn-success' : 'btn-outline-secondary'} btn-modal-free-time" 
                                data-passenger-id="${p.id}" ${disabledHtml}>
                                <i class="bi bi-clock-history"></i> ${activeSplit ? 'Đang tách' : 'Tách đoàn'}
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);

                    const extTr = document.createElement('tr');
                    extTr.id = `extend-row-${p.id}`;
                    extTr.className = 'd-none bg-light';
                    extTr.innerHTML = `
                        <td colspan="4" class="p-3 border-top-0">
                            <div class="row g-2 align-items-start text-start">
                                <div class="col-md-4">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Thời gian quay lại mới <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control form-control-sm gs-input" id="extend-time-${p.id}" onblur="validateField(this, 'extend_time', ${p.id})">
                                    <div class="invalid-feedback fw-bold" id="err-extend-time-${p.id}"></div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Lý do gia hạn <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-sm gs-input" id="extend-reason-${p.id}" rows="1" onblur="validateField(this, 'extend_reason', ${p.id})"></textarea>
                                    <div class="invalid-feedback fw-bold" id="err-extend-reason-${p.id}"></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Thao tác</label>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-primary w-100 btn-save-extend-ajax" data-passenger-id="${p.id}" data-split-id="${activeSplit ? activeSplit.id : ''}" id="save-extend-btn-${p.id}" disabled><i class="bi bi-floppy"></i> Lưu gia hạn</button>
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">Xác nhận bởi: <br>{{ auth()->user()->name ?? (auth()->user()->full_name ?? 'HDV') }}</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(extTr);

                    const ftTr = document.createElement('tr');
                    ftTr.id = `free-time-row-${p.id}`;
                    ftTr.className = 'd-none bg-light';
                    ftTr.innerHTML = `
                        <td colspan="4" class="p-3 border-top-0">
                            <div class="row g-2 align-items-start text-start">
                                <div class="col-md-4">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Lý do tách đoàn <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-sm gs-input" id="reason-ft-${p.id}" rows="1" placeholder="Tối thiểu 5 ký tự" onblur="validateField(this, 'reason', ${p.id})"></textarea>
                                    <div class="invalid-feedback fw-bold" id="err-reason-ft-${p.id}"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1 fw-semibold text-dark">SĐT liên hệ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm gs-input" id="phone-ft-${p.id}" placeholder="09xxxxxxxx" onblur="validateField(this, 'phone', ${p.id})">
                                    <div class="invalid-feedback fw-bold" id="err-phone-ft-${p.id}"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Địa điểm tách <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm gs-input" id="loc-ft-${p.id}" placeholder="VD: Khách sạn..." onblur="validateField(this, 'required', ${p.id})">
                                    <div class="invalid-feedback fw-bold" id="err-loc-ft-${p.id}"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control form-control-sm gs-input" id="start-ft-${p.id}" onblur="validateField(this, 'required', ${p.id})">
                                    <div class="invalid-feedback fw-bold" id="err-start-ft-${p.id}"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Thời gian kết thúc <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control form-control-sm gs-input" id="end-ft-${p.id}" onblur="validateField(this, 'end_time', ${p.id})">
                                    <div class="invalid-feedback fw-bold" id="err-end-ft-${p.id}"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1 fw-semibold text-dark">Địa điểm quay lại <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control form-control-sm gs-input" id="ret-loc-ft-${p.id}" placeholder="VD: Trạm xe..." onblur="validateField(this, 'required', ${p.id})">
                                        <button type="button" class="btn btn-sm btn-primary btn-save-free-time-ajax" data-passenger-id="${p.id}" data-passenger-name="${p.full_name}" id="save-btn-${p.id}" disabled><i class="bi bi-floppy"></i></button>
                                    </div>
                                    <div class="invalid-feedback fw-bold" id="err-ret-loc-ft-${p.id}"></div>
                                </div>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(ftTr);

                    if (activeSplit && activeSplit.status !== 'UNREACHABLE') {
                        startCountdown(p.id, activeSplit.end_time, activeSplit.status);
                    }
                });
            });
        });

        const rollcallBody = document.getElementById('activity-rollcall-body');
        
        rollcallBody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-modal-free-time');
            if (btn) {
                const passengerId = btn.getAttribute('data-passenger-id');
                const ftRow = document.getElementById(`free-time-row-${passengerId}`);
                if (ftRow) {
                    ftRow.classList.toggle('d-none');
                    if (!ftRow.classList.contains('d-none')) {
                        const startInput = document.getElementById(`start-ft-${passengerId}`);
                        const endInput = document.getElementById(`end-ft-${passengerId}`);
                        if (startInput && !startInput.value) {
                            startInput.value = getNowForDatetimeInput(0);
                        }
                        if (endInput && !endInput.value) {
                            endInput.value = getNowForDatetimeInput(1);
                        }
                        ['start-ft', 'end-ft', 'reason-ft', 'phone-ft', 'loc-ft', 'ret-loc-ft'].forEach(fieldPrefix => {
                            const field = document.getElementById(`${fieldPrefix}-${passengerId}`);
                            if (field && !field.dataset.realtimeBound) {
                                field.dataset.realtimeBound = '1';
                                field.addEventListener('input', () => checkAllValid(passengerId));
                                field.addEventListener('change', () => checkAllValid(passengerId));
                            }
                        });
                        checkAllValid(passengerId);
                    }
                }
            }

            const btnExtend = e.target.closest('.btn-extend-guest');
            if (btnExtend) {
                const passengerId = btnExtend.getAttribute('data-passenger-id');
                const extRow = document.getElementById(`extend-row-${passengerId}`);
                if (extRow) {
                    extRow.classList.toggle('d-none');
                    // Pre-fill end time if showing
                    if (!extRow.classList.contains('d-none')) {
                        const p = passengersData.find(x => x.id == passengerId);
                        if (p && p.active_split) {
                            document.getElementById(`extend-time-${passengerId}`).value = p.active_split.end_time;
                        }
                    }
                }
            }
        });

        rollcallBody.addEventListener('click', function(e) {
            const btnSaveExtend = e.target.closest('.btn-save-extend-ajax');
            if (btnSaveExtend) {
                const passengerId = btnSaveExtend.getAttribute('data-passenger-id');
                const splitId = btnSaveExtend.getAttribute('data-split-id');
                
                const newEndTime = document.getElementById(`extend-time-${passengerId}`).value;
                const reason = document.getElementById(`extend-reason-${passengerId}`).value;

                btnSaveExtend.disabled = true;
                const originalHtml = btnSaveExtend.innerHTML;
                btnSaveExtend.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(`/guide/group-splits/${splitId}/extend`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        new_end_time: newEndTime,
                        extend_reason: reason
                    })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(errData => { throw errData; });
                    }
                    return res.json();
                })
                .then(data => {
                    btnSaveExtend.disabled = false;
                    btnSaveExtend.innerHTML = originalHtml;

                    const p = passengersData.find(x => x.id == passengerId);
                    if (p) {
                        p.active_split = data.data;
                        document.getElementById(`extend-row-${passengerId}`).classList.add('d-none');
                        const nextBtn = document.getElementById(`save-extend-btn-${passengerId}`);
                        if (nextBtn) nextBtn.setAttribute('data-split-id', data.data.id);
                    }
                    
                    updatePassengerRowUI(passengerId);
                    showToast('Đã gia hạn thành công!', 'success');
                })
                .catch(err => {
                    console.error(err);
                    btnSaveExtend.disabled = false;
                    btnSaveExtend.innerHTML = originalHtml;
                    
                    let errorMsg = 'Không thể gia hạn.';
                    if (err && err.errors) {
                        const firstKey = Object.keys(err.errors)[0];
                        if (firstKey && err.errors[firstKey][0]) {
                            errorMsg = err.errors[firstKey][0];
                        }
                    } else if (err && err.message) {
                        errorMsg = err.message;
                    }
                    showToast(errorMsg, 'danger');
                });
                return;
            }

            const btnReturn = e.target.closest('.btn-return-guest');
            if (btnReturn) {
                if (!confirm("Xác nhận khách đã quay lại đoàn?")) return;
                
                const passengerId = btnReturn.getAttribute('data-passenger-id');
                const splitId = btnReturn.getAttribute('data-split-id');

                btnReturn.disabled = true;
                const originalHtml = btnReturn.innerHTML;
                btnReturn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(`/guide/group-splits/${splitId}/return`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(errData => { throw errData; });
                    }
                    return res.json();
                })
                .then(data => {
                    const p = passengersData.find(x => x.id == passengerId);
                    if (p) p.active_split = null;
                    
                    if (p && !p.activity_checkins.includes(parseInt(currentActivityId))) {
                        p.activity_checkins.push(parseInt(currentActivityId));
                        fetch(`/guide/schedules/${scheduleId}/activities/${currentActivityId}/passengers/${passengerId}/toggle-checkin`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
                        }).catch(console.error);
                    }
                    
                    updatePassengerRowUI(passengerId);
                    
                    const badgeContainer = document.getElementById(`free-time-info-${passengerId}`);
                    if (badgeContainer) {
                        badgeContainer.innerHTML = `<span class="badge bg-success text-white"><i class="bi bi-check-circle-fill me-1"></i>Đã quay lại đoàn</span>`;
                        setTimeout(() => { badgeContainer.innerHTML = ''; }, 3000);
                    }
                    
                    showToast('Khách đã quay lại đoàn thành công.', 'success');
                })
                .catch(err => {
                    console.error(err);
                    btnReturn.disabled = false;
                    btnReturn.innerHTML = originalHtml;
                    showToast('Có lỗi xảy ra, vui lòng thử lại.', 'danger');
                });
                return;
            }

            const btnSave = e.target.closest('.btn-save-free-time-ajax');
            if (btnSave) {
                const passengerId = btnSave.getAttribute('data-passenger-id');
                const passengerName = btnSave.getAttribute('data-passenger-name');
                const reason = document.getElementById(`reason-ft-${passengerId}`).value;
                const phone = document.getElementById(`phone-ft-${passengerId}`).value;
                const start = document.getElementById(`start-ft-${passengerId}`).value;
                const end = document.getElementById(`end-ft-${passengerId}`).value;
                const location = document.getElementById(`loc-ft-${passengerId}`).value;
                const retLocation = document.getElementById(`ret-loc-ft-${passengerId}`).value;

                btnSave.disabled = true;
                const originalHtml = btnSave.innerHTML;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(`/guide/schedules/${scheduleId}/passengers/${passengerId}/group-split`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tour_id: {{ $tourSchedule->tour_id }},
                        stop_id: parseInt(currentActivityId),
                        guest_id: parseInt(passengerId),
                        guest_name: passengerName,
                        reason: reason,
                        phone_number: phone,
                        start_time: start,
                        end_time: end,
                        split_location: location,
                        return_location: retLocation
                    })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(errData => { throw errData; });
                    }
                    return res.json();
                })
                .then(data => {
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalHtml;

                    const p = passengersData.find(x => x.id == passengerId);
                    if (p) {
                        p.active_split = data.data;
                        const nextBtn = document.getElementById(`save-extend-btn-${passengerId}`);
                        if (nextBtn) nextBtn.setAttribute('data-split-id', data.data.id);
                    }
                    
                    if (p && p.activity_checkins.includes(parseInt(currentActivityId))) {
                        p.activity_checkins = p.activity_checkins.filter(id => id !== parseInt(currentActivityId));
                    }
                    
                    updatePassengerRowUI(passengerId);
                    showToast('Đã tách đoàn cho khách, đồng hồ đếm ngược bắt đầu', 'success');
                })
                .catch(err => {
                    console.error(err);
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalHtml;
                    
                    let errorMsg = 'Không thể lưu thông tin tách đoàn.';
                    if (err && err.errors) {
                        const firstKey = Object.keys(err.errors)[0];
                        if (firstKey && err.errors[firstKey][0]) {
                            errorMsg = err.errors[firstKey][0];
                        }
                    } else if (err && err.message) {
                        errorMsg = err.message;
                    }
                    showToast(errorMsg, 'danger');
                });
            }
        });

        // 3. Toggle checkin checkbox via AJAX
        rollcallBody.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('activity-passenger-checkbox')) {
                const passengerId = e.target.getAttribute('data-passenger-id');
                const isChecked = e.target.checked;
                const tr = document.getElementById(`rollcall-row-${passengerId}`);
                const checkbox = e.target;

                // Optimistic UI: Đổi màu xanh / cập nhật ngay lập tức không chờ server
                tr.className = isChecked ? 'table-success' : '';
                const pData = passengersData.find(p => p.id == passengerId);
                if (pData) {
                    if (isChecked) {
                        if (!pData.activity_checkins.includes(parseInt(currentActivityId))) {
                            pData.activity_checkins.push(parseInt(currentActivityId));
                        }
                    } else {
                        pData.activity_checkins = pData.activity_checkins.filter(id => id !== parseInt(currentActivityId));
                    }
                }

                checkbox.disabled = true;

                const url = `/guide/schedules/${scheduleId}/activities/${currentActivityId}/passengers/${passengerId}/toggle-checkin`;
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    checkbox.disabled = false;
                    checkbox.checked = data.checked_in;
                    tr.className = data.checked_in ? 'table-success' : '';
                })
                .catch(err => {
                    console.error(err);
                    checkbox.disabled = false;
                    checkbox.checked = !isChecked;
                    tr.className = !isChecked ? 'table-success' : '';
                    showToast('Có lỗi xảy ra, vui lòng thử lại!', 'danger');
                });
            }
        });

        // 4. Chọn tất cả điểm danh
        const btnCheckinAll = document.getElementById('btn-checkin-all-activity');
        const btnUncheckAll = document.getElementById('btn-uncheck-all-activity');

        if (btnCheckinAll) {
            btnCheckinAll.addEventListener('click', function() {
                if (!currentActivityId) return;
                if (isLocked) return;
                
                btnCheckinAll.disabled = true;
                if (btnUncheckAll) btnUncheckAll.disabled = true;
                const originalHtml = btnCheckinAll.innerHTML;
                btnCheckinAll.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...';

                const url = `/guide/schedules/${scheduleId}/activities/${currentActivityId}/checkin-all`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    btnCheckinAll.disabled = isLocked;
                    if (btnUncheckAll) btnUncheckAll.disabled = isLocked;
                    btnCheckinAll.innerHTML = originalHtml;

                    if (data.success) {
                        const checkedIds = data.checked_in_ids || [];

                        // Cập nhật lại UI và Data
                        passengersData.forEach(p => {
                            if (checkedIds.includes(p.id)) {
                                if (!p.activity_checkins.includes(parseInt(currentActivityId))) {
                                    p.activity_checkins.push(parseInt(currentActivityId));
                                }
                            }
                            updatePassengerRowUI(p.id);
                        });

                        showToast(data.message, 'success');
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnCheckinAll.disabled = isLocked;
                    if (btnUncheckAll) btnUncheckAll.disabled = isLocked;
                    btnCheckinAll.innerHTML = originalHtml;
                    showToast('Có lỗi xảy ra khi điểm danh tất cả.', 'danger');
                });
            });
        }

        if (btnUncheckAll) {
            btnUncheckAll.addEventListener('click', function() {
                if (!currentActivityId) return;
                if (isLocked) return;
                
                btnUncheckAll.disabled = true;
                if (btnCheckinAll) btnCheckinAll.disabled = true;
                const originalHtmlUncheck = btnUncheckAll.innerHTML;
                btnUncheckAll.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...';

                const url = `/guide/schedules/${scheduleId}/activities/${currentActivityId}/uncheck-all`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    btnUncheckAll.disabled = isLocked;
                    if (btnCheckinAll) btnCheckinAll.disabled = isLocked;
                    btnUncheckAll.innerHTML = originalHtmlUncheck;

                    if (data.success) {
                        // Cập nhật lại UI và Data
                        passengersData.forEach(p => {
                            p.activity_checkins = p.activity_checkins.filter(id => id !== parseInt(currentActivityId));
                            updatePassengerRowUI(p.id);
                        });

                        showToast(data.message, 'success');
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnUncheckAll.disabled = isLocked;
                    if (btnCheckinAll) btnCheckinAll.disabled = isLocked;
                    btnUncheckAll.innerHTML = originalHtmlUncheck;
                    showToast('Có lỗi xảy ra khi hủy điểm danh.', 'danger');
                });
            });
        }

        // Sticky Bottom Actions for mobile
        const originalCompleteBtn = document.getElementById('btn-complete-tour');
        const stickyContainer = document.getElementById('mobile-sticky-actions');
        
        if (stickyContainer) {
            
            if (originalCompleteBtn) {
                const btn = document.createElement('button');
                btn.className = 'btn btn-success btn-sm fw-bold flex-grow-1 py-2';
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Hoàn thành Tour';
                btn.disabled = originalCompleteBtn.disabled;
                btn.title = originalCompleteBtn.title;
                
                const observer = new MutationObserver(function() {
                    btn.disabled = originalCompleteBtn.disabled;
                });
                observer.observe(originalCompleteBtn, { attributes: true, attributeFilter: ['disabled'] });
                
                btn.addEventListener('click', function() {
                    originalCompleteBtn.click();
                });
                stickyContainer.appendChild(btn);
            }
        }

        // Prevent ghost click / tap-through on mobile
        let lastModalHideTime = 0;
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hide.bs.modal', function() {
                lastModalHideTime = Date.now();
            });
        });

        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (Date.now() - lastModalHideTime < 400) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });

        // Viewport resize helper for virtual keyboard inputs on mobile
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                const activeEl = document.activeElement;
                if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
                    setTimeout(() => {
                        activeEl.scrollIntoView({ block: 'center', behavior: 'smooth' });
                    }, 100);
                }
            });
        }
    }
});
</script>

@foreach($tourSchedule->bookings as $booking)
    @if(in_array($booking->payment_status, ['pending', 'paid_30', 'paid_100']) && !in_array($booking->tour_status, [\App\Models\Booking::TOUR_CANCELLED_ADMIN, \App\Models\Booking::TOUR_CANCELLED_CUSTOMER]) && $booking->booking_status !== 'cancelled')
        @foreach($booking->booking_passengers as $passenger)
            <!-- Modal Chi tiết khách hàng -->
            <div class="modal fade text-start" id="passengerDetailModal-{{ $passenger->id }}" tabindex="-1" aria-labelledby="passengerDetailModalLabel-{{ $passenger->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-bottom px-4 py-3 bg-light">
                            <h5 class="modal-title fw-600" id="passengerDetailModalLabel-{{ $passenger->id }}">
                                <i class="bi bi-person-badge text-primary me-2"></i>Chi tiết khách hàng
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="text-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-2" style="width: 60px; height: 60px;">
                                    <i class="bi bi-person-fill fs-2"></i>
                                </div>
                                <h5 class="mb-1 fw-bold text-dark">{{ $passenger->full_name }}</h5>
                                <div>
                                    @if($passenger->passenger_type == 'adult')
                                        <span class="badge badge-soft-primary">Người lớn</span>
                                    @elseif($passenger->passenger_type == 'child')
                                        <span class="badge badge-soft-warning">Trẻ em</span>
                                    @else
                                        <span class="badge badge-soft-secondary">Em bé</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-3">
                                <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Thông tin cá nhân</h6>
                                <div class="row g-2 small">
                                    <div class="col-5 text-muted text-start">Giới tính:</div>
                                    <div class="col-7 fw-bold text-dark text-start">{{ $passenger->gender == 'male' ? 'Nam' : ($passenger->gender == 'female' ? 'Nữ' : 'Khác') }}</div>

                                    <div class="col-5 text-muted text-start">Ngày sinh:</div>
                                    <div class="col-7 fw-bold text-dark text-start">
                                        {{ $passenger->date_of_birth ? \Carbon\Carbon::parse($passenger->date_of_birth)->format('d/m/Y') : '—' }}
                                    </div>

                                    <div class="col-5 text-muted text-start">Số CCCD/Hộ chiếu:</div>
                                    <div class="col-7 fw-bold text-dark text-start">{{ $passenger->identity_number ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="card border-0 bg-light p-3 mb-3">
                                <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Thông tin liên hệ & Đơn hàng</h6>
                                <div class="row g-2 small">
                                    <div class="col-5 text-muted text-start">Mã Booking:</div>
                                    <div class="col-7 fw-bold text-primary text-start">#{{ $booking->id }}</div>

                                    <div class="col-5 text-muted text-start">Người đặt:</div>
                                    <div class="col-7 fw-bold text-dark text-start">{{ $booking->customer_name ?? ($booking->user->name ?? '—') }}</div>

                                    <div class="col-5 text-muted text-start">Số điện thoại:</div>
                                    <div class="col-7 fw-bold text-dark text-start">
                                        @php $phone = $booking->customer_phone ?? ($booking->user->phone ?? null); @endphp
                                        @if($phone)
                                            <a href="tel:{{ $phone }}" class="text-decoration-none"><i class="bi bi-telephone-fill me-1"></i>{{ $phone }}</a>
                                        @else
                                            �
                                        @endif
                                    </div>

                                    <div class="col-5 text-muted text-start">Email:</div>
                                    <div class="col-7 fw-bold text-dark text-start text-truncate">
                                        @php $email = $booking->customer_email ?? ($booking->user->email ?? null); @endphp
                                        @if($email)
                                            <a href="mailto:{{ $email }}" class="text-decoration-none"><i class="bi bi-envelope-fill me-1"></i>{{ $email }}</a>
                                        @else
                                            �
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($booking->booking_accommodations && $booking->booking_accommodations->count() > 0)
                                <div class="card border-0 bg-light p-3 mb-3 text-start">
                                    <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="bi bi-building me-1"></i>Thông tin lưu trú</h6>
                                    @foreach($booking->booking_accommodations as $index => $ba)
                                        <div class="{{ $index > 0 ? 'border-top pt-3 mt-3' : '' }}">
                                            <div class="row g-2 small">
                                                <div class="col-5 text-muted text-start">Khách sạn:</div>
                                                <div class="col-7 fw-bold text-dark text-start">
                                                    {{ $ba->accommodation_name_snapshot ?? ($ba->room_type->accommodation->name ?? '—') }}
                                                </div>

                                                <div class="col-5 text-muted text-start">Địa chỉ:</div>
                                                <div class="col-7 fw-semibold text-dark text-start" style="font-size: 0.75rem;">
                                                    {{ $ba->room_type->accommodation->address ?? '—' }}
                                                </div>

                                                <div class="col-5 text-muted text-start">Loại phòng:</div>
                                                <div class="col-7 fw-bold text-dark text-start">
                                                    <span class="badge bg-secondary">{{ $ba->room_type_name_snapshot ?? ($ba->room_type->name ?? '—') }}</span>
                                                </div>

                                                <div class="col-5 text-muted text-start">Số lượng:</div>
                                                <div class="col-7 fw-bold text-dark text-start">
                                                    ({{ $ba->single_rooms_count ?? 1 }} phòng, {{ $ba->extra_bed_qty ?? 0 }} giường phụ)
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @php
                                $activeSplit = $passenger->group_splits->whereIn('status', ['ON_TIME', 'OVERDUE', 'UNREACHABLE'])->first();
                            @endphp
                            @if($activeSplit)
                            <div class="card border-0 border-start border-3 border-primary bg-primary bg-opacity-10 p-3 mb-3">
                                <h6 class="fw-bold mb-2 text-primary text-start" style="font-size: 0.8rem;"><i class="bi bi-clock-history me-1"></i>Thông tin Tách đoàn:</h6>
                                <div class="row g-2 small text-start">
                                    <div class="col-5 text-muted">Thời gian:</div>
                                    <div class="col-7 fw-bold text-dark">{{ $activeSplit->start_time ? $activeSplit->start_time->format('H:i d/m') : '' }} - {{ $activeSplit->end_time ? $activeSplit->end_time->format('H:i d/m') : '' }}</div>
                                    <div class="col-5 text-muted">Lý do:</div>
                                    <div class="col-7 fw-bold text-dark">{{ $activeSplit->reason }}</div>
                                    <div class="col-5 text-muted">SĐT liên hệ:</div>
                                    <div class="col-7 fw-bold text-dark"><a href="tel:{{ $activeSplit->phone_number }}" class="text-decoration-none">{{ $activeSplit->phone_number }}</a></div>
                                    @if($activeSplit->split_location)
                                        <div class="col-5 text-muted">Điểm tách:</div>
                                        <div class="col-7 fw-bold text-dark">{{ $activeSplit->split_location }}</div>
                                    @endif
                                    <div class="col-5 text-muted">Điểm quay lại:</div>
                                    <div class="col-7 fw-bold text-dark">{{ $activeSplit->return_location }}</div>
                                </div>
                            </div>
                            @endif

                            @if($passenger->special_note)
                            <div class="card border-0 border-start border-3 border-warning bg-warning bg-opacity-10 p-3">
                                <h6 class="fw-bold mb-1 text-warning text-start" style="font-size: 0.8rem;"><i class="bi bi-sticky-fill me-1"></i>Ghi chú đặc biệt:</h6>
                                <p class="mb-0 small text-dark text-start">{{ $passenger->special_note }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer bg-light border-top px-4 py-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endforeach

<div class="d-md-none" id="mobile-sticky-actions" style="display: none;"></div>

@endsection

