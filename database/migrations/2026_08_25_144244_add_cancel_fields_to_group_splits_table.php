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
        Schema::table('group_splits', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->after('returned_at');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancel_reason');
            $table->dateTime('cancelled_at')->nullable()->after('cancelled_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_splits', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'cancelled_by', 'cancelled_at']);
        });
    }
};
