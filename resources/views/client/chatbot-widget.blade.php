<div id="chat-widget">
    <div id="chat-header">
        <span>🎬 GoCinema Chatbot</span>
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
#chat-body { display: none; flex-direction: column; height: 400px; }
#chatbox { flex: 1; overflow-y: auto; padding: 10px; background: #222; }
#chatbox .message { margin-bottom: 10px; padding: 6px 10px; border-radius: 8px; }
#chatbox .message.user { background: #0d6efd; color: #fff; text-align: right; }
#chatbox .message.bot { background: #444; color: #fff; text-align: left; }
#chat-form { display: flex; margin-top: 5px; }
#chat-form input { flex: 1; padding: 8px; border: none; background: #111; color: #fff; }
#chat-form button { padding: 0 12px; border: none; background: #0d6efd; color: #fff; cursor: pointer; }
#chat-clear { margin-top: 5px; border: none; background: #dc3545; color: #fff; padding: 5px; cursor: pointer; }
</style>
@endpush

@push('scripts')
<script>
const chatWidget = document.getElementById('chat-widget');
const chatHeader = document.getElementById('chat-header');
const chatBody = document.getElementById('chat-body');
const chatbox = document.getElementById('chatbox');
const chatForm = document.getElementById('chat-form');
const chatClear = document.getElementById('chat-clear');

// Toggle chat window
chatHeader.addEventListener('click', () => {
    chatBody.style.display = chatBody.style.display === 'block' ? 'none' : 'block';
});

// Submit message
chatForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('message');
    const message = input.value.trim();
    if (!message) return;
    renderMessage('user', message);
    input.value = '';

    const csrfToken = '{{ csrf_token() }}';
    const url = '{{ route("chatbot.message") }}';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ message }),
        });

        const data = await res.json();

        if (res.ok && data.reply) {
            renderMessage('bot', data.reply);
        } else if (data.error) {
            renderMessage('bot', `⚠️ ${data.error}`);
        } else {
            renderMessage('bot', '⚠️ Không có phản hồi từ máy chủ.');
        }
    } catch (err) {
        renderMessage('bot', '⚠️ Lỗi kết nối máy chủ.');
    }
});

// Clear chat
chatClear.addEventListener('click', async () => {
    await fetch("{{ route('chatbot.clear') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    chatbox.innerHTML = '';
});

// Render message
function renderMessage(role, text) {
    const div = document.createElement('div');
    div.classList.add('message', role);
    div.textContent = text;
    chatbox.appendChild(div);
    chatbox.scrollTop = chatbox.scrollHeight;
}
</script>
@endpush
