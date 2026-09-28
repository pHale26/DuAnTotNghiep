<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\ScheduleGuide;
use App\Models\Setting;
use App\Models\Tour;
use App\Models\TourAbsenceRequest;
use App\Models\TourAssignmentLog;
use App\Models\TourGuide;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

/**
 * Setup a test scenario for absence requests.
 */
function setupAbsenceScenario(bool $hasBackup = false, int $departureHours = 48): array
{
    Role::firstOrCreate(['name' => 'Admin']);
    Role::firstOrCreate(['name' => 'Guide']);

    $destination = Destination::create([
        'name' => 'Destination Test '.uniqid(),
        'description' => 'Description test',
    ]);

    $tour = Tour::create([
        'destination_id' => $destination->id,
        'title' => 'Tour Test '.uniqid(),
        'slug' => 'tour-test-'.uniqid(),
        'description' => 'Description',
        'duration_days' => 3,
        'duration_nights' => 2,
        'base_price' => 3500000,
    ]);

    $schedule = TourSchedule::create([
        'tour_id' => $tour->id,
        'departure_date' => Carbon::now()->addHours($departureHours)->toDateTimeString(),
        'return_date' => Carbon::now()->addHours($departureHours + 48)->toDateTimeString(),
        'capacity' => 20,
        'available_seats' => 20,
        'status' => 'available',
    ]);

    // Primary Guide User
    $guideUser = User::factory()->create(['role' => 'guide']);
    $guideUser->assignRole('Guide');
    $tourGuide = TourGuide::create([
        'user_id' => $guideUser->id,
        'name' => $guideUser->name,
        'phone' => '09'.rand(10000000, 99999999),
        'email' => $guideUser->email,
        'status' => 'active',
    ]);

    // Assign Primary Guide
    ScheduleGuide::create([
        'tour_schedule_id' => $schedule->id,
        'guide_id' => $tourGuide->id,
        'is_backup' => false,
    ]);

    $backupGuide = null;
    $backupGuideUser = null;

    if ($hasBackup) {
        $backupGuideUser = User::factory()->create(['role' => 'guide']);
        $backupGuideUser->assignRole('Guide');
        $backupGuide = TourGuide::create([
            'user_id' => $backupGuideUser->id,
            'name' => $backupGuideUser->name,
            'phone' => '09'.rand(10000000, 99999999),
            'email' => $backupGuideUser->email,
            'status' => 'active',
        ]);

        ScheduleGuide::create([
            'tour_schedule_id' => $schedule->id,
            'guide_id' => $backupGuide->id,
            'is_backup' => true,
        ]);
    }

    return compact('guideUser', 'tourGuide', 'schedule', 'backupGuideUser', 'backupGuide', 'tour');
}

test('guide can view the absence form if they are primary guide and tour has not started', function () {
    $data = setupAbsenceScenario(false, 48);

    $response = $this->actingAs($data['guideUser'])->get(route('guide.schedules.absence', $data['schedule']->id));

    $response->assertOk();
    $response->assertViewIs('guide.schedules.absence');
});

test('guide cannot view the absence form if they are not the primary guide', function () {
    $data = setupAbsenceScenario(true, 48);

    $response = $this->actingAs($data['backupGuideUser'])->get(route('guide.schedules.absence', $data['schedule']->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('guide cannot view the absence form or submit request if the tour has already departed or status is in_progress', function () {
    $data = setupAbsenceScenario(false, 48);

    // Create a booking with in_progress status
    $customer = User::factory()->create();
    Booking::create([
        'user_id' => $customer->id,
        'tour_schedule_id' => $data['schedule']->id,
        'total_price' => 3500000,
        'adults_count' => 2,
        'children_count' => 0,
        'payment_status' => Booking::PAYMENT_PAID_100,
        'tour_status' => Booking::TOUR_IN_PROGRESS,
    ]);

    // Guide try to view
    $responseGet = $this->actingAs($data['guideUser'])->get(route('guide.schedules.absence', $data['schedule']->id));
    $responseGet->assertRedirect();
    $responseGet->assertSessionHas('error');

    // Guide try to submit
    $responsePost = $this->actingAs($data['guideUser'])->post(route('guide.schedules.absence.store', $data['schedule']->id), [
        'reason_type' => 'ốm đau',
    ]);
    $responsePost->assertRedirect();
    $responsePost->assertSessionHas('error');
});

test('guide can submit absence request and it calculates status based on threshold', function () {
    // 1. Normal Request (Time left = 48 hours > 24 hours threshold)
    $data = setupAbsenceScenario(false, 48);
    Setting::set('ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS', 24);

    $response = $this->actingAs($data['guideUser'])->post(route('guide.schedules.absence.store', $data['schedule']->id), [
        'reason_type' => 'ốm đau',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $requestRecord = TourAbsenceRequest::where('tour_schedule_id', $data['schedule']->id)->first();
    expect($requestRecord)->not->toBeNull();
    expect($requestRecord->status)->toBe('pending_review');
    expect($requestRecord->urgency_level)->toBe('normal');

    // 2. Urgent Request (Time left = 10 hours < 24 hours threshold)
    $data2 = setupAbsenceScenario(false, 10);
    $response2 = $this->actingAs($data2['guideUser'])->post(route('guide.schedules.absence.store', $data2['schedule']->id), [
        'reason_type' => 'khác',
        'reason_custom' => 'Có việc gia đình đột xuất',
    ]);

    $response2->assertRedirect();
    $requestRecord2 = TourAbsenceRequest::where('tour_schedule_id', $data2['schedule']->id)->first();
    expect($requestRecord2->status)->toBe('pending_review_urgent');
    expect($requestRecord2->urgency_level)->toBe('urgent');
});

test('guide cannot submit duplicate pending request', function () {
    $data = setupAbsenceScenario(false, 48);

    // First request
    $this->actingAs($data['guideUser'])->post(route('guide.schedules.absence.store', $data['schedule']->id), [
        'reason_type' => 'ốm đau',
    ]);

    // Second request should fail
    $response = $this->actingAs($data['guideUser'])->post(route('guide.schedules.absence.store', $data['schedule']->id), [
        'reason_type' => 'trùng lịch',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('admin can approve absence request and backup guide is automatically promoted', function () {
    $data = setupAbsenceScenario(true, 48); // true means backup guide exists

    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    // Submit request
    $this->actingAs($data['guideUser'])->post(route('guide.schedules.absence.store', $data['schedule']->id), [
        'reason_type' => 'ốm đau',
    ]);
    $absenceRequest = TourAbsenceRequest::where('tour_schedule_id', $data['schedule']->id)->first();

    // Approve request
    $response = $this->actingAs($admin)->post(route('admin.absence_requests.approve', $absenceRequest->id), [
        'new_main_guide_id' => '',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Status updated
    expect($absenceRequest->fresh()->status)->toBe('approved');
    expect($absenceRequest->fresh()->new_main_guide_id)->toBe($data['backupGuide']->id);

    // Old main guide removed, backup guide promoted to main
    $assignments = ScheduleGuide::where('tour_schedule_id', $data['schedule']->id)->get();
    expect($assignments->count())->toBe(1);
    expect($assignments->first()->guide_id)->toBe($data['backupGuide']->id);
    expect($assignments->first()->is_backup)->toBe(false); // Promoted to main

    // Tour assignment log generated
    $log = TourAssignmentLog::where('tour_schedule_id', $data['schedule']->id)->first();
    expect($log)->not->toBeNull();
    expect($log->action)->toBe('absence_approval');
});

test('admin can approve absence request when no backup guide exists by manually choosing new main guide', function () {
    $data = setupAbsenceScenario(false, 48); // false means no backup guide

    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    // Create another free guide
    $newMainUser = User::factory()->create(['role' => 'guide']);
    $newMainUser->assignRole('Guide');
    $newMainGuide = TourGuide::create([
        'user_id' => $newMainUser->id,
        'name' => $newMainUser->name,
        'phone' => '09'.rand(10000000, 99999999),
        'email' => $newMainUser->email,
        'status' => 'active',
    ]);

    // Submit request
    $this->actingAs($data['guideUser'])->post(route('guide.schedules.absence.store', $data['schedule']->id), [
        'reason_type' => 'ốm đau',
    ]);
    $absenceRequest = TourAbsenceRequest::where('tour_schedule_id', $data['schedule']->id)->first();

    // Try approving without choosing main guide (should fail validation)
    $responseError = $this->actingAs($admin)->post(route('admin.absence_requests.approve', $absenceRequest->id), [
        'new_main_guide_id' => '',
    ]);
    $responseError->assertSessionHasErrors(['new_main_guide_id']);

    // Approve request with chosen main guide
    $response = $this->actingAs($admin)->post(route('admin.absence_requests.approve', $absenceRequest->id), [
        'new_main_guide_id' => $newMainGuide->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Status updated
    expect($absenceRequest->fresh()->status)->toBe('approved');
    expect($absenceRequest->fresh()->new_main_guide_id)->toBe($newMainGuide->id);

    // Verification
    $assignments = ScheduleGuide::where('tour_schedule_id', $data['schedule']->id)->get();
    expect($assignments->count())->toBe(1);
    expect($assignments->first()->guide_id)->toBe($newMainGuide->id);
    expect($assignments->first()->is_backup)->toBe(false);
});

test('admin can reject absence request with reason', function () {
    $data = setupAbsenceScenario(false, 48);

    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    // Submit request
    $this->actingAs($data['guideUser'])->post(route('guide.schedules.absence.store', $data['schedule']->id), [
        'reason_type' => 'ốm đau',
    ]);
    $absenceRequest = TourAbsenceRequest::where('tour_schedule_id', $data['schedule']->id)->first();

    // Reject request
    $response = $this->actingAs($admin)->post(route('admin.absence_requests.reject', $absenceRequest->id), [
        'reject_reason' => 'Không đủ tài liệu minh chứng y tế.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verification
    expect($absenceRequest->fresh()->status)->toBe('rejected');
    expect($absenceRequest->fresh()->reject_reason)->toBe('Không đủ tài liệu minh chứng y tế.');

    // Assignments remain unchanged
    $assignments = ScheduleGuide::where('tour_schedule_id', $data['schedule']->id)->get();
    expect($assignments->count())->toBe(1);
    expect($assignments->first()->guide_id)->toBe($data['tourGuide']->id);
});

test('available guides AJAX endpoint excludes the guide who reported busy', function () {
    $data = setupAbsenceScenario(false, 48);

    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('Admin');

    // Fetch available guides for the schedule
    $response = $this->actingAs($admin)->get(route('admin.absence_requests.available_guides', $data['schedule']->id));

    $response->assertOk();
    $guides = $response->json();

    // Verify the requesting guide is NOT in the list
    $guideIds = collect($guides)->pluck('id')->toArray();
    expect($guideIds)->not->toContain($data['tourGuide']->id);
});
