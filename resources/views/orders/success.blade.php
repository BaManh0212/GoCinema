@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-bold mb-4">Đặt vé thành công</h1>
  <div class="mb-3">
    <form method="POST" action="{{ route('orders.pay', ['donDatVe' => $donDatVe->id]) }}">
      @csrf
      <input type="hidden" name="nguoi_dung_id" value="{{ request('nguoi_dung_id') }}" />
      <button class="px-4 py-2 bg-black text-white rounded" type="submit">Thanh toán lại (demo)</button>
    </form>
  </div>
  <p class="mb-2">Mã đơn: <strong>{{ $donDatVe->ma_don }}</strong></p>
  <p class="mb-2">Phim: <strong>{{ $donDatVe->suatChieu->phim->tieu_de }}</strong></p>
  <p class="mb-2">Suất: {{ $donDatVe->suatChieu->gio_bat_dau }} — {{ $donDatVe->suatChieu->gio_ket_thuc }}</p>

  <h2 class="text-xl font-semibold mb-2">Vé</h2>
  <ul class="list-disc pl-5">
    @foreach($donDatVe->chiTietVes as $ct)
      <li>Ghế {{ $ct->ghe->hang }}{{ $ct->ghe->cot }} — {{ number_format($ct->gia, 0, ',', '.') }} VND</li>
    @endforeach
  </ul>

  <div class="mt-4">Tổng tiền: <strong>{{ number_format($donDatVe->tong_tien, 0, ',', '.') }} VND</strong></div>
</div>
@endsection
