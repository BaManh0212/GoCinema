@extends('client.layouts.app')

@section('title', 'Đặt vé - ' . $suatChieu->phim->tieu_de)

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- Thông tin phim và suất chiếu --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-film me-2"></i>
                        {{ $suatChieu->phim->tieu_de }}
                    </h5>
                </div>
                <div class="card-body bg-dark rounded-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            @if($suatChieu->phim->anh_poster)
                                <img src="{{ asset('storage/' . $suatChieu->phim->anh_poster) }}"
                                     alt="{{ $suatChieu->phim->tieu_de }}"
                                     class="img-fluid rounded shadow"
                                     style="border: 2px solid rgba(255,255,255,0.1);">
                            @else
                                <div class="bg-dark bg-opacity-25 rounded d-flex align-items-center justify-content-center"
                                     style="height: 200px; border: 2px dashed rgba(255,255,255,0.1);">
                                    <i class="bi bi-image text-white-50 fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8 text-white">
                            <h5 class="fw-bold text-white mb-3">{{ $suatChieu->phim->tieu_de }}</h5>
                            
                            <div class="movie-info-item mb-2">
                                <i class="bi bi-building text-warning me-2"></i>
                                <span class="text-white-75">Rạp:</span>
                                <span class="ms-1 fw-medium">{{ $suatChieu->phong->rap->ten }}</span>
                            </div>
                            
                            <div class="movie-info-item mb-2">
                                <i class="bi bi-calendar-event text-info me-2"></i>
                                <span class="text-white-75">Ngày chiếu:</span>
                                <span class="ms-1 fw-medium">{{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('l, d/m/Y') }}</span>
                            </div>
                            
                            <div class="movie-info-item mb-2">
                                <i class="bi bi-clock text-success me-2"></i>
                                <span class="text-white-75">Giờ chiếu:</span>
                                <span class="ms-1 fw-medium">
                                    {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($suatChieu->gio_ket_thuc)->format('H:i') }}
                                </span>
                            </div>
                            
                            <div class="movie-info-item mb-2">
                                <i class="bi bi-geo-alt text-danger me-2"></i>
                                <span class="text-white-75">Phòng:</span>
                                <span class="ms-1 fw-medium">{{ $suatChieu->phong->ten }}</span>
                            </div>
                            
                            <div class="movie-info-item">
                                <i class="bi bi-ticket-perforated text-primary me-2"></i>
                                <span class="text-white-75">Giá vé:</span>
                                <span class="ms-1 fw-bold text-warning">{{ number_format($suatChieu->gia_ve, 0, ',', '.') }}đ</span>
                                <small class="text-white-50">/vé</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chọn ghế --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-square me-2"></i>
                        Chọn ghế (Tối đa 8 ghế liền kề cho 1 tài khoản)
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Chú thích --}}
                    <div class="mb-4 d-flex flex-wrap justify-content-center gap-3">
                        <div><span class="legend-box seat-vip"></span> Ghế VIP</div>
                        <div><span class="legend-box seat-doi"></span> Ghế đôi</div>
                        <div><span class="legend-box seat-thuong"></span> Ghế thường</div>
                        <div><span class="legend-box seat-da-thanh-toan"></span> Ghế đã đặt</div>
                        <div><span class="legend-box seat-giu-tam"></span> Ghế giữ tạm</div>
                        <div><span class="legend-box seat-bao-tri"></span> Ghế bảo trì</div>
                        <div><span class="legend-box seat-chon"></span> Ghế đã chọn</div>
                    </div>

                    {{-- Sơ đồ ghế --}}
                    <div class="seat-map p-4 border rounded bg-light" style="display: grid; grid-template-columns: 30px 1fr; gap: 10px; align-items: center; max-width:100%; --cols: {{ $suatChieu->phong->so_cot }}; --seat-size: clamp(28px, calc((100% - 120px) / var(--cols)), 45px);">
                        {{-- Màn hình --}}
                        <div class="screen mb-4" style="grid-column: 1 / -1; width: min({{ $suatChieu->phong->so_cot * 45 + 40 }}px, 100%); justify-self: center;">🎥 MÀN HÌNH CHIẾU</div>

                        {{-- Lối vào --}}
                        <div class="d-flex justify-content-start mb-3" style="grid-column: 1 / -1; padding-left: 50px;">
                            <div class="seat-preview seat-entrance">VÀO</div>
                        </div>

                        @php
                            $hangLetters = range('A', chr(ord('A') + $suatChieu->phong->so_hang - 1));
                        @endphp

                        @foreach ($hangLetters as $index => $hang)
                            {{-- Nhãn hàng --}}
                            <div class="fw-bold text-primary d-flex align-items-center justify-content-center" style="height: 45px;">{{ $hang }}</div>

                            {{-- Ghế trong hàng --}}
                            <div class="row-seats d-flex align-items-center mb-2">
                                @php
                                    $danhSachGhe = $ghes->get($hang, collect());
                                    $cot = 1;
                                    $hasDoubleInRow = false;
                                    for ($c = 1; $c <= $suatChieu->phong->so_cot; $c++) {
                                        $gheCheck = $danhSachGhe->firstWhere('cot', $c);
                                        if ($gheCheck && $gheCheck->loai == 'doi') {
                                            $hasDoubleInRow = true;
                                            break;
                                        }
                                    }
                                @endphp
                                @while ($cot <= $suatChieu->phong->so_cot)
                                    @php
                                        $ghe = $danhSachGhe->firstWhere('cot', $cot);
                                        $currentLoai = $ghe ? $ghe->loai : 'thuong';
                                        $isDouble = $currentLoai == 'doi';
                                        $isLastSeat = $cot == $suatChieu->phong->so_cot;
                                        $skipLastIfDoubleAndOdd = $isLastSeat && $hasDoubleInRow && $suatChieu->phong->so_cot % 2 == 1;
                                        $trangthai = $ghe ? ($gheStatuses[$ghe->id] ?? 'hoat_dong') : 'hoat_dong';
                                        $disabled = false;

                                        // ✅ FIX: Kiểm tra ghế đã đặt/thanh toán/check-in TRƯỚC để đảm bảo không bị override
                                        // Ghế đã đặt/thanh toán/check-in KHÔNG BAO GIỜ được chọn lại, kể cả là của user hiện tại
                                        if ($ghe && in_array($ghe->id, $gheDaDat)) {
                                            $trangthai = 'da_dat';
                                            $disabled = true;
                                            $isMyHeldSeat = false; // Force reset để đảm bảo không bị override
                                        } else {
                                            // Kiểm tra xem ghế có phải của user hiện tại đang giữ không (chỉ khi chưa đặt)
                                            $isMyHeldSeat = $ghe && auth()->check() && isset($myHeldSeats) && in_array($ghe->id, $myHeldSeats);
                                            
                                            if ($ghe && in_array($ghe->id, $gheChoThanhToan)) {
                                                $trangthai = 'cho_thanh_toan';
                                                $disabled = true;
                                            } elseif ($ghe && in_array($ghe->id, $giuTamIds) && !$isMyHeldSeat) {
                                                // Ghế đang được user khác giữ tạm -> disabled
                                                $trangthai = 'giu_tam';
                                                $disabled = true;
                                            } elseif ($ghe && in_array($ghe->id, $giuTamIds) && $isMyHeldSeat) {
                                                // Ghế đang được user hiện tại giữ -> có thể bỏ chọn
                                                $trangthai = 'giu_tam';
                                                $disabled = false; // Cho phép bỏ chọn
                                            } elseif ($trangthai === 'bao_tri') {
                                                $disabled = true;
                                            } elseif ($trangthai === 'vo_hieu_hoa') {
                                                $disabled = true;
                                            }
                                        }

                                        $classes = 'seat seat-' . $currentLoai;
                                        if ($trangthai === 'bao_tri') {
                                            $classes = 'seat seat-bao-tri disabled';
                                        } elseif ($trangthai === 'da_dat') {
                                            $classes = 'seat seat-da-thanh-toan disabled';
                                        } elseif ($trangthai === 'cho_thanh_toan') {
                                            $classes = 'seat seat-giu-tam disabled';
                                        } elseif ($trangthai === 'giu_tam' && $isMyHeldSeat) {
                                            // Ghế của user hiện tại đang giữ -> hiển thị như đã chọn, không disabled
                                            $classes = 'seat seat-giu-tam seat-chon';
                                        } elseif ($trangthai === 'giu_tam' && !$isMyHeldSeat) {
                                            // Ghế của user khác đang giữ -> disabled
                                            $classes = 'seat seat-giu-tam disabled';
                                        } elseif ($trangthai === 'vo_hieu_hoa') {
                                            $classes = 'seat seat-vo-hieu-hoa disabled';
                                        }

                                        if ($isDouble) $classes .= ' double-seat';
                                    @endphp

                                    @if(!$skipLastIfDoubleAndOdd)
                                    <button type="button" class="seat {{ $classes }} {{ $disabled ? 'disabled' : '' }}"
                                            data-ghe-id="{{ $ghe ? $ghe->id : '' }}"
                                            data-hang="{{ $hang }}"
                                            data-cot="{{ $cot }}"
                                            data-loai="{{ $currentLoai }}"
                                            data-trangthai="{{ $trangthai }}"
                                            {{ $disabled ? 'disabled="disabled" style="pointer-events: none;"' : '' }}
                                            {{ $isMyHeldSeat ? 'data-my-held="true"' : '' }}>
                                        @if($isDouble)
                                            💑
                                            @php $cot += 1; @endphp {{-- Bỏ qua ghế tiếp theo --}}
                                        @else
                                            {{ $hang }}{{ $cot }}
                                        @endif
                                    </button>
                                    @endif

                                    @php $cot++; @endphp
                                @endwhile
                            </div>

                            @if ($hang == chr(ord('A') + ceil($suatChieu->phong->so_hang / 2) - 1))
                                <div style="grid-column: 1 / -1; height: 20px;"></div>
                            @endif
                        @endforeach

                        {{-- Lối ra --}}
                        <div class="d-flex justify-content-end mt-3" style="grid-column: 1 / -1; padding-right: 50px;">
                            <div class="seat-preview seat-exit">RA</div>
                        </div>
                    </div>

                    {{-- Ghế đã chọn --}}
                    <div class="mt-4">
                        <h6>Ghế đã chọn: <span id="selected-seats">Chưa chọn ghế nào</span></h6>
                        <div id="selected-seats-list" class="d-flex flex-wrap gap-2"></div>

                        {{-- Countdown timer --}}
                        <div id="hold-timer-container" class="mt-3 d-none">
                            <div class="alert alert-warning py-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock-fill me-2"></i>
                                    <div>
                                        <small class="text-balck">Thời gian giữ ghế:</small>
                                        <div id="hold-timer" class="fw-bold text-warning">10:00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thanh toán --}}
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Thanh toán
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Vé --}}
                    <div class="mb-3">
                        <h6>Vé phim</h6>
                        <div id="ticket-summary">
                            <p class="text-muted">Chưa chọn ghế</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tổng vé:</span>
                            <span id="ticket-total">0đ</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Combo --}}
                    <div class="mb-3">
                        <h6>Combo & Đồ ăn</h6>
                        <div id="combo-list"></div>
                        @if($combos->count() > 0)
                            <div class="mt-2">
                                @foreach($combos as $combo)
                                    @if($combo->so_luong > 0) {{-- Chỉ hiển thị combo còn hàng --}}
                                    <div class="card mb-2" data-combo-id="{{ $combo->id }}">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">{{ $combo->ten }}</h6>
                                                    <small class="text-muted">{{ number_format($combo->gia, 0, ',', '.') }}đ</small>
                                                    <div class="text-success">
                                                        <small>Còn lại: {{ $combo->so_luong }} cái</small>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-decrease" 
                                                            data-combo-id="{{ $combo->id }}" 
                                                            {{ $combo->so_luong <= 0 ? 'disabled' : '' }}>
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center mx-1 hide-spinner" 
                                                           id="combo-{{ $combo->id }}-qty" 
                                                           value="0" 
                                                           min="0" 
                                                           max="{{ $combo->so_luong }}" 
                                                           style="width: 50px; -moz-appearance: textfield;"
                                                           onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                           data-combo-id="{{ $combo->id }}"
                                                           {{ $combo->so_luong <= 0 ? 'disabled' : '' }}>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary btn-increase" 
                                                            data-combo-id="{{ $combo->id }}"
                                                            {{ $combo->so_luong <= 0 ? 'disabled' : '' }}>
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Hiện không có combo nào khả dụng.</p>
                        @endif
                        <div class="d-flex justify-content-between mt-2">
                            <span>Tổng combo:</span>
                            <span id="combo-total">0đ</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Mã giảm giá --}}
                    <div class="mb-3">
                        <h6>Mã giảm giá</h6>
                        
                        {{-- Danh sách voucher khả dụng --}}
                        <div class="mb-2">
                            <label class="form-label small text-muted">Chọn từ mã giảm giá có sẵn:</label>
                            <select class="form-select mb-2" id="voucher-select">
                                <option value="" selected>-- Chọn mã giảm giá --</option>
                                @if(isset($availableVouchers) && count($availableVouchers) > 0)
                                    @foreach($availableVouchers as $voucher)
                                        <option 
                                            value="{{ $voucher->ma }}" 
                                            data-min="{{ $voucher->gia_tri_don_hang_toi_thieu }}"
                                            data-type="{{ $voucher->loai }}"
                                            data-value="{{ $voucher->gia_tri }}"
                                            data-max="{{ $voucher->giam_toi_da }}">
                                            {{ $voucher->ma }} - Giảm 
                                            @if($voucher->loai == 'phan_tram')
                                                {{ $voucher->gia_tri }}%
                                                @if($voucher->giam_toi_da)
                                                    (Tối đa {{ number_format($voucher->giam_toi_da, 0, ',', '.') }}đ)
                                                @endif
                                            @else
                                                {{ number_format($voucher->gia_tri, 0, ',', '.') }}đ
                                            @endif
                                            @if($voucher->gia_tri_don_hang_toi_thieu)
                                                (Đơn tối thiểu {{ number_format($voucher->gia_tri_don_hang_toi_thieu, 0, ',', '.') }}đ)
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2 small">hoặc</span>
                            <hr class="flex-grow-1 my-0">
                        </div>
                        
                        <div class="input-group">
                            <input type="text" class="form-control" id="voucher-code" placeholder="Nhập mã giảm giá">
                            <button class="btn btn-outline-primary" type="button" id="apply-voucher">Áp dụng</button>
                        </div>
                        <div id="voucher-message"></div>
                        
                        {{-- Thông tin voucher đã áp dụng --}}
                    <div id="applied-voucher-info" class="mt-2 d-none">
                        <div class="alert alert-success p-2 mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span id="applied-voucher-text"></span>
                                <button type="button" class="btn-close" id="remove-voucher" aria-label="Xóa mã giảm giá"></button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Số tiền giảm giá --}}
                    <div class="d-flex justify-content-between fs-6 fw-bold text-success">
                        <span>Giảm giá:</span>
                        <span id="discount-amount">0đ</span>
                    </div>

                    <hr>

                    {{-- Tổng cộng --}}
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <span>Tổng cộng:</span>
                        <span id="grand-total">0đ</span>
                    </div>

                    {{-- Form đặt vé --}}
                    <form id="booking-form" method="POST" action="{{ route('booking.store') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="suat_chieu_id" value="{{ $suatChieu->id }}">
                        <input type="hidden" name="ghe_ids" id="selected-seats-input" value="">
                        <input type="hidden" name="ma_giam_gia" id="ma-giam-gia-input" value="">
                        <input type="hidden" name="voucher_nd_id" id="voucher-nd-id" value="">
                        <input type="hidden" name="combos" id="combos-input" value="">
                        
                        <button type="button" id="book-btn" class="btn btn-danger w-100" disabled>
                        <i class="bi bi-ticket-perforated-fill me-2"></i>
                        Đặt vé
                    </button>

                    @if(!auth()->check())
                        <p class="text-muted mt-2 mb-0 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đặt vé
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thông báo lỗi -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Thông báo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-3" style="font-size: 2rem;"></i>
                    <p id="errorMessage" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Ẩn mũi tên lên/xuống trong input number */
.hide-spinner::-webkit-outer-spin-button,
.hide-spinner::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
.hide-spinner {
    -moz-appearance: textfield;
}
</style>

{{-- Modal xác nhận --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận đặt vé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn đặt vé với thông tin sau?</p>
                <div id="confirm-details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-book">Xác nhận đặt vé</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal hết thời gian giữ ghế --}}
<div class="modal fade" id="holdExpiredModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Hết thời gian giữ ghế</h5>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-clock-fill text-warning" style="font-size: 3rem;"></i>
                <p class="mt-3">Thời gian giữ ghế tạm thời đã hết. Vui lòng chọn lại ghế.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="location.reload()">Chọn lại ghế</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.seat {
    width: var(--seat-size, 45px);
    height: var(--seat-size, 45px);
    margin: 4px;
    border-radius: 8px;
    border: 1px solid #ccc;
    text-align: center;
    line-height: var(--seat-size, 45px);
    font-size: calc(var(--seat-size, 45px) * 0.26);
    cursor: pointer;
    font-weight: 500;
    color: #222;
    user-select: none;
    transition: all 0.2s;
}
.double-seat {
    width: calc(var(--seat-size,45px) * 2 + 8px) !important; /* two seats side-by-side plus margins */
}
.seat-vip { background-color: #FFD700; }
.seat-doi { background-color: #98FB98; }
.seat-thuong { background-color: #87CEFA; }
.seat-dat { background-color: #FF6347 !important; cursor: not-allowed; }
.seat-da-thanh-toan { background-color: #DC3545 !important; cursor: not-allowed; pointer-events: none; }
.seat-giu-tam { background-color: #FFA500 !important; cursor: not-allowed; pointer-events: none; }
.seat-bao-tri { background-color: #d1d5db !important; cursor: not-allowed; pointer-events: none; }
.seat-vo-hieu-hoa { background-color: #6B7280 !important; cursor: not-allowed; pointer-events: none; }
.seat-chon { background-color: #28a745 !important; color: white; }
.seat.disabled { cursor: not-allowed !important; pointer-events: none; }

.seat-preview {
    width: 35px;
    height: 35px;
    margin: 3px;
    border-radius: 6px;
    border: 2px solid #ddd;
    text-align: center;
    line-height: 35px;
    font-size: 11px;
    font-weight: 600;
    color: #333;
    display: inline-block;
}

.seat-entrance {
    background: #ff6b6b;
    color: white;
    border-color: #ff6b6b;
}
.seat-exit {
    background: #4ecdc4;
    color: white;
    border-color: #4ecdc4;
}

.legend-box {
    display: inline-block;
    width: 25px; height: 25px;
    margin-right: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    vertical-align: middle;
}

.screen {
    background-color: #222;
    color: #fff;
    text-align: center;
    font-weight: 600;
    padding: 10px 0;
    border-radius: 5px;
    width: 60%;
    margin: 0 auto;
    letter-spacing: 2px;
}

/* Prevent overflow: allow horizontal scrolling for wide rows and keep seats from shrinking */
.seat-map { overflow-x: auto; }
.row-seats { overflow-x: auto; -webkit-overflow-scrolling: touch; display: flex; flex-wrap: nowrap; align-items: center; }
.row-seats .seat { flex: 0 0 auto; }

.row-seats::-webkit-scrollbar { height: 8px; }
.row-seats::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.08); border-radius: 4px; }

.seat:not(.disabled):hover {
    transform: scale(1.06);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.selected-seat-badge {
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}
p{
    color: black;
}
h5{
    color: black;
}

.voucher-option {
    display: block;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    margin-bottom: 5px;
    border-radius: 4px;
    font-size: 12px;
    color: #000;
}

/* Modal text colors */
#confirmModal .modal-content {
    color: #fbf6f6ff;
}

#confirmModal .modal-title,
#confirmModal .modal-body,
#confirmModal .modal-footer {
    color: #000;
}

/* Voucher section text colors */
#voucher-select,
#voucher-code,
#voucher-message,
#applied-voucher-info,
#applied-voucher-code,
#applied-voucher-desc,
#applied-voucher-info .alert,
#applied-voucher-info .alert * {
    color: #000 !important;
}

/* Form labels and text */
.form-label,
.small,
.text-muted,
.form-select,
.form-control {
    color: #f7f5f5ff !important;
}

/* Make sure dropdown options are also black */
option {
    color: #000;
    background-color: #fff;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let selectedSeats = [];
let comboQuantities = {};
let voucherDiscount = 0;
let appliedVoucherNdId = null; // id voucher_nguoi_dung nếu áp dụng VCxxxxxx
let appliedCode = null; // lưu lại mã đã áp dụng để gửi lên server
let baseTicketPrice = {{ $suatChieu->gia_ve }};
let combosData = @json($combos);

// Ghế đã giữ tạm của user hiện tại (để restore khi load trang)
let myHeldSeats = @json($myHeldSeats ?? []);

// Seat hold timer variables
let holdTimerInterval = null;
let holdExpirationTime = null;
let holdDuration = 10 * 60 * 1000; // 10 minutes in milliseconds

// Initialize combo quantities
combosData.forEach(combo => {
    comboQuantities[combo.id] = 0;
});

// Handle plus button click
$(document).on('click', '.btn-increase', function() {
    const comboId = $(this).data('combo-id');
    const input = $(`#combo-${comboId}-qty`);
    const max = parseInt(input.attr('max'));
    let value = parseInt(input.val()) || 0;
    
    if (value < max) {
        value++;
        input.val(value);
        comboQuantities[comboId] = value;
        updateComboSummary();
    }
    
    // Enable/disable buttons
    $(`.btn-decrease[data-combo-id="${comboId}"]`).prop('disabled', value <= 0);
    $(this).prop('disabled', value >= max);
});

// Handle minus button click
$(document).on('click', '.btn-decrease', function() {
    const comboId = $(this).data('combo-id');
    const input = $(`#combo-${comboId}-qty`);
    const max = parseInt(input.attr('max'));
    let value = parseInt(input.val()) || 0;
    
    if (value > 0) {
        value--;
        input.val(value);
        comboQuantities[comboId] = value;
        updateComboSummary();
    }
    
    // Enable/disable buttons
    $(this).prop('disabled', value <= 0);
    $(`.btn-increase[data-combo-id="${comboId}"]`).prop('disabled', value >= max);
});

// Handle direct input
$(document).on('change', 'input[type="number"].hide-spinner', function() {
    const comboId = $(this).data('combo-id');
    const max = parseInt($(this).attr('max'));
    let value = parseInt($(this).val()) || 0;
    
    // Ensure value is within bounds
    if (value < 0) value = 0;
    if (value > max) value = max;
    
    $(this).val(value);
    comboQuantities[comboId] = value;
    updateComboSummary();
    
    // Enable/disable buttons
    $(`.btn-decrease[data-combo-id="${comboId}"]`).prop('disabled', value <= 0);
    $(`.btn-increase[data-combo-id="${comboId}"]`).prop('disabled', value >= max);
});

function showError(message) {
    $('#voucher-message').html(`<div class="alert alert-danger p-2">${message}</div>`);
    console.error('Lỗi mã giảm giá:', message);
}

function updateVoucherUI(response, code) {
    $('#applied-voucher-code').text(code);
    let discountText = '';
    if (response.discount_type === 'phan_tram') {
        discountText = `Giảm ${response.discount_value}%`;
        if (response.max_discount) {
            discountText += ` (Tối đa ${formatCurrency(response.max_discount)})`;
        }
    } else {
        discountText = `Giảm ${formatCurrency(response.discount_value)}`;
    }

    let minOrderText = response.min_order_value ? ` • Đơn tối thiểu ${formatCurrency(response.min_order_value)}` : '';
    $('#applied-voucher-desc').html(`<br><small>${discountText}${minOrderText}</small>`);
    $('#applied-voucher-info').removeClass('d-none');
    $('#voucher-message').html('');
}

// Cập nhật lại hàm applyVoucher
function applyVoucher(code) {
    if (!code) {
        showError('Vui lòng nhập hoặc chọn mã giảm giá');
        return;
    }

    // Hiển thị trạng thái đang xử lý
    $('#voucher-message').html('<div class="alert alert-info p-2">Đang kiểm tra mã giảm giá...</div>');

    // Gửi AJAX để kiểm tra mã giảm giá
    $.ajax({
        url: '{{ route("booking.check-voucher") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            code: code,
            suat_chieu_id: {{ $suatChieu->id }},
            ghe_ids: selectedSeats,
            combo_items: getComboItems()
        },
        success: function(response) {
            console.log('Phản hồi từ server:', response); // Thêm dòng này để debug
            if (response.success) {
                // Xử lý khi áp dụng mã thành công
                voucherDiscount = response.discount;
                appliedCode = code;
                
                // Cập nhật giao diện
                updateVoucherUI(response, code);
                updateTotals();
            } else {
                resetVoucher();
                showError(response.message || 'Mã giảm giá không hợp lệ hoặc không áp dụng được');
            }
        },
        error: function(xhr, status, error) {
            console.error('Lỗi AJAX:', {xhr, status, error}); // Thêm log lỗi
            let errorMessage = 'Có lỗi xảy ra khi kiểm tra mã giảm giá';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showError(errorMessage);
        }
    });
}

function updateComboSummary() {
    let total = 0;
    let comboList = [];
    
    combosData.forEach(combo => {
        const qty = comboQuantities[combo.id] || 0;
        if (qty > 0) {
            total += combo.gia * qty;
            comboList.push({
                id: combo.id,
                name: combo.ten,
                price: combo.gia,
                quantity: qty,
                total: combo.gia * qty
            });
        }
    });
    
    // Update combo list display
    let comboHtml = '';
    comboList.forEach(combo => {
        comboHtml += `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>${combo.name} x${combo.quantity}</div>
                <div>${combo.total.toLocaleString('vi-VN')}đ</div>
            </div>`;
    });
    
    $('#combo-list').html(comboList.length > 0 ? comboHtml : '<p class="text-muted small mb-0">Chưa chọn combo</p>');
    $('#combo-total').text(total.toLocaleString('vi-VN') + 'đ');
    updateTotals();
}

$(document).ready(function() {
    // Khởi tạo combo quantities
    combosData.forEach(combo => {
        comboQuantities[combo.id] = 0;
    });

    // Restore ghế đã giữ tạm khi load trang (khi mở tab mới cùng URL)
    if (myHeldSeats && myHeldSeats.length > 0) {
        // Convert to array of numbers to ensure proper comparison
        selectedSeats = myHeldSeats.map(id => parseInt(id));
        
        // Highlight các ghế đã giữ tạm (force add class seat-chon)
        myHeldSeats.forEach(gheId => {
            const seatId = parseInt(gheId);
            const seat = $(`.seat[data-ghe-id="${seatId}"]`);
            if (seat.length) {
                // Force add class seat-chon và remove disabled nếu là ghế của user hiện tại
                seat.addClass('seat-chon');
                seat.removeClass('disabled');
                seat.prop('disabled', false);
                seat.css('pointer-events', 'auto');
            }
        });
        
        // Update UI
        updateSelectedSeatsDisplay();
        updateTotals();
        toggleBookButton();
        
        // Start timer nếu có ghế đã giữ
        if (selectedSeats.length > 0 && !holdTimerInterval) {
            startHoldTimer();
        }
    }

    // Click ghế - cho phép click cả ghế đã giữ tạm của user hiện tại
    $('.seat-map').on('click', '.seat', function() {
        // ✅ FIX: Kiểm tra trạng thái ghế từ data attribute
        const trangthai = $(this).data('trangthai');
        const isMyHeld = $(this).data('my-held') === 'true' || $(this).data('my-held') === true;
        
        // ✅ Ngăn chặn click vào ghế đã đặt/thanh toán/check-in
        if (trangthai === 'da_dat') {
            return; // Ghế đã đặt/thanh toán/check-in không thể chọn
        }
        
        // Cho phép click vào:
        // 1. Ghế đang được user hiện tại giữ (isMyHeld = true hoặc có class seat-chon)
        // 2. Ghế chưa bị disabled
        const canClick = isMyHeld || $(this).hasClass('seat-chon') || !$(this).hasClass('disabled');
        
        if (!canClick) {
            return;
        }
        
        const gheId = parseInt($(this).data('ghe-id'));
        const hang = $(this).data('hang');
        const cot = $(this).data('cot');
        const seatLabel = hang + cot;

        if ($(this).hasClass('seat-chon')) {
            // Bỏ chọn
            $(this).removeClass('seat-chon');
            selectedSeats = selectedSeats.filter(id => parseInt(id) !== gheId);
        } else {
            // Chọn (kiểm tra tối đa 8 ghế cho 1 tài khoản)
            if (selectedSeats.length >= 8) {
                alert('Chỉ được chọn tối đa 8 ghế cho 1 tài khoản!');
                return;
            }

            $(this).addClass('seat-chon');
            $(this).removeClass('disabled');
            $(this).prop('disabled', false);
            $(this).css('pointer-events', 'auto');
            selectedSeats.push(gheId);
        }

        updateSelectedSeatsDisplay();
        updateTotals();
        invalidateAppliedVoucher();
        toggleBookButton();

        // Handle seat hold - gọi lại mỗi khi thay đổi ghế để cập nhật danh sách
        if (selectedSeats.length > 0) {
            // Start timer nếu chưa có
            if (!holdTimerInterval) {
                startHoldTimer();
            }
            
            // Gọi holdSeats với toàn bộ danh sách ghế đã chọn
            $.post('{{ route("booking.holdSeats") }}', {
                _token: '{{ csrf_token() }}',
                suat_chieu_id: {{ $suatChieu->id }},
                ghe_ids: selectedSeats
            })
            .done(function(response) {
                if (!response.success) {
                    stopHoldTimer();
                    selectedSeats = [];
                    $('.seat.seat-chon').removeClass('seat-chon');
                    updateSelectedSeatsDisplay();
                    updateTotals();
                    invalidateAppliedVoucher();
                    toggleBookButton();
                    showError(response.message);
                }
            })
            .fail(function() {
                stopHoldTimer();
                selectedSeats = [];
                $('.seat.seat-chon').removeClass('seat-chon');
                updateSelectedSeatsDisplay();
                updateTotals();
                invalidateAppliedVoucher();
                toggleBookButton();
                showError('Không thể giữ ghế. Vui lòng thử lại.');
            });
        } else {
            // Nếu không còn ghế nào, dừng timer và release ghế
            stopHoldTimer();
            $.post('{{ route("booking.releaseSeats") }}', {
                _token: '{{ csrf_token() }}',
                suat_chieu_id: {{ $suatChieu->id }},
                ghe_ids: []
            });
        }
    });

    // Thêm/bớt combo
    $('.plus-btn').click(function() {
        const comboId = $(this).data('combo-id');
        comboQuantities[comboId]++;
        updateComboDisplay(comboId);
        updateTotals();
        invalidateAppliedVoucher();
    });

    $('.minus-btn').click(function() {
        const comboId = $(this).data('combo-id');
        if (comboQuantities[comboId] > 0) {
            comboQuantities[comboId]--;
            updateComboDisplay(comboId);
            updateTotals();
            invalidateAppliedVoucher();
        }
    });

    // Xử lý khi chọn mã giảm giá từ dropdown
    $('#voucher-select').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#voucher-code').val(selectedOption.val());
            applyVoucher(selectedOption.val());
        }
    });

    // Áp dụng mã giảm giá - removed duplicate function

    // Xử lý nút áp dụng mã giảm giá
    $('#apply-voucher').click(function() {
        const code = $('#voucher-code').val().trim();
        applyVoucher(code);
    });

    // Xóa mã giảm giá đã áp dụng
    $('#remove-voucher').click(function(e) {
        e.preventDefault();
        resetVoucher();
    });

    // Reset mã giảm giá - removed duplicate function

    // Đặt vé
    $('#book-btn').click(function() {
        if (selectedSeats.length === 0) {
            alert('Vui lòng chọn ít nhất 1 ghế!');
            return;
        }

        // Kiểm tra ghế liên tiếp cho ghế thường và VIP
        let hasNonConsecutiveSeats = false;
        const rowsWithSeats = {};

        // Nhóm ghế theo hàng
        selectedSeats.forEach(gheId => {
            const seat = $('.seat[data-ghe-id="' + gheId + '"]');
            const hang = seat.data('hang');
            const loai = seat.data('loai');

            if (!rowsWithSeats[hang]) {
                rowsWithSeats[hang] = [];
            }
            rowsWithSeats[hang].push({ gheId, loai, cot: seat.data('cot') });
        });

        // Kiểm tra từng hàng
        for (const hang in rowsWithSeats) {
            const rowSeats = rowsWithSeats[hang].filter(seat => seat.loai === 'thuong' || seat.loai === 'vip');
            if (rowSeats.length > 1) {
                const columns = rowSeats.map(seat => parseInt(seat.cot)).sort((a, b) => a - b);
                for (let i = 1; i < columns.length; i++) {
                    if (columns[i] !== columns[i-1] + 1) {
                        hasNonConsecutiveSeats = true;
                        break;
                    }
                }
            }
            if (hasNonConsecutiveSeats) break;
        }

        if (hasNonConsecutiveSeats) {
            // Hiển thị modal cảnh báo
            $('#errorMessage').text('Ghế thường và VIP phải được chọn liên tiếp trong cùng một hàng!');
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
            return;
        }

        // Hiển thị modal xác nhận
        let confirmHtml = '<div class="mb-3"><strong>Ghế đã chọn:</strong> ' + getSelectedSeatsLabels().join(', ') + '</div>';
        confirmHtml += '<div class="mb-3"><strong>Tổng tiền:</strong> ' + formatCurrency(calculateTotal()) + '</div>';

        if (getComboItems().length > 0) {
            confirmHtml += '<div class="mb-3"><strong>Combo:</strong><br>';
            getComboItems().forEach(item => {
                const combo = combosData.find(c => c.id == item.combo_id);
                confirmHtml += '- ' + combo.ten + ' x' + item.so_luong + '<br>';
            });
            confirmHtml += '</div>';
        }

        $('#confirm-details').html(confirmHtml);
        $('#confirmModal').modal('show');
    });

    // Handle modal close - stop timer if user cancels
    $('#confirmModal').on('hidden.bs.modal', function() {
        stopHoldTimer();
    });

    // Xác nhận đặt vé
    $('#confirm-book').click(function() {
        $('#confirm-book').prop('disabled', true).text('Đang xử lý...');

        // Đặt vé (seats are already held)
        $.post('{{ route("booking.store") }}', {
            _token: '{{ csrf_token() }}',
            suat_chieu_id: {{ $suatChieu->id }},
            ghe_ids: selectedSeats,
            combo_items: getComboItems(),
            // Nếu có voucher người dùng đã áp dụng thì gửi id, nếu không thì gửi mã đã áp dụng
            voucher_nd_id: appliedVoucherNdId,
            ma_giam_gia: appliedVoucherNdId ? null : (appliedCode || null)
        })
        .done(function(response) {
            if (response.success) {
                // ✅ Stop timer on successful booking
                stopHoldTimer();
                
                // ✅ Update seat status on UI immediately
                selectedSeats.forEach(gheId => {
                    const seat = $(`.seat[data-ghe-id="${gheId}"]`);
                    seat.removeClass('seat-chon seat-giu-tam');
                    seat.addClass('seat-da-thanh-toan disabled');
                    seat.prop('disabled', true);
                    seat.css('pointer-events', 'none');
                });
                
                // ✅ Clear selected seats
                const bookedSeatsCount = selectedSeats.length;
                selectedSeats = [];
                updateSelectedSeatsDisplay();
                updateTotals();
                toggleBookButton();
                
                // ✅ Hide confirmation modal
                $('#confirmModal').modal('hide');
                
                // ✅ Show success message
                showSuccessMessage('Đang chuyển đến trang xác nhận...');
                
                // ✅ Redirect after 2 seconds
                setTimeout(function() {
                    window.location.href = response.redirect;
                }, 2000);
            } else {
                showError(response.message);
                $('#confirm-book').prop('disabled', false).text('Xác nhận đặt vé');
                if (response.redirect) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 3000); // Chuyển hướng sau 3 giây
                }
            }
        })
        .fail(function(xhr) {
            const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : 'Có lỗi xảy ra khi đặt vé!';
            showError(errorMessage);
            stopHoldTimer(); // Stop timer on booking failure
            $('#confirm-book').prop('disabled', false).text('Xác nhận đặt vé');
        });
    });
});

// Hiển thị thông báo lỗi trong modal
function showError(message) {
    $('#errorMessage').text(message);
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
}

// Hiển thị thông báo thành công
function showSuccessMessage(message) {
    // Create success alert if not exists
    if ($('#successAlert').length === 0) {
        $('body').append(`
            <div id="successAlert" class="alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3" 
                 style="z-index: 9999; min-width: 300px; display: none;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span id="successAlertMessage"></span>
                </div>
            </div>
        `);
    }
    
    $('#successAlertMessage').text(message);
    $('#successAlert').fadeIn().delay(3000).fadeOut();
}

function updateSelectedSeatsDisplay() {
    const labels = getSelectedSeatsLabels();
    $('#selected-seats').text(labels.length > 0 ? labels.join(', ') : 'Chưa chọn ghế nào');

    let badges = '';
    labels.forEach(label => {
        badges += '<span class="badge bg-success me-1 selected-seat-badge">' + label + '</span>';
    });
    $('#selected-seats-list').html(badges);
}

function getSelectedSeatsLabels() {
    return selectedSeats.map(gheId => {
        const seatId = parseInt(gheId);
        const seat = $(`.seat[data-ghe-id="${seatId}"]`);
        if (seat.length) {
            return seat.data('hang') + seat.data('cot');
        }
        return '';
    }).filter(label => label !== '');
}

function updateComboDisplay(comboId) {
    $('.combo-quantity[data-combo-id="' + comboId + '"]').text(comboQuantities[comboId]);
}

function getComboItems() {
    let items = [];
    for (let comboId in comboQuantities) {
        if (comboQuantities[comboId] > 0) {
            items.push({
                combo_id: comboId,
                so_luong: comboQuantities[comboId]
            });
        }
    }
    return items;
}

function getSeatPrice(seatType) {
    let price = baseTicketPrice;

    // Tăng giá cuối tuần (thứ 7, Chủ nhật): +20%
    const showDate = new Date('{{ $suatChieu->gio_bat_dau }}');
    if (showDate.getDay() === 0 || showDate.getDay() === 6) {
        price *= 1.2;
    }

    // Tăng giá buổi tối từ 18h trở đi: +15%
    if (showDate.getHours() >= 18) {
        price *= 1.15;
    }

    // Áp dụng loại ghế
    switch(seatType) {
        case 'vip':
            return price * 1.5;
        case 'doi':
            return price * 2;
        default:
            return price;
    }
}

function calculateTotal() {
    let ticketTotal = 0;
    selectedSeats.forEach(gheId => {
        const seatId = parseInt(gheId);
        const seat = $(`.seat[data-ghe-id="${seatId}"]`);
        if (seat.length) {
            const seatType = seat.data('loai');
            ticketTotal += getSeatPrice(seatType);
        }
    });

    let comboTotal = 0;
    getComboItems().forEach(item => {
        const combo = combosData.find(c => c.id == item.combo_id);
        comboTotal += combo.gia * item.so_luong;
    });

    return ticketTotal + comboTotal - voucherDiscount;
}

function updateTotals() {
    let ticketTotal = 0;
    let ticketSummaryHtml = '';

    if (selectedSeats.length > 0) {
        selectedSeats.forEach(gheId => {
            const seatId = parseInt(gheId);
            const seat = $(`.seat[data-ghe-id="${seatId}"]`);
            if (seat.length) {
                const seatType = seat.data('loai');
                const seatLabel = seat.data('hang') + seat.data('cot');
                const price = getSeatPrice(seatType);
                ticketTotal += price;
                ticketSummaryHtml += '<div>' + seatLabel + ' (' + getSeatTypeName(seatType) + ') = ' + formatCurrency(price) + '</div>';
            }
        });
    } else {
        ticketSummaryHtml = '<p class="text-muted">Chưa chọn ghế</p>';
    }

    $('#ticket-summary').html(ticketSummaryHtml);
    $('#ticket-total').text(formatCurrency(ticketTotal));

    let comboTotal = 0;
    let comboListHtml = '';
    getComboItems().forEach(item => {
        const combo = combosData.find(c => c.id == item.combo_id);
        comboTotal += combo.gia * item.so_luong;
        comboListHtml += '<div>' + combo.ten + ' x' + item.so_luong + ' = ' + formatCurrency(combo.gia * item.so_luong) + '</div>';
    });
    $('#combo-list').html(comboListHtml);
    $('#combo-total').text(formatCurrency(comboTotal));

    // Update discount display
    $('#discount-amount').text(formatCurrency(voucherDiscount));

    const grandTotal = calculateTotal();
    $('#grand-total').text(formatCurrency(grandTotal));
}

function invalidateAppliedVoucher() {
    if (voucherDiscount > 0) {
        voucherDiscount = 0;
        appliedVoucherNdId = null;
        appliedCode = null;
        $('#voucher-message').html('<small class="text-warning">Giỏ hàng thay đổi, vui lòng áp dụng lại mã.</small>');
    }
}

function getSeatTypeName(seatType) {
    switch(seatType) {
        case 'vip':
            return 'VIP';
        case 'doi':
            return 'Đôi';
        default:
            return 'Thường';
    }
}

function toggleBookButton() {
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    $('#book-btn').prop('disabled', selectedSeats.length === 0 || !isLoggedIn);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

// Kiểm tra ghế có liên tiếp trong hàng không (chỉ áp dụng cho ghế thường và VIP)
function areSeatsConsecutiveInRow(hang, selectedSeats) {
    // Lấy tất cả ghế đã chọn trong hàng này không phải ghế đôi
    const rowSeats = selectedSeats.filter(gheId => {
        const seat = $('.seat[data-ghe-id="' + gheId + '"]');
        return seat.data('hang') === hang && seat.data('loai') !== 'doi';
    });

    if (rowSeats.length <= 1) return true; // 0 hoặc 1 ghế luôn liên tiếp

    // Lấy số cột của các ghế
    const columns = rowSeats.map(gheId => {
        const seat = $('.seat[data-ghe-id="' + gheId + '"]');
        return parseInt(seat.data('cot'));
    }).sort((a, b) => a - b);

    // Kiểm tra liên tiếp
    for (let i = 1; i < columns.length; i++) {
        if (columns[i] !== columns[i-1] + 1) {
            return false;
        }
    }
    return true;
}

// Reset mã giảm giá
function resetVoucher() {
    voucherDiscount = 0;
    appliedVoucherNdId = null;
    appliedCode = null;
    $('#voucher-code').val('');
    $('#voucher-select').val('');
    $('#applied-voucher-info').addClass('d-none');
    updateTotals();
}

// Seat hold timer functions
function startHoldTimer() {
    if (holdTimerInterval) {
        clearInterval(holdTimerInterval);
    }

    holdExpirationTime = Date.now() + holdDuration;
    $('#hold-timer-container').removeClass('d-none');

    holdTimerInterval = setInterval(function() {
        const now = Date.now();
        const remaining = holdExpirationTime - now;

        if (remaining <= 0) {
            clearInterval(holdTimerInterval);
            handleHoldExpired();
            return;
        }

        const minutes = Math.floor(remaining / (1000 * 60));
        const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
        const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        $('#hold-timer').text(timeString);

        // Show warning when less than 2 minutes remaining
        if (remaining <= 2 * 60 * 1000) {
            $('#hold-timer').addClass('text-danger').removeClass('text-warning');
            if (!$('#hold-timer-container .alert').hasClass('alert-danger')) {
                $('#hold-timer-container .alert').removeClass('alert-warning').addClass('alert-danger');
            }
        } else {
            $('#hold-timer').addClass('text-warning').removeClass('text-danger');
            $('#hold-timer-container .alert').removeClass('alert-danger').addClass('alert-warning');
        }
    }, 1000);
}

function stopHoldTimer() {
    if (holdTimerInterval) {
        clearInterval(holdTimerInterval);
        holdTimerInterval = null;
    }
    $('#hold-timer-container').addClass('d-none');
    holdExpirationTime = null;
}

function handleHoldExpired() {
    $('#hold-timer-container').addClass('d-none');

    // Release held seats on server
    $.post('{{ route("booking.releaseSeats") }}', {
        _token: '{{ csrf_token() }}',
        suat_chieu_id: {{ $suatChieu->id }},
        ghe_ids: selectedSeats
    });

    // Show expired modal
    const holdExpiredModal = new bootstrap.Modal(document.getElementById('holdExpiredModal'));
    holdExpiredModal.show();

    // Clear selected seats
    selectedSeats = [];
    updateSelectedSeatsDisplay();
    updateTotals();
    invalidateAppliedVoucher();
    toggleBookButton();

    // Remove seat selection styling
    $('.seat.seat-chon').removeClass('seat-chon');
}
</script>
@endpush
