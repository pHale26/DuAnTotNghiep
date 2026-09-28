@extends('layouts.master')

@section('title', 'Lịch Khởi Hành Tour - Travel Wonder')

@section('content')
<style>
/* ====== CALENDAR BASE ====== */
.cal-wrap { padding-top: 90px; min-height: 100vh; background: #f1f5f9; }

/* Past day cell - ô giữ nguyên, chỉ thẻ tour bên trong mờ nhẹ */
.cal-cell.cal-past {
    background: #fff;
    cursor: pointer;
}
.cal-cell.cal-past .cal-past-tour {
    opacity: 0.82;
    border-left-color: #cbd5e1 !important;
    background: #f8fafc !important;
}

/* ====== FILTERS BAR ====== */
.cal-filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
    align-items: stretch;
}
.cal-filter-group {
    position: relative;
    flex: 1 1 160px;
    min-width: 0;
}
.cal-filter-group i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    left: 12px;
    color: #64748b;
    pointer-events: none;
    z-index: 1;
    font-size: 0.85rem;
}
.cal-filter-group select,
.cal-filter-group input[type="text"] {
    width: 100%;
    padding: 9px 10px 9px 36px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-weight: 500;
    color: #1e3a5f;
    background: #fff;
    font-size: 0.85rem;
    outline: none;
    transition: border-color 0.2s;
    cursor: pointer;
    -webkit-appearance: none;
    appearance: none;
    height: 40px;
}
.cal-filter-group select:focus,
.cal-filter-group input[type="text"]:focus { border-color: #007CE8; }
.cal-btn-reset {
    flex: 0 0 auto;
    padding: 0 16px;
    height: 40px;
    border-radius: 10px;
    background: #e2e8f0;
    color: #475569;
    border: none;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
.cal-btn-reset:hover { background: #94a3b8; color: #fff; }
.cal-result-count {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #007CE8;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 20px;
    padding: 0 10px;
    height: 40px;
    white-space: nowrap;
}

/* ====== CONTROLS ROW ====== */
.cal-controls-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 14px;
}
.cal-legend-pills { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
.cal-legend-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all 0.2s;
    user-select: none;
    white-space: nowrap;
}
.cal-lp-available { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.cal-lp-available.cal-active,
.cal-lp-available:hover { background: #15803d; color: #fff; border-color: #15803d; }
.cal-lp-low      { background: #fffbeb; color: #b45309; border-color: #fde68a; }
.cal-lp-low.cal-active,
.cal-lp-low:hover { background: #b45309; color: #fff; border-color: #b45309; }
.cal-lp-full     { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
.cal-lp-full.cal-active,
.cal-lp-full:hover { background: #b91c1c; color: #fff; border-color: #b91c1c; }
.cal-lp-holiday  { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
.cal-lp-holiday.cal-active,
.cal-lp-holiday:hover { background: #c2410c; color: #fff; border-color: #c2410c; }
.cal-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #64748b;
    transition: all 0.2s;
    user-select: none;
    white-space: nowrap;
}
.cal-toggle-btn.cal-on { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }

/* Controls right side */
.cal-right-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.cal-view-switcher {
    display: flex;
    gap: 3px;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 10px;
}
.cal-view-btn {
    padding: 5px 12px;
    border-radius: 7px;
    font-size: 0.8rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    color: #64748b;
    background: transparent;
    transition: all 0.2s;
    white-space: nowrap;
}
.cal-view-btn.cal-active { background: #fff; color: #007CE8; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
.cal-month-nav { display: flex; align-items: center; gap: 6px; }
.cal-nav-btn {
    width: 32px; height: 32px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #1e3a5f;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
}
.cal-nav-btn:hover { background: #007CE8; color: #fff; border-color: #007CE8; }
#month-display {
    font-weight: 800;
    font-size: 0.95rem;
    color: #1e3a5f;
    min-width: 120px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.cal-today-btn {
    padding: 5px 14px;
    height: 32px;
    background: #007CE8;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex; align-items: center; gap: 4px;
    white-space: nowrap;
}
.cal-today-btn:hover { background: #005bb5; }

/* ====== CALENDAR GRID ====== */
#calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 2px;
    background: #94a3b8;
    border: 2px solid #94a3b8;
    border-radius: 16px;
    overflow: auto;
    max-height: 65vh;
    scroll-behavior: smooth;
    transition: opacity 0.25s;
}
.cal-header-cell {
    text-align: center;
    font-weight: 800;
    color: #475569;
    padding: 10px 4px;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: #f1f5f9;
    position: sticky;
    top: 0;
    z-index: 10;
}
.cal-header-cell.cal-sun { color: #ef4444; }
.cal-header-cell.cal-sat { color: #007CE8; }

.cal-cell {
    background: #fff;
    padding: 8px 7px 6px;
    display: flex;
    flex-direction: column;
    min-height: 140px;
    border-top: 3px solid #e2e8f0;
    transition: background 0.15s;
    overflow: hidden;
}
.cal-cell.cal-muted  { background: #f8fafc; }
.cal-cell.cal-today  { border-top-color: #007CE8; }
.cal-cell.cal-holiday { background: #fff8f8; border-top-color: #fca5a5; }
.cal-cell.cal-holiday-dimmed {
    opacity: 0.82;
}
.cal-cell.cal-holiday-highlight {
    background: #fff5f5 !important;
    border: 2px solid #ef4444 !important;
    border-top: 3px solid #ef4444 !important;
    box-shadow: 0 0 10px rgba(239, 68, 68, 0.15) inset;
    z-index: 5;
}
.cal-cell.cal-has-tour { cursor: pointer; }
.cal-cell.cal-has-tour:hover { background: #f0f7ff; }
.cal-cell.cal-holiday.cal-has-tour:hover { background: #fff0f0; }

/* Tour item inside cell */
.cal-tour-item {
    display: flex;
    flex-direction: column;
    padding: 3px 5px;
    border-radius: 6px;
    margin-bottom: 3px;
    transition: background 0.15s;
    overflow: hidden;
}
.cal-tour-item:hover { background: #eff6ff; }
.cal-tour-item.cal-full { opacity: 0.4; }
.cal-tour-name {
    font-size: 0.68rem;
    font-weight: 700;
    color: #1e3a5f;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cal-tour-price {
    font-size: 0.62rem;
    font-weight: 800;
    color: #007CE8;
    margin-top: 1px;
}

/* Seat badge */
.cal-seat-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.56rem;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
    margin-bottom: 2px;
    align-self: flex-start;
}
.cal-sb-green { background: #dcfce7; color: #15803d; }
.cal-sb-amber { background: #fef3c7; color: #b45309; }
.cal-sb-red   { background: #fee2e2; color: #b91c1c; }

/* More tours button with tooltip */
.cal-more-btn {
    font-size: 0.65rem;
    color: #007CE8;
    font-weight: 800;
    padding: 2px 5px;
    border-radius: 5px;
    cursor: pointer;
    position: relative;
    display: inline-block;
    transition: background 0.15s;
    margin-top: 2px;
}
.cal-more-btn:hover { background: #eff6ff; }
.cal-more-tooltip {
    display: none;
    position: absolute;
    left: 0;
    top: calc(100% + 4px);
    background: #1e293b;
    color: #fff;
    border-radius: 10px;
    padding: 8px 10px;
    width: 210px;
    z-index: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    font-size: 0.7rem;
    font-weight: 600;
    line-height: 1.7;
    pointer-events: none;
}
.cal-more-btn:hover .cal-more-tooltip { display: block; }

/* ====== AGENDA VIEW ====== */
.agenda-day-block {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 10px;
}
.agenda-day-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.agenda-day-num {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: #007CE8; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1.05rem;
    flex-shrink: 0;
}
.agenda-day-num.cal-today-num { background: #1e3a5f; }
.agenda-tour-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    transition: background 0.15s;
    color: inherit;
}
.agenda-tour-row:last-child { border-bottom: none; }
.agenda-tour-row:hover { background: #f0f7ff; }
.agenda-tour-row.cal-full-row { opacity: 0.5; pointer-events: none; }
.agenda-tour-row.cal-past-row {
    opacity: 0.82;
    pointer-events: none;
    cursor: default;
}
.agenda-day-block.cal-holiday-highlight {
    border: 2px solid #ef4444 !important;
    box-shadow: 0 0 12px rgba(239, 68, 68, 0.12);
}
.agenda-day-block.cal-holiday-dimmed {
    opacity: 0.85;
}
.agenda-tour-img {
    width: 50px; height: 50px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
}
.agenda-tour-title { font-size: 0.85rem; font-weight: 700; color: #1e3a5f; margin-bottom: 2px; line-height: 1.3; }
.agenda-tour-meta  { font-size: 0.72rem; color: #64748b; }
.agenda-price-box  { margin-left: auto; text-align: right; flex-shrink: 0; }
.agenda-price-val  { font-size: 0.88rem; font-weight: 800; color: #007CE8; white-space: nowrap; }
.agenda-price-lbl  { font-size: 0.65rem; color: #94a3b8; }

/* ====== RESPONSIVE ====== */
@media (max-width: 767px) {
    #calendar-grid { display: none !important; }
    #calendar-pagination { display: none !important; }
    #cal-agenda-view { display: block !important; }
    .cal-view-switcher { display: none; }
    .cal-filter-group { flex: 1 1 140px; }
    .cal-legend-pill, .cal-toggle-btn { font-size: 0.72rem; padding: 4px 9px; }
}
@media (min-width: 768px) {
    #cal-agenda-view { display: none; }
}
</style>

<div class="cal-wrap">

    {{-- Hero --}}
    <section style="background:linear-gradient(135deg,#007CE8 0%,#005bb5 100%);padding:48px 0 60px;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3" style="font-size:0.9rem;opacity:0.8;">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('frontend.tours.index') }}" class="text-white text-decoration-none">Tour trọn gói</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Lịch khởi hành</li>
                </ol>
            </nav>
            <h1 class="text-white mb-2" style="font-size:2.2rem;font-weight:800;">
                <i class="bi bi-calendar2-week me-3"></i>Lịch Khởi Hành Tour
            </h1>
            <p class="text-white mb-0" style="opacity:0.85;font-size:1.05rem;">
                Tra cứu lịch trình khởi hành của tất cả các tour một cách nhanh chóng và tiện lợi.
            </p>
        </div>
    </section>

    {{-- Main --}}
    <div class="container" style="margin-top:-30px;padding-bottom:60px;">
        <div class="card border-0 shadow-sm" style="border-radius:20px;overflow:hidden;">
            <div class="card-body p-3 p-md-4">

                {{-- ── FILTER BAR ── --}}
                <div class="cal-filter-bar">

                    {{-- Search --}}
                    <div class="cal-filter-group" style="flex:2 1 200px;">
                        <i class="bi bi-search"></i>
                        <input type="text" id="filter-search" placeholder="Tìm tên tour, điểm đến..." oninput="applyLocalFilter()">
                    </div>

                    {{-- Destination --}}
                    <div class="cal-filter-group">
                        <i class="bi bi-geo-alt-fill"></i>
                        <select id="filter-dest" onchange="fetchCalendarData()">
                            <option value="">Tất cả điểm đến</option>
                            @foreach($destinations as $dest)
                                <option value="{{ $dest->id }}">{{ $dest->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category --}}
                    <div class="cal-filter-group">
                        <i class="bi bi-compass-fill"></i>
                        <select id="filter-category" onchange="fetchCalendarData()">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Budget --}}
                    <div class="cal-filter-group">
                        <i class="bi bi-tags-fill"></i>
                        <select id="filter-budget" onchange="fetchCalendarData()">
                            <option value="">Tất cả mức giá</option>
                            <option value="under_1m">Dưới 1 triệu</option>
                            <option value="1m_2m">1 - 2 triệu</option>
                            <option value="2m_4m">2 - 4 triệu</option>
                            <option value="over_4m">Trên 4 triệu</option>
                        </select>
                    </div>

                    {{-- Duration --}}
                    <div class="cal-filter-group">
                        <i class="bi bi-clock-fill"></i>
                        <select id="filter-duration" onchange="fetchCalendarData()">
                            <option value="">Tất cả thời gian</option>
                            <option value="2d1n">2N1Đ</option>
                            <option value="3d2n">3N2Đ</option>
                            <option value="4d3n">4N3Đ</option>
                            <option value="5d4n">5N4Đ</option>
                            <option value="6d5n">6N5Đ</option>
                            <option value="7d6n">7N6Đ</option>
                        </select>
                    </div>

                    {{-- Reset --}}
                    <button class="cal-btn-reset" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise"></i> Làm mới
                    </button>
                </div>

                {{-- ── CONTROLS ROW ── --}}
                <div class="cal-controls-row">

                    {{-- Legend clickable pills (bỏ "Hết chỗ" khỏi bộ lọc vì đã làm mờ trong ô) --}}
                    <div class="cal-legend-pills">
                        <span class="cal-legend-pill cal-lp-available" data-filter="available" onclick="toggleLegendFilter(this)">
                            <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;flex-shrink:0;"></span> Còn chỗ
                        </span>
                        <span class="cal-legend-pill cal-lp-low" data-filter="low" onclick="toggleLegendFilter(this)">
                            <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;flex-shrink:0;"></span> Sắp hết
                        </span>
                        <span class="cal-legend-pill cal-lp-holiday" data-filter="holiday" onclick="toggleLegendFilter(this)">
                            <span style="width:9px;height:9px;border-radius:50%;border:1.5px solid currentColor;display:inline-block;flex-shrink:0;"></span> Ngày lễ
                        </span>
                    </div>

                    {{-- Right: view switcher + today + month nav --}}
                    <div class="cal-right-controls">

                        <div class="cal-view-switcher">
                            <button class="cal-view-btn cal-active" id="btn-month" onclick="setView('month')">
                                <i class="bi bi-calendar3"></i> Tháng
                            </button>
                            <button class="cal-view-btn" id="btn-week" onclick="setView('week')">
                                <i class="bi bi-calendar-week"></i> Tuần
                            </button>
                            <button class="cal-view-btn" id="btn-agenda" onclick="setView('agenda')">
                                <i class="bi bi-list-ul"></i> Danh sách
                            </button>
                        </div>

                        <button class="cal-today-btn" onclick="goToToday()">
                            <i class="bi bi-calendar-check"></i> Hôm nay
                        </button>

                        <div class="cal-month-nav">
                            <button class="cal-nav-btn" onclick="prevMonth()"><i class="bi bi-chevron-left"></i></button>
                            <div id="month-display"></div>
                            <button class="cal-nav-btn" onclick="nextMonth()"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                </div>


                {{-- Today Tours Panel --}}
                <div id="today-panel" style="display:none;margin-bottom:16px;">
                    <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.09);overflow:hidden;">
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:linear-gradient(135deg,#007CE8 0%,#0ea5e9 100%);">
                            <div>
                                <div style="color:#fff;font-weight:800;font-size:1rem;"><i class="bi bi-calendar-check"></i> Tour khởi hành hôm nay</div>
                                <div id="today-date-label" style="color:rgba(255,255,255,0.82);font-size:0.8rem;margin-top:2px;"></div>
                            </div>
                            <button onclick="closeTodayPanel()" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>
                        </div>
                        <div id="today-panel-body" style="padding:14px 16px;display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:10px;max-height:480px;overflow-y:auto;"></div>
                    </div>
                </div>

                {{-- Loading --}}
                <div id="calendar-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status" style="width:40px;height:40px;"></div>
                    <p class="mt-3 text-muted">Đang tải lịch...</p>
                </div>

                {{-- Static Notice --}}
                <div style="background:#e0f2fe;border:1px solid #bae6fd;color:#0369a1;padding:12px 18px;border-radius:12px;font-size:0.85rem;font-weight:600;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-info-circle-fill" style="font-size:1.1rem;"></i>
                    <span><strong>Lưu ý:</strong> Quý khách vui lòng đặt tour trước 3 ngày so với ngày khởi hành. Các tour khởi hành trong vòng 3 ngày tới thuộc trạng thái cận ngày (không nhận đăng ký trực tuyến).</span>
                </div>

                {{-- Notice Banner --}}
                <div id="calendar-notice" class="d-none" style="background:#fffbeb;border:1px solid #fde047;color:#b45309;padding:12px 18px;border-radius:12px;font-size:0.85rem;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.1rem;color:#d97706;"></i>
                    <span id="calendar-notice-text"></span>
                </div>

                {{-- Calendar Grid (desktop) --}}
                <div id="calendar-grid"></div>

                {{-- Agenda view (mobile auto / desktop on demand) --}}
                <div id="cal-agenda-view"></div>

                {{-- Scroll button --}}
                <div id="calendar-pagination" class="text-center mt-3 d-none">
                    <button onclick="scrollCalendar()" id="scroll-btn"
                        style="background:#fff;border:1.5px solid #cbd5e1;color:#007CE8;padding:8px 24px;border-radius:50px;font-weight:700;font-size:0.85rem;cursor:pointer;transition:all 0.2s;">
                        <i class="bi bi-chevron-down"></i> Xem các tuần tiếp theo
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="tour-modal"
    style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.75);display:none;justify-content:center;align-items:center;z-index:9999;backdrop-filter:blur(8px);opacity:0;transition:opacity 0.3s;"
    onclick="closeModal(event)">
    <div id="modal-inner" onclick="event.stopPropagation()"
        style="background:#f8fafc;width:90%;max-width:760px;border-radius:24px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);transform:translateY(30px);transition:transform 0.3s cubic-bezier(0.16,1,0.3,1);overflow:hidden;">
        <div style="padding:18px 22px;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;gap:10px;">
            <h3 id="modal-title" style="margin:0;font-size:1.05rem;font-weight:800;color:#1e3a5f;display:flex;align-items:center;gap:7px;flex-wrap:wrap;min-width:0;"></h3>
            <button onclick="closeModal()"
                style="background:#f1f5f9;border:none;width:34px;height:34px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;transition:all 0.2s;flex-shrink:0;"
                onmouseover="this.style.background='#fee2e2';this.style.color='#ef4444';"
                onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b';">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="modal-body" style="padding:16px 20px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── STATE ──────────────────────────────────────────────────
const CAL_TODAY    = new Date();
let currentYear    = CAL_TODAY.getFullYear();
let currentMonth   = CAL_TODAY.getMonth() + 1;
let currentView    = 'month';
let activeLFilter  = null;
let allToursData   = {};
let holidayMap     = {};

const todayStr = [
    CAL_TODAY.getFullYear(),
    String(CAL_TODAY.getMonth()+1).padStart(2,'0'),
    String(CAL_TODAY.getDate()).padStart(2,'0')
].join('-');

const BOOKING_LIMIT_DATE = new Date(CAL_TODAY);
BOOKING_LIMIT_DATE.setDate(BOOKING_LIMIT_DATE.getDate() + 3);
const bookingLimitStr = [
    BOOKING_LIMIT_DATE.getFullYear(),
    String(BOOKING_LIMIT_DATE.getMonth()+1).padStart(2,'0'),
    String(BOOKING_LIMIT_DATE.getDate()).padStart(2,'0')
].join('-');

const API_URL    = "{{ route('frontend.tours.calendar.data') }}";
const WEEKNAMES  = ['CN','Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7'];
const FALLBACK   = 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=800&auto=format&fit=crop';

// ── HELPERS ────────────────────────────────────────────────
const fmt = p => new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(p);
const fmtShort = p => {
    if (p >= 1e9) return (p/1e9).toFixed(1) + 'tỷ';
    if (p >= 1e6) return (p%1e6===0 ? p/1e6 : (p/1e6).toFixed(1)) + 'tr';
    if (p >= 1e3) return Math.round(p/1e3) + 'k';
    return p;
};

const getSeatInfo = (seats, status) => {
    if (status === 'full' || seats === 0)
        return { cls:'cal-sb-red',   badge:'Hết chỗ',              cat:'full' };
    if (seats <= 5)
        return { cls:'cal-sb-amber', badge:`Sắp hết · ${seats} chỗ`, cat:'low' };
    return     { cls:'cal-sb-green', badge:`Còn ${seats} chỗ`,       cat:'available' };
};

const isMobile = () => window.innerWidth < 768;

// ── FILTER HELPERS ─────────────────────────────────────────
function updateHeader() {
    document.getElementById('month-display').textContent =
        `Tháng ${currentMonth} / ${currentYear}`;
}

function setView(v) {
    currentView = v;
    
    // Đóng panel Hôm nay và chuyển trạng thái
    const panel = document.getElementById('today-panel');
    if (panel) panel.style.display = 'none';
    todayPanelOpen = false;

    // Trả style nút Hôm nay về bình thường
    const todayBtn = document.querySelector('.cal-today-btn');
    if (todayBtn) { todayBtn.style.background = ''; todayBtn.style.color = ''; }

    // Hiện lại nút lọc Ngày lễ
    const holidayPill = document.querySelector('.cal-lp-holiday');
    if (holidayPill) holidayPill.style.display = '';

    ['month','week','agenda'].forEach(k => {
        const el = document.getElementById('btn-'+k);
        if (el) el.classList.toggle('cal-active', k === v);
    });
    
    showCalendar();
}

function toggleLegendFilter(el) {
    const f = el.dataset.filter;
    if (activeLFilter === f) {
        activeLFilter = null;
        el.classList.remove('cal-active');
    } else {
        document.querySelectorAll('.cal-legend-pill').forEach(p => p.classList.remove('cal-active'));
        activeLFilter = f;
        el.classList.add('cal-active');
    }
    renderAll(allToursData);
}



function resetFilters() {
    ['filter-dest','filter-category','filter-budget','filter-duration'].forEach(id => {
        const el = document.getElementById(id); if(el) el.value = '';
    });
    const fs = document.getElementById('filter-search'); if(fs) fs.value = '';
    activeLFilter = null;
    document.querySelectorAll('.cal-legend-pill').forEach(p => p.classList.remove('cal-active'));
    fetchCalendarData();
}

function applyLocalFilter() { renderAll(allToursData); }

function getFilteredTours(tours) {
    const search = (document.getElementById('filter-search')?.value || '').toLowerCase().trim();
    return tours.filter(t => {
        if (t.status === 'full' || t.available_seats === 0) return false;
        if (search && !t.tour_name.toLowerCase().includes(search) && !(t.destination||'').toLowerCase().includes(search)) return false;
        if (activeLFilter) {
            if (activeLFilter === 'holiday') return true; // Giữ nguyên các tour khi đang lọc ngày lễ
            const info = getSeatInfo(t.available_seats, t.status);
            if (activeLFilter !== info.cat) return false;
        }
        return true;
    });
}

function updateResultCount(total) {
    // Không làm gì - đã xóa hiển thị count
}


// ── FETCH ──────────────────────────────────────────────────
let todayPanelOpen = false;

function prevMonth() {
    closeTodayPanel();
    if(--currentMonth < 1) { currentMonth=12; currentYear--; }
    fetchCalendarData();
}
function nextMonth() {
    closeTodayPanel();
    if(++currentMonth > 12){ currentMonth=1; currentYear++; }
    fetchCalendarData();
}

function goToToday() {
    if (todayPanelOpen) {
        // Toggle: đang xem hôm nay → quay về lịch
        closeTodayPanel();
        return;
    }
    // Mở panel hôm nay, ẩn lịch hoàn toàn
    currentYear  = CAL_TODAY.getFullYear();
    currentMonth = CAL_TODAY.getMonth() + 1;
    updateHeader();

    // Tắt trạng thái active của các nút Tháng, Tuần, Danh sách
    ['month','week','agenda'].forEach(k => {
        const el = document.getElementById('btn-'+k);
        if (el) el.classList.remove('cal-active');
    });

    // Ẩn nút Ngày lễ trên bộ lọc
    const holidayPill = document.querySelector('.cal-lp-holiday');
    if (holidayPill) holidayPill.style.display = 'none';

    // Reset lại bộ lọc Ngày lễ nếu đang active
    if (activeLFilter === 'holiday') {
        activeLFilter = null;
        document.querySelectorAll('.cal-legend-pill').forEach(p => p.classList.remove('cal-active'));
    }

    // Nếu data tháng hiện tại chưa có thì fetch trước
    const hasCurrentMonth = Object.keys(allToursData).some(k => k.startsWith(
        `${currentYear}-${String(currentMonth).padStart(2,'0')}`
    ));
    if (!hasCurrentMonth) {
        fetchCalendarData(true);
    } else {
        hideCalendar();
        openTodayPanel(allToursData);
    }
}

function showCalendar() {
    todayPanelOpen = false;
    const panel = document.getElementById('today-panel');
    if (panel) panel.style.display = 'none';
    
    // Hiện lại nút Ngày lễ
    const holidayPill = document.querySelector('.cal-lp-holiday');
    if (holidayPill) holidayPill.style.display = '';

    renderAll(allToursData);
}

function hideCalendar() {
    document.getElementById('calendar-grid').style.display = 'none';
    document.getElementById('cal-agenda-view').style.display = 'none';
    document.getElementById('calendar-pagination').classList.add('d-none');
}

function fetchCalendarData(openToday = false) {
    updateHeader();
    const grid   = document.getElementById('calendar-grid');
    const loader = document.getElementById('calendar-loading');
    loader.classList.remove('d-none');
    if (!openToday) grid.style.opacity = '0.3';

    const params = new URLSearchParams({ year: currentYear, month: currentMonth });
    const d = document.getElementById('filter-dest').value;
    const c = document.getElementById('filter-category').value;
    const b = document.getElementById('filter-budget').value;
    const r = document.getElementById('filter-duration').value;
    if (d) params.append('destination_id', d);
    if (c) params.append('category_id', c);
    if (b) params.append('budget', b);
    if (r) params.append('duration', r);

    fetch(`${API_URL}?${params}`)
        .then(res => res.json())
        .then(data => {
            holidayMap   = data.holidays || {};
            allToursData = data.tours    || {};
            if (openToday) {
                hideCalendar();
                openTodayPanel(allToursData);
            } else {
                renderAll(allToursData);
            }
        })
        .catch(() => { holidayMap={}; allToursData={}; renderAll({}); })
        .finally(() => { loader.classList.add('d-none'); grid.style.opacity = '1'; });
}

// ── RENDER DISPATCHER ──────────────────────────────────────
function renderAll(data) {
    // Ẩn thông báo notice mặc định
    const noticeEl = document.getElementById('calendar-notice');
    if (noticeEl) noticeEl.classList.add('d-none');

    if (todayPanelOpen) {
        hideCalendar();
        openTodayPanel(data);
        return;
    }

    const forcedAgenda = isMobile() || currentView === 'agenda';
    const isWeek       = !forcedAgenda && currentView === 'week';
    const grid         = document.getElementById('calendar-grid');
    const agendaEl     = document.getElementById('cal-agenda-view');

    // Count
    let total = 0;
    Object.values(data).forEach(arr => { total += getFilteredTours(arr).length; });
    updateResultCount(total);

    if (forcedAgenda) {
        grid.style.display     = 'none';
        agendaEl.style.display = 'block';
        document.getElementById('calendar-pagination').classList.add('d-none');
        renderAgenda(data);
    } else if (isWeek) {
        agendaEl.style.display = 'none';
        grid.style.display     = 'grid';
        renderWeek(data);
    } else {
        agendaEl.style.display = 'none';
        grid.style.display     = 'grid';
        renderMonth(data);
    }

    if (activeLFilter === 'holiday') {
        scrollToFirstHoliday();
    }
}

function scrollToFirstHoliday() {
    setTimeout(() => {
        const forcedAgenda = isMobile() || currentView === 'agenda';
        if (forcedAgenda) {
            const agendaEl = document.getElementById('cal-agenda-view');
            const firstHol = agendaEl.querySelector('.cal-holiday-day');
            if (firstHol) {
                firstHol.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            const grid = document.getElementById('calendar-grid');
            const firstHol = grid.querySelector('.cal-holiday');
            if (firstHol) {
                firstHol.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }, 120);
}

// ── MONTH VIEW ─────────────────────────────────────────────
function renderMonth(data) {
    const grid = document.getElementById('calendar-grid');
    grid.style.gridTemplateColumns = 'repeat(7, minmax(0, 1fr))';
    grid.innerHTML = '';

    WEEKNAMES.forEach((wd, i) => {
        const hd = document.createElement('div');
        hd.className = 'cal-header-cell' + (i===0?' cal-sun':i===6?' cal-sat':'');
        hd.textContent = wd;
        grid.appendChild(hd);
    });

    const firstDay    = new Date(currentYear, currentMonth-1, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth,   0).getDate();
    const prevDays    = new Date(currentYear, currentMonth-1, 0).getDate();
    const cells = [];

    for (let i=0; i<firstDay; i++)
        cells.push({ day: prevDays - firstDay + i + 1, type: 'muted' });
    for (let d=1; d<=daysInMonth; d++) {
        const ds = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        cells.push({ day: d, type: 'current', dateStr: ds });
    }
    let nx = 1;
    while (cells.length < 42) cells.push({ day: nx++, type: 'muted' });

    cells.forEach(cell => grid.appendChild(buildCell(cell, data)));
    setTimeout(checkCalendarScroll, 60);
}

// ── WEEK VIEW ──────────────────────────────────────────────
function renderWeek(data) {
    const grid = document.getElementById('calendar-grid');
    grid.style.gridTemplateColumns = 'repeat(7, minmax(0, 1fr))';
    grid.innerHTML = '';

    WEEKNAMES.forEach((wd, i) => {
        const hd = document.createElement('div');
        hd.className = 'cal-header-cell' + (i===0?' cal-sun':i===6?' cal-sat':'');
        hd.textContent = wd;
        grid.appendChild(hd);
    });

    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
    const refDay = (CAL_TODAY.getFullYear()===currentYear && CAL_TODAY.getMonth()+1===currentMonth)
        ? CAL_TODAY.getDate() : 1;
    const refDate = new Date(currentYear, currentMonth-1, refDay);
    const weekStartDate = new Date(refDate);
    weekStartDate.setDate(refDate.getDate() - refDate.getDay());

    // 1. Quét kiểm tra xem tuần này có ngày lễ nào không
    let hasHolidayInWeek = false;
    const weekCells = [];

    for (let col=0; col<7; col++) {
        const d = new Date(weekStartDate);
        d.setDate(weekStartDate.getDate() + col);
        if (d.getMonth()+1 === currentMonth) {
            const day = d.getDate();
            const ds  = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const hols = holidayMap[ds] || [];
            if (hols.length > 0) {
                hasHolidayInWeek = true;
            }
            weekCells.push({ day, type:'current', dateStr:ds });
        } else {
            weekCells.push({ type:'muted' });
        }
    }

    // 2. Hiện thông báo nếu đang lọc ngày lễ mà tuần đó không có ngày lễ
    const noticeEl = document.getElementById('calendar-notice');
    const noticeText = document.getElementById('calendar-notice-text');
    if (activeLFilter === 'holiday' && !hasHolidayInWeek) {
        if (noticeEl && noticeText) {
            noticeText.textContent = 'Không có ngày lễ trong tuần này';
            noticeEl.classList.remove('d-none');
        }
    } else {
        if (noticeEl) noticeEl.classList.add('d-none');
    }

    // 3. Render các ô ngày
    weekCells.forEach(cell => {
        if (cell.type === 'muted') {
            const empty = document.createElement('div');
            empty.className = 'cal-cell cal-muted';
            grid.appendChild(empty);
        } else {
            // Nếu đang lọc ngày lễ nhưng tuần đó không có ngày lễ, bỏ hiệu ứng làm mờ (giữ nguyên)
            const ignoreDimming = (activeLFilter === 'holiday' && !hasHolidayInWeek);
            grid.appendChild(buildCell(cell, data, ignoreDimming));
        }
    });

    document.getElementById('calendar-pagination').classList.add('d-none');
}

// ── BUILD CELL ─────────────────────────────────────────────
function buildCell(cell, data, ignoreHolidayDimming = false) {
    const el = document.createElement('div');

    if (cell.type === 'muted') {
        el.className = 'cal-cell cal-muted';
        el.innerHTML = `<span style="font-size:0.88rem;font-weight:700;color:#94a3b8;">${cell.day}</span>`;
        return el;
    }

    const rawTours  = data[cell.dateStr] || [];
    const tours     = getFilteredTours(rawTours);
    const isToday   = cell.dateStr === todayStr;
    const isPast    = cell.dateStr < todayStr;
    const holidays  = holidayMap[cell.dateStr] || [];
    const isHoliday = holidays.length > 0;
    const hasTour   = tours.length > 0;

    let cls = 'cal-cell';
    if (isPast)    cls += ' cal-past';
    if (isToday)   cls += ' cal-today';
    if (isHoliday) cls += ' cal-holiday';
    if (hasTour)   cls += ' cal-has-tour';

    if (activeLFilter === 'holiday' && !ignoreHolidayDimming) {
        if (isHoliday) cls += ' cal-holiday-highlight';
        else cls += ' cal-holiday-dimmed';
    }
    el.className = cls;

    if (hasTour) {
        el.onclick = () => openModal(cell.dateStr, rawTours, holidays, isPast);
    }

    // Day number
    let dayHtml;
    if (isToday && isHoliday) {
        dayHtml = `<span style="background:#007CE8;color:#fff;border-radius:7px;padding:1px 7px;font-weight:800;outline:2px solid #fca5a5;outline-offset:2px;display:inline-block;">${cell.day}</span>`;
    } else if (isToday) {
        dayHtml = `<span style="background:#007CE8;color:#fff;border-radius:7px;padding:1px 7px;font-weight:800;display:inline-block;">${cell.day}</span>`;
    } else if (isHoliday) {
        dayHtml = `<span style="border:2px solid #ef4444;color:#ef4444;border-radius:50%;width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:0.82rem;">${cell.day}</span>`;
    } else {
        dayHtml = `<span style="font-size:0.9rem;font-weight:800;color:#1e3a5f;">${cell.day}</span>`;
    }

    let holTag = '';
    if (isHoliday) {
        holTag = `<span style="font-size:0.58rem;font-weight:700;color:#ef4444;background:#fee2e2;padding:1px 4px;border-radius:8px;margin-left:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:65px;" title="${holidays.join(', ')}">${holidays[0]}</span>`;
    }

    let html = `
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
            <div style="display:flex;align-items:center;gap:2px;min-width:0;flex:1;">${dayHtml}${holTag}</div>
            ${rawTours.length > 0 ? `<span style="font-size:0.58rem;background:#dbeafe;color:#1d4ed8;padding:1px 5px;border-radius:20px;font-weight:800;flex-shrink:0;">${rawTours.length}</span>` : ''}
        </div>`;

    if (hasTour) {
        const visible = tours.slice(0, 3);
        const hidden  = tours.slice(3);
        html += `<div style="display:flex;flex-direction:column;flex:1;gap:3px;">`;
        visible.forEach(t => {
            const info   = getSeatInfo(t.available_seats, t.status);
            const isFull = info.cat === 'full';
            const pastClass = isPast ? 'cal-past-tour' : '';
            html += `
                <div class="${pastClass}" style="
                    padding: 3px 6px;
                    border-radius: 7px;
                    background: ${isFull ? '#f8fafc' : info.cat==='low' ? '#fffbeb' : '#f0f7ff'};
                    opacity: ${isFull ? '0.45' : '1'};
                    border-left: 3px solid ${isFull ? '#cbd5e1' : info.cat==='low' ? '#f59e0b' : '#007CE8'};
                    overflow: hidden;
                    cursor: ${isFull ? 'default' : 'pointer'};
                ">
                    <div style="font-size:0.72rem;font-weight:800;color:#1e3a5f;line-height:1.35;white-space:normal;word-break:break-word;">${t.tour_name}</div>
                </div>`;
        });
        if (hidden.length > 0) {
            const tipLines = hidden.map(t => `• ${t.tour_name}`).join('<br>');
            html += `
                <div class="cal-more-btn"
                    onclick="event.stopPropagation();openModal('${cell.dateStr}',${JSON.stringify(rawTours)},${JSON.stringify(holidays)},${isPast})">
                    +${hidden.length} tour khác
                    <div class="cal-more-tooltip">${tipLines}</div>
                </div>`;
        }
        html += `</div>`;
    } else if (!isHoliday) {
        html += `<div style="flex:1;display:flex;align-items:center;justify-content:center;color:#e2e8f0;font-size:1.1rem;">—</div>`;
    }

    el.innerHTML = html;
    return el;
}

// ── AGENDA VIEW ────────────────────────────────────────────
function renderAgenda(data) {
    const container = document.getElementById('cal-agenda-view');
    container.innerHTML = '';

    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
    let totalShown = 0;

    for (let d=1; d<=daysInMonth; d++) {
        const ds      = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const raw     = data[ds] || [];
        const tours   = getFilteredTours(raw);
        const hols    = holidayMap[ds] || [];
        if (tours.length === 0 && hols.length === 0) continue;

        totalShown += tours.length;
        const date    = new Date(currentYear, currentMonth-1, d);
        const isToday = ds === todayStr;
        const isPast  = ds < todayStr;
        const isTooClose = (!isPast && ds < bookingLimitStr);
        const dayName = WEEKNAMES[date.getDay()];

        const block   = document.createElement('div');
        let blockCls = 'agenda-day-block';
        if (isPast) blockCls += ' cal-past-block';
        if (hols.length > 0) blockCls += ' cal-holiday-day';
        if (activeLFilter === 'holiday') {
            if (hols.length > 0) blockCls += ' cal-holiday-highlight';
            else blockCls += ' cal-holiday-dimmed';
        }
        block.className = blockCls;

        const holBadges = hols.map(h =>
            `<span style="font-size:0.7rem;background:#fee2e2;color:#ef4444;padding:2px 7px;border-radius:6px;font-weight:700;">${h}</span>`
        ).join('');

        block.innerHTML = `
            <div class="agenda-day-header">
                <div class="agenda-day-num ${isToday?'cal-today-num':''}">${d}</div>
                <div>
                    <div style="font-weight:800;color:#1e3a5f;font-size:0.9rem;">${dayName}, ${d}/${currentMonth}</div>
                    <div style="font-size:0.75rem;color:#64748b;">${tours.length} tour ${holBadges}</div>
                </div>
            </div>`;

        tours.forEach(t => {
            const info   = getSeatInfo(t.available_seats, t.status);
            const isFull = info.cat === 'full';
            const row    = document.createElement('a');
            
            const disableClick = isFull || isPast || isTooClose;
            row.href     = disableClick ? '#' : t.tour_url;
            row.className= 'agenda-tour-row' + (isFull?' cal-full-row':'') + (disableClick?' cal-past-row':'');
            row.innerHTML = `
                <img src="${t.image_url||FALLBACK}" alt="${t.tour_name}" class="agenda-tour-img" onerror="this.src='${FALLBACK}'">
                <div style="flex:1;min-width:0;">
                    <div class="agenda-tour-title">${t.tour_name}</div>
                    <div class="agenda-tour-meta"><i class="bi bi-map"></i> ${t.destination||'Việt Nam'} · <i class="bi bi-clock"></i> ${t.duration}</div>
                    <span class="cal-seat-badge ${info.cls}" style="font-size:0.68rem;padding:2px 7px;margin-top:3px;">${info.badge}</span>
                </div>
                <div class="agenda-price-box">
                    <div class="agenda-price-lbl">Giá từ</div>
                    <div class="agenda-price-val">${fmt(t.price)}</div>
                </div>`;
            block.appendChild(row);
        });
        container.appendChild(block);
    }

    if (totalShown === 0) {
        container.innerHTML = `<div style="text-align:center;padding:60px 20px;color:#94a3b8;">
            <div style="font-size:3rem;">🗓️</div>
            <p style="font-weight:600;margin-top:12px;">Không có tour trong tháng này</p>
        </div>`;
    }
    updateResultCount(totalShown);
}

// ── MODAL ──────────────────────────────────────────────────
function openModal(dateStr, tours, holidays, isPastDay = false) {
    const [y,m,d] = dateStr.split('-');
    const filtered = getFilteredTours(tours);
    const display  = filtered.length > 0 ? filtered : tours;

    const holBadge = (holidays||[]).map(h =>
        `<span style="font-size:0.7rem;background:#fee2e2;color:#ef4444;padding:2px 8px;border-radius:6px;font-weight:700;">${h}</span>`
    ).join('');

    const isTooClose = (!isPastDay && dateStr < bookingLimitStr);

    const pastBanner = isPastDay
        ? `<div style="background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:7px 14px;font-size:0.8rem;font-weight:600;color:#854d0e;margin-bottom:4px;"><i class="bi bi-clock-history"></i> Lịch khởi hành đã qua — chỉ xem để tham khảo</div>`
        : isTooClose
        ? `<div style="background:#fff3cd;border:1px solid #ffe69c;border-radius:8px;padding:7px 14px;font-size:0.8rem;font-weight:600;color:#664d03;margin-bottom:4px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <div><i class="bi bi-exclamation-circle"></i> Tour cận ngày — Quý khách vui lòng đặt tour trước 3 ngày</div>
            <a href="/support" style="background:#ca8a04;color:#fff;border:none;padding:5px 12px;border-radius:6px;font-size:0.75rem;font-weight:700;text-decoration:none;white-space:nowrap;transition:all 0.2s;box-shadow:0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#a16207'" onmouseout="this.style.background='#ca8a04'"><i class="bi bi-headset"></i> Hỗ trợ</a>
        </div>`
        : '';

    document.getElementById('modal-title').innerHTML = `
        <i class="bi bi-calendar-event" style="color:${isPastDay?'#94a3b8':'#007CE8'};flex-shrink:0;"></i>
        Khởi hành ${d}/${m}/${y}
        ${holBadge}
        <span style="font-size:0.75rem;font-weight:600;color:#64748b;background:#f1f5f9;padding:2px 9px;border-radius:20px;">${display.length} tour</span>`;

    const body = document.getElementById('modal-body');
    body.innerHTML = pastBanner;

    display.forEach(t => {
        const info   = getSeatInfo(t.available_seats, t.status);
        const isFull = info.cat === 'full';
        const disableBook = isPastDay || isFull || isTooClose;

        const card   = document.createElement('a');
        card.href    = disableBook ? '#' : t.tour_url;
        card.style.cssText = `
            background:#fff;border-radius:14px;display:flex;height:160px;
            overflow:hidden;border:1px solid #e2e8f0;
            box-shadow:0 2px 8px rgba(0,0,0,0.04);
            transition:transform 0.2s,box-shadow 0.2s;flex-shrink:0;
            text-decoration:none;
            color:inherit;
            ${(isPastDay || isTooClose) ? 'filter:grayscale(30%);' : ''}
            ${disableBook ? 'pointer-events:none;cursor:default;' : ''}`;
        if (!disableBook) {
            card.addEventListener('mouseenter', () => { card.style.transform='translateY(-3px)'; card.style.boxShadow='0 10px 24px rgba(0,0,0,0.09)'; });
            card.addEventListener('mouseleave', () => { card.style.transform=''; card.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'; });
        }

        const btnLabel = isPastDay ? '<i class="bi bi-clock-history"></i> Đã qua'
            : isFull ? 'Hết vé'
            : isTooClose ? 'Cận ngày'
            : '<i class="bi bi-arrow-right-circle"></i> Xem tour';
        const btnStyle = disableBook
            ? 'background:#e2e8f0;color:#94a3b8;'
            : 'background:#007CE8;color:#fff;';

        card.innerHTML = `
            <div style="position:relative;width:180px;flex-shrink:0;overflow:hidden;">
                <span style="position:absolute;top:8px;left:8px;background:rgba(255,255,255,0.95);padding:2px 7px;border-radius:5px;font-size:0.68rem;font-weight:800;color:#1e3a5f;z-index:2;">${t.duration}</span>
                <img src="${t.image_url||FALLBACK}" alt="${t.tour_name}" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='${FALLBACK}'">
            </div>
            <div style="padding:12px 16px;flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">
                <h4 style="font-weight:800;font-size:0.92rem;color:#1e3a5f;margin:0 0 3px;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${t.tour_name}</h4>
                <div style="color:#64748b;font-size:0.75rem;margin-bottom:6px;"><i class="bi bi-map"></i> ${t.destination||'Việt Nam'}</div>
                <span class="cal-seat-badge ${info.cls}" style="font-size:0.7rem;padding:2px 8px;align-self:flex-start;">${info.badge}</span>
                <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;margin-top:auto;">
                    <div>
                        <div style="font-size:0.65rem;color:#94a3b8;">Giá từ</div>
                        <div style="color:#007CE8;font-weight:800;font-size:0.95rem;white-space:nowrap;">${fmt(t.price)}</div>
                    </div>
                    <div style="${btnStyle}border:none;padding:7px 12px;border-radius:9px;font-weight:700;font-size:0.78rem;display:inline-flex;align-items:center;gap:4px;white-space:nowrap;flex-shrink:0;">
                        ${btnLabel}
                    </div>
                </div>
            </div>`;
        body.appendChild(card);
    });

    const overlay = document.getElementById('tour-modal');
    overlay.style.display = 'flex';
    requestAnimationFrame(() => {
        overlay.style.opacity = '1';
        document.getElementById('modal-inner').style.transform = 'translateY(0)';
    });
    document.body.style.overflow = 'hidden';
}

function closeModal(e) {
    if (e && e.target !== document.getElementById('tour-modal')) return;
    const overlay = document.getElementById('tour-modal');
    overlay.style.opacity = '0';
    document.getElementById('modal-inner').style.transform = 'translateY(30px)';
    setTimeout(() => { overlay.style.display = 'none'; }, 300);
    document.body.style.overflow = 'auto';
}

// ── SCROLL ─────────────────────────────────────────────────
function checkCalendarScroll() {
    const grid = document.getElementById('calendar-grid');
    const btn  = document.getElementById('calendar-pagination');
    if (!btn || !grid) return;
    if (grid.scrollHeight > grid.clientHeight + 10) {
        btn.classList.remove('d-none'); updateScrollButton();
    } else {
        btn.classList.add('d-none');
    }
}
function updateScrollButton() {
    const grid = document.getElementById('calendar-grid');
    const btn  = document.getElementById('scroll-btn');
    if (!btn || !grid) return;
    if (grid.scrollTop + grid.clientHeight >= grid.scrollHeight - 20) {
        btn.innerHTML = '<i class="bi bi-chevron-up"></i> Quay lại đầu tháng';
    } else {
        btn.innerHTML = '<i class="bi bi-chevron-down"></i> Xem các tuần tiếp theo';
    }
}
function scrollCalendar() {
    const grid = document.getElementById('calendar-grid');
    if (grid.scrollTop + grid.clientHeight >= grid.scrollHeight - 20) {
        grid.scrollTo({ top:0, behavior:'smooth' });
    } else {
        grid.scrollBy({ top: grid.clientHeight - 80, behavior:'smooth' });
    }
}

// ── TODAY PANEL ────────────────────────────────────────────
function openTodayPanel(data) {
    const panel = document.getElementById('today-panel');
    const body  = document.getElementById('today-panel-body');
    const label = document.getElementById('today-date-label');
    if (!panel || !body) return;

    // Lọc các tour du lịch khởi hành hôm nay theo bộ lọc hiện tại
    const rawTours = data[todayStr] || [];
    const tours = getFilteredTours(rawTours);

    // Label ngày
    const days = ['Chủ nhật','Thứ hai','Thứ ba','Thứ tư','Thứ năm','Thứ sáu','Thứ bảy'];
    const d    = CAL_TODAY;
    label.textContent = `${days[d.getDay()]}, ${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()} · ${tours.length} tour`;

    if (tours.length === 0) {
        body.innerHTML = `<div style="text-align:center;padding:32px 0;color:#94a3b8;">
            <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
            <div style="margin-top:8px;font-weight:600;">Không có tour khởi hành hôm nay</div>
        </div>`;
    } else {
        body.innerHTML = '';
        tours.forEach(t => {
            const info   = getSeatInfo(t.available_seats, t.status);
            const isFull = info.cat === 'full';
            const isTooClose = todayStr < bookingLimitStr; // because today-panel is only for today
            const disableClick = isFull || isTooClose;
            
            const card   = document.createElement('a');
            card.href    = disableClick ? '#' : t.tour_url;
            card.style.cssText = `
                display:flex;align-items:stretch;
                height:110px;
                border-radius:12px;overflow:hidden;
                border:1px solid #e2e8f0;
                background:#fff;
                text-decoration:none;
                transition:box-shadow 0.2s,transform 0.2s;
                ${disableClick ? 'opacity:0.5;pointer-events:none;' : ''}
            `;
            if (!isFull) {
                card.addEventListener('mouseenter', () => { card.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'; card.style.transform='translateY(-2px)'; });
                card.addEventListener('mouseleave', () => { card.style.boxShadow=''; card.style.transform=''; });
            }
            card.innerHTML = `
                <div style="width:120px;min-width:120px;overflow:hidden;flex-shrink:0;">
                    <img src="${t.image_url||FALLBACK}" alt="${t.tour_name}"
                        style="width:100%;height:100%;object-fit:cover;"
                        onerror="this.src='${FALLBACK}'">
                </div>
                <div style="flex:1;padding:10px 14px;display:flex;flex-direction:column;justify-content:space-between;min-width:0;overflow:hidden;">
                    <div style="min-width:0;">
                        <div style="font-weight:800;font-size:0.88rem;color:#1e3a5f;line-height:1.35;
                            display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            ${t.tour_name}
                        </div>
                        <div style="color:#64748b;font-size:0.72rem;margin-top:4px;display:flex;gap:12px;white-space:nowrap;overflow:hidden;">
                            <span style="overflow:hidden;text-overflow:ellipsis;"><i class="bi bi-geo-alt" style="font-size:0.68rem;"></i> ${t.destination||'Việt Nam'}</span>
                            <span style="flex-shrink:0;"><i class="bi bi-clock" style="font-size:0.68rem;"></i> ${t.duration}</span>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:6px;flex-wrap:nowrap;">
                        <span class="cal-seat-badge ${info.cls}" style="font-size:0.66rem;padding:2px 7px;white-space:nowrap;">${info.badge}</span>
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-size:0.6rem;color:#94a3b8;line-height:1;">Giá từ</div>
                            <div style="font-weight:800;font-size:0.9rem;color:#007CE8;white-space:nowrap;">${fmt(t.price)}</div>
                        </div>
                    </div>
                </div>`;
            body.appendChild(card);
        });
    }

    panel.style.display = 'block';
    todayPanelOpen = true;
    // Đổi style nút Hôm nay thành active
    const todayBtn = document.querySelector('.cal-today-btn');
    if (todayBtn) {
        todayBtn.style.background = '#007CE8';
        todayBtn.style.color = '#fff';
    }
    // Cuộn người dùng lên đầu panel
    setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
}

function closeTodayPanel() {
    const panel = document.getElementById('today-panel');
    if (panel) panel.style.display = 'none';
    todayPanelOpen = false;
    
    // Trả nút Hôm nay về bình thường
    const todayBtn = document.querySelector('.cal-today-btn');
    if (todayBtn) { todayBtn.style.background = ''; todayBtn.style.color = ''; }
    
    // Hiện lại nút Ngày lễ
    const holidayPill = document.querySelector('.cal-lp-holiday');
    if (holidayPill) holidayPill.style.display = '';

    // Active lại nút tab lịch cũ
    const activeBtn = document.getElementById('btn-' + currentView);
    if (activeBtn) activeBtn.classList.add('cal-active');

    showCalendar();
}

// ── INIT ───────────────────────────────────────────────────
document.getElementById('calendar-grid').addEventListener('scroll', updateScrollButton);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
window.addEventListener('resize', () => renderAll(allToursData));
document.addEventListener('DOMContentLoaded', fetchCalendarData);
</script>
@endpush
