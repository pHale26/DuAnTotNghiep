@extends('layouts.master')

@section('content')
<style>
    .flight-card {
        transition: var(--transition-normal);
        border: 1px solid #edf2f7;
    }
    .flight-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(0, 124, 232, 0.2);
    }
    .flight-time {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark-blue);
        line-height: 1;
    }
    .flight-iata {
        font-weight: 600;
        color: var(--text-muted);
        letter-spacing: 1px;
    }
    .flight-line {
        height: 2px;
        background: #e2e8f0;
        position: relative;
        flex-grow: 1;
        margin: 0 20px;
    }
    .flight-line::before, .flight-line::after {
        content: '';
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        top: -3px;
    }
    .flight-line::before { left: 0; }
    .flight-line::after { right: 0; }
    .flight-line i {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        color: var(--primary-color);
        background: white;
        padding: 0 10px;
    }
</style>

<div class="container py-5">
    <div class="glass-panel p-4 p-md-5 mb-5 reveal-up">
        <div class="mb-4 d-flex align-items-center">
            <i class="bi bi-airplane-engines-fill text-primary fs-3 me-3"></i>
            <h2 class="section-heading mb-0 fs-3">Tìm Kiếm Chuyến Bay</h2>
        </div>

        <form action="{{ route('frontend.flights.search') }}" method="GET" class="row g-4 align-items-end">
            <input type="hidden" name="tour_booking_id" value="{{ request('tour_booking_id') }}">
            
            <div class="col-md-3">
                <label class="form-label text-dark fw-600">Điểm khởi hành</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                    <select name="origin" class="form-select search-form-control border-start-0 ps-0" required>
                        <option value="HAN" {{ request('origin') == 'HAN' ? 'selected' : '' }}>Hà Nội (HAN)</option>
                        <option value="SGN" {{ request('origin') == 'SGN' ? 'selected' : '' }}>TP. Hồ Chí Minh (SGN)</option>
                        <option value="DAD" {{ request('origin') == 'DAD' ? 'selected' : '' }}>Đà Nẵng (DAD)</option>
                        <option value="PQC" {{ request('origin') == 'PQC' ? 'selected' : '' }}>Phú Quốc (PQC)</option>
                        <option value="CXR" {{ request('origin') == 'CXR' ? 'selected' : '' }}>Nha Trang (CXR)</option>
                        <option value="DLI" {{ request('origin') == 'DLI' ? 'selected' : '' }}>Đà Lạt (DLI)</option>
                        <option value="HPH" {{ request('origin') == 'HPH' ? 'selected' : '' }}>Hải Phòng (HPH)</option>
                        <option value="VII" {{ request('origin') == 'VII' ? 'selected' : '' }}>Vinh (VII)</option>
                        <option value="HUI" {{ request('origin') == 'HUI' ? 'selected' : '' }}>Huế (HUI)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label text-dark fw-600">Điểm đến</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt-fill"></i></span>
                    <select name="destination" class="form-select search-form-control border-start-0 ps-0" required>
                        <option value="SGN" {{ request('destination') == 'SGN' ? 'selected' : '' }}>TP. Hồ Chí Minh (SGN)</option>
                        <option value="HAN" {{ request('destination') == 'HAN' ? 'selected' : '' }}>Hà Nội (HAN)</option>
                        <option value="DAD" {{ request('destination') == 'DAD' ? 'selected' : '' }}>Đà Nẵng (DAD)</option>
                        <option value="PQC" {{ request('destination') == 'PQC' ? 'selected' : '' }}>Phú Quốc (PQC)</option>
                        <option value="CXR" {{ request('destination') == 'CXR' ? 'selected' : '' }}>Nha Trang (CXR)</option>
                        <option value="DLI" {{ request('destination') == 'DLI' ? 'selected' : '' }}>Đà Lạt (DLI)</option>
                        <option value="HPH" {{ request('destination') == 'HPH' ? 'selected' : '' }}>Hải Phòng (HPH)</option>
                        <option value="VII" {{ request('destination') == 'VII' ? 'selected' : '' }}>Vinh (VII)</option>
                        <option value="HUI" {{ request('destination') == 'HUI' ? 'selected' : '' }}>Huế (HUI)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label text-dark fw-600">Ngày đi</label>
                <input type="date" name="departure_date" class="form-control search-form-control"
                    value="{{ request('departure_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
            </div>

            <div class="col-md-2">
                <label class="form-label text-dark fw-600">Hành khách & Hạng ghế</label>
                <div class="input-group">
                    <input type="number" name="passengers" class="form-control search-form-control"
                        value="{{ request('passengers', 1) }}" min="1" max="9" title="Số hành khách">
                    <select name="cabin_class" class="form-select search-form-control" style="max-width: 90px; padding-left: 10px;" title="Hạng ghế">
                        <option value="economy" {{ request('cabin_class') == 'economy' ? 'selected' : '' }}>PT</option>
                        <option value="business" {{ request('cabin_class') == 'business' ? 'selected' : '' }}>TG</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-search-primary w-100 py-3"><i class="bi bi-search me-2"></i>Tìm vé</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        @forelse($offers as $offer)
        <div class="col-12 reveal-up">
            <div class="premium-card flight-card">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-md-2 mb-4 mb-md-0 text-center text-md-start">
                            <div class="text-muted small fw-500 mb-1 text-uppercase">Hãng khai thác</div>
                            <div class="fw-bold fs-5 text-dark">{{ $offer['owner']['name'] }}</div>
                        </div>

                        <div class="col-md-7 d-flex align-items-center justify-content-between px-md-5 mb-4 mb-md-0">
                            <div class="text-center">
                                <div class="flight-time">
                                    {{ \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['departing_at'])->format('H:i') }}
                                </div>
                                <div class="flight-iata mt-2">{{ $offer['slices'][0]['origin']['iata_code'] }}</div>
                            </div>
                            
                            <div class="flex-grow-1 text-center position-relative">
                                <div class="text-primary fw-600 small mb-2">Bay thẳng</div>
                                <div class="flight-line"><i class="bi bi-airplane-fill fs-5"></i></div>
                                @php
                                $depart = \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['departing_at']);
                                $arrive = \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['arriving_at']);
                                $diffMins = $depart->diffInMinutes($arrive);
                                @endphp
                                <div class="text-muted small fw-500 mt-2">
                                    {{ floor($diffMins / 60) }}h {{ $diffMins % 60 }}m
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="flight-time">
                                    {{ \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['arriving_at'])->format('H:i') }}
                                </div>
                                <div class="flight-iata mt-2">{{ $offer['slices'][0]['destination']['iata_code'] }}</div>
                            </div>
                        </div>

                        <div class="col-md-3 text-center text-md-end border-md-start ps-md-4">
                            <div class="text-muted small fw-500 mb-2">Giá vé một chiều / khách</div>
                            <div class="text-danger mb-3 fw-bold" style="font-size: 1.8rem;">
                                {{ number_format($offer['total_amount'], 0, ',', '.') }}<span class="fs-5 ms-1">{{ $offer['total_currency'] }}</span>
                            </div>
                            <a href="{{ route('frontend.flights.checkout', [
                                'offer_id' => $offer['id'], 
                                'passenger_id' => $offer['passengers'][0]['id'] ?? '',
                                'tour_booking_id' => request('tour_booking_id'),
                                'total_amount' => $offer['total_amount'],
                                'total_currency' => $offer['total_currency']
                            ]) }}" class="btn btn-register-premium w-100 py-2">Chọn Chuyến Bay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 reveal-up text-center py-5">
            <div class="glass-panel p-5">
                <i class="bi bi-airplane text-muted opacity-50 mb-3" style="font-size: 4rem;"></i>
                <h4 class="fw-bold text-dark mb-2">Chưa có chuyến bay nào được tìm thấy</h4>
                <p class="text-muted fs-6">Vui lòng chọn điểm khởi hành, điểm đến và ngày đi để xem kết quả.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection