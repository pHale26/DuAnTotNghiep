<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', 1)
            ->where(function ($q) {
                $q->where('position', 'hero')
                    ->orWhereNull('position');
            })
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $adBanners = Banner::where('is_active', 1)
            ->where('position', 'home_ads')
            ->with('coupon')
            ->orderBy('sort_order')
            ->get();

        $destinations = Destination::withCount('tours')
            ->whereIn('id', function ($query) {
                $query->select('destination_id')
                    ->from('tours')
                    ->whereNull('deleted_at');
            })
            ->get();

        $categories = Category::all();

        $tours = Tour::with(['destination', 'tour_images', 'departure_location', 'categories', 'activeSchedules' => function ($q) {
            $q->orderBy('departure_date', 'asc')->limit(1);
        }])
            ->whereNull('deleted_at')
            ->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today()->addDays(3));
            })
            ->latest()
            ->take(8)
            ->get();

        $tickets = Ticket::with(['destination', 'ticket_options'])
            ->latest()
            ->take(12)
            ->get();

        $allDestinations = Destination::all();

        return view('welcome', compact(
            'banners',
            'adBanners',
            'destinations',
            'categories',
            'tours',
            'tickets',
            'allDestinations'
        ));
    }

    public function tours(Request $request)
    {
        $banners = Banner::where('is_active', 1)
            ->where(function ($q) {
                $q->where('position', 'hero')->orWhereNull('position');
            })
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $adBanners = Banner::where('is_active', 1)
            ->where('position', 'home_ads')
            ->with('coupon')
            ->orderBy('sort_order')
            ->get();

        $allDestinations = Destination::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $filterErrors = [];
        $date = $request->input('date') ?? $request->input('departure_date');
        if ($date) {
            try {
                $parsedDate = Carbon::parse($date);
                $threeDaysLater = Carbon::today()->addDays(3);
                if ($parsedDate->lt($threeDaysLater)) {
                    $filterErrors['departure_date'] = [__('Ngày khởi hành phải cách ngày hiện tại ít nhất 3 ngày.')];
                }
            } catch (\Exception $e) {
                // Ignore parse errors
            }
        }

        $query = Tour::with([
            'destination',
            'tour_images',
            'departure_location',
            'categories',
            'activeSchedules' => function ($q) {
                $q->orderBy('departure_date', 'asc')->limit(1);
            },
        ])
            ->whereNull('deleted_at');

        if (! empty($filterErrors)) {
            $query->whereRaw('1 = 0');
        } else {
            $query->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today()->addDays(3));
            });
        }

        if ($request->filled('ids') && is_array($request->ids)) {
            $query->whereIn('id', $request->ids);
        }

        if ($request->filled('keyword')) {
            $keyword = mb_strtolower($request->keyword, 'UTF-8');
            $matchedDest = Destination::whereRaw('LOWER(name) = ?', [$keyword])->first();
            if ($matchedDest) {
                $request->merge(['destination_id' => $matchedDest->id]);
                $request->offsetUnset('keyword');
            } else {
                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(CAST(title AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%'])
                        ->orWhereHas('destination', function ($q2) use ($keyword) {
                            $q2->whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%']);
                        });
                });
            }
        }

        if ($request->filled('transport')) {
            $transport = $request->transport;
            if (Schema::hasColumn('tours', 'transport_type')) {
                $query->where('transport_type', $transport);
            } else {
                if ($transport === 'xe') {
                    $query->where(function ($q) {
                        $q->whereRaw("LOWER(CAST(title AS CHAR)) LIKE '%xe%'")
                            ->orWhereRaw("LOWER(CAST(ai_tags AS CHAR)) LIKE '%xe%'");
                    });
                } elseif ($transport === 'bay') {
                    $query->where(function ($q) {
                        $q->whereRaw("LOWER(CAST(title AS CHAR)) LIKE '%bay%'")
                            ->orWhereRaw("LOWER(CAST(ai_tags AS CHAR)) LIKE '%bay%'")
                            ->orWhereRaw("LOWER(CAST(title AS CHAR)) LIKE '%máy bay%'");
                    });
                }
            }
        }

        if ($request->filled('departure_id')) {
            $query->where('departure_location_id', $request->departure_id);
        }

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($date && empty($filterErrors['departure_date'])) {
            try {
                $formattedDate = Carbon::parse($date)->toDateString();
            } catch (\Exception $e) {
                $formattedDate = null;
            }
            if ($formattedDate) {
                $query->whereHas('activeSchedules', function ($q) use ($formattedDate) {
                    $q->whereDate('departure_date', '=', $formattedDate);
                });
            }
        }

        if ($request->filled('stars')) {
            if (Schema::hasColumn('tours', 'hotel_stars')) {
                $query->where('hotel_stars', $request->stars);
            }
        }

        if ($request->filled('budget')) {
            match ($request->budget) {
                'under_1m' => $query->where('base_price', '<', 1000000),
                '1m_2m', '1m_to_2m' => $query->whereBetween('base_price', [1000000, 2000000]),
                '2m_4m', '2m_to_4m' => $query->whereBetween('base_price', [2000000, 4000000]),
                'over_4m' => $query->where('base_price', '>', 4000000),
                default => null,
            };
        }

        if ($request->filled('duration')) {
            match ($request->duration) {
                '2d1n' => $query->where('duration_days', 2)->where('duration_nights', 1),
                '3d2n' => $query->where('duration_days', 3)->where('duration_nights', 2),
                '4d3n' => $query->where('duration_days', 4)->where('duration_nights', 3),
                '5d4n' => $query->where('duration_days', 5)->where('duration_nights', 4),
                '6d5n' => $query->where('duration_days', 6)->where('duration_nights', 5),
                '7d6n' => $query->where('duration_days', 7)->where('duration_nights', 6),
                default => null,
            };
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('base_price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('base_price', 'desc');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $tours = $query->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('frontend.tours._results_list', compact('tours', 'categories', 'allDestinations', 'filterErrors'))->render();
        }

        return view('frontend.tours.index', compact('banners', 'tours', 'adBanners', 'allDestinations', 'categories', 'filterErrors'));
    }

    public function searchTours(Request $request)
    {
        $banners = Banner::where('is_active', true)->where('position', 'top')->get();
        $destinations = Destination::orderBy('name')->get();
        $categories = Category::all();

        $query = Tour::with(['destination', 'departure_location', 'tour_images'])
            ->whereNull('deleted_at')
            ->whereHas('activeSchedules', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today()->addDays(3));
            });

        if ($request->filled('keyword')) {
            $keyword = mb_strtolower($request->keyword, 'UTF-8');
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(CAST(title AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%'])
                    ->orWhereHas('destination', function ($q2) use ($keyword) {
                        $q2->whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%']);
                    });
            });
        }

        if ($request->filled('transport')) {
            $transport = $request->transport;
            if (Schema::hasColumn('tours', 'transport_type')) {
                $query->where('transport_type', $transport);
            } else {
                if ($transport === 'xe') {
                    $query->where(function ($q) {
                        $q->whereRaw("LOWER(CAST(title AS CHAR)) LIKE '%xe%'")
                            ->orWhereRaw("LOWER(CAST(ai_tags AS CHAR)) LIKE '%xe%'");
                    });
                } elseif ($transport === 'bay') {
                    $query->where(function ($q) {
                        $q->whereRaw("LOWER(CAST(title AS CHAR)) LIKE '%bay%'")
                            ->orWhereRaw("LOWER(CAST(ai_tags AS CHAR)) LIKE '%bay%'")
                            ->orWhereRaw("LOWER(CAST(title AS CHAR)) LIKE '%máy bay%'");
                    });
                }
            }
        }

        if ($request->filled('departure_id')) {
            $query->where('departure_location_id', $request->departure_id);
        }

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->filled('date')) {
            $date = $request->date;
            try {
                $formattedDate = Carbon::parse($date)->toDateString();
            } catch (\Exception $e) {
                $formattedDate = null;
            }
            if ($formattedDate) {
                $query->whereHas('activeSchedules', function ($q) use ($formattedDate) {
                    $q->whereDate('departure_date', '=', $formattedDate);
                });
            }
        }

        if ($request->filled('budget')) {
            match ($request->budget) {
                'under_5m' => $query->where('base_price', '<', 5000000),
                '5m_to_10m' => $query->whereBetween('base_price', [5000000, 10000000]),
                '10m_to_20m' => $query->whereBetween('base_price', [10000000, 20000000]),
                'over_20m' => $query->where('base_price', '>', 20000000),
                default => null,
            };
        }

        if ($request->filled('duration')) {
            match ($request->duration) {
                '2d1n' => $query->where('duration_days', 2)->where('duration_nights', 1),
                '3d2n' => $query->where('duration_days', 3)->where('duration_nights', 2),
                '4d3n' => $query->where('duration_days', 4)->where('duration_nights', 3),
                '5d4n' => $query->where('duration_days', 5)->where('duration_nights', 4),
                '6d5n' => $query->where('duration_days', 6)->where('duration_nights', 5),
                '7d6n' => $query->where('duration_days', 7)->where('duration_nights', 6),
                default => null,
            };
        }

        if ($request->sort === 'price_asc') {
            $query->orderBy('base_price', 'asc');
        } elseif ($request->sort === 'price_desc') {
            $query->orderBy('base_price', 'desc');
        } else {
            $query->latest();
        }

        $tours = $query->paginate(9)->withQueryString();

        if ($request->ajax()) {
            return view('frontend.tours._results', compact('tours'))->render();
        }

        return view('frontend.tours.search', compact('tours', 'destinations', 'categories', 'banners'));
    }

    public function searchDestinations(Request $request)
    {
        $keyword = mb_strtolower($request->get('q', ''), 'UTF-8');

        if (strlen($keyword) < 1) {
            return response()->json([]);
        }

        $destinations = Destination::whereRaw('LOWER(CAST(name AS CHAR)) LIKE BINARY ?', ['%'.$keyword.'%'])
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($destinations->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
        ]));
    }

    public function aiSuggest(Request $request)
    {
        $emotion = $request->input('emotion');
        $health = $request->input('health');

        if (! $emotion || ! $health) {
            return redirect()->back()->with('error', 'Vui lòng nhập đầy đủ cảm xúc và tình hình sức khỏe.');
        }

        try {
            $tours = Tour::select('id', 'title', 'description', 'ai_tags')
                ->whereNull('deleted_at')
                ->get()
                ->map(function ($tour) {
                    $title = is_array($tour->title) ? ($tour->title['vi'] ?? '') : $tour->title;

                    return [
                        'id' => $tour->id,
                        'title' => $title,
                        'ai_tags' => $tour->ai_tags,
                    ];
                })->toArray();

            $prompt = "Bạn là một chuyên gia tư vấn du lịch. Dựa vào cảm xúc: '{$emotion}' và tình trạng sức khỏe: '{$health}' của khách hàng, hãy chọn ra tối đa 4 tour phù hợp nhất từ danh sách sau:\n".json_encode($tours, JSON_UNESCAPED_UNICODE)."\nTrả về MỘT mảng JSON chứa danh sách các ID của tour được chọn (ví dụ: [1, 2, 3]). Không kèm theo bất kỳ văn bản, markdown format (như ```json) hay giải thích nào khác ngoài mảng JSON.";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key='.env('GEMINI_API_KEY'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                $ids = json_decode(trim($text), true);

                if (! is_array($ids)) {
                    $ids = [];
                }

                if (count($ids) > 0) {
                    return redirect()->route('frontend.tours.index', ['ids' => $ids, 'ai_suggest' => 1]);
                } else {
                    return redirect()->route('frontend.tours.index')->with('error', 'AI không tìm thấy tour nào phù hợp hoàn toàn, bạn có thể tham khảo các tour khác dưới đây.');
                }
            } else {
                Log::error('Gemini API Error', ['response' => $response->body()]);

                return redirect()->route('frontend.tours.index')->with('error', 'Có lỗi khi kết nối với AI. Vui lòng thử lại sau.');
            }
        } catch (\Exception $e) {
            Log::error('Gemini Suggestion Error', ['error' => $e->getMessage()]);

            return redirect()->route('frontend.tours.index')->with('error', 'Đã xảy ra lỗi hệ thống khi gọi AI.');
        }
    }
}
