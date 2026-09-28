<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$dests = \App\Models\Destination::where("name", "like", "%Đà Nẵng%")->get();
foreach($dests as $d) {
    echo "ID: {$d->id} - Name: {$d->name}\n";
}
