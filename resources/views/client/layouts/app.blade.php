<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'GoCinema'))</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Client CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/client.css') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            padding-top: var(--body-top-padding, 0px);
            background-color: #16213e;
        } 
        main {
            min-height: calc(100vh - 220px);
        }
        body:not(.has-banner) {
            --body-top-padding: 76px;
        }

        @media (max-width: 767px) {
            body:not(.has-banner) {
                --body-top-padding: 100px;
            }
        }
        .carousel,
        .carousel-item,
        .carousel-item img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .card {
            background: #fff !important;
            color: #000 !important;
            margin-bottom: 1.5rem;
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
