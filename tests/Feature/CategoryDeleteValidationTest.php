<?php

use App\Models\Category;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $this->adminUser = User::factory()->create(['role' => 'admin']);
    $this->adminUser->assignRole('Admin');
});

test('admin cannot delete category if it has active tours linked', function () {
    $category = Category::create([
        'name' => 'Du lịch Núi',
        'slug' => 'du-lich-nui',
    ]);

    $destination = Destination::create([
        'name' => 'Sa Pa',
        'description' => 'Khám phá vùng cao Sa Pa',
    ]);

    $tour = Tour::create([
        'destination_id' => $destination->id,
        'departure_location_id' => $destination->id,
        'title' => ['vi' => 'Tour khám phá Sa Pa'],
        'description' => ['vi' => 'Mô tả tour khám phá Sa Pa đầy thú vị'],
        'slug' => 'tour-kham-pha-sa-pa',
        'base_price' => 1000000,
        'child_price' => 500000,
        'duration_days' => 3,
        'duration_nights' => 2,
    ]);

    $tour->categories()->attach($category->id);

    $response = $this->actingAs($this->adminUser)
        ->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Không thể xóa danh mục này vì vẫn còn tour đang liên kết với nó!');

    expect(Category::find($category->id))->not->toBeNull();
});

test('admin can delete category if it has no tours linked', function () {
    $category = Category::create([
        'name' => 'Du lịch Biển',
        'slug' => 'du-lich-bien',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Đã xóa danh mục thành công!');

    expect(Category::find($category->id))->toBeNull();
    expect(Category::onlyTrashed()->find($category->id))->not->toBeNull();
});
