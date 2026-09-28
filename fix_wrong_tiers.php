<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tours = \App\Models\Tour::where("duration_nights", ">", 0)->get();
foreach($tours as $tour) {
    $tiers = \App\Models\TourAccommodationTier::where("tour_id", $tour->id)->get();
    $wrong = false;
    foreach($tiers as $tier) {
        $roomDestId = $tier->room_type->accommodation->destination_id;
        if ($roomDestId != $tour->destination_id) {
            $wrong = true;
            break;
        }
    }
    
    if ($wrong) {
        echo "Tour {$tour->id} has wrong tiers! Deleting them...\n";
        \App\Models\TourAccommodationTier::where("tour_id", $tour->id)->delete();
        
        $destId = $tour->destination_id;
        $roomTypes3Star = \App\Models\RoomType::whereHas("accommodation", function ($q) use ($destId) {
            $q->where("destination_id", $destId)->where("star_rating", 3);
        })->get();
        $roomTypes4Star = \App\Models\RoomType::whereHas("accommodation", function ($q) use ($destId) {
            $q->where("destination_id", $destId)->where("star_rating", 4);
        })->get();
        
        if ($roomTypes3Star->isNotEmpty()) {
            $rt3 = $roomTypes3Star->first();
            \App\Models\TourAccommodationTier::firstOrCreate(
                ["tour_id" => $tour->id, "room_type_id" => $rt3->id],
                ["tier_label" => "Tiêu chuẩn (3 sao)", "price_adjustment" => 0, "is_active" => true]
            );
        }
        if ($roomTypes4Star->isNotEmpty()) {
            $rt4 = $roomTypes4Star->first();
            $diff = $rt4->base_price - ($roomTypes3Star->isNotEmpty() ? $roomTypes3Star->first()->base_price : 0);
            \App\Models\TourAccommodationTier::firstOrCreate(
                ["tour_id" => $tour->id, "room_type_id" => $rt4->id],
                ["tier_label" => "Nâng cao (4 sao)", "price_adjustment" => max(0, $diff), "is_active" => true]
            );
        }
        echo "Assigned correct tiers for Tour {$tour->id}!\n";
    }
}
