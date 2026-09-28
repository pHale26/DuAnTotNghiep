<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'tour']);

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleHidden($id)
    {
        $review = Review::findOrFail($id);
        $review->is_hidden = ! $review->is_hidden;
        $review->save();

        $status = $review->is_hidden ? 'ẩn' : 'hiện';

        return back()->with('success', "Đã {$status} đánh giá thành công!");
    }
}
