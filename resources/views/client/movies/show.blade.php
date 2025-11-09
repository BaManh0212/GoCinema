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

        {{-- ================= HERO ================= --}}
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
                                            class="text-black">{{ $phim->dinh_dang ?: '2D' }}</span></li>
                                    <li class="mb-2"><span class="text-body-secondary">Lượt xem:</span> <span
                                            class="text-black">{{ number_format($phim->luot_xem ?? 0) }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tiêu đề + badges --}}
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
                        <li class="list-inline-item me-3"><i class="bi bi-clock me-1"></i>Thời lượng:
                            <strong>{{ $phim->thoi_luong ?? '—' }}</strong> phút
                        </li>
                        <li class="list-inline-item me-3"><i class="bi bi-calendar2-week me-1"></i>Khởi chiếu:
                            <strong>{{ $phim->ngay_cong_chieu ? \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') : '—' }}</strong>
                        </li>
                        @if ($phim->ngay_ket_thuc)
                            <li class="list-inline-item me-3"><i class="bi bi-calendar2-x me-1"></i>Kết thúc:
                                <strong>{{ \Carbon\Carbon::parse($phim->ngay_ket_thuc)->format('d/m/Y') }}</strong>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </section>

        {{-- ================= BODY ================= --}}
        <div class="container my-5">

            {{-- Mô tả --}}
            <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold">Nội dung phim</h5>
                    <p class="mb-0 text-body-emphasis" style="line-height:1.85">{{ $phim->mo_ta }}</p>
                </div>
            </div>

            {{-- Lịch chiếu 7 ngày --}}
            <div id="lichchieu" class="card bg-dark-subtle border-0 rounded-4 shadow-sm">
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
                            style="scrollbar-width:thin;">
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

            {{-- Đánh giá --}}
            <div class="row g-4 mt-4">
                <div class="col-lg-6">
                    <div class="card bg-dark-subtle border-0 rounded-4 shadow-sm h-100">
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

    {{-- ================= Phim liên quan (danh mục) — Slider ================= --}}
    @if (!empty($relatedMovies) && $relatedMovies->count())
        <div class="container my-5 related-modern">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="fw-bold text-light mb-0">Phim liên quan</h4>
                <div class="d-none d-lg-flex gap-2">
                    <button class="relx-nav btn btn-outline-light btn-sm rounded-pill" id="relxPrevTop" type="button"
                        aria-label="Trước">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="relx-nav btn btn-outline-light btn-sm rounded-pill" id="relxNextTop" type="button"
                        aria-label="Sau">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div id="relx" class="relx" data-autoplay="5000">
                <div class="relx-viewport">
                    {{-- arrows nổi hai bên --}}
                    <button class="relx-arrow relx-prev" id="relxPrev" type="button" aria-label="Trước"><i
                            class="bi bi-chevron-left"></i></button>
                    <button class="relx-arrow relx-next" id="relxNext" type="button" aria-label="Sau"><i
                            class="bi bi-chevron-right"></i></button>

                    <div class="relx-track">
                        @foreach ($relatedMovies as $rel)
                            <div class="relx-slide">
                                <a href="{{ route('movies.show', $rel->slug) }}" class="relx-card">
                                    {{-- KHÔNG dùng ratio-3x4 nữa --}}
                                    <div class="relx-media">
                                        @if ($rel->anh_poster)
                                            <img src="{{ asset('storage/' . $rel->anh_poster) }}"
                                                class="w-100 h-100 object-fit-cover" alt="{{ $rel->tieu_de }}">
                                        @else
                                            <img src="{{ asset('images/no-poster.jpg') }}"
                                                class="w-100 h-100 object-fit-cover" alt="{{ $rel->tieu_de }}">
                                        @endif
                                        @php
                                            $today = \Carbon\Carbon::now()->startOfDay();
                                            $start = $rel->ngay_cong_chieu ?? ($rel->ngay_khoi_chieu ?? null);
                                            $end = $rel->ngay_ket_thuc ?? null;
                                            $dang =
                                                $start &&
                                                \Carbon\Carbon::parse($start)->lte($today) &&
                                                (!$end || \Carbon\Carbon::parse($end)->gte($today));
                                        @endphp
                                        <span class="relx-badge">{{ $dang ? 'Đang chiếu' : 'Sắp chiếu' }}</span>
                                        <div class="relx-overlay"><span>Xem chi tiết</span></div>
                                    </div>
                                    <div class="relx-info">
                                        <h6 class="relx-title" title="{{ $rel->tieu_de }}">{{ $rel->tieu_de }}</h6>
                                        @if ($rel->danhMucs->count())
                                            <div class="relx-meta"><i class="bi bi-tags-fill me-1"></i>
                                                <span
                                                    class="line-clamp-1">{{ $rel->danhMucs->pluck('ten')->join(', ') }}</span>
                                            </div>
                                        @endif
                                        <div class="relx-foot"><i class="bi bi-clock me-1"></i>{{ $rel->thoi_luong }}
                                            phút</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="relxDots" class="relx-dots mt-3"></div>
            </div>
        </div>
    @endif


    {{-- ================= Styles ================= --}}
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
            filter: blur(1px) brightness(0.9) contrast(1.05) saturate(1.04);
            transform: scale(1.04);
            overflow: hidden
        }

        .movie-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background: transparent;
            opacity: 0;
            display: none;
            pointer-events: none;
        }

        .movie-hero .container {
            position: relative;
            z-index: 2
        }

        @media (min-width: 768px) {
            .movie-hero .hero-overlay::after {
                content: "";
                position: absolute;
                inset: 0;
                pointer-events: none;
                /* tối nhẹ quanh mép để trailer/poster nổi hơn */
                background: radial-gradient(80% 70% at 50% 35%,
                        rgba(0, 0, 0, 0) 0%,
                        rgba(0, 0, 0, 0) 55%,
                        rgba(0, 0, 0, 0.25) 100%);
            }
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

        /* ===== Related slider (relx*) ===== */
        .relx {
            --per: 4;
            position: relative
        }

        @media (max-width:991.98px) {
            .relx {
                --per: 3
            }
        }

        @media (max-width:767.98px) {
            .relx {
                --per: 2
            }
        }

        .relx-viewport {
            position: relative;
            overflow: hidden
        }

        .relx-track {
            display: flex;
            will-change: transform;
            transition: transform .55s cubic-bezier(.22, .61, .36, 1)
        }

        .relx-slide {
            flex: 0 0 calc(100% / var(--per));
            padding: 12px
        }

        /* KHUNG 3:4 bằng CSS chuẩn, không phụ thuộc Bootstrap ratio */
        .relx-media {
            position: relative;
            aspect-ratio: 3/4;
            overflow: hidden;
            background: #000;
        }

        .relx-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: contrast(1.02) saturate(1.02)
        }

        .relx-card {
            display: block;
            border-radius: 16px;
            overflow: hidden;
            background: #0f1625;
            border: 1px solid rgba(255, 255, 255, .06);
            text-decoration: none;
            color: inherit;
            box-shadow: 0 10px 20px rgba(0, 0, 0, .25);
            transition: transform .25s, box-shadow .25s, border-color .25s;
        }

        .relx-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 28px rgba(0, 0, 0, .35);
            border-color: rgba(229, 57, 53, .45)
        }

        .relx-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 2;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #fff;
            padding: 6px 10px;
            font-weight: 700;
            font-size: .72rem;
            border-radius: 999px;
            box-shadow: 0 4px 10px rgba(34, 197, 94, .4);
        }

        .relx-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent, rgba(0, 0, 0, .55) 60%);
            opacity: 0;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 14px;
            color: #fff;
            font-weight: 700;
            letter-spacing: .3px;
            transition: opacity .25s;
        }

        .relx-card:hover .relx-overlay {
            opacity: 1
        }

        .relx-info {
            padding: 12px 14px 14px
        }

        .relx-title {
            margin: 0 0 6px;
            font-weight: 800;
            color: #e6eef8;
            font-size: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden
        }

        .relx-meta {
            font-size: .86rem;
            color: #6fd3ff;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 6px
        }

        .relx-foot {
            font-size: .86rem;
            color: #b0b8c4
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden
        }

        /* Arrows */
        .relx-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .35);
            background: rgba(15, 22, 37, .55);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .35);
            z-index: 3;
            cursor: pointer;
            transition: background .2s, transform .2s, border-color .2s;
            opacity: .0;
        }

        .relx-viewport:hover .relx-arrow {
            opacity: 1
        }

        .relx-arrow:hover {
            background: #e53935;
            border-color: #e53935;
            transform: translateY(-50%) scale(1.06)
        }

        .relx-arrow:active {
            transform: translateY(-50%) scale(.96)
        }

        .relx-prev {
            left: 18px
        }

        .relx-next {
            right: 18px
        }

        @media (max-width:767.98px) {
            .relx-arrow {
                opacity: 1
            }
        }

        /* Dots */
        .relx-dots {
            display: flex;
            justify-content: center;
            gap: .5rem
        }

        .relx-dots .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #64748b;
            opacity: .55;
            border: 0
        }

        .relx-dots .dot.active {
            background: #e53935;
            opacity: 1
        }
    </style>

    {{-- ================= Scripts ================= --}}
    <script>
        (function() {
            // Bootstrap client-side validation
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(f => f.addEventListener('submit', e => {
                if (!f.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                f.classList.add('was-validated');
            }, false));
        })();
    </script>

    <script>
        (function() {
            const root = document.getElementById('relx');
            if (!root) return;

            const vp = root.querySelector('.relx-viewport');
            const trk = root.querySelector('.relx-track');
            const slides = Array.from(trk.children);

            const dotsWrap = document.getElementById('relxDots');
            const prev = document.getElementById('relxPrev');
            const next = document.getElementById('relxNext');
            const prevTop = document.getElementById('relxPrevTop');
            const nextTop = document.getElementById('relxNextTop');

            const autoplay = parseInt(root.dataset.autoplay || '5000', 10);
            let timer = null,
                page = 0,
                pages = 1;

            const perView = () => {
                const w = window.innerWidth;
                if (w < 768) return 2;
                if (w < 992) return 3;
                return 4;
            };

            function calcPages() {
                pages = Math.max(1, Math.ceil(slides.length / perView()));
            }

            function makeDots() {
                dotsWrap.innerHTML = '';
                for (let i = 0; i < pages; i++) {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'dot' + (i === page ? ' active' : '');
                    b.addEventListener('click', () => {
                        stop();
                        go(i);
                        start();
                    });
                    dotsWrap.appendChild(b);
                }
            }

            function go(i) {
                calcPages();
                if (i >= pages) i = 0;
                if (i < 0) i = pages - 1;
                page = i;
                const offset = vp.clientWidth * page;
                trk.style.transform = `translateX(-${offset}px)`;
                [...dotsWrap.children].forEach((d, idx) => d.classList.toggle('active', idx === page));
            }

            function start() {
                stop();
                timer = setInterval(() => go(page + 1), autoplay);
            }

            function stop() {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            }

            const bind = (el, dir) => el && el.addEventListener('click', () => {
                stop();
                go(page + dir);
                start();
            });
            bind(prev, -1);
            bind(next, 1);
            bind(prevTop, -1);
            bind(nextTop, 1);

            root.addEventListener('mouseenter', stop);
            root.addEventListener('mouseleave', start);

            // touch swipe
            let sx = 0,
                dx = 0;
            vp.addEventListener('touchstart', e => {
                sx = e.touches[0].clientX;
                dx = 0;
                stop();
            }, {
                passive: true
            });
            vp.addEventListener('touchmove', e => {
                dx = e.touches[0].clientX - sx;
            }, {
                passive: true
            });
            vp.addEventListener('touchend', () => {
                if (Math.abs(dx) > 40) go(page + (dx < 0 ? 1 : -1));
                start();
            });

            // resize debounce
            let rs;
            window.addEventListener('resize', () => {
                clearTimeout(rs);
                rs = setTimeout(() => {
                    calcPages();
                    makeDots();
                    go(page);
                }, 150);
            });

            // init
            calcPages();
            makeDots();
            go(0);
            start();
        })();
    </script>
@endsection
