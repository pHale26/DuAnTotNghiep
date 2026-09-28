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
            $table->decimal('cost_transport', 15, 2)->default(0)->after('child_price');
            $table->decimal('cost_meal', 15, 2)->default(0)->after('cost_transport');
            $table->decimal('cost_insurance', 15, 2)->default(0)->after('cost_meal');
            $table->decimal('cost_service_fee', 15, 2)->default(0)->after('cost_insurance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['cost_transport', 'cost_meal', 'cost_insurance', 'cost_service_fee']);
        });
    }
};
