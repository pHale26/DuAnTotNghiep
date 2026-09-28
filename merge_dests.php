<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dests = \App\Models\Destination::all();
$uniqueDests = [];
foreach($dests as $d) {
    $name = trim($d->name);
    if (!isset($uniqueDests[$name])) {
        $uniqueDests[$name] = $d->id;
    } else {
        $primaryId = $uniqueDests[$name];
        echo "Merging Dest {$d->id} into {$primaryId} ({$name})\n";
        
        \App\Models\Tour::where("destination_id", $d->id)->update(["destination_id" => $primaryId]);
        \App\Models\Accommodation::where("destination_id", $d->id)->update(["destination_id" => $primaryId]);
        \App\Models\Ticket::where("destination_id", $d->id)->update(["destination_id" => $primaryId]);
        
        $d->delete();
    }
}
