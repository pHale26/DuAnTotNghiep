<?php

use App\Models\Booking;
use App\Models\TourSchedule;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$schedule = TourSchedule::find(63);
echo 'Schedule ID: '.$schedule->id.PHP_EOL;
echo 'Available Seats: '.$schedule->available_seats.PHP_EOL;
echo 'Max People: '.$schedule->tour->max_people.PHP_EOL;
$bookings = Booking::where('tour_schedule_id', 63)->get();
foreach ($bookings as $b) {
    echo 'Booking '.$b->id.': '.$b->adults_count.' A + '.$b->children_count.' C = '.($b->adults_count + $b->children_count).' (Status: '.$b->booking_status.')'.PHP_EOL;
}
