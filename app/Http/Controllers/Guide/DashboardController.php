<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tourGuide = $user->tour_guide;

        $stats = [
            'total_schedules' => 0,
            'upcoming_schedules' => 0,
            'ongoing_schedules' => 0,
            'completed_schedules' => 0,
        ];

        $recentSchedules = collect();

        if ($tourGuide) {
            $stats['total_schedules'] = $tourGuide->schedule_guides()->count();

            $stats['upcoming_schedules'] = $tourGuide->schedule_guides()
                ->whereHas('tour_schedule', function ($q) {
                    $q->whereNotIn('status', ['completed', 'closed'])
                        ->where('departure_date', '>', now());
                })->count();

            $stats['ongoing_schedules'] = $tourGuide->schedule_guides()
                ->whereHas('tour_schedule', function ($q) {
                    $q->whereNotIn('status', ['completed', 'closed'])
                        ->where('departure_date', '<=', now())
                        ->where('return_date', '>=', now()->startOfDay());
                })->count();

            $stats['completed_schedules'] = $tourGuide->schedule_guides()
                ->whereHas('tour_schedule', function ($q) {
                    $q->where(function ($sq) {
                        $sq->whereIn('status', ['completed', 'closed'])
                            ->orWhere('return_date', '<', now()->startOfDay());
                    });
                })->count();

            $recentSchedules = $tourGuide->schedule_guides()
                ->with('tour_schedule.tour')
                ->whereHas('tour_schedule', function ($q) {
                    $q->whereNotIn('status', ['completed', 'closed'])
                        ->where('return_date', '>=', now()->startOfDay());
                })
                ->get()
                ->sortBy(function ($sg) {
                    return $sg->tour_schedule->departure_date;
                })
                ->take(5);
        }

        return view('guide.dashboard', compact('tourGuide', 'stats', 'recentSchedules'));
    }
}
