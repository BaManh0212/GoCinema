<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Gocinema') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
      <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 0; }
        .container { max-width: 960px; margin: 0 auto; padding: 16px; }
        .btn { display: inline-block; padding: 8px 12px; background: #111; color: #fff; border-radius: 4px; text-decoration: none; }
        .btn + .btn { margin-left: 8px; }
        .card { border: 1px solid #e5e5e5; border-radius: 6px; padding: 12px; }
        .mb-2 { margin-bottom: 8px; } .mb-3 { margin-bottom: 12px; } .mb-4 { margin-bottom: 16px; }
        .grid { display: grid; gap: 8px; }
      </style>
    @endif
  </head>
  <body>
    <header style="border-bottom: 1px solid #eee">
      <div class="container" style="display:flex;align-items:center;justify-content:space-between">
        <a href="{{ route('movies.index', ['nguoi_dung_id' => request('nguoi_dung_id')]) }}" class="btn" style="background:#f53003">Phim</a>
        <div style="color:#666">Demo param: nguoi_dung_id={{ request('nguoi_dung_id') ?? 'null' }}</div>
      </div>
    </header>
    <main>
      @yield('content')
    </main>
  </body>
</html>
