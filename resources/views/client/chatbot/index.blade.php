@extends('client.layouts.app')

@section('title', 'Chatbot')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-light">Chatbot AI</h2>
    <div id="chatbox" class="border rounded p-3 mb-3" style="height: 400px; overflow-y: auto; background: #111;">
        <!-- tin nhắn sẽ hiển thị ở đây -->
    </div>
    <form id="chat-form" class="d-flex">
        <input type="text" id="message" class="form-control me-2" placeholder="Nhập tin nhắn..." required>
        <button type="submit" class="btn btn-primary">Gửi</button>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('message');
    const message = input.value.trim();
    if (!message) return;

    const chatbox = document.getElementById('chatbox');
    chatbox.innerHTML += `<div class="text-light mb-2"><strong>Bạn:</strong> ${message}</div>`;
    chatbox.scrollTop = chatbox.scrollHeight;

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
        chatbox.innerHTML += `<div class="text-light mb-2"><strong>Bot:</strong> ${data.reply}</div>`;
        chatbox.scrollTop = chatbox.scrollHeight;
    } catch (error) {
        console.error(error);
        chatbox.innerHTML += `<div class="text-danger mb-2"><strong>Bot:</strong> Có lỗi xảy ra</div>`;
    }
});
</script>
<style>
    #chatbox {
    background: #111;
    border: 1px solid #444;
    border-radius: 8px;
    padding: 10px;
    height: 400px;
    overflow-y: auto;
}

#chatbox div {
    padding: 5px 10px;
    border-radius: 5px;
}

#chatbox div strong {
    color: #0d6efd;
}
</style>
@endpush
@endsection
