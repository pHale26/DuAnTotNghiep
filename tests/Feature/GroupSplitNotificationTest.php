<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Destination;
use App\Models\GroupSplit;
use App\Models\Tour;
use App\Models\TourGuide;
use App\Models\TourSchedule;
use App\Models\User;
use App\Notifications\Guide\GroupSplitOverdueNotification;
use App\Notifications\Guide\GroupSplitUnreachableNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->guideUser = User::factory()->create();
    $this->guide = TourGuide::create([
        'user_id' => $this->guideUser->id,
        'name' => 'HDV Test',
        'phone' => '0987654321',
        'email' => $this->guideUser->email,
    ]);

    $this->destination = Destination::create([
        'name' => 'Hạ Long',
        'description' => 'Vịnh Hạ Long',
    ]);

    $this->tour = Tour::create([
        'title' => ['vi' => 'Tour Hạ Long'],
        'slug' => 'tour-ha-long',
        'description' => ['vi' => 'Mô tả'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 2000000,
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
    ]);

    $this->schedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => now()->subDays(1)->toDateTimeString(),
        'return_date' => now()->addDays(1)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    // Assign guide
    $this->schedule->schedule_guides()->create([
        'guide_id' => $this->guide->id,
        'is_backup' => false,
    ]);

    // Create booking and passenger
    $this->booking = Booking::create([
        'user_id' => $this->guideUser->id, // just a user
        'tour_schedule_id' => $this->schedule->id,
        'total_price' => 2000000,
        'adults_count' => 1,
        'children_count' => 0,
        'booking_status' => 'confirmed',
        'payment_status' => 'paid_100',
        'tour_status' => Booking::TOUR_IN_PROGRESS,
    ]);

    $this->passenger = BookingPassenger::create([
        'booking_id' => $this->booking->id,
        'full_name' => 'Nguyen Van A',
        'passenger_type' => 'adult',
    ]);
});

test('marks split as overdue and notifies guide', function () {
    Notification::fake();

    // Create a split that is OVERDUE (past end_time by > 5 minutes)
    $split = GroupSplit::create([
        'tour_id' => $this->tour->id,
        'guest_id' => $this->passenger->id,
        'guest_name' => $this->passenger->full_name,
        'reason' => 'Tự do mua sắm',
        'phone_number' => '0912345678',
        'start_time' => Carbon::now()->subMinutes(30),
        'end_time' => Carbon::now()->subMinutes(10), // > 5 minutes past end_time
        'return_location' => 'Điểm hẹn',
        'status' => GroupSplit::STATUS_ON_TIME,
        'split_started_at' => Carbon::now()->subMinutes(30),
        'created_by' => $this->guideUser->id,
    ]);

    Artisan::call('group-splits:update-status');

    $split->refresh();
    expect($split->status)->toBe(GroupSplit::STATUS_OVERDUE);

    Notification::assertSentTo($this->guideUser, GroupSplitOverdueNotification::class);
});

test('marks split as unreachable and notifies guide', function () {
    Notification::fake();

    // Create a split that is UNREACHABLE (past split_started_at by > 60 minutes)
    $split = GroupSplit::create([
        'tour_id' => $this->tour->id,
        'guest_id' => $this->passenger->id,
        'guest_name' => $this->passenger->full_name,
        'reason' => 'Tự do mua sắm',
        'phone_number' => '0912345678',
        'start_time' => Carbon::now()->subMinutes(70),
        'end_time' => Carbon::now()->subMinutes(10),
        'return_location' => 'Điểm hẹn',
        'status' => GroupSplit::STATUS_ON_TIME,
        'split_started_at' => Carbon::now()->subMinutes(70), // > 60 minutes past split_started_at
        'created_by' => $this->guideUser->id,
    ]);

    Artisan::call('group-splits:update-status');

    $split->refresh();
    expect($split->status)->toBe(GroupSplit::STATUS_UNREACHABLE);

    Notification::assertSentTo($this->guideUser, GroupSplitUnreachableNotification::class);
});
