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

    <!-- QUẢN LÝ NỘI DUNG -->
    <div class="sidebar-heading">🎬 Quản lý nội dung</div>

    <!-- Phim -->
    <li class="nav-item {{ request()->is('admin/phim*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPhim" aria-expanded="false" aria-controls="menuPhim">
            <i class="fas fa-fw fa-film"></i>
            <span>Phim</span>
        </a>
        <div id="menuPhim" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/phim') ? 'active' : '' }}" href="{{ route('admin.phim.index') }}">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách phim
                </a>
                <a class="collapse-item {{ request()->is('admin/phim/create') ? 'active' : '' }}" href="{{ route('admin.phim.create') }}">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm phim mới
                </a>
            </div>
        </div>
    </li>

    <!-- Danh mục -->
    <li class="nav-item {{ request()->is('admin/danhmuc*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuDanhMuc" aria-expanded="false" aria-controls="menuDanhMuc">
            <i class="fas fa-fw fa-tags"></i>
            <span>Danh mục</span>
        </a>
        <div id="menuDanhMuc" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/danhmuc') ? 'active' : '' }}" href="{{ route('admin.danhmuc.index') }}">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách danh mục
                </a>
                <a class="collapse-item {{ request()->is('admin/danhmuc/create') ? 'active' : '' }}" href="{{ route('admin.danhmuc.create') }}">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm danh mục
                </a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- QUẢN LÝ RẠP & SUẤT CHIẾU -->
    <div class="sidebar-heading">🏢 Phòng chiếu & Suất chiếu</div>

    <!-- Rạp -->
    <li class="nav-item {{ request()->is('admin/rap*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuRap" aria-expanded="false" aria-controls="menuRap">
            <i class="fas fa-fw fa-building"></i>
            <span>Rạp chiếu</span>
        </a>
        <div id="menuRap" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/rap') ? 'active' : '' }}" href="{{ route('admin.rap.index') }}">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách rạp
                </a>
                <a class="collapse-item {{ request()->is('admin/rap/create') ? 'active' : '' }}" href="{{ route('admin.rap.create') }}">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm rạp mới
                </a>
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
                <a class="collapse-item" href="{{ route('admin.phongchieu.index') }}">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách phòng chiếu
                </a>
                <a class="collapse-item" href="{{ route('admin.phongchieu.create') }}">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm phòng chiếu
                </a>
            </div>
        </div>
    </li>

    <!-- Suất chiếu -->
   <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuSuatChieu"
        aria-expanded="false" aria-controls="menuSuatChieu">
        <i class="fas fa-fw fa-clock"></i>
        <span>Suất chiếu</span>
    </a>
    <div id="menuSuatChieu" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('admin.suatchieu.index') }}">
                <i class="fas fa-list fa-sm mr-2"></i> Danh sách suất chiếu
            </a>
            <a class="collapse-item" href="{{ route('admin.suatchieu.create') }}">
                <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm suất chiếu
            </a>
        </div>
    </div>
</li>
   {{-- <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuGhe"
        aria-expanded="false" aria-controls="menuGhe">
        <i class="fas fa-fw fa-chair"></i>
        <span>Ghế</span>
    </a>
    <div id="menuGhe" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="#">
                <i class="fas fa-list fa-sm mr-2"></i> Danh sách ghế
            </a>
            <a class="collapse-item" href="#">
                <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm ghế mới
            </a>
        </div>
    </div>
</li> --}}


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- QUẢN LÝ BÁN HÀNG -->
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
            <span>Sản phẩm</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuGiamGia" aria-expanded="false" aria-controls="menuGiamGia">
            <i class="fas fa-fw fa-percent"></i>
            <span>Mã giảm giá</span>
        </a>
        <div id="menuGiamGia" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách mã</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm mã mới</a>
            </div>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuDoiDiem" aria-expanded="false" aria-controls="menuDoiDiem">
            <i class="fas fa-fw fa-percent"></i>
            <span>Ưu đãi đổi điểm</span>
        </a>
        <div id="menuDoiDiem" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.voucher.index') }}"><i class="fas fa-list fa-sm mr-2"></i> Danh sách mã</a>
                <a class="collapse-item" href="{{ route('admin.voucher.create') }}"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm mã mới</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- NGƯỜI DÙNG & ĐƠN -->
    <div class="sidebar-heading">👥 Người dùng & Đơn hàng</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="/admin/nguoi-dung" data-toggle="collapse" data-target="#menuTaiKhoan" aria-expanded="false" aria-controls="menuTaiKhoan">
            <i class="fas fa-fw fa-user"></i>
            <span>Tài khoản</span>
        </a>
        <div id="menuTaiKhoan" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.nguoi-dung.index') }}"><i class="fas fa-list fa-sm mr-2"></i> Danh sách tài khoản</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuDon" aria-expanded="false" aria-controls="menuDon">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Đơn đặt vé</span>
        </a>
        <div id="menuDon" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.donve.index') }}"><i class="fas fa-list fa-sm mr-2"></i> Danh sách đơn</a>
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
