@extends('layouts.admin')

@section('page-title', 'Báo cáo Tour & Báo bận')

@section('content')
    <div class="mb-4">
        <ul class="nav nav-tabs border-bottom" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.reports.index', ['tab' => 'reports']) }}"
                    class="nav-link fw-semibold py-3 px-4 {{ $tab === 'reports' ? 'active' : '' }}" type="button">
                    <i class="bi bi-file-earmark-check me-2 text-primary"></i>Báo cáo Tour
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('admin.reports.index', ['tab' => 'absence']) }}"
                    class="nav-link fw-semibold py-3 px-4 {{ $tab === 'absence' ? 'active' : '' }}" type="button">
                    <i class="bi bi-calendar-x me-2 text-danger"></i>Yêu cầu báo bận
                    @php
                        $pendingAbsences = \App\Models\TourAbsenceRequest::whereIn('status', ['pending_review', 'pending_review_urgent'])->count();
                    @endphp
                    @if($pendingAbsences > 0)
                        <span class="badge bg-danger ms-2 rounded-pill">{{ $pendingAbsences }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="reportTabsContent">
        <!-- Tab 1: Tour Reports -->
        @if($tab === 'reports')
            <div class="admin-card border-0">
                <div class="admin-card-header bg-white py-3">
                    <h5 class="admin-card-title mb-0"><i class="bi bi-list-task me-2 text-primary"></i>Danh sách Báo Cáo Tour
                    </h5>
                </div>
                <div class="admin-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Mã Tour / Ngày khởi hành</th>
                                    <th>Tên Tour</th>
                                    <th>Hướng dẫn viên</th>
                                    <th>Khách thực tế</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $report->tour_schedule->tour->code ?? 'N/A' }}</div>
                                            <div class="small text-muted">
                                                {{ $report->tour_schedule->departure_date->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 250px;">
                                                {{ $report->tour_schedule->tour->title ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>{{ $report->tour_guide->name ?? 'N/A' }}</td>
                                        <td>{{ $report->actual_guests }}</td>
                                        <td>
                                            @if($report->status === 'approved')
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Đã
                                                    duyệt</span>
                                            @else
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1">Chờ
                                                    duyệt</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.reports.show', $report->id) }}"
                                                class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Chưa có báo cáo nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="admin-card-body border-top py-3">
                    {{ $reports->appends(['tab' => 'reports'])->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

        <!-- Tab 2: Absence Requests -->
        @if($tab === 'absence')
            <div class="admin-card border-0">
                <div class="admin-card-header bg-white py-3">
                    <h5 class="admin-card-title mb-0"><i class="bi bi-shield-exclamation me-2 text-danger"></i>Danh sách HDV Báo
                        Bận đột xuất</h5>
                </div>
                <div class="admin-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tour / Ngày khởi hành</th>
                                    <th>HDV Báo bận</th>
                                    <th>Lý do</th>
                                    <th>Thời gian còn lại</th>
                                    <th>HDV phụ hiện tại</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absenceRequests as $req)
                                    @php
                                        $backupGuide = $req->tour_schedule ? $req->tour_schedule->schedule_guides->where('is_backup', true)->first() : null;
                                        $backupGuideName = ($backupGuide && $backupGuide->tour_guide) ? $backupGuide->tour_guide->name : '';
                                        $backupGuideId = ($backupGuide && $backupGuide->tour_guide) ? $backupGuide->guide_id : 'null';
                                        $departure = \Carbon\Carbon::parse($req->tour_schedule->departure_date);
                                        $isPast = $departure->isPast();
                                        $timeLeft = now()->diffForHumans($departure, true);
                                    @endphp
                                    <tr class="{{ $req->status === 'pending_review_urgent' ? 'bg-danger bg-opacity-5' : '' }}">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $req->tour->code }}</div>
                                            <div class="text-truncate small text-secondary" style="max-width: 200px;">
                                                {{ $req->tour->title }}
                                            </div>
                                            <div class="small text-muted">{{ $departure->format('H:i d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $req->main_guide->name }}</div>
                                            <div class="small text-muted">{{ $req->main_guide->phone }}</div>
                                        </td>
                                        <td>
                                            <div class="text-wrap" style="max-width: 250px;">{{ $req->reason }}</div>
                                            @if($req->attachment_url)
                                                <div class="mt-1">
                                                    <a href="{{ $req->attachment_url }}" target="_blank"
                                                        class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 0.8rem;">
                                                        <i class="bi bi-paperclip"></i> Xem tài liệu
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isPast)
                                                <span class="text-danger small">Đã khởi hành</span>
                                            @else
                                                <span
                                                    class="small {{ $req->status === 'pending_review_urgent' ? 'text-danger fw-bold' : 'text-dark' }}">
                                                    {{ $timeLeft }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($backupGuide && $backupGuide->tour_guide)
                                                <div class="fw-medium text-success">{{ $backupGuide->tour_guide->name }}</div>
                                                <div class="small text-muted">HDV Phụ -> Chuyển làm HDV Chính</div>
                                            @else
                                                <span class="text-danger small fw-medium"><i
                                                        class="bi bi-exclamation-triangle-fill me-1"></i>Chưa có HDV phụ</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($req->status === 'pending_review_urgent')
                                                <span class="badge bg-danger text-white px-2 py-1 animate-pulse"><i
                                                        class="bi bi-exclamation-circle me-1"></i>Gấp - Chờ duyệt</span>
                                            @elseif($req->status === 'pending_review')
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1">Chờ
                                                    duyệt</span>
                                            @elseif($req->status === 'approved')
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Đã
                                                    duyệt</span>
                                            @elseif($req->status === 'rejected')
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">Bị
                                                    từ chối</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @if(in_array($req->status, ['pending_review', 'pending_review_urgent']))
                                                <button class="btn btn-sm btn-success me-1"
                                                    onclick="openApproveModal({{ $req->id }}, {{ $req->tour_schedule_id }}, {{ json_encode($backupGuideName) }}, {{ $backupGuideId }})">
                                                    Duyệt
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="openRejectModal({{ $req->id }})">
                                                    Từ chối
                                                </button>
                                            @else
                                                                    <button class="btn btn-sm btn-light border" onclick="viewDetailsModal({{ json_encode([
                                                    'status' => $req->status,
                                                    'reviewer' => $req->reviewer?->name ?? 'N/A',
                                                    'reviewed_at' => $req->reviewed_at ? $req->reviewed_at->format('H:i d/m/Y') : 'N/A',
                                                    'new_main' => $req->new_main_guide?->name ?? 'N/A',
                                                    'new_backup' => $req->new_backup_guide?->name ?? 'Không phân công',
                                                    'reject_reason' => $req->reject_reason ?? 'Không có'
                                                ]) }})">
                                                                        Xem log
                                                                    </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Không có yêu cầu báo bận nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="admin-card-body border-top py-3">
                    {{ $absenceRequests->appends(['tab' => 'absence'])->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Approval -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approveForm" method="POST" action="javascript:void(0);">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="approveModalLabel"><i
                                class="bi bi-check-circle-fill text-success me-2"></i>Duyệt yêu cầu báo bận</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="backup-guide-info"
                            class="alert alert-info border-0 bg-info bg-opacity-10 text-info mb-3 d-none">
                            <i class="bi bi-info-circle-fill me-1"></i> Tour này đang có HDV phụ là <strong
                                id="backup-guide-name"></strong>.
                            Khi duyệt, người này sẽ tự động chuyển lên làm <strong>HDV chính</strong>.
                        </div>

                        <div id="no-backup-guide-warning"
                            class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning mb-3 d-none">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Tour này <strong>chưa có HDV phụ</strong>.
                            Bạn bắt buộc phải chọn một HDV rảnh trong giờ này để bổ nhiệm làm <strong>HDV chính</strong>
                            mới.
                        </div>

                        <!-- Dropdown HDV chính mới (Chỉ hiển thị khi không có HDV phụ) -->
                        <div class="mb-3 d-none" id="main-guide-select-wrapper">
                            <label for="new_main_guide_id" class="form-label fw-bold">Chọn HDV chính mới <span
                                    class="text-danger">*</span></label>
                            <select name="new_main_guide_id" id="new_main_guide_id" class="form-select select2-modal"
                                style="width: 100%;">
                                <option value="">-- Đang tải danh sách HDV rảnh --</option>
                            </select>
                        </div>

                        <!-- Dropdown HDV phụ mới (Bắt buộc chọn) -->
                        <div class="mb-3">
                            <label for="new_backup_guide_id" class="form-label fw-bold">Chọn HDV phụ mới <span
                                    class="text-danger">*</span></label>
                            <select name="new_backup_guide_id" id="new_backup_guide_id" class="form-select select2-modal"
                                style="width: 100%;" required>
                                <option value="">-- Đang tải danh sách HDV rảnh --</option>
                            </select>
                            <div class="form-text text-muted">Vui lòng chọn 1 Hướng dẫn viên đang rảnh để gán làm HDV phụ
                                mới.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success fw-bold">Xác nhận duyệt</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="rejectForm" method="POST" action="javascript:void(0);">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-danger" id="rejectModalLabel"><i
                                class="bi bi-x-circle-fill text-danger me-2"></i>Từ chối yêu cầu báo bận</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reject_reason" class="form-label fw-bold">Lý do từ chối <span
                                    class="text-danger">*</span></label>
                            <textarea name="reject_reason" id="reject_reason" rows="4" class="form-control"
                                placeholder="Nhập lý do từ chối gửi về cho HDV..." required></textarea>
                            <div class="form-text text-muted">Lý do từ chối sẽ được gửi trực tiếp thành thông báo tới Hướng
                                dẫn viên.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger fw-bold">Từ chối yêu cầu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Log Details -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="detailsModalLabel"><i
                            class="bi bi-info-circle me-2 text-primary"></i>Chi tiết xử lý yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered mb-0">
                        <tbody id="details-body">
                            <!-- Filled by JS -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var approveModalObj;
    var rejectModalObj;
    var detailsModalObj;

        document.addEventListener('DOMContentLoaded', function () {
            approveModalObj = new bootstrap.Modal(document.getElementById('approveModal'));
            rejectModalObj = new bootstrap.Modal(document.getElementById('rejectModal'));
            detailsModalObj = new bootstrap.Modal(document.getElementById('detailsModal'));

            const approveForm = document.getElementById('approveForm');
            if (approveForm) {
                approveForm.addEventListener('submit', function (e) {
                    const action = this.getAttribute('action');
                    if (!action || action === 'javascript:void(0);' || action === '') {
                        e.preventDefault();
                        alert('Lỗi: Chưa xác định được ID yêu cầu báo bận. Vui lòng mở lại modal.');
                        return false;
                    }

                    const backupSelect = document.getElementById('new_backup_guide_id');
                    if (backupSelect && backupSelect.disabled) {
                        e.preventDefault();
                        alert('Hệ thống đang tải danh sách HDV rảnh, vui lòng đợi trong giây lát!');
                        return false;
                    }

                    if (!backupSelect || !backupSelect.value) {
                        e.preventDefault();
                        alert('Vui lòng chọn Hướng dẫn viên phụ mới trước khi xác nhận duyệt!');
                        return false;
                    }

                    // Disallow double-click submit and show loading indicator on submit button
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang xử lý...';
                    }
                });
            }

            const approveModalEl = document.getElementById('approveModal');
            if (approveModalEl) {
                approveModalEl.addEventListener('hidden.bs.modal', function () {
                    if (approveForm) {
                        const submitBtn = approveForm.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Xác nhận duyệt';
                        }
                    }
                });
            }

            const rejectForm = document.getElementById('rejectForm');
            if (rejectForm) {
                rejectForm.addEventListener('submit', function (e) {
                    const action = this.getAttribute('action');
                    if (!action || action === 'javascript:void(0);' || action === '') {
                        e.preventDefault();
                        alert('Lỗi: Chưa xác định được ID yêu cầu từ chối. Vui lòng mở lại modal.');
                        return false;
                    }
                });
            }
        });

        function openApproveModal(requestId, scheduleId, backupGuideName, backupGuideId = null) {
            const approveForm = document.getElementById('approveForm');
            if (approveForm) {
                approveForm.action = `/admin/absence-requests/${requestId}/approve`;
                const submitBtn = approveForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Xác nhận duyệt';
                }
            }

            const backupInfo = document.getElementById('backup-guide-info');
            const noBackupWarning = document.getElementById('no-backup-guide-warning');
            const mainGuideWrapper = document.getElementById('main-guide-select-wrapper');
            const mainSelect = document.getElementById('new_main_guide_id');
            const backupSelect = document.getElementById('new_backup_guide_id');

            // Set loading placeholder & disable select elements while loading AJAX
            mainSelect.innerHTML = '<option value="">⏳ Đang tải danh sách HDV rảnh...</option>';
            backupSelect.innerHTML = '<option value="">⏳ Đang tải danh sách HDV rảnh...</option>';
            mainSelect.disabled = true;
            backupSelect.disabled = true;

            if (backupGuideName) {
                document.getElementById('backup-guide-name').textContent = backupGuideName;
                backupInfo.classList.remove('d-none');
                noBackupWarning.classList.add('d-none');
                mainGuideWrapper.classList.add('d-none');
                mainSelect.removeAttribute('required');
            } else {
                backupInfo.classList.add('d-none');
                noBackupWarning.classList.remove('d-none');
                mainGuideWrapper.classList.remove('d-none');
                mainSelect.setAttribute('required', 'required');
            }

            approveModalObj.show();

            // Load available guides via AJAX
            fetch(`/admin/absence-requests/available-guides/${scheduleId}`)
                .then(res => res.json())
                .then(guides => {
                    // Clear loading options & enable selects
                    mainSelect.innerHTML = '<option value="">-- Chọn hướng dẫn viên --</option>';
                    backupSelect.innerHTML = '<option value="">-- Chọn hướng dẫn viên phụ --</option>';
                    mainSelect.disabled = false;
                    backupSelect.disabled = false;

                    if (!guides || guides.length === 0) {
                        mainSelect.innerHTML = '<option value="">-- Không có HDV rảnh trong khung giờ này --</option>';
                        backupSelect.innerHTML = '<option value="">-- Không có HDV rảnh trong khung giờ này --</option>';
                        return;
                    }

                    let normalGuides = [];
                    let promotedGuide = null;

                    guides.forEach(g => {
                        if (backupGuideId && g.id === backupGuideId) {
                            promotedGuide = g;
                        } else {
                            normalGuides.push(g);
                        }
                    });

                    // Populate main select
                    guides.forEach(g => {
                        const optionMain = document.createElement('option');
                        optionMain.value = g.id;
                        optionMain.textContent = `${g.name} (${g.phone})`;
                        mainSelect.appendChild(optionMain);
                    });

                    // Populate backup select
                    normalGuides.forEach(g => {
                        const optionBackup = document.createElement('option');
                        optionBackup.value = g.id;
                        optionBackup.textContent = `${g.name} (${g.phone})`;
                        backupSelect.appendChild(optionBackup);
                    });

                    // Append promoted guide at the end (disabled)
                    if (promotedGuide) {
                        const optionBackup = document.createElement('option');
                        optionBackup.value = promotedGuide.id;
                        optionBackup.textContent = `${promotedGuide.name} (${promotedGuide.phone}) - [HDV phụ hiện tại được lên làm chính]`;
                        optionBackup.disabled = true;
                        optionBackup.style.color = '#94a3b8';
                        backupSelect.appendChild(optionBackup);
                    }
                })
                .catch(err => {
                    console.error('Error fetching available guides:', err);
                    mainSelect.disabled = false;
                    backupSelect.disabled = false;
                    mainSelect.innerHTML = '<option value="">-- Lỗi tải danh sách HDV --</option>';
                    backupSelect.innerHTML = '<option value="">-- Lỗi tải danh sách HDV --</option>';
                    alert('Không thể tải danh sách hướng dẫn viên rảnh.');
                });

            // Add dynamic listener to avoid selecting the same guide for both main and backup
            mainSelect.addEventListener('change', function () {
                const selectedMainId = this.value;
                Array.from(backupSelect.options).forEach(opt => {
                    if (opt.value && opt.value === selectedMainId) {
                        opt.disabled = true;
                    } else if (opt.value && (!backupGuideId || parseInt(opt.value) !== backupGuideId)) {
                        opt.disabled = false;
                    }
                });
            });
        }

        function openRejectModal(requestId) {
            document.getElementById('rejectForm').action = `/admin/absence-requests/${requestId}/reject`;
            document.getElementById('reject_reason').value = '';
            rejectModalObj.show();
        }

        function viewDetailsModal(details) {
            const body = document.getElementById('details-body');
            body.innerHTML = '';

            const appendRow = (label, value) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<th style="width: 40%; bg-light">${label}</th><td>${value}</td>`;
                body.appendChild(tr);
            };

            if (details.status === 'approved') {
                appendRow('Trạng thái', '<span class="badge bg-success bg-opacity-10 text-success border border-success">Đã duyệt</span>');
                appendRow('Người duyệt', details.reviewer);
                appendRow('Thời gian duyệt', details.reviewed_at);
                appendRow('HDV chính mới', details.new_main);
                appendRow('HDV phụ mới', details.new_backup);
            } else {
                appendRow('Trạng thái', '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Bị từ chối</span>');
                appendRow('Người duyệt', details.reviewer);
                appendRow('Thời gian từ chối', details.reviewed_at);
                appendRow('Lý do từ chối', details.reject_reason);
            }

            detailsModalObj.show();
        }
    </script>
    <style>
        .animate-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        const backupInfo = document.getElementById('backup-guide-info');
        const noBackupWarning = document.getElementById('no-backup-guide-warning');
        const mainGuideWrapper = document.getElementById('main-guide-select-wrapper');
        const mainSelect = document.getElementById('new_main_guide_id');
        const backupSelect = document.getElementById('new_backup_guide_id');

        // Set loading placeholder & disable select elements while loading AJAX
        mainSelect.innerHTML = '<option value="">⏳ Đang tải danh sách HDV rảnh...</option>';
        backupSelect.innerHTML = '<option value="">⏳ Đang tải danh sách HDV rảnh...</option>';
        mainSelect.disabled = true;
        backupSelect.disabled = true;

        if (backupGuideName) {
            document.getElementById('backup-guide-name').textContent = backupGuideName;
            backupInfo.classList.remove('d-none');
            noBackupWarning.classList.add('d-none');
            mainGuideWrapper.classList.add('d-none');
            mainSelect.removeAttribute('required');
        } else {
            backupInfo.classList.add('d-none');
            noBackupWarning.classList.remove('d-none');
            mainGuideWrapper.classList.remove('d-none');
            mainSelect.setAttribute('required', 'required');
        }

        approveModalObj.show();

        // Load available guides via AJAX
        fetch(`/admin/absence-requests/available-guides/${scheduleId}`)
            .then(res => res.json())
            .then(guides => {
                // Clear loading options & enable selects
                mainSelect.innerHTML = '<option value="">-- Chọn hướng dẫn viên --</option>';
                backupSelect.innerHTML = '<option value="">-- Chọn hướng dẫn viên phụ --</option>';
                mainSelect.disabled = false;
                backupSelect.disabled = false;

                if (!guides || guides.length === 0) {
                    mainSelect.innerHTML = '<option value="">-- Không có HDV rảnh trong khung giờ này --</option>';
                    backupSelect.innerHTML = '<option value="">-- Không có HDV rảnh trong khung giờ này --</option>';
                    return;
                }

                let normalGuides = [];
                let promotedGuide = null;

                guides.forEach(g => {
                    if (backupGuideId && g.id === backupGuideId) {
                        promotedGuide = g;
                    } else {
                        normalGuides.push(g);
                    }
                });

                // Populate main select
                guides.forEach(g => {
                    const optionMain = document.createElement('option');
                    optionMain.value = g.id;
                    optionMain.textContent = `${g.name} (${g.phone})`;
                    mainSelect.appendChild(optionMain);
                });

                // Populate backup select
                normalGuides.forEach(g => {
                    const optionBackup = document.createElement('option');
                    optionBackup.value = g.id;
                    optionBackup.textContent = `${g.name} (${g.phone})`;
                    backupSelect.appendChild(optionBackup);
                });

                // Append promoted guide at the end (disabled)
                if (promotedGuide) {
                    const optionBackup = document.createElement('option');
                    optionBackup.value = promotedGuide.id;
                    optionBackup.textContent = `${promotedGuide.name} (${promotedGuide.phone}) - [HDV phụ hiện tại được lên làm chính]`;
                    optionBackup.disabled = true;
                    optionBackup.style.color = '#94a3b8';
                    backupSelect.appendChild(optionBackup);
                }
            })
            .catch(err => {
                console.error('Error fetching available guides:', err);
                mainSelect.disabled = false;
                backupSelect.disabled = false;
                mainSelect.innerHTML = '<option value="">-- Lỗi tải danh sách HDV --</option>';
                backupSelect.innerHTML = '<option value="">-- Lỗi tải danh sách HDV --</option>';
                alert('Không thể tải danh sách hướng dẫn viên rảnh.');
            });

        // Add dynamic listener to avoid selecting the same guide for both main and backup
        mainSelect.onchange = function() {
            const selectedMainId = this.value;
            Array.from(backupSelect.options).forEach(opt => {
                if (opt.value && opt.value === selectedMainId) {
                    opt.disabled = true;
                } else if (opt.value && (!backupGuideId || parseInt(opt.value) !== backupGuideId)) {
                    opt.disabled = false;
                }
            });
        };
    }

    function openRejectModal(requestId) {
        document.getElementById('rejectForm').action = `/admin/absence-requests/${requestId}/reject`;
        document.getElementById('reject_reason').value = '';
        rejectModalObj.show();
    }

    function viewDetailsModal(details) {
        const body = document.getElementById('details-body');
        body.innerHTML = '';

        const appendRow = (label, value) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<th style="width: 40%; bg-light">${label}</th><td>${value}</td>`;
            body.appendChild(tr);
        };

        if (details.status === 'approved') {
            appendRow('Trạng thái', '<span class="badge bg-success bg-opacity-10 text-success border border-success">Đã duyệt</span>');
            appendRow('Người duyệt', details.reviewer);
            appendRow('Thời gian duyệt', details.reviewed_at);
            appendRow('HDV chính mới', details.new_main);
            appendRow('HDV phụ mới', details.new_backup);
        } else {
            appendRow('Trạng thái', '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Bị từ chối</span>');
            appendRow('Người duyệt', details.reviewer);
            appendRow('Thời gian từ chối', details.reviewed_at);
            appendRow('Lý do từ chối', details.reject_reason);
        }

        detailsModalObj.show();
    }
</script>
<style>
    .animate-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
</style>
@endsection
