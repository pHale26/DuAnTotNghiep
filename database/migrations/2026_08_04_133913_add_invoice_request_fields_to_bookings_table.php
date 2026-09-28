<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('invoice_status')
                ->default('none')
                ->after('payment_status');

            $table->string('invoice_email')
                ->nullable()
                ->after('invoice_status');

            $table->timestamp('invoice_requested_at')
                ->nullable()
                ->after('invoice_email');

            $table->timestamp('invoice_sent_at')
                ->nullable()
                ->after('invoice_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_status',
                'invoice_email',
                'invoice_requested_at',
                'invoice_sent_at',
            ]);
        });
    }
};
