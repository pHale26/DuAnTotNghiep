<?php

use App\Models\Accommodation;
use App\Models\Destination;
use App\Models\Province;
use App\Models\RoomType;
use App\Models\Tour;
use App\Models\User;
use App\Models\Ward;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup roles
    Role::firstOrCreate(['name' => 'Admin']);

    $this->adminUser = User::factory()->create(['role' => 'admin']);
    $this->adminUser->assignRole('Admin');

    $this->destination = Destination::create([
        'name' => 'Hà Nội',
        'description' => 'Thủ đô Hà Nội',
    ]);
});

test('admin can store tour with departure time', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.tours.store'), [
            'title' => [
                'vi' => 'Tour khám phá Hà Nội mới',
                'en' => 'New Hanoi Tour',
                'zh' => 'New Hanoi Tour ZH',
            ],
            'description' => [
                'vi' => 'Chi tiết tour Hà Nội',
            ],
            'base_price' => 2000000,
            'meeting_point' => 'Cổng phụ công viên Thống Nhất',
            'destination_id' => $this->destination->id,
            'duration_days' => 2,
            'duration_nights' => 1,
            'departure_hour' => 7,
            'departure_minute' => 30,
        ]);

    $response->assertRedirect();

    $tour = Tour::where('slug', 'like', 'tour-kham-pha-ha-noi-moi%')->first();
    expect($tour)->not->toBeNull();
    expect($tour->departure_time)->toBe('07:30:00');
});

test('admin can update tour with departure time', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
    ]);

    $tour = Tour::create([
        'meeting_point' => 'Cổng công viên',
        'destination_id' => $this->destination->id,
        'title' => ['vi' => 'Tour Cũ'],
        'slug' => 'tour-cu',
        'description' => ['vi' => 'Mô tả tour cũ'],
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 1000000,
        'departure_time' => '08:00:00',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.tours.update', $tour->id), [
            'title' => [
                'vi' => 'Tour Cũ Cập Nhật',
            ],
            'base_price' => 1200000,
            'meeting_point' => 'Cổng phụ công viên Thống Nhất Cập Nhật',
            'destination_id' => $this->destination->id,
            'duration_days' => 1,
            'duration_nights' => 0,
            'departure_hour' => 21,
            'departure_minute' => 15,
        ]);

    $response->assertRedirect();

    $tour->refresh();
    expect($tour->departure_time)->toBe('21:15:00');
});

test('admin can store schedule for a one day tour with same departure and return date', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
    ]);

    $tour = Tour::create([
        'meeting_point' => 'Cổng công viên',
        'destination_id' => $this->destination->id,
        'title' => ['vi' => 'Tour 1 Ngày'],
        'slug' => 'tour-1-ngay',
        'description' => ['vi' => 'Mô tả tour 1 ngày'],
        'duration_days' => 1,
        'duration_nights' => 0,
        'base_price' => 500000,
        'departure_time' => '08:00:00',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.tours.schedules.store', $tour->id), [
            'departure_date' => '2026-08-01',
            'return_date' => '2026-08-01',
            'capacity' => 20,
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('tour_schedules', [
        'tour_id' => $tour->id,
        'departure_date' => '2026-08-01 00:00:00',
        'return_date' => '2026-08-01 00:00:00',
        'capacity' => 20,
    ]);
});

test('admin can store tour with matching accommodation destination and tiers are populated', function () {
    $accommodation = Accommodation::create([
        'destination_id' => $this->destination->id,
        'name' => 'Khách sạn Hà Nội',
        'address' => '123 Đường Hà Nội',
        'star_rating' => 4,
        'is_active' => true,
    ]);
    $roomType1 = RoomType::create([
        'accommodation_id' => $accommodation->id,
        'name' => 'Standard Room',
        'base_price' => 500000,
    ]);
    $roomType2 = RoomType::create([
        'accommodation_id' => $accommodation->id,
        'name' => 'VIP Room',
        'base_price' => 1000000,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.tours.store'), [
            'title' => [
                'vi' => 'Tour kiểm thử lưu trú hợp lệ',
            ],
            'base_price' => 2000000,
            'meeting_point' => 'Hà Nội',
            'destination_id' => $this->destination->id,
            'accommodation_id' => $accommodation->id,
            'duration_days' => 2,
            'duration_nights' => 1,
        ]);

    $response->assertRedirect();
    $tour = Tour::where('slug', 'like', 'tour-kiem-thu-luu-tru-hop-le%')->first();
    expect($tour)->not->toBeNull();
    expect($tour->accommodation_tiers)->toHaveCount(2);
});

test('admin cannot store tour with mismatching accommodation destination', function () {
    $otherDestination = Destination::create([
        'name' => 'Hồ Chí Minh',
        'description' => 'Sài Gòn',
    ]);
    $accommodation = Accommodation::create([
        'destination_id' => $otherDestination->id,
        'name' => 'Khách sạn Sài Gòn',
        'address' => '123 Đường Sài Gòn',
        'star_rating' => 4,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->from(route('admin.tours.create'))
        ->post(route('admin.tours.store'), [
            'title' => [
                'vi' => 'Tour kiểm thử lưu trú sai điểm đến',
            ],
            'base_price' => 2000000,
            'meeting_point' => 'Hà Nội',
            'destination_id' => $this->destination->id, // Hà Nội
            'accommodation_id' => $accommodation->id, // Hồ Chí Minh
            'duration_days' => 2,
            'duration_nights' => 1,
        ]);

    $response->assertRedirect(route('admin.tours.create'));
    $response->assertSessionHasErrors(['accommodation_id']);
});
