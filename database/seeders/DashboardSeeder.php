<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Review;
use App\Models\TicketBooking;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Đang khởi tạo 100 đơn hàng mẫu và dữ liệu thống kê cho Admin Dashboard...');

        // 1. Đảm bảo có danh sách Khách hàng mẫu
        $users = User::where('role', 'user')->get();
        if ($users->count() < 10) {
            $sampleNames = [
                'Nguyễn Văn An', 'Trần Thị Bích', 'Lê Hoàng Nam', 'Phạm Minh Đức', 'Vũ Thùy Linh',
                'Hoàng Gia Bách', 'Đặng Kim Ngân', 'Bùi Văn Hùng', 'Đỗ Thanh Hà', 'Ngô Quốc Anh',
            ];
            foreach ($sampleNames as $i => $name) {
                $email = 'customer'.($i + 1).'@example.com';
                $users->push(User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make('12345678'),
                        'role' => 'user',
                        'phone' => '09'.rand(10000000, 99999999),
                    ]
                ));
            }
        }

        // 2. Lấy danh sách Tour
        $tours = Tour::all();
        if ($tours->isEmpty()) {
            $this->command->warn('Chưa có tour trong CSDL. Vui lòng chạy MasterTourSeeder trước.');

            return;
        }

        // 3. Tạo các Lịch trình khởi hành (TourSchedules) trải dài từ -60 ngày đến +30 ngày
        $schedules = collect();
        foreach ($tours as $tour) {
            $duration = $tour->duration_days > 0 ? $tour->duration_days : 3;
            // Tạo 4-6 mốc lịch trình cho mỗi tour
            for ($daysOffset = -50; $daysOffset <= 30; $daysOffset += 15) {
                $depDate = now()->addDays($daysOffset)->setHour(8)->setMinute(0)->setSecond(0);
                $retDate = (clone $depDate)->addDays($duration);

                $status = 'available';
                if ($depDate->isPast()) {
                    $status = 'closed';
                }

                $schedule = TourSchedule::firstOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'departure_date' => $depDate->toDateTimeString(),
                    ],
                    [
                        'return_date' => $retDate->toDateTimeString(),
                        'capacity' => 30,
                        'available_seats' => rand(5, 25),
                        'status' => $status,
                    ]
                );
                $schedules->push($schedule);
            }
        }

        // 4. Sinh 100 Đơn hàng (Bookings) trải dài trong 60 ngày gần đây
        $paymentMethods = ['vnpay', 'momo', 'cash', 'transfer'];
        $transportTypes = ['flight', 'bus', 'none'];
        $firstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Vũ', 'Đặng', 'Bùi', 'Đỗ'];
        $lastNames = ['Hùng', 'Linh', 'Dũng', 'Trang', 'Phương', 'Minh', 'Tuấn', 'Hoa', 'Thảo', 'Thành'];

        for ($i = 1; $i <= 100; $i++) {
            $user = $users->random();
            $schedule = $schedules->random();
            $tour = $schedule->tour;

            // Ngày tạo đơn rải rác từ -59 ngày trước cho tới hôm nay
            $createdDaysAgo = rand(0, 59);
            $createdAt = now()->subDays($createdDaysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $adults = rand(1, 4);
            $children = rand(0, 2);
            $basePrice = $tour ? $tour->base_price : 3000000;
            $totalPrice = ($adults * $basePrice) + ($children * $basePrice * 0.7);

            // Phân bổ trạng thái
            $randStatus = rand(1, 100);
            if ($randStatus <= 55) {
                // 55% Đã thanh toán 100%
                $paymentStatus = Booking::PAYMENT_PAID_100;
                $bookingStatus = 'paid';
                $paidAmount = $totalPrice;
                $tourStatus = $schedule->departure_date <= now() ? Booking::TOUR_COMPLETED : Booking::TOUR_UPCOMING;
            } elseif ($randStatus <= 80) {
                // 25% Đã cọc 30%
                $paymentStatus = Booking::PAYMENT_PAID_30;
                $bookingStatus = 'confirmed';
                $paidAmount = $totalPrice * 0.3;
                $tourStatus = Booking::TOUR_UPCOMING;
            } elseif ($randStatus <= 92) {
                // 12% Chờ thanh toán
                $paymentStatus = Booking::PAYMENT_PENDING;
                $bookingStatus = 'pending';
                $paidAmount = 0;
                $tourStatus = Booking::TOUR_UPCOMING;
            } else {
                // 8% Thất bại / Đã hủy
                $paymentStatus = Booking::PAYMENT_FAILED;
                $bookingStatus = 'cancelled';
                $paidAmount = 0;
                $tourStatus = rand(0, 1) ? Booking::TOUR_CANCELLED_CUSTOMER : Booking::TOUR_CANCELLED_ADMIN;
            }

            $transportType = $transportTypes[array_rand($transportTypes)];
            $pnrCode = null;
            if ($transportType === 'flight') {
                // 60% có pnr_code, 40% chưa có pnr_code (để xuất hiện trên widget vé máy bay)
                if (rand(1, 100) <= 60 && $bookingStatus !== 'cancelled') {
                    $pnrCode = 'PNR'.strtoupper(Str::random(5));
                }
            }

            $booking = Booking::create([
                'user_id' => $user->id,
                'tour_schedule_id' => $schedule->id,
                'adults_count' => $adults,
                'children_count' => $children,
                'total_price' => $totalPrice,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'booking_status' => $bookingStatus,
                'tour_status' => $tourStatus,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'transport_type' => $transportType,
                'transport_price' => $transportType === 'flight' ? 1500000 * ($adults + $children) : 0,
                'pnr_code' => $pnrCode,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Tạo danh sách hành khách cho booking
            $totalPassengers = $adults + $children;
            for ($p = 0; $p < $totalPassengers; $p++) {
                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'full_name' => $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)],
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'passenger_type' => $p < $adults ? 'adult' : 'child',
                    'checked_in' => $tourStatus === Booking::TOUR_COMPLETED,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        // 5. Sinh dữ liệu TicketBookings (Vé tham quan)
        $ticketOptions = TicketOption::all();
        if ($ticketOptions->isNotEmpty()) {
            for ($t = 0; $t < 20; $t++) {
                $user = $users->random();
                $option = $ticketOptions->random();
                $quantity = rand(1, 5);
                $totalPrice = $option->price * $quantity;
                $createdDaysAgo = rand(0, 45);
                $createdAt = now()->subDays($createdDaysAgo);

                TicketBooking::create([
                    'user_id' => $user->id,
                    'ticket_option_id' => $option->id,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                    'visit_date' => $createdAt->copy()->addDays(rand(1, 10)),
                    'booking_status' => rand(1, 10) <= 8 ? 'completed' : 'pending',
                    'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TICKET-'.Str::random(8),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        // 6. Sinh dữ liệu Đánh giá (Reviews)
        $completedBookings = Booking::where('tour_status', Booking::TOUR_COMPLETED)->get();
        $comments = [
            'Chuyến đi rất tuyệt vời, hướng dẫn viên nhiệt tình và chu đáo!',
            'Dịch vụ ăn uống sạch sẽ, cảnh đẹp vượt ngoài mong đợi.',
            'Lịch trình hợp lý, gia đình tôi rất hài lòng.',
            'Khách sạn 4 sao rất xịn, xe di chuyển êm ái.',
            'Tour tổ chức chuyên nghiệp, nhất định sẽ quay lại lần sau!',
        ];

        foreach ($completedBookings->take(15) as $b) {
            $tourId = $b->tour_schedule->tour_id;
            Review::firstOrCreate(
                [
                    'user_id' => $b->user_id,
                    'tour_id' => $tourId,
                ],
                [
                    'rating' => rand(4, 5),
                    'comment' => $comments[array_rand($comments)],
                    'is_hidden' => false,
                    'created_at' => $b->created_at->addDays(rand(3, 7)),
                ]
            );
        }

        $this->command->info('✅ Đã tạo thành công 100 đơn hàng và dữ liệu thống kê cho Admin Dashboard!');
    }
}
