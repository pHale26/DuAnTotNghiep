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
        Schema::create('group_splits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('stop_id')->nullable();
            $table->unsignedBigInteger('guest_id'); // refers to booking_passengers.id
            $table->string('guest_name');
            $table->text('reason');
            $table->string('phone_number', 20);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('return_location');
            $table->string('split_location')->nullable();
            $table->enum('status', ['ON_TIME', 'OVERDUE', 'UNREACHABLE', 'RETURNED', 'CANCELLED'])->default('ON_TIME');
            $table->dateTime('split_started_at');
            $table->dateTime('returned_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // HDV id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_splits');
    }
};
