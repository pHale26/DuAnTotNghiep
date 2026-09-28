@extends('layouts.admin')

@section('page-title', 'Quản lý Điều Hành Tour')

@section('content')

@if(isset($unassignedUpcomingSchedules) && $unassignedUpcomingSchedules->count() > 0)
<div class="alert alert-warning alert-dismissible fade show border-warning border-start border-4" role="alert">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
            <div>
                <h6 class="alert-heading mb-1 fw-bold">Cảnh báo: Có {{ $unassignedUpcomingSchedules->count() }} tour sắp khởi hành nhưng chưa có HDV!</h6>
                <p class="mb-0 small">Các tour khởi hành trong vòng 7 ngày tới cần được phân công Hướng dẫn viên sớm nhất có thể.</p>
            </div>
        </div>
        <button class="btn btn-sm btn-outline-warning text-dark fw-500 me-4" type="button" data-bs-toggle="collapse" data-bs-target="#unassignedToursList" aria-expanded="false" aria-controls="unassignedToursList">
            Xem danh sách <i class="bi bi-chevron-down ms-1"></i>
        </button>
    </div>
    
    <div class="collapse mt-2" id="unassignedToursList">
        <hr class="my-2 border-warning opacity-25">
        <div class="mb-0" style="max-height: 250px; overflow-y: auto;">
            <ul class="mb-0 ps-3">
                @foreach($unassignedUpcomingSchedules as $upcoming)
                    <li class="mb-2">
                        <strong>{{ $upcoming->tour->title ?? 'N/A' }}</strong> 
                        <span class="text-muted">(Khởi hành: <span class="text-danger fw-500">{{ \Carbon\Carbon::parse($upcoming->departure_date)->format('d/m/Y') }}</span>)</span>
                        - <a href="#" class="text-primary fw-medium text-decoration-underline ms-1" data-bs-toggle="modal" data-bs-target="#assignGuideModal{{ $upcoming->id }}"><i class="bi bi-person-plus me-1"></i>Phân công ngay</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="admin-card border-0 mb-4">
    <div class="admin-card-body">
        <form action="{{ route('admin.ongoing_tours.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>-- Tất cả lịch trình --</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Sắp khởi hành</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành / Kết thúc</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-geo-alt me-2 text-primary"></i>Danh sách Lịch trình Tour</h5>
    </div>
    <div class="admin-card-body p-0">
        <form action="{{ route('admin.ongoing_tours.index') }}" method="GET">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tên Tour</th>
                            <th>Ngày Khởi Hành</th>
                            <th>Số Khách / Sức Chứa</th>
                            <th>Hướng Dẫn Viên</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                        <tr>
                            <th class="ps-4">
                                <input type="text" name="search_tour" class="form-control form-control-sm" placeholder="Tìm tên/Mã Tour..." value="{{ request('search_tour') }}">
                            </th>
                            <th>
                                <input type="date" name="search_date" class="form-control form-control-sm" value="{{ request('search_date') }}">
                            </th>
                            <th>
                                <input type="number" name="search_guests" class="form-control form-control-sm" placeholder="Khách tối thiểu..." value="{{ request('search_guests') }}">
                            </th>
                            <th>
                                <input type="text" name="search_guide" class="form-control form-control-sm" placeholder="Tìm tên HDV..." value="{{ request('search_guide') }}">
                            </th>
                            <th class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Tìm kiếm"><i class="bi bi-search"></i></button>
                                    <a href="{{ route('admin.ongoing_tours.index', ['status' => request('status', 'all')]) }}" class="btn btn-sm btn-secondary" title="Xóa lọc"><i class="bi bi-x-circle"></i></a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($schedules as $schedule)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="{{ $schedule->tour->title ?? '' }}">
                                {{ $schedule->tour->title ?? 'N/A' }}
                            </div>
                            <div class="small text-muted mt-1"><i class="bi bi-upc-scan me-1"></i>{{ $schedule->code }}</div>
                        </td>
                        <td>
                            <div class="fw-500 text-primary">
                                <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                                @if($schedule->tour && $schedule->tour->departure_time)
                                    <span class="ms-1 text-warning" title="Giờ khởi hành"><i class="bi bi-clock-fill me-1"></i>{{ \Carbon\Carbon::parse($schedule->tour->departure_time)->format('H\hi') }}</span>
                                @endif
                            </div>
                            <small class="text-muted">Đến {{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            @php
                                $guests = $schedule->total_guests ?? 0;
                                $percent = $schedule->capacity > 0 ? round(($guests / $schedule->capacity) * 100) : 0;
                            @endphp
                            <div class="fw-bold">{{ $guests }} / {{ $schedule->capacity }}</div>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-{{ $percent >= 100 ? 'danger' : 'success' }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </td>
                        <td>
                            @if($schedule->schedule_guides->count() > 0)
                                <div class="d-flex flex-column gap-1">
                                    @foreach($schedule->schedule_guides as $sg)
                                        @if($sg->is_backup)
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-2 py-1 text-start" style="font-size: 11px; width: fit-content;">
                                                <i class="bi bi-person-dash me-1"></i>Dự phòng: {{ $sg->tour_guide->name ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1 text-start" style="font-size: 11px; width: fit-content;">
                                                <i class="bi bi-person-check me-1"></i>Chính: {{ $sg->tour_guide->name ?? 'N/A' }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="badge-soft badge-soft-warning px-2 py-1">Chưa phân công</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if(in_array($schedule->status, ['completed', 'closed']) || \Carbon\Carbon::parse($schedule->return_date)->endOfDay()->isPast())
                                <button type="button" class="btn btn-action text-secondary bg-secondary bg-opacity-10" disabled title="Tour đã kết thúc/đóng">
                                    <i class="bi bi-lock-fill"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-action text-primary bg-primary bg-opacity-10" 
                                    data-bs-toggle="modal" data-bs-target="#assignGuideModal{{ $schedule->id }}" title="Phân công HDV">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 text-light mb-2 d-block"></i>
                            Chưa có lịch trình nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </form>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $schedules->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

@php
    $allModalSchedules = collect($schedules->items())->merge($unassignedUpcomingSchedules ?? [])->unique('id');
@endphp
@foreach($allModalSchedules as $schedule)
@php
    $primaryGuide = $schedule->schedule_guides->firstWhere('is_backup', false);
    $backupGuide  = $schedule->schedule_guides->firstWhere('is_backup', true);
    $primaryGuideId = $primaryGuide ? (string) $primaryGuide->guide_id : '';
    $backupGuideId  = $backupGuide  ? (string) $backupGuide->guide_id  : '';
@endphp
<div class="modal fade" id="assignGuideModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.ongoing_tours.assign_guides', $schedule->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4 py-3 bg-light">
                    <h5 class="modal-title fw-600">
                        <i class="bi bi-person-lines-fill me-2 text-primary"></i>Phân công Hướng Dẫn Viên
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4"
                    x-data="guideSelectorV2('{{ $primaryGuideId }}', '{{ $backupGuideId }}', {{ json_encode($schedule->busy_guide_ids) }})">

                    {{-- Tour info --}}
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">
                            Tour: {{ $schedule->tour->title ?? 'N/A' }}
                        </label>
                        <p class="mb-0">Khởi hành: <strong>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}@if($schedule->tour && $schedule->tour->departure_time) ({{ \Carbon\Carbon::parse($schedule->tour->departure_time)->format('H\hi') }})@endif</strong></p>
                    </div>

                    <hr class="my-3">

                    {{-- ===== HDV CHÍNH ===== --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success rounded-pill">
                                <i class="bi bi-person-check me-1"></i>HDV Chính
                            </span>
                            <small class="text-muted">Chọn 1 hướng dẫn viên chính cho lịch trình</small>
                        </div>

                        <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                            <div>
                                <label class="d-flex align-items-center gap-3 p-2 rounded-2 border guide-option-label"
                                    :class="primaryGuide === '' ? 'border-success bg-success bg-opacity-10' : 'border-dashed text-muted'"
                                    style="cursor: pointer; border-style: dashed !important;">
                                    <input class="form-check-input m-0 flex-shrink-0"
                                        type="radio"
                                        name="primary_guide_id"
                                        value=""
                                        x-model="primaryGuide">
                                    <div class="flex-grow-1">
                                        <div class="fw-500 fst-italic text-muted">— Không chọn HDV chính —</div>
                                    </div>
                                </label>
                            </div>
                            @foreach($tourGuides as $guide)
                            <div>
                                <label class="d-flex align-items-center gap-3 p-2 rounded-2 border guide-option-label"
                                    :class="{
                                        'border-success bg-success bg-opacity-10': primaryGuide === '{{ $guide->id }}',
                                        'opacity-50 pe-none': busyGuides.includes('{{ $guide->id }}') && primaryGuide !== '{{ $guide->id }}',
                                    }"
                                    for="primary_{{ $schedule->id }}_{{ $guide->id }}"
                                    style="cursor: pointer;">
                                    <input class="form-check-input m-0 flex-shrink-0"
                                        type="radio"
                                        name="primary_guide_id"
                                        value="{{ $guide->id }}"
                                        id="primary_{{ $schedule->id }}_{{ $guide->id }}"
                                        x-model="primaryGuide"
                                        :disabled="busyGuides.includes('{{ $guide->id }}') && primaryGuide !== '{{ $guide->id }}'">
                                    <div class="flex-grow-1">
                                        <div class="fw-500 d-flex align-items-center gap-2">
                                            <span>{{ $guide->name }}</span>
                                            <template x-if="busyGuides.includes('{{ $guide->id }}')">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2" style="font-size: 10px; font-weight: 500;">Bận trùng lịch</span>
                                            </template>
                                        </div>
                                        <small class="text-muted">{{ $guide->phone }}</small>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-success ms-auto"
                                        x-show="primaryGuide === '{{ $guide->id }}'"
                                        style="display:none!important" x-transition></i>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- ===== HDV DỰ BỊ ===== --}}
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-secondary rounded-pill">
                                <i class="bi bi-person-dash me-1"></i>HDV Dự Bị
                            </span>
                            <small class="text-muted">Chọn 1 hướng dẫn viên dự bị (không bắt buộc)</small>
                        </div>

                        <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                            <div>
                                <label class="d-flex align-items-center gap-3 p-2 rounded-2 border guide-option-label"
                                    :class="backupGuide === '' ? 'border-secondary bg-secondary bg-opacity-10' : 'border-dashed text-muted'"
                                    style="cursor: pointer; border-style: dashed !important;">
                                    <input class="form-check-input m-0 flex-shrink-0"
                                        type="radio"
                                        name="backup_guide_id"
                                        value=""
                                        x-model="backupGuide">
                                    <div class="flex-grow-1">
                                        <div class="fw-500 fst-italic text-muted">— Không chọn HDV dự bị —</div>
                                    </div>
                                </label>
                            </div>
                            @foreach($tourGuides as $guide)
                            <div>
                                <label class="d-flex align-items-center gap-3 p-2 rounded-2 border guide-option-label"
                                    :class="{
                                        'border-secondary bg-secondary bg-opacity-10': backupGuide === '{{ $guide->id }}',
                                        'opacity-50 pe-none': (busyGuides.includes('{{ $guide->id }}') && backupGuide !== '{{ $guide->id }}') || primaryGuide === '{{ $guide->id }}',
                                    }"
                                    for="backup_{{ $schedule->id }}_{{ $guide->id }}"
                                    style="cursor: pointer;">
                                    <input class="form-check-input m-0 flex-shrink-0"
                                        type="radio"
                                        name="backup_guide_id"
                                        value="{{ $guide->id }}"
                                        id="backup_{{ $schedule->id }}_{{ $guide->id }}"
                                        x-model="backupGuide"
                                        :disabled="(busyGuides.includes('{{ $guide->id }}') && backupGuide !== '{{ $guide->id }}') || primaryGuide === '{{ $guide->id }}'">
                                    <div class="flex-grow-1">
                                        <div class="fw-500 d-flex align-items-center gap-2">
                                            <span>{{ $guide->name }}</span>
                                            <template x-if="primaryGuide === '{{ $guide->id }}'">
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2" style="font-size: 10px;">Đang là HDV chính</span>
                                            </template>
                                            <template x-if="busyGuides.includes('{{ $guide->id }}') && primaryGuide !== '{{ $guide->id }}'">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2" style="font-size: 10px; font-weight: 500;">Bận trùng lịch</span>
                                            </template>
                                        </div>
                                        <small class="text-muted">{{ $guide->phone }}</small>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-secondary ms-auto"
                                        x-show="backupGuide === '{{ $guide->id }}'"
                                        style="display:none!important" x-transition></i>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="bi bi-check2 me-1"></i>Lưu Phân Công
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
function guideSelectorV2(initialPrimary, initialBackup, busyGuides) {
    return {
        primaryGuide: initialPrimary || '',
        backupGuide: initialBackup || '',
        busyGuides: Array.isArray(busyGuides) ? busyGuides.map(id => id.toString()) : [],
    };
}
</script>
<style>
.guide-option-label {
    transition: all 0.18s ease;
    user-select: none;
}
.guide-option-label:not(.opacity-50):hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    border-color: var(--bs-primary) !important;
}
</style>
@endpush


@endsection
