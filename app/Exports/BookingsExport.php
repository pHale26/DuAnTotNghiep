<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Mã Đơn',
            'Ngày Tạo',
            'Khách Hàng',
            'Số Điện Thoại',
            'Tour',
            'Khởi Hành',
            'Người Lớn',
            'Trẻ Em',
            'Tổng Tiền',
            'Đã Thanh Toán',
            'TT Thanh Toán',
            'TT Đơn Hàng',
            'TT Tour',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->code,
            $booking->created_at->format('d/m/Y H:i'),
            $booking->customer_name,
            $booking->customer_phone,
            $booking->tour_schedule->tour->name ?? 'N/A',
            $booking->tour_schedule ? Carbon::parse($booking->tour_schedule->departure_date)->format('d/m/Y') : 'N/A',
            $booking->adults_count,
            $booking->children_count,
            $booking->total_price,
            $booking->paid_amount,
            $this->getPaymentStatus($booking->payment_status),
            $this->getBookingStatus($booking->booking_status),
            $this->getTourStatus($booking->tour_status),
        ];
    }

    private function getPaymentStatus($status)
    {
        return match ($status) {
            'pending' => 'Chờ thanh toán',
            'paid_30' => 'Đã cọc 30%',
            'paid_100' => 'Đã thanh toán 100%',
            'failed' => 'Thất bại',
            default => $status,
        };
    }

    private function getBookingStatus($status)
    {
        return match ($status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
            default => $status,
        };
    }

    private function getTourStatus($status)
    {
        return match ($status) {
            'upcoming' => 'Sắp khởi hành',
            'in_progress' => 'Đang diễn ra',
            'checking_in' => 'Đang Check-in',
            'completed' => 'Hoàn thành',
            'cancelled_by_customer' => 'Khách Hủy',
            'cancelled_by_admin' => 'Admin Hủy',
            default => $status,
        };
    }
}
