@extends('client.layouts.app')

@section('title', 'Trang chủ - GoCinema')

@section('content')
    @push('scripts')
        <script>
            document.body.classList.add('has-banner');
        </script>
    @endpush

    {{-- ================= BANNER SLIDER ================= --}}
    @if ($banners->count())
        <div id="bannerCarousel" class="carousel slide position-relative" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($banners as $i => $banner)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        @if ($banner->type === 'image')
                            @if ($banner->link)
                                <a href="{{ $banner->link }}">
                                    <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100 banner-media"
                                        alt="{{ $banner->title }}">
                                </a>
                            @else
                                <img src="{{ asset('storage/' . $banner->image) }}" class="d-block w-100 banner-media"
                                    alt="{{ $banner->title }}">
                            @endif
                        @endif
                        @if ($banner->type === 'video')
                            <video class="w-100 banner-media" autoplay muted loop playsinline>
                                <source src="{{ asset('storage/' . $banner->video_url) }}" type="video/mp4">
                            </video>
                        @endif
                        @if ($banner->title)
                            <div class="carousel-caption d-none d-md-block" style="background: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--card-border); border-radius: var(--border-radius); padding: 15px;">
                                <h5 class="m-0" style="color: var(--text-light); font-weight: 600;">{{ $banner->title }}</h5>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    @endif

    {{-- ================= PHIM NỔI BẬT ================= --}}
    <section class="py-5" style="background: var(--secondary-bg);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title" style="color: var(--accent); font-size: 2.5rem;">🎬 Phim nổi bật</h2>
                <p class="text-secondary mb-0" style="color: var(--text-muted);">Khám phá những bộ phim hot nhất đang chiếu tại GoCinema</p>
            </div>

            <div class="row g-4">
                @forelse ($featured as $phim)
                    <div class="col-6 col-md-3">
                        <a href="{{ route('movies.show', $phim->slug) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                                {{-- Ảnh poster --}}
                                @if ($phim->anh_poster)
                                    <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-top poster-img" alt="{{ $phim->tieu_de }}">
                                @else
                                    <div class="card-img-top bg-secondary" style="height:280px;border-radius: var(--border-radius) var(--border-radius) 0 0;"></div>
                                @endif

                                {{-- Nhãn trạng thái góc trên --}}
                                @php
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $ngayBatDau = $phim->ngay_cong_chieu ?? ($phim->ngay_khoi_chieu ?? null);
                                    $ngayKetThuc = $phim->ngay_ket_thuc ?? null;

                                    if (
                                        $ngayBatDau &&
                                        \Carbon\Carbon::parse($ngayBatDau)->lte($today) &&
                                        (!$ngayKetThuc || \Carbon\Carbon::parse($ngayKetThuc)->gte($today))
                                    ) {
                                        $status = 'dang_chieu';
                                    } else {
                                        $status = 'sap_chieu';
                                    }
                                @endphp
                                <div class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}" style="border-radius: 20px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                    {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                                </div>

                                {{-- Thông tin phim --}}
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title text-truncate mb-1 fw-semibold" style="color: var(--text-light);">{{ $phim->tieu_de }}</h6>
                                    {{-- Danh mục --}}
                                    @if ($phim->danhMucs->count())
                                        <small class="text-info d-block mb-2" style="color: #74b9ff;">
                                            <i class="bi bi-tags-fill me-1"></i>
                                            {{ $phim->danhMucs->pluck('ten')->join(', ') }}
                                        </small>
                                    @endif
                                    <small class="text-muted d-block mb-1" style="color: var(--text-muted);">
                                        <i class="bi bi-clock me-1"></i>Thời lượng: {{ $phim->thoi_luong }} phút
                                    </small>

                                    @if ($phim->do_tuoi_gioi_han)
                                        <small class="badge" style="background: var(--accent); color: var(--text-light);">Độ tuổi: {{ $phim->do_tuoi_gioi_han }}</small>
                                    @endif
                                </div>

                                {{-- Overlay --}}
                                <div class="overlay d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.6);">
                                    <span class="text-white fw-bold" style="font-size: 1.1rem;">Xem chi tiết</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted fs-5 py-4" style="color: var(--text-muted);">Chưa có phim nổi bật.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ================= PHIM ĐANG CHIẾU ================= --}}
    <section class="py-5" style="background: var(--primary-bg);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title" style="color: var(--accent); font-size: 2.5rem;">🎟️ Phim đang chiếu</h2>
                <p class="text-secondary mb-0" style="color: var(--text-muted);">Xem ngay những bộ phim đang hot tại các rạp</p>
            </div>

            <div class="row g-4">
                @forelse ($nowShowing as $phim)
                    <div class="col-6 col-md-3">
                        <a href="{{ route('movies.show', $phim->slug) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                                {{-- Ảnh poster --}}
                                @if ($phim->anh_poster)
                                    <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-top poster-img" alt="{{ $phim->tieu_de }}">
                                @else
                                    <div class="card-img-top bg-secondary" style="height:280px;border-radius: var(--border-radius) var(--border-radius) 0 0;"></div>
                                @endif

                                {{-- Nhãn trạng thái góc trên --}}
                                @php
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $ngayBatDau = $phim->ngay_cong_chieu ?? ($phim->ngay_khoi_chieu ?? null);
                                    $ngayKetThuc = $phim->ngay_ket_thuc ?? null;

                                    if (
                                        $ngayBatDau &&
                                        \Carbon\Carbon::parse($ngayBatDau)->lte($today) &&
                                        (!$ngayKetThuc || \Carbon\Carbon::parse($ngayKetThuc)->gte($today))
                                    ) {
                                        $status = 'dang_chieu';
                                    } else {
                                        $status = 'sap_chieu';
                                    }
                                @endphp
                                <div class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}" style="border-radius: 20px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                    {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                                </div>

                                {{-- Thông tin phim --}}
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title text-truncate mb-1 fw-semibold" style="color: var(--text-light);">{{ $phim->tieu_de }}</h6>
                                    {{-- Danh mục --}}
                                    @if ($phim->danhMucs->count())
                                        <small class="text-info d-block mb-2" style="color: #74b9ff;">
                                            <i class="bi bi-tags-fill me-1"></i>
                                            {{ $phim->danhMucs->pluck('ten')->join(', ') }}
                                        </small>
                                    @endif
                                    <small class="text-muted d-block mb-1" style="color: var(--text-muted);">
                                        <i class="bi bi-clock me-1"></i>Thời lượng: {{ $phim->thoi_luong }} phút
                                    </small>

                                    @if ($phim->do_tuoi_gioi_han)
                                        <small class="badge" style="background: var(--accent); color: var(--text-light);">Độ tuổi: {{ $phim->do_tuoi_gioi_han }}</small>
                                    @endif
                                </div>

                                {{-- Overlay --}}
                                <div class="overlay d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.6);">
                                    <span class="text-white fw-bold" style="font-size: 1.1rem;">Xem chi tiết</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted fs-5 py-4" style="color: var(--text-muted);">Chưa có phim đang chiếu.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ================= PHIM SẮP CHIẾU ================= --}}
    <section class="py-5" style="background: var(--secondary-bg);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title" style="color: var(--accent); font-size: 2.5rem;">⏳ Phim sắp chiếu</h2>
                <p class="text-secondary mb-0" style="color: var(--text-muted);">Đừng bỏ lỡ những bộ phim bom tấn sắp ra mắt</p>
            </div>

            <div class="row g-4">
                @forelse ($comingSoon as $phim)
                    <div class="col-6 col-md-3">
                        <a href="{{ route('movies.show', $phim->slug) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                                {{-- Ảnh poster --}}
                                @if ($phim->anh_poster)
                                    <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-top poster-img" alt="{{ $phim->tieu_de }}">
                                @else
                                    <div class="card-img-top bg-secondary" style="height:280px;border-radius: var(--border-radius) var(--border-radius) 0 0;"></div>
                                @endif

                                {{-- Nhãn trạng thái góc trên --}}
                                @php
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $ngayBatDau = $phim->ngay_cong_chieu ?? ($phim->ngay_khoi_chieu ?? null);
                                    $ngayKetThuc = $phim->ngay_ket_thuc ?? null;

                                    if (
                                        $ngayBatDau &&
                                        \Carbon\Carbon::parse($ngayBatDau)->lte($today) &&
                                        (!$ngayKetThuc || \Carbon\Carbon::parse($ngayKetThuc)->gte($today))
                                    ) {
                                        $status = 'dang_chieu';
                                    } else {
                                        $status = 'sap_chieu';
                                    }
                                @endphp
                                <div class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}" style="border-radius: 20px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                    {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                                </div>

                                {{-- Thông tin phim --}}
                                <div class="card-body text-center p-3">
                                    <h6 class="card-title text-truncate mb-1 fw-semibold" style="color: var(--text-light);">{{ $phim->tieu_de }}</h6>
                                    {{-- Danh mục --}}
                                    @if ($phim->danhMucs->count())
                                        <small class="text-info d-block mb-2" style="color: #74b9ff;">
                                            <i class="bi bi-tags-fill me-1"></i>
                                            {{ $phim->danhMucs->pluck('ten')->join(', ') }}
                                        </small>
                                    @endif
                                    <small class="text-muted d-block mb-1" style="color: var(--text-muted);">
                                        <i class="bi bi-clock me-1"></i>Thời lượng: {{ $phim->thoi_luong }} phút
                                    </small>

                                    @if ($phim->do_tuoi_gioi_han)
                                        <small class="badge" style="background: var(--accent); color: var(--text-light);">Độ tuổi: {{ $phim->do_tuoi_gioi_han }}</small>
                                    @endif
                                </div>

                                {{-- Overlay --}}
                                <div class="overlay d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.6);">
                                    <span class="text-white fw-bold" style="font-size: 1.1rem;">Xem chi tiết</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted fs-5 py-4" style="color: var(--text-muted);">Chưa có phim sắp chiếu.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ================= Tại sao chọn chúng tôi ================= --}}
    <section class="py-5" style="background: var(--primary-bg);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--accent); font-size: 2.5rem;">Tại sao chọn chúng tôi?</h2>
                <p class="text-secondary mb-5" style="color: var(--text-muted); font-size: 1.1rem;">Website đặt vé xem phim hàng đầu Việt Nam với trải nghiệm người dùng tuyệt vời.</p>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3" style="font-size: 3rem; color: var(--accent);">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="feature-title mb-3" style="color: var(--text-light); font-weight: 700;">Giao diện thân thiện</h4>
                        <p class="feature-desc mb-0" style="color: var(--text-muted);">Dễ dàng tìm kiếm phim, rạp và suất chiếu phù hợp.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3" style="font-size: 3rem; color: var(--accent);">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4 class="feature-title mb-3" style="color: var(--text-light); font-weight: 700;">Thanh toán linh hoạt</h4>
                        <p class="feature-desc mb-0" style="color: var(--text-muted);">Hỗ trợ nhiều hình thức thanh toán trực tuyến an toàn.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3" style="font-size: 3rem; color: var(--accent);">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h4 class="feature-title mb-3" style="color: var(--text-light); font-weight: 700;">Ưu đãi hấp dẫn</h4>
                        <p class="feature-desc mb-0" style="color: var(--text-muted);">Nhận ưu đãi, voucher và thông báo phim mới mỗi ngày.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Đăng ký tài khoản ================= --}}
    <section class="py-5" style="background: var(--secondary-bg);">
        <div class="container text-center">
            <h2 class="fw-bold mb-3" style="color: var(--accent); font-size: 2.5rem;">Sẵn sàng đặt vé xem phim?</h2>
            <p class="mb-4" style="color: var(--text-muted); font-size: 1.1rem;">Đăng ký tài khoản ngay để nhận nhiều ưu đãi và trải nghiệm dịch vụ tốt nhất!</p>

            @guest
                <a href="{{ route('register') }}" class="btn btn-danger btn-lg px-5 py-3" style="border-radius: 30px; font-weight: 700; font-size: 1.1rem;">Đăng ký tài khoản</a>
            @else
                <button type="button" class="btn btn-secondary btn-lg px-5 py-3" id="alreadyLoggedInBtn" style="border-radius: 30px; font-weight: 700; font-size: 1.1rem;">
                    Đăng ký tài khoản
                </button>
            @endguest
        </div>
    </section>

    {{-- ================= TOAST THÔNG BÁO ================= --}}
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <div id="loginToast" class="toast align-items-center text-bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    ✅ Bạn đã đăng nhập tài khoản rồi!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>


    {{-- ================= CSS ================= --}}
    <style>
        .banner-media {
            max-height: 500px;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        #bannerCarousel {
            z-index: 1;
        }

        .poster-img {
            height: 280px;
            object-fit: cover;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            transition: var(--transition);
        }

        .movie-card:hover .poster-img {
            transform: scale(1.08);
        }

        .movie-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            cursor: pointer;
            position: relative;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .movie-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .movie-card .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            opacity: 0;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .movie-card:hover .overlay {
            opacity: 1;
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            z-index: 2;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .feature-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            color: var(--text-light);
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
            border-color: var(--accent);
        }

        .feature-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @media (max-width: 768px) {
            .display-4 {
                font-size: 2.5rem;
            }

            .feature-card {
                min-height: 180px;
            }
        }

        @media (max-width: 576px) {
            .display-4 {
                font-size: 2rem;
            }

            .feature-icon {
                font-size: 2.5rem;
            }
        }
    </style>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.getElementById('alreadyLoggedInBtn');
                if (btn) {
                    btn.addEventListener('click', function() {
                        const toastEl = document.getElementById('loginToast');
                        const toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    });
                }
            });
        </script>
    @endpush
@endsection
