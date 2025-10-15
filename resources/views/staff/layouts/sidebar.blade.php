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
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPhim" aria-expanded="false" aria-controls="menuPhim">
            <i class="fas fa-fw fa-film"></i>
            <span>Phim</span>
        </a>
        <div id="menuPhim" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('staff/phim') ? 'active' : '' }}" href="{{ route('staff.phim.index') }}">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách phim
                </a>
                <a class="collapse-item {{ request()->is('staff/phim/create') ? 'active' : '' }}" href="{{ route('staff.phim.create') }}">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm phim mới
                </a>
            </div>
        </div>
    </li>

    <!-- Danh mục -->
    <li class="nav-item {{ request()->is('staff/danhmuc*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuDanhMuc" aria-expanded="false" aria-controls="menuDanhMuc">
            <i class="fas fa-fw fa-tags"></i>
            <span>Danh mục</span>
        </a>
        <div id="menuDanhMuc" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('staff/danhmuc') ? 'active' : '' }}" href="{{ route('staff.danhmuc.index') }}">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách danh mục
                </a>
                <a class="collapse-item {{ request()->is('staff/danhmuc/create') ? 'active' : '' }}" href="{{ route('staff.danhmuc.create') }}">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm danh mục
                </a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- QUẢN LÝ RẠP & SUẤT CHIẾU -->
    <div class="sidebar-heading">🏢 Suất chiếu</div>

    <!-- Suất chiếu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuSuatChieu" aria-expanded="false" aria-controls="menuSuatChieu">
            <i class="fas fa-fw fa-clock"></i>
            <span>Suất chiếu</span>
        </a>
        <div id="menuSuatChieu" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách suất chiếu</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm suất chiếu</a>
            </div>
        </div>
    </li>

    <!-- Phòng chiếu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPhongChieu" aria-expanded="false" aria-controls="menuPhongChieu">
            <i class="fas fa-fw fa-tv"></i>
            <span>Phòng chiếu</span>
        </a>
        <div id="menuPhongChieu" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách phòng chiếu</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm phòng chiếu</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- QUẢN LÝ BÁN HÀNG -->
    <div class="sidebar-heading">💰 Bán hàng & Ưu đãi</div>

    <li class="nav-item {{ request()->is('staff/san_pham*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staff.san_pham.index') }}">
            <i class="fas fa-box-open"></i>
            <span>Sản phẩm</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- NGƯỜI DÙNG & ĐƠN -->
    <div class="sidebar-heading">👥Đơn hàng</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuDon" aria-expanded="false" aria-controls="menuDon">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Đơn đặt vé</span>
        </a>
        <div id="menuDon" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách đơn</a>
                <a class="collapse-item" href="#"><i class="fas fa-check-circle fa-sm mr-2"></i> Check-in vé</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Toggle -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End Sidebar -->
