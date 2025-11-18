@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

# 🎉 Đặt vé thành công!

Cảm ơn bạn đã đặt vé tại **{{ config('app.name') }}**! Dưới đây là thông tin chi tiết đơn hàng của bạn:

## 🎬 Thông tin phim
- **Phim:** {{ $donDatVe->suatChieu->phim->tieu_de }}
- **Rạp:** {{ $donDatVe->suatChieu->phong->rap->ten }}
- **Phòng chiếu:** {{ $donDatVe->suatChieu->phong->ten }}
- **Suất chiếu:** {{ $donDatVe->suatChieu->gio_bat_dau->format('H:i d/m/Y') }}

## 🎟️ Thông tin đơn hàng
- **Mã đơn hàng:** {{ $donDatVe->ma_don }}
- **Ngày đặt:** {{ $donDatVe->created_at->format('d/m/Y H:i') }}
- **Trạng thái:** 
    @if($donDatVe->trang_thai === 'da_thanh_toan')
        <span style="color: #10B981; font-weight: 600;">Đã thanh toán</span>
    @else
        <span style="color: #F59E0B; font-weight: 600;">Chờ thanh toán</span>
    @endif

## 🪑 Ghế đã đặt
@php $seatCount = 0; @endphp
@foreach($donDatVe->chiTietVes as $ve)
    @php
        $seatType = '';
        $seatPrice = $donDatVe->suatChieu->gia_ve;
        
        if ($ve->ghe->loai === 'vip') {
            $seatType = ' (VIP)';
            $seatPrice *= 1.5;
        } elseif ($ve->ghe->loai === 'doi') {
            $seatType = ' (Đôi)';
            $seatPrice *= 2;
        }
        $seatCount++;
    @endphp
    - Ghế {{ $ve->ghe->hang }}{{ $ve->ghe->cot }}{{ $seatType }} - {{ number_format($seatPrice, 0, ',', '.') }}đ
@endforeach

## 💰 Tổng tiền
- **Tổng tiền vé ({{ $seatCount }} vé):** {{ number_format($totalTicketPrice, 0, ',', '.') }}đ

@if($donDatVe->maGiamGia)
- **Mã giảm giá:** {{ $donDatVe->maGiamGia->ma }} ({{ $donDatVe->maGiamGia->gia_tri_giam }}%)
@endif

- **Tổng thanh toán:** <span style="font-size: 1.2em; font-weight: bold; color: #3B82F6;">{{ number_format($donDatVe->tong_tien, 0, ',', '.') }}đ</span>

## 🎫 Mã QR của bạn
Vui lòng xuất trình mã QR này khi đến rạp để nhận vé:

<div style="text-align: center; margin: 20px 0;">
    <img src="data:image/png;base64,{{ $qrCode }}" alt="Mã QR đặt vé" style="max-width: 200px; border: 1px solid #e2e8f0; border-radius: 8px;">
</div>

## 📌 Lưu ý quan trọng
- Vui lòng đến rạp trước ít nhất 15 phút trước giờ chiếu để làm thủ tục.
- Mã QR này là duy nhất và chỉ sử dụng được một lần.
- Vui lòng không chia sẻ mã QR này cho người khác.

@component('mail::button', ['url' => route('client.booking.show', $donDatVe->ma_don), 'color' => 'primary'])
Xem chi tiết đơn hàng
@endcomponent

Trân trọng,<br>
**Đội ngũ {{ config('app.name') }}**

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
@endcomponent
@endslot
@endcomponent
