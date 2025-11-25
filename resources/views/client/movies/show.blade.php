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
                    </div>

                    <hr class="border-secondary-subtle">

                    <ul class="list-unstyled m-0 small info-list">
                        <li class="mb-2"><i class="bi bi-person-video2 me-2 text-primary"></i><strong>Đạo diễn:</strong> {{ $phim->dao_dien ?: '—' }}</li>
                        <li class="mb-2"><i class="bi bi-people me-2 text-primary"></i><strong>Diễn viên:</strong> {{ $phim->dien_vien ?: '—' }}</li>
                        <li class="mb-2"><i class="bi bi-shield-check me-2 text-primary"></i><strong>Độ tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?: '—' }}</li>
                        <li class="mb-2"><i class="bi bi-translate me-2 text-primary"></i><strong>Ngôn ngữ:</strong> {{ $phim->ngonNgu?->ten ?? '—' }}</li>
                        <li class="mb-2"><i class="bi bi-aspect-ratio me-2 text-primary"></i><strong>Định dạng:</strong> {{ $phim->dinh_dang ?: '2D' }}</li>
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
                    {{-- <a href="{{ route('movies.schedule.json', $phim->slug) }}" class="small text-info" target="_blank">JSON</a> --}}
                </div>

                @if($lichChieuTheoNgay->isEmpty())
                    <div class="p-4 border rounded-3 text-body-secondary bg-white">Hiện chưa có lịch chiếu.</div>
                @else
                    @php
                        $dates = collect();
                        $start = \Carbon\Carbon::today();
                        for ($i=0; $i<7; $i++) { $dates->push($start->copy()->addDays($i)->format('Y-m-d')); }
                    @endphp

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
                                $ds = $lichChieuTheoNgay[$date] ?? collect();
                                $byRap = $ds->groupBy(fn($s) => $s->phong->rap->ten ?? 'Khác');
                            @endphp
                            <div class="tab-pane fade {{ $i===0? 'show active':'' }}" id="pane-{{ $i }}" role="tabpanel">
                                @if($ds->isEmpty())
                                    <div class="p-3 text-body-secondary bg-white rounded">Không có suất chiếu cho ngày này.</div>
                                @else
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
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Bình luận & đánh giá --}}
        {{-- Bình luận & đánh giá --}}
    <div class="row g-4 mt-4 align-items-stretch">
        {{-- Cột trái: Bình luận & đánh giá --}}
        <div class="col-lg-6 d-flex">
            <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm flex-fill d-flex flex-column">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Bình luận & đánh giá</h5>
                    @if(session('success')) <div class="alert alert-success py-2">{{ session('success') }}</div> @endif
                    @if(session('error')) <div class="alert alert-danger py-2">{{ session('error') }}</div> @endif

                    @auth
                        @if (!empty($eligible) && $eligible)
                            <form id="ratingForm" action="{{ route('phim.danh_gia.luu', $phim->slug) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label d-block mb-2">Chọn số sao</label>
                                    <div class="star-rating" role="radiogroup" aria-label="Số sao">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <input type="radio" name="so_sao" id="star{{ $i }}" value="{{ $i }}" class="d-none">
                                            <label for="star{{ $i }}" class="star" data-value="{{ $i }}">
                                                <i class="bi bi-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bình luận</label>
                                    <textarea name="binh_luan" rows="3" class="form-control" placeholder="Cảm nhận của bạn..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-send me-1"></i>Gửi đánh giá
                                </button>
                            </form>
                        @else
                            <div class="alert alert-secondary mb-0">
                                Bạn chỉ có thể gửi đánh giá sau khi đã <strong>mua vé và check-in thành công</strong>.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-secondary mb-0">
                            Vui lòng <a class="link-info" href="{{ url('/dang-nhap') }}">đăng nhập</a> để gửi đánh giá.
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Cột phải: Đánh giá gần đây --}}
        <div class="col-lg-6 d-flex">
            <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm flex-fill d-flex flex-column">
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
                                    <div class="avatar">{{ $initial }}</div>
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
:root {
    --primary-bg: #0b1220;
    --secondary-bg: #07101a;
    --card-bg: #111827;
    --text-light: #e6eef8;
    --text-muted: #9ca3af;
    --accent: #e53935;
    --card-border: rgba(255,255,255,0.04);
    --border-radius: 12px;
    --shadow: 0 4px 12px rgba(0,0,0,0.3);
    --transition: all 0.3s ease;
}

body {
    background: linear-gradient(180deg, var(--primary-bg) 0%, var(--secondary-bg) 100%);
    color: var(--text-light);
    font-family: 'Inter', 'Poppins', sans-serif;
}

.object-fit-cover { object-fit: cover; }
.movie-hero { position: relative; min-height: 460px; }
.movie-hero .hero-bg { position: absolute; inset: 0; filter: blur(2px) brightness(.45); transform: scale(1.04); }
.movie-hero .hero-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(9,12,20,0.18) 10%, rgba(9,12,20,0.85) 100%); }
.trailer-wrap iframe { border: 0; background: #000; }
.glass { background: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--card-border); border-radius: var(--border-radius); }
.sticky-card { position: sticky; top: 96px; }
.time-pill { border-color: var(--card-border); background: rgba(255,255,255,.02); color: #fff; border-radius: 20px; transition: var(--transition); }
.time-pill:hover { background: var(--accent); border-color: var(--accent); }
.star-rating { display: flex; gap: 6px; align-items: center; }
.star-rating .star i { font-size: 1.6rem; color: #7a8ba3; cursor: pointer; transition: .12s; }
.star-rating input:checked + .star i,
.star-rating .star.hover i,
.star-rating .star:hover i { color: #ffc107; }
.avatar { width: 36px; height: 36px; border-radius: 50%; background: #243240; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; }
.review-item { background: rgba(255,255,255,0.02); border-radius: var(--border-radius); }

/* PHIM LIÊN QUAN */
.related-modern .relx-card img { transition: var(--transition); }
.related-modern .relx-card:hover img { transform: scale(1.05); }
.related-modern .relx-card .overlay { background: rgba(0,0,0,0.35); opacity: 0; transition: var(--transition); pointer-events: none; }
.related-modern .relx-card:hover .overlay { opacity: 1; }
.relx-title { font-size: .875rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }

.hero-bg img { object-fit: cover; width: 100%; height: 100%; }
.hero-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.2) 20%, rgba(0,0,0,0.9) 100%); }
.sticky-card { position: sticky; top: 100px; }
.content-frame { background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); border: 1px solid var(--card-border); }

/* Override Bootstrap text classes for better contrast */
.text-body-secondary {
    color: var(--text-muted) !important;
}

.text-secondary {
    color: var(--text-muted) !important;
}

.badge {
    color: #fff !important;
}
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

    // ================= Auto switch schedule tabs =================
    (function(){
        const container = document.getElementById('lichchieu');
        if (!container) return;
        const tabButtons = Array.from(document.querySelectorAll('#dayTabs .nav-link'));
        const tabPanes = Array.from(document.querySelectorAll('.tab-content .tab-pane'));
        if (tabButtons.length === 0 || tabPanes.length === 0) return;

        let idx = tabButtons.findIndex(b => b.classList.contains('active'));
        if (idx < 0) idx = 0;
        let paused = false;
        let timer = null;

        function showTabAt(i){
            const next = ((i % tabButtons.length) + tabButtons.length) % tabButtons.length;
            const btn = tabButtons[next];
            if (!btn) return;
            // Use Bootstrap Tab API if available
            try {
                const tab = new bootstrap.Tab(btn);
                tab.show();
            } catch (_) {
                // Fallback: simulate click
                btn.click();
            }
            idx = next;
        }

        function start(){
            if (timer) return;
            timer = setInterval(()=>{ if (!paused) showTabAt(idx+1); }, 8000);
        }
        function stop(){ if (timer) { clearInterval(timer); timer = null; } }

        // Pause on hover
        container.addEventListener('mouseenter', ()=>{ paused = true; });
        container.addEventListener('mouseleave', ()=>{ paused = false; });

        // Update idx on manual click and briefly pause
        tabButtons.forEach((btn, i)=>{
            btn.addEventListener('click', ()=>{ idx = i; paused = true; setTimeout(()=>paused=false, 4000); });
        });

        start();
    })();
</script>
@endpush
@endsection
