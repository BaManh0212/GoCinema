<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'GoCinema'))</title>

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ✅ Reset lỗi margin/padding */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
        }

        * {
            box-sizing: border-box;
        }

        /* ✅ Navbar */
        .navbar {
            background: #111;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.4rem;
            color: #fff !important;
        }
        .navbar .nav-link {
            color: #fff !important;
            font-weight: 500;
        }
        .navbar .nav-link:hover {
            color: #ffcc00 !important;
        }

        /* ✅ Body fix khoảng trống do navbar fixed-top */
        body {
            background: #f8f9fa;
            padding-top: 70px;
        }

        /* ✅ Slider full width */
        .carousel,
        .carousel-item,
        .carousel-item img {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* ✅ Card & text fix cho theme sáng */
        .card {
            background: #fff !important;
            color: #000 !important;
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ✅ Navbar --}}
    @include('client.layouts.navigation')

    {{-- ✅ Content --}}
    <main class="w-100 p-0 m-0">
        @yield('content')
    </main>

    {{-- ✅ Footer --}}
    @include('client.layouts.footer')

    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>
</html>
