<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-film"></i>
        </div>
        <div class="sidebar-brand-text mx-3">GoCinema <sup>Admin</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Bảng điều khiển</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- NỘI DUNG & BANNER -->
    <div class="sidebar-heading">🎬 Nội dung & Banner</div>

    <!-- Phim -->
    <li class="nav-item {{ request()->is('admin/phim*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('admin.phim.index') }}">
            <i class="fas fa-fw fa-film"></i>
            <span>Phim</span>
        </a>
    </li>

    <!-- Danh mục -->
    <li class="nav-item {{ request()->is('admin/danhmuc*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('admin.danhmuc.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Danh mục</span>
        </a>
    </li>

    <!-- Banner -->
    <li class="nav-item {{ request()->is('admin/banners*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.banners.index') }}">
            <i class="fas fa-image"></i>
            <span>Banner</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- RẠP & SUẤT CHIẾU -->
    <div class="sidebar-heading">🏢 Rạp & Suất chiếu</div>

    <!-- Rạp -->
    <li class="nav-item {{ request()->is('admin/rap*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.rap.index') }}">
            <i class="fas fa-cubes"></i>
            <span>Rạp chiếu</span>
        </a>
    </li>

    <!-- Phòng chiếu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.phongchieu.index') }}">
            <i class="fas fa-fw fa-tv"></i>
            <span>Phòng chiếu</span>
        </a>
    </li>

    <!-- Suất chiếu -->
   <li class="nav-item">
    <a class="nav-link collapsed" href="{{ route('admin.suatchieu.index') }}">
        <i class="fas fa-fw fa-clock"></i>
        <span>Suất chiếu</span>
    </a>
</li>
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- BÁN HÀNG & ƯU ĐÃI -->
    <div class="sidebar-heading">💰 Bán hàng & Ưu đãi</div>

    <li class="nav-item {{ request()->is('admin/combo*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.combo.index') }}">
            <i class="fas fa-cubes"></i>
            <span>Combo</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/san_pham*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.san_pham.index') }}">
            <i class="fas fa-box-open"></i>
            <span>Đồ ăn và đồ lưu niệm</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuUuDai" aria-expanded="false" aria-controls="menuUuDai">
            <i class="fas fa-fw fa-percent"></i>
            <span>Ưu đãi</span>
        </a>
        <div id="menuUuDai" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.ma_giam_gia.index') }}"><i class="fas fa-list fa-sm mr-2"></i> Mã giảm giá</a>
                <a class="collapse-item" href="{{ route('admin.voucher.index') }}"><i class="fas fa-list fa-sm mr-2"></i> Ưu đãi đổi điểm</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- QUẢN LÝ HỆ THỐNG -->
    <div class="sidebar-heading">👥 Quản lý hệ thống</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.nguoi-dung.index') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Tài khoản</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.donve.index') }}" >
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Đơn đặt vé</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/baiviet*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.baiviet.index') }}">
            <i class="fas fa-newspaper"></i>
            <span>Bài viết</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/contacts*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.contacts.index') }}">
            <i class="fas fa-envelope"></i>
            <span>Liên hệ</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/logs*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.logs.index') }}">
            <i class="fas fa-fw fa-history"></i>
            <span>Lịch sử Check-in & In vé</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Toggle -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End Sidebar -->
