<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;

/**
 * Helper tạo booking nhanh cho một schedule, không cần user thật vì chỉ test command.
 *
 * @param  array<string, mixed>  $attributes
 */
function createBookingForSchedule(TourSchedule $schedule, array $attributes = []): Booking
{
    $user = User::factory()->create();

    return Booking::create(array_merge([
        'user_id' => $user->id,
        'tour_schedule_id' => $schedule->id,
        'total_price' => 3500000,
        'adults_count' => 2,
        'children_count' => 0,
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_UPCOMING,
    ], $attributes));
}

/**
 * Helper tạo TourSchedule với departure/return date tùy chỉnh.
 *
 * @param  array<string, mixed>  $attributes
 */
function createSchedule(array $attributes = []): TourSchedule
{
    $destination = Destination::create([
        'name' => 'Test Destination '.uniqid(),
        'description' => 'Mô tả test',
    ]);

    $tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Tour Test '.uniqid(),
        'slug' => 'tour-test-'.uniqid(),
        'description' => 'Mô tả',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3500000,
    ]);

    return TourSchedule::create(array_merge([
        'tour_id' => $tour->id,
        'departure_date' => Carbon::today()->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(2)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 18,
        'status' => 'available',
    ], $attributes));
}

test('booking với tour_status upcoming được chuyển sang in_progress khi đến ngày khởi hành', function () {
    $schedule = createSchedule([
        'departure_date' => Carbon::today()->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(2)->toDateTimeString(),
    ]);

    $booking = createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_UPCOMING]);

    $this->artisan('tours:update-status')->assertExitCode(0);

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_IN_PROGRESS);
});

test('chỉ cập nhật booking có tour_status upcoming, bỏ qua các trạng thái khác', function () {
    $schedule = createSchedule([
        'departure_date' => Carbon::today()->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(2)->toDateTimeString(),
    ]);

    $upcomingBooking = createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_UPCOMING]);
    $inProgressBooking = createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_IN_PROGRESS]);
    $completedBooking = createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_COMPLETED]);

    $this->artisan('tours:update-status')->assertExitCode(0);

    expect($upcomingBooking->fresh()->tour_status)->toBe(Booking::TOUR_IN_PROGRESS);
    expect($inProgressBooking->fresh()->tour_status)->toBe(Booking::TOUR_IN_PROGRESS);
    expect($completedBooking->fresh()->tour_status)->toBe(Booking::TOUR_COMPLETED);
});

test('booking đã huỷ không bị ảnh hưởng', function () {
    $schedule = createSchedule([
        'departure_date' => Carbon::today()->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(2)->toDateTimeString(),
    ]);

    $cancelledByAdmin = createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_CANCELLED_ADMIN]);
    $cancelledByCustomer = createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_CANCELLED_CUSTOMER]);

    $this->artisan('tours:update-status')->assertExitCode(0);

    expect($cancelledByAdmin->fresh()->tour_status)->toBe(Booking::TOUR_CANCELLED_ADMIN);
    expect($cancelledByCustomer->fresh()->tour_status)->toBe(Booking::TOUR_CANCELLED_CUSTOMER);
});

test('booking của schedule chưa đến ngày khởi hành không bị ảnh hưởng', function () {
    $futureSchedule = createSchedule([
        'departure_date' => Carbon::today()->addDays(5)->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(7)->toDateTimeString(),
    ]);

    $booking = createBookingForSchedule($futureSchedule, ['tour_status' => Booking::TOUR_UPCOMING]);

    $this->artisan('tours:update-status')->assertExitCode(0);

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_UPCOMING);
});

test('booking của schedule đã kết thúc (return_date trong quá khứ) không bị chuyển sang in_progress', function () {
    $pastSchedule = createSchedule([
        'departure_date' => Carbon::today()->subDays(5)->toDateTimeString(),
        'return_date' => Carbon::today()->subDays(1)->toDateTimeString(),
    ]);

    // Booking bị giữ ở upcoming (trường hợp dữ liệu không nhất quán)
    $booking = createBookingForSchedule($pastSchedule, ['tour_status' => Booking::TOUR_UPCOMING]);

    $this->artisan('tours:update-status')->assertExitCode(0);

    // Schedule đã kết thúc (return_date < today) → không nằm trong điều kiện lọc → không bị cập nhật
    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_UPCOMING);
});

test('command in ra thông báo thành công với số lượng booking đã cập nhật', function () {
    $schedule = createSchedule([
        'departure_date' => Carbon::today()->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(2)->toDateTimeString(),
    ]);

    createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_UPCOMING]);
    createBookingForSchedule($schedule, ['tour_status' => Booking::TOUR_UPCOMING]);

    $this->artisan('tours:update-status')
        ->expectsOutputToContain('2 booking')
        ->assertExitCode(0);
});

test('command in ra thông báo khi không có tour nào đang diễn ra', function () {
    $this->artisan('tours:update-status')
        ->expectsOutputToContain('Không có lịch trình nào đang diễn ra hôm nay')
        ->assertExitCode(0);
});
