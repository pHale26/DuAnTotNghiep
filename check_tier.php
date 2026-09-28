<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tiers = \App\Models\TourAccommodationTier::with("room_type.accommodation.destination")->where("tour_id", 19)->get();
foreach($tiers as $tier) {
    echo "Tier: {$tier->tier_label} - Acc: {$tier->room_type->accommodation->name} - Dest: {$tier->room_type->accommodation->destination->name}\n";
}
