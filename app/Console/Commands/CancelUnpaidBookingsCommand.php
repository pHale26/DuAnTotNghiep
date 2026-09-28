<?php

namespace App\Console\Commands;

use App\Events\SeatAvailabilityUpdated;
use App\Mail\TourBookingCancelledMail;
use App\Models\Booking;
use App\Notifications\User\BookingStatusUpdatedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CancelUnpaidBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hủy đơn đặt tour chưa thanh toán sau 15 phút và ghi lý do';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoffTime = now()->subMinutes(15);

        // Lấy danh sách booking chưa thanh toán quá 15 phút
        $bookings = Booking::with(['tour_schedule.tour', 'user', 'booking_passengers'])
            ->where('payment_status', Booking::PAYMENT_PENDING)
            ->where('booking_status', '!=', 'cancelled')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('Không có đơn hàng nào chưa thanh toán cần hủy.');

            return self::SUCCESS;
        }

        $cancelledCount = 0;

        foreach ($bookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    $isCurrentlyCancelled = in_array($booking->tour_status, [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER]);

                    $booking->update([
                        'booking_status' => 'cancelled',
                        'payment_status' => Booking::PAYMENT_FAILED,
                        'tour_status' => Booking::TOUR_CANCELLED_ADMIN,
                        'cancel_reason' => 'Đơn hàng bị hủy do hết hạn thanh toán (quá 15 phút)',
                    ]);

                    if (! $isCurrentlyCancelled && $booking->tour_schedule) {
                        $totalPersons = $booking->adults_count + $booking->children_count;
                        $booking->tour_schedule->increment('available_seats', $totalPersons);

                        broadcast(new SeatAvailabilityUpdated($booking->tour_schedule->id, $booking->tour_schedule->available_seats))->toOthers();
                    }
                });

                // Gửi email thông báo hủy đơn kèm link đặt lại
                $email = $booking->customer_email ?? ($booking->user->email ?? null);
                if ($email) {
                    try {
                        Mail::to($email)->send(new TourBookingCancelledMail($booking));
                    } catch (\Exception $me) {
                        Log::warning('[bookings:cancel-unpaid] Không thể gửi email hủy đơn #'.$booking->id.': '.$me->getMessage());
                    }
                }

                if ($booking->user) {
                    $booking->user->notify(new BookingStatusUpdatedNotification($booking, 'Đơn hàng của bạn đã bị huỷ do quá hạn thanh toán.'));
                }

                $cancelledCount++;
                $message = "Đã tự động hủy đơn hàng ID #{$booking->id} do quá hạn thanh toán 15 phút.";
                $this->info($message);
                Log::info('[bookings:cancel-unpaid] '.$message);
            } catch (\Exception $e) {
                $errorMessage = "Lỗi khi tự động hủy đơn hàng ID #{$booking->id}: ".$e->getMessage();
                $this->error($errorMessage);
                Log::error('[bookings:cancel-unpaid] '.$errorMessage);
            }
        }

        $this->info("Đã hoàn tất tự động hủy {$cancelledCount} đơn hàng.");

        return self::SUCCESS;
    }
}
