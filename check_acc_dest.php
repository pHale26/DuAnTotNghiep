<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$accs = \App\Models\Accommodation::with("destination")->get();
foreach($accs as $acc) {
    echo "Acc: {$acc->name} - Dest: " . ($acc->destination ? $acc->destination->name : "NONE") . "\n";
}
