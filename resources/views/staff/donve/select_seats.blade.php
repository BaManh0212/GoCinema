@extends('staff.layouts.staff')

@section('title', '💺 Chọn Ghế - Đặt Vé Tại Quầy')

@push('styles')
<style>
    .seat-map {
        background-color: #f8f9fa;
        border-radius: 10px;
        overflow: hidden;
    }

    .seat {
        width: 36px;
        height: 36px;
        margin: 3px;
        border-radius: 4px;
        border: none;
        text-align: center;
        line-height: 36px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        color: #fff;
        user-select: none;
        transition: all 0.15s ease-in-out;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    
    /* Seat types */
    .seat-thuong { 
        background: linear-gradient(135deg, #6c757d, #495057);
    }
    
    .seat-vip { 
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #000;
    }
    
    .seat-doi { 
        background: linear-gradient(135deg, #fd7e14, #e76f10);
        width: 75px;
    }
    
    .seat-chon { 
        background: #28a745 !important;
        transform: scale(1.05);
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #28a745;
    }
    
    .seat-da-thanh-toan { 
        background: #dc3545 !important;
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
        box-shadow: none;
    }
    
    .seat-giu-tam { 
        background: #6c757d !important;
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
        box-shadow: none;
    }
    
    .seat-dat {
        background: #6c757d !important;
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
        box-shadow: none;
    }

    .seat-bao-tri {
        background: #ffc107 !important;
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
        box-shadow: none;
    }
    
    .seat.disabled { 
        cursor: not-allowed !important; 
        pointer-events: none;
        opacity: 0.5;
    }

    .screen {
        background: linear-gradient(to right, #333, #555, #333);
        color: #fff;
        text-align: center;
        font-weight: 600;
        padding: 12px 0;
        border-radius: 0 0 50% 50%/10px;
        width: 70%;
        margin: 0 auto 30px;
        letter-spacing: 4px;
        text-transform: uppercase;
        font-size: 0.9rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .screen:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(0deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 50%);
        pointer-events: none;
    }

    .seat:not(.disabled):hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        z-index: 1;
    }

    /* Seat map container */
    .seat-map {
        position: relative;
    }
    
    /* Legend */
    .legend-container {
        background: #f1f3f5;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px 20px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        font-size: 13px;
        color: #343a40;
    }
    .legend-box {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 3px;
        margin-right: 5px;
        vertical-align: middle;
    }
    
    .seat-vip .legend-box {
        background-color: #ffc107;
    }
    
    .seat-doi .legend-box {
        background-color: #fd7e14;
    }
    
    .seat-thuong .legend-box {
        background-color: #e9ecef;
    }
    
    .seat-da-thanh-toan .legend-box {
        background-color: #dc3545;
    }
    
    .seat-giu-tam .legend-box {
        background-color: #6c757d;
    }
    
    .seat-chon .legend-box {
        background-color: #198754;
    }
    
    /* Selected seats list */
    #selected-seats-list {
        margin-top: 10px;
    }
    
    .selected-seat-badge {
        background-color: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .selected-seat-badge .seat-type {
        font-weight: 600;
        margin-left: 5px;
        color: #6c757d;
    }
    
    .selected-seat-badge .remove-seat {
        margin-left: 5px;
        cursor: pointer;
        color: #dc3545;
        font-weight: bold;
    }
    
    /* Combo section */
    .combo-item {
        transition: all 0.2s;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    
    .combo-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
    
    .combo-item .card-body {
        padding: 10px;
    }
    
    .combo-item h6 {
        margin-bottom: 5px;
        font-size: 0.95rem;
    }
    
    .combo-item small {
        font-size: 0.8rem;
    }
    
    /* Form controls */
    .form-control[type="number"]::-webkit-inner-spin-button,
    .form-control[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .form-control[type="number"] {
        -moz-appearance: textfield;
        text-align: center;
        width: 50px;
        display: inline-block;
        margin: 0 5px;
    }
    
    .btn-decrease, .btn-increase {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .seat {
            width: 30px;
            height: 30px;
            font-size: 10px;
        }
        
        .seat-gap {
            width: 10px;
            height: 30px;
        }
    }
    
    /* Hide number input spinner */
    .hide-spinner::-webkit-outer-spin-button,
    .hide-spinner::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .hide-spinner {
        -moz-appearance: textfield;
    }
</style>
@endpush

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
                                <i class="bi bi-ticket-perforated text-danger me-2"></i>
                                <span class="text-white-75">Phòng:</span>
                                <span class="ms-1 fw-medium">{{ $suatChieu->phong->ten }}</span>
                            </div>
                            
                            <div class="movie-info-item">
                                <i class="bi bi-cash-coin text-primary me-2"></i>
                                <span class="text-white-75">Giá vé:</span>
                                <span class="ms-1 fw-bold text-warning">{{ number_format($suatChieu->gia_ve, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sơ đồ ghế --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-square me-2"></i>
                        Chọn ghế (Tối đa 8 ghế liền kề)
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Chú thích --}}
                    <div class="legend-container mb-4">
                        <div class="legend-item">
                            <span class="legend-box seat-vip"></span>
                            <span>Ghế VIP</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-doi"></span>
                            <span>Ghế đôi</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-thuong"></span>
                            <span>Ghế thường</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-da-thanh-toan"></span>
                            <span>Ghế đã đặt</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-giu-tam"></span>
                            <span>Ghế giữ tạm</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-bao-tri"></span>
                            <span>Ghế bảo trì</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-chon"></span>
                            <span>Ghế đã chọn</span>
                        </div>
                    </div>

                    {{-- Sơ đồ ghế --}}
                    <div class="seat-map">
                        <div class="screen">MÀN HÌNH CHIẾU</div>

                        <div class="d-flex flex-column align-items-center">
                            @foreach ($ghes as $hang => $danhSachGhe)
                                <div class="d-flex mb-2">
                                    @foreach ($danhSachGhe as $ghe)
                                        @php
                                            $classes = 'seat seat-' . $ghe->loai;
                                            $trangthai = $gheStatuses[$ghe->id] ?? 'hoat_dong';
                                            $disabled = false;
                                            $basePrice = $suatChieu->gia_ve;

                                            // Tăng giá cuối tuần (thứ 7, Chủ nhật): +20%
                                            if ($suatChieu->gio_bat_dau->isWeekend()) {
                                                $basePrice *= 1.2;
                                            }

                                            // Tăng giá buổi tối từ 18h trở đi: +15%
                                            if ($suatChieu->gio_bat_dau->hour >= 18) {
                                                $basePrice *= 1.15;
                                            }

                                            // Áp dụng loại ghế
                                            if ($ghe->loai === 'vip') {
                                                $basePrice *= 1.5;
                                            } elseif ($ghe->loai === 'doi') {
                                                $basePrice *= 2;
                                            }

                                            $gia = $basePrice;

                                            if(in_array($ghe->id, $gheDaDat)){
                                                $classes = 'seat seat-da-thanh-toan disabled';
                                                $trangthai = 'da_dat';
                                                $disabled = true;
                                            } elseif(in_array($ghe->id, $giuTamIds)){
                                                $classes = 'seat seat-giu-tam disabled';
                                                $trangthai = 'giu_tam';
                                                $disabled = true;
                                            } elseif(isset($gheStatuses[$ghe->id]) && $gheStatuses[$ghe->id] === 'bao_tri') {
                                                $classes = 'seat seat-bao-tri disabled';
                                                $trangthai = 'bao_tri';
                                                $disabled = true;

                                            } elseif(isset($gheStatuses[$ghe->id]) && $gheStatuses[$ghe->id] === 'vo_hieu_hoa') {
                                                $classes = 'seat seat-vo-hieu-hoa disabled';
                                                $trangthai = 'vo_hieu_hoa';
                                                $disabled = true;
                                            }
                                        @endphp

                                        <button type="button" class="seat {{ $classes }} {{ $disabled ? 'disabled' : '' }}"
                                                data-ghe-id="{{ $ghe->id }}"
                                                data-hang="{{ $hang }}"
                                                data-cot="{{ $ghe->cot }}"
                                                data-loai="{{ $ghe->loai }}"
                                                data-gia="{{ $gia }}"
                                                data-trangthai="{{ $trangthai }}"
                                                {{ $disabled ? 'disabled="disabled" style="pointer-events: none;"' : '' }}>
                                            {{ $hang }}{{ $ghe->cot }}
                                        </button>
                                        
                                        @if($ghe->cot % 5 == 0 && !$loop->last)
                                            <div class="seat-gap"></div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Ghế đã chọn --}}
                    <div class="mt-4">
                        <h6>Ghế đã chọn: <span id="selected-seats">Chưa chọn ghế nào</span></h6>
                        <div id="selected-seats-list" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    {{-- Thông tin đặt chỗ --}}
                    <div class="booking-summary">
                        <h5 class="mb-3">Thông tin đặt chỗ</h5>
                        <div id="ticket-summary" class="mb-3">
                            <div id="ticket-details"></div>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Tổng vé:</span>
                                <span id="ticket-amount">0đ</span>
                            </div>
                        </div>
                        <div id="combo-summary" class="mb-3">
                            <div id="combo-list">
                                <p class="text-muted small mb-0">Chưa chọn combo</p>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>Combo:</span>
                                <span id="combo-total">0đ</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-3 pt-2 border-top">
                            <span>Tổng cộng:</span>
                            <span id="total-amount">0đ</span>
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
                        Thông tin đặt vé
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Thông tin phim --}}
                    <div class="mb-4">
                        <h6>Thông tin phim</h6>
                        <p class="mb-1"><strong>{{ $suatChieu->phim->tieu_de }}</strong></p>
                        <p class="mb-1 small text-muted">
                            {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($suatChieu->gio_ket_thuc)->format('H:i') }} | 
                            {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('d/m/Y') }}
                        </p>
                        <p class="mb-1 small">
                            <span class="text-muted">Rạp:</span> {{ $suatChieu->phong->rap->ten }} - {{ $suatChieu->phong->ten }}
                        </p>
                    </div>

                    {{-- Combo & Đồ ăn --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Combo & Đồ ăn</h6>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#comboSection">
                                <i class="bi bi-plus-lg"></i> Thêm
                            </button>
                        </div>
                        
                        <div class="collapse" id="comboSection">
                            <div class="card card-body bg-light mb-3">
                                @if($combos->count() > 0)
                                    @foreach($combos as $combo)
                                        @if($combo->so_luong > 0)
                                            <div class="combo-item mb-3 p-2 border rounded" data-combo-id="{{ $combo->id }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $combo->ten }}</h6>
                                                        <small class="text-muted">{{ number_format($combo->gia, 0, ',', '.') }}đ</small>
                                                        <div class="text-success small">Còn: {{ $combo->so_luong }}</div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-decrease me-1"
                                                                data-combo-id="{{ $combo->id }}">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" class="form-control form-control-sm text-center combo-qty"
                                                            id="combo-{{ $combo->id }}-qty" value="0" min="0" max="{{ $combo->so_luong }}"
                                                            style="width: 50px;" data-combo-id="{{ $combo->id }}">
                                                        <button type="button" class="btn btn-sm btn-outline-primary btn-increase ms-1"
                                                                data-combo-id="{{ $combo->id }}">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted small mb-0">Không có combo nào khả dụng</p>
                                @endif
                            </div>
                        </div>
                        
                        <div id="selected-combos">
                            <p class="text-muted small mb-0">Chưa chọn combo</p>
                        </div>
                    </div>

                    {{-- Phương thức thanh toán --}}
                    <div class="mb-4">
                        <h6>Phương thức thanh toán</h6>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="tien_mat" value="tien_mat" checked>
                                <label class="form-check-label" for="tien_mat">
                                    <i class="bi bi-cash-coin me-1"></i> Tiền mặt
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="chuyen_khoan" value="chuyen_khoan">
                                <label class="form-check-label" for="chuyen_khoan">
                                    <i class="bi bi-bank me-1"></i> Chuyển khoản
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin khách hàng --}}
                    <div class="mb-4">
                        <h6>Thông tin khách hàng</h6>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label small mb-1">Số điện thoại (nếu có)</label>
                            <input type="text" class="form-control form-control-sm" id="customer_phone" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label small mb-1">Tên khách hàng (nếu có)</label>
                            <input type="text" class="form-control form-control-sm" id="customer_name" placeholder="Nhập tên khách hàng">
                        </div>
                    </div>

                    {{-- Tổng thanh toán --}}
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng tiền vé:</span>
                            <span id="payment-ticket-amount">0đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Combo & Đồ ăn:</span>
                            <span id="payment-combo-amount">0đ</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-2 pt-2 border-top">
                            <span>Tổng thanh toán:</span>
                            <span class="text-primary" id="payment-total-amount">0đ</span>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" id="reset-selection">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Chọn lại
                            </button>
                            <button type="button" class="btn btn-primary" id="confirm-booking" disabled>
                                <i class="bi bi-ticket-perforated me-1"></i> Xác nhận đặt vé
                            </button>
                            <a href="{{ route('staff.donve.selectSuat', $suatChieu->phim_id) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Chọn suất khác
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal xác nhận --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Xác nhận đặt vé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i> Vui lòng kiểm tra lại thông tin trước khi xác nhận.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Thông tin phim</h6>
                        <p class="mb-1"><strong>Phim:</strong> {{ $suatChieu->phim->tieu_de }}</p>
                        <p class="mb-1"><strong>Suất chiếu:</strong> {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('H:i d/m/Y') }}</p>
                        <p class="mb-1"><strong>Phòng:</strong> {{ $suatChieu->phong->ten }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Thông tin đặt vé</h6>
                        <div id="confirm-seats"></div>
                        <div id="confirm-combos"></div>
                        <div class="d-flex justify-content-between fw-bold mt-2">
                            <span>Tổng cộng:</span>
                            <span id="confirm-total"></span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <label for="staff_note" class="form-label">Ghi chú (nếu có)</label>
                    <textarea class="form-control" id="staff_note" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-booking-btn">
                    <i class="bi bi-check2-circle me-1"></i> Xác nhận đặt vé
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal thông báo --}}
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Nội dung thông báo sẽ được điền vào đây -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Khởi tạo biến
    let selectedSeats = [];
    let comboQuantities = {};
    let baseTicketPrice = {{ $suatChieu->gia_ve }};
    let combosData = @json($combos);
    
    // Khởi tạo số lượng combo
    combosData.forEach(combo => {
        comboQuantities[combo.id] = 0;
    });
    
    // Xử lý click tăng số lượng combo
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
            updatePaymentSummary();
            updateSelectedCombos();
        }
        
        // Bật/tắt nút giảm/tăng
        $(`.btn-decrease[data-combo-id="${comboId}"]`).prop('disabled', value <= 0);
        $(this).prop('disabled', value >= max);
    });
    
    // Xử lý click giảm số lượng combo
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
            updatePaymentSummary();
            updateSelectedCombos();
        }
        
        // Bật/tắt nút giảm/tăng
        $(this).prop('disabled', value <= 0);
        $(`.btn-increase[data-combo-id="${comboId}"]`).prop('disabled', value >= max);
    });
    
    // Xử lý nhập trực tiếp số lượng
    $(document).on('change', '.combo-qty', function() {
        const comboId = $(this).data('combo-id');
        const max = parseInt($(this).attr('max'));
        let value = parseInt($(this).val()) || 0;
        
        // Đảm bảo giá trị nằm trong khoảng cho phép
        if (value < 0) value = 0;
        if (value > max) value = max;
        
        $(this).val(value);
        comboQuantities[comboId] = value;
        updateComboSummary();
        updatePaymentSummary();
        updateSelectedCombos();
        
        // Bật/tắt nút giảm/tăng
        $(`.btn-decrease[data-combo-id="${comboId}"]`).prop('disabled', value <= 0);
        $(`.btn-increase[data-combo-id="${comboId}"]`).prop('disabled', value >= max);
    });
    
    // Xử lý chọn ghế
    $('.seat-map').on('click', '.seat:not(.disabled)', function() {
        const gheId = $(this).data('ghe-id');
        const hang = $(this).data('hang');
        const cot = $(this).data('cot');
        const loai = $(this).data('loai');
        const gia = $(this).data('gia');
        const seatLabel = hang + cot;

        // Nếu ghế đã được chọn thì bỏ chọn
        if ($(this).hasClass('seat-chon')) {
            $(this).removeClass('seat-chon');
            selectedSeats = selectedSeats.filter(id => id !== gheId);
        } else {
            // Kiểm tra số lượng ghế tối đa
            if (selectedSeats.length >= 8) {
                showMessage('Thông báo', 'Chỉ được chọn tối đa 8 ghế cho mỗi lần đặt!');
                return;
            }

            $(this).addClass('seat-chon');
            selectedSeats.push(gheId);
        }

        updateSelectedSeatsDisplay();
        updateTotals();
        updatePaymentSummary();
        toggleBookButton();
    });
    
    // Xử lý nút chọn lại
    $('#reset-selection').click(function() {
        // Xóa tất cả các ghế đã chọn
        $('.seat-chon').removeClass('seat-chon');
        selectedSeats = [];
        
        // Đặt lại số lượng combo về 0
        $('.combo-qty').val(0);
        Object.keys(comboQuantities).forEach(key => {
            comboQuantities[key] = 0;
        });
        
        // Cập nhật giao diện
        updateSelectedSeatsDisplay();
        updateComboSummary();
        updateSelectedCombos();
        updateTotals();
        updatePaymentSummary();
        toggleBookButton();
    });
    
    // Xử lý nút xác nhận đặt vé
    $('#confirm-booking').click(function() {
        if (selectedSeats.length === 0) {
            showMessage('Lỗi', 'Vui lòng chọn ít nhất 1 ghế!');
            return;
        }
        
        // Hiển thị thông tin xác nhận
        showConfirmModal();
    });
    
    // Xác nhận đặt vé
    $('#confirm-booking-btn').click(function() {
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        const customerPhone = $('#customer_phone').val();
        const customerName = $('#customer_name').val();
        const staffNote = $('#staff_note').val();
        
        // Lấy danh sách combo đã chọn
        const selectedCombos = [];
        Object.keys(comboQuantities).forEach(comboId => {
            const qty = comboQuantities[comboId];
            if (qty > 0) {
                selectedCombos.push({
                    id: comboId,
                    quantity: qty
                });
            }
        });
        
        // Gửi yêu cầu đặt vé
        $.ajax({
            url: '{{ route("staff.donve.store") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                suat_chieu_id: {{ $suatChieu->id }},
                ghe_ids: selectedSeats,
                combos: selectedCombos,
                payment_method: paymentMethod,
                customer_phone: customerPhone,
                customer_name: customerName,
                staff_note: staffNote
            },
            beforeSend: function() {
                $('#confirm-booking-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
            },
            success: function(response) {
                if (response.success) {
                    // Hiển thị thông báo thành công
                    showMessage('Thành công', 'Đặt vé thành công!', function() {
                        // Tải lại trang sau khi đóng thông báo
                        window.location.href = response.redirect_url || '{{ route("staff.donve.index") }}';
                    });
                } else {
                    showMessage('Lỗi', response.message || 'Có lỗi xảy ra khi đặt vé!');
                    $('#confirm-booking-btn').prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Xác nhận đặt vé');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Có lỗi xảy ra khi đặt vé!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showMessage('Lỗi', errorMessage);
                $('#confirm-booking-btn').prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Xác nhận đặt vé');
            }
        });
    });
    
    // Cập nhật hiển thị ghế đã chọn
    function updateSelectedSeatsDisplay() {
        const selectedSeatsList = $('.seat-chon').map(function() {
            const hang = $(this).data('hang');
            const cot = $(this).data('cot');
            const loai = $(this).data('loai');
            const loaiText = loai === 'vip' ? 'VIP' : (loai === 'doi' ? 'Đôi' : 'Thường');
            return `${hang}${cot} (${loaiText})`;
        }).get().join(', ');
        
        if (selectedSeats.length > 0) {
            $('#selected-seats').html(`
                <div class="alert alert-info p-2 mb-0">
                    <i class="bi bi-check2-circle me-1"></i>
                    <strong>Đã chọn ${selectedSeats.length} ghế:</strong> ${selectedSeatsList}
                </div>
            `);
        } else {
            $('#selected-seats').html('<p class="text-muted mb-0">Chưa chọn ghế</p>');
        }
    }
    // Lấy tổng tiền vé theo ghế đã chọn
function getTicketTotal() {
    let total = 0;
    $('.seat-chon').each(function() {
        const price = parseFloat($(this).data('gia')) || 0;
        total += price;
    });
    return total;
}

// Cập nhật tổng tiền vé và combo
function updateTotals() {
    const ticketTotal = getTicketTotal();

    // Tính tổng tiền combo
    let comboTotal = 0;
    Object.keys(comboQuantities).forEach(comboId => {
        const combo = combosData.find(c => c.id == comboId);
        if (combo) {
            comboTotal += combo.gia * comboQuantities[comboId];
        }
    });

    // Cập nhật giao diện tổng tiền vé
    $('#ticket-summary').html(`
        <div class="d-flex justify-content-between">
            <span>Vé phim (${$('.seat-chon').length} ghế):</span>
            <span>${formatCurrency(ticketTotal)}</span>
        </div>
    `);

    // Cập nhật tổng tiền combo
    $('#combo-total').text(formatCurrency(comboTotal));

    // Tổng thanh toán
    const totalAmount = ticketTotal + comboTotal;
    $('#total-amount').text(formatCurrency(totalAmount));

    // Cập nhật tổng tiền trong modal xác nhận
    $('#confirm-total').text(formatCurrency(totalAmount));
}

// Cập nhật thanh toán bên phải
function updatePaymentSummary() {
    const ticketTotal = getTicketTotal();

    // Tính tổng tiền combo và tạo HTML hiển thị
    let comboTotal = 0;
    let hasCombos = false;
    let comboHtml = '';

    Object.keys(comboQuantities).forEach(comboId => {
        const qty = comboQuantities[comboId];
        if (qty > 0) {
            const combo = combosData.find(c => c.id == comboId);
            if (combo) {
                const comboAmount = combo.gia * qty;
                comboTotal += comboAmount;
                hasCombos = true;

                comboHtml += `
                    <div class="d-flex justify-content-between small mb-1">
                        <span>${combo.ten} x${qty}</span>
                        <span>${formatCurrency(comboAmount)}</span>
                    </div>
                `;
            }
        }
    });

    // Cập nhật giao diện thanh toán
    $('#payment-ticket-amount').text(formatCurrency(ticketTotal));
    $('#payment-combo-amount').text(formatCurrency(comboTotal));

    const totalAmount = ticketTotal + comboTotal;
    $('#payment-total-amount').text(formatCurrency(totalAmount));

    if (hasCombos) {
        $('#selected-combos').html(comboHtml);
    } else {
        $('#selected-combos').html('<p class="text-muted small mb-0">Chưa chọn combo</p>');
    }
}

// Khi click ghế hoặc thay đổi combo, gọi lại hai hàm trên
$('.seat, .combo-input').on('click change', function() {
    updateTotals();
    updatePaymentSummary();
});

    
    // Cập nhật danh sách combo đã chọn
    function updateSelectedCombos() {
        let comboListHtml = '';
        let hasCombos = false;
        
        Object.keys(comboQuantities).forEach(comboId => {
            const qty = comboQuantities[comboId];
            if (qty > 0) {
                const combo = combosData.find(c => c.id == comboId);
                if (combo) {
                    comboListHtml += `
                        <div class="d-flex justify-content-between">
                            <span>${combo.ten} x${qty}</span>
                            <span>${formatCurrency(combo.gia * qty)}</span>
                        </div>
                    `;
                    hasCombos = true;
                }
            }
        });
        
        if (hasCombos) {
            $('#combo-list').html(comboListHtml);
        } else {
            $('#combo-list').html('<p class="text-muted small mb-0">Chưa chọn combo</p>');
        }
        
        updateTotals();
        updatePaymentSummary();
    }
    
    // Hiển thị modal xác nhận
    function showConfirmModal() {
        // Cập nhật thông tin ghế đã chọn
        const selectedSeatsList = $('.seat-chon').map(function() {
            const hang = $(this).data('hang');
            const cot = $(this).data('cot');
            const loai = $(this).data('loai');
            const loaiText = loai === 'vip' ? 'VIP' : (loai === 'doi' ? 'Đôi' : 'Thường');
            return `${hang}${cot} (${loaiText})`;
        }).get().join(', ');
        
        $('#confirm-seats').html(`
            <p class="mb-1"><strong>Ghế đã chọn (${selectedSeats.length}):</strong> ${selectedSeatsList}</p>
            <p class="mb-1"><strong>Giá vé:</strong> ${formatCurrency(selectedSeats.length * baseTicketPrice)}</p>
        `);
        
        // Cập nhật thông tin combo
        let comboHtml = '';
        let hasCombos = false;
        
        Object.keys(comboQuantities).forEach(comboId => {
            const qty = comboQuantities[comboId];
            if (qty > 0) {
                const combo = combosData.find(c => c.id == comboId);
                if (combo) {
                    comboHtml += `
                        <div class="d-flex justify-content-between">
                            <span>${combo.ten} x${qty}</span>
                            <span>${formatCurrency(combo.gia * qty)}</span>
                        </div>
                    `;
                    hasCombos = true;
                }
            }
        });
        
        if (hasCombos) {
            $('#confirm-combos').html(`
                <div class="mt-2">
                    <strong>Combo & Đồ ăn:</strong>
                    ${comboHtml}
                </div>
            `);
        } else {
            $('#confirm-combos').html('<p class="mt-2"><strong>Không có combo nào được chọn</strong></p>');
        }
        
        // Hiển thị modal
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    }
    
    // Bật/tắt nút đặt vé
    function toggleBookButton() {
        const hasSelectedSeats = selectedSeats.length > 0;
        $('#confirm-booking').prop('disabled', !hasSelectedSeats);
    }
    
    // Hiển thị thông báo
    function showMessage(title, message, callback = null) {
        $('#messageModalTitle').text(title);
        $('#messageModalBody').html(message);
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));
        
        if (callback && typeof callback === 'function') {
            $('#messageModal').on('hidden.bs.modal', function() {
                callback();
                $(this).off('hidden.bs.modal');
            });
        }
        
        modal.show();
    }
    
    // Định dạng tiền tệ
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount).replace('₫', '') + 'đ';
    }
    
    // Khởi tạo ban đầu
    updateSelectedSeatsDisplay();
    updateTotals();
    updatePaymentSummary();
    toggleBookButton();
});
</script>
@endpush
@endsection
