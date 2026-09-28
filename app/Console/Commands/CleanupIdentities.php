<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\UserIdentity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupIdentities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'identities:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up encrypted ID images 10 days after tour completion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = Carbon::now()->subDays(10);

        // Find bookings that are completed and ended more than 10 days ago
        $bookings = Booking::where('tour_status', 'completed')
            ->whereHas('tour_schedule', function ($q) use ($threshold) {
                // Approximate end date: departure_date + 3 days (can use duration_days if needed)
                // Since duration_days is in Tour, we join it.
                $q->join('tours', 'tours.id', '=', 'tour_schedules.tour_id')
                    ->whereRaw('DATE_ADD(tour_schedules.departure_date, INTERVAL tours.duration_days DAY) <= ?', [$threshold]);
            })
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $identity = UserIdentity::where('user_id', $booking->user_id)->first();
            if ($identity) {
                if ($identity->front_image_url && str_starts_with($identity->front_image_url, 'private/')) {
                    Storage::disk('local')->delete($identity->front_image_url);
                    $identity->front_image_url = null;
                }
                if ($identity->back_image_url && str_starts_with($identity->back_image_url, 'private/')) {
                    Storage::disk('local')->delete($identity->back_image_url);
                    $identity->back_image_url = null;
                }
                if ($identity->isDirty()) {
                    $identity->save();
                    $count++;
                }
            }
        }

        $this->info("Cleaned up identities for {$count} users.");
    }
}
