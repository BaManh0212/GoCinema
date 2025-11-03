@extends('client.layouts.app')

@section('title', 'Trang chủ - GoCinema')

@section('content')

    {{-- ✅ BANNER SLIDER FULL WIDTH --}}
    @if($banners->count())
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($banners as $i => $banner)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">

                    {{-- IMAGE --}}
                    @if($banner->type === 'image')
                        @if($banner->link)
                            <a href="{{ $banner->link }}">
                                <img src="{{ asset('storage/'.$banner->image) }}" class="d-block w-100 banner-media" alt="{{ $banner->title }}">
                            </a>
                        @else
                            <img src="{{ asset('storage/'.$banner->image) }}" class="d-block w-100 banner-media" alt="{{ $banner->title }}">
                        @endif
                    @endif

                    {{-- VIDEO --}}
                    @if($banner->type === 'video')
                        <video class="w-100 banner-media" autoplay muted loop playsinline>
                            <source src="{{ asset('storage/'.$banner->video_url) }}" type="video/mp4">
                        </video>
                    @endif

                    @if($banner->title)
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded px-3 py-2">
                        <h5 class="m-0">{{ $banner->title }}</h5>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    @endif


    {{-- ✅ MAIN CONTENT --}}
    <div class="container py-5">

        <div class="text-center mb-5">
            <h2 class="fw-bold">🎬 Phim nổi bật</h2>
            <p class="text-muted">Xem lịch chiếu và đặt vé ngay hôm nay</p>
        </div>

        <div class="row gx-3 gy-4">
            @forelse($featured as $phim)
            <div class="col-6 col-md-3">
                <div class="card h-100 shadow-sm movie-card">
                    <div class="poster-wrapper">
                        <img src="{{ asset('storage/' . $phim->anh_poster) }}" 
                             class="card-img-top" 
                             alt="{{ $phim->tieu_de }}" 
                             loading="lazy">
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-1 text-truncate">{{ $phim->tieu_de }}</h6>
                        <p class="small text-muted mb-0">{{ $phim->ngayCongChieuFormatted ?? '' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">Chưa có phim nổi bật.</div>
            </div>
            @endforelse
        </div>

    </div>

@endsection
