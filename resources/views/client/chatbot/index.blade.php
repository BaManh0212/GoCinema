@extends('client.layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">💬 Chatbot GoCinema AI</h2>
    <div id="chatbox" class="border rounded p-3 bg-light" style="height: 500px; overflow-y: auto;">
        <div id="messages"></div>
    </div>

    <div class="input-group mt-3">
        <input type="text" id="user-input" class="form-control" placeholder="Nhập tin nhắn...">
        <button id="send-btn" class="btn btn-primary">Gửi</button>
    </div>
</div>

<style>
.chat-message {
    margin: 5px 0;
    padding: 8px 12px;
    border-radius: 12px;
    max-width: 75%;
}
.user-message {
    background-color: #007bff;
    color: #fff;
    align-self: flex-end;
    text-align: right;
}
.bot-message {
    background-color: #f1f1f1;
    color: #222;
    align-self: flex-start;
    text-align: left;
}
#messages {
    display: flex;
    flex-direction: column;
}

/* Hiệu ứng loading dots */
.loading-dots {
    display: inline-block;
    line-height: 1;
}
.loading-dots span {
    display: inline-block;
    width: 8px;
    height: 8px;
    margin: 0 3px;
    background-color: #007bff; /* xanh nổi bật */
    border-radius: 50%;
    animation: bounce 1.2s infinite;
}
.loading-dots span:nth-child(2) { animation-delay: 0.2s; }
.loading-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const chatbotMessages = document.getElementById("messages");
    const chatbotText = document.getElementById("user-input");
    const sendBtn = document.getElementById("send-btn");
    let isSending = false;

    function appendMessage(sender, message) {
        const msgDiv = document.createElement("div");
        msgDiv.classList.add("chat-message");
        msgDiv.classList.add(sender === "user" ? "user-message" : "bot-message");
        msgDiv.textContent = message;
        chatbotMessages.appendChild(msgDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        return msgDiv;
    }

    function appendLoading() {
        console.log("Loading dots appended"); // test log
        const loadingDiv = document.createElement("div");
        loadingDiv.classList.add("chat-message", "bot-message");
        loadingDiv.innerHTML = '<div class="loading-dots"><span></span><span></span><span></span></div>';
        chatbotMessages.appendChild(loadingDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        return loadingDiv;
    }

    async function sendMessage() {
        if (isSending) return;
        const message = chatbotText.value.trim();
        if (!message) return;

        isSending = true;
        sendBtn.disabled = true;

        appendMessage("user", message);
        chatbotText.value = "";

        const loadingDiv = appendLoading();

        const csrfToken = '{{ csrf_token() }}';
        const url = '{{ route("chatbot.message") }}';

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ message }),
            });

            const data = await response.json();

            chatbotMessages.removeChild(loadingDiv);

            if (data && data.reply) {
                appendMessage("bot", data.reply);
            } else {
                appendMessage("bot", "Xin lỗi, tôi không hiểu câu hỏi của bạn.");
            }
        } catch (error) {
            chatbotMessages.removeChild(loadingDiv);
            appendMessage("bot", "Lỗi hệ thống, vui lòng thử lại sau.");
        } finally {
            isSending = false;
            sendBtn.disabled = false;
        }
    }

    let enterTimeout;
    chatbotText.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            clearTimeout(enterTimeout);
            enterTimeout = setTimeout(() => sendMessage(), 120);
        }
    });

    sendBtn.addEventListener("click", sendMessage);
});
</script>
@endsection
