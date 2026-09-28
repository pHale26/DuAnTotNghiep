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
        if (! Schema::hasColumn('schedule_guides', 'is_backup')) {
            Schema::table('schedule_guides', function (Blueprint $table) {
                $table->boolean('is_backup')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('schedule_guides', 'is_backup')) {
            Schema::table('schedule_guides', function (Blueprint $table) {
                $table->dropColumn('is_backup');
            });
        }
    }
};
