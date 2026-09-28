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
        Schema::create('schedule_activity_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_schedule_id')->constrained('tour_schedules')->cascadeOnDelete();
            $table->foreignId('tour_activity_id')->constrained('tour_activities')->cascadeOnDelete();
            $table->foreignId('guide_id')->constrained('tour_guides')->cascadeOnDelete();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            $table->unique(['tour_schedule_id', 'tour_activity_id'], 'chk_sched_act_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_activity_checkins');
    }
};
