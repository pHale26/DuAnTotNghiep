<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\ActivityPassengerCheckin;
use App\Models\BookingPassenger;
use App\Models\GroupSplit;
use App\Models\GroupSplitExtension;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupSplitController extends Controller
{
    public function index(Request $request)
    {
        $query = GroupSplit::query();

        if ($request->has('schedule_id')) {
            $schedule = TourSchedule::with('bookings.booking_passengers')->find($request->schedule_id);
            if ($schedule) {
                $passengerIds = $schedule->bookings->flatMap->booking_passengers->pluck('id')->toArray();
                $query->whereIn('guest_id', $passengerIds);
            }
        }

        if ($request->has('stop_id')) {
            $query->where('stop_id', $request->stop_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $groupSplits = $query->with('extensions')->latest()->paginate($request->input('per_page', 1000));

        return response()->json($groupSplits);
    }

    public function store(Request $request, $scheduleId, $passengerId)
    {
        $schedule = TourSchedule::findOrFail($scheduleId);
        $passenger = BookingPassenger::findOrFail($passengerId);

        $request->validate([
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
            return response()->json([
                'success' => false,
                'message' => 'Thời gian bắt đầu không được trong quá khứ.',
            ], 422);
        }

        $hasActiveSplit = GroupSplit::where('guest_id', $passengerId)
            ->whereIn('status', [GroupSplit::STATUS_ON_TIME, GroupSplit::STATUS_OVERDUE])
            ->exists();

        if ($hasActiveSplit) {
            return response()->json([
                'success' => false,
                'message' => 'Khách này đang có một lượt tách đoàn chưa kết thúc.',
            ], 422);
        }

        $groupSplit = GroupSplit::create([
            'tour_id' => $schedule->tour_id,
            'stop_id' => $request->stop_id ? intval($request->stop_id) : null,
            'guest_id' => $passenger->id,
            'guest_name' => $passenger->full_name,
            'reason' => $request->reason,
            'phone_number' => $request->phone_number,
            'start_time' => $startTime,
            'end_time' => Carbon::parse($request->end_time),
            'split_location' => $request->split_location,
            'return_location' => $request->return_location,
            'status' => GroupSplit::STATUS_ON_TIME,
            'split_started_at' => now(),
            'created_by' => auth()->id(),
        ]);

        if ($request->stop_id) {
            ActivityPassengerCheckin::where('booking_passenger_id', $passenger->id)
                ->where('tour_activity_id', $request->stop_id)
                ->delete();
        } else {
            ActivityPassengerCheckin::where('booking_passenger_id', $passenger->id)
                ->delete();
        }

        $passenger->update([
            'checked_in' => false,
            'is_free_time' => true,
            'free_time_start' => $startTime,
            'free_time_end' => Carbon::parse($request->end_time),
            'free_time_location' => $request->split_location ?? $request->return_location,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tách đoàn thành công.',
            'data' => $groupSplit,
        ]);
    }

    public function extend(Request $request, $groupSplitId)
    {
        $groupSplit = GroupSplit::findOrFail($groupSplitId);

        if (! in_array($groupSplit->status, [GroupSplit::STATUS_ON_TIME, GroupSplit::STATUS_OVERDUE])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gia hạn cho trạng thái tách đoàn này.',
            ], 422);
        }

        $request->validate([
            'new_end_time' => 'required|date|after:'.$groupSplit->end_time,
            'extend_reason' => 'required|string|max:500',
        ], [
            'new_end_time.after' => 'Thời gian hẹn mới phải sau thời gian hẹn cũ.',
        ]);

        $oldEndTime = $groupSplit->end_time;
        $newEndTime = Carbon::parse($request->new_end_time);

        $groupSplit->update([
            'end_time' => $newEndTime,
            'status' => GroupSplit::STATUS_ON_TIME,
        ]);

        GroupSplitExtension::create([
            'group_split_id' => $groupSplit->id,
            'old_end_time' => $oldEndTime,
            'new_end_time' => $newEndTime,
            'extend_reason' => $request->extend_reason,
            'confirmed_by_guide_id' => auth()->id(),
            'confirmed_by_guide_name' => auth()->user()->name ?? (auth()->user()->full_name ?? 'HDV'),
        ]);

        $passenger = BookingPassenger::find($groupSplit->guest_id);
        if ($passenger) {
            $passenger->update([
                'free_time_end' => $newEndTime,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Gia hạn thời gian thành công.',
            'data' => $groupSplit,
        ]);
    }

    public function return(Request $request, $groupSplitId)
    {
        $groupSplit = GroupSplit::findOrFail($groupSplitId);

        if (in_array($groupSplit->status, [GroupSplit::STATUS_RETURNED, GroupSplit::STATUS_CANCELLED])) {
            return response()->json([
                'success' => false,
                'message' => 'Khách đã quay lại hoặc lượt tách đã bị huỷ.',
            ], 422);
        }

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

        return response()->json([
            'success' => true,
            'message' => 'Xác nhận khách quay lại thành công.',
            'data' => $groupSplit,
        ]);
    }

    public function cancel(Request $request, $groupSplitId)
    {
        $groupSplit = GroupSplit::findOrFail($groupSplitId);

        if ($groupSplit->status !== GroupSplit::STATUS_ON_TIME) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể huỷ lượt tách đoàn khi đang ở trạng thái đúng hẹn.',
            ], 422);
        }

        $groupSplit->update([
            'status' => GroupSplit::STATUS_CANCELLED,
        ]);

        $passenger = BookingPassenger::find($groupSplit->guest_id);
        if ($passenger) {
            $passenger->update([
                'is_free_time' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã huỷ lượt tách đoàn.',
            'data' => $groupSplit,
        ]);
    }
}
