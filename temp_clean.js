
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let totalCount = 1;
    let checkedCount = 1;
    const isLocked = 1;
    const scheduleId = 1;
    let passengersData = [];

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

    // ─── Group Status Toggle ──────────────────────────────────────────
    const toggleGroupBtn = document.getElementById('toggle-group-status-btn');
    const groupWrapper = document.getElementById('group-status-form-wrapper');
    if (toggleGroupBtn && groupWrapper) {
        toggleGroupBtn.addEventListener('click', function() {
            if (groupWrapper.style.display === 'none') {
                groupWrapper.style.display = 'block';
            } else {
                groupWrapper.style.display = 'none';
            }
        });
    }

    const groupStatusSelect = document.getElementById('group-tour-status-select');
    const groupCheckinContainer = document.getElementById('group-checkin-step-container');
    if (groupStatusSelect && groupCheckinContainer) {
        groupStatusSelect.addEventListener('change', function() {
            if (this.value === 'checking_in') {
                groupCheckinContainer.style.display = 'block';
            } else {
                groupCheckinContainer.style.display = 'none';
            }
        });
    }

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

    // Chạy khi load trang
    setTimeout(updateActivityLocks, 100);

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

    function renderSplitBadge(split, passengerId) {
        if (!split) return '';
        let badgeHtml = '';
        let returnBtnHtml = '';
        let extendBtnHtml = '';
        let historyHtml = '';
        let countdownHtml = `<span id="countdown-${passengerId}">--:--</span>`;

        if (split.status === 'ON_TIME') {
            badgeHtml = `<span class="badge bg-primary text-white"><i class="bi bi-clock-history me-1"></i>Đang tách đoàn (<span id="countdown-${passengerId}">--:--</span>)</span>`;
        } else if (split.status === 'OVERDUE') {
            badgeHtml = `<span class="badge bg-danger text-white"><i class="bi bi-exclamation-triangle-fill me-1"></i>Quá giờ (<span id="countdown-${passengerId}">--:--</span>)</span>`;
        } else if (split.status === 'UNREACHABLE') {
            badgeHtml = `<span class="badge" style="background-color: #8b0000; color: white; font-weight: bold;"><i class="bi bi-exclamation-octagon-fill me-1"></i>Không liên lạc được</span>`;
        } else if (split.status === 'RETURNED') {
            badgeHtml = `<span class="badge bg-success text-white"><i class="bi bi-check-circle-fill me-1"></i>Đã quay lại đoàn</span>`;
        }

        if (['ON_TIME', 'OVERDUE'].includes(split.status)) {
            extendBtnHtml = `<button type="button" class="btn btn-sm btn-outline-warning mt-2 d-block w-100 fw-bold btn-extend-guest" data-passenger-id="${passengerId}" data-split-id="${split.id}"><i class="bi bi-hourglass-split me-1"></i>Gia hạn</button>`;
        }

        if (['ON_TIME', 'OVERDUE', 'UNREACHABLE'].includes(split.status)) {
            returnBtnHtml = `<button type="button" class="btn btn-sm btn-outline-success mt-2 d-block w-100 fw-bold btn-return-guest" data-passenger-id="${passengerId}" data-split-id="${split.id}"><i class="bi bi-person-check-fill me-1"></i>Khách đã quay lại</button>`;
        }

        if (split.extensions && split.extensions.length > 0) {
            let listHtml = split.extensions.map(e => `
                <div class="small border-bottom border-secondary pb-1 mb-1 border-opacity-25">
                    <div><span class="text-muted"><i class="bi bi-arrow-right-short"></i></span> ${e.old_end_time} <i class="bi bi-arrow-right text-warning mx-1"></i> <strong>${e.new_end_time}</strong></div>
                    <div class="text-muted" style="font-size: 0.75rem;">Lý do: ${e.extend_reason}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">HDV xác nhận: ${e.confirmed_by_guide_name}</div>
                </div>
            `).join('');
            historyHtml = `
                <div class="mt-2 text-start">
                    <a class="text-decoration-none text-muted small collapsed d-inline-block fw-semibold" data-bs-toggle="collapse" href="#history-${passengerId}">
                        <i class="bi bi-clock-history"></i> Lịch sử gia hạn (${split.extensions.length}) <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse mt-2 bg-light p-2 rounded" id="history-${passengerId}">
                        ${listHtml}
                    </div>
                </div>
            `;
        }

        return badgeHtml + historyHtml + extendBtnHtml + returnBtnHtml;
    }

    function startCountdown(passengerId, endTimeStr, splitStatus) {
        if (countdownIntervals[passengerId]) clearInterval(countdownIntervals[passengerId]);
        
        const endTime = new Date(endTimeStr).getTime();
        
        countdownIntervals[passengerId] = setInterval(() => {
            const el = document.getElementById(`countdown-${passengerId}`);
            if (!el) {
                clearInterval(countdownIntervals[passengerId]);
                return;
            }

            const now = new Date().getTime();
            const distance = endTime - now;
            const isLate = distance < 0;
            const absDistance = Math.abs(distance);
            
            const hours = Math.floor((absDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((absDistance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((absDistance % (1000 * 60)) / 1000);
            
            let timeStr = "";
            if (hours > 0) timeStr += hours + "h ";
            timeStr += minutes + "m " + seconds + "s";
            
            if (isLate) {
                if (splitStatus === 'ON_TIME') {
                    el.innerHTML = "Đã quá giờ, chờ cập nhật...";
                } else {
                    el.innerHTML = "Trễ " + timeStr;
                }
            } else {
                el.innerHTML = timeStr;
            }
        }, 1000);
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

        if (activeSplit) {
            badgeContainer.innerHTML = renderSplitBadge(activeSplit, passengerId);
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
                fetch(`/api/group-splits?stop_id=${currentActivityId}&per_page=100`, {
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
            }, 30000);
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
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">Xác nhận bởi: <br>1</div>
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

                fetch(`/api/group-splits/${splitId}/extend`, {
                    method: 'PATCH',
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
                .then(res => res.json())
                .then(data => {
                    btnSaveExtend.disabled = false;
                    btnSaveExtend.innerHTML = originalHtml;

                    if (data.errors || data.message === 'The given data was invalid.') {
                        showToast('Dữ liệu không hợp lệ, vui lòng kiểm tra lại!', 'danger');
                        return;
                    }

                    const p = passengersData.find(x => x.id == passengerId);
                    if (p) {
                        p.active_split = data;
                        document.getElementById(`extend-row-${passengerId}`).classList.add('d-none');
                        // Update split id on the form buttons dynamically for future
                        const nextBtn = document.getElementById(`save-extend-btn-${passengerId}`);
                        if (nextBtn) nextBtn.setAttribute('data-split-id', data.id);
                    }
                    
                    updatePassengerRowUI(passengerId);
                    showToast('Đã gia hạn thành công!', 'success');
                })
                .catch(err => {
                    console.error(err);
                    btnSaveExtend.disabled = false;
                    btnSaveExtend.innerHTML = originalHtml;
                    showToast('Không thể gia hạn.', 'danger');
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

                fetch(`/api/group-splits/${splitId}/return`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const p = passengersData.find(x => x.id == passengerId);
                    if (p) p.active_split = null;
                    
                    if (p && !p.activity_checkins.includes(parseInt(currentActivityId))) {
                        p.activity_checkins.push(parseInt(currentActivityId));
                        // Automatically check them in on backend too
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

                fetch(`/api/group-splits`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tour_id: 1,
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
                .then(res => res.json())
                .then(data => {
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalHtml;

                    if (data.errors || data.message === 'The given data was invalid.') {
                        showToast('Dữ liệu không hợp lệ, vui lòng kiểm tra lại!', 'danger');
                        return;
                    }

                    const p = passengersData.find(x => x.id == passengerId);
                    if (p) {
                        p.active_split = data;
                        // Dynamically update extend button split ID in the DOM
                        const nextBtn = document.getElementById(`save-extend-btn-${passengerId}`);
                        if (nextBtn) nextBtn.setAttribute('data-split-id', data.id);
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
                    showToast('Không thể lưu thông tin tách đoàn.', 'danger');
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
                    
                    if (data.checked_in) {
                        tr.className = 'table-success';
                        const pData = passengersData.find(p => p.id == passengerId);
                        if(pData && !pData.activity_checkins.includes(parseInt(currentActivityId))) {
                            pData.activity_checkins.push(parseInt(currentActivityId));
                        }
                    } else {
                        tr.className = '';
                        const pData = passengersData.find(p => p.id == passengerId);
                        if(pData) {
                            pData.activity_checkins = pData.activity_checkins.filter(id => id !== parseInt(currentActivityId));
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    checkbox.disabled = false;
                    checkbox.checked = !isChecked;
                    showToast('Có lỗi xảy ra, vui lòng thử lại!', 'danger');
                });
            }
        });

        // 4. Chọn tất cả điểm danh
        const btnCheckinAll = document.getElementById('btn-checkin-all-activity');
        if (btnCheckinAll) {
            btnCheckinAll.addEventListener('click', function() {
                if (!currentActivityId) return;
                if (isLocked) return;
                
                btnCheckinAll.disabled = true;
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
                    btnCheckinAll.disabled = false;
                    btnCheckinAll.innerHTML = originalHtml;

                    if (data.success) {
                        const checkedIds = data.checked_in_ids || [];

                        // Cập nhật lại UI và Data
                        passengersData.forEach(p => {
                            if (checkedIds.includes(p.id)) {
                                if (!p.activity_checkins.includes(parseInt(currentActivityId))) {
                                    p.activity_checkins.push(parseInt(currentActivityId));
                                }

                                const tr = document.getElementById(`rollcall-row-${p.id}`);
                                if (tr && !p.is_free_time) {
                                    tr.className = 'table-success';
                                    const checkbox = tr.querySelector('.activity-passenger-checkbox');
                                    if (checkbox) checkbox.checked = true;
                                }
                            }
                        });

                        showToast(data.message, 'success');
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnCheckinAll.disabled = false;
                    btnCheckinAll.innerHTML = originalHtml;
                    showToast('Có lỗi xảy ra khi điểm danh tất cả.', 'danger');
                });
            });
        }
    }
});
