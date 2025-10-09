<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
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

    <!-- Quản lý phim -->
    <div class="sidebar-heading">Quản lý nội dung</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPhim">
            <i class="fas fa-fw fa-film"></i>
            <span>Phim</span>
        </a>
        <div id="menuPhim" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách phim
                </a>
                <a class="collapse-item" href="#">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm phim mới
                </a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuDanhMuc">
            <i class="fas fa-fw fa-tags"></i>
            <span>Danh mục</span>
        </a>
        <div id="menuDanhMuc" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách danh mục</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm danh mục</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuRap">
            <i class="fas fa-fw fa-building"></i>
            <span>Rạp chiếu</span>
        </a>
        <div id="menuRap" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.rap.index') }}">
                    <i class="fas fa-list fa-sm mr-2"></i> Danh sách rạp
                </a>
                <a class="collapse-item" href="{{ route('admin.rap.create') }}">
                    <i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm rạp mới
                </a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Quản lý chiếu phim -->
    <div class="sidebar-heading">Chiếu phim</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuSuatChieu">
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

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuPhongChieu">
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

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuGhe">
            <i class="fas fa-fw fa-chair"></i>
            <span>Ghế ngồi</span>
        </a>
        <div id="menuGhe" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách ghế</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm ghế</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Quản lý bán hàng -->
    <div class="sidebar-heading">Bán hàng & Ưu đãi</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuMaGiamGia">
            <i class="fas fa-fw fa-percent"></i>
            <span>Mã giảm giá</span>
        </a>
        <div id="menuMaGiamGia" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách mã</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm mã mới</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuCombo">
            <i class="fas fa-fw fa-box"></i>
            <span>Combo</span>
        </a>
        <div id="menuCombo" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách combo</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm combo</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuSanPham">
            <i class="fas fa-fw fa-candy-cane"></i>
            <span>Sản phẩm</span>
        </a>
        <div id="menuSanPham" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách sản phẩm</a>
                <a class="collapse-item" href="#"><i class="fas fa-plus-circle fa-sm mr-2"></i> Thêm sản phẩm</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Người dùng & đơn -->
    <div class="sidebar-heading">Người dùng & Đơn hàng</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuTaiKhoan">
            <i class="fas fa-fw fa-user"></i>
            <span>Tài khoản</span>
        </a>
        <div id="menuTaiKhoan" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#"><i class="fas fa-list fa-sm mr-2"></i> Danh sách tài khoản</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuDon">
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

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
