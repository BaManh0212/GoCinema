@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-bold mb-4">Thanh toán</h1>

  <div class="mb-4">Phim: <strong>{{ $suatChieu->phim->tieu_de }}</strong></div>
  <div class="mb-4">Suất: {{ $suatChieu->gio_bat_dau }} — {{ $suatChieu->gio_ket_thuc }}</div>

  <h2 class="text-xl font-semibold mb-2">Ghế đã giữ</h2>
  <ul class="mb-4 list-disc pl-5">
    @foreach($seats as $seat)
      <li>{{ $seat->hang }}{{ $seat->cot }} ({{ $seat->loai }})</li>
    @endforeach
  </ul>

  <div class="mb-6">Tạm tính: <strong>{{ number_format($subtotal, 0, ',', '.') }} VND</strong></div>

  <form method="POST" action="{{ route('checkout.process', $suatChieu->id) }}">
    @csrf
    <input type="hidden" name="nguoi_dung_id" value="{{ request('nguoi_dung_id') }}" />
    <button class="px-4 py-2 bg-black text-white rounded" type="submit">Xác nhận và tạo đơn</button>
  </form>
</div>
@endsection
