@extends('client.layouts.app')

@section('title', 'Trang chủ - GoCinema')

@section('content')
    @push('scripts')
    <script>
        document.body.classList.add('has-banner');
    </script>
    @endpush

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
            <h2 class="section-title text-white">🎬 Phim nổi bật</h2>
            <p class="text-white" style="color: rgba(255,255,255,0.7) !important;">Xem lịch chiếu và đặt vé ngay hôm nay</p>
        </div>

        <div class="row gx-4 gy-4">
            @forelse($featured as $phim)
            <div class="col-6 col-lg-3">
                <div class="movie-card">
                    <div class="poster-wrapper">
                        <img src="{{ asset('storage/' . $phim->anh_poster) }}" 
                             alt="{{ $phim->tieu_de }}" 
                             loading="lazy">
                        <div class="movie-overlay">
                            <a href="#" class="btn btn-primary btn-sm">Chi tiết</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-2">{{ $phim->tieu_de }}</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="small text-muted mb-0">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $phim->ngayCongChieuFormatted ?? 'Sắp chiếu' }}
                            </p>
                            <span class="badge bg-primary">
                                <i class="fas fa-star me-1"></i>
                                {{ number_format($phim->rating ?? 0, 1) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-film fa-3x mb-3 text-muted"></i>
                    <p class="text-muted">Chưa có phim nổi bật.</p>
                </div>
            </div>
            @endforelse
        </div>

    </div>

@endsection
