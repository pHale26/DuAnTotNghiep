<?php

use App\Mail\TourReminderMail;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->destination = Destination::create([
        'name' => 'Hà Nội',
        'description' => 'Thủ đô Hà Nội',
    ]);
});

test('sends reminder email and closes schedule when tour departs in 3 days', function () {
    Mail::fake();

    $tour = Tour::create([
        'title' => ['vi' => 'Tour Miền Tây'],
        'slug' => 'tour-mien-tay',
        'description' => ['vi' => 'Mô tả tour'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 1500000,
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
    ]);

    // Lịch trình khởi hành sau đúng 3 ngày (tương lai)
    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(3)->toDateTimeString(),
        'return_date' => now()->addDays(5)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
        'reminder_sent' => false,
    ]);

    $booking = Booking::create([
        'user_id' => $this->user->id,
        'tour_schedule_id' => $schedule->id,
        'total_price' => 1500000,
        'adults_count' => 1,
        'children_count' => 0,
        'booking_status' => 'confirmed',
        'payment_status' => 'paid_100',
        'tour_status' => Booking::TOUR_UPCOMING,
    ]);

    // Chạy artisan command
    Artisan::call('tours:reminder-three-days');

    // Tải lại dữ liệu lịch trình
    $schedule->refresh();

    // Lịch trình phải được chuyển sang 'closed' và đánh dấu 'reminder_sent' = true
    expect($schedule->status)->toBe('closed');
    expect($schedule->reminder_sent)->toBe(true);

    // Xác nhận đã gửi email nhắc nhở cho hành khách đặt tour
    Mail::assertSent(TourReminderMail::class, function ($mail) use ($booking) {
        return $mail->booking->id === $booking->id;
    });
});

test('does not send reminder email if reminder_sent is already true', function () {
    Mail::fake();

    $tour = Tour::create([
        'title' => ['vi' => 'Tour Miền Tây'],
        'slug' => 'tour-mien-tay',
        'description' => ['vi' => 'Mô tả tour'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 1500000,
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
    ]);

    // Lịch trình khởi hành sau đúng 3 ngày nhưng đã được gửi nhắc nhở trước đó
    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(3)->toDateTimeString(),
        'return_date' => now()->addDays(5)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'closed',
        'reminder_sent' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->user->id,
        'tour_schedule_id' => $schedule->id,
        'total_price' => 1500000,
        'adults_count' => 1,
        'children_count' => 0,
        'booking_status' => 'confirmed',
        'payment_status' => 'paid_100',
        'tour_status' => Booking::TOUR_UPCOMING,
    ]);

    // Chạy artisan command
    Artisan::call('tours:reminder-three-days');

    // Xác nhận KHÔNG gửi thêm email nào
    Mail::assertNotSent(TourReminderMail::class);
});
