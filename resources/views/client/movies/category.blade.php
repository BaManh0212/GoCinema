@extends('client.layouts.app')

@section('title', 'Phim - ' . $danhMuc->ten)

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4 text-danger">🎬 Phim thuộc danh mục: {{ $danhMuc->ten }}</h2>

    <div class="row g-4">
        @forelse ($movies as $movie)
            <div class="col-6 col-md-3">
                <a href="#" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm border-0 movie-card">
                    @if($movie->anh_poster)
                        <img src="{{ asset('storage/' . $movie->anh_poster) }}" class="card-img-top" alt="{{ $movie->tieu_de }}">
                    @else
                        <div class="card-img-top bg-light" style="height:280px;border-radius:6px;"></div>
                    @endif
                    <div class="card-body text-center">
                        <h6 class="card-title text-truncate">{{ $movie->tieu_de }}</h6>
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
