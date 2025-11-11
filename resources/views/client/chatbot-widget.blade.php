<div id="chat-widget">
    <div id="chat-header">
        <span>Chatbot AI</span>
        <button id="chat-toggle">×</button>
    </div>
    <div id="chat-body">
        <div id="chatbox"></div>
        <form id="chat-form">
            <input type="text" id="message" placeholder="Nhập tin nhắn..." required>
            <button type="submit">Gửi</button>
        </form>
        <button id="chat-clear" class="btn btn-sm btn-danger w-100 mt-2">Xóa chat</button>
    </div>
</div>

@push('styles')
<style>
#chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 320px;
    max-height: 450px;
    background: #111;
    color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 9999;
    font-family: sans-serif;
}

#chat-header {
    background: #0d6efd;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

#chat-body {
    display: none;
    flex-direction: column;
    height: 400px;
}

#chatbox {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    background: #222;
}

#chatbox .message {
    display: flex;
    align-items: flex-start;
    margin-bottom: 10px;
}

#chatbox .message.user {
    justify-content: flex-end;
}

#chatbox .message .avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin: 0 5px;
    object-fit: cover;
}

#chatbox .message .text {
    max-width: 70%;
    padding: 8px 12px;
    border-radius: 10px;
}

#chatbox .message.user .text {
    background: #0d6efd;
    color: #fff;
}

#chatbox .message.bot .text {
    background: #444;
}

#chat-form {
    display: flex;
    border-top: 1px solid #333;
}

#chat-form input {
    flex: 1;
    border: none;
    padding: 10px;
    background: #111;
    color: #fff;
}

#chat-form button {
    background: #0d6efd;
    border: none;
    color: white;
    padding: 0 15px;
    cursor: pointer;
}

#chat-clear {
    background: #dc3545;
    border: none;
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
const chatWidget = document.getElementById('chat-widget');
const chatHeader = document.getElementById('chat-header');
const chatBody = document.getElementById('chat-body');
const chatbox = document.getElementById('chatbox');
const chatForm = document.getElementById('chat-form');
const chatToggle = document.getElementById('chat-toggle');
const chatClear = document.getElementById('chat-clear');

// Toggle chat
chatHeader.addEventListener('click', () => {
    chatBody.style.display = chatBody.style.display === 'block' ? 'none' : 'block';
});

chatToggle.addEventListener('click', () => chatBody.style.display = 'none');

// Render history từ session
const chatHistory = @json(session('chat_history', []));
chatHistory.forEach(m => renderMessage(m.role, m.content));

// Gửi tin nhắn
chatForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('message');
    const message = input.value.trim();
    if (!message) return;
    renderMessage('user', message);
    input.value = '';

    try {
        const res = await fetch("{{ route('chatbot.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ message }),
        });
        const data = await res.json();
        renderMessage('bot', data.reply);
    } catch (err) {
        renderMessage('bot', 'Có lỗi xảy ra');
    }
});

// Xóa chat
chatClear.addEventListener('click', async () => {
    await fetch("{{ route('chatbot.clear') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
    });
    chatbox.innerHTML = '';
});

// Hàm render message
function renderMessage(role, text) {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('message', role);

    const avatar = document.createElement('img');
    avatar.classList.add('avatar');
    avatar.src = role === 'user' ? '{{ asset("images/user-avatar.png") }}' : '{{ asset("images/bot-avatar.png") }}';

    const textDiv = document.createElement('div');
    textDiv.classList.add('text');
    textDiv.textContent = text;

    if(role === 'user'){
        msgDiv.appendChild(textDiv);
        msgDiv.appendChild(avatar);
    } else {
        msgDiv.appendChild(avatar);
        msgDiv.appendChild(textDiv);
    }

    chatbox.appendChild(msgDiv);
    chatbox.scrollTop = chatbox.scrollHeight;
}
</script>
@endpush
