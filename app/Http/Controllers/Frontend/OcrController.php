<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrController extends Controller
{
    /**
     * Handle OCR scanning for CCCD/CMND using FPT AI.
     */
    public function scanCccd(Request $request)
    {
        try {
            $request->validate([
                'front_image' => 'required|image|max:5120', // Max 5MB
                'back_image' => 'nullable|image|max:5120',
            ]);

            $apiKey = config('services.fpt_ai.key');

            // Nếu chưa cấu hình API Key, trả về lỗi để FE dùng mock data
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key FPT AI chưa được cấu hình.',
                ], 400);
            }

            $frontImage = $request->file('front_image');
            $backImage = $request->file('back_image');

            // 1. Nhận diện mặt trước
            $response = Http::withHeaders([
                'api-key' => $apiKey,
            ])->attach(
                'image',
                file_get_contents($frontImage->getRealPath()),
                $frontImage->getClientOriginalName()
            )->post('https://api.fpt.ai/vision/idr/vnm');

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi kết nối đến server FPT AI.',
                ], 400);
            }

            $result = $response->json();
            if (! isset($result['errorCode']) || $result['errorCode'] !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => $result['errorMessage'] ?? 'Không thể nhận diện hình ảnh mặt trước.',
                ], 400);
            }

            $data = $result['data'][0] ?? [];
            $responseData = [
                'success' => true,
                'id' => $data['id'] ?? '',
                'name' => $data['name'] ?? '',
                'dob' => $data['dob'] ?? '',
                'sex' => strtolower($data['sex'] ?? ''),
                'issue_date' => $data['issue_date'] ?? '',
                'expiry_date' => $data['doe'] ?? '', // FPT API uses 'doe' for Date of Expiry
                'issue_place' => $data['issue_loc'] ?? '', // FPT API uses 'issue_loc'
            ];

            // 2. Nhận diện mặt sau (nếu có upload)
            if ($backImage) {
                $backResponse = Http::withHeaders([
                    'api-key' => $apiKey,
                ])->attach(
                    'image',
                    file_get_contents($backImage->getRealPath()),
                    $backImage->getClientOriginalName()
                )->post('https://api.fpt.ai/vision/idr/vnm');

                if ($backResponse->successful()) {
                    $backResult = $backResponse->json();
                    if (isset($backResult['errorCode']) && $backResult['errorCode'] === 0) {
                        $backData = $backResult['data'][0] ?? [];
                        if (! empty($backData['issue_date']) && $backData['issue_date'] !== 'N/A') {
                            $responseData['issue_date'] = $backData['issue_date'];
                        }
                        if (! empty($backData['issue_loc']) && $backData['issue_loc'] !== 'N/A') {
                            $responseData['issue_place'] = $backData['issue_loc'];
                        }
                    }
                }
            }

            return response()->json($responseData);

        } catch (\Exception $e) {
            Log::error('OCR Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình nhận diện.',
            ], 500);
        }
    }
}
