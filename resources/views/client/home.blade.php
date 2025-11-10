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
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
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
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded px-3 py-2">
                                <h5 class="m-0">{{ $banner->title }}</h5>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ================= PHIM NỔI BẬT ================= --}}
    <div class="container py-5 section-featured">
        <h2 class="fw-bold mb-4 text-danger text-center">🎬 Phim nổi bật</h2>
        <div class="row g-4">
            @forelse ($featured as $phim)
                <div class="col-6 col-md-3">
                    <a href="{{ route('movies.show', $phim->slug) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                            {{-- Ảnh poster --}}
                            @if ($phim->anh_poster)
                                <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-top poster-img"
                                    alt="{{ $phim->tieu_de }}">
                            @else
                                <div class="card-img-top bg-secondary" style="height:280px;border-radius:8px 8px 0 0;">
                                </div>
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
                            <div
                                class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                            </div>
                            {{-- Thông tin phim --}}
                            <div class="card-body text-center p-3">
                                <h6 class="card-title text-truncate mb-1 fw-semibold">{{ $phim->tieu_de }}</h6>
                                {{-- Danh mục --}}
                                @if ($phim->danhMucs->count())
                                    <small class="text-info d-block mb-2">
                                        <i class="bi bi-tags-fill me-1"></i>
                                        {{ $phim->danhMucs->pluck('ten')->join(', ') }}
                                    </small>
                                @endif
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-clock me-1"></i>Thời lượng: {{ $phim->thoi_luong }} phút
                                </small>

                                @if ($phim->do_tuoi_gioi_han)
                                    <small class="badge bg-danger">Độ tuổi: {{ $phim->do_tuoi_gioi_han }}</small>
                                @endif
                            </div>

                            {{-- Overlay --}}
                            <div class="overlay d-flex justify-content-center align-items-center">
                                <span class="text-white fw-bold">Xem chi tiết</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-muted text-center">Chưa có phim nổi bật.</p>
            @endforelse
        </div>
    </div>

    {{-- ================= PHIM ĐANG CHIẾU ================= --}}
    <div class="container py-5 section-nowshowing">
        <h2 class="fw-bold mb-4 text-primary text-center">🎟️ Phim đang chiếu</h2>
        <div class="row g-4">
            @forelse ($nowShowing as $phim)
                <div class="col-6 col-md-3">
                    <a href="{{ route('movies.show', $phim->slug) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                            {{-- Ảnh poster --}}
                            @if ($phim->anh_poster)
                                <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-top poster-img"
                                    alt="{{ $phim->tieu_de }}">
                            @else
                                <div class="card-img-top bg-secondary" style="height:280px;border-radius:8px 8px 0 0;">
                                </div>
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
                            <div
                                class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                            </div>

                            {{-- Thông tin phim --}}
                            <div class="card-body text-center p-3">
                                <h6 class="card-title text-truncate mb-1 fw-semibold">{{ $phim->tieu_de }}</h6>
                                {{-- Danh mục --}}
                                @if ($phim->danhMucs->count())
                                    <small class="text-info d-block mb-2">
                                        <i class="bi bi-tags-fill me-1"></i>
                                        {{ $phim->danhMucs->pluck('ten')->join(', ') }}
                                    </small>
                                @endif
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-clock me-1"></i>Thời lượng: {{ $phim->thoi_luong }} phút
                                </small>

                                @if ($phim->do_tuoi_gioi_han)
                                    <small class="badge bg-danger">Độ tuổi: {{ $phim->do_tuoi_gioi_han }}</small>
                                @endif
                            </div>

                            {{-- Overlay --}}
                            <div class="overlay d-flex justify-content-center align-items-center">
                                <span class="text-white fw-bold">Xem chi tiết</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-muted text-center">Chưa có phim đang chiếu.</p>
            @endforelse
        </div>
    </div>

    {{-- ================= PHIM SẮP CHIẾU ================= --}}
    <div class="container py-5 section-comingsoon">
        <h2 class="fw-bold mb-4 text-success text-center">⏳ Phim sắp chiếu</h2>
        <div class="row g-4">
            @forelse ($comingSoon as $phim)
                <div class="col-6 col-md-3">
                    <a href="{{ route('movies.show', $phim->slug) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                            {{-- Ảnh poster --}}
                            @if ($phim->anh_poster)
                                <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-top poster-img"
                                    alt="{{ $phim->tieu_de }}">
                            @else
                                <div class="card-img-top bg-secondary" style="height:280px;border-radius:8px 8px 0 0;">
                                </div>
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
                            <div
                                class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                            </div>

                            {{-- Thông tin phim --}}
                            <div class="card-body text-center p-3">
                                <h6 class="card-title text-truncate mb-1 fw-semibold">{{ $phim->tieu_de }}</h6>
                                {{-- Danh mục --}}
                                @if ($phim->danhMucs->count())
                                    <small class="text-info d-block mb-2">
                                        <i class="bi bi-tags-fill me-1"></i>
                                        {{ $phim->danhMucs->pluck('ten')->join(', ') }}
                                    </small>
                                @endif
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-clock me-1"></i>Thời lượng: {{ $phim->thoi_luong }} phút
                                </small>

                                @if ($phim->do_tuoi_gioi_han)
                                    <small class="badge bg-danger">Độ tuổi: {{ $phim->do_tuoi_gioi_han }}</small>
                                @endif
                            </div>

                            {{-- Overlay --}}
                            <div class="overlay d-flex justify-content-center align-items-center">
                                <span class="text-white fw-bold">Xem chi tiết</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-white text-center">Chưa có phim sắp chiếu.</p>
            @endforelse
        </div>
    </div>

    {{-- ================= Tại sao chọn chúng tôi ================= --}}
    <section class="why-choose py-5">
        <div class="container">
            <h2 class="why-title text-center text-white fw-bold mb-3">Tại sao chọn chúng tôi?</h2>
            <p class="text-center text-white mb-5">Website đặt vé xem phim hàng đầu Việt Nam với trải nghiệm người dùng
                tuyệt vời.</p>

            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="feature-card p-4 h-100">
                        <h4 class="feature-title mb-3">Giao diện thân thiện</h4>
                        <p class="feature-desc mb-0">Dễ dàng tìm kiếm phim, rạp và suất chiếu phù hợp.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="feature-card p-4 h-100">
                        <h4 class="feature-title mb-3">Thanh toán linh hoạt</h4>
                        <p class="feature-desc mb-0">Hỗ trợ nhiều hình thức thanh toán trực tuyến an toàn.</p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="feature-card p-4 h-100">
                        <h4 class="feature-title mb-3">Ưu đãi hấp dẫn</h4>
                        <p class="feature-desc mb-0">Nhận ưu đãi, voucher và thông báo phim mới mỗi ngày.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Đăng ký tài khoản ================= --}}
    <section class="why-choose py-5">
        <div class="container text-center">
            <h2 class="why-title text-white fw-bold mb-3">Sẵn sàng đặt vé xem phim?</h2>
            <p class="text-white mb-4">Đăng ký tài khoản ngay để nhận nhiều ưu đãi và trải nghiệm dịch vụ tốt nhất!</p>

            @guest
                <a href="{{ route('register') }}" class="btn btn-danger btn-lg">Đăng ký tài khoản</a>
            @else
                <button type="button" class="btn btn-secondary btn-lg" id="alreadyLoggedInBtn">
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
        }

        .poster-img {
            height: 280px;
            object-fit: cover;
            border-radius: 16px 16px 0 0; /* bo tròn góc trên */
            transition: transform 0.3s ease;
        }
        .movie-card:hover .poster-img {
            transform: scale(1.08); /* poster zoom nổi bật hơn */
        }
        .movie-card {
            border-radius: 16px; /* viền bo lớn */
            overflow: hidden;
            cursor: pointer;
            position: relative;
            background-color: #ffffff; /* nền sáng để nổi bật trên background section */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3); /* shadow rõ */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .movie-card:hover {
            transform: translateY(-6px) scale(1.05);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45); /* hover shadow sâu hơn */
        }

        .movie-card .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
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
            padding: 6px 14px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.85rem;
            z-index: 2;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.6);
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 14px; /* bo tròn overlay trùng card */
        }
        .overlay span {
            font-size: 1.2rem;
        }

        .why-choose {
            background: transparent;
        }

        .why-title {
            font-size: 2.1rem;
        }

        .feature-card {
            background: linear-gradient(180deg, rgba(8, 18, 30, 0.95), rgba(11, 23, 36, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(2, 6, 23, 0.6);
            color: #dbeaf7;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .feature-card:hover {
            border: 1px solid red;
            box-shadow: 0 6px 18px rgba(255, 0, 0, 0.5);
            transform: translateY(-3px);
        }

        .feature-title {
            color: #fff;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .feature-desc {
            color: #9fb6cc;
            line-height: 1.6;
            font-size: 0.98rem;
        }

        .section-featured {
            background-color: rgba(255, 0, 0, 0.05);
            border-radius: 12px;
            padding: 40px 20px;
            margin-bottom: 30px;
        }

        .section-nowshowing {
            background-color: rgba(0, 123, 255, 0.05);
            border-radius: 12px;
            padding: 40px 20px;
            margin-bottom: 30px;
        }

        .section-comingsoon {
            background-color: rgba(40, 167, 69, 0.05);
            border-radius: 12px;
            padding: 40px 20px;
            margin-bottom: 30px;
        }

        .section-featured h2,
        .section-nowshowing h2,
        .section-comingsoon h2 {
            font-size: 2.5rem;
            letter-spacing: 1px;
        }

        @media (max-width:768px) {

            .section-featured h2,
            .section-nowshowing h2,
            .section-comingsoon h2 {
                font-size: 2rem;
            }
        }

        @media (max-width:576px) {

            .section-featured h2,
            .section-nowshowing h2,
            .section-comingsoon h2 {
                font-size: 1.8rem;
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
