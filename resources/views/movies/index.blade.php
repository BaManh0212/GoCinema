@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-bold mb-4">Phim đang chiếu</h1>
  <ul class="space-y-2">
    @foreach($movies as $movie)
      <li class="border p-3 rounded flex items-center justify-between">
        <div>
          <div class="font-semibold">{{ $movie->tieu_de }}</div>
          <div class="text-sm text-gray-600">Số suất chiếu: {{ $movie->suat_chieus_count }}</div>
        </div>
        <a href="{{ route('movies.show', ['phim' => $movie->id, 'nguoi_dung_id' => request('nguoi_dung_id')]) }}" class="text-blue-600">Xem chi tiết</a>
      </li>
    @endforeach
  </ul>
</div>
@endsection
