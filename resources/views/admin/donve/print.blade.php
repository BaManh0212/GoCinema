<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé - {{ $donVe->ma_don }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            color: #333;
        }
        .ticket-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
        }
        h2 { 
            color: #e74c3c; 
            margin: 0 0 5px 0;
            font-size: 20px;
        }
        h3 { 
            margin: 15px 0 10px; 
            font-size: 16px;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .qr-section {
            text-align: center;
            margin: 15px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            border: 1px dashed #ddd;
        }
        .qr-code {
            display: inline-block;
            padding: 10px;
            background: white;
            border: 1px solid #eee;
            margin: 10px 0;
        }
        .barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 30px;
            letter-spacing: 3px;
            margin-top: 5px;
            display: block;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0;
            font-size: 12px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background: #f2f2f2; 
            text-align: center; 
            font-weight: bold;
        }
        .footer { 
            text-align: center; 
            margin-top: 20px; 
            font-size: 11px; 
            color: #777;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .section { 
            margin-bottom: 15px; 
        }
        .info-row { 
            margin: 5px 0;
            display: flex;
        }
        .info-label {
            font-weight: bold;
            min-width: 100px;
            color: #555;
        }
        .text-right { 
            text-align: right; 
        }
        .text-center { 
            text-align: center; 
        }
        .total-amount {
            font-size: 14px;
            font-weight: bold;
            color: #e74c3c;
        }
        .cinema-name {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        // Tính tổng tiền vé
        $ticketTotal = $donVe->chiTietVes->sum('gia');
        
        // Tính tổng tiền combo
        $comboTotal = $donVe->combos->sum(function($combo) {
            return $combo->pivot->gia * $combo->pivot->so_luong;
        });
    @endphp
    
    <div class="ticket-container">
        <!-- Header Section -->
        <div class="header">
            <h2>🎟️ VÉ XEM PHIM</h2>
            <div class="barcode">{{ $donVe->ma_don }}</div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section" style="text-align: center; margin: 20px 0; padding: 15px; background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 8px;">
            <h3 style="margin-top: 0; color: #2c3e50;">MÃ VÉ ĐIỆN TỬ</h3>
            @php
                // Tạo dữ liệu cho QR code
                $qrData = [
                    'ma_don' => $donVe->ma_don,
                    'phim' => $donVe->suatChieu->phim->tieu_de ?? 'N/A',
                    'ngay_chieu' => \Carbon\Carbon::parse($donVe->suatChieu->gio_bat_dau)->format('d/m/Y H:i'),
                    'phong' => $donVe->suatChieu->phongChieu->ten ?? 'N/A',
                    'rap' => $donVe->suatChieu->phongChieu->rap->ten ?? 'N/A',
                    'ghe' => $donVe->chiTietVes->map(fn($ve) => $ve->ghe->hang . $ve->ghe->cot)->implode(', '),
                    'khach_hang' => $donVe->nguoiDung->ho_ten ?? 'N/A',
                    'tong_tien' => number_format($donVe->tong_tien, 0, ',', '.') . ' VNĐ',
                    'ngay_tao' => now()->format('d/m/Y H:i:s')
                ];
                $qrContent = json_encode($qrData, JSON_UNESCAPED_UNICODE);
            @endphp
            
            <!-- Mã QR code sử dụng Google Charts API -->
            <div class="text-center mb-4">
                <div class="d-inline-block p-3 bg-white rounded-3">
                    <?php
                    $qrText = urlencode($donVe->ma_don);
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$qrText";
                    ?>
                    <img src="<?php echo $qrUrl; ?>" 
                         alt="Mã QR vé xem phim" 
                         style="width: 150px; height: 150px; display: block; margin: 0 auto;">
                    <p class="mt-2 mb-0 text-muted small">Mã đơn: {{ $donVe->ma_don }}</p>
                    <p class="text-muted small">Quét mã QR để check-in</p>
                </div>
            </div>
            
            <!-- Hiển thị mã vạch -->
            <div style="font-family: 'Libre Barcode 128', cursive; font-size: 36px; margin: 10px 0;">
                {{ $donVe->ma_don }}
            </div>
            
            <!-- Hướng dẫn -->
            <div style="margin-top: 10px; font-size: 12px; color: #6c757d;">
                <div>Quét mã QR để xác thực vé</div>
                <div>Mã đơn: <strong>{{ $donVe->ma_don }}</strong></div>
            </div>
        </div>

        <!-- Movie Information -->
        <div class="section">
            <h3>THÔNG TIN PHIM</h3>
            <div class="info-row">
                <div class="info-label">Phim:</div>
                <div><strong>{{ $donVe->suatChieu->phim->tieu_de ?? 'N/A' }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Rạp:</div>
                <div>{{ $donVe->suatChieu->phongChieu->rap->ten ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phòng:</div>
                <div>{{ $donVe->suatChieu->phongChieu->ten ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ngày chiếu:</div>
                <div>{{ \Carbon\Carbon::parse($donVe->suatChieu->gio_bat_dau)->format('l, d/m/Y') }}</div>
            </div>
            <div class="info-row">
    <div class="info-label">Giờ chiếu:</div>
    <div>
        {{ \Carbon\Carbon::parse($donVe->suatChieu->gio_bat_dau)->format('H:i') }} - 
        {{ \Carbon\Carbon::parse($donVe->suatChieu->gio_ket_thuc)->format('H:i') }}
        ({{ $donVe->suatChieu->phim->thoi_luong ?? 'N/A' }} phút)
    </div>
</div>
            </div>
        </div>

        <!-- Seat Information -->
        <div class="section">
            <h3>THÔNG TIN GHẾ NGỒI</h3>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Ghế</th>
                        <th>Loại ghế</th>
                        <th>Đơn giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donVe->chiTietVes as $index => $ct)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $ct->ghe->hang ?? '' }}{{ $ct->ghe->cot ?? '' }}</td>
                        <td>{{ ucfirst($ct->loai_ghe) }}</td>
                        <td class="text-right">{{ number_format($ct->gia, 0, ',', '.') }} đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($donVe->combos->count() > 0)
        <!-- Combo Information -->
        <div class="section">
            <h3>COMBO & ĐỒ ĂN</h3>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên combo</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donVe->combos as $index => $combo)
                    @php 
                        $comboAmount = $combo->pivot->gia * $combo->pivot->so_luong;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $combo->ten }}</td>
                        <td class="text-right">{{ number_format($combo->pivot->gia, 0, ',', '.') }} đ</td>
                        <td class="text-center">{{ $combo->pivot->so_luong }}</td>
                        <td class="text-right">{{ number_format($comboAmount, 0, ',', '.') }} đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Payment Information -->
        <div class="section">
            <h3>THANH TOÁN</h3>
            <table>
                <tr>
                    <td style="border: none; padding: 5px 0;">
                        @if($donVe->maGiamGia)
                            <div class="info-row">
                                <div class="info-label">Mã giảm giá:</div>
                                <div>{{ $donVe->maGiamGia->ma }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Giảm giá:</div>
                                <div>
                                    @if($donVe->maGiamGia->loai_giam_gia === 'tien_mat')
                                        {{ number_format($donVe->maGiamGia->gia_tri, 0, ',', '.') }} đ
                                    @else
                                        {{ $donVe->maGiamGia->gia_tri }}%
                                    @endif
                                </div>
                            </div>
                        @endif
                    </td>
                    <td style="border: none; padding: 5px 0; text-align: right;">
                        <div class="info-row" style="justify-content: flex-end;">
                            <div style="min-width: 100px; text-align: right;">
                                <strong>Tổng tiền:</strong>
                            </div>
                            <div style="width: 120px; text-align: right;">
                                {{ number_format($ticketTotal + $comboTotal, 0, ',', '.') }} đ
                            </div>
                        </div>
                        @if($donVe->maGiamGia)
                        @php
                            $discount = $donVe->maGiamGia->loai_giam_gia === 'tien_mat' 
                                ? $donVe->maGiamGia->gia_tri 
                                : (($ticketTotal + $comboTotal) * $donVe->maGiamGia->gia_tri / 100);
                        @endphp
                        <div class="info-row" style="justify-content: flex-end;">
                            <div style="min-width: 100px; text-align: right;">
                                <strong>Giảm giá:</strong>
                            </div>
                            <div style="width: 120px; text-align: right; color: #e74c3c;">
                                -{{ number_format($discount, 0, ',', '.') }} đ
                            </div>
                        </div>
                        @endif
                        <div class="info-row" style="justify-content: flex-end; margin-top: 5px; padding-top: 5px; border-top: 1px solid #eee;">
                            <div style="min-width: 100px; text-align: right;">
                                <strong>Thành tiền:</strong>
                            </div>
                            <div style="width: 120px; text-align: right; font-weight: bold; color: #e74c3c; font-size: 14px;">
                                {{ number_format($donVe->tong_tien, 0, ',', '.') }} đ
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>-----------------------------------</div>
            <div><strong class="cinema-name">GO CINEMA - RẠP CHIẾU PHIM CAO CẤP</strong></div>
            <div>Địa chỉ: Số 13, Trịnh Văn Bô, Hà Nội</div>
            <div>Hotline: 0359445669 - Website: gocinema.vn</div>
            <div style="margin-top: 10px; color: #e74c3c; font-weight: bold;">
                Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!
            </div>
            <div style="font-size: 10px; margin-top: 5px; color: #777;">
                Vui lòng giữ vé để được vào xem phim
            </div>
        </div>
    </div>
</body>
</html>