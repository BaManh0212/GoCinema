<!-- Sidebar -->

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

```
<!-- Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
    <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-film"></i></div>
    <div class="sidebar-brand-text mx-3">GoCinema <sup>Admin</sup></div>
</a>

<hr class="sidebar-divider my-0">

<!-- Dashboard -->
<li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Bảng điều khiển</span>
    </a>
</li>

<hr class="sidebar-divider">

<!-- Nội dung & Banner -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuNoiDung" aria-expanded="false">
        <i class="fas fa-fw fa-film"></i>
        <span>🎬 Nội dung & Banner</span>
    </a>
    <div id="menuNoiDung" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('admin.phim.index') }}"><i class="fas fa-film fa-sm mr-2"></i> Phim</a>
            <a class="collapse-item" href="{{ route('admin.danhmuc.index') }}"><i class="fas fa-tags fa-sm mr-2"></i> Danh mục</a>
            <a class="collapse-item" href="{{ route('admin.banners.index') }}"><i class="fas fa-image fa-sm mr-2"></i> Banner</a>
        </div>
    </div>
</li>

<!-- Rạp & Suất chiếu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuRap" aria-expanded="false">
        <i class="fas fa-fw fa-cubes"></i>
        <span>🏢 Rạp & Suất chiếu</span>
    </a>
    <div id="menuRap" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('admin.rap.index') }}"><i class="fas fa-cubes fa-sm mr-2"></i> Rạp chiếu</a>
            <a class="collapse-item" href="{{ route('admin.phongchieu.index') }}"><i class="fas fa-tv fa-sm mr-2"></i> Phòng chiếu</a>
            <a class="collapse-item" href="{{ route('admin.suatchieu.index') }}"><i class="fas fa-clock fa-sm mr-2"></i> Suất chiếu</a>
        </div>
    </div>
</li>

<!-- Bán hàng & Ưu đãi -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuBanHang" aria-expanded="false">
        <i class="fas fa-fw fa-money-bill-wave"></i>
        <span>💰 Bán hàng & Ưu đãi</span>
    </a>
    <div id="menuBanHang" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('admin.combo.index') }}"><i class="fas fa-cubes fa-sm mr-2"></i> Combo</a>
            <a class="collapse-item" href="{{ route('admin.san_pham.index') }}"><i class="fas fa-box-open fa-sm mr-2"></i> Sản phẩm</a>
            <a class="collapse-item collapsed" href="#" data-toggle="collapse" data-target="#subUuDai" aria-expanded="false"><i class="fas fa-percent fa-sm mr-2"></i> Ưu đãi</a>
            <div id="subUuDai" class="collapse">
                <a class="collapse-item" href="{{ route('admin.ma_giam_gia.index') }}">Mã giảm giá</a>
                <a class="collapse-item" href="{{ route('admin.voucher.index') }}">Đổi điểm</a>
            </div>
        </div>
    </div>
</li>

<!-- Quản lý hệ thống -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuHeThong" aria-expanded="false">
        <i class="fas fa-fw fa-users"></i>
        <span>👥 Hệ thống</span>
    </a>
    <div id="menuHeThong" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('admin.nguoi-dung.index') }}"><i class="fas fa-user fa-sm mr-2"></i> Tài khoản</a>
            <a class="collapse-item" href="{{ route('admin.donve.index') }}"><i class="fas fa-ticket-alt fa-sm mr-2"></i> Đơn vé</a>
            <a class="collapse-item" href="{{ route('admin.baiviet.index') }}"><i class="fas fa-newspaper fa-sm mr-2"></i> Bài viết</a>
            <a class="collapse-item" href="{{ route('admin.contacts.index') }}"><i class="fas fa-envelope fa-sm mr-2"></i> Liên hệ</a>
            <a class="collapse-item" href="{{ route('admin.logs.index') }}"><i class="fas fa-history fa-sm mr-2"></i> Lịch sử</a>
        </div>
    </div>
</li>

<hr class="sidebar-divider d-none d-md-block">

<!-- Toggle -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>
```

</ul>
<!-- End Sidebar -->
