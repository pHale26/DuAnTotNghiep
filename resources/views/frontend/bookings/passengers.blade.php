@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark mb-0">Bổ Sung Danh Sách Hành Khách</h3>
                <a href="{{ route('user.bookings.detail', $booking->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại đơn hàng
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 bg-light rounded">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold text-primary mb-2">{{ $booking->tour_schedule->tour->title }}</h5>
                            <p class="mb-1"><i class="bi bi-calendar-event me-2"></i>Khởi hành: <strong>{{ \Carbon\Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') }}</strong></p>
                            <p class="mb-0"><i class="bi bi-people me-2"></i>Số lượng: <strong>{{ $booking->adults_count }} người lớn, {{ $booking->children_count }} trẻ em</strong></p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            @if($isLocked)
                                <span class="badge bg-danger p-2 fs-6"><i class="bi bi-lock-fill me-1"></i> Đã khóa bổ sung</span>
                            @elseif($booking->is_passenger_list_submitted)
                                <span class="badge bg-success p-2 fs-6"><i class="bi bi-check-circle-fill me-1"></i> Đã hoàn tất</span>
                            @else
                                <span class="badge bg-warning text-dark p-2 fs-6"><i class="bi bi-exclamation-circle-fill me-1"></i> Cần bổ sung</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!$isLocked && !$booking->is_passenger_list_submitted)
                @php
                    $totalPassengers = $booking->adults_count + $booking->children_count;
                @endphp

                @if($totalPassengers > 3)
                <div class="alert alert-warning shadow-sm border-0">
                    <i class="bi bi-info-circle-fill me-2"></i> Do số lượng hành khách trên 3 người, vui lòng sử dụng chức năng <strong>Tải Lên Excel</strong> để bổ sung danh sách nhằm tiết kiệm thời gian.
                </div>
                @endif

                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    @if($totalPassengers <= 3)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold px-4" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button" role="tab">
                            <i class="bi bi-pencil-square me-2"></i> Nhập Thủ Công
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold px-4" id="pills-excel-tab" data-bs-toggle="pill" data-bs-target="#pills-excel" type="button" role="tab">
                            <i class="bi bi-file-earmark-excel me-2"></i> Tải Lên Excel
                        </button>
                    </li>
                    @else
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold px-4" id="pills-excel-tab" data-bs-toggle="pill" data-bs-target="#pills-excel" type="button" role="tab">
                            <i class="bi bi-file-earmark-excel me-2"></i> Tải Lên Excel
                        </button>
                    </li>
                    @endif
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    @if($totalPassengers <= 3)
                    <!-- Tab Thủ Công -->
                    <div class="tab-pane fade show active" id="pills-manual" role="tabpanel">
                        <form action="{{ route('frontend.bookings.passengers.manual', $booking->id) }}" method="POST">
                            @csrf
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle-fill me-2"></i> Khách hàng đầu tiên là Trưởng đoàn đã được điền lúc đặt vé.
                                    </div>

                                    @php
                                        $leader = $booking->booking_passengers->first();
                                        $totalAdults = $booking->adults_count;
                                        $totalChildren = $booking->children_count;
                                    @endphp

                                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">Người lớn ({{ $totalAdults }})</h5>
                                    @for($i = 0; $i < $totalAdults; $i++)
                                        <div class="p-3 bg-light rounded mb-3 border">
                                            <div class="fw-bold mb-2">Người lớn {{ $i + 1 }} @if($i==0) <span class="badge bg-primary ms-2">Trưởng đoàn</span> @endif</div>
                                            <input type="hidden" name="passengers[{{ $i }}][passenger_type]" value="adult">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small">Họ và Tên</label>
                                                    <input type="text" name="passengers[{{ $i }}][full_name]" class="form-control" required value="{{ $i == 0 && $leader ? $leader->full_name : '' }}" {{ $i == 0 ? 'readonly' : '' }}>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">CCCD/Passport</label>
                                                    <input type="text" name="passengers[{{ $i }}][identity_number]" class="form-control" value="{{ $i == 0 && $leader ? $leader->identity_number : '' }}" {{ $i == 0 ? 'readonly' : '' }}>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Ngày Sinh</label>
                                                    <input type="date" name="passengers[{{ $i }}][date_of_birth]" class="form-control" value="{{ $i == 0 && $leader && $leader->date_of_birth ? $leader->date_of_birth->format('Y-m-d') : '' }}" {{ $i == 0 ? 'readonly' : '' }}>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Giới tính</label>
                                                    <select name="passengers[{{ $i }}][gender]" class="form-select" {{ $i == 0 ? 'readonly' : '' }}>
                                                        <option value="male" {{ $i == 0 && $leader && $leader->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                                        <option value="female" {{ $i == 0 && $leader && $leader->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                                        <option value="other" {{ $i == 0 && $leader && $leader->gender == 'other' ? 'selected' : '' }}>Khác</option>
                                                    </select>
                                                    @if($i == 0)
                                                    <input type="hidden" name="passengers[{{ $i }}][gender]" value="{{ $leader->gender ?? 'other' }}">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endfor

                                    @if($totalChildren > 0)
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-primary">Trẻ em ({{ $totalChildren }})</h5>
                                    @for($i = 0; $i < $totalChildren; $i++)
                                        @php $idx = $totalAdults + $i; @endphp
                                        <div class="p-3 bg-light rounded mb-3 border">
                                            <div class="fw-bold mb-2">Trẻ em {{ $i + 1 }}</div>
                                            <input type="hidden" name="passengers[{{ $idx }}][passenger_type]" value="child">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small">Họ và Tên</label>
                                                    <input type="text" name="passengers[{{ $idx }}][full_name]" class="form-control" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small">Ngày Sinh</label>
                                                    <input type="date" name="passengers[{{ $idx }}][date_of_birth]" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small">Giới tính</label>
                                                    <select name="passengers[{{ $idx }}][gender]" class="form-select">
                                                        <option value="male">Nam</option>
                                                        <option value="female">Nữ</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                    @endif

                                </div>
                                <div class="card-footer bg-white border-top p-3 text-end">
                                    <button type="submit" class="btn btn-primary px-4 fw-bold">Xác Nhận & Gửi Danh Sách</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif

                    <!-- Tab Excel -->
                    <div class="tab-pane fade {{ $totalPassengers > 3 ? 'show active' : '' }}" id="pills-excel" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-5 text-center">
                                <i class="bi bi-file-earmark-excel text-success mb-3" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold mb-3">Tải lên danh sách từ file Excel</h5>
                                <p class="text-muted mb-4">Vui lòng tải file mẫu về, điền thông tin và tải lên lại hệ thống.</p>
                                
                                <a href="{{ route('frontend.bookings.passengers.template') }}" class="btn btn-outline-success mb-4 fw-bold px-4">
                                    <i class="bi bi-download me-2"></i> Tải File Mẫu
                                </a>

                                <hr class="my-4 mx-auto w-50">

                                <form action="{{ route('frontend.bookings.passengers.import', $booking->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3 mx-auto" style="max-width: 400px;">
                                        <input class="form-control" type="file" name="excel_file" accept=".xls,.xlsx" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-bold px-4">
                                        <i class="bi bi-cloud-upload me-2"></i> Tải Lên & Xác Nhận
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- View Only Mode -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold mb-0 text-primary">Danh Sách Hành Khách Đã Đăng Ký</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và Tên</th>
                                    <th>Loại</th>
                                    <th>Giới tính</th>
                                    <th>Ngày sinh</th>
                                    <th>CCCD/Passport</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->booking_passengers as $index => $passenger)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">
                                        {{ $passenger->full_name }}
                                        @if($index == 0) <span class="badge bg-primary ms-1">Trưởng đoàn</span> @endif
                                    </td>
                                    <td>
                                        @if($passenger->passenger_type == 'adult')
                                            <span class="badge bg-secondary">Người lớn</span>
                                        @else
                                            <span class="badge bg-info text-dark">Trẻ em</span>
                                        @endif
                                    </td>
                                    <td>{{ $passenger->gender == 'male' ? 'Nam' : ($passenger->gender == 'female' ? 'Nữ' : 'Khác') }}</td>
                                    <td>{{ $passenger->date_of_birth ? $passenger->date_of_birth->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $passenger->identity_number ?: '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
