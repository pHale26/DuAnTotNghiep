<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TourAccommodationTier;

TourAccommodationTier::where("tier_label", "like", "Ti%u chu%n%")->update(["tier_label" => "Tiêu chuẩn (3 sao)"]);
TourAccommodationTier::where("tier_label", "like", "N%ng cao%")->update(["tier_label" => "Nâng cao (4 sao)"]);

echo "Fixed encoding in DB!\n";

