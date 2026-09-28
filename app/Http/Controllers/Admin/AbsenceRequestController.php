<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleGuide;
use App\Models\TourAbsenceRequest;
use App\Models\TourAssignmentLog;
use App\Models\TourGuide;
use App\Models\TourSchedule;
use App\Notifications\Guide\AbsenceRequestApprovedNotification;
use App\Notifications\Guide\AbsenceRequestRejectedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceRequestController extends Controller
{
    /**
     * Display a listing of absence requests.
     */
    public function index(Request $request)
    {
        // Sort pending_review_urgent first, then pending_review, then approved/rejected by latest
        $requests = TourAbsenceRequest::with(['tour', 'tour_schedule.schedule_guides.tour_guide', 'main_guide', 'new_main_guide', 'new_backup_guide', 'reviewer'])
            ->orderByRaw("CASE 
                WHEN status = 'pending_review_urgent' THEN 1 
                WHEN status = 'pending_review' THEN 2 
                ELSE 3 
            END")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.tour_reports.index', compact('requests'));
    }

    /**
     * AJAX endpoint to get available guides for a specific schedule.
     */
    public function getAvailableGuides(TourSchedule $schedule): JsonResponse
    {
        // Get guides assigned to overlapping schedules
        $busyGuideIds = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
            ->whereHas('tour_schedule', function ($q) use ($schedule) {
                $q->where('departure_date', '<=', $schedule->return_date)
                    ->where('return_date', '>=', $schedule->departure_date);
            })
            ->pluck('guide_id')
            ->toArray();

        // Also exclude the current primary guide of this schedule (who is reporting busy)
        $currentPrimaryGuideId = $schedule->schedule_guides()
            ->where('is_backup', false)
            ->value('guide_id');

        if ($currentPrimaryGuideId) {
            $busyGuideIds[] = $currentPrimaryGuideId;
        }

        // Get active, non-blacklisted guides
        $availableGuides = TourGuide::whereNotIn('id', $busyGuideIds)
            ->where('is_blacklisted', false)
            ->where('status', 'active')
            ->get(['id', 'name', 'phone']);

        return response()->json($availableGuides);
    }

    /**
     * Approve an absence request.
     */
    public function approve(Request $request, TourAbsenceRequest $absenceRequest): RedirectResponse
    {
        if (in_array($absenceRequest->status, ['approved', 'rejected'])) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        $schedule = $absenceRequest->tour_schedule;
        $backupGuideAssignment = $schedule->schedule_guides()->where('is_backup', true)->first();

        // If there's no backup guide, we require selection of a new main guide
        $rules = [];
        if (! $backupGuideAssignment) {
            $rules['new_main_guide_id'] = 'required|exists:tour_guides,id';
        } else {
            $rules['new_main_guide_id'] = 'nullable|exists:tour_guides,id';
        }
        $rules['new_backup_guide_id'] = 'nullable|exists:tour_guides,id|different:new_main_guide_id';

        $request->validate($rules, [
            'new_main_guide_id.required' => 'Tour này không có HDV phụ, bạn bắt buộc phải chọn một HDV chính thay thế.',
            'new_backup_guide_id.different' => 'HDV phụ mới không được trùng với HDV chính mới.',
        ]);

        $newMainGuideId = $request->input('new_main_guide_id');
        $newBackupGuideId = $request->input('new_backup_guide_id');

        // Check compatibility for the chosen guides
        $chosenGuideIds = array_filter([$newMainGuideId, $newBackupGuideId]);
        foreach ($chosenGuideIds as $guideId) {
            $isBusy = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
                ->where('guide_id', $guideId)
                ->whereHas('tour_schedule', function ($q) use ($schedule) {
                    $q->where('departure_date', '<=', $schedule->return_date)
                        ->where('return_date', '>=', $schedule->departure_date);
                })
                ->exists();

            if ($isBusy) {
                $guideName = TourGuide::find($guideId)?->name ?? 'Hướng dẫn viên';

                return redirect()->back()->with('error', "HDV {$guideName} đã bị trùng lịch trình khác trong thời gian này.");
            }

            $guide = TourGuide::find($guideId);
            if ($guide && ($guide->is_blacklisted || $guide->status !== 'active')) {
                return redirect()->back()->with('error', "HDV {$guide->name} đang trong blacklist hoặc không hoạt động.");
            }
        }

        DB::transaction(function () use ($absenceRequest, $schedule, $backupGuideAssignment, $newMainGuideId, $newBackupGuideId) {
            $oldMainGuideId = $absenceRequest->main_guide_id;
            $oldMainGuide = TourGuide::find($oldMainGuideId);
            $adminUser = auth()->user();

            $logDetails = [
                'old_main_guide' => [
                    'id' => $oldMainGuideId,
                    'name' => $oldMainGuide?->name,
                ],
            ];

            // 1. Remove old main guide assignment
            $schedule->schedule_guides()->where('guide_id', $oldMainGuideId)->delete();

            // 2. Set/Promote the new main guide
            $finalMainGuideId = null;
            if ($backupGuideAssignment) {
                // Auto promote backup to main
                $backupGuideId = $backupGuideAssignment->guide_id;
                $schedule->schedule_guides()->where('guide_id', $backupGuideId)->update(['is_backup' => false]);
                $finalMainGuideId = $backupGuideId;

                $backupGuide = TourGuide::find($backupGuideId);
                $logDetails['new_main_guide'] = [
                    'id' => $backupGuideId,
                    'name' => $backupGuide?->name,
                    'promoted_from_backup' => true,
                ];
            } else {
                // Manual assign main
                $schedule->schedule_guides()->create([
                    'guide_id' => $newMainGuideId,
                    'is_backup' => false,
                ]);
                $finalMainGuideId = $newMainGuideId;

                $newMain = TourGuide::find($newMainGuideId);
                $logDetails['new_main_guide'] = [
                    'id' => $newMainGuideId,
                    'name' => $newMain?->name,
                    'promoted_from_backup' => false,
                ];
            }

            // 3. Assign new backup guide if provided
            if ($newBackupGuideId) {
                // Delete any old backup guide assignment first
                $schedule->schedule_guides()->where('is_backup', true)->delete();

                // Create new backup guide assignment
                $schedule->schedule_guides()->create([
                    'guide_id' => $newBackupGuideId,
                    'is_backup' => true,
                ]);

                $newBackup = TourGuide::find($newBackupGuideId);
                $logDetails['new_backup_guide'] = [
                    'id' => $newBackupGuideId,
                    'name' => $newBackup?->name,
                ];
            }

            // 4. Update the Absence Request
            $absenceRequest->update([
                'status' => 'approved',
                'reviewed_by' => $adminUser->id,
                'reviewed_at' => now(),
                'new_main_guide_id' => $finalMainGuideId,
                'new_backup_guide_id' => $newBackupGuideId,
            ]);

            // 5. Log the assignment changes
            TourAssignmentLog::create([
                'tour_schedule_id' => $schedule->id,
                'user_id' => $adminUser->id,
                'action' => 'absence_approval',
                'description' => "Duyệt báo bận cho HDV {$oldMainGuide?->name}. Đã cập nhật HDV chính và phụ mới.",
                'details' => $logDetails,
            ]);

            // 6. Send notifications
            // Notify the old main guide
            if ($oldMainGuide && $oldMainGuide->user) {
                $oldMainGuide->user->notify(new AbsenceRequestApprovedNotification($absenceRequest, 'old_main'));
            }

            // Notify the new main guide
            $finalMainGuide = TourGuide::find($finalMainGuideId);
            if ($finalMainGuide && $finalMainGuide->user) {
                $finalMainGuide->user->notify(new AbsenceRequestApprovedNotification($absenceRequest, 'new_main'));
            }

            // Notify the new backup guide if assigned
            if ($newBackupGuideId) {
                $finalBackupGuide = TourGuide::find($newBackupGuideId);
                if ($finalBackupGuide && $finalBackupGuide->user) {
                    $finalBackupGuide->user->notify(new AbsenceRequestApprovedNotification($absenceRequest, 'new_backup'));
                }
            }
        });

        return redirect()->back()->with('success', 'Đã duyệt yêu cầu báo bận và phân công lại hướng dẫn viên thành công.');
    }

    /**
     * Reject an absence request.
     */
    public function reject(Request $request, TourAbsenceRequest $absenceRequest): RedirectResponse
    {
        if (in_array($absenceRequest->status, ['approved', 'rejected'])) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ], [
            'reject_reason.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $adminUser = auth()->user();

        $absenceRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $adminUser->id,
            'reviewed_at' => now(),
            'reject_reason' => $request->input('reject_reason'),
        ]);

        // Send notification to the old main guide
        $oldMainGuide = TourGuide::find($absenceRequest->main_guide_id);
        if ($oldMainGuide && $oldMainGuide->user) {
            $oldMainGuide->user->notify(new AbsenceRequestRejectedNotification($absenceRequest));
        }

        return redirect()->back()->with('success', 'Đã từ chối yêu cầu báo bận của Hướng dẫn viên.');
    }
}
