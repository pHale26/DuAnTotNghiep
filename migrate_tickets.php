<?php

use App\Models\Ticket;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$tickets = Ticket::with('ticket_options')->get();
foreach ($tickets as $ticket) {
    if ($ticket->ticket_options->count() > 0) {
        $ticket->adult_price = $ticket->ticket_options[0]->price;
        $ticket->child_price = isset($ticket->ticket_options[1]) ? $ticket->ticket_options[1]->price : $ticket->ticket_options[0]->price;
        $ticket->save();
    }
}
echo "Done\n";
