<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Transport type: xe (bus/car) or bay (flight)
            $table->string('transport_type')->nullable()->after('base_price')->comment('xe or bay');
            // Hotel star rating: 1-5
            $table->tinyInteger('hotel_stars')->nullable()->after('transport_type')->comment('1 to 5 stars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['transport_type', 'hotel_stars']);
        });
    }
};
