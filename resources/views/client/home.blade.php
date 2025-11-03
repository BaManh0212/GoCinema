@extends('client.layouts.app')

@section('title', 'Trang chủ - GoCinema')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5">GoCinema</h2>
            <p class="text-muted">Rạp phim gần bạn — Xem lịch chiếu và đặt vé nhanh chóng</p>
        </div>

        <section id="phim" class="mb-4">
            <h3 class="h4 mb-3">Phim nổi bật</h3>
            <div class="row gx-3 gy-4">
                {{-- Placeholder: controllers should pass `featured` collection when using this view --}}
                @if(isset($featured) && $featured->count())
                    @foreach($featured as $phim)
                        <div class="col-6 col-md-3">
                            <div class="card h-100 shadow-sm">
                                @if($phim->anh_poster)
                                    <div style="height:220px;overflow:hidden;position:relative;">
                                        <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-cover" alt="{{ $phim->tieu_de }}" onerror="(function(img){img.style.display='none';var p=document.createElement('div');p.className='movie-placeholder';p.innerHTML='<i class=\'fa fa-film fa-2x\'></i>';img.parentNode.appendChild(p);})(this)">
                                    </div>
                                @else
                                    <div class="movie-placeholder"><i class="fa fa-film fa-2x"></i></div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ $phim->tieu_de }}</h6>
                                    <p class="small text-muted mb-0">{{ $phim->getNgayCongChieuFormattedAttribute() ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">Chưa có phim nổi bật để hiển thị.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
