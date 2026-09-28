@extends('layouts.admin')

@section('page-title', 'Xuất hóa đơn')

@section('content')

    <div class="invoice-toolbar d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Xuất hóa đơn</h4>
            <div class="text-muted">
                Đơn đặt tour:
                <span class="text-primary fw-semibold">
                    #BOOK-{{ $booking->created_at->format('Ymd') }}-{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        </div>

    <div class="invoice-actions d-flex flex-wrap align-items-center gap-2">

        <a
            href="{{ route('admin.bookings.index') }}"
            class="btn btn-light border"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại danh sách
        </a>
@if(($booking->invoice_status ?? 'none') === 'requested')
    <form action="{{ route('admin.bookings.invoice.send', $booking->id) }}"
          method="POST"
          class="d-inline-block m-0"
          onsubmit="return confirm('Gửi hóa đơn đến {{ $booking->invoice_email ?? $booking->user?->email }}?')">
        @csrf

        <button type="submit" class="btn btn-warning">
            <i class="bi bi-envelope-arrow-up me-1"></i>
            Gửi hóa đơn cho khách
        </button>
    </form>

@elseif(($booking->invoice_status ?? 'none') === 'sent')
    <button type="button"
            class="btn btn-outline-success"
            disabled>
        <i class="bi bi-check-circle-fill me-1"></i>
        Đã gửi hóa đơn
    </button>
@endif
        <form
            action="{{ route('admin.bookings.invoice.download', ['id' => $booking->id]) }}"
            method="GET"
            class="d-inline-block m-0">
            <button
                type="submit"
                class="btn btn-primary">
                <i class="bi bi-file-earmark-arrow-down me-1"></i>
                Tải PDF
            </button>
        </form>

        <button
            type="button"
            class="btn btn-success"
            onclick="window.print()"
        >
            <i class="bi bi-printer me-1"></i>
            In hóa đơn
        </button>

    </div>
</div>
</div>

@include('admin.bookings.partials.invoice-content', ['isPdf' => false])
@endsection

@push('styles')
<style>
    .invoice-toolbar {
        position: relative !important;
        z-index: 9999 !important;
        pointer-events: auto !important;
    }

    .invoice-actions {
        position: relative !important;
        z-index: 10000 !important;
        pointer-events: auto !important;
    }

    .invoice-actions a,
    .invoice-actions button,
    .invoice-actions form {
        position: relative !important;
        z-index: 10001 !important;
        pointer-events: auto !important;
        cursor: pointer !important;
    }

</style>
@endpush
