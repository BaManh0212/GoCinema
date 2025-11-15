@extends('client.layouts.app')

@section('content')
<div class="container py-4">

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-4 text-primary">
                📄 Chi tiết đơn vé — {{ $order->ma_don }}
            </h3>

            {{-- Thông tin phim --}}
            <div class="mb-4">
                <h5 class="fw-bold mb-2">🎬 Thông tin phim</h5>
                <p class="mb-1"><strong>Phim:</strong> {{ $order->suatChieu->phim->ten }}</p>
                <p class="mb-1"><strong>Suất chiếu:</strong> {{ $order->suatChieu->bat_dau }}</p>
                <p class="mb-1"><strong>Phòng:</strong> {{ $order->suatChieu->phong->ten }}</p>
            </div>

            {{-- Combo --}}
            @if($order->combos->count() > 0)
            <div class="mb-4">
                <h5 class="fw-bold mb-2">🍿 Combo đã mua</h5>
                <ul>
                    @foreach($order->combos as $cb)
                    <li>
                        {{ $cb->ten }} × {{ $cb->pivot->so_luong }} — 
                        <strong class="text-primary">{{ number_format($cb->pivot->gia) }}đ</strong>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tổng tiền --}}
            <div class="mb-3">
                <h5 class="fw-bold mb-2">💵 Tổng tiền</h5>
                <p class="fs-4 text-danger fw-bold">{{ number_format($order->tong_tien) }} đ</p>
            </div>

            {{-- Trạng thái --}}
            <div class="mb-4">
                <h5 class="fw-bold mb-2">📌 Trạng thái</h5>

                <span class="badge fs-6 px-3 py-2 rounded-pill
                    @if($order->trang_thai=='da_thanh_toan') bg-success
                    @elseif($order->trang_thai=='cho_thanh_toan') bg-warning text-dark
                    @elseif($order->trang_thai=='da_huy') bg-danger
                    @else bg-primary
                    @endif
                ">
                    {{ strtoupper(str_replace('_',' ', $order->trang_thai)) }}
                </span>
            </div>

            {{-- Nút hành động --}}
            <div class="d-flex gap-2">

                <a href="{{ route('order.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Quay lại
                </a>

                @if($order->trang_thai == 'cho_thanh_toan')
                <a href="" 
                   class="btn btn-success rounded-pill px-4">
                    💳 Thanh toán ngay
                </a>
                @endif

            </div>

        </div>
    </div>

</div>
@endsection
