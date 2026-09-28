<?php

use App\Models\Tour;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Http;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$tour = Tour::where('title', 'like', '%cano 4 đảo Phú Quốc & Cáp treo Hòn Thơm%')->first();
$comments = $tour->reviews()->where('is_hidden', false)->pluck('comment')->filter()->implode("\n- ");
$tourName = $tour->title;
$prompt = "Dưới đây là các đánh giá của khách hàng về tour '{$tourName}'. Hãy đóng vai là một trợ lý ảo, tóm tắt ngắn gọn trong 3-4 câu một cách khách quan nhất những điểm mạnh và điểm yếu (nếu có) chính mà khách hàng nhắc đến, sử dụng văn phong lịch sự, thân thiện. Không cần chào hỏi dài dòng.\nDanh sách đánh giá:\n- {$comments}";

$apiKey = env('GEMINI_API_KEY');
echo 'API Key length: '.strlen($apiKey)."\n";
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
