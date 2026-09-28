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
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_passenger_list_submitted')->default(false)->after('booking_status');
        });

        Schema::table('booking_passengers', function (Blueprint $table) {
            $table->boolean('is_free_time')->default(false)->after('special_note');
            $table->dateTime('free_time_start')->nullable()->after('is_free_time');
            $table->dateTime('free_time_end')->nullable()->after('free_time_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings_and_passengers_tables', function (Blueprint $table) {
            //
        });
    }
};
