@extends('client.layouts.app')

@php
    // Parse YouTube URL -> embed
    function youtubeEmbed($url)
    {
        if (!$url) {
            return null;
        }
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

        {{-- ====== HERO ====== --}}
        <section class="movie-hero position-relative">
            <div class="hero-bg">
                @if ($phim->anh_poster)
                    <img src="{{ asset('storage/' . $phim->anh_poster) }}" alt="bg" class="w-100 h-100 object-fit-cover">
                @endif
            </div>
            <div class="hero-overlay"></div>

            <div class="container py-5">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-8">
                        <div
                            class="ratio ratio-16x9 rounded-4 overflow-hidden border border-secondary-subtle bg-black shadow-lg">
                            @if ($yt)
                                <iframe src="{{ $yt }}" title="Trailer" allowfullscreen></iframe>
                            @else
                                <div class="d-flex align-items-center justify-content-center text-secondary">
                                    <div class="text-center p-4">
                                        <i class="bi bi-youtube display-4 d-block mb-2"></i>
                                        <div>Chưa có trailer</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card glass border-0 rounded-4 overflow-hidden shadow-lg h-100">
                            <div class="ratio ratio-2x3">
                                @if ($phim->anh_poster)
                                    <img src="{{ asset('storage/' . $phim->anh_poster) }}"
                                        class="w-100 h-100 object-fit-cover" alt="Poster">
                                @else
                                    <img src="{{ asset('images/no-poster.jpg') }}" class="w-100 h-100 object-fit-cover"
                                        alt="Poster">
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="small text-body-secondary">Đánh giá trung bình</div>
                                <div class="d-flex align-items-end gap-2 mb-2">
                                    <div class="display-5 fw-bold text-warning">{{ number_format($diemTB, 1) }}</div>
                                    <div class="text-body-secondary">/5</div>
                                </div>
                                <div class="small text-body-secondary mb-3">{{ $soDanhGia }} lượt đánh giá</div>

                                <a href="#lichchieu" class="btn btn-danger w-100 rounded-3 mb-3">
                                    <i class="bi bi-ticket-perforated-fill me-1"></i> Đặt vé ngay
                                </a>

                                {{-- THÔNG TIN NHANH (đưa lên chung card) --}}
                                <hr class="border-secondary-subtle">
                                <h6 class="fw-semibold mb-3">Thông tin</h6>
                                <ul class="list-unstyled m-0 small">
                                    <li class="mb-2"><span class="text-body-secondary">Đạo diễn:</span> <span
                                            class="text-black">{{ $phim->dao_dien ?: '—' }}</span></li>
                                    <li class="mb-2"><span class="text-body-secondary">Diễn viên:</span> <span
                                            class="text-black">{{ $phim->dien_vien ?: '—' }}</span></li>
                                    <li class="mb-2"><span class="text-body-secondary">Độ tuổi:</span> <span
                                            class="text-black">{{ $phim->do_tuoi_gioi_han ?: '—' }}</span></li>
                                    <li class="mb-2"><span class="text-body-secondary">Phụ đề:</span> <span
                                            class="text-black">{{ $phim->phu_de ?: '—' }}</span></li>
                                    <li class="mb-2"><span class="text-body-secondary">Định dạng:</span> <span
                                            class="text-black">{{ $phim->dinh_dang ?: '—' }}</span></li>
                                    <li class="mb-2"><span class="text-body-secondary">Lượt xem:</span> <span
                                            class="text-black">{{ number_format($phim->luot_xem ?? 0) }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Title + badges --}}
                <div class="mt-4">
                    <h1 class="h3 fw-bold text-light mb-2">{{ $phim->tieu_de }}</h1>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($theLoaiText)
                            <span class="badge rounded-pill text-bg-info"><i
                                    class="bi bi-collection-play me-1"></i>{{ $theLoaiText }}</span>
                        @endif
                        @if ($danhMucText)
                            <span class="badge rounded-pill text-bg-danger"><i
                                    class="bi bi-tags-fill me-1"></i>{{ $danhMucText }}</span>
                        @endif
                        @if ($phim->do_tuoi_gioi_han)
                            <span class="badge rounded-pill text-bg-warning text-dark"><i
                                    class="bi bi-shield-exclamation me-1"></i>{{ $phim->do_tuoi_gioi_han }}</span>
                        @endif
                        @if ($phim->dinh_dang)
                            <span class="badge rounded-pill text-bg-primary"><i
                                    class="bi bi-aspect-ratio me-1"></i>{{ $phim->dinh_dang }}</span>
                        @endif
                        @if ($phim->phu_de)
                            <span class="badge rounded-pill text-bg-secondary"><i class="bi bi-translate me-1"></i>Phụ đề:
                                {{ $phim->phu_de }}</span>
                        @endif
                    </div>

                    <ul class="list-inline mt-3 text-light">
                        <li class="list-inline-item me-3"><i class="bi bi-clock me-1"></i>Thời lượng: <strong
                                class="text-light">{{ $phim->thoi_luong ?? '—' }}</strong> phút</li>
                        <li class="list-inline-item me-3"><i class="bi bi-calendar2-week me-1"></i>Khởi chiếu: <strong
                                class="text-light">{{ $phim->ngay_cong_chieu ? \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') : '—' }}</strong>
                        </li>
                        @if ($phim->ngay_ket_thuc)
                            <li class="list-inline-item me-3"><i class="bi bi-calendar2-x me-1"></i>Kết thúc: <strong
                                    class="text-light">{{ \Carbon\Carbon::parse($phim->ngay_ket_thuc)->format('d/m/Y') }}</strong>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </section>

        {{-- ====== BODY ====== --}}
        <div class="container my-5">

            <div class="row g-4">
                <div class="col-lg-12">
                    {{-- Nội dung phim --}}
                    <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold">Nội dung phim</h5>
                            <p class="mb-0 text-body-emphasis" style="line-height:1.85">{{ $phim->mo_ta }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ====== Lịch chiếu theo ngày (nav-pills) ====== --}}
            <div id="lichchieu" class="card bg-dark-subtle border-0 rounded-4 shadow-sm mt-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-semibold mb-0">Lịch chiếu (7 ngày tới)</h5>
                        <a href="{{ route('movies.schedule.json', $phim->slug) }}" class="small text-info"
                            target="_blank">JSON</a>
                    </div>

                    @if ($lichChieuTheoNgay->isEmpty())
                        <div class="p-4 border rounded-3 text-body-secondary bg-dark">Hiện chưa có lịch chiếu.</div>
                    @else
                        @php $dates = $lichChieuTheoNgay->keys()->values(); @endphp

                        <ul class="nav nav-pills gap-2 flex-nowrap overflow-auto pb-2" id="dayTabs" role="tablist"
                            style="scrollbar-width: thin;">
                            @foreach ($dates as $i => $date)
                                @php $label = \Carbon\Carbon::parse($date)->isoFormat('ddd • DD/MM'); @endphp
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $i === 0 ? 'active' : '' }} rounded-pill"
                                        id="tab-{{ $i }}" data-bs-toggle="tab"
                                        data-bs-target="#pane-{{ $i }}" type="button" role="tab">
                                        {{ $label }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content mt-3">
                            @foreach ($dates as $i => $date)
                                @php
                                    $ds = $lichChieuTheoNgay[$date];
                                    $byRap = $ds->groupBy(fn($s) => $s->phong->rap->ten ?? 'Khác');
                                @endphp
                                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}"
                                    id="pane-{{ $i }}" role="tabpanel">
                                    @foreach ($byRap as $rapTen => $items)
                                        <div class="mb-3">
                                            <div class="fw-semibold mb-2">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $rapTen }}
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($items as $s)
                                                    @php
                                                        $gio = \Carbon\Carbon::parse($s->gio_bat_dau)->format('H:i');
                                                        $phong = $s->phong->ten ?? null;
                                                    @endphp
                                                    <a href="{{ url('/booking?suat_chieu_id=' . $s->id) }}"
                                                        class="btn btn-outline-light btn-sm rounded-pill d-inline-flex align-items-center gap-2">
                                                        <span class="fw-semibold">{{ $gio }}</span>
                                                        @if ($phong)
                                                            <span class="text-body-secondary">{{ $phong }}</span>
                                                        @endif
                                                        <span
                                                            class="badge text-bg-warning text-dark">{{ number_format($s->gia_ve, 0, ',', '.') }}đ</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @if (!$loop->last)
                                            <hr class="border-secondary-subtle">
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ====== Đánh giá ====== --}}
            <div class="row g-4 mt-4">
                <div class="col-lg-6">
                    <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-3">Bình luận & đánh giá</h5>

                            @if (session('success'))
                                <div class="alert alert-success py-2">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger  py-2">{{ session('error') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger py-2 small mb-3">
                                    @foreach ($errors->all() as $err)
                                        <div>{{ $err }}</div>
                                    @endforeach
                                </div>
                            @endif

                            @auth
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
                                    Vui lòng <a class="link-info" href="{{ url('/dang-nhap') }}">đăng nhập</a> để gửi đánh
                                    giá.
                                </div>
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
                                <div class="border rounded-3 p-3 mb-3 bg-dark">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar">{{ $initial }}</div>
                                            <div class="fw-semibold">{{ $name }}</div>
                                        </div>
                                        <div class="small text-body-secondary">{{ $dg->created_at?->format('d/m/Y H:i') }}
                                        </div>
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
    </div>

    {{-- ====== Phim liên quan (danh mục) — Slider ====== --}}
    @if (!empty($relatedMovies) && $relatedMovies->count())
        <div class="container my-5 related-section">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="fw-bold text-light mb-0">Phim liên quan</h1>
                <div class="d-flex gap-2">
                    <button id="relPrev" class="btn btn-outline-light btn-sm rounded-pill" type="button">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button id="relNext" class="btn btn-outline-light btn-sm rounded-pill" type="button">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div id="relSlider" class="rel-slider" data-autoplay="5000">
                <div class="rel-viewport">
                    <div class="rel-track">
                        @foreach ($relatedMovies as $rel)
                            <div class="rel-slide">
                                <a href="{{ route('movies.show', $rel->slug) }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                                        {{-- Poster --}}
                                        @if ($rel->anh_poster)
                                            <img src="{{ asset('storage/' . $rel->anh_poster) }}"
                                                class="card-img-top poster-img" alt="{{ $rel->tieu_de }}">
                                        @else
                                            <div class="card-img-top bg-secondary"
                                                style="height:280px;border-radius:10px 10px 0 0;"></div>
                                        @endif

                                        {{-- Trạng thái --}}
                                        @php
                                            $today = \Carbon\Carbon::now()->startOfDay();
                                            $ngayBatDau = $rel->ngay_cong_chieu ?? ($rel->ngay_khoi_chieu ?? null);
                                            $ngayKetThuc = $rel->ngay_ket_thuc ?? null;
                                            $status =
                                                $ngayBatDau &&
                                                \Carbon\Carbon::parse($ngayBatDau)->lte($today) &&
                                                (!$ngayKetThuc || \Carbon\Carbon::parse($ngayKetThuc)->gte($today))
                                                    ? 'dang_chieu'
                                                    : 'sap_chieu';
                                        @endphp
                                        <div
                                            class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                                        </div>

                                        {{-- Info --}}
                                        <div class="card-body text-center p-3">
                                            <h6 class="card-title text-truncate mb-1 fw-semibold">{{ $rel->tieu_de }}</h6>
                                            @if ($rel->danhMucs->count())
                                                <small class="text-info d-block mb-2">
                                                    <i
                                                        class="bi bi-tags-fill me-1"></i>{{ $rel->danhMucs->pluck('ten')->join(', ') }}
                                                </small>
                                            @endif
                                            <small class="text-muted d-block">
                                                <i class="bi bi-clock me-1"></i>{{ $rel->thoi_luong }} phút
                                            </small>
                                        </div>

                                        <div class="overlay d-flex justify-content-center align-items-center">
                                            <span class="text-white fw-bold">Xem chi tiết</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="relDots" class="rel-dots mt-3"></div>
        </div>
    @endif


    {{-- ====== Scoped styles ====== --}}
    <style>
        .object-fit-cover {
            object-fit: cover
        }

        .movie-hero {
            position: relative;
            min-height: 420px
        }

        .movie-hero .hero-bg {
            position: absolute;
            inset: 0;
            filter: blur(24px) brightness(0.6);
            transform: scale(1.08);
            overflow: hidden
        }

        .movie-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(3, 7, 18, 0.35), rgba(3, 7, 18, 0.9))
        }

        .movie-hero .container {
            position: relative;
            z-index: 2
        }

        .glass {
            background: rgba(255, 255, 255, .04);
            backdrop-filter: blur(10px)
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #343a40;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700
        }

        .nav-pills .nav-link {
            background: #0f1625;
            border: 1px solid rgba(255, 255, 255, .08);
            color: #dbe4f0
        }

        .nav-pills .nav-link.active {
            background: #e53935;
            color: #fff;
            border-color: #e53935
        }

        /* Cards chung */
        .poster-img {
            height: 280px;
            object-fit: cover;
            border-radius: 10px 10px 0 0
        }

        .movie-card {
            border-radius: 10px;
            background: #0f1625;
            transition: all .25s ease;
            position: relative
        }

        .movie-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .5)
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            z-index: 2
        }

        .movie-card .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            opacity: 0;
            transition: opacity .25s
        }

        .movie-card:hover .overlay {
            opacity: 1
        }

        /* === Related slider (tự cuộn) === */
        .rel-slider {
            --perView: 4;
            position: relative
        }

        .rel-viewport {
            overflow: hidden;
            width: 100%
        }

        .rel-track {
            display: flex;
            transition: transform .55s ease;
            will-change: transform
        }

        .rel-slide {
            flex: 0 0 calc(100% / var(--perView));
            padding: .5rem
        }

        /* breakpoints:  ≥992px:4 ; ≥768px:3 ; <768px:2 */
        @media (max-width: 991.98px) {
            .rel-slider {
                --perView: 3
            }
        }

        @media (max-width: 767.98px) {
            .rel-slider {
                --perView: 2
            }
        }

        /* Dots */
        .rel-dots {
            display: flex;
            gap: .5rem;
            justify-content: center
        }

        .rel-dots .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #6c757d;
            border: 0;
            opacity: .6
        }

        .rel-dots .dot.active {
            background: #e53935;
            opacity: 1
        }

        /* Đảm bảo không chồng nhau nếu có CSS khác override */
        .related-section .rel-slide {
            display: block
        }
    </style>

    {{-- Client-side form validation --}}
    <script>
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', e => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

    <script>
        (function() {
            const root = document.getElementById('relSlider');
            if (!root) return;

            const viewport = root.querySelector('.rel-viewport');
            const track = root.querySelector('.rel-track');
            const slides = Array.from(track.children);
            const dotsWrap = document.getElementById('relDots');
            const prevBtn = document.getElementById('relPrev');
            const nextBtn = document.getElementById('relNext');

            const autoplay = parseInt(root.dataset.autoplay || '5000', 10);
            let timer = null,
                current = 0,
                pages = 1;

            function perView() {
                const w = window.innerWidth;
                if (w < 768) return 2;
                if (w < 992) return 3;
                return 4;
            }

            function calcPages() {
                pages = Math.max(1, Math.ceil(slides.length / perView()));
            }

            function buildDots() {
                dotsWrap.innerHTML = '';
                for (let i = 0; i < pages; i++) {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'dot' + (i === current ? ' active' : '');
                    b.addEventListener('click', () => {
                        stop();
                        goTo(i);
                        start();
                    });
                    dotsWrap.appendChild(b);
                }
            }

            function goTo(index) {
                calcPages();
                if (index >= pages) index = 0;
                if (index < 0) index = pages - 1;
                current = index;

                const offset = viewport.clientWidth * current;
                track.style.transform = `translateX(-${offset}px)`;

                [...dotsWrap.children].forEach((d, i) => d.classList.toggle('active', i === current));
            }

            function start() {
                if (timer) clearInterval(timer);
                timer = setInterval(() => goTo(current + 1), autoplay);
            }

            function stop() {
                if (timer) clearInterval(timer);
                timer = null;
            }

            // Resize → giữ vị trí trang hiện tại
            window.addEventListener('resize', () => {
                calcPages();
                buildDots();
                goTo(current);
            });

            // Controls
            if (prevBtn) prevBtn.addEventListener('click', () => {
                stop();
                goTo(current - 1);
                start();
            });
            if (nextBtn) nextBtn.addEventListener('click', () => {
                stop();
                goTo(current + 1);
                start();
            });

            // Pause on hover
            root.addEventListener('mouseenter', stop);
            root.addEventListener('mouseleave', start);

            // Init
            calcPages();
            buildDots();
            goTo(0);
            start();
        })();
    </script>

@endsection
