<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleGuide;
use App\Models\TourGuide;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OngoingTourController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all'); // 'all', 'upcoming', 'ongoing'
        $today = now()->startOfDay();

        $query = TourSchedule::with(['tour', 'schedule_guides.tour_guide'])
            ->withCount(['bookings as total_guests' => function ($q) {
                $q->select(\DB::raw('SUM(adults_count + children_count)'))
                    ->whereNotIn('booking_status', ['cancelled', 'failed']);
            }]);

        if ($status === 'upcoming') {
            $query->whereNotIn('status', ['completed', 'closed'])
                ->where('departure_date', '>', $today);
        } elseif ($status === 'ongoing') {
            $query->whereNotIn('status', ['completed', 'closed'])
                ->where('departure_date', '<=', $today)
                ->where('return_date', '>=', $today);
        } elseif ($status === 'completed') {
            $query->where(function ($q) use ($today) {
                $q->whereIn('status', ['completed', 'closed'])
                    ->orWhere('return_date', '<', $today);
            });
        }

        // Search filters
        if ($request->filled('search_tour')) {
            $searchTour = $request->input('search_tour');
            $query->where(function ($q) use ($searchTour) {
                $q->whereHas('tour', function ($sq) use ($searchTour) {
                    $sq->where('title', 'like', "%{$searchTour}%");
                })->orWhere('id', 'like', "%{$searchTour}%");
            });
        }

        if ($request->filled('search_date')) {
            $query->whereDate('departure_date', $request->input('search_date'));
        }

        if ($request->filled('search_guests')) {
            $searchGuests = $request->input('search_guests');
            $query->whereRaw('(SELECT COALESCE(SUM(adults_count + children_count), 0) FROM bookings WHERE bookings.tour_schedule_id = tour_schedules.id AND booking_status NOT IN (\'cancelled\', \'failed\')) >= ?', [$searchGuests]);
        }

        if ($request->filled('search_guide')) {
            $searchGuide = $request->input('search_guide');
            $query->whereHas('schedule_guides.tour_guide', function ($q) use ($searchGuide) {
                $q->where('name', 'like', "%{$searchGuide}%");
            });
        }

        $schedules = $query->orderByRaw("CASE WHEN tour_schedules.status IN ('completed', 'closed') OR tour_schedules.return_date < CURRENT_DATE() THEN 1 ELSE 0 END")
            ->orderBy('departure_date', 'asc')
            ->paginate(15);
        $tourGuides = TourGuide::all(); // Cho modal gán hướng dẫn viên

        // Xác định các HDV bị trùng lịch cho mỗi schedule
        foreach ($schedules as $schedule) {
            $schedule->busy_guide_ids = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
                ->whereHas('tour_schedule', function ($q) use ($schedule) {
                    $q->whereNotIn('status', ['completed', 'closed'])
                        ->where('departure_date', '<=', $schedule->return_date)
                        ->where('return_date', '>=', $schedule->departure_date);
                })
                ->pluck('guide_id')
                ->toArray();
        }

        $unassignedUpcomingSchedules = TourSchedule::with('tour')
            ->whereNotIn('status', ['completed', 'closed'])
            ->where('departure_date', '>=', $today)
            ->where('departure_date', '<=', $today->copy()->addDays(7))
            ->doesntHave('schedule_guides')
            ->orderBy('departure_date', 'asc')
            ->get();

        foreach ($unassignedUpcomingSchedules as $schedule) {
            $schedule->busy_guide_ids = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
                ->whereHas('tour_schedule', function ($q) use ($schedule) {
                    $q->whereNotIn('status', ['completed', 'closed'])
                        ->where('departure_date', '<=', $schedule->return_date)
                        ->where('return_date', '>=', $schedule->departure_date);
                })
                ->pluck('guide_id')
                ->toArray();
        }

        return view('admin.ongoing_tours.index', compact('schedules', 'tourGuides', 'status', 'unassignedUpcomingSchedules'));
    }

    public function assignGuides(Request $request, TourSchedule $schedule)
    {
        $request->validate([
            'primary_guide_id' => 'nullable|exists:tour_guides,id',
            'backup_guide_id' => 'nullable|exists:tour_guides,id|different:primary_guide_id',
        ], [
            'backup_guide_id.different' => 'HDV dự bị không được trùng với HDV chính.',
        ]);

        if (in_array($schedule->status, ['completed', 'closed']) || Carbon::parse($schedule->return_date)->endOfDay()->isPast()) {
            return redirect()->back()->with('error', 'Tour đã kết thúc hoặc đã qua ngày về, không thể thay đổi hướng dẫn viên.');
        }

        $primaryId = $request->filled('primary_guide_id') ? $request->primary_guide_id : null;
        $backupId = $request->filled('backup_guide_id') ? $request->backup_guide_id : null;

        // Kiểm tra trùng lịch
        foreach (array_filter([$primaryId, $backupId]) as $guideId) {
            $isBusy = ScheduleGuide::where('tour_schedule_id', '!=', $schedule->id)
                ->where('guide_id', $guideId)
                ->whereHas('tour_schedule', function ($q) use ($schedule) {
                    $q->whereNotIn('status', ['completed', 'closed'])
                        ->where('departure_date', '<=', $schedule->return_date)
                        ->where('return_date', '>=', $schedule->departure_date);
                })
                ->exists();

            if ($isBusy) {
                $guideName = TourGuide::find($guideId)?->name ?? 'Hướng dẫn viên';

                return redirect()->back()->with('error', "Hướng dẫn viên {$guideName} đã được phân công cho một lịch trình khác trùng thời gian này.");
            }

            $guide = TourGuide::find($guideId);
            if ($guide && $guide->is_blacklisted) {
                return redirect()->back()->with('error', "Hướng dẫn viên {$guide->name} đang trong danh sách đen (Blacklist). Không thể phân công!");
            }
        }

        // Xóa phân công cũ và lưu mới
        $schedule->schedule_guides()->delete();

        if ($primaryId) {
            $schedule->schedule_guides()->create([
                'guide_id' => $primaryId,
                'is_backup' => false,
            ]);
        }

        if ($backupId) {
            $schedule->schedule_guides()->create([
                'guide_id' => $backupId,
                'is_backup' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Đã cập nhật danh sách Hướng dẫn viên cho Lịch trình!');
    }
}
