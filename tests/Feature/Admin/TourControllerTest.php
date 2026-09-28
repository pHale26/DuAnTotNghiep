<?php

use App\Models\Category;
use App\Models\Destination;
use App\Models\District;
use App\Models\Province;
use App\Models\Tour;
use App\Models\User;
use App\Models\Ward;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup roles
    Role::firstOrCreate(['name' => 'Admin']);

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->admin->assignRole('Admin');
    $this->actingAs($this->admin);

    $this->destination = Destination::create([
        'name' => 'Đà Nẵng',
        'description' => 'Thành phố Đà Nẵng',
    ]);
});

test('admin can create a tour with unique name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);
    $category = Category::first() ?? Category::create([
        'name' => ['vi' => 'Test Category', 'en' => 'Test Category', 'zh' => 'Test Category'],
        'slug' => 'test-category',
    ]);

    $tourData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'description' => [
            'vi' => 'Mô tả tour',
            'en' => 'Tour description',
            'zh' => '旅游描述',
        ],
        'base_price' => 1000000,
        'child_price' => 500000,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
        'categories' => [$category->id],
        'cost_transport' => 200000,
        'cost_meal' => 300000,
        'cost_insurance' => 50000,
        'cost_service_fee' => 100000,
    ];

    $response = $this->post(route('admin.tours.store'), $tourData);

    $response->assertRedirect();
    $this->assertDatabaseHas('tours', [
        'title->vi' => 'Tour Du Lịch Đà Nẵng',
    ]);
});

test('admin cannot create tour with duplicate Vietnamese name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);

    // Create first tour
    Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'slug' => 'tour-du-lich-da-nang-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    // Try to create second tour with same Vietnamese name
    $tourData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Different Name',
            'zh' => '不同的名字',
        ],
        'description' => [
            'vi' => 'Mô tả tour',
            'en' => 'Tour description',
            'zh' => '旅游描述',
        ],
        'base_price' => 1000000,
        'child_price' => 500000,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
        'cost_transport' => 0,
        'cost_meal' => 0,
        'cost_insurance' => 0,
        'cost_service_fee' => 0,
    ];

    $response = $this->post(route('admin.tours.store'), $tourData);

    $response->assertSessionHasErrors('title.vi');
    expect(session('errors')->get('title.vi')[0])
        ->toBe('Tên tour (Tiếng Việt) đã tồn tại. Vui lòng chọn tên khác.');
});

test('admin can update tour while keeping same name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);

    $tour = Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'slug' => 'tour-du-lich-da-nang-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    $updateData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng', // Same name
            'en' => 'Updated Da Nang Tour',
            'zh' => '更新岘港之旅',
        ],
        'description' => [
            'vi' => 'Mô tả tour cập nhật',
            'en' => 'Updated tour description',
            'zh' => '更新旅游描述',
        ],
        'base_price' => 1500000,
        'child_price' => 750000,
        'meeting_point' => 'Cổng công viên Thống Nhất Cập Nhật',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
        'cost_transport' => 200000,
        'cost_meal' => 300000,
        'cost_insurance' => 50000,
        'cost_service_fee' => 100000,
    ];

    $response = $this->put(route('admin.tours.update', $tour->id), $updateData);

    $response->assertRedirect(route('admin.tours.index'));
    $this->assertDatabaseHas('tours', [
        'id' => $tour->id,
        'title->vi' => 'Tour Du Lịch Đà Nẵng',
        'title->en' => 'Updated Da Nang Tour',
    ]);
});

test('admin cannot update tour to duplicate name', function () {
    $province = Province::first() ?? Province::create(['name' => 'Test Province']);
    $ward = Ward::first() ?? Ward::create([
        'name' => 'Test Ward',
        'province_id' => $province->id,
        'district_id' => District::first()?->id ?? 1,
    ]);

    // Create first tour
    Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng',
            'en' => 'Da Nang Tour',
            'zh' => '岘港之旅',
        ],
        'slug' => 'tour-du-lich-da-nang-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    // Create second tour
    $tour2 = Tour::create([
        'title' => [
            'vi' => 'Tour Du Lịch Hà Nội',
            'en' => 'Ha Noi Tour',
            'zh' => '河内之旅',
        ],
        'slug' => 'tour-du-lich-ha-noi-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 1000000,
        'duration_days' => 3,
        'duration_nights' => 2,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
    ]);

    // Try to update second tour to have same name as first tour
    $updateData = [
        'title' => [
            'vi' => 'Tour Du Lịch Đà Nẵng', // Duplicate name
            'en' => 'Updated Tour',
            'zh' => '更新之旅',
        ],
        'description' => [
            'vi' => 'Mô tả tour',
            'en' => 'Tour description',
            'zh' => '旅游描述',
        ],
        'base_price' => 1000000,
        'child_price' => 500000,
        'meeting_point' => 'Cổng công viên Thống Nhất',
        'destination_id' => $this->destination->id,
        'duration_days' => 3,
        'duration_nights' => 2,
        'departure_hour' => 8,
        'departure_minute' => 30,
        'cost_transport' => 0,
        'cost_meal' => 0,
        'cost_insurance' => 0,
        'cost_service_fee' => 0,
    ];

    $response = $this->put(route('admin.tours.update', $tour2->id), $updateData);

    $response->assertSessionHasErrors('title.vi');
    expect(session('errors')->get('title.vi')[0])
        ->toBe('Tên tour (Tiếng Việt) đã tồn tại. Vui lòng chọn tên khác.');
});

test('admin can create tour with cost breakdown fields', function () {
    $tourData = [
        'title' => [
            'vi' => 'Tour Chi Phí Test',
            'en' => 'Cost Test Tour',
            'zh' => '费用测试旅游',
        ],
        'description' => [
            'vi' => 'Mô tả',
            'en' => 'Description',
            'zh' => '描述',
        ],
        'meeting_point' => 'Điểm tập kết',
        'destination_id' => $this->destination->id,
        'duration_days' => 2,
        'duration_nights' => 1,
        'cost_transport' => 500000,
        'cost_meal' => 300000,
        'cost_insurance' => 100000,
        'cost_service_fee' => 200000,
    ];

    $response = $this->post(route('admin.tours.store'), $tourData);

    $response->assertRedirect();

    $tour = Tour::where('title->vi', 'Tour Chi Phí Test')->first();
    expect($tour)->not->toBeNull();
    expect((float) $tour->cost_transport)->toBe(500000.0);
    expect((float) $tour->cost_meal)->toBe(300000.0);
    expect((float) $tour->cost_insurance)->toBe(100000.0);
    expect((float) $tour->cost_service_fee)->toBe(200000.0);
    // base_price should be auto-calculated: 500k + 300k + 100k + 200k = 1,100,000
    expect((float) $tour->base_price)->toBe(1100000.0);
});

test('admin can update tour cost breakdown fields', function () {
    $tour = Tour::create([
        'title' => ['vi' => 'Tour Update Cost', 'en' => 'Update Cost Tour', 'zh' => '更新费用'],
        'slug' => 'tour-update-cost-'.time(),
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 0,
        'duration_days' => 2,
        'duration_nights' => 1,
        'meeting_point' => 'Điểm tập kết',
        'destination_id' => $this->destination->id,
        'cost_transport' => 0,
        'cost_meal' => 0,
        'cost_insurance' => 0,
        'cost_service_fee' => 0,
    ]);

    $updateData = [
        'title' => ['vi' => 'Tour Update Cost', 'en' => 'Update Cost Tour', 'zh' => '更新费用'],
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'base_price' => 0,
        'child_price' => 0,
        'meeting_point' => 'Điểm tập kết',
        'destination_id' => $this->destination->id,
        'duration_days' => 2,
        'duration_nights' => 1,
        'cost_transport' => 800000,
        'cost_meal' => 400000,
        'cost_insurance' => 150000,
        'cost_service_fee' => 250000,
    ];

    $response = $this->put(route('admin.tours.update', $tour->id), $updateData);

    $tour->refresh();
    expect((float) $tour->cost_transport)->toBe(800000.0);
    expect((float) $tour->cost_meal)->toBe(400000.0);
    expect((float) $tour->cost_insurance)->toBe(150000.0);
    expect((float) $tour->cost_service_fee)->toBe(250000.0);
    // base_price recalculated: 800k + 400k + 150k + 250k = 1,600,000
    expect((float) $tour->base_price)->toBe(1600000.0);
});

test('cost breakdown validation rejects negative values', function () {
    $tourData = [
        'title' => ['vi' => 'Tour Negative Cost', 'en' => 'Negative', 'zh' => '负数'],
        'description' => ['vi' => 'Test', 'en' => 'Test', 'zh' => 'Test'],
        'meeting_point' => 'Điểm tập kết',
        'destination_id' => $this->destination->id,
        'duration_days' => 2,
        'duration_nights' => 1,
        'cost_transport' => -100000,
        'cost_meal' => 300000,
        'cost_insurance' => 100000,
        'cost_service_fee' => 200000,
    ];

    $response = $this->post(route('admin.tours.store'), $tourData);

    $response->assertSessionHasErrors('cost_transport');
});
