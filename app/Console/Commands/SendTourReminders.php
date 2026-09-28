<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\User\TourDepartureReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTourReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send departure reminders to customers 1 day before tour starts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get tomorrow's date (Y-m-d)
        $tomorrow = Carbon::tomorrow()->toDateString();

        $this->info("Bắt đầu gửi thông báo cho các tour khởi hành vào ngày: $tomorrow");
        Log::info("SendTourReminders: Bắt đầu gửi thông báo cho ngày $tomorrow");

        // Find bookings that are confirmed/paid and depart tomorrow
        $bookings = Booking::whereIn('booking_status', ['confirmed', 'paid'])
            ->whereHas('tour_schedule', function ($q) use ($tomorrow) {
                $q->whereDate('start_date', $tomorrow);
            })
            ->with('user', 'tour_schedule.tour')
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            if ($booking->user) {
                $booking->user->notify(new TourDepartureReminderNotification($booking));
                $count++;
            }
        }

        $this->info("Đã gửi thành công $count thông báo nhắc nhở.");
        Log::info("SendTourReminders: Đã gửi $count thông báo.");
    }
}
