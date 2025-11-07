{{-- ================= HEADER CLIENT ================= --}}
@php
    use App\Models\DanhMuc;
    $danhmucs = DanhMuc::orderBy('ten', 'asc')->get();
@endphp

<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm" id="mainNavbar">
    <div class="container d-flex align-items-center">

        {{-- 🔹 Logo --}}
        <a class="navbar-brand d-flex align-items-center fw-bold text-danger" href="{{ route('home') }}">
            <img src="{{ asset('uploads/rap/logo-datn.png') }}" alt="GoCinema" style="height: 36px; margin-right: 8px;">
            GoCinema
        </a>

        {{-- 🔹 Menu --}}
        <ul class="navbar-nav ms-auto d-flex align-items-center">

            <li class="nav-item"><a class="nav-link px-3" href="{{ route('home') }}">Trang chủ</a></li>
            <span class="divider">|</span>

            <li class="nav-item"><a class="nav-link px-3" href="#lich-chieu">Lịch Chiếu</a></li>
            <span class="divider">|</span>

            {{-- 🔹 Danh mục phim --}}
            <li class="nav-item dropdown mega-parent">
                <a class="nav-link px-3" href="{{ route('movies.index') }}">Danh Mục Phim</a>
                <div class="mega-box">
                    <div class="mega-container">
                        <div class="row row-cols-4 g-2">
                            @foreach($danhmucs as $dm)
                                <div class="col">
                                    {{-- data-no-preserve: used by small JS below to ensure left-click navigates to the clean URL (no leftover query string) --}}
                                    <a class="dropdown-item" href="{{ route('movies.category', $dm->slug) }}" data-no-preserve="1">
                                        {{ $dm->ten }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </li>
            <span class="divider">|</span>

            <li class="nav-item"><a class="nav-link px-3" href="{{route('baiviet.index')}}">Tin Tức</a></li>
            <span class="divider">|</span>

            <li class="nav-item"><a class="nav-link px-3" href="{{ route('policies') }}">Quy định & Chính sách</a></li>
            <span class="divider">|</span>

            {{-- 🔹 Liên hệ (dropdown click mở giống auth) --}}
            <li class="nav-item dropdown">
                <a class="nav-link px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    Liên hệ
                </a>
                <ul class="dropdown-menu shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="{{ route('contact.create') }}">📩 Gửi liên hệ</a></li>
                    @auth
                        <li><a class="dropdown-item" href="{{ route('contact.history') }}">🗂️ Lịch sử liên hệ</a></li>
                    @endauth
                </ul>
            </li>

        </ul>

        {{-- 🔹 Auth buttons --}}
        <div class="d-flex align-items-center ms-3">
            @guest
                <a class="btn btn-outline-light btn-sm me-2" href="{{ route('login') }}">Đăng Nhập</a>
                <a class="btn btn-outline-light btn-sm" href="{{ route('register') }}">Đăng ký</a>
            @else
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->avatar_url ?? asset('uploads/default-avatar.png') }}" 
                            alt="Avatar" 
                            class="rounded-circle me-2"
                            style="width: 36px; height: 36px; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);">
                        <span class="fw-semibold">{{ Auth::user()->ho_ten ?? Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('account.index') }}">
                                <i class="fas fa-user-circle me-2 text-primary"></i> Hồ sơ cá nhân
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('account.rewards') }}">
                                <i class="fas fa-gift me-2 text-warning"></i> Đổi điểm thưởng
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item d-flex align-items-center text-danger" type="submit">
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
    background: #16213e !important;
    transition: 0.25s ease;
}
.nav-link { color: #fff !important; font-weight: 500; }
.nav-link:hover { text-decoration: underline; }

.divider {
    color: rgba(255,255,255,0.4);
    margin: 0 8px;
}

/* ========== MEGA MENU DANH MỤC ========== */
.mega-parent { position: relative; }

.mega-box {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100vw;
    max-width: 1180px;
    background: rgba(15, 20, 35, 0.96);
    backdrop-filter: blur(8px);
    padding: 10px 0;
    border-radius: 0 0 12px 12px;
    opacity: 0;
    visibility: hidden;
    transition: .22s ease;
    z-index: 999;
}
.mega-parent:hover .mega-box {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}
.mega-container {
    max-height: 240px;
    overflow-y: auto;
    padding: 16px 30px;
}
.mega-container::-webkit-scrollbar { width: 6px; }
.mega-container::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.18);
    border-radius: 20px;
}
.mega-box .dropdown-item {
    padding: 6px 4px !important;
    background: none !important;
    color: #fff !important;
    font-size: 0.94rem;
    font-weight: 500;
    transition: .2s ease;
}
.mega-box .dropdown-item:hover {
    color: #ffb13a !important;
    text-decoration: underline;
}

/* Ẩn caret (mũi tên) bootstrap */
.dropdown-toggle::after,
.mega-parent > a::after {
    display: none !important;
}

/* ========== DROPDOWN STYLE (Liên hệ + Auth) ========== */
.dropdown-menu {
    border-radius: 10px;
    background-color: #111827;
    color: #e5e7eb;
    animation: fadeIn .2s ease;
}
.dropdown-item {
    color: #e5e7eb;
    font-weight: 500;
}
.dropdown-item:hover {
    background-color: #1f2937;
    color: #fff;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
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
</script>
