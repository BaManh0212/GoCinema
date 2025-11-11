<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /**
     * Display chatbot page (optional - not used, popup is in layout).
     */
    public function index()
    {
        // Chatbot is embedded in client layout, so we can redirect home
        return redirect('/');
    }

    /**
     * Test endpoint - simple health check
     */
    public function test()
    {
        return response()->json(['status' => 'ok', 'message' => 'Chatbot endpoint is working']);
    }

    /**
     * Send user message and get bot response using OpenAI GPT API.
     * Chat history is stored in session.
     */
    public function sendMessage(Request $request)
    {
        // Simple validation
        $message = trim($request->input('message', ''));
        
        if (empty($message) || strlen($message) > 500) {
            return response()
                ->json(['error' => 'Invalid message', 'reply' => 'Tin nhắn không hợp lệ.'], 400)
                ->header('Content-Type', 'application/json; charset=utf-8');
        }

        try {
            // Get or initialize chat history from session
            $chatHistory = session('chat_history', []);

            // Generate bot response using OpenAI API
            $botReply = $this->generateAIResponse($message, $chatHistory);

            // Add messages to history
            $chatHistory[] = [
                'role' => 'user',
                'content' => $message,
                'timestamp' => now()->format('H:i'),
            ];

            $chatHistory[] = [
                'role' => 'bot',
                'content' => $botReply,
                'timestamp' => now()->format('H:i'),
            ];

            // Save to session
            session(['chat_history' => $chatHistory]);

            // Return JSON response with explicit headers
            return response()
                ->json([
                    'success' => true,
                    'reply' => $botReply,
                    'timestamp' => now()->format('H:i'),
                    'history_count' => count($chatHistory)
                ])
                ->header('Content-Type', 'application/json; charset=utf-8')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        } catch (\Exception $e) {
            return response()
                ->json([
                    'error' => 'Server error',
                    'reply' => 'Có lỗi xảy ra trên server: ' . $e->getMessage()
                ], 500)
                ->header('Content-Type', 'application/json; charset=utf-8');
        }
    }

    /**
     * Clear chat history from session.
     */
    public function clearChat()
    {
        session()->forget('chat_history');
        return response()->json(['status' => 'ok']);
    }

    /**
     * Generate AI response using Groq API (Free & Fast)
     */
    private function generateAIResponse($userMessage, $chatHistory)
    {
        $apiKey = env('GROQ_API_KEY');
        
        if (!$apiKey) {
            return 'Xin lỗi, API key chưa được cấu hình. Vui lòng liên hệ support! 🔧';
        }

        try {
            // Build messages for Groq API
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Bạn là một trợ lý ảo thông minh của GoCinema - một nền tảng đặt vé xem phim online hàng đầu Việt Nam. ' .
                                'Hãy trả lời câu hỏi của khách hàng một cách thân thiện, chuyên nghiệp và hữu ích. ' .
                                'Tập trung vào giúp người dùng về đặt vé, tìm phim, ưu đãi, tài khoản, và các dịch vụ khác của GoCinema. ' .
                                'Hãy sử dụng emoji để làm phong phú đáp ứng. Trả lời bằng tiếng Việt.'
                ]
            ];

            // Add chat history (last 10 messages to provide context)
            $historyCount = min(10, count($chatHistory));
            for ($i = count($chatHistory) - $historyCount; $i < count($chatHistory); $i++) {
                if ($i >= 0) {
                    $messages[] = [
                        'role' => $chatHistory[$i]['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $chatHistory[$i]['content']
                    ];
                }
            }

            // Add current user message
            $messages[] = [
                'role' => 'user',
                'content' => $userMessage
            ];

            // Call Groq API
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(45)->post('https://api.groq.com/openai/v1/chat/completions', [
                // Make model configurable via .env, default to a supported model
                'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->failed()) {
                $status = $response->status();
                $body = $response->body();

                \Log::error('Groq API Error', [
                    'status' => $status,
                    'body' => $body,
                ]);

                // If in debug, expose brief error detail to help developer
                if (config('app.debug')) {
                    // Try to parse JSON error message
                    $err = @json_decode($body, true);
                    $detail = $err['error']['message'] ?? ($err['message'] ?? substr($body, 0, 300));
                    return "Lỗi gọi Groq API (HTTP $status): " . $detail;
                }

                return 'Xin lỗi, tôi đang gặp vấn đề kết nối. Vui lòng thử lại sau! 🔄';
            }

            $data = $response->json();
            
            if (isset($data['choices'][0]['message']['content'])) {
                return $data['choices'][0]['message']['content'];
            } else {
                return 'Xin lỗi, tôi không thể xử lý yêu cầu của bạn. Vui lòng thử lại! 🤔';
            }

        } catch (\Exception $e) {
            \Log::error('Chatbot AI Error: ' . $e->getMessage());
            return 'Xin lỗi, có lỗi xảy ra: ' . $e->getMessage() . ' Vui lòng thử lại sau! 🔄';
        }
    }
}
