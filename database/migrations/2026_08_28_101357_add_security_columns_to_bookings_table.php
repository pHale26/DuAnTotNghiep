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
            $table->boolean('is_email_verified')->default(false)->after('payment_status');
            $table->string('email_verify_token')->nullable()->after('is_email_verified');
            $table->boolean('ignored_by_user')->default(false)->after('email_verify_token');
            $table->boolean('is_reported')->default(false)->after('ignored_by_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_email_verified', 'email_verify_token', 'ignored_by_user', 'is_reported']);
        });
    }
};
