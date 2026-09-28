<?php

use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourGuide;
use App\Models\TourReport;
use App\Models\TourSchedule;
use App\Models\User;
use App\Notifications\Guide\TourReportApprovedNotification;
use App\Notifications\Guide\TourReportRejectedNotification;
use App\Notifications\Guide\TourReportSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Tạo Spatie Role và gán cho Admin
    Role::create(['name' => 'Admin']);
    $this->adminUser = User::factory()->create();
    $this->adminUser->assignRole('Admin');

    // Tạo HDV User với role 'guide'
    $this->guideUser = User::factory()->create([
        'role' => 'guide',
    ]);

    $this->guide = TourGuide::create([
        'user_id' => $this->guideUser->id,
        'name' => 'HDV Test',
        'phone' => '0987654321',
        'email' => $this->guideUser->email,
    ]);

    $this->destination = Destination::create([
        'name' => 'Hạ Long',
        'description' => 'Vịnh Hạ Long',
    ]);

    $this->tour = Tour::create([
        'title' => ['vi' => 'Tour Hạ Long'],
        'slug' => 'tour-ha-long',
        'description' => ['vi' => 'Mô tả'],
        'duration_days' => 2,
        'duration_nights' => 1,
        'base_price' => 2000000,
        'destination_id' => $this->destination->id,
        'departure_location_id' => $this->destination->id,
    ]);

    $this->schedule = TourSchedule::create([
        'tour_id' => $this->tour->id,
        'departure_date' => now()->subDays(2)->toDateTimeString(),
        'return_date' => now()->subDays(1)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'completed',
    ]);

    // Gán HDV cho lịch trình
    $this->schedule->schedule_guides()->create([
        'guide_id' => $this->guide->id,
        'is_backup' => false,
    ]);
});

test('guide submitting tour report dispatches notification to guide', function () {
    Notification::fake();

    $response = $this->actingAs($this->guideUser)
        ->post(route('guide.reports.store', $this->schedule->id), [
            'actual_guests' => 15,
            'incident_notes' => 'Không có sự cố.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $report = TourReport::where('tour_schedule_id', $this->schedule->id)->first();
    expect($report)->not->toBeNull();

    Notification::assertSentTo($this->guideUser, TourReportSubmittedNotification::class);
});

test('admin approving tour report dispatches notification to guide', function () {
    Notification::fake();

    $report = TourReport::create([
        'tour_schedule_id' => $this->schedule->id,
        'guide_id' => $this->guide->id,
        'actual_guests' => 15,
        'incident_notes' => 'Không có sự cố.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.reports.approve', $report->id));

    $response->assertRedirect();

    Notification::assertSentTo($this->guideUser, TourReportApprovedNotification::class);
});

test('admin rejecting tour report dispatches notification to guide and deletes report', function () {
    Notification::fake();

    $report = TourReport::create([
        'tour_schedule_id' => $this->schedule->id,
        'guide_id' => $this->guide->id,
        'actual_guests' => 15,
        'incident_notes' => 'Không có sự cố.',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.reports.reject', $report->id), [
            'reject_reason' => 'Báo cáo thiếu thông tin quyết toán.',
        ]);

    $response->assertRedirect();

    // Check that the report is deleted from the database
    $this->assertDatabaseMissing('tour_reports', [
        'id' => $report->id,
    ]);

    Notification::assertSentTo($this->guideUser, TourReportRejectedNotification::class, function ($notification) {
        return $notification->reason === 'Báo cáo thiếu thông tin quyết toán.';
    });
});
