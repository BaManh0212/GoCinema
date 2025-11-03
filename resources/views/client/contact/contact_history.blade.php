@extends('client.layouts.app')

@section('title','Lịch sử liên hệ')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endpush

@section('content')
<div class="container py-5">
    <h2>Lịch sử liên hệ</h2>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    @forelse($contacts as $c)
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>{{ $c->subject ?? 'Không có tiêu đề' }}</strong>
                    <div class="small text-muted">{{ $c->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div>
                    @if($c->status == 'pending') <span class="badge bg-danger">Chưa đọc</span>
                    @elseif($c->status == 'read') <span class="badge bg-warning text-dark">Đã đọc</span>
                    @else <span class="badge bg-success">Đã trả lời</span>
                    @endif
                </div>
            </div>

            <p class="mt-2">{{ $c->message }}</p>

            @if($c->replies->count())
                <hr>
                <h6>Phản hồi</h6>
                @foreach($c->replies as $r)
                    <div class="p-3 mb-2 border rounded">
                        <div class="small text-muted">Phản hồi lúc {{ $r->created_at->format('Y-m-d H:i') }}</div>
                        <div>{!! nl2br(e($r->reply_message)) !!}</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    @empty
        <div class="alert alert-info">Bạn chưa gửi liên hệ nào.</div>
    @endforelse

    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
