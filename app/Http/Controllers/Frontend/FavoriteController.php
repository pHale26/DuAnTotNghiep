<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Tour;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with([
            'tour.destination',
            'tour.tour_images',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('frontend.favorites.index', compact('favorites'));
    }

    public function toggle(Tour $tour)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('tour_id', $tour->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
            $message = 'Đã bỏ lưu tour.';
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'tour_id' => $tour->id,
            ]);
            $isFavorite = true;
            $message = 'Đã lưu tour yêu thích.';
        }

        // Kiểm tra nếu là AJAX request
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorite' => $isFavorite,
                'tour_id' => $tour->id,
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Tour $tour)
    {
        Favorite::where('user_id', Auth::id())
            ->where('tour_id', $tour->id)
            ->delete();

        $message = 'Đã xóa tour khỏi danh sách đã lưu.';

        // Kiểm tra nếu là AJAX request
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'tour_id' => $tour->id,
            ]);
        }

        return back()->with('success', $message);
    }
}
