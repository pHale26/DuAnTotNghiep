<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityPassengerCheckin;
use App\Models\BookingPassenger;
use App\Models\GroupSplit;
use App\Models\GroupSplitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GroupSplitController extends Controller
{
    /**
     * Lấy danh sách tách đoàn.
     */
    public function index(Request $request)
    {
        $query = GroupSplit::query();

        if ($request->has('stop_id')) {
            $query->where('stop_id', $request->stop_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $groupSplits = $query->with('extensions')->latest()->paginate($request->input('per_page', 15));

        return response()->json($groupSplits);
    }

    /**
     * Tạo mới 1 lượt tách đoàn.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_id' => 'required|integer',
            'stop_id' => 'nullable|integer',
            'guest_id' => 'required|integer',
            'guest_name' => 'required|string|max:255',
            'reason' => 'required|string|max:500',
            'phone_number' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})\b$/'],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'split_location' => 'nullable|string|max:255',
            'return_location' => 'required|string|max:255',
        ], [
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'phone_number.regex' => 'Số điện thoại không đúng định dạng Việt Nam.',
        ]);

        $startTime = Carbon::parse($request->start_time);

        if ($startTime->isPast() && $startTime->diffInMinutes(now(), false) > 2) {
            throw ValidationException::withMessages([
                'start_time' => ['Thời gian bắt đầu không được trong quá khứ.'],
            ]);
        }

        // Kiểm tra xem khách có đang có lượt tách đoàn nào chưa hoàn thành không
        $hasActiveSplit = GroupSplit::where('guest_id', $request->guest_id)
            ->whereIn('status', [GroupSplit::STATUS_ON_TIME, GroupSplit::STATUS_OVERDUE])
            ->exists();

        if ($hasActiveSplit) {
            throw ValidationException::withMessages([
                'guest_id' => ['Khách này đang có một lượt tách đoàn chưa kết thúc.'],
            ]);
        }

        $groupSplit = GroupSplit::create(array_merge($validated, [
            'status' => GroupSplit::STATUS_ON_TIME,
            'split_started_at' => now(),
            'created_by' => auth()->id(),
        ]));

        if ($request->stop_id) {
            ActivityPassengerCheckin::where('booking_passenger_id', $request->guest_id)
                ->where('tour_activity_id', $request->stop_id)
                ->delete();
        } else {
            ActivityPassengerCheckin::where('booking_passenger_id', $request->guest_id)
                ->delete();
        }

        $passenger = BookingPassenger::find($request->guest_id);
        if ($passenger) {
            $passenger->update([
                'checked_in' => false,
                'is_free_time' => true,
                'free_time_start' => $request->start_time,
                'free_time_end' => $request->end_time,
                'free_time_location' => $request->split_location ?? $request->return_location,
            ]);
        }

        return response()->json($groupSplit, 201);
    }

    /**
     * Huỷ tách đoàn.
     */
    public function cancel(Request $request, $id)
    {
        $groupSplit = GroupSplit::findOrFail($id);

        if (in_array($groupSplit->status, [GroupSplit::STATUS_RETURNED, GroupSplit::STATUS_CANCELLED])) {
            return response()->json([
                'message' => 'Không thể huỷ lượt tách đoàn đã kết thúc hoặc đã huỷ.',
            ], 422);
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        $groupSplit->update([
            'status' => GroupSplit::STATUS_CANCELLED,
            'cancel_reason' => $request->cancel_reason,
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        return response()->json($groupSplit);
    }

    /**
     * Xác nhận khách đã quay lại đoàn.
     */
    public function returnGuest(Request $request, $id)
    {
        $groupSplit = GroupSplit::findOrFail($id);

        if ($groupSplit->status === GroupSplit::STATUS_RETURNED) {
            return response()->json([
                'message' => 'Khách đã được đánh dấu quay lại trước đó.',
            ], 422);
        }

        if ($groupSplit->status === GroupSplit::STATUS_CANCELLED) {
            return response()->json([
                'message' => 'Không thể đánh dấu quay lại cho lượt tách đoàn đã huỷ.',
            ], 422);
        }

        $oldStatus = $groupSplit->status;

        $groupSplit->update([
            'status' => GroupSplit::STATUS_RETURNED,
            'returned_at' => now(),
        ]);

        $passenger = BookingPassenger::find($groupSplit->guest_id);
        if ($passenger) {
            $passenger->update([
                'is_free_time' => false,
            ]);
        }

        if (class_exists(GroupSplitLog::class)) {
            GroupSplitLog::create([
                'group_split_id' => $groupSplit->id,
                'old_status' => $oldStatus,
                'new_status' => GroupSplit::STATUS_RETURNED,
                'description' => 'Khách đã quay lại đoàn',
                'triggered_by' => auth()->id(),
            ]);
        }

        return response()->json($groupSplit);
    }

    /**
     * Gia hạn thời gian tách đoàn.
     */
    public function extend(Request $request, $id)
    {
        $groupSplit = GroupSplit::findOrFail($id);

        if (! in_array($groupSplit->status, [GroupSplit::STATUS_ON_TIME, GroupSplit::STATUS_OVERDUE])) {
            return response()->json([
                'message' => 'Chỉ có thể gia hạn khi trạng thái đang là Đang tách đoàn hoặc Quá giờ.',
            ], 422);
        }

        $request->validate([
            'new_end_time' => 'required|date|after:'.$groupSplit->end_time->toDateTimeString(),
            'extend_reason' => 'required|string|max:500',
        ], [
            'new_end_time.after' => 'Thời gian quay lại mới phải sau thời gian cũ.',
        ]);

        $oldEndTime = $groupSplit->end_time;

        DB::transaction(function () use ($groupSplit, $request, $oldEndTime) {
            $groupSplit->extensions()->create([
                'old_end_time' => $oldEndTime,
                'new_end_time' => $request->new_end_time,
                'extend_reason' => $request->extend_reason,
                'confirmed_by_guide_id' => auth()->id(),
                'confirmed_by_guide_name' => auth()->user()->name ?? (auth()->user()->full_name ?? 'HDV'),
                'created_at' => now(),
            ]);

            $updateData = [
                'end_time' => $request->new_end_time,
            ];

            if ($groupSplit->status === GroupSplit::STATUS_OVERDUE) {
                $updateData['status'] = GroupSplit::STATUS_ON_TIME;
            }

            $groupSplit->update($updateData);

            if (class_exists(GroupSplitLog::class) && isset($updateData['status'])) {
                GroupSplitLog::create([
                    'group_split_id' => $groupSplit->id,
                    'old_status' => GroupSplit::STATUS_OVERDUE,
                    'new_status' => GroupSplit::STATUS_ON_TIME,
                    'description' => 'Khách được gia hạn thời gian',
                    'triggered_by' => auth()->id(),
                ]);
            }
        });

        $groupSplit->load('extensions');

        return response()->json($groupSplit);
    }
}
