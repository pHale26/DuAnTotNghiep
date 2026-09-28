@extends('layouts.admin')

@section('page-title', 'Chi tiết Báo cáo Tour')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="admin-card border-0 mb-4">
            <div class="admin-card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="admin-card-title mb-0">Chi tiết Báo cáo #{{ $report->id }}</h5>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-light border">Quay lại</a>
            </div>
            <div class="admin-card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">Tour:</p>
                        <h6 class="fw-bold">{{ $report->tour_schedule->tour->title }}</h6>
                        <p class="mb-1"><strong>Mã Tour:</strong> {{ $report->tour_schedule->tour->code }}</p>
                        <p class="mb-0"><strong>Thời gian:</strong> {{ $report->tour_schedule->departure_date->format('d/m/Y') }} - {{ $report->tour_schedule->return_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">Hướng dẫn viên:</p>
                        <h6 class="fw-bold">{{ $report->tour_guide->name }}</h6>
                        <p class="mb-1"><strong>SĐT:</strong> {{ $report->tour_guide->phone }}</p>
                        <p class="mb-0"><strong>Khách thực tế:</strong> {{ $report->actual_guests }} / {{ $report->tour_schedule->capacity }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="mb-1 text-muted small">Ghi chú sự cố:</p>
                    <div class="p-3 bg-light rounded border">
                        {!! nl2br(e($report->incident_notes ?? 'Không có ghi chú.')) !!}
                    </div>
                </div>



                @if($report->status === 'pending')
                    <div class="d-flex justify-content-end gap-2 mt-4" x-data="{ showRejectForm: false }">
                        <button type="button" @click="showRejectForm = !showRejectForm" class="btn btn-danger px-4">
                            <i class="bi bi-x-circle me-1"></i> Từ chối Báo cáo
                        </button>
                        
                        <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success px-4" onclick="return confirm('Xác nhận đã xem báo cáo và duyệt đóng tour này?')">
                                <i class="bi bi-check-circle me-1"></i> Duyệt Báo Cáo & Khóa Tour
                            </button>
                        </form>
                    </div>

                    <div x-show="showRejectForm" class="mt-3 p-3 border rounded bg-light text-start" x-cloak x-transition>
                        <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="reject_reason" class="form-label fw-bold">Lý do từ chối <span class="text-danger">*</span></label>
                                <textarea name="reject_reason" id="reject_reason" rows="3" class="form-control" placeholder="Nhập lý do từ chối..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" @click="showRejectForm = false" class="btn btn-sm btn-secondary me-2">Hủy</button>
                                <button type="submit" class="btn btn-sm btn-danger">Xác nhận Từ chối</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill me-2"></i> Báo cáo này đã được duyệt.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
