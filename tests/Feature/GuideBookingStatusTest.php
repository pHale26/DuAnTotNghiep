<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Destination;
use App\Models\ScheduleGuide;
use App\Models\Tour;
use App\Models\TourGuide;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

/**
 * Tạo đầy đủ dữ liệu: user guide + tour guide profile + schedule + booking
 *
 * @return array{guideUser: User, tourGuide: TourGuide, schedule: TourSchedule, booking: Booking}
 */
function setupGuideScenario(string $tourStatus = Booking::TOUR_IN_PROGRESS): array
{
    Role::firstOrCreate(['name' => 'Admin']);

    $destination = Destination::create([
        'name' => 'Địa danh Test '.uniqid(),
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

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => Carbon::today()->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(2)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 18,
        'status' => 'available',
    ]);

    // User có role guide
    $guideUser = User::factory()->create(['role' => 'guide']);
    $tourGuide = TourGuide::create([
        'user_id' => $guideUser->id,
        'name' => $guideUser->name,
        'phone' => '0900000001',
        'email' => $guideUser->email,
    ]);

    // Gán HDV vào lịch trình
    ScheduleGuide::create([
        'tour_schedule_id' => $schedule->id,
        'guide_id' => $tourGuide->id,
    ]);

    // Tạo booking đã thanh toán với trạng thái tour cho trước
    $customer = User::factory()->create();
    $booking = Booking::create([
        'user_id' => $customer->id,
        'tour_schedule_id' => $schedule->id,
        'total_price' => 3500000,
        'adults_count' => 2,
        'children_count' => 0,
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => $tourStatus,
    ]);

    return compact('guideUser', 'tourGuide', 'schedule', 'booking');
}

// ─── ADMIN bị khóa ──────────────────────────────────────────────────────────

test('admin có thể thay đổi tour_status khi booking đang in_progress', function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    ['booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    $response = $this->actingAs($admin)->post(route('admin.bookings.update_status', $booking->id), [
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_COMPLETED,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_COMPLETED);
});

test('admin có thể thay đổi tour_status khi booking đang checking_in', function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    ['booking' => $booking] = setupGuideScenario(Booking::TOUR_CHECKING_IN);

    $this->actingAs($admin)->post(route('admin.bookings.update_status', $booking->id), [
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_COMPLETED,
    ])->assertSessionHas('success');

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_COMPLETED);
});

test('admin vẫn có thể cập nhật payment_status khi tour đang in_progress', function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    ['booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    // Gửi tour_status giống hiện tại (không thay đổi)
    $this->actingAs($admin)->post(route('admin.bookings.update_status', $booking->id), [
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_IN_PROGRESS,
    ])->assertSessionHas('success');

    expect($booking->fresh()->payment_status)->toBe(Booking::PAYMENT_PAID_100);
    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_IN_PROGRESS);
});

test('admin vẫn có thể thay đổi tour_status khi booking còn ở upcoming', function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    ['booking' => $booking] = setupGuideScenario(Booking::TOUR_UPCOMING);

    $this->actingAs($admin)->post(route('admin.bookings.update_status', $booking->id), [
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_CANCELLED_ADMIN,
    ])->assertSessionHas('success');

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_CANCELLED_ADMIN);
});

// ─── GUIDE có quyền ─────────────────────────────────────────────────────────

test('guide có thể chuyển booking từ in_progress sang checking_in', function () {
    ['guideUser' => $guideUser, 'booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    $this->actingAs($guideUser)->post(route('guide.bookings.update_status', $booking->id), [
        'tour_status' => Booking::TOUR_CHECKING_IN,
        'current_checkin_step' => 'Sân bay Nội Bài',
    ])->assertRedirect()->assertSessionHas('success');

    $booking->refresh();
    expect($booking->tour_status)->toBe(Booking::TOUR_CHECKING_IN);
    expect($booking->current_checkin_step)->toBe('Sân bay Nội Bài');
});

test('guide có thể hoàn thành tour (completed)', function () {
    ['guideUser' => $guideUser, 'booking' => $booking] = setupGuideScenario(Booking::TOUR_CHECKING_IN);

    $this->actingAs($guideUser)->post(route('guide.bookings.update_status', $booking->id), [
        'tour_status' => Booking::TOUR_COMPLETED,
    ])->assertRedirect()->assertSessionHas('success');

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_COMPLETED);
});

test('guide không thể chuyển booking đã completed', function () {
    ['guideUser' => $guideUser, 'booking' => $booking] = setupGuideScenario(Booking::TOUR_COMPLETED);

    $this->actingAs($guideUser)->post(route('guide.bookings.update_status', $booking->id), [
        'tour_status' => Booking::TOUR_IN_PROGRESS,
    ])->assertForbidden();

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_COMPLETED);
});

test('guide không thể thao tác booking của lịch trình không được giao', function () {
    // Booking thuộc schedule khác, guide này không được giao
    $destination = Destination::create(['name' => 'Khác '.uniqid(), 'description' => '']);
    $tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Tour khác',
        'slug' => 'tour-khac-'.uniqid(),
        'description' => '',
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 1000000,
    ]);
    $otherSchedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => Carbon::today()->toDateTimeString(),
        'return_date' => Carbon::today()->addDays(1)->toDateTimeString(),
        'capacity' => 10,
        'available_seats' => 10,
        'status' => 'available',
    ]);
    $customer = User::factory()->create();
    $otherBooking = Booking::create([
        'user_id' => $customer->id,
        'tour_schedule_id' => $otherSchedule->id,
        'total_price' => 1000000,
        'adults_count' => 1,
        'children_count' => 0,
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_IN_PROGRESS,
    ]);

    // Guide được giao lịch khác, không phải $otherSchedule
    ['guideUser' => $guideUser] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    $this->actingAs($guideUser)->post(route('guide.bookings.update_status', $otherBooking->id), [
        'tour_status' => Booking::TOUR_COMPLETED,
    ])->assertForbidden();
});

test('khách hàng bình thường không thể gọi route guide.bookings.update_status', function () {
    ['booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    $customer = User::factory()->create();

    $this->actingAs($customer)->post(route('guide.bookings.update_status', $booking->id), [
        'tour_status' => Booking::TOUR_COMPLETED,
    ])->assertForbidden();
});

test('guide co the cap nhat checkin location ma khong reset trang thai diem danh cua hanh khach', function () {
    ['guideUser' => $guideUser, 'schedule' => $schedule, 'booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    // Tạo hành khách cho booking và đánh dấu đã điểm danh
    $passenger = BookingPassenger::create([
        'booking_id' => $booking->id,
        'full_name' => 'Nguyễn Văn A',
        'passenger_type' => 'adult',
        'gender' => 'male',
        'checked_in' => true,
    ]);

    // Gọi API cập nhật địa điểm check-in mới
    $response = $this->actingAs($guideUser)->postJson(route('guide.schedules.update_checkin_location', $schedule->id), [
        'location' => 'Điểm check-in mới',
    ]);

    $response->assertOk();
    $response->assertJson([
        'location' => 'Điểm check-in mới',
    ]);

    // Đảm bảo không bị reset checked_in của passenger
    expect($passenger->fresh()->checked_in)->toBeTrue();
});

test('guide co the chuyen trang thai tu in_progress sang completed truc tiep', function () {
    ['guideUser' => $guideUser, 'booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    // in_progress được phép chuyển trực tiếp sang hoàn thành (completed) theo yêu cầu mới
    $this->actingAs($guideUser)->post(route('guide.bookings.update_status', $booking->id), [
        'tour_status' => Booking::TOUR_COMPLETED,
    ])->assertRedirect()->assertSessionHas('success');

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_COMPLETED);
});

test('admin có thể chuyển trạng thái tour tùy ý từ upcoming sang completed', function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    ['booking' => $booking] = setupGuideScenario(Booking::TOUR_UPCOMING);

    $this->actingAs($admin)->post(route('admin.bookings.update_status', $booking->id), [
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_COMPLETED,
    ])->assertRedirect()->assertSessionHas('success');

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_COMPLETED);
});

test('guide co the luu diem danh hang loat cho hanh khach', function () {
    ['guideUser' => $guideUser, 'schedule' => $schedule, 'booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    $passenger1 = BookingPassenger::create([
        'booking_id' => $booking->id,
        'full_name' => 'Khách A',
        'passenger_type' => 'adult',
        'gender' => 'male',
        'checked_in' => false,
    ]);

    $passenger2 = BookingPassenger::create([
        'booking_id' => $booking->id,
        'full_name' => 'Khách B',
        'passenger_type' => 'adult',
        'gender' => 'female',
        'checked_in' => true,
    ]);

    $this->actingAs($guideUser)->post(route('guide.schedules.save_attendance', $schedule->id), [
        'checked_passengers' => [$passenger1->id], // chỉ check khách A, bỏ check khách B
    ])->assertRedirect()->assertSessionHas('success');

    expect($passenger1->fresh()->checked_in)->toBeTrue();
    expect($passenger2->fresh()->checked_in)->toBeFalse();
});

test('guide co the cap nhat trang thai tour doan hang loat', function () {
    ['guideUser' => $guideUser, 'schedule' => $schedule, 'booking' => $booking] = setupGuideScenario(Booking::TOUR_IN_PROGRESS);

    // Thêm một booking thứ hai cho lịch trình này
    $booking2 = Booking::create([
        'user_id' => User::factory()->create()->id,
        'tour_schedule_id' => $schedule->id,
        'tour_status' => Booking::TOUR_IN_PROGRESS,
        'payment_status' => Booking::PAYMENT_PAID_100,
        'adults_count' => 1,
        'children_count' => 0,
        'sub_total' => 1000000,
        'total_price' => 1000000,
    ]);

    $this->actingAs($guideUser)->post(route('guide.schedules.update_group_status', $schedule->id), [
        'tour_status' => Booking::TOUR_CHECKING_IN,
        'current_checkin_step' => 'Trạm kiểm soát số 1',
    ])->assertRedirect()->assertSessionHas('success');

    expect($booking->fresh()->tour_status)->toBe(Booking::TOUR_CHECKING_IN);
    expect($booking->fresh()->current_checkin_step)->toBe('Trạm kiểm soát số 1');

    expect($booking2->fresh()->tour_status)->toBe(Booking::TOUR_CHECKING_IN);
    expect($booking2->fresh()->current_checkin_step)->toBe('Trạm kiểm soát số 1');
});
