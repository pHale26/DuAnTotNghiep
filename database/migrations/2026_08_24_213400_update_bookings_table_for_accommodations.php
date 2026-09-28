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
            $table->foreignId('accommodation_id')->nullable()->after('coupon_id')->constrained()->nullOnDelete();
            $table->integer('single_rooms_count')->default(0)->after('children_count');
            $table->integer('extra_beds_count')->default(0)->after('single_rooms_count');
            $table->json('price_breakdown')->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['accommodation_id']);
            $table->dropColumn(['accommodation_id', 'single_rooms_count', 'extra_beds_count', 'price_breakdown']);
        });
    }
};
