<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tour_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_itinerary_id');
            // cột này dùng để phân nhóm: Entertainment, Dining, Attractions...
            $table->string('activity_type')->default('Others');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->foreign('tour_itinerary_id')
                ->references('id')->on('tour_itineraries')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_activities');
    }
};
