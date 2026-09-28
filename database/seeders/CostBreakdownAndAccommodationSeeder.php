<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Addon;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class CostBreakdownAndAccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accommodations = Accommodation::all();

        if ($accommodations->isEmpty()) {
            $this->call(AccommodationSeeder::class);
            $accommodations = Accommodation::all();
        }

        $tours = Tour::all();

        foreach ($tours as $tour) {
            // Randomly assign 1 to 3 accommodations to this tour
            $tour->accommodations()->sync(
                $accommodations->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Generate cost breakdown matching base_price and tickets
            $basePrice = $tour->base_price > 0 ? $tour->base_price : rand(1000000, 5000000);
            $ticketAdultCost = $tour->tickets->sum('adult_price');
            $ticketChildCost = $tour->tickets->sum('child_price');
            if ($ticketAdultCost >= $basePrice) {
                $basePrice = $ticketAdultCost + 500000;
            }

            $remaining = max(0, $basePrice - $ticketAdultCost);
            $transport = round($remaining * 0.35, -3);
            $meal = round($remaining * 0.35, -3);
            $insurance = round($remaining * 0.05, -3);
            $service = $remaining - ($transport + $meal + $insurance);
            $childRate = config('booking.child_price_rate', 0.7);

            $tour->update([
                'cost_transport' => $transport,
                'cost_meal' => $meal,
                'cost_insurance' => $insurance,
                'cost_service_fee' => $service,
                'base_price' => $transport + $meal + $insurance + $service + $ticketAdultCost,
                'child_price' => round((($transport + $meal + $insurance + $service) * $childRate) + $ticketChildCost, -3),
            ]);
        }

        // Update addons type
        $addons = Addon::all();
        foreach ($addons as $index => $addon) {
            // Half included, half extra
            $addon->update([
                'type' => $index % 2 == 0 ? 'included' : 'extra',
            ]);
        }
    }
}
