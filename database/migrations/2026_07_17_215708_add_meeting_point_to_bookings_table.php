<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'meeting_point')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('meeting_point')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'meeting_point')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('meeting_point');
            });
        }
    }
};
