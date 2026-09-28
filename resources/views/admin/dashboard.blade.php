@extends('layouts.admin')

@section('page-title', 'Bảng Điều Khiển')

@push('styles')
<style>
    .admin-dashboard-wrapper {
        background-color: #f8fafc;
        font-family: 'Inter', system-ui, sans-serif;
    }
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    /* Custom Progress bar styling */
    .progress-slim {
        height: 8px;
        border-radius: 4px;
        background-color: #f1f5f9;
        overflow: hidden;
        display: flex;
    }
    .progress-slim .progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease;
    }
    
    /* Table styling */
    .table-custom th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 600;
        border-bottom-width: 1px;
    }
    .table-custom td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .badge-soft-info {
        background: #e0f2fe;
        color: #0369a1;
    }
</style>
@endpush

@section('content')
<div class="admin-dashboard-wrapper">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Chào {{ auth()->user()->name ?? 'bạn' }}, đây là tổng quan hôm nay</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex bg-white rounded-pill shadow-sm p-1 align-items-center m-0" id="dateFilterForm">
                <input type="date" name="start_date" id="startDate" value="{{ $startDate->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="form-control border-0 shadow-none ps-3 pe-1 text-sm bg-transparent" style="width: 140px;" required>
                <span class="text-muted text-sm px-1">-</span>
                <input type="date" name="end_date" id="endDate" value="{{ $endDate->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="form-control border-0 shadow-none ps-1 pe-3 text-sm bg-transparent" style="width: 140px;" required>
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 py-1 text-sm fw-500 ms-1">
                    Lọc
                </button>
            </form>
        </div>
    </div>

    <!-- Actionable Items -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between stat-card">
                    <div>
                        <div class="text-muted text-sm mb-1">Đơn chờ duyệt (chưa thanh toán)</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $pendingBookingsCount }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.ongoing_tours.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between stat-card">
                    <div>
                        <div class="text-muted text-sm mb-1" title="Tour khởi hành trong 7 ngày tới chưa có HDV">Chờ xếp HDV (7 ngày tới)</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $unassignedGuidesCount ?? 0 }}</h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-exclamation fs-4"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="#" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-row align-items-center justify-content-between stat-card">
                    <div>
                        <div class="text-muted text-sm mb-1">Tin nhắn chưa đọc</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $unreadMessagesCount }}</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-chat-dots fs-4"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @php
        function calculateGrowth($diff, $prevTotal) {
            if ($prevTotal <= 0) return $diff > 0 ? 100 : 0;
            return round(($diff / $prevTotal) * 100, 1);
        }
        
        $growthBookings = calculateGrowth($diffBookings, $totalBookings - $diffBookings);
        $growthRevenue = calculateGrowth($diffRevenue, $totalRevenue - $diffRevenue);
        $growthNewUsers = calculateGrowth($diffNewUsers, $newUsersCount - $diffNewUsers);
        $growthCancel = calculateGrowth($diffCancelRate, $cancelRate - $diffCancelRate); // technically diff in points
    @endphp

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <!-- Bookings -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-cart-check fs-5"></i>
                    </div>
                    <span class="badge {{ $diffBookings >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' }} bg-opacity-10 rounded-pill px-2 py-1 text-xs">
                        <i class="bi bi-arrow-{{ $diffBookings >= 0 ? 'up' : 'down' }}"></i> {{ abs($growthBookings) }}%
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($totalBookings, 0, ',', '.') }}</h3>
                <p class="text-muted text-sm mb-4">Tổng lượt đặt tour</p>
                <div class="d-flex justify-content-between align-items-center text-xs mt-auto pt-3 border-top">
                    <span class="text-muted">So với {{ $periodLength }}N trước</span>
                    <span class="fw-bold {{ $diffBookings >= 0 ? 'text-success' : 'text-danger' }}">{{ $diffBookings > 0 ? '+' : '' }}{{ number_format($diffBookings, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-currency-dollar fs-5"></i>
                    </div>
                    <span class="badge {{ $diffRevenue >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' }} bg-opacity-10 rounded-pill px-2 py-1 text-xs">
                        <i class="bi bi-arrow-{{ $diffRevenue >= 0 ? 'up' : 'down' }}"></i> {{ abs($growthRevenue) }}%
                    </span>
                </div>
                @php 
                    $revFormatted = $totalRevenue >= 1000000000 ? round($totalRevenue / 1000000000, 2) . ' tỷ' : 
                                  ($totalRevenue >= 1000000 ? round($totalRevenue / 1000000, 2) . ' tr' : number_format($totalRevenue, 0, ',', '.'));
                    
                    $diffRevFormatted = abs($diffRevenue) >= 1000000000 ? round(abs($diffRevenue) / 1000000000, 2) . ' tỷ' : 
                                  (abs($diffRevenue) >= 1000000 ? round(abs($diffRevenue) / 1000000, 2) . ' tr' : number_format(abs($diffRevenue), 0, ',', '.'));
                @endphp
                <h3 class="fw-bold mb-1 text-dark">{{ $revFormatted }}</h3>
                <p class="text-muted text-sm mb-2">Thực thu (VNĐ)</p>
                
                <div class="d-flex justify-content-between align-items-center text-xs mb-3 text-muted">
                    <span>Tour: {{ number_format($totalTourRevenue, 0, ',', '.') }}đ</span>
                    <span>Vé: {{ number_format($totalTicketRevenue, 0, ',', '.') }}đ</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center text-xs mt-auto pt-3 border-top">
                    <span class="text-muted">So với {{ $periodLength }}N trước</span>
                    <span class="fw-bold {{ $diffRevenue >= 0 ? 'text-success' : 'text-danger' }}">{{ $diffRevenue > 0 ? '+' : '-' }}{{ $diffRevFormatted }}</span>
                </div>
            </div>
        </div>
        
        <!-- New Customers -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-person-plus fs-5"></i>
                    </div>
                    <span class="badge {{ $diffNewUsers >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' }} bg-opacity-10 rounded-pill px-2 py-1 text-xs">
                        <i class="bi bi-arrow-{{ $diffNewUsers >= 0 ? 'up' : 'down' }}"></i> {{ abs($growthNewUsers) }}%
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($newUsersCount, 0, ',', '.') }}</h3>
                <p class="text-muted text-sm mb-4">Khách hàng đăng ký mới</p>
                <div class="d-flex justify-content-between align-items-center text-xs mt-auto pt-3 border-top">
                    <span class="text-muted">So với {{ $periodLength }}N trước</span>
                    <span class="fw-bold {{ $diffNewUsers >= 0 ? 'text-success' : 'text-danger' }}">{{ $diffNewUsers > 0 ? '+' : '' }}{{ number_format($diffNewUsers, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Cancel Rate -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-x-circle fs-5"></i>
                    </div>
                    <!-- Lower cancel rate is better, so negative diff is success -->
                    <span class="badge {{ $diffCancelRate <= 0 ? 'bg-success text-success' : 'bg-danger text-danger' }} bg-opacity-10 rounded-pill px-2 py-1 text-xs">
                        <i class="bi bi-arrow-{{ $diffCancelRate > 0 ? 'up' : 'down' }}"></i> {{ abs(round($diffCancelRate, 1)) }}đ
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ round($cancelRate, 1) }}%</h3>
                <p class="text-muted text-sm mb-4">Tỷ lệ huỷ đơn</p>
                <div class="d-flex justify-content-between align-items-center text-xs mt-auto pt-3 border-top">
                    <span class="text-muted">So với {{ $periodLength }}N trước</span>
                    <span class="fw-bold {{ $diffCancelRate <= 0 ? 'text-success' : 'text-danger' }}">{{ $diffCancelRate > 0 ? '+' : '' }}{{ round($diffCancelRate, 1) }}đ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue Journey Line Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Hành trình doanh thu</h5>
                        <div class="text-muted text-sm">Doanh thu & số đơn đặt theo {{ $groupBy == 'month' ? 'tháng' : 'ngày' }}</div>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="text-sm d-flex align-items-center"><span class="bg-success rounded-circle me-2" style="width:10px;height:10px;"></span> Doanh thu</div>
                        <div class="text-sm d-flex align-items-center"><span class="bg-warning rounded-circle me-2" style="width:10px;height:10px;"></span> Số đơn</div>
                    </div>
                </div>
                <div style="height: 300px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Check-in Status Horizontal Bars -->
        <div class="col-lg-4">
             <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Trạng thái check-in đơn</h5>
                    <div class="text-muted text-sm mb-4">Toàn bộ vòng đời đơn đặt</div>
                </div>
                
                @php
                    // Group cancellations together
                    $mergedStatus = [
                        'upcoming' => $checkinStatus['upcoming'] ?? 0,
                        'in_progress' => ($checkinStatus['in_progress'] ?? 0) + ($checkinStatus['checking_in'] ?? 0),
                        'completed' => $checkinStatus['completed'] ?? 0,
                        'cancelled' => ($checkinStatus['cancelled_by_customer'] ?? 0) + ($checkinStatus['cancelled_by_admin'] ?? 0),
                    ];
                    
                    $mergedConfig = [
                        'upcoming' => ['label' => 'Sắp khởi hành', 'color' => '#3b82f6'],
                        'in_progress' => ['label' => 'Đang check-in / Đi tour', 'color' => '#10b981'],
                        'completed' => ['label' => 'Hoàn thành', 'color' => '#6366f1'],
                        'cancelled' => ['label' => 'Đã huỷ', 'color' => '#ef4444'],
                    ];
                    
                    $maxStatus = max($mergedStatus) ?: 1; // avoid division by zero
                @endphp
                
                <div class="d-flex flex-column gap-3 mt-2">
                    @foreach($mergedConfig as $key => $config)
                        @php 
                            $count = $mergedStatus[$key];
                            $width = ($count / $maxStatus) * 100;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between text-sm mb-1">
                                <span class="fw-500 text-dark">{{ $config['label'] }}</span>
                                <span class="fw-bold">{{ number_format($count, 0, ',', '.') }}</span>
                            </div>
                            <div class="progress-slim">
                                <div class="progress-bar" style="width: {{ $width }}%; height: 100%; background-color: {{ $config['color'] }} !important;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Lists Row -->
    <div class="row g-4 mb-4">
        <!-- Top Destinations -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Điểm đến dẫn đầu</h5>
                    <div class="text-muted text-sm mb-4">Theo số lượt đặt trong {{ $periodLength }} ngày</div>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($topDestinations as $index => $dest)
                        @php
                            $width = ($dest->total_bookings / $maxTopDestBookings) * 100;
                            // Colors map: 1st green, 2nd yellow, 3rd blue, 4th green-light, 5th red
                            $colors = ['#10b981', '#f59e0b', '#3b82f6', '#34d399', '#ef4444'];
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <div class="d-flex align-items-center">
                            <div class="text-muted fw-bold me-3 text-sm">0{{ $index + 1 }}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        @php
                                            $destName = $dest->province_name;
                                            if (is_string($destName) && str_starts_with(trim($destName), '{')) {
                                                $decoded = json_decode($destName, true);
                                                $destName = is_array($decoded) ? ($decoded['vi'] ?? $decoded['en'] ?? reset($decoded) ?: $destName) : $destName;
                                            }
                                        @endphp
                                        <div class="fw-bold text-dark text-sm">{{ $destName }}</div>
                                        <div class="text-muted text-xs">Điểm đến</div>
                                    </div>
                                    <div class="fw-bold text-sm">{{ number_format($dest->total_bookings, 0, ',', '.') }}</div>
                                </div>
                                <div class="progress-slim mt-1">
                                    <div class="progress-bar rounded-pill" style="width: {{ $width }}%; height: 100%; background-color: {{ $color }} !important;"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Không có dữ liệu điểm đến.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Tours -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Tour HOT nhất</h5>
                        <div class="text-muted text-sm">Theo số lượt đặt trong {{ $periodLength }} ngày</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;" data-bs-toggle="modal" data-bs-target="#topToursModal" title="Xem danh sách đầy đủ">
                        <i class="bi bi-arrows-angle-expand"></i>
                    </button>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($topTours as $index => $tour)
                        @php
                            $width = ($tour->total_bookings / $maxTopToursBookings) * 100;
                            $colors = ['#8b5cf6', '#ec4899', '#f43f5e', '#a855f7', '#d946ef'];
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <div class="d-flex align-items-center">
                            <div class="text-muted fw-bold me-3 text-sm">0{{ $index + 1 }}</div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="flex-grow-1 pe-2" style="min-width:0;">
                                        <div class="fw-bold text-dark text-sm text-truncate" title="{{ $tour->title }}">{{ $tour->title }}</div>
                                        <div class="text-muted text-xs">Tour</div>
                                    </div>
                                    <div class="fw-bold text-sm">{{ number_format($tour->total_bookings, 0, ',', '.') }}</div>
                                </div>
                                <div class="progress-slim mt-1">
                                    <div class="progress-bar rounded-pill" style="width: {{ $width }}%; height: 100%; background-color: {{ $color }} !important;"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Không có dữ liệu tour.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tour Fill Rate -->
        <div class="col-lg-4">
             <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Tỷ lệ lấp đầy tour</h5>
                        <div class="text-muted text-sm">Số ghế đã đặt / tổng số ghế — chuyến khởi hành gần nhất</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light rounded-circle" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;" data-bs-toggle="modal" data-bs-target="#tourFillRatesModal" title="Xem danh sách đầy đủ">
                        <i class="bi bi-arrows-angle-expand"></i>
                    </button>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($tourFillRates as $schedule)
                        @php
                            $guests = $schedule->total_guests ?? 0;
                            $capacity = $schedule->capacity > 0 ? $schedule->capacity : 1;
                            $percent = min(($guests / $capacity) * 100, 100);
                            $color = $percent >= 90 ? '#ef4444' : ($percent >= 70 ? '#f59e0b' : '#10b981');
                            
                            $daysUntilDeparture = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($schedule->departure_date)->startOfDay(), false);
                            $isWarning = $daysUntilDeparture <= 3 && $percent < 50;
                            
                            $hasGuide = $schedule->schedule_guides && $schedule->schedule_guides->count() > 0;
                        @endphp
                        <div class="{{ $isWarning ? 'bg-danger bg-opacity-10 p-2 rounded' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold {{ $isWarning ? 'text-danger' : 'text-dark' }} text-sm text-truncate" style="max-width:180px;" title="{{ $schedule->tour->title ?? 'Tour' }}">
                                            {{ $schedule->tour->title ?? 'Tour' }}
                                        </div>
                                        @if($isWarning) <i class="bi bi-exclamation-triangle-fill text-danger ms-1 flex-shrink-0" title="Cần đẩy sale!"></i> @endif
                                    </div>
                                    <div class="text-muted text-xs d-flex align-items-center gap-2 mt-1">
                                        <span>Khởi hành {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m') }}</span>
                                        <span class="badge {{ $hasGuide ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 text-{{ $hasGuide ? 'success' : 'secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                            <i class="bi bi-person-badge"></i> {{ $hasGuide ? 'Đã có HDV' : 'Chưa có HDV' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="fw-bold text-sm text-end">
                                    {{ $guests }}/{{ $schedule->capacity }} <br>
                                    <span class="text-muted text-xs fw-normal">{{ round($percent) }}%</span>
                                </div>
                            </div>
                            <div class="progress-slim mt-2">
                                <div class="progress-bar rounded-pill" style="width: {{ $percent }}%; background-color: {{ $color }}"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Không có tour nào sắp tới.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row g-4">
        <!-- Recent Bookings Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Đơn đặt gần đây</h5>
                        <div class="text-muted text-sm">10 giao dịch mới nhất</div>
                    </div>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light rounded-pill px-3 text-sm">Xem tất cả</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Mã vé</th>
                                <th>Khách hàng</th>
                                <th>Tour</th>
                                <th>Khởi hành</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Giá trị</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                @php
                                    // Randomize avatar color based on user id or name length
                                    $colors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'];
                                    $avatarColor = $colors[($booking->user_id ?? 0) % count($colors)];
                                    $userName = $booking->customer_name ?? ($booking->user->name ?? 'Khách lẻ');
                                    $initials = collect(explode(' ', $userName))->map(fn($part) => mb_substr($part, 0, 1))->take(2)->join('');
                                    
                                    // Status Badge mapping
                                    $statusMap = [
                                        'upcoming' => ['label' => 'Chờ xác nhận', 'class' => 'badge-soft badge-soft-warning'],
                                        'in_progress' => ['label' => 'Đã xác nhận', 'class' => 'badge-soft badge-soft-success'],
                                        'checking_in' => ['label' => 'Check-in', 'class' => 'badge-soft badge-soft-info'],
                                        'completed' => ['label' => 'Hoàn thành', 'class' => 'badge-soft badge-soft-primary'],
                                        'closed' => ['label' => 'closed', 'class' => 'badge-soft badge-soft-secondary'],
                                        'cancelled_by_customer' => ['label' => 'Đã huỷ', 'class' => 'badge-soft badge-soft-danger'],
                                        'cancelled_by_admin' => ['label' => 'Đã huỷ', 'class' => 'badge-soft badge-soft-danger'],
                                    ];
                                    $statusInfo = $statusMap[$booking->tour_status] ?? ['label' => $booking->tour_status, 'class' => 'badge-soft badge-soft-secondary'];
                                    
                                    $isCancelled = in_array($booking->tour_status, ['cancelled_by_customer', 'cancelled_by_admin']);
                                @endphp
                                <tr class="border-bottom">
                                    <td class="text-muted">{{ $booking->code }}</td>
                                    <td>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $userName }}</div>
                                            <div class="text-xs text-muted">{{ $booking->user->phone ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-500 text-truncate" style="max-width:150px;" title="{{ $booking->tour_schedule->tour->title ?? 'N/A' }}">
                                            {{ $booking->tour_schedule->tour->title ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $booking->tour_schedule ? \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="{{ $statusInfo['class'] }} text-nowrap d-inline-block">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $isCancelled ? 'text-muted text-decoration-line-through' : 'text-dark' }}">{{ number_format($booking->total_price, 0, ',', '.') }}đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có đơn đặt nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Reviews -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Đánh giá khách hàng</h5>
                    <div class="text-muted text-sm mb-4">Toàn hệ thống</div>
                </div>
                
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="display-4 fw-bold text-dark">{{ number_format($averageRating, 1) }}</div>
                    <div class="d-flex flex-column justify-content-center">
                        <div class="text-warning fs-5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= round($averageRating) ? '-fill' : ($i - 0.5 <= $averageRating ? '-half' : '') }}"></i>
                            @endfor
                        </div>
                        <div class="text-muted text-xs mt-1">{{ number_format($totalReviews, 0, ',', '.') }} lượt đánh giá</div>
                    </div>
                </div>
                
                <div class="d-flex flex-column gap-2 mb-4">
                    @foreach($ratings as $star => $data)
                        <div class="d-flex align-items-center gap-2 text-sm">
                            <span class="text-muted d-flex align-items-center" style="width: 30px;">{{ $star }}<i class="bi bi-star-fill text-warning ms-1" style="font-size:0.7rem;"></i></span>
                            <div class="progress-slim flex-grow-1">
                                <div class="progress-bar bg-warning" style="width: {{ $data['percent'] }}%"></div>
                            </div>
                            <span class="text-muted text-end" style="width: 35px;">{{ $data['percent'] }}%</span>
                        </div>
                    @endforeach
                </div>
                
                <div class="text-xs text-muted border-top pt-3">
                    Tất cả lượt đánh giá trong hệ thống. Lượt đánh giá trung bình phản ánh sự hài lòng chung của khách hàng.
                </div>
            </div>
        </div>
    </div>

    <!-- Guide Ratings Row -->
    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Top Hướng dẫn viên</h5>
                        <div class="text-muted text-sm">Điểm đánh giá trung bình cao nhất</div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless table-custom align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hướng dẫn viên</th>
                                <th>Số lượt đánh giá</th>
                                <th class="text-end">Điểm trung bình</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guideRatings ?? [] as $index => $guide)
                                <tr>
                                    <td class="text-muted fw-bold">0{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @php
                                                $colors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'];
                                                $color = $colors[strlen($guide->name) % count($colors)];
                                            @endphp
                                            <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:35px; height:35px; background-color: {{ $color }}">
                                                {{ mb_substr($guide->name, 0, 1) }}
                                            </div>
                                            <div class="fw-bold text-dark">{{ $guide->name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($guide->reviews_count) }} lượt</td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <span class="fw-bold fs-5 text-dark">{{ number_format($guide->kpi_score, 1) }}</span>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Chưa có đánh giá nào cho HDV.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Top Tours -->
<div class="modal fade" id="topToursModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light pb-3">
        <div>
            <h5 class="modal-title fw-bold">Danh sách Tour HOT nhất đầy đủ</h5>
            <div class="text-muted text-sm">Theo số lượt đặt trong {{ $periodLength }} ngày</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4">
        <div class="d-flex flex-column gap-4">
            @forelse($topTours as $index => $tour)
                @php
                    $width = ($tour->total_bookings / $maxTopToursBookings) * 100;
                    $colors = ['#8b5cf6', '#ec4899', '#f43f5e', '#a855f7', '#d946ef'];
                    $color = $colors[$index % count($colors)];
                @endphp
                <div class="d-flex align-items-start">
                    <div class="text-muted fw-bold me-3 fs-5 mt-1">0{{ $index + 1 }}</div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="pe-3">
                                <div class="fw-bold text-dark fs-6">{{ $tour->title }}</div>
                                <div class="text-muted text-sm">Tour</div>
                            </div>
                            <div class="fw-bold fs-5 text-dark">{{ number_format($tour->total_bookings, 0, ',', '.') }} <span class="text-sm text-muted fw-normal">lượt</span></div>
                        </div>
                        <div class="progress-slim mt-1">
                            <div class="progress-bar rounded-pill" style="width: {{ $width }}%; height: 100%; background-color: {{ $color }} !important;"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">Không có dữ liệu tour.</div>
            @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tour Fill Rates -->
<div class="modal fade" id="tourFillRatesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light pb-3">
        <div>
            <h5 class="modal-title fw-bold">Chi tiết Tỷ lệ lấp đầy tour</h5>
            <div class="text-muted text-sm">Số ghế đã đặt / tổng số ghế — chuyến khởi hành gần nhất</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4">
        <div class="d-flex flex-column gap-4">
            @forelse($tourFillRates as $schedule)
                @php
                    $guests = $schedule->total_guests ?? 0;
                    $capacity = $schedule->capacity > 0 ? $schedule->capacity : 1;
                    $percent = min(($guests / $capacity) * 100, 100);
                    $color = $percent >= 90 ? '#ef4444' : ($percent >= 70 ? '#f59e0b' : '#10b981');
                    
                    $daysUntilDeparture = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($schedule->departure_date)->startOfDay(), false);
                    $isWarning = $daysUntilDeparture <= 3 && $percent < 50;
                    
                    $hasGuide = $schedule->schedule_guides && $schedule->schedule_guides->count() > 0;
                @endphp
                <div class="{{ $isWarning ? 'bg-danger bg-opacity-10 p-3 rounded' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="pe-3">
                            <div class="d-flex align-items-center">
                                <div class="fw-bold {{ $isWarning ? 'text-danger' : 'text-dark' }} fs-6">
                                    {{ $schedule->tour->title ?? 'Tour' }}
                                </div>
                                @if($isWarning) <span class="badge bg-danger ms-2"><i class="bi bi-exclamation-triangle-fill"></i> Cần đẩy sale</span> @endif
                            </div>
                            <div class="text-muted text-sm d-flex align-items-center gap-3 mt-2">
                                <span><i class="bi bi-calendar-event me-1"></i> Khởi hành: {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}</span>
                                <span class="badge {{ $hasGuide ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 text-{{ $hasGuide ? 'success' : 'secondary' }} rounded-pill px-2 py-1">
                                    <i class="bi bi-person-badge"></i> {{ $hasGuide ? 'Đã có HDV' : 'Chưa có HDV' }}
                                </span>
                            </div>
                        </div>
                        <div class="fw-bold fs-5 text-end text-nowrap">
                            {{ $guests }} / {{ $schedule->capacity }} <br>
                            <span class="text-muted text-sm fw-normal">{{ round($percent) }}% đã lấp đầy</span>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar" style="width: {{ $percent }}%; background-color: {{ $color }}"></div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">Không có tour nào sắp tới.</div>
            @endforelse
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Revenue Line/Area Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Parse PHP data
    const chartData = @json($dates);
    const labels = Object.keys(chartData);
    const revenueData = labels.map(key => chartData[key].revenue);
    const orderData = labels.map(key => chartData[key].orders);

    // Gradient for Revenue Area
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');   // Emerald
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Doanh thu',
                    data: revenueData,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Số đơn',
                    data: orderData,
                    borderColor: '#f59e0b',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false // We built a custom legend in HTML
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1e293b',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.datasetIndex === 0) { // Revenue
                                    label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                } else { // Orders
                                    label += context.parsed.y;
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        callback: function(value, index, values) {
                            // Only show every Nth label to prevent crowding
                            const dateStr = labels[value];
                            if (dateStr.length === 7) {
                                // YYYY-MM
                                const parts = dateStr.split('-');
                                return parts[1] + '/' + parts[0];
                            }
                            const date = new Date(dateStr);
                            return date.getDate() + '/' + (date.getMonth() + 1);
                        },
                        maxTicksLimit: 15
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false,
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000000) return (value / 1000000000).toFixed(1) + ' tỷ';
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + ' tr';
                            return value;
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: false, // hide the order axis visually to keep it clean
                    position: 'right',
                    grid: { display: false }
                }
            }
        }
    });

    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    if (startDateInput && endDateInput) {
        function addOneDay(dateString) {
            if (!dateString) return '';
            const d = new Date(dateString);
            d.setDate(d.getDate() + 1);
            return d.toISOString().split('T')[0];
        }

        function subOneDay(dateString) {
            if (!dateString) return '';
            const d = new Date(dateString);
            d.setDate(d.getDate() - 1);
            return d.toISOString().split('T')[0];
        }

        startDateInput.addEventListener('change', function() {
            if (this.value > this.max) this.value = this.max;
            
            const minEndDate = addOneDay(this.value);
            if (endDateInput.value && endDateInput.value < minEndDate) {
                endDateInput.value = minEndDate;
            }
            endDateInput.min = minEndDate;
        });

        endDateInput.addEventListener('change', function() {
            if (this.value > this.max) this.value = this.max;
            
            const maxStartDate = subOneDay(this.value);
            if (startDateInput.value && startDateInput.value >= this.value) {
                startDateInput.value = maxStartDate;
            }
        });
        
        if (startDateInput.value) {
            endDateInput.min = addOneDay(startDateInput.value);
        }
    }
});
</script>
@endpush