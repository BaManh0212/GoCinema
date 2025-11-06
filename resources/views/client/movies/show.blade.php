@extends('client.layouts.app')

@section('title', $phim->tieu_de)

@section('content')
<div class="container py-5">
    <style>
        :root {
            --mc-text: #f8fafc;
            --mc-text-muted: rgba(248,250,252,0.85);
            --mc-accent: #ef4444;
        }
        .movie-detail { color: var(--mc-text); }
        .movie-detail h1 { color: var(--mc-text); }
        .movie-detail .meta { color: var(--mc-text-muted); font-weight:500; }
        .movie-detail .movie-description, .movie-detail .movie-description p { color: var(--mc-text); }
        .movie-poster-lg { border-radius:8px; box-shadow: 0 8px 26px rgba(2,6,23,0.6); }
        .trailer-frame iframe { border: none; }
    </style>

    <div class="row movie-detail">
        <div class="col-md-4 mb-4">
            @if($phim->anh_poster)
                <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="img-fluid movie-poster-lg" alt="{{ $phim->tieu_de }}">
            @endif
        </div>
        <div class="col-md-8">
            <h1 class="fw-bold">{{ $phim->tieu_de }}</h1>
            <p class="meta">@if($phim->danhMuc) <strong>Danh mục:</strong> {{ $phim->danhMuc->ten }} @endif</p>
            <p class="meta">@if($phim->theLoais->count()) <strong>Thể loại:</strong> {{ $phim->theLoais->pluck('ten')->join(', ') }} @endif</p>
            <p class="meta"><strong>Thời lượng:</strong> {{ $phim->thoi_luong ?? '—' }} phút</p>
            <p class="meta"><strong>Ngày công chiếu:</strong> {{ optional($phim->ngay_cong_chieu)->format('d/m/Y') ?? '—' }}</p>
            <hr>
            <div>
                {!! $phim->mo_ta !!}
            </div>

            @if($phim->trailer)
                <div class="mt-4">
                    <h5>Trailer</h5>
                    <div class="ratio ratio-16x9 trailer-frame">
                        <iframe src="{{ $phim->trailer }}" allowfullscreen></iframe>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
