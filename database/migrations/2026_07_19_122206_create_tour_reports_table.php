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
        Schema::create('tour_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_schedule_id')->constrained('tour_schedules')->cascadeOnDelete();
            $table->foreignId('guide_id')->constrained('tour_guides')->cascadeOnDelete();

            // Báo cáo
            $table->integer('actual_guests')->default(0);
            $table->text('incident_notes')->nullable();

            // Quyết toán
            $table->decimal('advance_amount', 12, 2)->default(0);
            $table->decimal('actual_expense', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_reports');
    }
};
