<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\TourAbsenceRequest;
use App\Models\TourSchedule;
use App\Models\User;
use App\Notifications\Admin\NewAbsenceRequestNotification;
use App\Notifications\Admin\UrgentAbsenceRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class AbsenceRequestController extends Controller
{
    /**
     * Display the form to request absence.
     */
    public function create(TourSchedule $schedule)
    {
        $tourGuide = auth()->user()->tour_guide;
        if (! $tourGuide) {
            return redirect()->route('guide.dashboard')->with('error', 'Tài khoản chưa được liên kết với hồ sơ Hướng dẫn viên.');
        }

        // Check if guide is assigned as primary
        $scheduleGuide = $schedule->schedule_guides()
            ->where('guide_id', $tourGuide->id)
            ->first();

        if (! $scheduleGuide || $scheduleGuide->is_backup) {
            return redirect()->route('guide.schedules.show', $schedule->id)->with('error', 'Chỉ Hướng dẫn viên chính mới được quyền báo bận.');
        }

        // Check if tour is already started (by time or status)
        $firstBooking = $schedule->bookings()
            ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
            ->first();
        $groupStatus = $firstBooking ? $firstBooking->tour_status : 'upcoming';

        if ($groupStatus !== 'upcoming' || Carbon::parse($schedule->departure_date)->isPast()) {
            return redirect()->route('guide.schedules.show', $schedule->id)->with('error', 'Tour đã khởi hành, không thể báo bận.');
        }

        // Check for existing pending request
        $existingRequest = TourAbsenceRequest::where('tour_schedule_id', $schedule->id)
            ->where('main_guide_id', $tourGuide->id)
            ->whereIn('status', ['pending_review', 'pending_review_urgent'])
            ->exists();

        if ($existingRequest) {
            return redirect()->route('guide.schedules.show', $schedule->id)->with('error', 'Bạn đã gửi một yêu cầu đang chờ admin duyệt.');
        }

        return view('guide.schedules.absence', compact('schedule', 'tourGuide'));
    }

    /**
     * Store the absence request.
     */
    public function store(Request $request, TourSchedule $schedule): RedirectResponse
    {
        $tourGuide = auth()->user()->tour_guide;
        if (! $tourGuide) {
            return redirect()->route('guide.dashboard')->with('error', 'Tài khoản chưa được liên kết với hồ sơ Hướng dẫn viên.');
        }

        // Validate assignment and time limits
        $scheduleGuide = $schedule->schedule_guides()
            ->where('guide_id', $tourGuide->id)
            ->first();

        if (! $scheduleGuide || $scheduleGuide->is_backup) {
            return redirect()->route('guide.schedules.show', $schedule->id)->with('error', 'Chỉ Hướng dẫn viên chính mới được quyền báo bận.');
        }

        $firstBooking = $schedule->bookings()
            ->whereNotIn('tour_status', [Booking::TOUR_CANCELLED_ADMIN, Booking::TOUR_CANCELLED_CUSTOMER])
            ->first();
        $groupStatus = $firstBooking ? $firstBooking->tour_status : 'upcoming';

        if ($groupStatus !== 'upcoming' || Carbon::parse($schedule->departure_date)->isPast()) {
            return redirect()->route('guide.schedules.show', $schedule->id)->with('error', 'Tour đã khởi hành, không thể báo bận.');
        }

        $existingRequest = TourAbsenceRequest::where('tour_schedule_id', $schedule->id)
            ->where('main_guide_id', $tourGuide->id)
            ->whereIn('status', ['pending_review', 'pending_review_urgent'])
            ->exists();

        if ($existingRequest) {
            return redirect()->route('guide.schedules.show', $schedule->id)->with('error', 'Bạn đã gửi một yêu cầu đang chờ admin duyệt.');
        }

        // Validation
        $request->validate([
            'reason_type' => 'required|in:ốm đau,trùng lịch,việc gia đình,khác',
            'reason_custom' => 'required_if:reason_type,khác|nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // max 5MB
        ], [
            'reason_type.required' => 'Vui lòng chọn lý do báo bận.',
            'reason_custom.required_if' => 'Vui lòng nhập lý do chi tiết khi chọn lý do khác.',
            'attachment.mimes' => 'Tài liệu minh chứng phải là ảnh (jpeg, png, jpg) hoặc PDF.',
            'attachment.max' => 'Tài liệu minh chứng không được vượt quá 5MB.',
        ]);

        $reason = $request->input('reason_type');
        if ($reason === 'khác') {
            $reason = 'Khác: '.$request->input('reason_custom');
        }

        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('absence_attachments', 'public');
            $attachmentUrl = Storage::url($path);
        }

        // Determine urgency level
        $now = Carbon::now();
        $departure = Carbon::parse($schedule->departure_date);
        $hoursDiff = $now->diffInHours($departure, false);

        $threshold = (int) Setting::get('ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS', 24);

        if ($hoursDiff < $threshold) {
            $status = 'pending_review_urgent';
            $urgencyLevel = 'urgent';
        } else {
            $status = 'pending_review';
            $urgencyLevel = 'normal';
        }

        // Create absence request record
        $absenceRequest = TourAbsenceRequest::create([
            'tour_id' => $schedule->tour_id,
            'tour_schedule_id' => $schedule->id,
            'main_guide_id' => $tourGuide->id,
            'reason' => $reason,
            'attachment_url' => $attachmentUrl,
            'status' => $status,
            'urgency_level' => $urgencyLevel,
        ]);

        // Send notification to admins
        $admins = Role::where('name', 'Admin')->exists() ? User::role('Admin')->get() : collect();
        if ($admins->isNotEmpty()) {
            if ($status === 'pending_review_urgent') {
                Notification::send($admins, new UrgentAbsenceRequestNotification($absenceRequest));
            } else {
                Notification::send($admins, new NewAbsenceRequestNotification($absenceRequest));
            }
        }

        return redirect()->route('guide.schedules.show', $schedule->id)
            ->with('success', 'Gửi yêu cầu báo bận thành công. Vui lòng chờ admin duyệt.');
    }
}
