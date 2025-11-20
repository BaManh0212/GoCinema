@extends('client.layouts.app')

@section('title', 'Xác nhận đặt vé')

@section('content')
<style>
    .ticket-card {
        border-radius: 14px;
        overflow: hidden;
        background: #0f1b2a;
        color: #e6edf3;
        border: 1px solid #1f2d3d;
    }
    .ticket-header {
        background: linear-gradient(90deg, #15c27b, #0bbf9a);
        padding: 18px;
    }
    .ticket-header h4 {
        color: #fff !important;
        font-weight: 600;
    }
    .tag-seat {
        background: #223347;
        color: #9cc5ff;
        padding: 4px 8px;
        font-size: 13px;
        border-radius: 6px;
    }
    .info-section-title {
        color: #3fd2ff;
        font-weight: 600;
    }
    .custom-table th {
        background: #132235 !important;
        color: #9ec7ff !important;
        border: none !important;
    }
    .custom-table td {
        background: #0f1b2a !important;
        color: #d8e6f7 !important;
        border-color: #1f2d3d !important;
    }
    .badge-status {
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 8px;
    }
    .alert-success, .alert-info {
        border-radius: 12px;
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="ticket-card shadow">

                {{-- Header --}}
                <div class="ticket-header text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>Đặt vé thành công!
                    </h4>
                </div>

                <div class="card-body p-4">

                    {{-- Success message --}}
                    <div class="alert alert-success">
                        <h5 class="mb-1">Cảm ơn bạn đã đặt vé tại GoCinema!</h5>
                        <p class="mb-0">Mã đơn hàng: <strong>{{ $donDatVe->ma_don }}</strong></p>
                    </div>

                    {{-- Mã QR code --}}
                    <div class="text-center mb-4">
                        <div class="d-inline-block p-3 bg-white rounded-3">
                            @php
                                $qrData = [
                                    'ma_don' => $donDatVe->ma_don,
                                    'nguoi_dat' => $donDatVe->nguoiDung ? $donDatVe->nguoiDung->ho_ten : 'Khách hàng',
                                    'phim' => $donDatVe->suatChieu->phim->tieu_de,
                                    'rap' => $donDatVe->suatChieu->phong->rap->ten,
                                    'phong' => $donDatVe->suatChieu->phong->ten,
                                    'ngay_chieu' => \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('d/m/Y H:i'),
                                    'ghe' => $donDatVe->chiTietVes->map(fn($ve) => $ve->ghe->hang . $ve->ghe->cot . ' (' . $ve->ghe->loai . ')')->implode(', '),
                                    'tong_tien' => number_format($donDatVe->tong_tien, 0, ',', '.') . ' VNĐ',
                                    'trang_thai' => $donDatVe->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chờ thanh toán'
                                ];
                                $qrCode = QrCode::size(200)->generate(json_encode($qrData));
                            @endphp
                            {!! $qrCode !!}
                            <p class="mt-2 mb-0 text-muted small">Quét mã QR để xem thông tin vé</p>
                        </div>
                    </div>

                    {{-- Thông tin phim + vé --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="info-section-title">🎬 Thông tin phim</h6>
                            <p class="mb-1 fw-bold">{{ $donDatVe->suatChieu->phim->tieu_de }}</p>
                            <p class="mb-1">{{ $donDatVe->suatChieu->phong->rap->ten }}</p>
                            <p class="mb-1">{{ $donDatVe->suatChieu->phong->ten }}</p>
                            <p class="mb-0">
                                {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('l, d/m/Y H:i') }} -
                                {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_ket_thuc)->format('H:i') }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="info-section-title">🎟️ Thông tin vé</h6>
                            <p class="mb-1"><strong>Ghế đã đặt:</strong></p>
                            <div class="mb-2">
                                @php
                                    $totalTicketPrice = 0;
                                @endphp
                                @foreach($donDatVe->chiTietVes as $ve)
                                    @php
                                        $seatPrice = $donDatVe->suatChieu->gia_ve;
                                        if ($ve->ghe->loai === 'vip') {
                                            $seatPrice *= 1.5;
                                        } elseif ($ve->ghe->loai === 'doi') {
                                            $seatPrice *= 2;
                                        }
                                        $totalTicketPrice += $seatPrice;
                                        
                                        $seatType = '';
                                        if ($ve->ghe->loai === 'vip') {
                                            $seatType = ' (VIP)';
                                        } elseif ($ve->ghe->loai === 'doi') {
                                            $seatType = ' (Đôi)';
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="tag-seat me-1">{{ $ve->ghe->hang }}{{ $ve->ghe->cot }}{{ $seatType }}</span>
                                        <span>{{ number_format($seatPrice, 0, ',', '.') }}đ</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span><strong>Tổng tiền vé:</strong></span>
                                <span>{{ number_format($totalTicketPrice, 0, ',', '.') }}đ</span>
                            </div>

                            {{-- Mã giảm giá --}}
                            @if($donDatVe->maGiamGia)
                                <p class="text-success mb-1">
                                    <strong>Mã giảm giá:</strong> {{ $donDatVe->maGiamGia->ma }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Combo --}}
                    @if($combos->count() > 0)
                        <hr>
                        <h6 class="info-section-title">🍿 Combo & Đồ ăn</h6>
                        <div class="table-responsive">
                            <table class="table table-sm custom-table">
                                <thead>
                                <tr>
                                    <th>Tên combo</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end">Giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($combos as $combo)
                                    <tr>
                                        <td>{{ $combo->ten }}</td>
                                        <td class="text-center">{{ $combo->so_luong }}</td>
                                        <td class="text-end">{{ number_format($combo->gia, 0, ',', '.') }}đ</td>
                                        <td class="text-end">{{ number_format($combo->gia * $combo->so_luong, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <hr>

                    {{-- Tổng tiền --}}
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <span>Tổng tiền:</span>
                        <span class="text-danger">{{ number_format($donDatVe->tong_tien, 0, ',', '.') }}đ</span>
                    </div>

                    {{-- Trạng thái đơn --}}
                    <div class="mt-3">
                        <span class="badge-status
                            @if($donDatVe->trang_thai === 'da_thanh_toan') bg-success
                            @elseif($donDatVe->trang_thai === 'cho_thanh_toan') bg-warning text-dark
                            @else bg-secondary
                            @endif">
                            @if($donDatVe->trang_thai === 'da_thanh_toan')
                                Đã thanh toán
                            @elseif($donDatVe->trang_thai === 'cho_thanh_toan')
                                Chờ thanh toán
                            @else
                                {{ $donDatVe->trang_thai }}
                            @endif
                        </span>
                    </div>

                    {{-- Hướng dẫn --}}
                    <div class="alert alert-info mt-4">
                        <h6><i class="bi bi-info-circle me-2"></i>Hướng dẫn</h6>
                        <ul class="mb-0">
                            @if($donDatVe->trang_thai === 'da_thanh_toan')
                                <li>Vé đã được xác nhận và gửi qua email.</li>
                                <li>Vui lòng đến rạp trước 30 phút để check-in.</li>
                            @else
                                <li>Vui lòng thanh toán trong 15 phút để giữ chỗ.</li>
                                <li>Sau khi thanh toán, vé sẽ được gửi qua email.</li>
                                <li>Đến rạp trước 30 phút để check-in.</li>
                            @endif
                            <li>Mã đơn hàng: <strong>{{ $donDatVe->ma_don }}</strong></li>
                        </ul>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house me-1"></i>Về trang chủ
                        </a>
                        <a href="{{ route('account.bookings') }}" class="btn btn-outline-light">
                            <i class="bi bi-person me-1"></i>Lịch sử đặt vé
                        </a>

                        @if($donDatVe->trang_thai === 'cho_thanh_toan')
                            <form method="POST" action="{{ route('booking.cancel', $donDatVe->id) }}"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt vé không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Hủy vé
                                </button>
                            </form>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection
