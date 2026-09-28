<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Holiday;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Hiển thị trang Lịch Tour.
     */
    public function index()
    {
        $destinations = Destination::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('frontend.tours.calendar', compact('destinations', 'categories'));
    }

    /**
     * API trả về dữ liệu lịch tour + ngày lễ theo tháng/bộ lọc (JSON).
     */
    public function data(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $destinationId = $request->get('destination_id');
        $categoryId = $request->get('category_id');
        $budget = $request->get('budget');
        $duration = $request->get('duration');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // --- Tour schedules ---
        $query = TourSchedule::with([
            'tour.primaryImage',
            'tour.destination',
            'tour.categories',
        ])
            ->whereBetween('departure_date', [$startDate, $endDate])
            ->whereIn('status', ['available', 'full'])
            ->orderBy('departure_date', 'asc');

        // Lọc theo điểm đến
        if ($destinationId) {
            $query->whereHas('tour', function ($q) use ($destinationId) {
                $q->where('destination_id', $destinationId);
            });
        }

        // Lọc theo danh mục tour
        if ($categoryId) {
            $query->whereHas('tour.categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        // Lọc theo ngân sách (khớp với trang index)
        if ($budget) {
            $query->whereHas('tour', function ($q) use ($budget) {
                match ($budget) {
                    'under_1m' => $q->where('base_price', '<', 1000000),
                    '1m_2m' => $q->whereBetween('base_price', [1000000, 2000000]),
                    '2m_4m' => $q->whereBetween('base_price', [2000000, 4000000]),
                    'over_4m' => $q->where('base_price', '>', 4000000),
                    default => null,
                };
            });
        }

        // Lọc theo thời gian (số ngày)
        if ($duration) {
            $query->whereHas('tour', function ($q) use ($duration) {
                match ($duration) {
                    '2d1n' => $q->where('duration_days', 2)->where('duration_nights', 1),
                    '3d2n' => $q->where('duration_days', 3)->where('duration_nights', 2),
                    '4d3n' => $q->where('duration_days', 4)->where('duration_nights', 3),
                    '5d4n' => $q->where('duration_days', 5)->where('duration_nights', 4),
                    '6d5n' => $q->where('duration_days', 6)->where('duration_nights', 5),
                    '7d6n' => $q->where('duration_days', 7)->where('duration_nights', 6),
                    default => null,
                };
            });
        }

        $schedules = $query->get();

        // Nhóm theo ngày
        $grouped = $schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->departure_date)->format('Y-m-d');
        });

        $tours = [];

        foreach ($grouped as $dateStr => $daySchedules) {
            $toursForDay = $daySchedules->map(function ($schedule) {
                $tour = $schedule->tour;

                $imageUrl = null;
                if ($tour->primaryImage) {
                    $raw = $tour->primaryImage->image_url ?? null;
                    if ($raw) {
                        $imageUrl = (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://'))
                            ? $raw
                            : asset($raw);
                    }
                }

                return [
                    'schedule_id' => $schedule->id,
                    'tour_id' => $tour->id,
                    'tour_name' => $tour->title,
                    'tour_slug' => $tour->slug,
                    'tour_url' => route('frontend.tours.show', $tour->slug),
                    'destination' => $tour->destination?->name,
                    'duration' => $tour->duration_days.'N'.$tour->duration_nights.'Đ',
                    'price' => $tour->base_price,
                    'available_seats' => $schedule->available_seats,
                    'capacity' => $schedule->capacity,
                    'status' => $schedule->status,
                    'image_url' => $imageUrl,
                ];
            })->values();

            $tours[$dateStr] = $toursForDay;
        }

        // --- Ngày lễ trong tháng ---
        $holidays = Holiday::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->get(['name', 'start_date', 'end_date']);

        // Mở rộng khoảng ngày lễ thành từng ngày riêng lẻ
        $holidayMap = [];
        foreach ($holidays as $holiday) {
            $cursor = Carbon::parse($holiday->start_date);
            $end = Carbon::parse($holiday->end_date);
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m-d');
                if (! isset($holidayMap[$key])) {
                    $holidayMap[$key] = [];
                }
                $holidayMap[$key][] = $holiday->name;
                $cursor->addDay();
            }
        }

        return response()->json([
            'tours' => $tours,
            'holidays' => $holidayMap,
        ]);
    }
}
