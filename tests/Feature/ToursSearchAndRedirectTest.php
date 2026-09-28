<?php

use Carbon\Carbon;

test('tours search route redirects to tours index page with query parameters', function () {
    $response = $this->get('/tours/search?keyword=hanoi&date=2026-08-01');

    $response->assertRedirect('/tour-tron-goi?keyword=hanoi&date=2026-08-01');
});

test('tours index page is accessible', function () {
    $response = $this->get('/tour-tron-goi');

    $response->assertOk();
    $response->assertViewIs('frontend.tours.index');
});

test('tours index page returns validation error and zero results if search date is less than 3 days from today', function () {
    $blockedDate = Carbon::today()->addDays(2)->format('m/d/Y');

    $response = $this->get('/tour-tron-goi?date='.$blockedDate);

    $response->assertOk();
    $response->assertViewHas('filterErrors');
    $errors = $response->viewData('filterErrors');
    expect($errors)->toHaveKey('departure_date');

    $tours = $response->viewData('tours');
    expect($tours->total())->toBe(0);
});
