<?php
$tours = \App\Models\Tour::where("duration_days", ">", 1)->doesntHave("accommodation_tiers")->get();
$roomTypes = \App\Models\RoomType::all();
foreach($tours as $tour) {
    if($roomTypes->isEmpty()) continue;
    $randomRooms = $roomTypes->random(min(2, $roomTypes->count()));
    foreach($randomRooms as $i => $room) {
        \App\Models\TourAccommodationTier::firstOrCreate(
            ["tour_id" => $tour->id, "room_type_id" => $room->id],
            ["tier_label" => $i === 0 ? "Tiêu chuẩn" : "Cao cấp"]
        );
    }
    echo "Added tiers for Tour ID: " . $tour->id . PHP_EOL;
}
