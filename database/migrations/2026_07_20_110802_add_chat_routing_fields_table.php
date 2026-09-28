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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('is_active');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->timestamp('assigned_at')->nullable()->after('booking_id');
            $table->string('routing_status')->default('unassigned')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_seen_at');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['assigned_at', 'routing_status']);
        });
    }
};
