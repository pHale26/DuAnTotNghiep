<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Setup roles
    Role::firstOrCreate(['name' => 'Admin']);

    $this->adminUser = User::factory()->create(['role' => 'admin']);
    $this->adminUser->assignRole('Admin');

    $this->category = Category::create([
        'name' => 'Du lịch Biển',
        'slug' => 'du-lich-bien',
    ]);
});

test('admin can view edit coupon page', function () {
    $coupon = Coupon::create([
        'code' => 'TESTCOUPON',
        'discount_type' => 'percent',
        'discount_value' => 15,
        'valid_from' => now()->toDateString(),
        'valid_until' => now()->addDays(5)->toDateString(),
        'usage_limit' => 10,
        'used_count' => 0,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.coupons.edit', $coupon));

    $response->assertOk();
    $response->assertSee('TESTCOUPON');
    $response->assertSee('Loại tour áp dụng');
});

test('admin can update coupon', function () {
    $coupon = Coupon::create([
        'code' => 'TESTCOUPON',
        'discount_type' => 'percent',
        'discount_value' => 15,
        'valid_from' => now()->toDateString(),
        'valid_until' => now()->addDays(5)->toDateString(),
        'usage_limit' => 10,
        'used_count' => 0,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.coupons.update', $coupon), [
            'code' => 'UPDATED_COUPON',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_value' => 200000,
            'max_discount' => 50000,
            'usage_limit' => 20,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(10)->toDateString(),
            'category_id' => $this->category->id,
        ]);

    $response->assertRedirect(route('admin.coupons.index'));

    $coupon->refresh();
    expect($coupon->code)->toBe('UPDATED_COUPON');
    expect($coupon->discount_type)->toBe('fixed');
    expect((int) $coupon->discount_value)->toBe(50000);
    expect($coupon->category_id)->toBe($this->category->id);
});

test('user checkout only sees coupons matching tour category or null category', function () {
    $user = User::factory()->create();

    $destination = Destination::create([
        'name' => 'Nha Trang',
        'description' => 'Biển xanh cát trắng',
    ]);

    $tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Tour Nha Trang',
        'slug' => 'tour-nha-trang',
        'description' => 'Mô tả',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3000000,
    ]);

    // Attach "Du lịch Biển" category
    $tour->categories()->attach($this->category->id);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(10)->toDateTimeString(),
        'return_date' => now()->addDays(12)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    $otherCategory = Category::create([
        'name' => 'Khám phá Di sản',
        'slug' => 'kham-pha-di-san',
    ]);

    // Create a coupon for "Du lịch Biển"
    $seaCoupon = Coupon::create([
        'code' => 'SEA10',
        'discount_type' => 'percent',
        'discount_value' => 10,
        'valid_from' => now()->toDateString(),
        'valid_until' => now()->addDays(5)->toDateString(),
        'usage_limit' => 10,
        'used_count' => 0,
        'category_id' => $this->category->id,
    ]);

    // Create a coupon for "Khám phá Di sản" (mismatched)
    $heritageCoupon = Coupon::create([
        'code' => 'HERITAGE20',
        'discount_type' => 'percent',
        'discount_value' => 20,
        'valid_from' => now()->toDateString(),
        'valid_until' => now()->addDays(5)->toDateString(),
        'usage_limit' => 10,
        'used_count' => 0,
        'category_id' => $otherCategory->id,
    ]);

    // Create a general coupon (null category)
    $generalCoupon = Coupon::create([
        'code' => 'GEN15',
        'discount_type' => 'percent',
        'discount_value' => 15,
        'valid_from' => now()->toDateString(),
        'valid_until' => now()->addDays(5)->toDateString(),
        'usage_limit' => 10,
        'used_count' => 0,
        'category_id' => null,
    ]);

    $response = $this->actingAs($user)
        ->get(route('frontend.tours.checkout', [
            'schedule_id' => $schedule->id,
            'adults' => 2,
            'children' => 0,
        ]));

    $response->assertOk();

    // Check that we see the compatible coupons and NOT the heritage one
    $couponsInView = $response->viewData('coupons');
    expect($couponsInView->contains($seaCoupon))->toBeTrue();
    expect($couponsInView->contains($generalCoupon))->toBeTrue();
    expect($couponsInView->contains($heritageCoupon))->toBeFalse();
});

test('applying coupon with mismatched category fails validation', function () {
    $user = User::factory()->create();

    $destination = Destination::create([
        'name' => 'Nha Trang',
        'description' => 'Biển xanh cát trắng',
    ]);

    $tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Tour Nha Trang',
        'slug' => 'tour-nha-trang',
        'description' => 'Mô tả',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3000000,
    ]);

    // Attach "Du lịch Biển" category
    $tour->categories()->attach($this->category->id);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => now()->addDays(10)->toDateTimeString(),
        'return_date' => now()->addDays(12)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    $otherCategory = Category::create([
        'name' => 'Khám phá Di sản',
        'slug' => 'kham-pha-di-san',
    ]);

    $heritageCoupon = Coupon::create([
        'code' => 'HERITAGE20',
        'discount_type' => 'percent',
        'discount_value' => 20,
        'valid_from' => now()->toDateString(),
        'valid_until' => now()->addDays(5)->toDateString(),
        'usage_limit' => 10,
        'used_count' => 0,
        'category_id' => $otherCategory->id,
    ]);

    // Test API validation
    $response = $this->actingAs($user)->postJson('/api/coupons/apply', [
        'code' => 'HERITAGE20',
        'order_value' => 6000000,
        'schedule_id' => $schedule->id,
    ]);

    $response->assertJson([
        'success' => false,
        'message' => 'Mã giảm giá không áp dụng cho loại tour này.',
    ]);
});

test('store coupon fails with invalid discount_type', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'TESTCODE',
            'discount_type' => 'invalid_type',
            'discount_value' => 10,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(5)->toDateString(),
        ]);

    $response->assertSessionHasErrors('discount_type');
});

test('store coupon fails when percent discount exceeds 100', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'PERCENT150',
            'discount_type' => 'percent',
            'discount_value' => 150,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(5)->toDateString(),
        ]);

    $response->assertSessionHasErrors('discount_value');
});

test('store coupon fails with special characters in code', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'SALE@25!',
            'discount_type' => 'percent',
            'discount_value' => 25,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(5)->toDateString(),
        ]);

    $response->assertSessionHasErrors('code');
});

test('store coupon fails with code shorter than 3 characters', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'AB',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(5)->toDateString(),
        ]);

    $response->assertSessionHasErrors('code');
});

test('store coupon fails with usage_limit of zero', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'ZERO_LIMIT',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'usage_limit' => 0,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(5)->toDateString(),
        ]);

    $response->assertSessionHasErrors('usage_limit');
});

test('store coupon fails with negative min_order_value', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'NEG_ORDER',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_value' => -100000,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(5)->toDateString(),
        ]);

    $response->assertSessionHasErrors('min_order_value');
});

test('store coupon succeeds with valid data', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'VALID_CODE',
            'discount_type' => 'percent',
            'discount_value' => 25,
            'min_order_value' => 500000,
            'max_discount' => 200000,
            'usage_limit' => 100,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'category_id' => $this->category->id,
        ]);

    $response->assertRedirect(route('admin.coupons.index'));
    $response->assertSessionHas('success');

    $coupon = Coupon::where('code', 'VALID_CODE')->first();
    expect($coupon)->not->toBeNull();
    expect($coupon->discount_type)->toBe('percent');
    expect((float) $coupon->discount_value)->toBe(25.0);
    expect($coupon->used_count)->toBe(0);
});

test('store coupon allows fixed discount value above 100', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.coupons.store'), [
            'code' => 'FIXED_HIGH',
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(10)->toDateString(),
        ]);

    $response->assertRedirect(route('admin.coupons.index'));
    $response->assertSessionHas('success');
});
