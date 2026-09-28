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
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('guide_id')->nullable()->constrained('tour_guides')->nullOnDelete();
            $table->integer('guide_rating')->nullable()->comment('Đánh giá HDV từ 1-5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['guide_id']);
            $table->dropColumn(['guide_id', 'guide_rating']);
        });
    }
};
