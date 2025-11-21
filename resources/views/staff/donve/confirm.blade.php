@extends('staff.layouts.staff')

@section('title', '✅ Xác Nhận Đặt Vé Tại Quầy')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-check-circle"></i> Đặt Vé Thành Công
            </h2>
            <small class="text-muted">Mã đơn: {{ $donDatVe->ma_don }}</small>
        </div>
        <div>
            <a href="{{ route('staff.donve.print', $donDatVe->id) }}" class="btn btn-primary rounded-pill me-2" target="_blank">
                <i class="bi bi-printer me-1"></i>
                In Vé
            </a>
            <a href="{{ route('staff.donve.create') }}" class="btn btn-success rounded-pill">
                <i class="bi bi-plus-circle me-1"></i>
                Đặt Vé Mới
            </a>
        </div>
    </div>

    {{-- ✅ Thông báo thành công --}}
    <div class="alert alert-success shadow-sm rounded-3 mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Đặt vé thành công!</strong> Vé đã được thanh toán bằng {{ $donDatVe->phuong_thuc_thanh_toan == 'tien_mat' ? 'tiền mặt' : 'quét mã QR' }} và sẵn sàng in.
    </div>

    <div class="row">
        {{-- 📋 Chi tiết đơn hàng --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Chi Tiết Đơn Hàng
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Thông tin suất chiếu --}}
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-film me-2"></i>
                            {{ $donDatVe->suatChieu->phim->tieu_de }}
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Thời gian:</strong> {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('H:i d/m/Y') }}</p>
                                <p class="mb-1"><strong>Rạp:</strong> {{ $donDatVe->suatChieu->phong->rap->ten }}</p>
                                <p class="mb-1"><strong>Phòng:</strong> {{ $donDatVe->suatChieu->phong->ten }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Mã đơn:</strong> <span class="badge bg-info">{{ $donDatVe->ma_don }}</span></p>
                                <p class="mb-1"><strong>Trạng thái:</strong> <span class="badge bg-success">Đã thanh toán</span></p>
                                <p class="mb-1"><strong>Thời gian đặt:</strong> {{ $donDatVe->created_at->format('H:i d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Danh sách ghế --}}
                    <div class="mb-4">
                        <h6 class="text-success mb-3">
                            <i class="bi bi-person-square me-2"></i>
                            Ghế Đã Đặt
                        </h6>
                        <div class="row">
                            @foreach($donDatVe->chiTietVes as $chiTietVe)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="card bg-light">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $chiTietVe->ghe->hang }}{{ $chiTietVe->ghe->cot }}</strong>
                                                    <span class="badge bg-secondary ms-1">{{ ucfirst($chiTietVe->ghe->loai) }}</span>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted">{{ number_format($chiTietVe->gia, 0, ',', '.') }}đ</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Combo --}}
                    @if($combos->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-warning mb-3">
                                <i class="bi bi-cup-straw me-2"></i>
                                Combo & Đồ Ăn
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tên combo</th>
                                            <th class="text-center">SL</th>
                                            <th class="text-end">Đơn giá</th>
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
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 💰 Tổng kết --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cash me-2"></i>
                        Tổng Kết Thanh Toán
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng tiền vé:</span>
                        <span class="fw-bold">{{ number_format($donDatVe->tong_tien - ($combos->sum(function($combo) { return $combo->gia * $combo->so_luong; })), 0, ',', '.') }}đ</span>
                    </div>

                    @if($combos->count() > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng tiền combo:</span>
                            <span class="fw-bold">{{ number_format($combos->sum(function($combo) { return $combo->gia * $combo->so_luong; }), 0, ',', '.') }}đ</span>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between fs-5 fw-bold text-primary">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($donDatVe->tong_tien, 0, ',', '.') }}đ</span>
                    </div>

                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Đã thanh toán bằng tiền mặt tại quầy
                        </small>
                    </div>
                </div>
            </div>

            {{-- 🔄 Hành động tiếp theo --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Hành Động Tiếp Theo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('staff.donve.print', $donDatVe->id) }}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-printer me-2"></i>
                            In Vé Lại
                        </a>
                        <a href="{{ route('staff.donve.show', $donDatVe->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-2"></i>
                            Xem Chi Tiết
                        </a>
                        <a href="{{ route('staff.donve.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>
                            Đặt Vé Mới
                        </a>
                        <a href="{{ route('staff.donve.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-2"></i>
                            Danh Sách Đơn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card {
    transition: transform 0.25s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
