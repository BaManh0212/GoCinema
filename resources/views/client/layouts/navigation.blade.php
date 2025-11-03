{{-- ================= HEADER CLIENT ================= --}}
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm"
     id="mainNavbar"
     style="background: rgba(0, 0, 0, 0.85); transition: background 0.3s ease, box-shadow 0.3s ease;">
    <div class="container">
        {{-- 🔹 Logo --}}
        <a class="navbar-brand d-flex align-items-center fw-bold text-danger" href="{{ route('home') }}">
            <img src="{{ asset('uploads/rap/logo-datn.png') }}" alt="GoCinema" 
                 style="height: 36px; margin-right: 8px;">
            GoCinema
        </a>

        {{-- 🔹 Menu --}}
        <div class="flex-grow-1 d-flex justify-content-end align-items-center"> 
            <ul class="navbar-nav mb-2 mb-lg-0 d-flex align-items-center"> 
                <li class="nav-item">
                    <a class="nav-link px-3" href="{{ route('home') }}">Trang chủ</a>
                </li> 
                <span class="divider">|</span>
                <li class="nav-item">
                    <a class="nav-link px-3" href="#lich-chieu">Lịch Chiếu</a>
                </li>
                <span class="divider">|</span>
                <li class="nav-item">
                    <a class="nav-link px-3" href="#tin-tuc">Tin Tức</a>
                </li> 
                <span class="divider">|</span>
                <li class="nav-item">
                    <a class="nav-link px-3" href="#quy-dinh">Quy định</a>
                </li> 
                <span class="divider">|</span>
                <li class="nav-item">
                    <a class="nav-link px-3" href="#lien-he">Liên hệ</a>
                </li> 
            </ul>

            {{-- 🔹 Nút đăng nhập/đăng ký --}}
            <div class="d-flex align-items-center ms-3"> 
                @guest 
                <a class="btn btn-outline-light btn-sm me-2" href="{{ route('login') }}" style="border-radius:6px;padding:8px 14px">Đăng Nhập</a> 
                <a class="btn btn-outline-light btn-sm" href="{{ route('register') }}" style="background:rgba(255,255,255,0.06);border-radius:6px;padding:8px 14px">Đăng ký</a>
                @else 
                <div class="dropdown"> 
                    <a class="btn btn-outline-light btn-sm dropdown-toggle" href="#" role="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">{{ Auth::user()->name }}</a> 
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu"> 
                        <li>
                            <a class="dropdown-item" href="{{ route('account.index') }}">Hồ sơ</a>
                        </li> 
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf 
                                <button class="dropdown-item" type="submit">Đăng xuất</button> 
                            </form> 
                        </li> 
                    </ul> 
                </div> 
                @endguest
            </div>
        </div>
    </div>
</nav>

{{-- ================= HIỆU ỨNG CUỘN ================= --}}
<script>
document.addEventListener("scroll", function() {
    const navbar = document.getElementById("mainNavbar");
    if (window.scrollY > 50) {
        navbar.style.background = "rgba(0, 0, 0, 0.95)";
        navbar.style.boxShadow = "0 2px 8px rgba(0,0,0,0.3)";
    } else {
        navbar.style.background = "rgba(0, 0, 0, 0.85)";
        navbar.style.boxShadow = "none";
    }
});
</script>

{{-- ================= CSS ================= --}}
<style>
.nav-link {
    color: #fff !important;
    position: relative;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
}
.nav-link:hover,
.nav-link.active {
    color: #fff !important;
    text-decoration: underline;
}
.navbar-brand {
    font-size: 1.35rem;
    color: #fff !important;
}
.navbar-toggler i {
    font-size: 1.3rem;
}

/* Dấu gạch dọc giữa các menu */
.divider {
    color: rgba(255, 255, 255, 0.4);
    margin: 0 4px;
    font-weight: 300;
}
</style>
