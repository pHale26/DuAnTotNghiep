<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tour = \App\Models\Tour::find(19);
echo "Tour 19 dest_id: " . $tour->destination_id . "\n";
$accs = \App\Models\Accommodation::where("name", "like", "%Đà Nẵng%")->get();
foreach($accs as $a) {
    echo "Acc: {$a->name} - dest_id: " . $a->destination_id . "\n";
}
