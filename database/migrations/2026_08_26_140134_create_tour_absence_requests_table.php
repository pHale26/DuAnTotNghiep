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
        Schema::create('tour_absence_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->foreignId('tour_schedule_id')->constrained('tour_schedules')->onDelete('cascade');
            $table->foreignId('main_guide_id')->constrained('tour_guides')->onDelete('cascade');
            $table->text('reason');
            $table->string('attachment_url')->nullable();
            $table->string('status')->default('pending_review'); // pending_review, pending_review_urgent, approved, rejected
            $table->string('urgency_level')->default('normal'); // normal, urgent
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reject_reason')->nullable();
            $table->foreignId('new_main_guide_id')->nullable()->constrained('tour_guides')->onDelete('set null');
            $table->foreignId('new_backup_guide_id')->nullable()->constrained('tour_guides')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_absence_requests');
    }
};
