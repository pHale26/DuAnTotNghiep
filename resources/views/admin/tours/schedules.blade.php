@extends('layouts.admin')
@section('page-title', 'Quản lý lịch trình: ' . $tour->title)

@section('content')
<!-- Thêm Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.tours.index') }}" class="text-decoration-none fw-semibold">
                <i class="bi bi-box-seam me-1"></i>Quản lý Tour
            </a>
        </li>
        <li class="breadcrumb-item text-muted">{{ \Illuminate\Support\Str::limit($tour->title, 40) }}</li>
        <li class="breadcrumb-item active fw-bold" aria-current="page">Lịch trình</li>
    </ol>
</nav>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Thêm lịch trình mới</div>
            <div class="card-body">
                <!-- Bắt đầu phần hiển thị thông báo -->
                @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success small">
                    {{ session('success') }}
                </div>
                @endif
                <!-- Kết thúc phần hiển thị thông báo -->
                <form action="{{ route('admin.tours.schedules.store', $tour->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small">Ngày khởi hành</label>
                        <input type="date" name="departure_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Ngày về</label>
                        <input type="date" name="return_date" class="form-control bg-light" readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Số lượng khách tối đa</label>
                        <input type="number" name="capacity" class="form-control" value="20" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Lưu lịch trình</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Danh sách ngày khởi hành</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Khởi hành</th>
                            <th>Ngày về</th>
                            <th>Chỗ trống</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tour->tour_schedules as $sch)
                        <tr>
                            <td>{{ $sch->departure_date->format('d/m/Y') }}</td>
                            <td>{{ $sch->return_date->format('d/m/Y') }}</td>
                            <td>{{ $sch->available_seats }}/{{ $sch->capacity }}</td>
                            <td>
                                <span class="badge {{ $sch->status == 'available' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $sch->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departureInput = document.querySelector('input[name="departure_date"]');
    const returnInput = document.querySelector('input[name="return_date"]');
    const durationDays = {{ $tour->duration_days ?? 1 }};

    if (departureInput && returnInput) {
        departureInput.addEventListener('change', function() {
            const departureVal = this.value;
            if (departureVal) {
                const date = new Date(departureVal);
                // Cộng thêm (durationDays - 1) ngày
                date.setDate(date.getDate() + (durationDays - 1));

                // Định dạng ngày thành YYYY-MM-DD
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');

                returnInput.value = `${yyyy}-${mm}-${dd}`;
            } else {
                returnInput.value = '';
            }
        });
    }
});
</script>
@endpush
@endsection