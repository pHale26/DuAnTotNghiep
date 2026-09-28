<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use App\Models\UserIdentity;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Seed Spatie Role Admin
    Role::firstOrCreate(['name' => 'Admin']);

    // Create destination
    $destination = Destination::create([
        'name' => 'Đà Nẵng',
        'description' => 'Thành phố đáng sống',
    ]);

    // Create tour
    $this->tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Khám phá Đà Nẵng - Hội An',
        'slug' => 'tour-da-nang-3-ngay-2-dem',
        'description' => 'Hành trình khám phá miền Trung',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3500000,
        'cost_transport' => 2000000,
        'cost_meal' => 500000,
        'cost_insurance' => 500000,
        'cost_service_fee' => 500000,
    ]);

    // Create tour schedule
    $this->schedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => Carbon::now()->addDays(10)->toDateTimeString(),
        'return_date' => Carbon::now()->addDays(12)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);
});

test('guest can access checkout', function () {
    $response = $this->get(route('frontend.tours.checkout', [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertOk();
    $response->assertViewIs('frontend.tours.checkout');
});

test('authenticated user can access checkout with valid parameters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('frontend.tours.checkout', [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertOk();
    $response->assertViewIs('frontend.tours.checkout');
    $response->assertViewHas('totalPrice', 7000000);
});

test('booking stores successfully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('frontend.tours.store'), [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
        'customer_name' => 'Nguyễn Văn A',
        'customer_phone' => '0987654321',
        'customer_email' => 'customer@example.com',
        'customer_email_confirmation' => 'customer@example.com',
        'meeting_point' => 'Sân bay',
        'total_price' => 7000000,
        'issue_date' => '2021-05-18',
        'expiry_date' => '2036-05-18',
        'issue_place' => 'Cục Cảnh sát',
        'passengers' => [
            'adult' => [
                [
                    'full_name' => 'Nguyễn Văn A',
                    'identity_number' => '036123456789',
                    'date_of_birth' => '1996-05-18',
                    'gender' => 'male',
                ],
                [
                    'full_name' => 'Nguyễn Thị B',
                    'identity_number' => '036123456790',
                    'date_of_birth' => '1998-05-18',
                    'gender' => 'female',
                ],
            ],
        ],
        'transport_type' => 'self',
        'payment_type' => 'full',
        'payment_method' => 'transfer',
    ]);

    $this->assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
    ]);

    $booking = Booking::where('user_id', $user->id)->first();
    $response->assertRedirect(route('frontend.tours.booking_success', $booking->id));
    $response->assertSessionHas('success', 'Đặt tour thành công. Bạn tự túc phương tiện di chuyển.');
});

test('booking redirects to vnpay when vnpay payment is chosen', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('frontend.tours.store'), [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
        'customer_name' => 'Nguyễn Văn A',
        'customer_phone' => '0987654321',
        'customer_email' => 'customer@example.com',
        'customer_email_confirmation' => 'customer@example.com',
        'meeting_point' => 'Sân bay',
        'total_price' => 7000000,
        'issue_date' => '2021-05-18',
        'expiry_date' => '2036-05-18',
        'issue_place' => 'Cục Cảnh sát',
        'passengers' => [
            'adult' => [
                [
                    'full_name' => 'Nguyễn Văn A',
                    'identity_number' => '036123456789',
                    'date_of_birth' => '1996-05-18',
                    'gender' => 'male',
                ],
                [
                    'full_name' => 'Nguyễn Thị B',
                    'identity_number' => '036123456790',
                    'date_of_birth' => '1998-05-18',
                    'gender' => 'female',
                ],
            ],
        ],
        'transport_type' => 'self',
        'payment_type' => 'full',
        'payment_method' => 'vnpay',
    ]);

    $this->assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'total_price' => 7000000,
    ]);

    $this->assertDatabaseHas('payments', [
        'amount' => 7000000,
        'payment_method' => 'vnpay',
        'payment_status' => 'pending',
    ]);

    $response->assertRedirect();
    $targetUrl = $response->headers->get('Location');
    expect($targetUrl)->toContain('https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    expect($targetUrl)->toContain('vnp_TmnCode='.config('vnpay.tmn_code'));
});

test('vnpay return handles successful payment correctly', function () {
    $user = User::factory()->create();
    $booking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
        'total_price' => 7000000,
        'adults_count' => 2,
        'booking_status' => 'pending',
    ]);

    $txnRef = $booking->id.'_12345678';
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'amount' => 7000000,
        'payment_method' => 'vnpay',
        'transaction_code' => $txnRef,
        'payment_status' => 'pending',
    ]);

    $params = [
        'vnp_Amount' => '700000000',
        'vnp_BankCode' => 'NCB',
        'vnp_CardType' => 'ATM',
        'vnp_OrderInfo' => 'Thanh toan',
        'vnp_PayDate' => '20260602120000',
        'vnp_ResponseCode' => '00',
        'vnp_TmnCode' => '32Z7UQKT',
        'vnp_TransactionNo' => '12345',
        'vnp_TxnRef' => $txnRef,
    ];

    ksort($params);
    $hashData = '';
    $i = 0;
    foreach ($params as $key => $value) {
        if ($i == 1) {
            $hashData .= '&'.urlencode($key).'='.urlencode($value);
        } else {
            $hashData .= urlencode($key).'='.urlencode($value);
            $i = 1;
        }
    }
    $secureHash = hash_hmac('sha512', $hashData, config('vnpay.hash_secret'));
    $params['vnp_SecureHash'] = $secureHash;

    $response = $this->actingAs($user)->get(route('frontend.tours.vnpay_return', $params));

    $response->assertRedirect(route('user.bookings'));
    $response->assertSessionHas('success', 'Thanh toán đặt tour qua VNPay thành công!');

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'payment_status' => 'success',
    ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'booking_status' => 'confirmed',
    ]);
});

test('vnpay ipn updates payment status correctly', function () {
    $user = User::factory()->create();
    $booking = Booking::create([
        'user_id' => $user->id,
        'tour_schedule_id' => $this->schedule->id,
        'total_price' => 7000000,
        'adults_count' => 2,
        'booking_status' => 'pending',
    ]);

    $txnRef = $booking->id.'_12345678';
    $payment = Payment::create([
        'booking_id' => $booking->id,
        'amount' => 7000000,
        'payment_method' => 'vnpay',
        'transaction_code' => $txnRef,
        'payment_status' => 'pending',
    ]);

    $params = [
        'vnp_Amount' => '700000000',
        'vnp_BankCode' => 'NCB',
        'vnp_CardType' => 'ATM',
        'vnp_OrderInfo' => 'Thanh toan',
        'vnp_PayDate' => '20260602120000',
        'vnp_ResponseCode' => '00',
        'vnp_TmnCode' => '32Z7UQKT',
        'vnp_TransactionNo' => '12345',
        'vnp_TxnRef' => $txnRef,
    ];

    ksort($params);
    $hashData = '';
    $i = 0;
    foreach ($params as $key => $value) {
        if ($i == 1) {
            $hashData .= '&'.urlencode($key).'='.urlencode($value);
        } else {
            $hashData .= urlencode($key).'='.urlencode($value);
            $i = 1;
        }
    }
    $secureHash = hash_hmac('sha512', $hashData, config('vnpay.hash_secret'));
    $params['vnp_SecureHash'] = $secureHash;

    $response = $this->get(route('frontend.tours.vnpay_ipn', $params));

    $response->assertJson([
        'RspCode' => '00',
        'Message' => 'Confirm Success',
    ]);

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'payment_status' => 'success',
    ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'booking_status' => 'confirmed',
    ]);
});

test('authenticated user cannot access checkout for schedule starting within 3 days', function () {
    $user = User::factory()->create();

    // Create a schedule starting 2 days from now
    $closeSchedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => Carbon::now()->addDays(2)->toDateTimeString(),
        'return_date' => Carbon::now()->addDays(4)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    $response = $this->actingAs($user)->get(route('frontend.tours.checkout', [
        'schedule_id' => $closeSchedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Tour khởi hành trong vòng 3 ngày tới không thể đặt trực tuyến. Vui lòng chọn lịch trình khác.');
});

test('booking store fails for schedule starting within 3 days', function () {
    $user = User::factory()->create();

    // Create a schedule starting 2 days from now
    $closeSchedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => Carbon::now()->addDays(2)->toDateTimeString(),
        'return_date' => Carbon::now()->addDays(4)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    $response = $this->actingAs($user)->post(route('frontend.tours.store'), [
        'schedule_id' => $closeSchedule->id,
        'adults' => 2,
        'children' => 0,
        'customer_name' => 'Nguyễn Văn A',
        'customer_phone' => '0987654321',
        'customer_email' => 'customer@example.com',
        'customer_email_confirmation' => 'customer@example.com',
        'meeting_point' => 'Sân bay',
        'total_price' => 7000000,
        'passengers' => [
            'adult' => [
                [
                    'full_name' => 'Nguyễn Văn A',
                    'identity_number' => '036123456789',
                    'date_of_birth' => '1996-05-18',
                    'gender' => 'male',
                ],
                [
                    'full_name' => 'Nguyễn Thị B',
                    'identity_number' => '036123456790',
                    'date_of_birth' => '1998-05-18',
                    'gender' => 'female',
                ],
            ],
        ],
        'transport_type' => 'self',
        'payment_type' => 'full',
        'payment_method' => 'transfer',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Tour khởi hành trong vòng 3 ngày tới không thể đặt trực tuyến. Vui lòng chọn lịch trình khác.');

    $this->assertDatabaseMissing('bookings', [
        'tour_schedule_id' => $closeSchedule->id,
    ]);
});

test('checkout shows identity upload section for multi-day tours', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('frontend.tours.checkout', [
        'schedule_id' => $this->schedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertOk();
    $response->assertViewHas('tour');
    $response->assertSee('upload_cccd_check');
    $response->assertSee('front_image');
    $response->assertSee('back_image');
});

test('checkout hides identity upload section for single day tours', function () {
    $user = User::factory()->create();

    $singleDayTour = Tour::create([
        'destination_id' => $this->tour->destination_id,
        'title' => 'City tour trong ngày',
        'slug' => 'city-tour-trong-ngay',
        'description' => 'Tour tham quan thành phố trong ngày',
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 500000,
        'cost_transport' => 200000,
        'cost_meal' => 150000,
        'cost_insurance' => 50000,
        'cost_service_fee' => 100000,
    ]);

    $singleDaySchedule = TourSchedule::create([
        'tour_id' => $singleDayTour->id,
        'departure_date' => Carbon::now()->addDays(10)->toDateTimeString(),
        'return_date' => Carbon::now()->addDays(10)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    $response = $this->actingAs($user)->get(route('frontend.tours.checkout', [
        'schedule_id' => $singleDaySchedule->id,
        'adults' => 2,
        'children' => 0,
    ]));

    $response->assertOk();
    $response->assertDontSee('upload_cccd_check');
    $response->assertDontSee('front_image');
    $response->assertDontSee('back_image');
});

test('booking stores identity images when uploaded', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $frontImage = UploadedFile::fake()->image('front.jpg');
    $backImage = UploadedFile::fake()->image('back.jpg');

    $response = $this->actingAs($user)->post(route('frontend.tours.store'), [
        'schedule_id' => $this->schedule->id,
        'adults' => 1,
        'children' => 0,
        'customer_name' => 'Nguyễn Văn A',
        'customer_phone' => '0987654321',
        'customer_email' => 'customer@example.com',
        'customer_email_confirmation' => 'customer@example.com',
        'meeting_point' => 'Sân bay',
        'total_price' => 3500000,
        'front_image' => $frontImage,
        'back_image' => $backImage,
        'passengers' => [
            'adult' => [
                [
                    'full_name' => 'Nguyễn Văn A',
                    'identity_number' => '036123456789',
                    'date_of_birth' => '1996-05-18',
                    'gender' => 'male',
                ],
            ],
        ],
        'transport_type' => 'self',
        'payment_type' => 'full',
        'payment_method' => 'transfer',
    ]);

    $identity = UserIdentity::where('user_id', $user->id)->first();
    expect($identity)->not->toBeNull();
    expect($identity->front_image_url)->not->toBeNull();
    expect($identity->back_image_url)->not->toBeNull();

    $response->assertRedirect();
});

test('tour detail page displays correct base price matching tour base price', function () {
    $tour = Tour::create([
        'destination_id' => $this->tour->destination_id,
        'title' => 'Tour Test Giá Đồng Nhất',
        'slug' => 'tour-test-gia-dong-nhat',
        'description' => 'Mô tả tour',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3500000,
        'cost_transport' => 0,
        'cost_meal' => 0,
        'cost_insurance' => 0,
        'cost_service_fee' => 0,
    ]);

    expect($tour->getBasePrice())->toEqual(3500000);

    $response = $this->get(route('frontend.tours.show', $tour->slug));
    $response->assertOk();
    $response->assertSee('3.500.000');
});
