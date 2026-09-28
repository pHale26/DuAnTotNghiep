<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Review;
use App\Models\TicketBooking;
use App\Models\Tour;
use App\Models\TourGuide;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $now = now();
        $groupBy = 'day';

        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = Carbon::parse($endDateInput)->endOfDay();

            // Ensure startDate < endDate
            if ($startDate >= $endDate) {
                $startDate = $endDate->copy()->subDay()->startOfDay();
            }

            // Prevent future dates
            if ($endDate > $now->copy()->endOfDay()) {
                $endDate = $now->copy()->endOfDay();
                if ($startDate >= $endDate) {
                    $startDate = $endDate->copy()->subDay()->startOfDay();
                }
            }

            $periodLength = $startDate->diffInDays($endDate) + 1;

            $prevEndDate = $startDate->copy()->subDay()->endOfDay();
            $prevStartDate = $startDate->copy()->subDays($periodLength)->startOfDay();
        } else {
            // Default to last 30 days
            $periodLength = 30;
            $startDate = $now->copy()->subDays(29)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $prevStartDate = $now->copy()->subDays(59)->startOfDay();
            $prevEndDate = $now->copy()->subDays(30)->endOfDay();
        }

        // --- 1. KEY METRICS ---
        // Current Period
        $currentBookings = Booking::whereBetween('created_at', [$startDate, $endDate]);
        $totalBookings = $currentBookings->count();

        $totalTourRevenue = (clone $currentBookings)->whereIn('payment_status', [Booking::PAYMENT_PAID_100, Booking::PAYMENT_PAID_30, Booking::PAYMENT_PAID])->sum(DB::raw('IFNULL(paid_amount, IF(payment_status = "paid_100", total_price, 0))'));

        $currentTicketBookings = TicketBooking::whereBetween('created_at', [$startDate, $endDate]);
        $totalTicketRevenue = (clone $currentTicketBookings)->where('booking_status', 'completed')->sum('total_price');

        $totalRevenue = $totalTourRevenue + $totalTicketRevenue;

        $cancelledBookings = (clone $currentBookings)->whereIn('tour_status', [Booking::TOUR_CANCELLED_CUSTOMER, Booking::TOUR_CANCELLED_ADMIN])->count();
        $cancelRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;

        $newUsersCount = User::whereBetween('created_at', [$startDate, $endDate])->where('role', 'customer')->count();

        // Previous Period
        $prevBookings = Booking::whereBetween('created_at', [$prevStartDate, $prevEndDate]);
        $prevTotalBookings = $prevBookings->count();

        $prevTotalTourRevenue = (clone $prevBookings)->whereIn('payment_status', [Booking::PAYMENT_PAID_100, Booking::PAYMENT_PAID_30, Booking::PAYMENT_PAID])->sum(DB::raw('IFNULL(paid_amount, IF(payment_status = "paid_100", total_price, 0))'));
        $prevTicketBookings = TicketBooking::whereBetween('created_at', [$prevStartDate, $prevEndDate]);
        $prevTotalTicketRevenue = (clone $prevTicketBookings)->where('booking_status', 'completed')->sum('total_price');

        $prevTotalRevenue = $prevTotalTourRevenue + $prevTotalTicketRevenue;

        $prevCancelledBookings = (clone $prevBookings)->whereIn('tour_status', [Booking::TOUR_CANCELLED_CUSTOMER, Booking::TOUR_CANCELLED_ADMIN])->count();
        $prevCancelRate = $prevTotalBookings > 0 ? ($prevCancelledBookings / $prevTotalBookings) * 100 : 0;

        $prevNewUsersCount = User::whereBetween('created_at', [$prevStartDate, $prevEndDate])->where('role', 'customer')->count();

        // Diffs
        $diffBookings = $totalBookings - $prevTotalBookings;
        $diffRevenue = $totalRevenue - $prevTotalRevenue;
        $diffNewUsers = $newUsersCount - $prevNewUsersCount;
        $diffCancelRate = $cancelRate - $prevCancelRate;

        // --- 1.5 ACTIONABLE ITEMS ---
        $pendingBookingsCount = Booking::where('tour_status', Booking::TOUR_UPCOMING)->where('payment_status', Booking::PAYMENT_PENDING)->count();
        $unassignedGuidesCount = TourSchedule::whereDate('departure_date', '>=', now()->toDateString())
            ->whereDate('departure_date', '<=', now()->addDays(7)->toDateString())
            ->doesntHave('schedule_guides')
            ->count();
        $unreadMessagesCount = Message::whereNull('read_at')->where('sender_id', '!=', auth()->id())->count();

        // --- 1.6 MARKETING STATS ---
        $couponUsageCount = (clone $currentBookings)->whereNotNull('coupon_id')->count()
            + (clone $currentTicketBookings)->whereNotNull('coupon_id')->count();
        $totalDiscountAmount = (clone $currentBookings)->sum('discount_amount')
            + (clone $currentTicketBookings)->sum('discount_amount');

        // --- 2. REVENUE JOURNEY (Chart) ---
        $revenueJourney = Booking::whereBetween('created_at', [$startDate, $endDate]);
        if ($groupBy === 'month') {
            $revenueJourney->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date'),
                DB::raw('SUM(CASE WHEN payment_status = "'.Booking::PAYMENT_PAID_100.'" THEN total_price ELSE 0 END) as revenue'),
                DB::raw('COUNT(*) as orders')
            );
        } else {
            $revenueJourney->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN payment_status = "'.Booking::PAYMENT_PAID_100.'" THEN total_price ELSE 0 END) as revenue'),
                DB::raw('COUNT(*) as orders')
            );
        }

        $revenueJourney = $revenueJourney->groupBy('date')->orderBy('date', 'asc')->get();

        // Fill missing dates
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $key = $groupBy === 'month' ? $currentDate->format('Y-m') : $currentDate->format('Y-m-d');
            $dates[$key] = ['revenue' => 0, 'orders' => 0];

            if ($groupBy === 'month') {
                $currentDate->addMonth();
            } else {
                $currentDate->addDay();
            }
        }

        foreach ($revenueJourney as $item) {
            if (isset($dates[$item->date])) {
                $dates[$item->date] = ['revenue' => $item->revenue, 'orders' => $item->orders];
            }
        }

        // --- 3. CHECK-IN STATUS (Bar Chart) ---
        $checkinStatus = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('tour_status', DB::raw('count(*) as total'))
            ->groupBy('tour_status')
            ->pluck('total', 'tour_status')
            ->toArray();

        // --- 4. TOP DESTINATIONS ---
        $topDestinations = DB::table('bookings')
            ->join('tour_schedules', 'bookings.tour_schedule_id', '=', 'tour_schedules.id')
            ->join('tours', 'tour_schedules.tour_id', '=', 'tours.id')
            ->leftJoin('destinations', 'tours.destination_id', '=', 'destinations.id')
            ->leftJoin('provinces', 'tours.destination_province_id', '=', 'provinces.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(destinations.name, provinces.name, "Khác") as province_name'),
                DB::raw('COALESCE(destinations.id, provinces.id, 0) as province_id'),
                DB::raw('count(bookings.id) as total_bookings')
            )
            ->groupBy('province_id', 'province_name')
            ->orderBy('total_bookings', 'desc')
            ->take(5)
            ->get();

        $topDestinations->transform(function ($item) {
            $name = $item->province_name;
            if (is_string($name) && str_starts_with(trim($name), '{')) {
                $decoded = json_decode($name, true);
                $item->province_name = is_array($decoded) ? ($decoded['vi'] ?? $decoded['en'] ?? reset($decoded) ?: $name) : $name;
            }

            return $item;
        });

        $maxTopDestBookings = $topDestinations->max('total_bookings') ?? 1; // avoid div by zero

        // --- 4.5 TOP TOURS ---
        $topTours = DB::table('bookings')
            ->join('tour_schedules', 'bookings.tour_schedule_id', '=', 'tour_schedules.id')
            ->join('tours', 'tour_schedules.tour_id', '=', 'tours.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->select(
                'tours.id',
                'tours.title',
                DB::raw('count(bookings.id) as total_bookings')
            )
            ->groupBy('tours.id', 'tours.title')
            ->orderBy('total_bookings', 'desc')
            ->take(5)
            ->get();

        $topTours->transform(function ($item) {
            $title = $item->title;
            if (is_string($title) && str_starts_with(trim($title), '{')) {
                $decoded = json_decode($title, true);
                $item->title = is_array($decoded) ? ($decoded['vi'] ?? $decoded['en'] ?? reset($decoded) ?: $title) : $title;
            }

            return $item;
        });

        $maxTopToursBookings = $topTours->max('total_bookings') ?? 1;

        // --- 5. TOUR FILL RATE ---
        $today = now()->startOfDay();
        $tourFillRates = TourSchedule::with(['tour', 'schedule_guides'])
            ->withCount(['bookings as total_guests' => function ($q) {
                $q->select(DB::raw('SUM(adults_count + children_count)'))
                    ->whereNotIn('booking_status', ['cancelled', 'failed']);
            }])
            ->where('departure_date', '>=', $today)
            ->orderBy('departure_date', 'asc')
            ->take(8)
            ->get();

        // --- 6. RECENT BOOKINGS ---
        $recentBookings = Booking::with(['user', 'tour_schedule.tour'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // --- 7. CUSTOMER REVIEWS ---
        $averageRating = Review::avg('rating') ?? 0;
        $totalReviews = Review::count();
        $ratingDistribution = Review::select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Map 1-5 to ensure all keys exist
        $ratings = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $ratingDistribution[$i] ?? 0;
            $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
            $ratings[$i] = ['count' => $count, 'percent' => round($percent)];
        }

        // --- Guide Ratings ---
        $guideRatings = TourGuide::where('kpi_score', '>', 0)
            ->withCount('reviews')
            ->orderByDesc('kpi_score')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'periodLength', 'startDate', 'endDate',
            'totalBookings', 'totalRevenue', 'totalTourRevenue', 'totalTicketRevenue',
            'newUsersCount', 'cancelRate',
            'diffBookings', 'diffRevenue', 'diffNewUsers', 'diffCancelRate',
            'pendingBookingsCount', 'unassignedGuidesCount', 'unreadMessagesCount',
            'couponUsageCount', 'totalDiscountAmount',
            'dates', 'groupBy',
            'checkinStatus',
            'topDestinations', 'maxTopDestBookings',
            'topTours', 'maxTopToursBookings',
            'tourFillRates',
            'recentBookings',
            'averageRating', 'totalReviews', 'ratings',
            'guideRatings'
        ));
    }
}
