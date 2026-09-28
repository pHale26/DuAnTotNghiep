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
        Schema::create('activity_passenger_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_passenger_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_passenger_checkins');
    }
};
