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
            $table->string('payment_method')->default('transfer')->after('booking_status');
            $table->string('payment_type')->default('full')->after('payment_method');
            $table->string('payment_status')->default('unpaid')->after('payment_type');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_type', 'payment_status', 'paid_amount']);
        });
    }
};
