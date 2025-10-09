<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/admin') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-film"></i>
        </div>
        <div class="sidebar-brand-text mx-3">GoCinema Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Bảng điều khiển</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Quản lý rạp</div>

    <!-- Nav Items -->
    <li class="nav-item">
    <a class="nav-link" href="{{ route('admin.rap.index') }}">
        <i class="fas fa-fw fa-building"></i>
        <span>Danh sách rạp</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.rap.create') }}">
        <i class="fas fa-fw fa-plus-circle"></i>
        <span>Thêm rạp mới</span>
    </a>
</li>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">Quản lý phim</div>

    <!-- Quản lý Danh mục -->
    <li class="nav-item {{ request()->is('admin/danhmuc*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.danhmuc.index') }}">
            <i class="fas fa-fw fa-list"></i>
            <span>Danh mục phim</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/danhmuc/create') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.danhmuc.create') }}">

            <i class="fas fa-fw fa-plus-circle"></i>
            <span>Thêm danh mục</span>
        </a>
    </li>

    <!-- Quản lý Phim -->
    <li class="nav-item {{ request()->is('admin/phim*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.phim.index') }}">
            <i class="fas fa-fw fa-video"></i>
            <span>Danh sách phim</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/phim/create') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.phim.create') }}">
            <i class="fas fa-fw fa-plus-circle"></i>
            <span>Thêm phim mới</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
