<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Tour;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function getHistory(Request $request)
    {
        $sessionToken = $request->cookie('chatbot_session') ?? $request->header('X-Chatbot-Session');

        if (! $sessionToken) {
            return response()->json(['messages' => []]);
        }

        $session = ChatSession::where('session_id', $sessionToken)->first();

        if (! $session) {
            return response()->json(['messages' => []]);
        }

        $messages = $session->messages()
            ->whereIn('role', ['user', 'model'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'role' => $m->role === 'user' ? 'user' : 'assistant',
                    'content' => $m->content,
                    'created_at' => $m->created_at->format('H:i d/m/Y'),
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $sessionToken = $request->cookie('chatbot_session') ?? $request->header('X-Chatbot-Session');
        $isNewSession = false;

        if (! $sessionToken) {
            $sessionToken = (string) Str::uuid();
            $isNewSession = true;
        }

        $session = ChatSession::firstOrCreate(
            ['session_id' => $sessionToken],
            ['user_id' => Auth::id()]
        );

        // Store user message
        $session->messages()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        try {
            // Generate response, handle potential tool calls in a loop
            $maxIter = 5;
            $iter = 0;

            while ($iter < $maxIter) {
                $messages = $session->messages()->orderBy('created_at', 'asc')->get()->map(function ($m) {
                    return [
                        'role' => $m->role,
                        'content' => $m->content,
                        'tool_calls' => $m->tool_calls,
                        'name' => $m->name ?? null, // for function response
                    ];
                })->toArray();

                $response = $this->chatbotService->chat($messages);

                if ($response['type'] === 'text') {
                    $cleanContent = $this->sanitizeContentLinks($response['content']);

                    $session->messages()->create([
                        'role' => 'model',
                        'content' => $cleanContent,
                    ]);

                    $cookie = cookie('chatbot_session', $sessionToken, 60 * 24 * 30); // 30 days

                    return response()->json([
                        'role' => 'assistant',
                        'content' => $cleanContent,
                        'session_token' => $sessionToken,
                    ])->withCookie($cookie);
                } elseif ($response['type'] === 'function_call') {
                    // Store the model's tool call message with raw_parts
                    $session->messages()->create([
                        'role' => 'model',
                        'tool_calls' => [
                            'name' => $response['name'],
                            'arguments' => $response['arguments'],
                            'raw_parts' => $response['raw_parts'] ?? [],
                        ],
                    ]);

                    // Execute local function
                    $funcResponse = $this->chatbotService->handleLocalFunctionCall($response['name'], $response['arguments']);

                    // Store the function response
                    $session->messages()->create([
                        'role' => 'function',
                        'content' => json_encode($funcResponse, JSON_UNESCAPED_UNICODE),
                        'tool_calls' => ['name' => $response['name']],
                    ]);
                }

                $iter++;
            }

            return response()->json([
                'role' => 'assistant',
                'content' => 'Xin lỗi, tôi đang xử lý quá nhiều thông tin. Vui lòng thử lại sau.',
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: '.$e->getMessage());

            return response()->json([
                'role' => 'assistant',
                'content' => 'Xin lỗi, hệ thống AI đang bảo trì. Vui lòng thử lại sau ít phút.',
            ], 500);
        }
    }

    protected function sanitizeContentLinks(string $content): string
    {
        return preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/u', function ($matches) {
            $text = trim($matches[1]);
            $url = trim($matches[2]);

            // If it's already a valid local /tours/ URL without fake domain, keep it
            if (str_contains($url, '/tours/') && ! str_contains($url, 'travelwonder.vn')) {
                return "[{$text}]({$url})";
            }

            // Search if $text matches any tour title
            $cleanTitle = preg_replace('/[*_👉]/u', '', $text);
            $cleanTitle = trim($cleanTitle);

            $tour = Tour::where('title', 'like', "%{$cleanTitle}%")->first();
            if (! $tour) {
                // Try searching with prominent words
                $words = preg_split('/\s+/', $cleanTitle);
                if (count($words) >= 2) {
                    $key = $words[0].' '.$words[1];
                    $tour = Tour::where('title', 'like', "%{$key}%")->first();
                }
            }

            if ($tour) {
                $realUrl = route('frontend.tours.show', $tour->slug);

                return "[{$text}]({$realUrl})";
            }

            // If no match found, output plain bold text instead of broken link
            return "**{$cleanTitle}**";
        }, $content);
    }
}
