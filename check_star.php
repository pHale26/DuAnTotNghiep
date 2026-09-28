<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$accs = \App\Models\Accommodation::all();
foreach($accs as $acc) {
    echo "{$acc->name} - Stars: {$acc->star_rating}\n";
}
