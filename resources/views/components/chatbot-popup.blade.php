<!-- Chatbot Popup Component -->
<div id="chatbot-popup" class="chatbot-popup hidden">
    {{-- Header --}}
    <div class="chatbot-header">
        <div class="chatbot-header-content">
            <div class="chatbot-avatar bot-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div class="chatbot-header-text">
                <h4>GoCinema Bot</h4>
                <p class="status">Đang hoạt động</p>
            </div>
        </div>
        <button id="chatbot-close-btn" class="chatbot-close-btn" title="Đóng">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Messages Container --}}
    <div id="chatbot-messages" class="chatbot-messages">
        <div class="chatbot-message bot-message">
            <div class="chatbot-avatar bot-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div class="message-content">
                <p>Xin chào! 👋 Tôi là trợ lý ảo của GoCinema. Tôi có thể giúp bạn về đặt vé, ưu đãi, tìm phim, hoặc bất kỳ câu hỏi nào. Hãy nhắn cho tôi! 😊</p>
            </div>
        </div>
    </div>

    {{-- Input Area --}}
    <div class="chatbot-input-area">
        <input
            type="text"
            id="chatbot-input"
            class="chatbot-input"
            placeholder="Nhập tin nhắn..."
            maxlength="500"
        >
        <button id="chatbot-send-btn" class="chatbot-send-btn" title="Gửi">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

{{-- Chatbot Toggle Button --}}
<button id="chatbot-toggle-btn" class="chatbot-toggle-btn" title="Mở chat">
    <i class="bi bi-chat-dots-fill"></i>
    <span class="chatbot-badge">1</span>
</button>

<style>
    /* Chatbot Toggle Button */
    .chatbot-toggle-btn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e53935, #f44336);
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(229, 57, 53, 0.4);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s ease;
        z-index: 999;
    }

    .chatbot-toggle-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(229, 57, 53, 0.5);
    }

    .chatbot-toggle-btn:active {
        transform: scale(0.95);
    }

    .chatbot-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #ffc107;
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }

    /* Chatbot Popup */
    .chatbot-popup {
        position: fixed;
        bottom: 96px;
        right: 24px;
        width: 360px;
        height: 540px;
        background: linear-gradient(180deg, #0f1625, #0d1420);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 999;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .chatbot-popup.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    /* Header */
    .chatbot-header {
        padding: 16px;
        background: linear-gradient(135deg, rgba(229, 57, 53, 0.15), rgba(244, 67, 54, 0.1));
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-header-content {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .chatbot-header-text h4 {
        margin: 0;
        color: #fff;
        font-size: 16px;
        font-weight: 700;
    }

    .chatbot-header-text p {
        margin: 2px 0 0;
        color: #6fd3ff;
        font-size: 12px;
    }

    .chatbot-close-btn {
        background: transparent;
        border: none;
        color: #999;
        font-size: 18px;
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s;
    }

    .chatbot-close-btn:hover {
        color: #fff;
    }

    /* Messages Container */
    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scroll-behavior: smooth;
    }

    .chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chatbot-messages::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 3px;
    }

    .chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    /* Message */
    .chatbot-message {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chatbot-message.user-message {
        flex-direction: row-reverse;
    }

    .chatbot-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .bot-avatar {
        background: linear-gradient(135deg, #e53935, #f44336);
        color: white;
    }

    .user-avatar {
        background: #3b82f6;
        color: white;
    }

    .message-content {
        max-width: 260px;
        padding: 12px 14px;
        border-radius: 12px;
        line-height: 1.5;
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    .bot-message .message-content {
        background: rgba(100, 116, 139, 0.2);
        color: #dbeaf7;
        border: 1px solid rgba(100, 116, 139, 0.3);
    }

    .user-message .message-content {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    /* Input Area */
    .chatbot-input-area {
        padding: 12px;
        background: rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        gap: 8px;
    }

    .chatbot-input {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.06);
        color: #fff;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
    }

    .chatbot-input::placeholder {
        color: #666;
    }

    .chatbot-input:focus {
        border-color: #e53935;
        background: rgba(255, 255, 255, 0.09);
    }

    .chatbot-send-btn {
        padding: 10px 12px;
        background: linear-gradient(135deg, #e53935, #f44336);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .chatbot-send-btn:hover {
        background: linear-gradient(135deg, #d32f2f, #e64a4a);
    }

    .chatbot-send-btn:active {
        transform: scale(0.95);
    }

    .chatbot-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .chatbot-popup {
            width: 100%;
            height: 100%;
            bottom: 0;
            right: 0;
            border-radius: 0;
            max-width: 100vw;
            max-height: 100vh;
        }

        .chatbot-toggle-btn {
            bottom: 16px;
            right: 16px;
        }

        .message-content {
            max-width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const popup = document.getElementById('chatbot-popup');
        const toggleBtn = document.getElementById('chatbot-toggle-btn');
        const closeBtn = document.getElementById('chatbot-close-btn');
        const messagesContainer = document.getElementById('chatbot-messages');
        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send-btn');

        // Load chat history from sessionStorage on page load
        loadChatHistory();

        // Toggle popup
        toggleBtn.addEventListener('click', () => {
            popup.classList.toggle('hidden');
            if (!popup.classList.contains('hidden')) {
                input.focus();
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });

        // Close popup
        closeBtn.addEventListener('click', () => {
            popup.classList.add('hidden');
        });

        // Send message on button click
        sendBtn.addEventListener('click', sendMessage);

        // Send message on Enter key
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Send message function
        function sendMessage() {
            const message = input.value.trim();
            if (!message) return;

            // Disable button while sending
            sendBtn.disabled = true;

            // Add user message to UI
            addMessageToUI('user', message);
            saveChatHistory('user', message);

            // Clear input
            input.value = '';

            // Send to server with better error handling
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const url = '{{ route("chatbot.message") }}';

            console.log('Sending message:', message);
            console.log('URL:', url);
            console.log('CSRF Token present:', !!csrfToken);

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message })
            })
            .then(response => {
                console.log('Response received:',  response.status, response.statusText);

                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response body:', text);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('Bot response:', data);
                if (data.reply) {
                    addMessageToUI('bot', data.reply);
                    saveChatHistory('bot', data.reply);
                } else if (data.error) {
                    addMessageToUI('bot', data.error);
                } else {
                    addMessageToUI('bot', 'Không có phản hồi từ server.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                addMessageToUI('bot', 'Lỗi: ' + (error.message || 'Không thể kết nối'));
            })
            .finally(() => {
                sendBtn.disabled = false;
                input.focus();
            });
        }

        // Add message to UI
        function addMessageToUI(role, content) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${role}-message`;

            const avatarDiv = document.createElement('div');
            avatarDiv.className = `chatbot-avatar ${role === 'bot' ? 'bot-avatar' : 'user-avatar'}`;
            avatarDiv.innerHTML = role === 'bot'
                ? '<i class="bi bi-robot"></i>'
                : '<i class="bi bi-person-fill"></i>';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.textContent = content;

            messageDiv.appendChild(avatarDiv);
            messageDiv.appendChild(contentDiv);
            messagesContainer.appendChild(messageDiv);

            // Auto scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Save chat history to sessionStorage
        function saveChatHistory(role, content) {
            let history = JSON.parse(sessionStorage.getItem('chatbot_history') || '[]');
            history.push({ role, content, timestamp: new Date().toLocaleTimeString() });
            sessionStorage.setItem('chatbot_history', JSON.stringify(history));
        }

        // Load chat history from sessionStorage
        function loadChatHistory() {
            const history = JSON.parse(sessionStorage.getItem('chatbot_history') || '[]');
            if (history.length > 0) {
                // Clear default message if there's history
                messagesContainer.innerHTML = '';
                history.forEach(msg => {
                    addMessageToUI(msg.role, msg.content);
                });
            }
        }

        // Optional: Clear chat history
        window.clearChatHistory = function() {
            sessionStorage.removeItem('chatbot_history');
            messagesContainer.innerHTML = `
                <div class="chatbot-message bot-message">
                    <div class="chatbot-avatar bot-avatar">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div class="message-content">
                        <p>Xin chào! 👋 Tôi là trợ lý ảo của GoCinema. Tôi có thể giúp bạn về đặt vé, ưu đãi, tìm phim, hoặc bất kỳ câu hỏi nào. Hãy nhắn cho tôi! 😊</p>
                    </div>
                </div>
            `;
        };
    });
</script>
