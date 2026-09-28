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
        Schema::table('tour_guides', function (Blueprint $table) {
            $table->string('guide_card_type')->nullable(); // Thẻ nội địa, quốc tế
            $table->json('languages')->nullable();
            $table->boolean('is_blacklisted')->default(false);
            $table->decimal('kpi_score', 3, 1)->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_guides', function (Blueprint $table) {
            $table->dropColumn(['guide_card_type', 'languages', 'is_blacklisted', 'kpi_score']);
        });
    }
};
