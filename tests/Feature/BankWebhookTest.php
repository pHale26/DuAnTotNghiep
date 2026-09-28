<?php

use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;

test('bank webhook automatically updates booking payment status to paid when QR content matches', function () {
    $user = User::factory()->create();

    $tour = Tour::create([
        'title' => 'Tour Test Webhook',
        'slug' => 'tour-test-webhook',
        'description' => ['vi' => 'Mô tả tour test'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 1500000,
    ]);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(5),
        'return_date' => now()->addDays(7),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $schedule->id,
        'payment_status' => Booking::PAYMENT_PENDING,
        'booking_status' => 'pending',
        'tour_status' => Booking::TOUR_UPCOMING,
        'total_price' => 1500000,
        'paid_amount' => 0,
        'adults_count' => 1,
        'children_count' => 0,
    ]);

    $response = $this->postJson(route('api.webhooks.bank_transfer'), [
        'transactionContent' => "TW{$booking->id} thanh toan tour",
        'amountIn' => 1500000,
        'referenceNumber' => 'BANK_REF_9999',
    ]);

    $response->assertOk();
    $response->assertJson([
        'status' => 'success',
        'booking_id' => $booking->id,
        'payment_status' => Booking::PAYMENT_PAID_100,
    ]);

    $booking->refresh();
    expect($booking->payment_status)->toBe(Booking::PAYMENT_PAID_100);
    expect((float) $booking->paid_amount)->toBe(1500000.0);

    $this->assertDatabaseHas('payments', [
        'booking_id' => $booking->id,
        'amount' => 1500000,
        'transaction_code' => 'BANK_REF_9999',
    ]);
});

test('bank webhook ignores transactions without valid booking code', function () {
    $response = $this->postJson(route('api.webhooks.bank_transfer'), [
        'transactionContent' => 'Chuyen tien mung sinh nhat',
        'amountIn' => 500000,
    ]);

    $response->assertOk();
    $response->assertJson([
        'status' => 'ignored',
    ]);
});

test('bank webhook updates booking status to paid_30 when 30 percent deposit is paid', function () {
    $user = User::factory()->create();

    $tour = Tour::create([
        'title' => 'Tour Test Deposit',
        'slug' => 'tour-test-deposit',
        'description' => ['vi' => 'Mô tả tour test deposit'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 2000000,
    ]);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(5),
        'return_date' => now()->addDays(7),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $schedule->id,
        'payment_status' => Booking::PAYMENT_PENDING,
        'booking_status' => 'pending',
        'tour_status' => Booking::TOUR_UPCOMING,
        'total_price' => 2000000,
        'paid_amount' => 0,
        'payment_type' => 'deposit',
        'adults_count' => 1,
        'children_count' => 0,
    ]);

    $response = $this->postJson(route('api.webhooks.bank_transfer'), [
        'transactionContent' => "TW{$booking->id} dat coc 30%",
        'amountIn' => 600000,
        'referenceNumber' => 'BANK_REF_3030',
    ]);

    $response->assertOk();
    $response->assertJson([
        'status' => 'success',
        'booking_id' => $booking->id,
        'payment_status' => Booking::PAYMENT_PAID_30,
    ]);

    $booking->refresh();
    expect($booking->payment_status)->toBe(Booking::PAYMENT_PAID_30);
    expect((float) $booking->paid_amount)->toBe(600000.0);
});
