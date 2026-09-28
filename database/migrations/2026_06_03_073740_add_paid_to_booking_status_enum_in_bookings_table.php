<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN booking_status ENUM('pending', 'confirmed', 'paid', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN booking_status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
