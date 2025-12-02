<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('staff.donve.index') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-film"></i>
        </div>
        <div class="sidebar-brand-text mx-3">GoCinema <sup>Staff</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- ĐƠN HÀNG -->
    <div class="sidebar-heading">📦 Đơn đặt vé</div>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('staff.donve.index') }}">
            <i class="fas fa-fw fa-ticket-alt"></i>
            <span>Đơn đặt vé</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('staff.donve.checkin') }}">
            <i class="fas fa-fw fa-check-circle"></i>
            <span>Check-in vé</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('staff.donve.create') }}">
            <i class="fas fa-fw fa-check-circle"></i>
            <span>Tạo đơn vé</span>
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