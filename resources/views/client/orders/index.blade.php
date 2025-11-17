@extends('client.layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-4 text-primary">
        📄 Đơn vé của bạn
    </h2>

    @if($orders->isEmpty())
        <div class="alert alert-info shadow-sm rounded-pill px-4 py-3 text-center">
            Bạn chưa có đơn vé nào.
        </div>
    @else

        @foreach($orders as $order)
        <div class="card shadow-sm rounded-4 mb-4 border-0">
            <div class="card-body p-4">

                {{-- Mã đơn + Trạng thái --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold">
                        Mã đơn: <span class="text-primary fw-bold">{{ $order->ma_don }}</span>
                    </h5>

                    <span class="badge px-3 py-2 fs-6 rounded-pill
                        @if($order->trang_thai=='da_thanh_toan') bg-success
                        @elseif($order->trang_thai=='cho_thanh_toan') bg-warning text-dark
                        @elseif($order->trang_thai=='da_huy') bg-danger
                        @else bg-primary
                        @endif
                    ">
                        {{ strtoupper(str_replace('_',' ', $order->trang_thai)) }}
                    </span>
                </div>

                {{-- Thông tin phim --}}
                <div class="mb-3">
                    <p class="mb-1">
                        🎬 <strong>Phim:</strong> {{ $order->suatChieu->phim->ten }}
                    </p>
                    <p class="mb-1">
                        🕒 <strong>Suất chiếu:</strong> {{ $order->suatChieu->bat_dau }}
                    </p>
                    <p class="mb-1">
                        📍 <strong>Phòng:</strong> {{ $order->suatChieu->phong->ten }}
                    </p>
                </div>

               {{-- GHẾ ĐÃ ĐẶT --}}
{{-- GHẾ ĐÃ ĐẶT --}}
@if($order->gheDaDat->count() > 0)
<div class="mb-3">
    <strong>💺 Ghế đã đặt:</strong>
    <p class="mt-2 text-muted">
        @foreach($order->gheDaDat as $g)
            <span class="badge bg-secondary me-1">
                {{ $g->ghe->ten_ghe }}
                @if($g->loai == 'vip') (VIP)
                @elseif($g->loai == 'doi') (Đôi)
                @endif
            </span>
        @endforeach
    </p>
</div>
@endif

                {{-- COMBO --}}
                @if($order->combos->count() > 0)
                <div class="mb-3">
                    <strong>🍿 Combo đã mua:</strong>
                    <ul class="mt-2">
                        @foreach($order->combos as $cb)
                            <li class="text-muted">
                                {{ $cb->ten }} × {{ $cb->pivot->so_luong }} 
                                — <span class="text-primary fw-bold">{{ number_format($cb->pivot->gia) }}đ</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- TÍNH TỔNG TIỀN (GHẾ + COMBO) --}}
{{-- TÍNH TỔNG TIỀN GHẾ + COMBO --}}
@php
$totalSeats = $order->gheDaDat->sum(function($g){
    $giaVeThuong = $order->suatChieu->gia_ve;
    return match($g->loai) {
        'vip' => $giaVeThuong * 1.5,
        'doi' => $giaVeThuong * 2,
        default => $giaVeThuong,
    };
});

$totalCombos = $order->combos->sum(function($cb){
    return $cb->pivot->so_luong * $cb->pivot->gia;
});

$total = $totalSeats + $totalCombos;
@endphp

<div class="mb-3">
    💵 <strong>Tổng tiền:</strong>
    <span class="text-danger fw-bold fs-5">{{ number_format($total) }} đ</span>
</div>
                <div class="d-flex gap-2">

                    {{-- Xem chi tiết --}}
                    <a href="{{ route('order.show', $order->id) }}" 
                       class="btn btn-outline-primary rounded-pill px-4">
                        Xem chi tiết
                    </a>

                    {{-- Thanh toán --}}
                    @if($order->trang_thai == 'cho_thanh_toan')
                        <a href="" 
                           class="btn btn-success rounded-pill px-4">
                            💳 Thanh toán
                        </a>
                    @endif

                </div>

            </div>
        </div>
        @endforeach

    @endif

</div>
@endsection
