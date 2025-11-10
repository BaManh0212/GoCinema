@extends('client.layouts.app')

@php
    // Helper: Parse YouTube URL -> embed
    function youtubeEmbed($url)
    {
        if (!$url) return null;
        if (preg_match('~youtu\.be/([^?&/]+)~', $url, $m)) {
            $id = $m[1];
        } elseif (preg_match('~v=([^&]+)~', $url, $m)) {
            $id = $m[1];
        } else {
            $id = null;
        }
        return $id ? "https://www.youtube.com/embed/$id" : null;
    }

    $yt = youtubeEmbed($phim->trailer);
    $theLoaiText = $phim->theLoais?->pluck('ten')->join(', ');
    $danhMucText = $phim->danhMucs?->pluck('ten')->join(', ');
@endphp

@section('title', $phim->tieu_de)

@section('content')
<div class="movie-detail">

    {{-- HERO / TRAILER + INFO --}}
<section class="movie-hero position-relative">

    {{-- Ảnh nền --}}
    <div class="hero-bg">
        <img src="{{ $phim->anh_poster ? asset('storage/' . $phim->anh_poster) : asset('images/no-poster.jpg') }}" 
             alt="{{ $phim->tieu_de }}" class="w-100 h-100 object-fit-cover">
    </div>
    <div class="hero-overlay"></div>

    <div class="container position-relative py-5">

        {{-- Khung 1: Tiêu đề --}}
        <div class="title-frame text-center mb-5">
            <h1 class="text-light fw-bold display-4 text-shadow">{{ $phim->tieu_de }}</h1>
        </div>

        {{-- Khung 2: Trailer + Thông tin --}}
        <div class="row g-4 mb-5 align-items-start">
            <div class="col-lg-8">
                <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg trailer-wrap">
                    @if($yt)
                        <iframe src="{{ $yt }}" title="Trailer {{ $phim->tieu_de }}" allowfullscreen></iframe>
                    @else
                        <div class="d-flex align-items-center justify-content-center text-secondary h-100">
                            <div class="text-center p-4">
                                <i class="bi bi-youtube display-4 d-block mb-2"></i>
                                <div>Chưa có trailer</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card glass border-0 rounded-4 shadow-lg h-100 sticky-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <div class="small text-body-secondary">Đánh giá trung bình</div>
                            <div class="d-flex align-items-end gap-2">
                                <div class="display-5 fw-bold text-warning">{{ number_format($diemTB, 1) }}</div>
                                <div class="text-body-secondary">/5</div>
                            </div>
                            <div class="small text-body-secondary">{{ $soDanhGia }} lượt</div>
                        </div>
                        <div class="ms-2 text-end d-none d-md-block">
                            <a href="#lichchieu" class="btn btn-danger rounded-3 px-3 py-2 btn-book">
                                <i class="bi bi-ticket-perforated-fill me-1"></i>Đặt vé
                            </a>
                        </div>
                    </div>

                    <hr class="border-secondary-subtle">

                    <ul class="list-unstyled m-0 small info-list">
                        <li class="mb-2"><i class="bi bi-person-video2 me-2 text-primary"></i><strong>Đạo diễn:</strong> {{ $phim->dao_dien ?: '—' }}</li>
                        <li class="mb-2"><i class="bi bi-people me-2 text-primary"></i><strong>Diễn viên:</strong> {{ $phim->dien_vien ?: '—' }}</li>
                        <li class="mb-2"><i class="bi bi-shield-check me-2 text-primary"></i><strong>Độ tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?: '—' }}</li>
                        <li class="mb-2"><i class="bi bi-translate me-2 text-primary"></i><strong>Ngôn ngữ:</strong> {{ $phim->ngonNgu?->ten ?? '—' }}</li>
                        <li class="mb-2"><i class="bi bi-aspect-ratio me-2 text-primary"></i><strong>Định dạng:</strong> {{ $phim->dinh_dang ?: '2D' }}</li>
                        <li class="mb-2"><i class="bi bi-eye me-2 text-primary"></i><strong>Lượt xem:</strong> {{ number_format($phim->luot_xem ?? 0) }}</li>
                    </ul>

                    <hr class="border-secondary-subtle">

                    <div class="small text-body-secondary mb-2">Thể loại & danh mục</div>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($theLoaiText)<span class="badge rounded-pill text-bg-info">{{ $theLoaiText }}</span>@endif
                        @if($danhMucText)<span class="badge rounded-pill text-bg-danger">{{ $danhMucText }}</span>@endif
                    </div>

                    <div class="mt-3 small text-body-secondary">
                        <div>Thời lượng: <strong>{{ $phim->thoi_luong ?? '—' }} phút</strong></div>
                        <div>Khởi chiếu: <strong>{{ $phim->ngay_cong_chieu ? \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') : '—' }}</strong></div>
                        @if($phim->ngay_ket_thuc)
                            <div>Kết thúc: <strong>{{ \Carbon\Carbon::parse($phim->ngay_ket_thuc)->format('d/m/Y') }}</strong></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Khung 3: Nội dung --}}
        <div class="content-frame glass rounded-4 shadow-lg p-4 position-relative" style="margin-top:-120px; z-index:2;">
            <h2 class="h4 fw-bold text-light mb-3">Nội dung phim</h2>
            <p class="text-white" style="line-height:1.85">{{ $phim->mo_ta }}</p>
        </div>
    </div>
</section>

    {{-- LỊCH CHIẾU --}}
    <div class="container my-5">
        <div id="lichchieu" class="card bg-dark-subtle border-0 rounded-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-semibold mb-0">Lịch chiếu (7 ngày tới)</h5>
                    <a href="{{ route('movies.schedule.json', $phim->slug) }}" class="small text-info" target="_blank">JSON</a>
                </div>

                @if($lichChieuTheoNgay->isEmpty())
                    <div class="p-4 border rounded-3 text-body-secondary bg-white">Hiện chưa có lịch chiếu.</div>
                @else
                    @php $dates = $lichChieuTheoNgay->keys()->values(); @endphp

                    <ul class="nav nav-pills gap-2 flex-nowrap overflow-auto pb-2" id="dayTabs" role="tablist" style="scrollbar-width:thin;">
                        @foreach($dates as $i => $date)
                            @php $label = \Carbon\Carbon::parse($date)->isoFormat('ddd • DD/MM'); @endphp
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $i===0? 'active':'' }} rounded-pill" id="tab-{{ $i }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $i }}" type="button" role="tab">{{ $label }}</button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mt-3">
                        @foreach($dates as $i => $date)
                            @php
                                $ds = $lichChieuTheoNgay[$date];
                                $byRap = $ds->groupBy(fn($s) => $s->phong->rap->ten ?? 'Khác');
                            @endphp
                            <div class="tab-pane fade {{ $i===0? 'show active':'' }}" id="pane-{{ $i }}" role="tabpanel">
                                @foreach($byRap as $rapTen => $items)
                                    <div class="mb-3">
                                        <div class="fw-semibold mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $rapTen }}</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($items as $s)
                                                @php
                                                    $gio = \Carbon\Carbon::parse($s->gio_bat_dau)->format('H:i');
                                                    $phong = $s->phong->ten ?? null;
                                                @endphp
                                                <a href="{{ url('/booking?suat_chieu_id=' . $s->id) }}" class="btn time-pill btn-sm rounded-pill d-inline-flex align-items-center gap-2">
                                                    <span class="fw-semibold">{{ $gio }}</span>
                                                    @if($phong)
                                                        <span class="text-body-secondary">{{ $phong }}</span>
                                                    @endif
                                                    <span class="badge text-bg-warning text-white">{{ number_format($s->gia_ve,0,',','.') }}đ</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @if(!$loop->last)<hr class="border-secondary-subtle">@endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Bình luận & đánh giá --}}
        <div class="row g-4 mt-4">
            <div class="col-lg-6">
                <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Bình luận & đánh giá</h5>
                        @if(session('success')) <div class="alert alert-success py-2">{{ session('success') }}</div> @endif
                        @if(session('error')) <div class="alert alert-danger py-2">{{ session('error') }}</div> @endif

                        @auth
                        <form id="ratingForm" action="{{ route('phim.danh_gia.luu', $phim->slug) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label d-block mb-2">Chọn số sao</label>
                                <div class="star-rating" role="radiogroup" aria-label="Số sao">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <input type="radio" name="so_sao" id="star{{ $i }}" value="{{ $i }}" class="d-none visually-hidden">
                                        <label for="star{{ $i }}" class="star" data-value="{{ $i }}">
                                            <i class="bi bi-star" aria-hidden="true"></i>
                                        </label>
                                    @endfor
                                </div>
                                <div class="invalid-feedback">Vui lòng chọn số sao.</div>
                            </div>

                            @auth
                                @if (!empty($eligible) && $eligible)
                                    <form action="{{ route('phim.danh_gia.luu', $phim->slug) }}" method="POST"
                                        class="needs-validation" novalidate>
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Số sao</label>
                                            <select name="so_sao" class="form-select" required>
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <option value="{{ $i }}">{{ $i }} sao</option>
                                                @endfor
                                            </select>
                                            <div class="invalid-feedback">Vui lòng chọn số sao.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Bình luận</label>
                                            <textarea name="binh_luan" rows="3" class="form-control" placeholder="Cảm nhận của bạn..."></textarea>
                                        </div>
                                        <button class="btn btn-danger"><i class="bi bi-send me-1"></i>Gửi đánh giá</button>
                                    </form>
                                @else
                                    <div class="alert alert-secondary mb-0">
                                        Bạn chỉ có thể gửi đánh giá sau khi đã <strong>mua vé và check-in thành công</strong>
                                        cho phim này.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-secondary mb-0">
                                    Vui lòng <a class="link-info" href="{{ url('/dang-nhap') }}">đăng nhập</a> để gửi đánh
                                    giá.
                                </div>
                            @endauth

                        </div>
                            <div class="mb-3">
                                <label class="form-label">Bình luận</label>
                                <textarea name="binh_luan" rows="3" class="form-control" placeholder="Cảm nhận của bạn..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger" id="ratingSubmit">Gửi đánh giá</button>
                        </form>
                        @else
                            <div class="alert alert-secondary mb-0">Vui lòng <a class="link-info" href="{{ url('/dang-nhap') }}">đăng nhập</a> để gửi đánh giá.</div>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Đánh giá gần đây</h5>
                        @php $danhgias = $phim->danhGias()->with('nguoiDung')->latest()->take(20)->get(); @endphp
                        @forelse($danhgias as $dg)
                            @php
                                $name = $dg->nguoiDung->name ?? 'Người dùng';
                                $initial = mb_strtoupper(mb_substr($name, 0, 1));
                            @endphp
                            <div class="border rounded-3 p-3 mb-3 bg-dark review-item">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar" aria-hidden="true">{{ $initial }}</div>
                                        <div class="fw-semibold">{{ $name }}</div>
                                    </div>
                                    <div class="small text-body-secondary">{{ $dg->created_at?->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="text-warning small mb-1">
                                    {!! str_repeat('<i class="bi bi-star-fill"></i>', (int) $dg->so_sao) !!}
                                    {!! str_repeat('<i class="bi bi-star"></i>', 5 - (int) $dg->so_sao) !!}
                                </div>
                                <div class="text-light">{{ $dg->binh_luan }}</div>
                            </div>
                        @empty
                            <div class="alert alert-secondary mb-0">Chưa có đánh giá nào.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PHIM LIÊN QUAN --}}
    @if(!empty($relatedMovies) && $relatedMovies->count())
    <div class="container my-5 related-modern">
        <h4 class="fw-bold text-light mb-3">Phim liên quan</h4>
        <div class="row g-3">
            @foreach($relatedMovies as $rel)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="{{ route('movies.show', $rel->slug) }}" class="relx-card text-decoration-none d-block">
                    <div class="relx-img-wrap position-relative overflow-hidden rounded-3 shadow-sm">
                        <img src="{{ $rel->anh_poster ? asset('storage/' . $rel->anh_poster) : asset('images/no-poster.jpg') }}" 
                             alt="{{ $rel->tieu_de }}" class="w-100 object-fit-cover">
                        <div class="overlay position-absolute inset-0 d-flex align-items-center justify-content-center">
                            <i class="bi bi-play-circle-fill text-white fs-1"></i>
                        </div>
                    </div>
                    <div class="relx-title text-light mt-1 text-truncate">{{ $rel->tieu_de }}</div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('styles')
<style>
    :root { --bg-primary:#16213e; --card:rgba(255,255,255,0.03); --accent:#e53935; }
    body{background:var(--bg-primary);}
    .object-fit-cover{object-fit:cover;}
    .movie-hero{position:relative; min-height:460px;}
    .movie-hero .hero-bg{position:absolute; inset:0; filter: blur(2px) brightness(.45); transform: scale(1.04);}
    .movie-hero .hero-overlay{position:absolute; inset:0; background:linear-gradient(180deg, rgba(9,12,20,0.18) 10%, rgba(9,12,20,0.85) 100%);}
    .trailer-wrap iframe{border:0; background:#000;}
    .glass{background:var(--card);backdrop-filter:blur(6px);}
    .sticky-card{position:sticky; top:96px;}
    .time-pill{border-color:rgba(255,255,255,.08); background: rgba(255,255,255,.02); color:#fff;}
    .time-pill:hover{background:var(--accent);border-color:var(--accent);}
    .star-rating{display:flex;gap:6px; align-items:center;}
    .star-rating .star i{font-size:1.6rem; color:#7a8ba3; cursor:pointer; transition:.12s;}
    .star-rating input:checked + .star i,
    .star-rating .star.hover i,
    .star-rating .star:hover i{color:#ffc107;}
    .avatar{width:36px;height:36px;border-radius:50%;background:#243240;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;}
    .review-item{background:rgba(255,255,255,0.02);}
    /* PHIM LIÊN QUAN */
    .related-modern .relx-card img {transition: transform 0.3s ease;}
    .related-modern .relx-card:hover img {transform: scale(1.05);}
    .related-modern .relx-card .overlay {background: rgba(0,0,0,0.35);opacity: 0; transition: opacity 0.3s ease; pointer-events: none;}
    .related-modern .relx-card:hover .overlay {opacity: 1;}
    .relx-title {font-size: .875rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}
     .movie-hero {position:relative;}
    .hero-bg img{object-fit:cover;width:100%;height:100%;}
    .hero-overlay{position:absolute;inset:0;background:linear-gradient(180deg, rgba(0,0,0,0.2) 20%, rgba(0,0,0,0.9) 100%);}
    .sticky-card{position:sticky;top:100px;}
    .glass{background:rgba(255,255,255,0.03);backdrop-filter:blur(6px);}
    .content-frame{background:rgba(0,0,0,0.5);}
</style>
@endpush

@push('scripts')
<script>
    // Star rating
    document.querySelectorAll('.star-rating .star').forEach(star=>{
        const val=parseInt(star.dataset.value);
        star.addEventListener('click',()=>{document.getElementById('star'+val).checked=true; updateStars(val);});
        star.addEventListener('mouseover',()=>{hoverStars(val);});
        star.addEventListener('mouseout',()=>{updateStars(document.querySelector('.star-rating input:checked')?.value || 0);});
    });

    function updateStars(v){
        document.querySelectorAll('.star-rating .star').forEach(s=>{
            const val=parseInt(s.dataset.value);
            s.querySelector('i').classList.toggle('bi-star-fill', val <= v);
            s.querySelector('i').classList.toggle('bi-star', val > v);
        });
    }

    function hoverStars(v){
        document.querySelectorAll('.star-rating .star').forEach(s=>{
            const val=parseInt(s.dataset.value);
            s.querySelector('i').classList.toggle('bi-star-fill', val <= v);
            s.querySelector('i').classList.toggle('bi-star', val > v);
        });
    }
</script>
@endpush
@endsection
