<?php

use App\Models\Destination;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup roles
    Role::firstOrCreate(['name' => 'Admin']);

    $this->adminUser = User::factory()->create(['role' => 'admin']);
    $this->adminUser->assignRole('Admin');
    $this->actingAs($this->adminUser);
});

test('admin can create destination with unique name', function () {
    $response = $this->post(route('admin.destinations.store'), [
        'name' => [
            'vi' => 'Hạ Long',
            'en' => 'Ha Long',
            'zh' => '下龙',
        ],
        'description' => [
            'vi' => 'Vịnh Hạ Long tuyệt đẹp',
            'en' => 'Beautiful Ha Long Bay',
            'zh' => '美丽的下龙湾',
        ],
    ]);

    $response->assertRedirect(route('admin.destinations.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('destinations', [
        'name->vi' => 'Hạ Long',
    ]);
});

test('admin cannot create destination with duplicate Vietnamese name', function () {
    // Tạo destination đầu tiên
    Destination::create([
        'name' => [
            'vi' => 'Đà Nẵng',
            'en' => 'Da Nang',
            'zh' => '岘港',
        ],
        'description' => 'Test',
    ]);

    // Thử tạo destination với cùng tên tiếng Việt
    $response = $this->post(route('admin.destinations.store'), [
        'name' => [
            'vi' => 'Đà Nẵng',
            'en' => 'Danang City',
            'zh' => '岘港市',
        ],
        'description' => [
            'vi' => 'Một mô tả khác',
        ],
    ]);

    $response->assertSessionHasErrors('name.vi');
    expect(session('errors')->first('name.vi'))->toBe('Tên điểm đến này đã tồn tại.');
});

test('admin can update destination with same name', function () {
    $destination = Destination::create([
        'name' => [
            'vi' => 'Nha Trang',
            'en' => 'Nha Trang',
            'zh' => '芽庄',
        ],
        'description' => 'Biển đẹp',
    ]);

    // Cập nhật với cùng tên (không nên báo lỗi)
    $response = $this->put(route('admin.destinations.update', $destination), [
        'name' => [
            'vi' => 'Nha Trang',
            'en' => 'Nha Trang City',
            'zh' => '芽庄市',
        ],
        'description' => [
            'vi' => 'Biển xanh cát trắng',
        ],
    ]);

    $response->assertRedirect(route('admin.destinations.index'));
    $response->assertSessionHas('success');
});

test('admin cannot update destination with name that already exists', function () {
    // Tạo 2 destinations
    $destination1 = Destination::create([
        'name' => [
            'vi' => 'Hà Nội',
            'en' => 'Hanoi',
            'zh' => '河内',
        ],
        'description' => 'Thủ đô',
    ]);

    $destination2 = Destination::create([
        'name' => [
            'vi' => 'Sài Gòn',
            'en' => 'Saigon',
            'zh' => '西贡',
        ],
        'description' => 'Thành phố lớn nhất',
    ]);

    // Thử cập nhật destination2 với tên của destination1
    $response = $this->put(route('admin.destinations.update', $destination2), [
        'name' => [
            'vi' => 'Hà Nội',
            'en' => 'Hanoi Capital',
            'zh' => '河内首都',
        ],
        'description' => [
            'vi' => 'Mô tả mới',
        ],
    ]);

    $response->assertSessionHasErrors('name.vi');
    expect(session('errors')->first('name.vi'))->toBe('Tên điểm đến này đã tồn tại.');
});

test('destination name validation is case sensitive', function () {
    // Tạo destination với tên viết hoa
    Destination::create([
        'name' => [
            'vi' => 'Đà Lạt',
            'en' => 'Dalat',
            'zh' => '大叻',
        ],
        'description' => 'Thành phố ngàn hoa',
    ]);

    // Thử tạo với tên giống hệt (case-sensitive)
    $response = $this->post(route('admin.destinations.store'), [
        'name' => [
            'vi' => 'Đà Lạt',
            'en' => 'Da Lat',
            'zh' => '大叻市',
        ],
        'description' => [
            'vi' => 'Mô tả khác',
        ],
    ]);

    $response->assertSessionHasErrors('name.vi');
});
