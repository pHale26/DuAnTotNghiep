<?php

namespace Tests\Feature;

use App\Mail\TourBookingCancelledMail;
use App\Mail\TourBookingMail;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TourBookingMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_tour_booking_mail_content_for_paid_30_deposit(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $tour = Tour::create([
            'title' => 'Tour Test Mail',
            'slug' => 'tour-test-mail',
            'description' => ['vi' => 'Mô tả test'],
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
            'payment_status' => Booking::PAYMENT_PAID_30,
            'payment_type' => 'deposit',
            'booking_status' => 'confirmed',
            'tour_status' => Booking::TOUR_UPCOMING,
            'total_price' => 2000000,
            'paid_amount' => 600000,
            'adults_count' => 1,
            'children_count' => 0,
        ]);

        $mail = new TourBookingMail($booking);
        $mail->assertHasSubject('Xác nhận cọc 30% tour thành công - Mã đơn #'.str_pad($booking->id, 6, '0', STR_PAD_LEFT));
        $mail->assertSeeInHtml('Xác Nhận Cọc 30% Tour Thành Công');
        $mail->assertSeeInHtml('600.000₫');
        $mail->assertSeeInHtml('1.400.000₫');
        $mail->assertSeeInHtml('#pay70Section');
    }

    public function test_cancel_unpaid_booking_sends_cancelled_mail_with_rebook_link(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $tour = Tour::create([
            'title' => 'Tour Test Cancel Mail',
            'slug' => 'tour-test-cancel-mail',
            'description' => ['vi' => 'Mô tả test cancel'],
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
            'adults_count' => 2,
            'children_count' => 1,
            'created_at' => now()->subMinutes(35),
        ]);
        DB::table('bookings')->where('id', $booking->id)->update(['created_at' => now()->subMinutes(35)]);

        $this->artisan('bookings:cancel-unpaid')
            ->assertExitCode(0);

        Mail::assertSent(TourBookingCancelledMail::class, function ($mail) use ($user, $schedule) {
            return $mail->hasTo($user->email) &&
                   str_contains($mail->rebookUrl, 'schedule_id='.$schedule->id) &&
                   str_contains($mail->rebookUrl, 'adults=2') &&
                   str_contains($mail->rebookUrl, 'children=1');
        });
    }
}
