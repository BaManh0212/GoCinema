@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-secondary mb-3">← Quay lại</a>

    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ $contact->name }} <small class="text-muted">({{ $contact->email }})</small></h5>
            <p>{{ $contact->message }}</p>
            <p><small>Gửi lúc: {{ $contact->created_at->format('Y-m-d H:i') }}</small></p>
        </div>
    </div>

    <div class="mb-4">
        <h6>Phản hồi</h6>
        @forelse($contact->replies as $r)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="small text-muted">Admin: {{ $r->admin?->name ?? 'Admin' }} • {{ $r->created_at->format('Y-m-d H:i') }}</div>
                    <p class="mb-0">{!! nl2br(e($r->reply_message)) !!}</p>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Chưa có phản hồi.</div>
        @endforelse
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.contacts.reply', $contact->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Trả lời</label>
                    <textarea name="reply_message" class="form-control" rows="5" required>{{ old('reply_message') }}</textarea>
                </div>
                <button class="btn btn-primary">Gửi phản hồi</button>
            </form>
        </div>
    </div>
</div>
@endsection
