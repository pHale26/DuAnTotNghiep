<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Review;
use App\Models\ScheduleGuide;
use App\Models\Tour;
use App\Models\TourGuide;
use App\Models\TourReport;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Bắt đầu chạy DemoSeeder (Dữ liệu cố định cho ngày 21/07/2026)...');

        // 1. Tạo HDV Demo
        $guideUser = User::firstOrCreate(
            ['email' => 'guide@gmail.com'],
            [
                'name' => 'Hướng dẫn viên Demo',
                'password' => Hash::make('12345678'),
                'role' => 'guide',
                'phone' => '0999888777',
            ]
        );

        $tourGuide = TourGuide::firstOrCreate(
            ['user_id' => $guideUser->id],
            [
                'name' => 'Hướng dẫn viên Demo',
                'phone' => '0999888777',
                'email' => 'guide@gmail.com',
                'guide_card_type' => 'quoc_te',
                'languages' => ['Việt', 'Anh'],
                'status' => 'active',
            ]
        );

        $customerUser = User::where('email', 'user@gmail.com')->first();
        $adminUser = User::where('email', 'admin@gmail.com')->first();

        if (! $customerUser || ! $adminUser) {
            $this->command->warn('Không tìm thấy user@gmail.com hoặc admin@gmail.com. Vui lòng chạy DatabaseSeeder trước.');

            return;
        }

        // Lấy 1 tour ngẫu nhiên (hoặc tour đầu tiên) để làm demo
        $tour = Tour::first();
        if (! $tour) {
            $this->command->warn('Chưa có dữ liệu Tour. Vui lòng chạy MasterTourSeeder trước.');

            return;
        }

        // Cố định số ngày của tour cho dễ tính
        $tourDays = $tour->duration_days > 0 ? $tour->duration_days : 3;

        // 2. Tạo các Lịch trình (Tour Schedules) xoay quanh mốc 21/07/2026
        // A. Tour đã hoàn thành (Past Tour) - Khởi hành 15/07, Kết thúc 18/07
        $pastDepDate = Carbon::create(2026, 7, 15, 8, 0, 0);
        $pastRetDate = (clone $pastDepDate)->addDays($tourDays);
        $pastSchedule = TourSchedule::firstOrCreate([
            'tour_id' => $tour->id,
            'departure_date' => $pastDepDate->toDateTimeString(),
        ], [
            'return_date' => $pastRetDate->toDateTimeString(),
            'capacity' => 25,
            'available_seats' => 20,
            'status' => 'closed',
        ]);

        // B. Tour đang diễn ra (In-Progress) - Khởi hành 20/07, Kết thúc 23/07
        $inProgressDepDate = Carbon::create(2026, 7, 20, 8, 0, 0);
        $inProgressRetDate = (clone $inProgressDepDate)->addDays($tourDays);
        $inProgressSchedule = TourSchedule::firstOrCreate([
            'tour_id' => $tour->id,
            'departure_date' => $inProgressDepDate->toDateTimeString(),
        ], [
            'return_date' => $inProgressRetDate->toDateTimeString(),
            'capacity' => 30,
            'available_seats' => 25,
            'status' => 'closed',
        ]);

        // C. Tour sắp bắt đầu (Upcoming) - Khởi hành 22/07, Kết thúc 25/07
        $upcomingDepDate = Carbon::create(2026, 7, 22, 8, 0, 0);
        $upcomingRetDate = (clone $upcomingDepDate)->addDays($tourDays);
        $upcomingSchedule = TourSchedule::firstOrCreate([
            'tour_id' => $tour->id,
            'departure_date' => $upcomingDepDate->toDateTimeString(),
        ], [
            'return_date' => $upcomingRetDate->toDateTimeString(),
            'capacity' => 20,
            'available_seats' => 10,
            'status' => 'available', // status của schedule là available cho upcoming
        ]);

        // 3. Phân công HDV (ScheduleGuide)
        foreach ([$pastSchedule, $inProgressSchedule, $upcomingSchedule] as $schedule) {
            ScheduleGuide::firstOrCreate([
                'tour_schedule_id' => $schedule->id,
                'guide_id' => $tourGuide->id,
            ], [
                'is_backup' => false,
            ]);
        }

        // 4. Tạo Đơn hàng (Bookings) cho Khách hàng

        // Helper function tạo mã đơn
        $generateBookingCode = function () {
            return 'PNR'.strtoupper(Str::random(6));
        };

        // A. Booking cho Past Tour (Hoàn thành)
        $pastBooking = Booking::firstOrCreate([
            'tour_schedule_id' => $pastSchedule->id,
            'user_id' => $customerUser->id,
        ], [
            'pnr_code' => $generateBookingCode(),
            'adults_count' => 2,
            'children_count' => 0,
            'total_price' => $tour->base_price * 2,
            'payment_status' => 'paid_100',
            'payment_method' => 'vnpay',
            'tour_status' => 'completed',
        ]);

        // B. Booking cho In-Progress Tour (Đang đi)
        $inProgressBooking = Booking::firstOrCreate([
            'tour_schedule_id' => $inProgressSchedule->id,
            'user_id' => $customerUser->id,
        ], [
            'pnr_code' => $generateBookingCode(),
            'adults_count' => 2,
            'children_count' => 1,
            'total_price' => ($tour->base_price * 2) + ($tour->child_price * 1),
            'payment_status' => 'paid_100',
            'payment_method' => 'bank_transfer',
            'tour_status' => 'in_progress',
            'current_checkin_step' => 'Trạm 1: Khởi hành',
        ]);

        // C. Bookings cho Upcoming Tour (1 cái Pending/Đã Thanh toán, 1 cái Khách Hủy)
        $upcomingBooking = Booking::firstOrCreate([
            'tour_schedule_id' => $upcomingSchedule->id,
            'user_id' => $customerUser->id,
        ], [
            'pnr_code' => $generateBookingCode(),
            'adults_count' => 4,
            'children_count' => 0,
            'total_price' => $tour->base_price * 4,
            'payment_status' => 'paid_100',
            'payment_method' => 'cash',
            'tour_status' => 'upcoming',
        ]);

        // 5. Đánh giá & Báo cáo cho Past Tour
        // A. Review
        Review::firstOrCreate([
            'tour_id' => $tour->id,
            'user_id' => $customerUser->id,
        ], [
            'rating' => 5,
            'comment' => 'Tour rất tuyệt vời, HDV nhiệt tình và cẩn thận. Rất đáng tiền!',
            'guide_id' => $tourGuide->id,
            'guide_rating' => 5,
        ]);

        // B. Tour Report (HDV làm báo cáo)
        TourReport::firstOrCreate([
            'tour_schedule_id' => $pastSchedule->id,
            'guide_id' => $tourGuide->id,
        ], [
            'actual_guests' => 2,
            'incident_notes' => 'Không có sự cố nào xảy ra. Khách hàng hài lòng.',
            'advance_amount' => 1000000,
            'actual_expense' => 800000,
            'balance' => 200000,
            'status' => 'pending',
        ]);

        // 6. Chat / Messages
        $conversation = Conversation::firstOrCreate([
            'user_id' => $customerUser->id,
            'cskh_id' => $adminUser->id,
        ], [
            'status' => 'open',
        ]);

        Message::firstOrCreate([
            'conversation_id' => $conversation->id,
            'message' => 'Chào bạn, tôi muốn hỏi chút thông tin về tour khởi hành ngày 22/07?',
        ], [
            'sender_id' => $customerUser->id,
            'read_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2),
        ]);

        Message::firstOrCreate([
            'conversation_id' => $conversation->id,
            'message' => 'Chào anh/chị, em có thể giúp gì được ạ?',
        ], [
            'sender_id' => $adminUser->id,
            'read_at' => null,
            'created_at' => Carbon::now()->subDays(2)->addMinutes(5),
        ]);

        $this->command->info('Hoàn tất chạy DemoSeeder!');
    }
}
