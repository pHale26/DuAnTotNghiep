<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Imports\PassengersImport;
use App\Models\ActivityPassengerCheckin;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\ScheduleActivityCheckin;
use App\Models\TourActivity;
use App\Models\TourSchedule;
use App\Models\User;
use App\Notifications\AdminBookingNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class ScheduleController extends Controller
{
    public function index()
    {
        Booking::updateUpcomingTourStatuses();

        $user = auth()->user();
        $tourGuide = $user->tour_guide;

        if (! $tourGuide) {
            return redirect()->route('guide.dashboard')->with('error', 'Tài khoản chưa được liên kết với hồ sơ Hướng dẫn viên.');
        }

        $schedules = $tourGuide->schedule_guides()
            ->with(['tour_schedule.tour', 'tour_schedule.bookings' => function ($q) {
                $q->whereIn('payment_status', ['pending', 'paid_30', 'paid_100']);
            }])
            ->orderByDesc('tour_schedule_id')
            ->paginate(15);

        return view('guide.schedules.index', compact('schedules'));
    }

    public function show($id)
    {
        Booking::updateUpcomingTourStatuses();

        $user = auth()->user();
        $tourGuide = $user->tour_guide;

        if (! $tourGuide) {
            return redirect()->route('guide.dashboard')->with('error', 'Tài khoản chưa được liên kết với hồ sơ Hướng dẫn viên.');
        }

        $scheduleGuide = $tourGuide->schedule_guides()
            ->with(['tour_schedule.tour.tour_itineraries.activities', 'tour_schedule.activity_checkins', 'tour_schedule.bookings.booking_passengers.activity_checkins', 'tour_schedule.bookings.booking_passengers.group_splits', 'tour_schedule.bookings.user', 'tour_schedule.bookings.ticket_bookings.ticket_option.ticket', 'tour_schedule.bookings.addons', 'tour_schedule.bookings.booking_accommodations.room_type.accommodation'])
            ->where('tour_schedule_id', $id)
            ->firstOrFail();

        return view('guide.schedules.show', compact('scheduleGuide'));
    }

    /**
     * Update checkin location for a tour schedule (AJAX).
     */
    public function updateCheckinLocation(TourSchedule $schedule)
    {
        $tourGuide = auth()->user()->tour_guide;
        abort_unless($tourGuide, 403);

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $schedule->id)
            ->exists();
        abort_unless($assigned, 403);

        request()->validate(['location' => 'nullable|string|max:500']);

        $schedule->update(['checkin_location' => request('location')]);

        return response()->json([
            'message' => request('location') ? 'Đã cập nhật địa điểm check-in.' : 'Đã xóa địa điểm check-in.',
            'location' => $schedule->checkin_location,
        ]);
    }

    /**
     * Toggle check-in status for a passenger (AJAX).
     */
    public function toggleCheckin(BookingPassenger $passenger)
    {
        $this->authorizePassenger($passenger);

        $passenger->update(['checked_in' => ! $passenger->checked_in]);

        return response()->json([
            'checked_in' => $passenger->checked_in,
            'message' => $passenger->checked_in ? 'Đã điểm danh' : 'Đã bỏ điểm danh',
        ]);
    }

    /**
     * Toggle check-in status for an activity (AJAX).
     */
    public function toggleActivityCheckin(TourSchedule $schedule, TourActivity $activity)
    {
        $tourGuide = auth()->user()->tour_guide;
        abort_unless($tourGuide, 403);

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $schedule->id)
            ->exists();
        abort_unless($assigned, 403);

        // Đảm bảo activity thuộc về tour này
        abort_unless($activity->tour_itinerary->tour_id === $schedule->tour_id, 403);

        $checkin = ScheduleActivityCheckin::where('tour_schedule_id', $schedule->id)
            ->where('tour_activity_id', $activity->id)
            ->first();

        if ($checkin) {
            $checkin->delete();
            $status = false;

            // Revert bookings if it was the current checkin step
            Booking::where('tour_schedule_id', $schedule->id)
                ->where('current_checkin_step', $activity->title)
                ->update([
                    'current_checkin_step' => null,
                    'tour_status' => Booking::TOUR_IN_PROGRESS,
                ]);
        } else {
            // Check if all passengers are accounted for (checked-in or free time filled)
            $allPassengers = $schedule->bookings()
                ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
                ->where('booking_status', '!=', 'cancelled')
                ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
                ->get()
                ->flatMap(fn ($b) => $b->booking_passengers);

            $checkedInPassengerIds = ActivityPassengerCheckin::where('tour_schedule_id', $schedule->id)
                ->where('tour_activity_id', $activity->id)
                ->pluck('booking_passenger_id')
                ->toArray();

            foreach ($allPassengers as $p) {
                $isCheckedIn = in_array($p->id, $checkedInPassengerIds);
                $isFreeTimeFilled = (bool) $p->is_free_time;

                if (! $isCheckedIn && ! $isFreeTimeFilled) {
                    return response()->json([
                        'message' => "Không thể check-in. Vui lòng điểm danh hoặc điền thông tin tách đoàn cho tất cả hành khách trước! (Khách chưa hoàn thành: {$p->full_name})",
                    ], 400);
                }
            }

            ScheduleActivityCheckin::create([
                'tour_schedule_id' => $schedule->id,
                'tour_activity_id' => $activity->id,
                'guide_id' => $tourGuide->id,
                'checked_in_at' => now(),
            ]);
            $status = true;

            // Update all bookings to show they are at this step
            Booking::where('tour_schedule_id', $schedule->id)
                ->whereIn('tour_status', [Booking::TOUR_UPCOMING, Booking::TOUR_IN_PROGRESS, Booking::TOUR_CHECKING_IN])
                ->update([
                    'tour_status' => Booking::TOUR_CHECKING_IN,
                    'current_checkin_step' => $activity->title,
                ]);

            // Đồng bộ trạng thái lịch trình khi bắt đầu check-in
            if (in_array($schedule->status, ['available', 'full'])) {
                $schedule->update(['status' => 'in_progress']);
            }
        }

        // Gửi thông báo cho Admin khi check-in thành công
        if ($status) {
            $guideName = auth()->user()->name ?? 'Hướng dẫn viên';
            $tourTitle = $schedule->tour->title ?? '';
            $message = "Hướng dẫn viên {$guideName} đã check-in tại địa điểm \"{$activity->title}\" trong tour \"{$tourTitle}\" (Lịch trình #{$schedule->id}) lúc ".now()->format('H:i d/m/Y').'.';

            $firstBooking = $schedule->bookings()->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])->first();
            if ($firstBooking) {
                $admins = Role::where('name', 'Admin')->exists() ? User::role('Admin')->get() : collect();
                if ($admins->count() > 0) {
                    Notification::send($admins, new AdminBookingNotification(
                        $firstBooking,
                        'activity_checkin',
                        $message,
                        'HDV check-in địa điểm'
                    ));
                }
            }
        }

        return response()->json([
            'checked_in' => $status,
            'message' => $status ? 'Đã check-in điểm tham quan' : 'Đã hủy check-in điểm tham quan',
            'time' => now()->format('H:i d/m/Y'),
        ]);
    }

    /**
     * Save special note for a passenger (AJAX).
     */
    public function updateNote(BookingPassenger $passenger)
    {
        $this->authorizePassenger($passenger);

        request()->validate(['note' => 'nullable|string|max:1000']);

        $passenger->update(['special_note' => request('note')]);

        return response()->json(['message' => 'Đã lưu ghi chú thành công.']);
    }

    /**
     * Ensure the authenticated guide is assigned to the tour schedule of this passenger.
     */
    private function authorizePassenger(BookingPassenger $passenger): void
    {
        $tourGuide = auth()->user()->tour_guide;

        abort_unless($tourGuide, 403);

        $tourScheduleId = $passenger->booking->tour_schedule_id;

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $tourScheduleId)
            ->exists();

        abort_unless($assigned, 403);
    }

    /**
     * Ensure the authenticated guide is assigned to this tour schedule.
     */
    private function authorizeSchedule(TourSchedule $schedule): void
    {
        $tourGuide = auth()->user()->tour_guide;

        abort_unless($tourGuide, 403);

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $schedule->id)
            ->exists();

        abort_unless($assigned, 403);
    }

    /**
     * Hướng dẫn viên cập nhật trạng thái tour cho một booking.
     * Chỉ cho phép đổi khi tour đang ở in_progress hoặc checking_in.
     */
    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $tourGuide = auth()->user()->tour_guide;
        abort_unless($tourGuide, 403);

        // Đảm bảo guide được phân công lịch trình này
        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $booking->tour_schedule_id)
            ->exists();
        abort_unless($assigned, 403);

        // Chỉ cho phép đổi khi booking đang trong trạng thái guide kiểm soát
        $allowedCurrentStatuses = [Booking::TOUR_UPCOMING, Booking::TOUR_IN_PROGRESS, Booking::TOUR_CHECKING_IN];
        abort_unless(in_array($booking->tour_status, $allowedCurrentStatuses), 403);

        $request->validate([
            'tour_status' => 'required|in:in_progress,checking_in,completed',
            'current_checkin_step' => 'nullable|string|max:255',
        ]);

        $validStatuses = Booking::getValidNextStatuses($booking->tour_status);
        if (! in_array($request->tour_status, $validStatuses)) {
            return back()->with('error', 'Không thể chuyển đổi trạng thái tour từ trạng thái hiện tại sang trạng thái này (Không được nhảy cóc).');
        }

        $booking->tour_status = $request->tour_status;

        if ($request->tour_status === Booking::TOUR_CHECKING_IN) {
            $booking->current_checkin_step = $request->current_checkin_step;
        } else {
            $booking->current_checkin_step = null;
        }

        $booking->save();

        // Bắn thông báo cho Admin
        $tourTitle = $booking->tour_schedule->tour->title ?? '';
        $guideName = auth()->user()->name ?? 'Hướng dẫn viên';
        $statusLabels = [
            'upcoming' => 'Sắp bắt đầu',
            'in_progress' => 'Đang thực hiện',
            'checking_in' => 'Đang check-in'.($request->current_checkin_step ? " ({$request->current_checkin_step})" : ''),
            'completed' => 'Đã hoàn thành',
            'closed' => 'Đã đóng',
        ];
        $newStatusLabel = $statusLabels[$request->tour_status] ?? $request->tour_status;

        $message = "Đơn đặt chỗ #{$booking->code} của tour \"{$tourTitle}\" đã được Hướng dẫn viên {$guideName} chuyển trạng thái sang \"{$newStatusLabel}\".";

        $admins = Role::where('name', 'Admin')->exists() ? User::role('Admin')->get() : collect();
        if ($admins->count() > 0) {
            Notification::send($admins, new AdminBookingNotification(
                $booking,
                'tour_status_updated',
                $message,
                'Tour thay đổi trạng thái'
            ));
        }

        return back()->with('success', 'Đã cập nhật trạng thái tour thành công.');
    }

    /**
     * Hướng dẫn viên lưu điểm danh hàng loạt cho hành khách trong lịch trình.
     */
    public function saveAttendance(Request $request, TourSchedule $schedule): RedirectResponse
    {
        $tourGuide = auth()->user()->tour_guide;
        abort_unless($tourGuide, 403);

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $schedule->id)
            ->exists();
        abort_unless($assigned, 403);

        $request->validate([
            'checked_passengers' => 'nullable|array',
            'checked_passengers.*' => 'integer|exists:booking_passengers,id',
        ]);

        $checkedPassengerIds = $request->input('checked_passengers', []);

        // Lấy tất cả các passenger_ids của các booking thuộc schedule này
        $allPassengerIds = $schedule->bookings
            ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
            ->flatMap(fn ($b) => $b->booking_passengers->pluck('id'))
            ->toArray();

        if (empty($allPassengerIds)) {
            return back()->with('error', 'Không có hành khách nào để điểm danh.');
        }

        // Cập nhật checked_in = true cho các passenger được check
        BookingPassenger::whereIn('id', $allPassengerIds)
            ->whereIn('id', $checkedPassengerIds)
            ->update(['checked_in' => true]);

        // Cập nhật checked_in = false cho các passenger không được check
        BookingPassenger::whereIn('id', $allPassengerIds)
            ->whereNotIn('id', $checkedPassengerIds)
            ->update(['checked_in' => false]);

        return back()->with('success', 'Đã lưu danh sách điểm danh thành công.');
    }

    /**
     * Hướng dẫn viên cập nhật trạng thái tour cho tất cả bookings trong lịch trình.
     */
    public function updateGroupStatus(Request $request, TourSchedule $schedule): RedirectResponse
    {
        $tourGuide = auth()->user()->tour_guide;
        abort_unless($tourGuide, 403);

        $assigned = $tourGuide->schedule_guides()
            ->where('tour_schedule_id', $schedule->id)
            ->exists();
        abort_unless($assigned, 403);

        $request->validate([
            'tour_status' => 'required|in:in_progress,checking_in,completed',
            'current_checkin_step' => 'nullable|string|max:255',
        ]);

        $bookings = $schedule->bookings()
            ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER, Booking::TOUR_COMPLETED, 'closed'])
            ->whereNotIn('booking_status', ['cancelled'])
            ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
            ->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', 'Không có đơn đặt chỗ nào hoạt động để cập nhật.');
        }

        // Kiểm tra xem trạng thái mới có hợp lệ đối với tất cả bookings không (tránh nhảy cóc)
        foreach ($bookings as $booking) {
            $validStatuses = Booking::getValidNextStatuses($booking->tour_status);
            if (! in_array($request->tour_status, $validStatuses)) {
                return back()->with('error', 'Không thể chuyển đổi trạng thái tour (Không được nhảy cóc).');
            }
        }

        foreach ($bookings as $booking) {
            $booking->tour_status = $request->tour_status;
            if ($request->tour_status === Booking::TOUR_CHECKING_IN) {
                $booking->current_checkin_step = $request->current_checkin_step;
            } else {
                $booking->current_checkin_step = null;
            }
            $booking->save();
        }

        // Nếu hoàn thành tour, cập nhật trạng thái lịch trình luôn
        if ($request->tour_status === 'completed') {
            $schedule->update(['status' => 'completed']);
        }

        // Bắn thông báo cho Admin
        $firstBooking = $bookings->first();
        $tourTitle = $schedule->tour->title ?? '';
        $guideName = auth()->user()->name ?? 'Hướng dẫn viên';
        $statusLabels = [
            'upcoming' => 'Sắp bắt đầu',
            'in_progress' => 'Đang thực hiện',
            'checking_in' => 'Đang check-in'.($request->current_checkin_step ? " ({$request->current_checkin_step})" : ''),
            'completed' => 'Đã hoàn thành',
            'closed' => 'Đã đóng',
        ];
        $newStatusLabel = $statusLabels[$request->tour_status] ?? $request->tour_status;

        $message = "Đoàn của tour \"{$tourTitle}\" (Mã lịch trình: {$schedule->id}) đã được Hướng dẫn viên {$guideName} chuyển trạng thái sang \"{$newStatusLabel}\".";

        $admins = Role::where('name', 'Admin')->exists() ? User::role('Admin')->get() : collect();
        if ($admins->count() > 0) {
            Notification::send($admins, new AdminBookingNotification(
                $firstBooking,
                'tour_status_updated',
                $message,
                'Tour thay đổi trạng thái'
            ));
        }

        return back()->with('success', 'Trạng thái tập trung của cả đoàn đã được cập nhật.');
    }

    public function storeManualPassengers(Request $request, TourSchedule $schedule, Booking $booking)
    {
        $this->authorizeSchedule($schedule);
        if ($booking->tour_schedule_id !== $schedule->id) {
            abort(404);
        }

        $request->validate([
            'passengers' => 'required|array',
            'passengers.*.full_name' => 'required|string|max:255',
            'passengers.*.identity_number' => 'nullable|string|max:50',
            'passengers.*.date_of_birth' => 'nullable|date',
            'passengers.*.gender' => 'nullable|in:male,female,other',
            'passengers.*.passenger_type' => 'required|in:adult,child',
        ]);

        $leader = $booking->booking_passengers()->orderBy('id')->first();
        if ($leader) {
            $booking->booking_passengers()->where('id', '!=', $leader->id)->delete();
        } else {
            $booking->booking_passengers()->delete();
        }

        foreach ($request->passengers as $index => $pData) {
            if ($index == 0 && $leader) {
                $leader->update([
                    'full_name' => $pData['full_name'],
                    'identity_number' => $pData['identity_number'],
                    'date_of_birth' => $pData['date_of_birth'],
                    'gender' => $pData['gender'],
                ]);
            } else {
                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'full_name' => $pData['full_name'],
                    'identity_number' => $pData['identity_number'] ?? null,
                    'date_of_birth' => $pData['date_of_birth'] ?? null,
                    'gender' => $pData['gender'] ?? 'other',
                    'passenger_type' => $pData['passenger_type'],
                ]);
            }
        }

        $booking->update(['is_passenger_list_submitted' => true]);

        return back()->with('success', 'Đã bổ sung danh sách hành khách thành công!');
    }

    public function importExcelPassengers(Request $request, TourSchedule $schedule, Booking $booking)
    {
        $this->authorizeSchedule($schedule);
        if ($booking->tour_schedule_id !== $schedule->id) {
            abort(404);
        }

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $import = new PassengersImport($booking);
            Excel::import($import, $request->file('excel_file'));

            $booking->update(['is_passenger_list_submitted' => true]);

            return back()->with('success', 'Đã import danh sách hành khách thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi import: '.$e->getMessage());
        }
    }

    public function updateFreeTime(Request $request, TourSchedule $schedule, BookingPassenger $passenger)
    {
        $this->authorizeSchedule($schedule);
        if ($passenger->booking->tour_schedule_id !== $schedule->id) {
            abort(404);
        }

        $request->validate([
            'is_free_time' => 'required|boolean',
            'free_time_start' => 'nullable|date',
            'free_time_end' => 'nullable|date|after_or_equal:free_time_start',
            'free_time_location' => 'nullable|string|max:255',
        ]);

        $passenger->update([
            'is_free_time' => $request->is_free_time,
            'free_time_start' => $request->free_time_start,
            'free_time_end' => $request->free_time_end,
            'free_time_location' => $request->free_time_location,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thời gian tách đoàn thành công.',
                'passenger' => [
                    'id' => $passenger->id,
                    'is_free_time' => (bool) $passenger->is_free_time,
                    'free_time_start' => $passenger->free_time_start ? Carbon::parse($passenger->free_time_start)->format('Y-m-d\TH:i') : null,
                    'free_time_end' => $passenger->free_time_end ? Carbon::parse($passenger->free_time_end)->format('Y-m-d\TH:i') : null,
                    'free_time_location' => $passenger->free_time_location,
                ],
            ]);
        }

        return back()->with('success', 'Cập nhật thời gian tách đoàn thành công.');
    }

    public function togglePassengerActivityCheckin(Request $request, TourSchedule $schedule, TourActivity $activity, BookingPassenger $passenger)
    {
        $this->authorizeSchedule($schedule);

        if ($passenger->booking->tour_schedule_id !== $schedule->id) {
            abort(404);
        }

        $checkin = ActivityPassengerCheckin::where('tour_schedule_id', $schedule->id)
            ->where('tour_activity_id', $activity->id)
            ->where('booking_passenger_id', $passenger->id)
            ->first();

        if ($checkin) {
            $checkin->delete();
            $status = false;
        } else {
            ActivityPassengerCheckin::create([
                'tour_schedule_id' => $schedule->id,
                'tour_activity_id' => $activity->id,
                'booking_passenger_id' => $passenger->id,
            ]);
            $status = true;
        }

        return response()->json([
            'checked_in' => $status,
            'message' => $status ? 'Đã điểm danh' : 'Đã bỏ điểm danh',
        ]);
    }

    public function checkinAllActivityPassengers(Request $request, TourSchedule $schedule, TourActivity $activity)
    {
        $this->authorizeSchedule($schedule);

        $passengers = $schedule->bookings()
            ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
            ->whereNotIn('booking_status', ['cancelled'])
            ->whereIn('payment_status', ['pending', 'paid_30', 'paid_100'])
            ->with(['booking_passengers.group_splits' => function ($q) {
                $q->whereIn('status', ['ON_TIME', 'OVERDUE', 'UNREACHABLE']);
            }])
            ->get()
            ->flatMap->booking_passengers;

        $checkedInIds = [];

        foreach ($passengers as $passenger) {
            if ($passenger->group_splits->isEmpty()) {
                ActivityPassengerCheckin::firstOrCreate([
                    'tour_schedule_id' => $schedule->id,
                    'tour_activity_id' => $activity->id,
                    'booking_passenger_id' => $passenger->id,
                ]);
                $checkedInIds[] = $passenger->id;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã điểm danh tất cả khách tham gia.',
            'checked_in_ids' => $checkedInIds,
        ]);
    }

    public function uncheckAllActivityPassengers(Request $request, TourSchedule $schedule, TourActivity $activity)
    {
        $this->authorizeSchedule($schedule);

        ActivityPassengerCheckin::where('tour_schedule_id', $schedule->id)
            ->where('tour_activity_id', $activity->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy điểm danh tất cả khách.',
            'checked_in_ids' => [],
        ]);
    }
}
