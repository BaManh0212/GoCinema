@extends('admin.layouts.admin')

@section('title', '🎫 Chi tiết đơn đặt vé')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">🎫 Chi tiết đơn: {{ $donVe->ma_don }}</h2>
        <div class="d-flex gap-2">
            @php $canPrint = in_array($donVe->trang_thai, ['da_thanh_toan','da_checkin']); @endphp
            @if($canPrint)
                <a href="{{ route('admin.donve.print', $donVe->id) }}" class="btn btn-success">
                    <i class="bi bi-printer"></i> In vé
                </a>
            @else
                <button class="btn btn-outline-secondary" disabled title="Chỉ in khi đơn đã thanh toán hoặc đã check-in">
                    <i class="bi bi-printer"></i> In vé
                </button>
            @endif
            <a href="{{ route('admin.donve.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    {{-- Hiển thị flash messages / lỗi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Thông tin khách hàng -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold">👤 Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1"><strong>Họ tên:</strong> {{ $donVe->nguoiDung->ho_ten ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $donVe->nguoiDung->email ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Điện thoại:</strong> {{ $donVe->nguoiDung->so_dien_thoai ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Ngày đặt:</strong> {{ $donVe->created_at->format('H:i d/m/Y') }}</p>
                        <p class="mb-0">
                            <strong>Trạng thái:</strong>
                            @php
                                $statusColor = 'secondary';
                                if ($donVe->trang_thai == 'da_thanh_toan') $statusColor = 'success';
                                if ($donVe->trang_thai == 'da_huy') $statusColor = 'danger';
                                if ($donVe->trang_thai == 'da_checkin') $statusColor = 'info';
                            @endphp
                            <span class="badge bg-{{ $statusColor }} text-uppercase">
                                {{ str_replace('_', ' ', $donVe->trang_thai) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin suất chiếu -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold">🎬 Thông tin suất chiếu</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <img src="{{ $donVe->suatChieu->phim->hinh_anh ? asset('uploads/phim/' . $donVe->suatChieu->phim->hinh_anh) : asset('images/no-image.jpg') }}" 
                             alt="{{ $donVe->suatChieu->phim->tieu_de }}" 
                             class="img-thumbnail me-3" 
                             style="width: 80px; height: 120px; object-fit: cover;">
                        <div>
                            <h5 class="card-title mb-1">{{ $donVe->suatChieu->phim->tieu_de ?? 'N/A' }}</h5>
                            <p class="mb-1 text-muted small">
                                <i class="bi bi-clock"></i> {{ $donVe->suatChieu->phim->thoi_luong ?? 'N/A' }} phút
                            </p>
                            <p class="mb-1 text-muted small">
                                <i class="bi bi-tag"></i> {{ $donVe->suatChieu->phim->theLoais->pluck('ten')->join(', ') }}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Rạp:</strong> {{ $donVe->suatChieu->phongChieu->rap->ten ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Phòng:</strong> {{ $donVe->suatChieu->phongChieu->ten ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Ngày chiếu:</strong> {{ \Carbon\Carbon::parse($donVe->suatChieu->gio_bat_dau)->format('d/m/Y') }}</p>
                            <p class="mb-1"><strong>Giờ chiếu:</strong> {{ \Carbon\Carbon::parse($donVe->suatChieu->gio_bat_dau)->format('H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Danh sách vé -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold">🎟️ Danh sách vé</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Ghế</th>
                                    <th>Loại ghế</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                    <th class="text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $ticketTotal = 0;
                                    $ticketCount = 0;
                                @endphp
                                @foreach($donVe->chiTietVes as $ct)
                                    @php 
                                        $seatPrice = $ct->gia;
                                        $ticketTotal += $seatPrice;
                                        $ticketCount++;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $ct->ghe->hang ?? '' }}{{ $ct->ghe->cot ?? '' }}</td>
                                        <td>
                                            @php
                                                $seatType = 'Thường';
                                                $seatBadge = 'bg-secondary';
                                                if ($ct->loai_ghe === 'vip') {
                                                    $seatType = 'VIP';
                                                    $seatBadge = 'bg-warning text-dark';
                                                } elseif ($ct->loai_ghe === 'doi') {
                                                    $seatType = 'Đôi';
                                                    $seatBadge = 'bg-info';
                                                }
                                            @endphp
                                            <span class="badge {{ $seatBadge }}">{{ $seatType }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($ct->gia, 0, ',', '.') }} đ</td>
                                        <td class="text-end">{{ number_format($seatPrice, 0, ',', '.') }} đ</td>
                                        <td class="text-center">
                                            @php
                                                $ctColor = 'secondary';
                                                if ($ct->trang_thai == 'da_thanh_toan') $ctColor = 'success';
                                                if ($ct->trang_thai == 'da_huy') $ctColor = 'danger';
                                                if ($ct->trang_thai == 'da_su_dung') $ctColor = 'info';
                                            @endphp
                                            <span class="badge bg-{{ $ctColor }} text-uppercase">
                                                {{ str_replace('_', ' ', $ct->trang_thai) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Tổng tiền vé:</td>
                                    <td class="text-end fw-bold" colspan="2">{{ number_format($ticketTotal, 0, ',', '.') }} đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin thanh toán -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold">💰 Thông tin thanh toán</h5>
                </div>
                <div class="card-body">
                    @if($donVe->maGiamGia)
                    <div class="alert alert-info p-2 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-tag me-1"></i> 
                                <strong>Mã giảm giá:</strong> {{ $donVe->maGiamGia->ma }}
                            </div>
                            @php
                                $discount = $donVe->maGiamGia->loai_giam_gia === 'tien_mat' 
                                    ? $donVe->maGiamGia->gia_tri 
                                    : (($ticketTotal + ($donVe->combos->sum('pivot.gia') * $donVe->combos->sum('pivot.so_luong'))) * $donVe->maGiamGia->gia_tri / 100);
                            @endphp
                            <span class="badge bg-danger">
                                -{{ number_format($discount, 0, ',', '.') }} đ
                            </span>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng tiền vé ({{ $ticketCount }} vé):</span>
                            <span>{{ number_format($ticketTotal, 0, ',', '.') }} đ</span>
                        </div>

                        @if($donVe->combos->count() > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Combo & đồ ăn:</span>
                            <span>{{ number_format($donVe->combos->sum(function($combo) {
                                return $combo->pivot->gia * $combo->pivot->so_luong;
                            }), 0, ',', '.') }} đ</span>
                        </div>
                        @endif

                        @if($donVe->maGiamGia)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Giảm giá:</span>
                            <span>-{{ number_format($discount, 0, ',', '.') }} đ</span>
                        </div>
                        @endif

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tổng thanh toán:</h5>
                            <h4 class="mb-0 text-danger">{{ number_format($donVe->tong_tien, 0, ',', '.') }} đ</h4>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.donve.changeStatus', $donVe->id) }}" method="POST" class="mb-0">
                            @csrf
                            <div class="input-group">
                                <select name="trang_thai" class="form-select">
                                    <option value="cho_thanh_toan" {{ $donVe->trang_thai == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                                    <option value="da_thanh_toan" {{ $donVe->trang_thai == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="da_checkin" {{ $donVe->trang_thai == 'da_checkin' ? 'selected' : '' }} {{ $donVe->trang_thai != 'da_thanh_toan' ? 'disabled' : '' }}>Đã check-in</option>
                                    <option value="da_huy" {{ $donVe->trang_thai == 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($donVe->combos->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 fw-bold">🍿 Combo & đồ ăn</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tên combo</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $comboTotal = 0; @endphp
                        @foreach($donVe->combos as $combo)
                            @php 
                                $comboAmount = $combo->pivot->gia * $combo->pivot->so_luong;
                                $comboTotal += $comboAmount;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($combo->hinh_anh)
                                        <img src="{{ asset('uploads/combos/' . $combo->hinh_anh) }}" 
                                             alt="{{ $combo->ten }}" 
                                             class="img-thumbnail me-2" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $combo->ten }}</div>
                                            <small class="text-muted">{{ $combo->mo_ta }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">{{ number_format($combo->pivot->gia, 0, ',', '.') }} đ</td>
                                <td class="text-center">{{ $combo->pivot->so_luong }}</td>
                                <td class="text-end">{{ number_format($comboAmount, 0, ',', '.') }} đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Tổng tiền combo:</td>
                            <td class="text-end fw-bold">{{ number_format($comboTotal, 0, ',', '.') }} đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection