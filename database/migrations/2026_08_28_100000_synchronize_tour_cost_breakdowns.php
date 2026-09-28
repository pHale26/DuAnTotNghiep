<?php

use App\Models\Tour;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $childRate = config('booking.child_price_rate', 0.7);

        Tour::with('tickets')->get()->each(function ($tour) use ($childRate) {
            $ticketAdult = (float) $tour->tickets->sum('adult_price');
            $ticketChild = (float) $tour->tickets->sum('child_price');
            $targetBase = (float) ($tour->base_price > 0 ? $tour->base_price : 1000000);

            if ($ticketAdult >= $targetBase) {
                $targetBase = $ticketAdult + 500000;
            }

            $remaining = max(0, $targetBase - $ticketAdult);
            $transport = round($remaining * 0.35, -3);
            $meal = round($remaining * 0.35, -3);
            $insurance = round($remaining * 0.05, -3);
            $service = $remaining - ($transport + $meal + $insurance);

            $calculatedBase = $transport + $meal + $insurance + $service + $ticketAdult;
            $calculatedChild = round((($transport + $meal + $insurance + $service) * $childRate) + $ticketChild, -3);

            $tour->update([
                'cost_transport' => $transport,
                'cost_meal' => $meal,
                'cost_insurance' => $insurance,
                'cost_service_fee' => $service,
                'base_price' => $calculatedBase,
                'child_price' => $calculatedChild,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed as this updates data values
    }
};
