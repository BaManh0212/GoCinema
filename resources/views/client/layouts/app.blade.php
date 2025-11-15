<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'GoCinema'))</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    {{-- Bootstrap JS Bundle v5 --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Client CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/client.css') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-bg: #0b1220;
            --secondary-bg: #07101a;
            --card-bg: #111827;
            --text-light: #e6eef8;
            --text-muted: #9ca3af;
            --accent: #e53935;
            --card-border: rgba(255,255,255,0.04);
            --border-radius: 12px;
            --shadow: 0 4px 12px rgba(0,0,0,0.3);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            padding-top: var(--body-top-padding, 0px);
            background: linear-gradient(180deg, var(--primary-bg) 0%, var(--secondary-bg) 100%);
            background-attachment: fixed;
            color: var(--text-light);
            font-family: 'Inter', 'Poppins', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        main {
            min-height: calc(100vh - 220px);
        }

        body:not(.has-banner) {
            --body-top-padding: 80px;
        }

        @media (max-width: 767px) {
            body:not(.has-banner) {
                --body-top-padding: 110px;
            }
        }

        .carousel,
        .carousel-item,
        .carousel-item img {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: var(--border-radius);
        }

        .card {
            background: var(--card-bg) !important;
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border) !important;
            border-radius: var(--border-radius) !important;
            color: var(--text-light) !important;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #ff4b2b);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ff4b2b, #e53935);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 57, 53, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333, #a02622);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: var(--text-light);
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.25);
            color: var(--text-light);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-gradient {
            background: linear-gradient(90deg, var(--accent), #ff4b2b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .display-4 {
            font-size: 3.5rem;
            font-weight: 800;
        }

        @media (max-width: 768px) {
            .display-4 {
                font-size: 2.5rem;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--secondary-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ff4b2b;
        }

        /* Page-specific styles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(229,57,53,0.6);
            color: var(--accent);
        }

        .poster-img {
            height: 280px;
            object-fit: cover;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            transition: var(--transition);
        }

        .movie-card:hover .poster-img {
            transform: scale(1.08);
        }

        .movie-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            cursor: pointer;
            position: relative;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .movie-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .movie-card .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            opacity: 0;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .movie-card:hover .overlay {
            opacity: 1;
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            z-index: 2;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .filter-box {
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .card-hover {
            border-radius: var(--border-radius);
            overflow: hidden;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow);
            transition: var(--transition);
            color: var(--text-light);
        }

        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .card-hover:hover img {
            transform: scale(1.08);
        }

        .hover-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow);
            transition: var(--transition);
            color: var(--text-light);
        }

        .hover-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .hover-card:hover img {
            transform: scale(1.08);
        }

        .account-sidebar {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: var(--card-bg);
            border: 1px solid var(--card-border);
        }

        .account-avatar {
            background: linear-gradient(135deg, #002855, #00509e);
            color: #fff;
            padding: 40px 20px;
        }

        .account-avatar i {
            font-size: 5rem;
        }

        .account-avatar h5 {
            margin-top: 10px;
            font-weight: 600;
            color: #fff;
        }

        .account-avatar p {
            color: #f1f1f1;
            margin-bottom: 10px;
        }

        .badge-points {
            background: linear-gradient(135deg, #ffb347, #ffcc33);
            color: #fff;
            border-radius: 30px;
            padding: 5px 15px;
            font-weight: 600;
            display: inline-block;
        }

        .account-sidebar .list-group-item {
            border: none;
            border-bottom: 1px solid var(--card-border);
            font-weight: 500;
            transition: var(--transition);
            color: var(--text-light);
            background: transparent;
        }

        .account-sidebar .list-group-item:hover {
            background-color: rgba(255,255,255,0.05);
            padding-left: 18px;
        }

        .account-sidebar .list-group-item.active {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: #fff;
            border: none;
        }

        .account-content .card {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-light);
        }

        .account-content .card-header {
            font-weight: 600;
            font-size: 1rem;
            padding: 12px 20px;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid var(--card-border);
        }

        .account-content .card-body {
            color: var(--text-light);
        }

        .account-history table th {
            background-color: rgba(255,255,255,0.05);
            border-bottom: 1px solid var(--card-border);
            color: var(--text-muted);
        }

        .account-history table td {
            color: var(--text-light);
            border-bottom: 1px solid var(--card-border);
        }

        .user-info {
            color: var(--text-light) !important;
        }

        .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .btn-outline-light {
            border-color: var(--card-border);
            color: var(--text-light);
            background: transparent;
        }

        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            color: var(--text-light);
        }

        .form-check-label {
            color: var(--text-light);
        }

        .btn-secondary {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-light);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
        }

        .form-label {
            color: var(--text-muted);
        }

        .text-secondary {
            color: var(--text-muted) !important;
        }

        .fs-5 {
            font-size: 1.15rem;
            color: var(--text-light);
        }

        @media (max-width:768px) {
            h1.text-gradient {
                font-size: 2rem !important;
            }
            .hover-card div[style*="height:180px"] {
                height:150px !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @if (session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 2000">
            <div id="successToast" class="toast align-items-center text-bg-success border-0 show shadow-lg"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toastEl = document.getElementById('successToast');
                // Support both Bootstrap 5 (class API) and Bootstrap 4 (jQuery plugin)
                try {
                    if (window.bootstrap && typeof window.bootstrap.Toast === 'function') {
                        const toast = new window.bootstrap.Toast(toastEl, { delay: 3000 });
                        toast.show();
                    } else if (window.jQuery) {
                        window.jQuery(toastEl).toast({ delay: 3000 });
                        window.jQuery(toastEl).toast('show');
                    }
                } catch (e) {
                    console.warn('Toast init warning:', e);
                }
            });
        </script>
    @endif

    {{-- ✅ Navbar --}}
    @include('client.layouts.navigation')

    {{-- ✅ Content --}}
    <main class="w-100 p-0 m-0">
        @yield('content')
    </main>

    {{-- ✅ Footer --}}
    @include('client.layouts.footer')

    @stack('scripts')

    {{-- ✅ Chatbot Popup --}}
    @include('components.chatbot-popup')

    {{-- Bootstrap JS Bundle v5 loaded in head --}}

</body>

</html>
