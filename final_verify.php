<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$wrong = 0;
$tours = \App\Models\Tour::where("duration_nights", ">", 0)->get();
foreach($tours as $tour) {
    $tiers = \App\Models\TourAccommodationTier::where("tour_id", $tour->id)->get();
    foreach($tiers as $tier) {
        $roomDestId = $tier->room_type->accommodation->destination_id;
        if ($roomDestId != $tour->destination_id) {
            echo "WRONG: Tour {$tour->id} dest {$tour->destination_id} has tier with room dest {$roomDestId}\n";
            $wrong++;
        }
    }
}
if ($wrong == 0) {
    echo "All tours have correct tiers matching their destination.\n";
}
