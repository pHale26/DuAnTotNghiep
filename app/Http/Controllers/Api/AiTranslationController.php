<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTranslationController extends Controller
{
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $text = $request->input('text');

        try {
            $prompt = "Bạn là một biên dịch viên du lịch chuyên nghiệp. Hãy dịch đoạn văn bản Tiếng Việt sau sang Tiếng Anh và Tiếng Trung Quốc (Giản thể).\n\nĐoạn văn bản:\n\"{$text}\"\n\nTrả về MỘT JSON hợp lệ duy nhất có định dạng chính xác như sau, không kèm bất kỳ giải thích hay markdown code block nào:\n{\"en\": \"Bản dịch Tiếng Anh\", \"zh\": \"Bản dịch Tiếng Trung\"}";

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
                $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

                $translations = json_decode(trim($responseText), true);

                if (is_array($translations) && isset($translations['en']) && isset($translations['zh'])) {
                    return response()->json([
                        'success' => true,
                        'data' => $translations,
                    ]);
                }
            }

            Log::error('Gemini Translation Error', ['response' => $response->body()]);

            return response()->json(['success' => false, 'message' => 'Lỗi khi dịch qua AI.'], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Translation Exception', ['error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống.'], 500);
        }
    }
}
