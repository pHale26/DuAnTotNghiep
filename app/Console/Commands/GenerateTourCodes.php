<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;

class GenerateTourCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:generate-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate codes for existing tours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tours = Tour::whereNull('code')->orWhere('code', '')->get();
        $count = 0;
        foreach ($tours as $tour) {
            $tour->code = Tour::generateTourCode($tour);
            $tour->save();
            $count++;
        }

        $this->info("Generated codes for {$count} tours.");
    }
}
