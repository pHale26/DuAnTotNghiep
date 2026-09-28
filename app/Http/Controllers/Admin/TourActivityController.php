<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourActivity;
use App\Models\TourItinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourActivityController extends Controller
{
    public function store(Request $request, TourItinerary $itinerary)
    {
        $request->validate([
            'activity_type' => 'required|string',
            'start_time' => 'nullable',
            'title.vi' => 'required|string|max:255',
            'title.en' => 'nullable|string|max:255',
            'title.zh' => 'nullable|string|max:255',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = $request->except(['image_upload', 'title', 'description']);
        $data['title'] = [
            'vi' => $request->title['vi'],
            'en' => $request->title['en'] ?? $request->title['vi'],
            'zh' => $request->title['zh'] ?? $request->title['vi'],
        ];

        if ($request->hasFile('image_upload')) {
            $path = $request->file('image_upload')->store('activities', 'public');
            $data['image_url'] = '/storage/'.$path;
        }

        $itinerary->activities()->create($data);

        return back()->with('success', 'Thêm hoạt động thành công!');
    }

    public function update(Request $request, TourActivity $activity)
    {
        $request->validate([
            'activity_type' => 'required|string',
            'start_time' => 'nullable',
            'title.vi' => 'required|string|max:255',
            'title.en' => 'nullable|string|max:255',
            'title.zh' => 'nullable|string|max:255',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = $request->except(['image_upload', 'title']);
        $data['title'] = [
            'vi' => $request->title['vi'],
            'en' => $request->title['en'] ?? $request->title['vi'],
            'zh' => $request->title['zh'] ?? $request->title['vi'],
        ];

        if ($request->hasFile('image_upload')) {
            if ($activity->image_url && str_starts_with($activity->image_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $activity->image_url));
            }
            $path = $request->file('image_upload')->store('activities', 'public');
            $data['image_url'] = '/storage/'.$path;
        }

        $activity->update($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hoạt động thành công!',
                'data' => $activity,
            ]);
        }

        return back()->with('success', 'Cập nhật hoạt động thành công!');
    }

    public function destroy(TourActivity $activity)
    {
        if ($activity->image_url && str_starts_with($activity->image_url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $activity->image_url));
        }

        $activity->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa hoạt động!',
            ]);
        }

        return back()->with('success', 'Đã xóa hoạt động!');
    }
}
