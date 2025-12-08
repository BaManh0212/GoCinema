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
            
            <h3>Thông tin đơn hàng</h3>
            <p><strong>Mã đơn hàng:</strong> {{ $donDatVe->ma_don }}</p>
            <p><strong>Phim:</strong> {{ $donDatVe->suatChieu->phim->tieu_de }}</p>
            <p><strong>Rạp:</strong> {{ $donDatVe->suatChieu->phong->rap->ten }}</p>
            <p><strong>Phòng:</strong> {{ $donDatVe->suatChieu->phong->ten }}</p>
            <p><strong>Ngày chiếu:</strong> {{ $donDatVe->suatChieu->gio_bat_dau->format('d/m/Y H:i') }}</p>
            <p><strong>Tổng tiền:</strong> {{ number_format($donDatVe->tong_tien, 0, ',', '.') }} VNĐ</p>
            <p><strong>Trạng thái:</strong> {{ $donDatVe->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chờ thanh toán' }}</p>
            
            <div class="qr-code">
                <p><strong>Mã QR của bạn:</strong></p>
                <div style="text-align: center; margin: 20px 0;">
                    @if(!empty($qrCode))
                        <img src="{{ $qrCode }}" alt="QR Code" style="max-width:260px; height:auto; display:block; margin:0 auto;">
                    @else
                        <p><em>Không thể tạo mã QR. Vui lòng liên hệ bộ phận hỗ trợ.</em></p>
                    @endif
                </div>
            </div>
            
            <p>Vui lòng xuất trình mã QR này khi đến rạp để nhận vé.</p>
        </div>
        
        <div class="footer">
            <p>Đây là email tự động, vui lòng không trả lời email này.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>