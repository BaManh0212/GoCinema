<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Staff Dashboard - {{ config('app.name') }}</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/admins/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/admins/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        /* Giữ nguyên layout chính */
        .chart-area, .chart-pie {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Tùy chỉnh cho sơ đồ ghế */
        .seat-map {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .seat {
            width: 45px; height: 45px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin: 4px;
            text-align: center;
            font-size: 12px;
            line-height: 40px;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .seat:hover { transform: scale(1.1); }

        .seat-thuong { background-color: #87CEFA; } /* Xanh dương nhạt */
        .seat-vip { background-color: #FFD700; }     /* Vàng */
        .seat-doi { background-color: #98FB98; }     /* Xanh lá nhạt */
        .seat-empty { background-color: #f1f2f6; }

        .screen {
            background-color: #222;
            color: #fff;
            text-align: center;
            font-weight: 600;
            padding: 8px 0;
            margin-bottom: 25px;
            border-radius: 5px;
            width: 60%;
        }

        /* Flash message */
        .alert {
            border-radius: 8px;
            font-size: 15px;
        }

        /* Fix sidebar position */
        .sidebar {
            position: fixed !important;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        /* Hide scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            display: none;
        }

        #content-wrapper {
            margin-left: 250px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: static !important;
                height: auto;
            }
            #content-wrapper {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('staff.layouts.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('staff.layouts.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    {{-- ✅ Nơi nội dung các trang con hiển thị --}}
                    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3 mx-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3 mx-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

                    @yield('content')
                </div>
                <!-- End Page Content -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('staff.layouts.footer')
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    @include('staff.layouts.logout-modal')

    <!-- Core JavaScript -->
    <script src="{{ asset('assets/admins/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admins/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admins/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts -->
    <script src="{{ asset('assets/admins/js/sb-admin-2.min.js') }}"></script>

    <!-- Chart.js -->
    <script src="{{ asset('assets/admins/vendor/chart.js/Chart.min.js') }}"></script>

    @stack('scripts')
</body>

</html>