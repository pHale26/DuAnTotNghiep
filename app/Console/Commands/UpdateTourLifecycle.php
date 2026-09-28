<?php

namespace App\Console\Commands;

use App\Mail\TourCompletedMail;
use App\Models\Booking;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class UpdateTourLifecycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:update-lifecycle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically updates the status of tour schedules based on departure and return dates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // 1. Available/Full/Closed -> Operating
        $operatingCount = TourSchedule::where(function ($q) {
            $q->whereNull('status')->orWhereIn('status', ['available', 'full', 'closed', 'pending']);
        })
            ->where('departure_date', '<=', $today)
            ->where('return_date', '>=', $today)
            ->update(['status' => 'operating']);

        // 2. Operating/Available/Full/Closed -> Completed
        $completedSchedules = TourSchedule::where(function ($q) {
            $q->whereNull('status')->orWhereIn('status', ['available', 'full', 'closed', 'pending', 'operating']);
        })
            ->where('return_date', '<', $today)
            ->get();

        $completedCount = 0;
        foreach ($completedSchedules as $schedule) {
            $schedule->update(['status' => 'completed']);

            // Cập nhật booking và gửi email
            $bookings = Booking::where('tour_schedule_id', $schedule->id)
                ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER, Booking::TOUR_COMPLETED])
                ->get();

            foreach ($bookings as $booking) {
                $booking->update(['tour_status' => Booking::TOUR_COMPLETED]);
                if ($booking->customer_email) {
                    Mail::to($booking->customer_email)->send(new TourCompletedMail($booking));
                }
            }
            $completedCount++;
        }

        $this->info("Updated {$operatingCount} tours to Operating.");
        $this->info("Updated {$completedCount} tours to Completed.");
    }
}
