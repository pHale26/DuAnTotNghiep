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
        Schema::create('tour_assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_schedule_id')->constrained('tour_schedules')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // admin/staff who performed action
            $table->string('action'); // e.g., 'absence_approval', 'manual_reassign'
            $table->text('description');
            $table->json('details')->nullable(); // holds old and new guide details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_assignment_logs');
    }
};
