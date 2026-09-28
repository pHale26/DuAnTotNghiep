<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->destination = Destination::create([
        'name' => 'Hà Nội',
        'description' => 'Thủ đô Hà Nội',
    ]);
});

test('tour status is updated to in_progress if departure time has passed', function () {
    // 1. Tour khởi hành 1 tiếng trước
    $pastTour = Tour::create([
        'title' => ['vi' => 'Tour Quá Khứ'],
        'slug' => 'tour-qua-khu',
        'description' => ['vi' => 'Mô tả tour quá khứ'],
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 1000000,
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
        'departure_time' => now()->subHour()->format('H:i'),
    ]);

    $schedule1 = TourSchedule::create([
        'tour_id' => $pastTour->id,
        'departure_date' => now()->toDateString(),
        'return_date' => now()->toDateString(),
        'capacity' => 10,
        'available_seats' => 10,
    ]);

    $booking1 = Booking::create([
        'user_id' => $this->user->id,
        'tour_schedule_id' => $schedule1->id,
        'total_price' => 1000000,
        'adults_count' => 1,
        'children_count' => 0,
        'tour_status' => Booking::TOUR_UPCOMING,
        'booking_status' => 'confirmed',
    ]);

    // 2. Tour khởi hành 1 tiếng sau (tương lai)
    $futureTour = Tour::create([
        'title' => ['vi' => 'Tour Tương Lai'],
        'slug' => 'tour-tuong-lai',
        'description' => ['vi' => 'Mô tả tour tương lai'],
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 1000000,
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
        'departure_time' => now()->addHour()->format('H:i'),
    ]);

    $schedule2 = TourSchedule::create([
        'tour_id' => $futureTour->id,
        'departure_date' => now()->toDateString(),
        'return_date' => now()->toDateString(),
        'capacity' => 10,
        'available_seats' => 10,
    ]);

    $booking2 = Booking::create([
        'user_id' => $this->user->id,
        'tour_schedule_id' => $schedule2->id,
        'total_price' => 1000000,
        'adults_count' => 1,
        'children_count' => 0,
        'tour_status' => Booking::TOUR_UPCOMING,
        'booking_status' => 'confirmed',
    ]);

    // Chạy logic tự động cập nhật
    Booking::updateUpcomingTourStatuses();

    // Check kết quả
    $booking1->refresh();
    $booking2->refresh();

    expect($booking1->tour_status)->toBe(Booking::TOUR_IN_PROGRESS);
    expect($booking2->tour_status)->toBe(Booking::TOUR_UPCOMING);
});
