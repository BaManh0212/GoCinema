{{-- ================= HEADER CLIENT ================= --}}
@php
    use App\Models\DanhMuc;
    $danhmucs = DanhMuc::orderBy('ten', 'asc')->get();
@endphp

<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-transparent" id="mainNavbar">
    <div class="container d-flex align-items-center">

        {{-- 🔹 Logo --}}
        <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('home') }}" style="color: var(--accent);">
            <img src="{{ asset('uploads/rap/logo-datn.png') }}" alt="GoCinema" style="height: 40px; margin-right: 10px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
            <span style="font-size: 1.5rem; font-weight: 800;">GoCinema</span>
        </a>

        {{-- 🔹 Menu --}}
        <ul class="navbar-nav ms-auto d-flex align-items-center">

            <li class="nav-item">
                <a class="nav-link px-3" href="{{ route('home') }}" style="font-weight: 500; transition: var(--transition);">
                    <i class="fas fa-home me-1"></i>Trang chủ
                </a>
            </li>
            <span class="divider">|</span>

            <li class="nav-item">
                <a class="nav-link px-3" href="{{ route('schedule.index') }}" style="font-weight: 500; transition: var(--transition);">
                    <i class="fas fa-calendar-alt me-1"></i>Lịch Chiếu
                </a>
            </li>
            <span class="divider">|</span>

            {{-- 🔹 Danh mục phim --}}
            <li class="nav-item dropdown mega-parent">
                <a class="nav-link px-3" href="{{ route('movies.index') }}" style="font-weight: 500; transition: var(--transition);">
                    <i class="fas fa-film me-1"></i>Danh Mục Phim
                </a>
                <div class="mega-box">
                    <div class="mega-container">
                        <div class="row row-cols-4 g-3">
                            @foreach($danhmucs as $dm)
                                <div class="col">
                                    <a class="dropdown-item" href="{{ route('movies.category', $dm->slug) }}" data-no-preserve="1" style="border-radius: 8px; transition: var(--transition);">
                                        <i class="fas fa-tag me-2" style="color: var(--accent);"></i>{{ $dm->ten }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </li>
            <span class="divider">|</span>

            <li class="nav-item">
                <a class="nav-link px-3" href="{{route('baiviet.index')}}" style="font-weight: 500; transition: var(--transition);">
                    <i class="fas fa-newspaper me-1"></i>Tin Tức
                </a>
            </li>
            <span class="divider">|</span>

            <li class="nav-item">
                <a class="nav-link px-3" href="{{ route('policies') }}" style="font-weight: 500; transition: var(--transition);">
                    <i class="fas fa-shield-alt me-1"></i>Quy định & Chính sách
                </a>
            </li>
            <span class="divider">|</span>

            {{-- 🔹 Liên hệ (dropdown click mở giống auth) --}}
            <li class="nav-item dropdown">
                <a class="nav-link px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown" style="font-weight: 500; transition: var(--transition);">
                    <i class="fas fa-envelope me-1"></i>Liên hệ
                </a>
                <ul class="dropdown-menu shadow border-0 mt-2" style="background: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--card-border); border-radius: 12px;">
                    <li><a class="dropdown-item" href="{{ route('contact.create') }}" style="color: var(--text-light);"><i class="fas fa-paper-plane me-2" style="color: var(--accent);"></i>Gửi liên hệ</a></li>
                    @auth
                        <li><a class="dropdown-item" href="{{ route('contact.history') }}" style="color: var(--text-light);"><i class="fas fa-history me-2" style="color: var(--accent);"></i>Lịch sử liên hệ</a></li>
                    @endauth
                </ul>
            </li>

        </ul>

        {{-- 🔹 Auth buttons --}}
        <div class="d-flex align-items-center ms-3">
            @guest
                <a class="btn btn-outline-light btn-sm me-2" href="{{ route('login') }}" style="border-radius: 20px; font-weight: 600; transition: var(--transition); border-color: var(--accent); color: var(--accent);">Đăng Nhập</a>
                <a class="btn btn-danger btn-sm" href="{{ route('register') }}" style="border-radius: 20px; font-weight: 600;">Đăng ký</a>
            @else
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" style="border-radius: 20px; padding: 8px 12px; transition: var(--transition);">
                        <img src="{{ Auth::user()->avatar_url ?? asset('uploads/default-avatar.png') }}"
                            alt="Avatar"
                            class="rounded-circle me-2"
                            style="width: 36px; height: 36px; object-fit: cover; border: 2px solid var(--accent);">
                        <span class="fw-semibold">{{ Auth::user()->ho_ten ?? Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="background: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--card-border); border-radius: 12px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('account.index') }}" style="color: var(--text-light);">
                                <i class="fas fa-user-circle me-2" style="color: var(--accent);"></i> Hồ sơ cá nhân
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('account.rewards') }}" style="color: var(--text-light);">
                                <i class="fas fa-gift me-2" style="color: var(--accent);"></i> Đổi điểm thưởng
                            </a>
                        </li>
                        <li><hr class="dropdown-divider" style="border-color: var(--card-border);"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center text-danger" type="submit" style="border: none; background: none; color: #ff6b6b;">
                                    <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</nav>

<style>
#mainNavbar {
    background: rgba(15, 12, 41, 0.95) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: var(--transition);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
    z-index: 1030;
}

#mainNavbar.navbar-transparent {
    background: rgba(15, 12, 41, 0.3) !important;
    backdrop-filter: blur(5px);
    border-bottom: none;
    box-shadow: none;
}

#mainNavbar:hover {
    background: rgba(15, 12, 41, 0.98) !important;
}

.nav-link {
    color: var(--text-light) !important;
    font-weight: 500;
    position: relative;
}

.nav-link:hover {
    color: var(--accent) !important;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--accent);
    transition: var(--transition);
    transform: translateX(-50%);
}

.nav-link:hover::after {
    width: 100%;
}

.divider {
    color: rgba(255,255,255,0.4);
    margin: 0 8px;
    font-weight: 300;
}

/* ========== MEGA MENU DANH MỤC ========== */
.mega-parent { position: relative; }

.mega-box {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100vw;
    max-width: 1200px;
    background: var(--card-bg);
    backdrop-filter: blur(15px);
    border: 1px solid var(--card-border);
    padding: 20px 0;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    opacity: 0;
    visibility: hidden;
    transition: var(--transition);
    z-index: 999;
    box-shadow: var(--shadow);
}
.mega-parent:hover .mega-box {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}
.mega-container {
    max-height: 300px;
    overflow-y: auto;
    padding: 20px 40px;
}
.mega-container::-webkit-scrollbar { width: 6px; }
.mega-container::-webkit-scrollbar-thumb {
    background: var(--accent);
    border-radius: 20px;
}
.mega-box .dropdown-item {
    padding: 10px 12px !important;
    background: none !important;
    color: var(--text-light) !important;
    font-size: 0.95rem;
    font-weight: 500;
    transition: var(--transition);
    border-radius: 8px;
    margin-bottom: 5px;
}
.mega-box .dropdown-item:hover {
    background: rgba(255, 107, 107, 0.1) !important;
    color: var(--accent) !important;
    transform: translateX(5px);
}

/* Ẩn caret (mũi tên) bootstrap */
.dropdown-toggle::after,
.mega-parent > a::after {
    display: none !important;
}

/* ========== DROPDOWN STYLE (Liên hệ + Auth) ========== */
.dropdown-menu {
    border-radius: var(--border-radius);
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--card-border);
    color: var(--text-light);
    animation: fadeIn .3s ease;
    box-shadow: var(--shadow);
    z-index: 1000;
}
.dropdown-item {
    color: var(--text-light);
    font-weight: 500;
    transition: var(--transition);
}
.dropdown-item:hover {
    background: rgba(255, 107, 107, 0.1);
    color: var(--accent);
    transform: translateX(5px);
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

{{-- Small inline script: when user left-clicks a category link, navigate to the plain href (strip any existing querystring).
     Allows middle-click / ctrl/cmd-click to open in new tab normally. This is a lightweight guard against
     accidentally preserving current query string when navigating from pages that have filter params. --}}
<script>
    (function () {
        document.addEventListener('click', function (ev) {
            // Only handle anchors with data-no-preserve
            var a = ev.target.closest && ev.target.closest('a[data-no-preserve="1"]');
            if (!a) return;

            // Allow middle-click or cmd/ctrl/meta to open in new tab/window
            if (ev.button !== 0 || ev.ctrlKey || ev.metaKey || ev.shiftKey || ev.altKey) return;

            // Prevent default and navigate to the href without any query string
            ev.preventDefault();
            var href = a.getAttribute('href') || '';
            // Remove query string and hash from current href, but preserve hash on target if present
            var parts = href.split('?');
            var clean = parts[0];
            window.location.href = clean;
        }, false);
    })();

    // Script to make navbar solid on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('mainNavbar');
        const banner = document.querySelector('#bannerCarousel');

        if (banner) {
            const bannerHeight = banner.offsetHeight;

            window.addEventListener('scroll', function() {
                if (window.scrollY > bannerHeight - 80) {
                    navbar.classList.remove('navbar-transparent');
                } else {
                    navbar.classList.add('navbar-transparent');
                }
            });
        }
    });
</script>
