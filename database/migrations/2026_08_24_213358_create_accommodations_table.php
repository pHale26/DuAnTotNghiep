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
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price_per_adult', 15, 2)->default(0);
            $table->decimal('price_single_supplement', 15, 2)->default(0);
            $table->decimal('price_extra_bed', 15, 2)->default(0);
            $table->decimal('price_child', 15, 2)->default(0);
            $table->decimal('holiday_price_per_adult', 15, 2)->default(0);
            $table->decimal('holiday_price_single_supplement', 15, 2)->default(0);
            $table->decimal('holiday_price_extra_bed', 15, 2)->default(0);
            $table->decimal('holiday_price_child', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
