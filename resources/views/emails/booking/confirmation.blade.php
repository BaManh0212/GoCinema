<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đặt vé thành công</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4a90e2; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #777; }
        .qr-code { text-align: center; margin: 20px 0; }
        .section-title { background-color: #f0f0f0; padding: 10px; margin-top: 20px; font-weight: bold; }
        .seat-list { padding: 10px 20px; background-color: #fafafa; margin: 10px 0; border-left: 3px solid #4a90e2; }
        .combo-item { padding: 10px; background-color: #fff; margin: 5px 0; border-left: 3px solid #e74c3c; }
        .info-row { display: flex; justify-content: space-between; padding: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Xác nhận đặt vé thành công</h1>
        </div>
        
        <div class="content">
            <p>Xin chào {{ $donDatVe->nguoiDung->ho_ten ?? 'Quý khách' }},</p>
            <p>Cảm ơn bạn đã đặt vé xem phim tại {{ config('app.name') }}. Dưới đây là thông tin đặt vé của bạn:</p>
            
            <div class="section-title">📋 Thông tin đơn hàng</div>
            <p><strong>Mã đơn hàng:</strong> {{ $donDatVe->ma_don }}</p>
            <p><strong>Phim:</strong> {{ $donDatVe->suatChieu->phim->tieu_de }}</p>
            <p><strong>Rạp:</strong> {{ $donDatVe->suatChieu->phong->rap->ten }}</p>
            <p><strong>Phòng:</strong> {{ $donDatVe->suatChieu->phong->ten }}</p>
            <p><strong>Ngày chiếu:</strong> {{ $donDatVe->suatChieu->gio_bat_dau->format('d/m/Y H:i') }}</p>
            
            {{-- Ghế đã đặt --}}
            @if($donDatVe->chiTietVes && $donDatVe->chiTietVes->count() > 0)
            <div class="section-title">🪑 Ghế đã đặt</div>
            <div class="seat-list">
                @foreach($donDatVe->chiTietVes as $ve)
                    <div class="info-row">
                        <span><strong>Ghế:</strong> {{ $ve->ghe->hang ?? 'N/A' }}{{ $ve->ghe->cot ?? '' }}</span> 
                        <span><strong>Loại:</strong> {{ \App\Helpers\SeatHelper::getSeatTypeName($ve->loai_ghe) }}</span>
                        <span><strong>Giá:</strong> {{ number_format($ve->gia, 0, ',', '.') }} đ</span>
                    </div>
                @endforeach
            </div>
            @endif
            
            {{-- Combo đã đặt --}}
            @if($donDatVe->combos && $donDatVe->combos->count() > 0)
            <div class="section-title">🍿 Combo bắp nước</div>
            @foreach($donDatVe->combos as $combo)
                <div class="combo-item">
                    <div class="info-row">
                        <span><strong>{{ $combo->ten }}</strong></span>
                        <span>x{{ $combo->pivot->so_luong }}</span>
                    </div>
                    <p style="margin: 5px 0; font-size: 12px; color: #666;">{{ $combo->mo_ta ?? '' }}</p>
                    <div class="info-row">
                        <span>Đơn giá:</span>
                        <span><strong>{{ number_format($combo->pivot->gia * $combo->pivot->so_luong, 0, ',', '.') }} đ</strong></span>
                    </div>
                </div>
            @endforeach
            @endif
            
            {{-- Tổng tiền --}}
            <div class="section-title">💰 Thanh toán</div>
            <p style="font-size: 18px; color: #e74c3c;"><strong>Tổng tiền: {{ number_format($donDatVe->tong_tien, 0, ',', '.') }} VNĐ</strong></p>
            <p><strong>Trạng thái:</strong> <span style="color: {{ $donDatVe->trang_thai === 'da_thanh_toan' ? '#27ae60' : '#f39c12' }};">{{ $donDatVe->trang_thai === 'da_thanh_toan' ? '✓ Đã thanh toán' : '⏳ Chờ thanh toán' }}</span></p>
            
            {{-- Mã QR --}}
            <div class="qr-code">
                <div class="section-title">📱 Mã QR của bạn</div>
                <div style="text-align: center; margin: 20px 0; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                    @if(!empty($qrCode))
                        <img src="{{ $qrCode }}" alt="QR Code" style="max-width:260px; height:auto; display:block; margin:0 auto;">
                    @else
                        <p><em>Không thể tạo mã QR. Vui lòng liên hệ bộ phận hỗ trợ.</em></p>
                    @endif
                </div>
                <p>Vui lòng xuất trình mã QR này khi đến rạp để nhận vé.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Đây là email tự động, vui lòng không trả lời email này.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
            <p>Hotline: 1900 0000 - Website: gocinema.vn</p>
        </div>
    </div>
</body>
</html>