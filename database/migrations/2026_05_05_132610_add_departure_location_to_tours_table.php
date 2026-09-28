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
        Schema::table('tours', function (Blueprint $table) {
            $table->unsignedBigInteger('departure_location_id')->nullable()->after('destination_id');
            $table->foreign('departure_location_id')->references('id')->on('destinations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropForeign(['departure_location_id']);
            $table->dropColumn('departure_location_id');
        });
    }
};
