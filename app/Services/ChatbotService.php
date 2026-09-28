<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\Tour;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-3.1-flash-lite');
    }

    protected function getSystemPrompt(): string
    {
        return 'Bạn là trợ lý ảo hỗ trợ khách hàng của công ty du lịch Travel Wonder.

QUY TẮC XƯNG HÔ VÀ PHONG CÁCH (BẮT BUỘC ĐỒNG NHẤT 100%):
- Luôn luôn xưng là "Em" hoặc "Travel Wonder".
- Luôn luôn gọi khách hàng là "Quý khách" (hoặc "Anh/Chị").
- TUYỆT ĐỐI KHÔNG xưng "Tôi", KHÔNG gọi khách là "bạn" hoặc "mình" trong mọi câu trả lời.
- Giọng văn: Lịch sự, nhã nhặn, chu đáo, chuẩn phong cách chăm sóc khách hàng du lịch cao cấp.
- Trình bày: Sử dụng định dạng Markdown rõ ràng (dấu gạch đầu dòng, in đậm tên tour và giá tiền).

DANH MỤC ĐIỂM ĐẾN & BẢNG GIÁ THAM KHẢO TẠI TRAVEL WONDER:
- Phân khúc siêu tiết kiệm (Dưới 1.000.000 VND):
  + [Đà Lạt] Săn Mây Đồi Chè Cầu Đất - Nửa ngày: **450.000 VND** (Tour có giá thấp nhất toàn hệ thống)
  + [Hà Nội] Food Tour phố cổ Hà Nội về đêm: **600.000 VND**
  + [Hà Nội] Hà Nội City Tour Tuyến Cổ Điển: **800.000 VND**
  + [Phú Quốc / Miền Nam] Khám phá Nam Đảo - Hoàng hôn Sunset Sanato: **850.000 VND** (Tour có giá thấp nhất tại Miền Nam / Phú Quốc)
  + [Đà Nẵng / Miền Trung] Đà Nẵng - Huế - Dấu ấn cố đô: **950.000 VND**
  + [Hà Nội] Hà Nội - Ninh Bình (Hoa Lư - Tam Cốc): **950.000 VND**
- Phân khúc từ 1.000.000 VND - 2.000.000 VND:
  + [Hạ Long] Tham quan Vịnh Hạ Long Tuyến 2 (6 Tiếng): **1.050.000 VND**
  + [Phú Quốc / Miền Nam] Tour cano 4 đảo Phú Quốc & Cáp treo Hòn Thơm: **1.200.000 VND**
  + [Sapa] Trekking bản Lao Chải - Tả Van: **1.500.000 VND**
  + [Đà Nẵng] Tour Cù Lao Chàm lặn ngắm san hô: **1.800.000 VND**
  + [Sapa] Sapa mùa lúa chín - Moana: **1.800.000 VND**
  + [Đà Lạt] Cắm trại thung lũng Vàng BBQ: **1.800.000 VND**
- Phân khúc trọn gói & nghỉ dưỡng (Trên 2.000.000 VND):
  + [Đà Lạt] Đà Lạt 3N2Đ Chinh phục Langbiang: **2.500.000 VND**
  + [Sapa] Sapa - Cát Cát - Fansipan: **2.800.000 VND**
  + [Hạ Long] Tour Hạ Long - Sun World Park: **2.800.000 VND**
  + [Hạ Long] Du thuyền 5 sao Vịnh Hạ Long: **3.200.000 VND**
  + [Đà Nẵng] Khám phá Đà Nẵng - Hội An - Bà Nà Hills: **3.500.000 VND**
  + [Phú Quốc / Miền Nam] Nghỉ dưỡng Vinpearl Phú Quốc trọn gói 3N2Đ: **5.900.000 VND**

QUY TẮC TƯ VẤN TOUR THEO YÊU CẦU:
- Khi Quý khách hỏi tour rẻ nhất / tour tiết kiệm:
  + Nếu hỏi chung: Giới thiệu các tour tiêu biểu từ thấp đến cao như Đà Lạt (450k), Hà Nội (600k), Phú Quốc (850k).
  + Nếu hỏi Miền Nam: Giới thiệu tour Nam Đảo Phú Quốc (850k), Cano 4 đảo Phú Quốc (1.200k), và có thể gợi ý thêm tour gần phía Nam như Đà Lạt Săn Mây (450k) để Quý khách có thêm lựa chọn siêu tiết kiệm.
- Khi Quý khách hỏi tour theo vùng miền (Miền Nam, Miền Trung, Miền Bắc, Tây Nguyên): Gọi tool search_tours với keyword hoặc region tương ứng.
- Khi Quý khách có ngân sách tối đa: Gọi tool search_tours với max_price.

QUY TẮC BẮT BUỘC VỀ ĐƯỜNG LINK (TUYỆT ĐỐI KHÔNG TỰ BỊA LINK):
- BẤT KỲ KHI NÀO giới thiệu, gợi ý hoặc nhắc đến một tour, BẮT BUỘC phải gọi công cụ search_tours hoặc get_tour_itinerary để lấy chính xác trường "url" THẬT do hệ thống trả về.
- TUYỆT ĐỐI KHÔNG tự bịa đặt link, KHÔNG tự phỏng đoán URL, KHÔNG dùng tên miền giả (CẤM dùng https://travelwonder.vn/..., CẤM tự chế link dạng /da-nang-hue-co-do...).
- NẾU chưa gọi tool để lấy URL, CHỈ ĐƯỢC PHÉP in đậm tên tour dạng **Tên Tour** chứ TUYỆT ĐỐI KHÔNG gắn link giả.
- Cú pháp gắn link khi có url từ tool: [**Tên Tour**](url) hoặc [👉 Xem chi tiết & Đặt tour](url).

CHÍNH SÁCH HOÀN TIỀN CỦA TRAVEL WONDER:
1. Hủy trước 7 ngày: Hoàn 100% số tiền đã thanh toán.
2. Hủy từ 3 đến 7 ngày: Hoàn 50% số tiền đã thanh toán.
3. Hủy trong vòng 3 ngày: Không hoàn tiền (0%).
Lưu ý: Tiền hoàn được kế toán chuyển khoản thủ công qua ngân hàng, khách nhận email tự động xác nhận.';
    }

    protected function getToolsDeclaration(): array
    {
        return [
            [
                'functionDeclarations' => [
                    [
                        'name' => 'search_tours',
                        'description' => 'Tìm kiếm các tour du lịch hiện có trong hệ thống dựa trên từ khóa, điểm đến, vùng miền, giá, hoặc sắp xếp theo giá.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'keyword' => [
                                    'type' => 'STRING',
                                    'description' => 'Từ khóa tìm kiếm hoặc điểm đến (VD: Phú Quốc, Sapa, Đà Lạt, Hạ Long, Miền Nam, Miền Bắc, Miền Trung...)',
                                ],
                                'region' => [
                                    'type' => 'STRING',
                                    'description' => 'Vùng miền nếu có: mien_nam, mien_bac, mien_trung, tay_nguyen',
                                ],
                                'sort_by' => [
                                    'type' => 'STRING',
                                    'description' => 'Sắp xếp: price_asc (rẻ nhất / tăng dần), price_desc (cao cấp nhất / giảm dần), newest',
                                ],
                                'max_price' => [
                                    'type' => 'NUMBER',
                                    'description' => 'Mức giá tối đa mà khách có thể trả',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'get_tour_itinerary',
                        'description' => 'Lấy thông tin chi tiết lịch trình của một tour cụ thể dựa vào ID tour.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'tour_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID của tour',
                                ],
                            ],
                            'required' => ['tour_id'],
                        ],
                    ],
                    [
                        'name' => 'search_accommodations',
                        'description' => 'Tìm kiếm thông tin nơi lưu trú, khách sạn, resort theo điểm đến hoặc vùng miền.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'destination_name' => [
                                    'type' => 'STRING',
                                    'description' => 'Tên điểm đến hoặc vùng miền (VD: Phú Quốc, Đà Lạt, Đà Nẵng, Hạ Long, Miền Nam, Miền Bắc...)',
                                ],
                                'min_star' => [
                                    'type' => 'INTEGER',
                                    'description' => 'Hạng sao tối thiểu (VD: 3, 4, 5)',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function handleLocalFunctionCall(string $functionName, array $args): array
    {
        switch ($functionName) {
            case 'search_tours':
                return $this->searchTours($args);
            case 'get_tour_itinerary':
                return $this->getTourItinerary($args);
            case 'search_accommodations':
                return $this->searchAccommodations($args);
            default:
                return ['error' => 'Function not found'];
        }
    }

    protected function resolveRegionPlaces(?string $term): ?array
    {
        if (empty($term)) {
            return null;
        }

        $termLower = mb_strtolower(trim($term));

        if (str_contains($termLower, 'nam') || str_contains($termLower, 'phú quốc') || str_contains($termLower, 'kiên giang') || str_contains($termLower, 'tây nam') || str_contains($termLower, 'miền tây')) {
            return ['Phú Quốc', 'Kiên Giang', 'Nam Đảo', 'Hòn Thơm', 'Vinpearl'];
        }

        if (str_contains($termLower, 'trung') || str_contains($termLower, 'đà nẵng') || str_contains($termLower, 'hội an') || str_contains($termLower, 'huế') || str_contains($termLower, 'đà lạt') || str_contains($termLower, 'tây nguyên') || str_contains($termLower, 'lâm đồng')) {
            return ['Đà Nẵng', 'Hội An', 'Huế', 'Đà Lạt', 'Bà Nà', 'Cù Lao Chàm', 'Langbiang', 'Cầu Đất'];
        }

        if (str_contains($termLower, 'bắc') || str_contains($termLower, 'hà nội') || str_contains($termLower, 'sapa') || str_contains($termLower, 'hạ long') || str_contains($termLower, 'ninh bình')) {
            return ['Hà Nội', 'Sapa', 'Hạ Long', 'Ninh Bình', 'Fansipan', 'Cát Cát', 'Tràng An'];
        }

        return null;
    }

    protected function searchTours(array $args): array
    {
        $query = Tour::with(['destination', 'categories']);

        $regionPlaces = $this->resolveRegionPlaces($args['region'] ?? null);
        if (! $regionPlaces && ! empty($args['keyword'])) {
            $regionPlaces = $this->resolveRegionPlaces($args['keyword']);
        }

        if ($regionPlaces) {
            $query->where(function ($q) use ($regionPlaces) {
                foreach ($regionPlaces as $place) {
                    $q->orWhere('title', 'like', "%{$place}%")
                        ->orWhere('description', 'like', "%{$place}%")
                        ->orWhereHas('destination', function ($q2) use ($place) {
                            $q2->where('name', 'like', "%{$place}%");
                        });
                }
            });
        } elseif (! empty($args['keyword'])) {
            $keyword = trim($args['keyword']);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('destination', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('categories', function ($q3) use ($keyword) {
                        $q3->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if (! empty($args['max_price'])) {
            $query->where('base_price', '<=', $args['max_price']);
        }

        // Sorting
        $sortBy = $args['sort_by'] ?? 'price_asc';
        if ($sortBy === 'price_desc') {
            $query->orderBy('base_price', 'desc');
        } elseif ($sortBy === 'newest') {
            $query->latest();
        } else {
            $query->orderBy('base_price', 'asc');
        }

        $tours = $query->take(8)->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'destination' => $t->destination->name ?? '',
                'base_price' => number_format($t->base_price).' VND',
                'duration' => $t->duration_days.' ngày '.$t->duration_nights.' đêm',
                'url' => route('frontend.tours.show', $t->slug),
            ];
        });

        $result = ['tours' => $tours->toArray()];

        if ($tours->isEmpty()) {
            $suggested = Tour::with('destination')->orderBy('base_price', 'asc')->take(4)->get()->map(function ($t) {
                return [
                    'id' => $t->id,
                    'title' => $t->title,
                    'destination' => $t->destination->name ?? '',
                    'base_price' => number_format($t->base_price).' VND',
                    'duration' => $t->duration_days.' ngày '.$t->duration_nights.' đêm',
                    'url' => route('frontend.tours.show', $t->slug),
                ];
            });
            $result['suggested_tours'] = $suggested->toArray();
        }

        return $result;
    }

    protected function getTourItinerary(array $args): array
    {
        if (empty($args['tour_id'])) {
            return ['error' => 'Missing tour_id'];
        }

        $tour = Tour::with('tour_itineraries')->find($args['tour_id']);
        if (! $tour) {
            return ['error' => 'Tour not found'];
        }

        $itineraries = $tour->tour_itineraries->map(function ($i) {
            return [
                'day' => $i->day_number,
                'title' => $i->title,
                'description' => strip_tags($i->description),
            ];
        });

        return [
            'tour_name' => $tour->title,
            'url' => route('frontend.tours.show', $tour->slug),
            'itineraries' => $itineraries->toArray(),
        ];
    }

    protected function searchAccommodations(array $args): array
    {
        $query = Accommodation::with('destination')->where('is_active', true);

        $destTerm = $args['destination_name'] ?? null;
        $regionPlaces = $this->resolveRegionPlaces($destTerm);

        if ($regionPlaces) {
            $query->where(function ($q) use ($regionPlaces) {
                foreach ($regionPlaces as $place) {
                    $q->orWhere('name', 'like', "%{$place}%")
                        ->orWhereHas('destination', function ($q2) use ($place) {
                            $q2->where('name', 'like', "%{$place}%");
                        });
                }
            });
        } elseif (! empty($destTerm)) {
            $query->where(function ($q) use ($destTerm) {
                $q->where('name', 'like', "%{$destTerm}%")
                    ->orWhereHas('destination', function ($q2) use ($destTerm) {
                        $q2->where('name', 'like', "%{$destTerm}%");
                    });
            });
        }

        if (! empty($args['min_star'])) {
            $query->where('star_rating', '>=', $args['min_star']);
        }

        $accommodations = $query->take(6)->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'name' => $a->name,
                'star_rating' => $a->star_rating,
                'address' => $a->address,
                'destination' => $a->destination->name ?? '',
            ];
        });

        return ['accommodations' => $accommodations->toArray()];
    }

    public function chat(array $messages): array
    {
        $geminiContents = [];
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                continue;
            }

            if ($msg['role'] === 'function') {
                $funcName = is_array($msg['tool_calls']) && isset($msg['tool_calls']['name']) ? $msg['tool_calls']['name'] : 'unknown';
                $decoded = json_decode($msg['content'], true);
                $respContent = is_array($decoded) ? $decoded : ['result' => $msg['content']];

                $geminiContents[] = [
                    'role' => 'user',
                    'parts' => [
                        [
                            'functionResponse' => [
                                'name' => $funcName,
                                'response' => $respContent,
                            ],
                        ],
                    ],
                ];
            } elseif (! empty($msg['tool_calls'])) {
                $toolCalls = is_string($msg['tool_calls']) ? json_decode($msg['tool_calls'], true) : $msg['tool_calls'];

                if (isset($toolCalls['raw_parts']) && is_array($toolCalls['raw_parts'])) {
                    $geminiContents[] = [
                        'role' => 'model',
                        'parts' => $toolCalls['raw_parts'],
                    ];
                } else {
                    $functionCalls = [];
                    if (isset($toolCalls['name'])) {
                        $functionCalls[] = [
                            'functionCall' => [
                                'name' => $toolCalls['name'],
                                'args' => $toolCalls['arguments'] ?? [],
                            ],
                        ];
                    } elseif (is_array($toolCalls)) {
                        foreach ($toolCalls as $call) {
                            if (is_array($call) && isset($call['name'])) {
                                $functionCalls[] = [
                                    'functionCall' => [
                                        'name' => $call['name'],
                                        'args' => $call['arguments'] ?? [],
                                    ],
                                ];
                            }
                        }
                    }
                    $geminiContents[] = [
                        'role' => 'model',
                        'parts' => ! empty($functionCalls) ? $functionCalls : [['text' => '']],
                    ];
                }
            } else {
                $role = $msg['role'] === 'user' ? 'user' : 'model';
                $geminiContents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $msg['content'] ?? ''],
                    ],
                ];
            }
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $this->getSystemPrompt()],
                ],
            ],
            'contents' => $geminiContents,
            'tools' => $this->getToolsDeclaration(),
        ];

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API Error: '.$response->body());
            throw new \Exception('Failed to communicate with AI Chatbot.');
        }

        $data = $response->json();

        $candidateParts = $data['candidates'][0]['content']['parts'] ?? [];
        if (empty($candidateParts)) {
            return [
                'type' => 'text',
                'content' => 'Xin lỗi, tôi chưa thể trả lời câu hỏi của bạn lúc này.',
            ];
        }

        // Search for functionCall in all returned parts
        foreach ($candidateParts as $part) {
            if (isset($part['functionCall'])) {
                return [
                    'type' => 'function_call',
                    'name' => $part['functionCall']['name'],
                    'arguments' => $part['functionCall']['args'] ?? [],
                    'raw_parts' => $candidateParts,
                ];
            }
        }

        // If no function call, collect text
        $fullText = '';
        foreach ($candidateParts as $part) {
            if (isset($part['text'])) {
                $fullText .= $part['text'];
            }
        }

        return [
            'type' => 'text',
            'content' => $fullText ?: 'Xin lỗi, tôi chưa thể trả lời câu hỏi của bạn lúc này.',
        ];
    }
}
