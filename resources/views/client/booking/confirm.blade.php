@extends('client.layouts.app')

@section('title', 'Xác nhận đặt vé')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Đặt vé thành công!
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5>Cảm ơn bạn đã đặt vé tại GoCinema!</h5>
                        <p class="mb-0">Mã đơn hàng: <strong>{{ $donDatVe->ma_don }}</strong></p>
                    </div>

                    {{-- Thông tin đơn hàng --}}
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Thông tin phim</h6>
                            <p class="mb-1"><strong>{{ $donDatVe->suatChieu->phim->tieu_de }}</strong></p>
                            <p class="mb-1">{{ $donDatVe->suatChieu->phong->rap->ten }}</p>
                            <p class="mb-1">{{ $donDatVe->suatChieu->phong->ten }}</p>
                            <p class="mb-1">
                                {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('l, d/m/Y H:i') }} -
                                {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_ket_thuc)->format('H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Thông tin vé</h6>
                            <p class="mb-1">
                                <strong>Ghế:</strong>
                                @foreach($donDatVe->chiTietVes as $ve)
                                    <span class="badge bg-secondary me-1">{{ $ve->ghe->hang }}{{ $ve->ghe->cot }}</span>
                                @endforeach
                            </p>
                            <p class="mb-1"><strong>Số lượng:</strong> {{ $donDatVe->chiTietVes->count() }} vé</p>
                            <p class="mb-1"><strong>Giá vé:</strong> {{ number_format($donDatVe->suatChieu->gia_ve, 0, ',', '.') }}đ/vé</p>
                            @if($donDatVe->maGiamGia)
                                <p class="mb-1 text-success">
                                    <strong>Mã giảm giá:</strong> {{ $donDatVe->maGiamGia->ma }}
                                    (-{{ number_format($donDatVe->tong_tien - ($donDatVe->chiTietVes->count() * $donDatVe->suatChieu->gia_ve + ($combos->sum('gia') ?? 0)), 0, ',', '.') }}đ)
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($combos->count() > 0)
                        <hr>
                        <h6 class="text-primary">Combo & Đồ ăn</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tên combo</th>
                                        <th class="text-center">Số lượng</th>
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

                    {{-- Trạng thái --}}
                    <div class="mt-3">
                        <span class="badge
                            @if($donDatVe->trang_thai === 'da_thanh_toan') bg-success
                            @elseif($donDatVe->trang_thai === 'cho_thanh_toan') bg-warning
                            @else bg-secondary
                            @endif">
                            @if($donDatVe->trang_thai === 'da_thanh_toan') Đã thanh toán
                            @elseif($donDatVe->trang_thai === 'cho_thanh_toan') Chờ thanh toán
                            @else {{ $donDatVe->trang_thai }}
                            @endif
                        </span>
                    </div>

                    {{-- Hướng dẫn --}}
                    <div class="alert alert-info mt-4">
                        <h6><i class="bi bi-info-circle me-2"></i>Hướng dẫn</h6>
                        <ul class="mb-0">
                            @if($donDatVe->trang_thai === 'da_thanh_toan')
                                <li>Thanh toán thành công! Vé của bạn đã được xác nhận.</li>
                                <li>Vé sẽ được gửi qua email trong vòng 5 phút.</li>
                                <li>Đến rạp trước giờ chiếu 30 phút để check-in.</li>
                            @else
                                <li>Vui lòng thanh toán trong vòng 15 phút để giữ chỗ.</li>
                                <li>Sau khi thanh toán thành công, vé sẽ được gửi qua email.</li>
                                <li>Đến rạp trước giờ chiếu 30 phút để check-in.</li>
                            @endif
                            <li>Mã đơn hàng: <strong>{{ $donDatVe->ma_don }}</strong></li>
                        </ul>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="bi bi-house me-1"></i>Về trang chủ
                        </a>
                        <a href="{{ route('account.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person me-1"></i>Xem lịch sử đặt vé
                        </a>
                        @if($donDatVe->trang_thai === 'cho_thanh_toan')
                            <form method="POST" action="{{ route('booking.cancel', $donDatVe->id) }}" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt vé này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Hủy đặt vé
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
