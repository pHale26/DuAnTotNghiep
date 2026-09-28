<?php

use App\Models\GroupSplit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('requires mandatory fields for creating group split', function () {
    $response = $this->postJson('/api/group-splits', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'tour_id',
            'guest_id',
            'guest_name',
            'reason',
            'phone_number',
            'start_time',
            'end_time',
            'return_location',
        ]);
});

it('validates start_time is not in the past', function () {
    $response = $this->postJson('/api/group-splits', [
        'tour_id' => 1,
        'guest_id' => 1,
        'guest_name' => 'Nguyen Van A',
        'reason' => 'Test',
        'phone_number' => '0912345678',
        'start_time' => Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s'),
        'end_time' => Carbon::now()->addHours(1)->format('Y-m-d H:i:s'),
        'return_location' => 'Hotel',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['start_time']);
});

it('validates end_time is after start_time', function () {
    $startTime = Carbon::now()->addMinutes(10);
    $response = $this->postJson('/api/group-splits', [
        'tour_id' => 1,
        'guest_id' => 1,
        'guest_name' => 'Nguyen Van A',
        'reason' => 'Test',
        'phone_number' => '0912345678',
        'start_time' => $startTime->format('Y-m-d H:i:s'),
        'end_time' => $startTime->copy()->subMinutes(5)->format('Y-m-d H:i:s'), // end_time before start_time
        'return_location' => 'Hotel',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['end_time']);
});

it('validates vietnamese phone number format', function () {
    $response = $this->postJson('/api/group-splits', [
        'tour_id' => 1,
        'guest_id' => 1,
        'guest_name' => 'Nguyen Van A',
        'reason' => 'Test',
        'phone_number' => '12345678', // Invalid VN phone
        'start_time' => Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s'),
        'end_time' => Carbon::now()->addHours(1)->format('Y-m-d H:i:s'),
        'return_location' => 'Hotel',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phone_number']);
});

it('prevents overlapping active splits for the same guest', function () {
    $guestId = 999;

    // Create an active split
    GroupSplit::create([
        'tour_id' => 1,
        'guest_id' => $guestId,
        'guest_name' => 'Test Guest',
        'reason' => 'Testing',
        'phone_number' => '0987654321',
        'start_time' => Carbon::now(),
        'end_time' => Carbon::now()->addHour(),
        'return_location' => 'Somewhere',
        'status' => GroupSplit::STATUS_ON_TIME,
        'split_started_at' => Carbon::now(),
    ]);

    $response = $this->postJson('/api/group-splits', [
        'tour_id' => 1,
        'guest_id' => $guestId, // Same guest
        'guest_name' => 'Test Guest',
        'reason' => 'New split',
        'phone_number' => '0912345678',
        'start_time' => Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s'),
        'end_time' => Carbon::now()->addHours(1)->format('Y-m-d H:i:s'),
        'return_location' => 'Hotel',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['guest_id']);
});

it('successfully creates a group split and sets status and split_started_at', function () {
    $response = $this->postJson('/api/group-splits', [
        'tour_id' => 1,
        'guest_id' => 100,
        'guest_name' => 'Nguyen Van B',
        'reason' => 'Shopping',
        'phone_number' => '0987654321',
        'start_time' => Carbon::now()->addMinutes(1)->format('Y-m-d H:i:s'),
        'end_time' => Carbon::now()->addHours(2)->format('Y-m-d H:i:s'),
        'return_location' => 'Center Mall',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('group_splits', [
        'guest_id' => 100,
        'status' => 'ON_TIME',
    ]);
});
