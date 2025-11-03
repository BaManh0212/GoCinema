<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'GoCinema'))</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* 🎨 HEADER */
        .navbar {
            background: linear-gradient(90deg, #ff1f3d, #d4145a);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.4rem;
            color: #fff !important;
        }
        .navbar .nav-link {
            color: #fff !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .navbar .nav-link:hover {
            color: #ffe082 !important;
        }

        /* 🧭 BODY */
        body {
            padding-top: 75px; /* navbar fixed-top */
            background-color: #f9fafc;
        }

        /* 🎬 MOVIE POSTER / PLACEHOLDER */
        .movie-placeholder {
            background: #eaeaea;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
        }
        .card-img-cover {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        /* 📋 CARD STYLE TRANG TÀI KHOẢN */
        .card {
            background-color: #ffffff !important;
            color: #000000 !important;
        }

        .card label,
        .card .col-form-label {
            color: #222 !important;
            font-weight: 500;
        }

        .card input.form-control {
            color: #000 !important;
            background-color: #fff !important;
            border-color: #ccc !important;
        }

        .card-header.bg-primary,
        .card-header.bg-warning,
        .card-header.bg-success {
            color: #fff !important;
        }

        .list-group-item {
            color: #333 !important;
            background-color: #fff !important;
        }

        .list-group-item.active {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;
        }

        .text-muted {
            color: #555 !important;
        }

        /* 💡 FIX MẤT CHỮ KHI NỀN TỐI */
        .bg-dark,
        .bg-primary,
        .bg-gradient,
        .bg-danger,
        .bg-purple {
            color: #fff !important;
        }

        .bg-dark a,
        .bg-primary a,
        .bg-gradient a {
            color: #fff !important;
            text-decoration: underline;
        }

        .bg-dark .form-label,
        .bg-primary .form-label,
        .bg-gradient .form-label {
            color: #fff !important;
        }

        .text-light {
            color: #fff !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    {{-- 🔺 HEADER --}}
    @include('client.layouts.navigation')

    {{-- 🔹 CONTENT --}}
    <main class="py-4">
        @yield('content')
    </main>

    {{-- 🔻 FOOTER --}}
    @include('client.layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
