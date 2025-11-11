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
            --primary-bg: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            --secondary-bg: #1a1a2e;
            --accent: #ff6b6b;
            --accent-hover: #ff5252;
            --text-light: #ffffff;
            --text-muted: #b0b3b8;
            --card-bg: rgba(255, 255, 255, 0.05);
            --card-border: rgba(255, 255, 255, 0.1);
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            padding-top: var(--body-top-padding, 0px);
            background: var(--primary-bg);
            background-attachment: fixed;
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
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
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            border: none;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-hover), #ff3838);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
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
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
            color: var(--text-light);
        }

        .text-muted {
            color: var(--text-muted) !important;
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
            background: var(--accent-hover);
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
