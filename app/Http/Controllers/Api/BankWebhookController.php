<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TourBookingMail;
use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\User\PaymentSuccessNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BankWebhookController extends Controller
{
    /**
     * Xử lý tín hiệu Webhook chuyển khoản ngân hàng (SePay / VietQR Gateway / MBBank / BIDV...)
     */
    public function handleBankTransfer(Request $request): JsonResponse
    {
        Log::info('Bank Webhook received:', $request->all());

        $content = $request->input('transactionContent') ?? $request->input('content') ?? $request->input('body') ?? '';
        $amountIn = (float) ($request->input('amountIn') ?? $request->input('transferAmount') ?? $request->input('amount') ?? 0);
        $referenceNumber = $request->input('referenceNumber') ?? $request->input('transaction_id') ?? 'BANK_'.time();

        // Tìm mã đơn hàng có cú pháp TW{booking_id} hoặc TW_{booking_id}
        if (preg_match('/TW_?(\d+)/i', $content, $matches)) {
            $bookingId = (int) $matches[1];
            $booking = Booking::with('tour_schedule')->find($bookingId);

            if ($booking) {
                if (in_array($booking->payment_status, [Booking::PAYMENT_PAID, Booking::PAYMENT_PAID_100])) {
                    return response()->json(['status' => 'success', 'message' => 'Booking is already fully paid']);
                }

                DB::transaction(function () use ($booking, $amountIn, $referenceNumber) {
                    $newPaidAmount = $booking->paid_amount + $amountIn;

                    $paymentStatus = ($newPaidAmount >= $booking->total_price)
                        ? Booking::PAYMENT_PAID_100
                        : Booking::PAYMENT_PAID_30;

                    $booking->update([
                        'payment_status' => $paymentStatus,
                        'paid_amount' => $newPaidAmount,
                    ]);

                    Payment::create([
                        'booking_id' => $booking->id,
                        'amount' => $amountIn,
                        'payment_method' => 'transfer',
                        'transaction_code' => $referenceNumber,
                        'payment_status' => 'success',
                        'paid_at' => now(),
                    ]);
                });

                Log::info("Booking #{$booking->id} updated via Bank Webhook. Paid: {$amountIn}");

                $bookingFresh = $booking->fresh();
                $email = $bookingFresh->customer_email ?? ($bookingFresh->user->email ?? null);

                if ($email) {
                    try {
                        Mail::to($email)->send(new TourBookingMail($bookingFresh));
                    } catch (\Exception $me) {
                        Log::warning("BankWebhook: Failed to send email for booking #{$booking->id}: ".$me->getMessage());
                    }
                }

                if ($bookingFresh->user) {
                    $bookingFresh->user->notify(new PaymentSuccessNotification($bookingFresh, $amountIn));
                }

                return response()->json([
                    'status' => 'success',
                    'message' => "Booking #{$booking->id} successfully updated.",
                    'booking_id' => $booking->id,
                    'payment_status' => $booking->payment_status,
                ]);
            }
        }

        return response()->json([
            'status' => 'ignored',
            'message' => 'No matching booking code found in transaction content',
        ], 200);
    }
}
