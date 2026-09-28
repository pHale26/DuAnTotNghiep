<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestTourScheduleAugustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tours = Tour::all();
        $currentYear = date('Y');

        foreach ($tours as $tour) {
            // Generate schedules for dates: 2, 7, 12, 17 in August
            $dayOffsets = [2, 7, 12, 17];

            foreach ($dayOffsets as $day) {
                // Set departure time to 08:00 AM
                $depDate = Carbon::create($currentYear, 8, $day, 8, 0, 0);

                // Calculate return date based on duration
                $duration = $tour->duration_days ?: 0;
                $retDate = (clone $depDate)->addDays($duration);

                TourSchedule::firstOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'departure_date' => $depDate->toDateTimeString(),
                    ],
                    [
                        'return_date' => $retDate->toDateTimeString(),
                        'capacity' => 20,
                        'available_seats' => 20,
                        'status' => 'available',
                    ]
                );
            }
        }

        $this->command->info('Successfully seeded tour schedules from Aug 1 to Aug 20.');
    }
}
