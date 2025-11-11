# 🤖 GoCinema AI Chatbot Setup - OpenAI GPT Integration

## Overview
The chatbot has been upgraded to use **OpenAI GPT-3.5-turbo** for intelligent, context-aware responses.

## Setup Instructions

### 1. Environment Configuration
The API key should already be in your `.env` file:
```env
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 2. How It Works

#### Request Flow
```
User Message → ChatbotController@sendMessage
    ↓
Load Chat History from Session
    ↓
Build Message Array for OpenAI (with context)
    ↓
Call OpenAI GPT-3.5-turbo API
    ↓
Get AI Response
    ↓
Store in Session & Return to User
```

#### Key Features
- **Context Awareness**: Sends last 10 messages to OpenAI for better conversation flow
- **System Prompt**: Instructs AI to be helpful for GoCinema customers
- **Error Handling**: Falls back to friendly error messages if API fails
- **Session History**: Maintains conversation history across page reloads
- **Timeout Protection**: 30-second timeout to prevent hanging requests

### 3. Configuration Details

```php
// Model: gpt-3.5-turbo (fast and cost-effective)
// Temperature: 0.7 (balanced creativity)
// Max Tokens: 500 (reasonable response length)
// API Timeout: 30 seconds
```

### 4. System Prompt
The bot is instructed to:
- Act as an intelligent virtual assistant for GoCinema
- Focus on movie booking, movie search, promotions, account management
- Use Vietnamese language
- Use emojis for friendly communication
- Be professional yet approachable

### 5. Session Storage
- Chat history saved in Laravel session
- Persists across page reloads (via sessionStorage + server session)
- Format:
```php
[
    'role' => 'user|assistant',
    'content' => 'message text',
    'timestamp' => 'H:i'
]
```

### 6. API Costs
Example costs (as of Nov 2025):
- Input: $0.0005 per 1K tokens
- Output: $0.0015 per 1K tokens
- Average message: ~100 tokens (very cheap!)

### 7. Error Handling
If API fails, the bot will:
1. Log the error to `storage/logs/laravel.log`
2. Return a friendly error message to user
3. Suggest trying again later

### 8. Testing

#### Test the Endpoint
```bash
# Visit in browser or use curl
curl -X GET http://localhost/chatbot/test
```

#### Sample Conversation
The bot can handle:
- **Greetings**: "Xin chào", "Hello", "Chào"
- **Movie Search**: "Tìm phim hay", "Gợi ý phim"
- **Booking Help**: "Làm sao đặt vé", "Cách book vé"
- **Promotions**: "Có ưu đãi gì", "Voucher"
- **Account**: "Đổi mật khẩu", "Quên password"
- **Payment**: "Thanh toán như thế nào"
- **Support**: "Liên hệ support", "Gọi hotline"
- **General Questions**: Any question about GoCinema

### 9. Troubleshooting

#### "API key chưa được cấu hình"
- Check `.env` file has `OPENAI_API_KEY` set
- Run `php artisan config:clear`

#### "Tôi đang gặp vấn đề kết nối"
- Check internet connection
- Verify API key is valid (visit https://platform.openai.com/account/api-keys)
- Check OpenAI account has credits/billing set up
- Check logs: `storage/logs/laravel.log`

#### Slow Responses
- OpenAI API typically responds in 1-3 seconds
- Network issues can cause delays
- Consider increasing timeout if needed

### 10. Optional Enhancements

```php
// Future improvements:
// - Add rate limiting per user
// - Store conversation history in database for analytics
// - Add sentiment analysis
// - Integrate with movie database for real-time recommendations
// - Add user feedback (thumbs up/down)
// - Multi-language support
```

## Files Modified
- `app/Http/Controllers/ChatbotController.php` - OpenAI integration
- `.env` - OPENAI_API_KEY configuration

## Testing Checklist
- [x] Syntax errors checked
- [x] OpenAI API connection verified
- [x] Session storage working
- [x] Error handling in place
- [x] Chat history persistence
- [x] Chatbot popup in layout
- [x] CSRF token handling

---

**Developed**: November 2025  
**Status**: ✅ Ready for Production
