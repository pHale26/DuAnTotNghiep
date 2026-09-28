<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Tour;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Http;

$tour = Tour::where('title', 'like', '%Cáp treo Hòn Thơm%')->with('reviews')->first();
if (! $tour) {
    echo "Tour not found\n";
    exit;
}

$comments = $tour->reviews->pluck('comment')->filter()->implode("\n- ");
$tourName = $tour->title;

$prompt = "Dưới đây là các đánh giá của khách hàng về tour '{$tourName}'. Hãy đóng vai là một trợ lý ảo, tóm tắt ngắn gọn trong 3-4 câu một cách khách quan nhất những điểm mạnh và điểm yếu (nếu có) chính mà khách hàng nhắc đến, sử dụng văn phong lịch sự, thân thiện. Không cần chào hỏi dài dòng.\nDanh sách đánh giá:\n- {$comments}";

echo 'Number of reviews: '.$tour->reviews->count()."\n";
echo 'Prompt length: '.strlen($prompt)."\n";

$apiKey = env('GEMINI_API_KEY');
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

echo 'Status: '.$response->status()."\n";
echo 'Body: '.$response->body()."\n";
