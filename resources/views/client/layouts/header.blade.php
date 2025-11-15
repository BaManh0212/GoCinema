<header class="client-hero" style="position:relative; height:70vh; overflow:hidden; display:flex; align-items:center;">
    @php
        // $banner should be passed from controller as URL or null
        $hasBanner = !empty($banner);
    @endphp
    @if($hasBanner)
        <div class="hero-bg" style="position:absolute;inset:0;background-image: url('{{ $banner }}'); background-size:cover; background-position:center; filter:brightness(0.6); z-index:0;"></div>
    @else
        <div class="hero-bg" style="position:absolute;inset:0;background: linear-gradient(180deg, rgba(210,33,44,0.95), rgba(30,30,30,0.9)); z-index:0;"></div>
    @endif
    <div class="container-fluid px-0" style="position:absolute;inset:0;z-index:2;">
        <div class="d-flex justify-content-end px-4 pt-3">
            @guest
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light me-2">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-light">Đăng ký</a>
            @else
                <div class="dropdown">
                    <a class="btn btn-sm btn-outline-light dropdown-toggle" href="#" role="button" id="headerUserMenu" data-bs-toggle="dropdown" aria-expanded="false">{{ Auth::user()->name }}</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="headerUserMenu">
                        <li><a class="dropdown-item" href="{{ route('profile.account') }}">Hồ sơ</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button class="dropdown-item" type="submit">Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>

    <div class="text-start px-4" style="max-width:1100px;margin:0 auto;color:#fff;position:relative;">
            <p class="text-uppercase small" style="opacity:.9;">Khám phá thế giới điện ảnh đỉnh cao</p>
            <h1 class="display-2 fw-bold" style="line-height:1.05;">Đắm chìm trong những câu chuyện<br>Đỉnh cao của điện ảnh</h1>
            <p class="lead mt-3" style="max-width:720px;opacity:.95;">Từ những bộ phim bom tấn đình đám đến những tác phẩm nghệ thuật đầy cảm xúc, chúng tôi mang đến cho bạn trải nghiệm xem phim tuyệt vời nhất.</p>
            <div class="mt-4">
                <a href="#phim" class="btn btn-lg btn-danger me-3" style="padding:0.8rem 2rem;border-radius:40px;">Xem phim ngay</a>
            </div>
        </div>
    </div>
</header>
