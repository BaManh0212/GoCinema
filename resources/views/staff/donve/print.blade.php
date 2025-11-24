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
            display: flex;
            justify-content: center;
            align-items: center;
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

    <!-- Vé cho từng ghế -->
    @foreach($donVe->chiTietVes as $index => $ct)
    <div class="ticket-container" style="page-break-after: always;">
        <!-- Header Section -->
        <div class="header">
            <h2>🎟️ VÉ XEM PHIM</h2>
            {{-- <div class="barcode">{{ $donVe->ma_don }}-{{ $ct->ghe->hang ?? '' }}{{ $ct->ghe->cot ?? '' }}</div> --}}
        </div>

        @php
            $qrData = [
                'ma_don' => $donVe->ma_don,
                'ghe' => ($ct->ghe->hang ?? '') . ($ct->ghe->cot ?? ''),
                'ngay_dat' => now()->format('Y-m-d H:i:s'),
            ];
            $qrCode = QrCode::size(150)->generate(json_encode($qrData));
        @endphp
        <div class="qr-section" style="text-align:center;">
            {!! $qrCode !!}
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

        <!-- Seat Information -->
        <div class="section">
            <h3>THÔNG TIN GHẾ NGỒI</h3>
            <div class="info-row">
                <div class="info-label">Ghế:</div>
                <div>{{ $ct->ghe->hang ?? '' }}{{ $ct->ghe->cot ?? '' }} - {{ ucfirst($ct->loai_ghe) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Đơn giá:</div>
                <div>{{ number_format($ct->gia, 0, ',', '.') }} đ</div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="section">
            <h3>THANH TOÁN</h3>
            <div class="info-row">
                <div class="info-label">Giá vé:</div>
                <div><strong>{{ number_format($ct->gia, 0, ',', '.') }} đ</strong></div>
            </div>
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
    @endforeach

    <!-- Vé cho từng combo -->
    @php $firstCombo = true; @endphp
    @foreach($donVe->combos as $combo)
        @for($i = 0; $i < $combo->pivot->so_luong; $i++)
        @if(!$firstCombo)
        <div style="page-break-before: always;"></div>
        @endif
        <div class="ticket-container" style="page-break-inside: avoid;">
        @php $firstCombo = false; @endphp
            <!-- Header Section -->
            <div class="header">
                <h2>🍿 VÉ COMBO</h2>
                <div class="barcode">{{ $donVe->ma_don }}-C{{ $combo->id }}-{{ $i + 1 }}</div>
            </div>

            <!-- Combo Information -->
            <div class="section">
                <h3>THÔNG TIN COMBO</h3>
                <div class="info-row">
                    <div class="info-label">Tên combo:</div>
                    <div><strong>{{ $combo->ten }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Mô tả:</div>
                    <div>{{ $combo->mo_ta }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Đơn giá:</div>
                    <div>{{ number_format($combo->pivot->gia, 0, ',', '.') }} đ</div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="section">
                <h3>THANH TOÁN</h3>
                <div class="info-row">
                    <div class="info-label">Giá combo:</div>
                    <div><strong>{{ number_format($combo->pivot->gia, 0, ',', '.') }} đ</strong></div>
                </div>
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
                    Vui lòng giữ vé để được nhận combo
                </div>
            </div>
        </div>
        @endfor
    @endforeach
</body>
</html>
