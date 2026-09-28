<?php

use App\Models\Holiday;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Admin']);
    $this->adminUser = User::factory()->create(['role' => 'admin']);
    $this->adminUser->assignRole('Admin');
});

test('holiday creation fails if end_date is equal to start_date', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.holidays.store'), [
            'name' => 'Tết Dương Lịch',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'price_increase_percentage' => 30,
        ]);

    $response->assertSessionHasErrors(['end_date']);
    expect(Holiday::count())->toBe(0);
});

test('holiday creation succeeds if end_date is at least 1 day after start_date', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.holidays.store'), [
            'name' => 'Tết Dương Lịch',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(6)->toDateString(),
            'price_increase_percentage' => 30,
        ]);

    $response->assertRedirect(route('admin.holidays.index'));
    $response->assertSessionHas('success', 'Đã thêm ngày lễ thành công.');
    expect(Holiday::count())->toBe(1);
});

test('holiday creation fails if surcharge is greater than 100', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.holidays.store'), [
            'name' => 'Lễ Quốc Khánh',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'price_increase_percentage' => 120,
        ]);

    $response->assertSessionHasErrors(['price_increase_percentage']);
    expect(Holiday::count())->toBe(0);
});
