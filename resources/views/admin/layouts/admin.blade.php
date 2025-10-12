<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Admin Dashboard - {{ config('app.name') }}</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/admins/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/admins/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .chart-area,
        .chart-pie {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('admin.layouts.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('admin.layouts.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    {{-- ✅ Nơi nội dung các trang con hiển thị --}}
                    @yield('content')
                </div>
                <!-- End Page Content -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('admin.layouts.footer')
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
    @include('admin.layouts.logout-modal')

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
