<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$c = app()->make('App\Http\Controllers\Frontend\TourController');
echo $c->aiSummary(5)->getContent();
