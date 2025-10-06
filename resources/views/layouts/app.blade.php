<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
  </head>
  <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] min-h-screen">
    <div class="max-w-7xl mx-auto p-6 lg:p-8">
      <header class="mb-6 flex items-center justify-between">
        <a href="/" class="text-sm underline underline-offset-4">Trang chủ</a>
        <nav class="flex items-center gap-4 text-sm">
          <a href="{{ route('dashboard') }}" class="underline underline-offset-4">Dashboard</a>
          @if (Route::has('login'))
            @auth
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="underline underline-offset-4">Đăng xuất</button>
              </form>
            @else
              <a href="{{ route('login') }}" class="underline underline-offset-4">Đăng nhập</a>
            @endauth
          @endif
        </nav>
      </header>

      @yield('content')
    </div>
  </body>
</html>
