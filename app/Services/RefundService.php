<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\RefundRequest;
use App\Models\RoomInventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RefundService
{
    /**
     * Calculate how much should be refunded for a booking
     *
     * @return array
     */
    public function calculateRefund(Booking $booking)
    {
        // Total paid by customer
        $paidAmount = (float) $booking->paid_amount;

        if ($paidAmount <= 0) {
            return [
                'is_refundable' => false,
                'refund_amount' => 0,
                'reason' => 'Khách hàng chưa thanh toán.',
            ];
        }

        // Deduct insurance (non-refundable)
        // Check price_breakdown for insurance
        $insuranceCost = 0;
        if (is_array($booking->price_breakdown) && isset($booking->price_breakdown['insurance'])) {
            // Wait, price breakdown insurance is per person * total persons, or total directly?
            // Usually price_breakdown has total for that category.
            $insuranceCost = (float) $booking->price_breakdown['insurance'];
        }

        // Refundable base is Paid - Insurance
        $refundableBase = max(0, $paidAmount - $insuranceCost);

        // Calculate days to departure
        $departureDate = Carbon::parse($booking->tour_schedule->departure_date);
        $daysToDeparture = now()->startOfDay()->diffInDays($departureDate->startOfDay(), false);

        $refundPercent = 0;
        $reason = '';

        if ($daysToDeparture >= 7) {
            $refundPercent = 100;
            $reason = 'Hủy trước 7 ngày (Hoàn 100% trừ bảo hiểm)';
        } elseif ($daysToDeparture >= 3) {
            $refundPercent = 50;
            $reason = 'Hủy trước 3-7 ngày (Hoàn 50% trừ bảo hiểm)';
        } else {
            $refundPercent = 0;
            $reason = 'Hủy trong vòng 3 ngày (Không hoàn tiền)';
        }

        $refundAmount = ($refundableBase * $refundPercent) / 100;

        return [
            'is_refundable' => $refundAmount > 0,
            'refund_amount' => $refundAmount,
            'refund_percent' => $refundPercent,
            'insurance_cost' => $insuranceCost,
            'refundable_base' => $refundableBase,
            'days_to_departure' => $daysToDeparture,
            'reason' => $reason,
        ];
    }

    public function processUserCancellation(Booking $booking, $bankData = null)
    {
        return DB::transaction(function () use ($booking, $bankData) {
            $booking->booking_status = 'cancelled';
            $booking->tour_status = Booking::TOUR_CANCELLED_CUSTOMER;
            $booking->cancel_reason = 'Khách hàng tự hủy';
            $booking->save();

            // Free up inventory (TourSchedule seats & Accommodation rooms)
            // Restore seats
            $totalPersons = $booking->adults_count + $booking->children_count;
            $booking->tour_schedule->increment('available_seats', $totalPersons);

            // Restore rooms
            if ($booking->booking_accommodations && $booking->booking_accommodations->count() > 0) {
                foreach ($booking->booking_accommodations as $bAcc) {
                    RoomInventory::where('room_type_id', $bAcc->room_type_id)
                        ->where('date', $booking->tour_schedule->departure_date)
                        ->decrement('booked_rooms', $bAcc->single_rooms_count);
                }
            }

            $refundCalc = $this->calculateRefund($booking);

            if ($refundCalc['is_refundable']) {
                RefundRequest::create([
                    'booking_id' => $booking->id,
                    'amount' => $refundCalc['refund_amount'],
                    'status' => 'pending',
                    'refund_method' => 'bank_transfer',
                    'bank_name' => $bankData['bank_name'] ?? null,
                    'bank_account_name' => $bankData['bank_account_name'] ?? null,
                    'bank_account_number' => $bankData['bank_account_number'] ?? null,
                    'reason' => $refundCalc['reason'],
                ]);
            }

            return $refundCalc;
        });
    }
}
