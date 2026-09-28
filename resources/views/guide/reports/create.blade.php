@extends('layouts.guide')

@section('page-title', 'Báo Cáo Tour')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Báo Cáo Sự Cố Tour</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <strong>Tour:</strong> {{ $schedule->tour->title }} <br>
                    <strong>Khởi hành:</strong> {{ $schedule->departure_date->format('d/m/Y') }} - {{ $schedule->return_date->format('d/m/Y') }}
                </div>

                <form action="{{ route('guide.reports.store', $schedule->id) }}" method="POST">
                    @csrf
                    
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">1. Thông tin Báo cáo</h6>
                    <div class="mb-3">
                        <label class="form-label">Số khách thực tế tham gia (Tối đa: {{ $schedule->capacity }}) <span class="text-danger">*</span></label>
                        <input type="number" name="actual_guests" class="form-control @error('actual_guests') is-invalid @enderror" required min="0" max="{{ $schedule->capacity }}" value="{{ old('actual_guests', $schedule->capacity) }}">
                        @error('actual_guests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Ghi chú sự cố (Nếu có)</label>
                        <textarea name="incident_notes" class="form-control" rows="4" placeholder="Khách bỏ đoàn, tai nạn, trễ giờ, phàn nàn...">{{ old('incident_notes') }}</textarea>
                    </div>



                    <div class="text-end">
                        <a href="{{ route('guide.schedules.show', $schedule->id) }}" class="btn btn-light px-4 me-2">Quay lại</a>
                        <button type="submit" class="btn btn-primary px-4" onclick="return confirm('Bạn có chắc chắn muốn nộp báo cáo? Sau khi nộp sẽ không thể sửa.')">
                            <i class="bi bi-send me-1"></i> Nộp Báo Cáo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
