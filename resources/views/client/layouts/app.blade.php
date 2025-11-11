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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    {{-- Bootstrap JS (moved to end of body, load local to avoid QUIC issues) --}}

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
    
    {{-- jQuery (required for local Bootstrap 4 bundle) --}}
    <script src="{{ asset('assets/admins/vendor/jquery/jquery.min.js') }}"></script>
    {{-- Bootstrap Bundle JS (Local v4.6) --}}
    <script src="{{ asset('assets/admins/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
