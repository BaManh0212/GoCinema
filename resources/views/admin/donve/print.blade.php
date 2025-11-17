<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé - {{ $donVe->ma_don }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #555; }
    </style>
</head>
<body>
    <h2>🎟️ Vé xem phim - {{ $donVe->suatChieu->phim->tieu_de ?? 'N/A' }}</h2>

    <p><strong>Mã đơn:</strong> {{ $donVe->ma_don }}</p>
    <p><strong>Khách hàng:</strong> {{ $donVe->nguoiDung->ho_ten ?? 'N/A' }}</p>
    <p><strong>Phòng chiếu:</strong> {{ $donVe->suatChieu->phongChieu->ten ?? 'N/A' }}</p>
    <p><strong>Giờ chiếu:</strong> {{ \Carbon\Carbon::parse($donVe->suatChieu->gio_bat_dau)->format('H:i d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Ghế</th>
                <th>Loại ghế</th>
                <th>Giá vé</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donVe->chiTietVes as $ct)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ct->ghe->hang ?? '' }}{{ $ct->ghe->cot ?? '' }}</td>
                    <td>{{ ucfirst($ct->loai_ghe) }}</td>
                    <td>{{ number_format($ct->gia, 0, ',', '.') }} đ</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align:right; font-weight:bold; margin-top:10px;">
        Tổng cộng: {{ number_format($donVe->tong_tien, 0, ',', '.') }} đ
    </p>

    <div class="footer">
        Cảm ơn quý khách đã đặt vé tại <strong>GoCinema</strong>! 🎬<br>
        <small>Vé chỉ có hiệu lực trong thời gian suất chiếu đã đặt.</small>
    </div>
</body>
</html>
