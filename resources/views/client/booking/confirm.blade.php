@extends('client.layouts.app')

@section('title', 'Xác nhận đặt vé')

@section('content')
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px 0;
    }
    .ticket-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .ticket-header {
        background: #1a237e;
        color: white;
        padding: 20px;
        text-align: center;
    }
    .ticket-body {
        padding: 25px;
    }
    .qr-section {
        text-align: center;
        padding: 20px 0;
        border-bottom: 2px dashed #e0e0e0;
        margin: 0 auto 20px;
        max-width: 80%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .movie-poster {
        width: 80px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }
    .info-row {
        display: flex;
        margin-bottom: 10px;
        align-items: flex-start;
    }
    .info-label {
        width: 120px;
        color: #757575;
        font-size: 14px;
    }
    .info-value {
        flex: 1;
        font-weight: 500;
    }
    .ticket-code {
        color: #00c853;
        font-weight: bold;
    }
    .price-section {
        background: #f5f5f5;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
    }
    .price-row {
        display: flex;
        justify-content: space-between;
        margin: 8px 0;
    }
    .total-price {
        font-size: 18px;
        font-weight: bold;
        color: #1a237e;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
    }
    .footer {
        text-align: center;
        padding: 20px;
        color: #757575;
        font-size: 14px;
    }
    .success-message {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }
</style>

<div class="ticket-container">
    <!-- Header -->
    <div class="ticket-header">
        <h2 style="margin: 0; font-size: 24px;">GO CINEMA</h2>
        <p style="margin: 5px 0 0; opacity: 0.9;">Vé xem phim</p>
    </div>

    <!-- Success Message -->
    <div class="success-message">
        <h3 style="margin: 0 0 5px 0;">Đặt vé thành công!</h3>
        <p style="margin: 0;">Mã đơn hàng: <strong>{{ $donDatVe->ma_don }}</strong></p>
    </div>

    <!-- QR Code Section -->
    <div style="width: 100%; display: flex; justify-content: center; margin: 20px 0;">
        <div class="qr-section">
            @php
                $qrData = [
                    'ma_don' => $donDatVe->ma_don,
                    'ngay_dat' => now()->format('Y-m-d H:i:s')
                ];
                $qrCode = QrCode::size(180)->generate(json_encode($qrData));
            @endphp
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                {!! $qrCode !!}
            </div>
            <p style="margin: 15px 0 0; color: #1a237e; font-weight: 500;">Quét mã để vào rạp</p>
        </div>
    </div>

    <!-- Movie Info -->
    <div class="ticket-body">
        <div style="display: flex; margin-bottom: 20px;">
            <div style="flex: 1;">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px; color: #1a237e;">
                    {{ $donDatVe->suatChieu->phim->tieu_de }} ({{ $donDatVe->suatChieu->phim->do_tuoi_gioi_han }}+)
                </div>
                <div style="color: #757575; margin-bottom: 5px;">
                    {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('d/m/Y') }}, 
                    {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('H:i') }} ~ 
                    {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_ket_thuc)->format('H:i') }}
                </div>
                <div style="color: #757575; margin-bottom: 5px;">
                    {{ $donDatVe->suatChieu->phong->rap->ten }} - {{ $donDatVe->suatChieu->phong->ten }}
                </div>
                <div style="margin-top: 10px;">
                    <span class="ticket-code">Mã lấy vé: {{ strtoupper(substr(md5($donDatVe->id), 0, 7)) }}</span>
                </div>
            </div>
            <div>
                <img src="{{ asset('storage/' . $donDatVe->suatChieu->phim->anh_poster) }}" alt="Poster phim" class="movie-poster">
            </div>
        </div>

        <!-- Seat Info -->
        <div class="info-row">
            <div class="info-label">Ghế</div>
            <div class="info-value" style="color: #1a237e">
                @foreach($donDatVe->chiTietVes as $index => $ve)
                    {{ $ve->ghe->hang }}{{ $ve->ghe->cot }}@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Phòng chiếu</div>
            <div class="info-value" style="color: #1a237e"> {{ $donDatVe->suatChieu->phong->ten }}</div>
        </div>

        <!-- Combo Info -->
        @if(isset($combos) && $combos->count() > 0)
        <div style="margin-top: 20px;">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #1a237e;">BẮP NƯỚC</h3>
            @foreach($combos as $combo)
                <div style="margin-bottom: 5px;">
                    <div style="font-weight: 500;">{{ $combo->ten }} (x{{ $combo->so_luong }})</div>
                    <div style="color: #757575; font-size: 14px;">{{ $combo->mo_ta ?? '' }}</div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Price Summary -->
        <div class="price-section">
            @php
                $ticketTotal = $donDatVe->chiTietVes->sum('gia');
                $comboTotal = isset($combos) ? $combos->sum(function($combo) {
                    return $combo->gia * $combo->so_luong;
                }) : 0;
                $total = $ticketTotal + $comboTotal;
            @endphp
            
            @if($donDatVe->chiTietVes->count() > 0)
            <div class="price-row" style="color: #1a237e">
                <span>Vé xem phim ({{ $donDatVe->chiTietVes->count() }})</span>
                <span>{{ number_format($ticketTotal, 0, ',', '.') }} đ</span>
            </div>
            @endif

            @if(isset($combos) && $combos->count() > 0)
            <div class="price-row">
                <span>Combo bắp nước</span>
                <span>{{ number_format($comboTotal, 0, ',', '.') }} đ</span>
            </div>
            @endif

            <div class="total-price">
                <span>Tổng tiền</span>
                <span>{{ number_format($total, 0, ',', '.') }} đ</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary me-2">
                <i class="bi bi-house"></i> Về trang chủ
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="margin: 0;">Cảm ơn quý khách đã sử dụng dịch vụ của GoCinema</p>
        <p style="margin: 5px 0 0;">Hotline: 1900 0000 - Website: gocinema.vn</p>
    </div>
</div>
@endsection
