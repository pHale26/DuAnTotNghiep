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
            $table->string('departure_province_id', 50)->nullable();
            $table->unsignedBigInteger('departure_ward_id')->nullable();

            $table->string('destination_province_id', 50)->nullable();
            $table->unsignedBigInteger('destination_ward_id')->nullable();

            // Make the old columns nullable
            $table->unsignedBigInteger('destination_id')->nullable()->change();
            $table->unsignedBigInteger('departure_location_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'departure_province_id',
                'departure_ward_id',
                'destination_province_id',
                'destination_ward_id',
            ]);

            // Revert changes (Warning: This might cause issues if they were null and now required)
            // It's safer to leave them nullable in down() or make sure there is data.
            // $table->unsignedBigInteger('destination_id')->nullable(false)->change();
            // $table->unsignedBigInteger('departure_location_id')->nullable(false)->change();
        });
    }
};
