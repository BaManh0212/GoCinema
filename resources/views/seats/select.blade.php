@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <a href="{{ route('movies.show', $suatChieu->phim_id) }}" class="text-blue-600">← Quay lại</a>
  <h1 class="text-2xl font-bold mb-4 mt-2">Chọn ghế — {{ $suatChieu->phim->tieu_de }}</h1>

  <form method="POST" action="{{ route('seats.hold', $suatChieu->id) }}">
    @csrf
    <input type="hidden" name="nguoi_dung_id" value="{{ request('nguoi_dung_id') }}" />
    <div class="grid grid-cols-8 gap-2 mb-4">
      @foreach($seats as $seat)
        @php $disabled = $unavailableSeatIds->contains($seat->id); @endphp
        <label class="border rounded p-2 {{ $disabled ? 'bg-gray-200' : '' }}">
          <input type="checkbox" name="ghe_ids[]" value="{{ $seat->id }}" {{ $disabled ? 'disabled' : '' }} />
          {{ $seat->hang }}{{ $seat->cot }} ({{ $seat->loai }})
        </label>
      @endforeach
    </div>
    <button type="submit" class="px-4 py-2 bg-black text-white rounded">Giữ ghế</button>
  </form>
</div>
@endsection
