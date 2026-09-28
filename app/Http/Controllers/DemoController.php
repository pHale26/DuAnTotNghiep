<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BankWebhookController;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DemoController extends Controller
{
    /**
     * Giả lập Tiền về (Pay Now) qua QR Ngân hàng
     */
    public function simulatePayment($id): JsonResponse
    {
        $booking = Booking::findOrFail($id);

        $amountIn = ($booking->payment_status === Booking::PAYMENT_PAID_30)
            ? $booking->remaining_amount
            : (($booking->payment_type === 'deposit') ? $booking->deposit_amount : $booking->total_price);

        // Gọi trực tiếp Webhook giả lập
        $controller = new BankWebhookController;
        $request = new Request([
            'transactionContent' => "TW{$booking->id} thanh toan tour",
            'amountIn' => $amountIn,
            'referenceNumber' => 'DEMO_TRANS_'.time(),
        ]);

        return $controller->handleBankTransfer($request);
    }

    /**
     * Tua nhanh 30 phút và kích hoạt tự động hủy đơn
     */
    public function fastForwardCancel($id): JsonResponse
    {
        $booking = Booking::with('tour_schedule')->findOrFail($id);

        if (in_array($booking->payment_status, [Booking::PAYMENT_PAID, Booking::PAYMENT_PAID_100, Booking::PAYMENT_PAID_30])) {
            return response()->json(['status' => 'error', 'message' => 'Đơn hàng này đã thanh toán, không thể tự động hủy!'], 400);
        }

        // Lùi thời gian tạo về 31 phút trước
        $booking->timestamps = false;
        $booking->created_at = now()->subMinutes(31);
        $booking->save();

        // Kích hoạt Artisan Command hủy đơn quá hạn
        Artisan::call('bookings:cancel-unpaid');

        $booking->refresh();

        return response()->json([
            'status' => 'success',
            'message' => "Đã tua nhanh 30 phút và tự động hủy đơn #{$booking->id}.",
            'booking_status' => $booking->booking_status,
            'cancel_reason' => $booking->cancel_reason,
            'available_seats' => $booking->tour_schedule ? $booking->tour_schedule->available_seats : 0,
        ]);
    }
}
