<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\GroupSplit;
use App\Models\TourReport;
use App\Models\TourSchedule;
use App\Notifications\Guide\TourReportSubmittedNotification;
use Illuminate\Http\Request;

class TourReportController extends Controller
{
    public function create(TourSchedule $schedule)
    {
        // Kiểm tra quyền (có phải HDV của tour này không)
        $isAssigned = $schedule->schedule_guides()->where('guide_id', auth()->user()->tour_guide->id ?? 0)->exists();
        if (! $isAssigned) {
            abort(403);
        }

        $firstBooking = $schedule->bookings
            ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
            ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
            ->first();
        if (! $firstBooking) {
            $firstBooking = $schedule->bookings
                ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
                ->first();
        }
        $groupStatus = $firstBooking ? $firstBooking->tour_status : 'upcoming';

        if ($schedule->status !== 'completed' && $groupStatus !== 'completed') {
            return redirect()->back()->with('error', 'Chỉ có thể viết báo cáo khi Tour đã kết thúc.');
        }

        $report = TourReport::where('tour_schedule_id', $schedule->id)->first();
        if ($report) {
            return redirect()->back()->with('info', 'Bạn đã nộp báo cáo cho Tour này.');
        }

        $passengerIds = $schedule->bookings()
            ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
            ->whereNotIn('booking_status', ['cancelled'])
            ->get()
            ->flatMap(fn ($b) => $b->booking_passengers->pluck('id'))
            ->toArray();

        $freeTimePassengers = BookingPassenger::whereIn('id', $passengerIds)
            ->whereHas('group_splits', function ($q) {
                $q->where('status', '!=', GroupSplit::STATUS_CANCELLED);
            })
            ->with(['group_splits' => function ($q) {
                $q->where('status', '!=', GroupSplit::STATUS_CANCELLED)->orderBy('id', 'desc');
            }])
            ->get()
            ->map(function ($p) {
                $lastSplit = $p->group_splits->first();
                if ($lastSplit) {
                    $p->free_time_location = $p->free_time_location ?? ($lastSplit->split_location ?? $lastSplit->return_location);
                    $p->free_time_start = $p->free_time_start ?? $lastSplit->start_time;
                    $p->free_time_end = $p->free_time_end ?? $lastSplit->end_time;
                }

                return $p;
            });

        return view('guide.reports.create', compact('schedule', 'freeTimePassengers'));
    }

    public function store(Request $request, TourSchedule $schedule)
    {
        $isAssigned = $schedule->schedule_guides()->where('guide_id', auth()->user()->tour_guide->id ?? 0)->exists();
        if (! $isAssigned) {
            abort(403);
        }

        $request->validate([
            'actual_guests' => 'required|integer|min:0|max:'.$schedule->capacity,
            'incident_notes' => 'nullable|string',
        ], [
            'actual_guests.max' => 'Số khách thực tế không được vượt quá số lượng tối đa của tour ('.$schedule->capacity.' người).',
        ]);

        $tourReport = TourReport::create([
            'tour_schedule_id' => $schedule->id,
            'guide_id' => auth()->user()->tour_guide->id,
            'actual_guests' => $request->actual_guests,
            'incident_notes' => $request->incident_notes,
            'advance_amount' => 0,
            'actual_expense' => 0,
            'balance' => 0,
            'status' => 'pending',
        ]);

        // Send notification to the guide
        auth()->user()->notify(new TourReportSubmittedNotification($tourReport));

        return redirect()->route('guide.schedules.show', $schedule->id)->with('success', 'Đã nộp Báo cáo sự cố thành công. Chờ Admin duyệt.');
    }
}
