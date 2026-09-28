<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('tour_status')->default('upcoming')->after('payment_status');
            $table->string('current_checkin_step')->nullable()->after('tour_status');
        });

        // Migrate existing data
        DB::table('bookings')->orderBy('id')->chunkById(100, function ($bookings) {
            foreach ($bookings as $booking) {
                $payment_status = 'pending';
                $tour_status = 'upcoming';

                switch ($booking->booking_status) {
                    case 'pending':
                    case 'confirmed':
                        $payment_status = 'pending';
                        $tour_status = 'upcoming';
                        break;
                    case 'paid':
                        $payment_status = 'paid_100';
                        $tour_status = 'upcoming';
                        break;
                    case 'cancelled':
                        $payment_status = 'failed';
                        $tour_status = 'cancelled_by_admin';
                        break;
                    case 'completed':
                        $payment_status = 'paid_100';
                        $tour_status = 'completed';
                        break;
                }

                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'payment_status' => $payment_status,
                        'tour_status' => $tour_status,
                    ]);
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booking_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_status', ['pending', 'confirmed', 'paid', 'cancelled', 'completed'])->default('pending')->after('children_count');
        });

        DB::table('bookings')->orderBy('id')->chunkById(100, function ($bookings) {
            foreach ($bookings as $booking) {
                $booking_status = 'pending';

                if ($booking->tour_status === 'completed') {
                    $booking_status = 'completed';
                } elseif (in_array($booking->tour_status, ['cancelled_by_admin', 'cancelled_by_customer'])) {
                    $booking_status = 'cancelled';
                } elseif ($booking->payment_status === 'paid_100') {
                    $booking_status = 'paid';
                } elseif ($booking->payment_status === 'pending') {
                    $booking_status = 'pending';
                }

                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'booking_status' => $booking_status,
                    ]);
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['tour_status', 'current_checkin_step']);
        });
    }
};
