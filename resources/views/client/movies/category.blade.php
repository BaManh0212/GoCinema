@extends('client.layouts.app')

@section('title', 'Phim - ' . $danhMuc->ten)

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-danger">🎬 Phim thuộc danh mục: {{ $danhMuc->ten }}</h2>

    {{-- Bộ lọc: thể loại, rạp, ngày chiếu (màu nhẹ nhàng) --}}
    <style>
        /* Theme variables for movie pages (dark theme friendly) */
        :root {
            --mc-card-bg: rgba(255,255,255,0.03);
            --mc-input-bg: rgba(255,255,255,0.04);
            --mc-input-border: rgba(255,255,255,0.06);
            --mc-text: #f8fafc; /* near-white */
            --mc-text-muted: rgba(248,250,252,0.72);
            --mc-accent: #ef4444; /* warm red */
            --mc-accent-hover: #dc2626;
        }

        /* Filter UI */
        .filter-row .filter-card {
            padding: 10px; border-radius: 8px; background: var(--mc-card-bg); border: 1px solid var(--mc-input-border);
        }
        .filter-row .form-select, .filter-row .form-control {
            background: var(--mc-input-bg); color: var(--mc-text); border: 1px solid var(--mc-input-border); padding: .5rem .75rem; border-radius:6px;
        }
        .filter-row .form-select:focus, .filter-row .form-control:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.06); outline: none; }
        .filter-row .form-select option { color: #111; }

        .btn-filter-soft { background: var(--mc-accent); color: #fff; border: none; border-radius: 8px; padding: .5rem .8rem; }
        .btn-filter-soft:hover { background: var(--mc-accent-hover); }

        /* Movie cards */
        .movie-poster { border-radius:6px; }
    .movie-card { border-radius: 8px; transition: transform 0.18s ease-in-out; overflow: hidden; background: transparent !important; }
        .movie-card:hover { transform: translateY(-4px); }
    .movie-card .card-body { background: transparent; }
    .movie-card .card-title { color: var(--mc-text) !important; font-weight:700 !important; }
    .movie-link { color: var(--mc-text) !important; }
    .movie-card { box-shadow: 0 6px 18px rgba(2,6,23,0.4); }

        /* Overlay */
        .movie-card .overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: linear-gradient(180deg, rgba(0,0,0,0.0), rgba(0,0,0,0.45)); opacity:0; transition: opacity .18s; }
        .movie-card:hover .overlay { opacity:1; }
        .overlay span { background: rgba(0,0,0,0.56); padding:6px 12px; border-radius:18px; color:#fff; }

        /* Pagination */
        .pagination .page-link { background: transparent; color: var(--mc-text); border: 1px solid rgba(255,255,255,0.04); }
        .pagination .active .page-link { background: var(--mc-accent); border-color: var(--mc-accent); }
    </style>

    <form method="GET" action="{{ route('movies.category', $danhMuc->slug) }}" class="row g-3 mb-4 filter-row align-items-end">
        <div class="col-12 col-md-4">
            <div class="filter-card">
                <label class="visually-hidden" for="the_loai">Thể loại</label>
                <select id="the_loai" name="the_loai" class="form-select">
                    <option value="">-- Tất cả thể loại --</option>
                    @isset($theLoais)
                        @foreach($theLoais as $tl)
                            <option value="{{ $tl->id }}" @if(request('the_loai') == $tl->id) selected @endif>{{ $tl->ten }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="filter-card">
                <label class="visually-hidden" for="rap">Rạp</label>
                <select id="rap" name="rap" class="form-select">
                    <option value="">-- Tất cả rạp --</option>
                    @isset($raps)
                        @foreach($raps as $r)
                            <option value="{{ $r->id }}" @if(request('rap') == $r->id) selected @endif>{{ $r->ten }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </div>
        <div class="col-8 col-md-3">
            <div class="filter-card">
                <label class="visually-hidden" for="date">Ngày</label>
                <input type="date" id="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
        </div>
        <div class="col-4 col-md-1 d-grid">
            <button class="btn btn-filter-soft" type="submit">Lọc</button>
        </div>
    </form>

    <div class="row g-4">
        @forelse ($movies as $movie)
            <div class="col-6 col-md-3">
                <a href="{{ route('movies.show', $movie->slug) }}" class="text-decoration-none movie-link">
                    <div class="card h-100 shadow-sm border-0 movie-card position-relative overflow-hidden">
                        @if($movie->anh_poster)
                            <img src="{{ asset('storage/' . $movie->anh_poster) }}" class="card-img-top movie-poster" alt="{{ $movie->tieu_de }}">
                        @else
                            <div class="card-img-top bg-light" style="height:280px;border-radius:6px;"></div>
                        @endif
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate">{{ $movie->tieu_de }}</h6>
                        </div>

                        <div class="overlay d-flex justify-content-center align-items-center">
                            <span class="text-white fw-bold">Xem chi tiết</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-muted">Không có phim nào trong danh mục này.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $movies->links() }}
    </div>
</div>

@endsection
