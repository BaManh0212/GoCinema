<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('staff.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-film"></i>
        </div>
        <div class="sidebar-brand-text mx-3">GoCinema <sup>Staff</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staff.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Bảng điều khiển</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- QUẢN LÝ NỘI DUNG -->
    <div class="sidebar-heading">🎬 Quản lý nội dung</div>

    <!-- Phim -->
    <li class="nav-item {{ request()->is('staff/phim*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staff.phim.index') }}">
            <i class="fas fa-fw fa-film"></i>
            <span>Phim</span>
        </a>
    </li>

    <!-- Danh mục -->
    <li class="nav-item {{ request()->is('staff/danhmuc*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staff.danhmuc.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Danh mục</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- SUẤT CHIẾU -->
    <div class="sidebar-heading">🎟️ Suất chiếu & Phòng</div>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-clock"></i>
            <span>Suất chiếu</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-tv"></i>
            <span>Phòng chiếu</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- BÁN HÀNG -->
    <div class="sidebar-heading">🍿 Bán hàng</div>

    <li class="nav-item {{ request()->is('staff/san_pham*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staff.san_pham.index') }}">
            <i class="fas fa-box-open"></i>
            <span>Đồ ăn và đồ lưu niệm</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('staff/san_pham*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staff.combo.index') }}">
            <i class="fas fa-box-open"></i>
            <span>Combo</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- ĐƠN HÀNG -->
    <div class="sidebar-heading">📦 Đơn đặt vé</div>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Đơn đặt vé</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-check-circle"></i>
            <span>Check-in vé</span>
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
