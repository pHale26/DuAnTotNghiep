<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tours = \App\Models\Tour::whereIn("destination_id", [1,2,3,4,5,6,7,8,9,10,11,12])->get()->groupBy("destination_id");
foreach($tours as $destId => $tGroup) {
    echo "Dest $destId has " . $tGroup->count() . " tours\n";
}

$accs = \App\Models\Accommodation::whereIn("destination_id", [1,2,3,4,5,6,7,8,9,10,11,12])->get()->groupBy("destination_id");
foreach($accs as $destId => $aGroup) {
    echo "Dest $destId has " . $aGroup->count() . " accommodations\n";
}
