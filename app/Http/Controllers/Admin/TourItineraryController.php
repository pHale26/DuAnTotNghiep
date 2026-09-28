<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourItinerary;
use Illuminate\Http\Request;

class TourItineraryController extends Controller
{
    public function index(Tour $tour)
    {
        $itineraries = $tour->tour_itineraries()->with('activities')->orderBy('day_number')->get();

        return view('admin.itineraries.index', compact('tour', 'itineraries'));
    }

    public function store(Request $request, Tour $tour)
    {
        $request->validate([
            'day_number' => 'required|integer|min:1|max:'.$tour->duration_days,
            'title.vi' => 'required|string|max:255',
            'title.en' => 'nullable|string|max:255',
            'title.zh' => 'nullable|string|max:255',
        ], [
            'day_number.max' => 'Số ngày không được vượt quá tổng số ngày của tour ('.$tour->duration_days.' ngày).',
        ]);

        $data = $request->except(['title', 'description']);
        $data['title'] = [
            'vi' => $request->title['vi'],
            'en' => $request->title['en'] ?? $request->title['vi'],
            'zh' => $request->title['zh'] ?? $request->title['vi'],
        ];
        $data['description'] = [
            'vi' => $request->description['vi'] ?? '',
            'en' => $request->description['en'] ?? ($request->description['vi'] ?? ''),
            'zh' => $request->description['zh'] ?? ($request->description['vi'] ?? ''),
        ];

        $tour->tour_itineraries()->create($data);

        return back()->with('success', 'Thêm ngày lịch trình thành công!');
    }

    public function update(Request $request, TourItinerary $itinerary)
    {
        $request->validate([
            'day_number' => 'required|integer|min:1',
            'title.vi' => 'required|string|max:255',
            'title.en' => 'nullable|string|max:255',
            'title.zh' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['title', 'description']);
        $data['title'] = [
            'vi' => $request->title['vi'],
            'en' => $request->title['en'] ?? $request->title['vi'],
            'zh' => $request->title['zh'] ?? $request->title['vi'],
        ];
        $data['description'] = [
            'vi' => $request->description['vi'] ?? '',
            'en' => $request->description['en'] ?? ($request->description['vi'] ?? ''),
            'zh' => $request->description['zh'] ?? ($request->description['vi'] ?? ''),
        ];

        $itinerary->update($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật ngày thành công!',
                'data' => $itinerary,
            ]);
        }

        return back()->with('success', 'Cập nhật ngày thành công!');
    }

    public function destroy(TourItinerary $itinerary)
    {
        $itinerary->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ngày lịch trình!',
            ]);
        }

        return back()->with('success', 'Đã xóa ngày lịch trình!');
    }
}
