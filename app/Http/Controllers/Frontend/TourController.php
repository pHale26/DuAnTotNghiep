<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TourController extends Controller
{
    public function show($slug)
    {
        $tour = Tour::with([
            'destination',
            'departure_location',
            'tour_images',
            'tour_schedules' => function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today()->addDays(3))
                    ->where('status', 'available')
                    ->orderBy('departure_date', 'asc');
            },
            'tour_itineraries.activities',
            'categories',
            'tickets',
            'addons',
            'reviews' => function ($q) {
                $q->where('is_hidden', false)->latest();
            },
            'reviews.user',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $groupedActivities = $tour->tour_itineraries
            ->flatMap->activities
            ->groupBy('activity_type');

        $categoryIds = $tour->categories->pluck('id')->toArray();

        $relatedTours = Tour::with(['destination', 'tour_images'])
            ->where('id', '!=', $tour->id)
            ->when(! empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            })
            ->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today());
            })
            ->take(4)
            ->get();

        return view('frontend.tours.show', compact(
            'tour',
            'groupedActivities',
            'relatedTours'
        ));
    }

    public function aiSummary($id)
    {
        $cacheKey = 'tour_'.$id.'_ai_summary';

        if (Cache::has($cacheKey)) {
            $summary = Cache::get($cacheKey);
            if (! in_array($summary, [
                'Không thể kết nối đến dịch vụ AI.',
                'Không thể tạo tóm tắt vào lúc này do lỗi từ dịch vụ AI.',
                'Hệ thống chưa được cấu hình AI. Vui lòng liên hệ quản trị viên để cập nhật GEMINI_API_KEY.',
            ])) {
                return response()->json([
                    'success' => true,
                    'summary' => $summary,
                ]);
            }
            Cache::forget($cacheKey);
        }

        $tour = Tour::with(['reviews' => function ($q) {
            $q->where('is_hidden', false);
        }])->findOrFail($id);

        if ($tour->reviews->isEmpty()) {
            $summary = 'Chưa có đánh giá nào để tóm tắt.';
            Cache::put($cacheKey, $summary, now()->addDay());

            return response()->json(['success' => true, 'summary' => $summary]);
        }

        $comments = $tour->reviews->pluck('comment')->filter()->implode("\n- ");
        $tourName = $tour->title;

        $prompt = "Dưới đây là các đánh giá của khách hàng về tour '{$tourName}'. Hãy đóng vai là một trợ lý ảo, tóm tắt ngắn gọn trong 3-4 câu một cách khách quan nhất những điểm mạnh và điểm yếu (nếu có) chính mà khách hàng nhắc đến, sử dụng văn phong lịch sự, thân thiện. Không cần chào hỏi dài dòng.\nDanh sách đánh giá:\n- {$comments}";

        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['success' => true, 'summary' => 'Hệ thống chưa được cấu hình AI. Vui lòng liên hệ quản trị viên để cập nhật GEMINI_API_KEY.']);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $summary = $result['candidates'][0]['content']['parts'][0]['text'];
                    Cache::put($cacheKey, $summary, now()->addDay());

                    return response()->json(['success' => true, 'summary' => $summary]);
                }
            }

            Log::error('Gemini API Error', ['response' => $response->body()]);

            return response()->json(['success' => true, 'summary' => 'Không thể tạo tóm tắt vào lúc này do lỗi từ dịch vụ AI.']);
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['message' => $e->getMessage()]);

            return response()->json(['success' => true, 'summary' => 'Không thể kết nối đến dịch vụ AI.']);
        }
    }
}
