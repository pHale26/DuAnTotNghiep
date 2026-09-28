<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tours = \App\Models\Tour::where("duration_nights", ">", 0)->get();
foreach($tours as $t) {
    $dest = $t->destination;
    echo "Tour ID: {$t->id} - Dest: " . ($dest ? $dest->name : "NONE") . " - Tiers: " . $t->accommodation_tiers->count() . "\n";
}
