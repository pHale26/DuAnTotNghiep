<?php

use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

test('bookings cancel unpaid command automatically cancels pending bookings created over 30 minutes ago and restores seats', function () {
    $user = User::factory()->create();

    $tour = Tour::create([
        'title' => 'Tour Test Auto Cancel',
        'slug' => 'tour-test-auto-cancel',
        'description' => ['vi' => 'Mô tả tour test'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 1000000,
    ]);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(5),
        'return_date' => now()->addDays(7),
        'capacity' => 20,
        'available_seats' => 10,
        'status' => 'available',
    ]);

    // Đơn hàng chưa thanh toán tạo cách đây 31 phút
    $expiredBooking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $schedule->id,
        'payment_status' => Booking::PAYMENT_PENDING,
        'booking_status' => 'pending',
        'tour_status' => Booking::TOUR_UPCOMING,
        'total_price' => 1000000,
        'paid_amount' => 0,
        'adults_count' => 2,
        'children_count' => 1,
    ]);
    DB::table('bookings')->where('id', $expiredBooking->id)->update(['created_at' => now()->subMinutes(31)]);

    // Đơn hàng mới tạo cách đây 10 phút
    $recentBooking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $schedule->id,
        'payment_status' => Booking::PAYMENT_PENDING,
        'booking_status' => 'pending',
        'tour_status' => Booking::TOUR_UPCOMING,
        'total_price' => 1000000,
        'paid_amount' => 0,
        'adults_count' => 1,
        'children_count' => 0,
    ]);
    DB::table('bookings')->where('id', $recentBooking->id)->update(['created_at' => now()->subMinutes(10)]);

    Artisan::call('bookings:cancel-unpaid');

    $expiredBooking->refresh();
    $recentBooking->refresh();
    $schedule->refresh();

    expect($expiredBooking->booking_status)->toBe('cancelled');
    expect($expiredBooking->payment_status)->toBe(Booking::PAYMENT_FAILED);
    expect($expiredBooking->cancel_reason)->toContain('15 phút');

    expect($recentBooking->booking_status)->toBe('pending');

    // Restore seats count: 10 + 3 (from expired booking) = 13
    expect($schedule->available_seats)->toBe(13);
});

test('demo fast forward endpoint updates creation time and cancels unpaid booking', function () {
    $user = User::factory()->create();

    $tour = Tour::create([
        'title' => 'Tour Test Fast Forward',
        'slug' => 'tour-test-fast-forward',
        'description' => ['vi' => 'Mô tả tour test'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 1000000,
    ]);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(5),
        'return_date' => now()->addDays(7),
        'capacity' => 20,
        'available_seats' => 5,
        'status' => 'available',
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $schedule->id,
        'payment_status' => Booking::PAYMENT_PENDING,
        'booking_status' => 'pending',
        'tour_status' => Booking::TOUR_UPCOMING,
        'total_price' => 1000000,
        'paid_amount' => 0,
        'adults_count' => 2,
        'children_count' => 0,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->postJson(route('demo.bookings.fast_forward_cancel', $booking->id));

    $response->assertOk();
    $response->assertJson([
        'status' => 'success',
        'booking_status' => 'cancelled',
    ]);

    $booking->refresh();
    $schedule->refresh();

    expect($booking->booking_status)->toBe('cancelled');
    expect($schedule->available_seats)->toBe(7);
});

test('demo simulate payment endpoint triggers payment confirmation via bank webhook', function () {
    $user = User::factory()->create();

    $tour = Tour::create([
        'title' => 'Tour Test Simulate Payment',
        'slug' => 'tour-test-simulate-payment',
        'description' => ['vi' => 'Mô tả tour test'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 2000000,
    ]);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(5),
        'return_date' => now()->addDays(7),
        'capacity' => 20,
        'available_seats' => 10,
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
        'adults_count' => 1,
        'children_count' => 0,
        'payment_type' => 'full',
    ]);

    $response = $this->actingAs($user)->postJson(route('demo.bookings.simulate_payment', $booking->id));

    $response->assertOk();
    $response->assertJson([
        'status' => 'success',
        'payment_status' => Booking::PAYMENT_PAID_100,
    ]);

    $booking->refresh();
    expect($booking->payment_status)->toBe(Booking::PAYMENT_PAID_100);
});
