@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <a href="{{ route('movies.index') }}" class="text-blue-600">← Quay lại</a>
  <h1 class="text-2xl font-bold mb-4 mt-2">{{ $phim->tieu_de }}</h1>

  <h2 class="text-xl font-semibold mb-2">Chọn suất chiếu</h2>
  <ul class="space-y-2">
    @foreach($showtimes as $st)
      <li class="border p-3 rounded flex items-center justify-between">
        <div>
          <div>{{ $st->gio_bat_dau }} — {{ $st->gio_ket_thuc }}</div>
          <div class="text-sm text-gray-600">Phòng: {{ $st->phong->ten }} — Rạp: {{ $st->phong->rap->ten }}</div>
        </div>
        <a href="{{ route('seats.select', ['suatChieu' => $st->id, 'nguoi_dung_id' => request('nguoi_dung_id')]) }}" class="text-blue-600">Chọn ghế</a>
      </li>
    @endforeach
  </ul>
</div>
@endsection
