@extends('staff.layouts.staff')

@section('title', '💺 Chọn Ghế - Đặt Vé Tại Quầy')

@push('styles')
<style>
    .seat-map {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
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
        color: #000;
        user-select: none;
        transition: all 0.15s ease-in-out;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Seat types */
    .seat-thuong { 
        background: linear-gradient(135deg, #87CEFA, #87CEFA);
    }
    
    .seat-vip { 
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .seat-doi { 
        background: linear-gradient(135deg, #98FB98, #98FB98);
        width: 75px;
    }
    
    .seat-chon { 
        background: #28a745 !important;
        transform: scale(1.05);
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #28a745;
    }
    
    .seat-da-thanh-toan { 
        background: #DC3545 !important;
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .seat-giu-tam { 
        background: #6c757d !important;
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .seat-dat {
        background: #FF6347 !important;
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .seat-bao-tri {
        background: #ffc107 !important;
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
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
    }

    .seat:not(.disabled):not(.seat-da-thanh-toan):not(.seat-giu-tam):not(.seat-dat):not(.seat-bao-tri):hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        z-index: 1;
    }

    .legend-container {
        background: #f1f3f5;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin: 0 10px;
        font-size: 0.9rem;
    }
    
    .legend-box {
        width: 20px;
        height: 20px;
        border-radius: 3px;
        margin-right: 8px;
        display: inline-block;
    }

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

    .row-label {
        font-weight: bold;
        font-size: 1.1em;
        color: #333;
        min-width: 20px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .seat {
            width: 30px;
            height: 30px;
            line-height: 30px;
            font-size: 9px;
            margin: 2px;
        }
        
        .seat-doi {
            width: 64px;
        }
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-film me-2"></i>
                        {{ $suatChieu->phim->tieu_de }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($suatChieu->phim->anh_poster)
                                <img src="{{ asset('storage/' . $suatChieu->phim->anh_poster) }}"
                                     alt="{{ $suatChieu->phim->tieu_de }}"
                                     class="img-fluid rounded shadow">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="height: 200px;">
                                    <i class="bi bi-image text-muted fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h5 class="mb-3">{{ $suatChieu->phim->tieu_de }}</h5>
                            <div class="mb-2">
                                <i class="bi bi-building text-primary me-2"></i>
                                {{ $suatChieu->phong->rap->ten }}
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-calendar-event text-primary me-2"></i>
                                {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('d/m/Y H:i') }}
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                Phòng {{ $suatChieu->phong->ten }}
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-ticket-perforated text-primary me-2"></i>
                                Giá vé: {{ number_format($suatChieu->gia_ve, 0, ',', '.') }}đ
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-square me-2"></i>
                        Chọn ghế
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Legend -->
                    <div class="legend-container">
                        <div class="legend-item">
                            <span class="legend-box seat-thuong"></span>
                            <span>Thường</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-vip"></span>
                            <span>VIP</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box seat-doi"></span>
                            <span>Đôi</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background: #28a745;"></span>
                            <span>Đang chọn</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background: #DC3545;"></span>
                            <span>Đã đặt</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background: #6c757d;"></span>
                            <span>Giữ tạm</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background: #ffc107;"></span>
                            <span>Bảo trì</span>
                        </div>
                    </div>

                    <!-- Screen -->
                    <div class="screen mb-4">MÀN HÌNH</div>

                    <!-- Entrance -->
                    <div class="text-start mb-3">
                        <div class="seat-preview seat-entrance">VÀO</div>
                    </div>

                    <!-- Seating Area -->
                    <div class="seat-map">
                        @php
                            $hangLetters = range('A', chr(ord('A') + $suatChieu->phong->so_hang - 1));
                        @endphp

                        @foreach ($hangLetters as $hang)
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <div class="row-label me-3">{{ $hang }}</div>
                                @php
                                    $danhSachGhe = $ghes->get($hang, collect());
                                    $cot = 1;
                                    $seatCount = 0;

                                    // First pass to check for double seats
                                    $doubleSeats = [];
                                    for ($c = 1; $c <= $suatChieu->phong->so_cot; $c++) {
                                        $ghe = $danhSachGhe->firstWhere('cot', $c);
                                        if ($ghe && $ghe->loai == 'doi') {
                                            $doubleSeats[$c] = true;
                                        }
                                    }
                                @endphp

                                @while ($cot <= $suatChieu->phong->so_cot)
                                    @php
                                        // Skip if this is the second part of a double seat
                                        if (isset($doubleSeats[$cot - 1]) && $doubleSeats[$cot - 1]) {
                                            $cot++;
                                            continue;
                                        }

                                        $ghe = $danhSachGhe->firstWhere('cot', $cot);
                                        $currentLoai = $ghe ? $ghe->loai : 'thuong';
                                        $isDouble = $currentLoai == 'doi';
                                        
                                        $trangthai = $ghe ? ($gheStatuses[$ghe->id] ?? 'hoat_dong') : 'hoat_dong';
                                        if ($ghe && in_array($ghe->id, $giuTamIds)) {
                                            $trangthai = 'giu_tam';
                                        } elseif ($ghe && in_array($ghe->id, $gheDaDat)) {
                                            $trangthai = 'da_dat';
                                        }
                                        
                                        $classes = 'seat seat-' . $currentLoai;
                                        if ($trangthai === 'bao_tri') {
                                            $classes = 'seat seat-bao-tri';
                                        } elseif ($trangthai === 'da_dat') {
                                            $classes = 'seat seat-da-thanh-toan';
                                        } elseif ($trangthai === 'giu_tam') {
                                            $classes = 'seat seat-giu-tam';
                                        } elseif ($trangthai === 'da_dat') {
                                            $classes = 'seat seat-dat';
                                        }

                                        $seatNumber = $hang . $cot;
                                    @endphp

                                    <div class="{{ $classes }}"
                                         data-ghe-id="{{ $ghe ? $ghe->id : '' }}"
                                         data-hang="{{ $hang }}"
                                         data-cot="{{ $cot }}"
                                         data-loai="{{ $currentLoai }}"
                                         data-trangthai="{{ $trangthai }}"
                                         title="{{ $seatNumber }}"
                                         @if(in_array($trangthai, ['da_dat', 'giu_tam', 'bao_tri', 'da_thanh_toan']))
                                            style="cursor: not-allowed;"
                                         @endif>
                                        @if($isDouble)
                                            💑
                                        @else
                                            {{ $cot }}
                                        @endif
                                    </div>

                                    @php
                                        $cot++;
                                        $seatCount++;
                                    @endphp
                                @endwhile
                            </div>

                            @if ($hang == chr(ord('A') + floor($suatChieu->phong->so_hang / 2) - 1))
                                <div style="height: 20px;"></div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Exit -->
                    <div class="text-end mt-3">
                        <div class="seat-preview seat-exit">RA</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        Thông tin khách hàng
                    </h5>
                </div>
                <div class="card-body">
                    <form id="customer-form">
                        <div class="mb-3">
                            <label for="customer-name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="customer-phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="customer-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customer-email">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Combo & Food -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">
                        <i class="bi bi-cup-hot me-2"></i>
                        Combo & Đồ ăn
                    </h5>
                </div>
                <div class="card-body">
                    @if($combos->count() > 0)
                        @foreach($combos as $combo)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0">{{ $combo->ten }}</h6>
                                    <small class="text-muted">{{ number_format($combo->gia, 0, ',', '.') }}đ</small>
                                    @if($combo->so_luong > 0)
                                        <div class="text-success">
                                            <small>Còn lại: {{ $combo->so_luong }} cái</small>
                                        </div>
                                    @else
                                        <div class="text-danger">
                                            <small>Hết hàng</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary btn-decrease" 
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
                                           style="width: 50px;"
                                           data-combo-id="{{ $combo->id }}"
                                           {{ $combo->so_luong <= 0 ? 'disabled' : '' }}>
                                    <button class="btn btn-sm btn-outline-primary btn-increase" 
                                            data-combo-id="{{ $combo->id }}"
                                            {{ $combo->so_luong <= 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-info-circle me-1"></i> Hiện không có combo nào
                        </div>
                    @endif
                </div>
            </div>

            <!-- Booking Information -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-check me-2"></i>
                        Thông tin đặt vé
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Selected Seats -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Ghế đã chọn:</label>
                        <div id="selected-seats" class="selected-seats-container p-3 bg-white rounded-3 border">
                            <span class="text-muted">Chưa chọn ghế nào</span>
                        </div>
                    </div>

                    <!-- Selected Combos -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Combo đã chọn:</label>
                        <div id="selected-combos" class="p-3 bg-white rounded-3 border">
                            <span class="text-muted">Chưa chọn combo nào</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Tổng cộng:</h5>
                            <h4 class="mb-0 text-primary" id="total-amount">0đ</h4>
                        </div>
                        <button id="confirm-booking" class="btn btn-success btn-lg w-100" disabled>
                            <i class="bi bi-check-circle me-2"></i>Xác nhận đặt vé
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .selected-seats-container {
        min-height: 60px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .seat-badge {
        display: inline-flex;
        align-items: center;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 16px;
        padding: 4px 12px;
        font-size: 0.85rem;
        color: #212529;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .seat-badge .seat-type {
        font-weight: 600;
        margin-right: 6px;
        color: #495057;
    }
    .seat-badge .seat-number {
        background: #e9ecef;
        padding: 1px 8px;
        border-radius: 10px;
        margin: 0 4px;
        font-family: monospace;
    }
    .seat-badge .remove-seat {
        margin-left: 6px;
        color: #dc3545;
        cursor: pointer;
        font-size: 1.1em;
        line-height: 1;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .seat-badge .remove-seat:hover {
        opacity: 1;
    }
    
    /* Combo styles */
    .combo-badge {
        display: inline-flex;
        align-items: center;
        background: #fff8e1;
        border: 1px solid #ffe082;
        border-radius: 16px;
        padding: 6px 12px;
        margin: 4px;
        font-size: 0.85rem;
    }
    .combo-badge .combo-name {
        font-weight: 600;
        color: #e65100;
        margin-right: 6px;
    }
    .combo-badge .combo-qty {
        background: #ffecb3;
        padding: 1px 8px;
        border-radius: 10px;
        margin: 0 4px;
        font-family: monospace;
    }
    .combo-badge .combo-price {
        color: #2e7d32;
        font-weight: 500;
        margin-left: 4px;
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const seats = document.querySelectorAll('.seat:not(.seat-da-thanh-toan):not(.seat-giu-tam):not(.seat-dat):not(.seat-bao-tri)');
    const selectedSeats = new Set();
    const selectedSeatsContainer = document.getElementById('selected-seats');
    const totalAmountElement = document.getElementById('total-amount');
    const confirmButton = document.getElementById('confirm-booking');
    const ticketPrice = {{ $suatChieu->gia_ve }};
    let comboQuantities = {!! $combos->pluck('so_luong', 'id') !!};
    let comboPrices = {!! $combos->pluck('gia', 'id') !!};

    // Initialize combo quantities
    Object.keys(comboQuantities).forEach(comboId => {
        const input = document.getElementById(`combo-${comboId}-qty`);
        if (input) {
            input.addEventListener('change', updateComboQuantity);
        }
    });

    // Handle seat selection
    function toggleSeatSelection(seatElement) {
        const seatId = seatElement.dataset.gheId;
        const seatNumber = `${seatElement.dataset.hang}${seatElement.dataset.cot}`;
        const seatType = seatElement.dataset.loai;

        if (seatElement.classList.contains('seat-chon')) {
            // Deselect seat
            seatElement.classList.remove('seat-chon');
            selectedSeats.delete(seatId);
        } else {
            // Check seat limit based on type
            const seatCount = seatType === 'doi' ? 2 : 1;
            const currentSeatCount = Array.from(selectedSeats).reduce((total, id) => {
                const element = document.querySelector(`[data-ghe-id="${id}"]`);
                return total + (element && element.dataset.loai === 'doi' ? 2 : 1);
            }, 0);

            if (currentSeatCount + seatCount > 8) {
                alert('Bạn chỉ được chọn tối đa 8 ghế');
                return;
            }

            // Select the seat
            seatElement.classList.add('seat-chon');
            selectedSeats.add(seatId);
        }

        updateSelectedSeats();
        updateTotal();
    }
    
    // Add click event listeners to all seats
    seats.forEach(seat => {
        seat.addEventListener('click', function() {
            toggleSeatSelection(this);
        });
    });

    // Update selected seats display
    function updateSelectedSeats() {
        selectedSeatsContainer.innerHTML = '';
        if (selectedSeats.size === 0) {
            selectedSeatsContainer.innerHTML = '<span class="text-muted">Chưa chọn ghế nào</span>';
            confirmButton.disabled = true;
            return;
        }
        
        // Group seats by type
        const seatsByType = {
            'thuong': [],
            'vip': [],
            'doi': []
        };
        
        selectedSeats.forEach(seatId => {
            const seatElement = document.querySelector(`[data-ghe-id="${seatId}"]`);
            if (seatElement) {
                const type = seatElement.dataset.loai || 'thuong';
                const seatNumber = `${seatElement.dataset.hang}${seatElement.dataset.cot}`;
                seatsByType[type].push(seatNumber);
            }
        });
        
        // Display selected seats with better formatting
        Object.entries(seatsByType).forEach(([type, seats]) => {
            if (seats.length > 0) {
                const typeName = type === 'thuong' ? 'Thường' : (type === 'vip' ? 'VIP' : 'Đôi');
                const typeClass = type === 'thuong' ? 'text-primary' : (type === 'vip' ? 'text-warning' : 'text-success');
                
                // Create a container for this seat type
                const container = document.createElement('div');
                container.className = 'd-flex flex-wrap align-items-center mb-2';
                
                // Add type label
                const typeLabel = document.createElement('span');
                typeLabel.className = `me-2 fw-bold ${typeClass}`;
                typeLabel.textContent = `${typeName}:`;
                container.appendChild(typeLabel);
                
                // Add each seat as a separate badge
                seats.forEach(seatNumber => {
                    const seatBadge = document.createElement('span');
                    seatBadge.className = 'seat-badge me-2 mb-1';
                    seatBadge.innerHTML = `
                        <span class="seat-number">${seatNumber}</span>
                        <span class="remove-seat" data-seat="${seatNumber}">&times;</span>
                    `;
                    container.appendChild(seatBadge);
                });
                
                selectedSeatsContainer.appendChild(container);
                
                // Add event listeners to remove buttons
                container.querySelectorAll('.remove-seat').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const seatNumber = this.dataset.seat;
                        const seatElement = document.querySelector(`[data-hang="${seatNumber[0]}"][data-cot="${seatNumber.slice(1)}"]`);
                        if (seatElement) {
                            seatElement.click(); // Trigger click to deselect
                        }
                    });
                });
            }
        });
        
        confirmButton.disabled = selectedSeats.size === 0;
    }

    // Calculate and update total amount
    function updateTotal() {
        let total = Array.from(selectedSeats).length * ticketPrice;
        
        // Add combo prices
        document.querySelectorAll('[id^="combo-"]').forEach(input => {
            if (input.id.endsWith('-qty')) {
                const comboId = input.dataset.comboId;
                const quantity = parseInt(input.value) || 0;
                total += comboPrices[comboId] * quantity;
            }
        });
        
        totalAmountElement.textContent = total.toLocaleString('vi-VN') + 'đ';
    }

    // Handle combo quantity changes
    function updateComboQuantity(e) {
        const input = e.target;
        const comboId = input.dataset.comboId;
        let value = parseInt(input.value) || 0;
        
        // Validate max quantity
        if (value > comboQuantities[comboId]) {
            value = comboQuantities[comboId];
            input.value = value;
        }
        
        // Update total
        updateTotal();
    }

    // Handle increase/decrease buttons
    document.querySelectorAll('.btn-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const comboId = this.dataset.comboId;
            const input = document.getElementById(`combo-${comboId}-qty`);
            let value = parseInt(input.value) || 0;
            
            if (value < comboQuantities[comboId]) {
                input.value = value + 1;
                updateTotal();
            }
        });
    });

    document.querySelectorAll('.btn-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const comboId = this.dataset.comboId;
            const input = document.getElementById(`combo-${comboId}-qty`);
            let value = parseInt(input.value) || 0;
            
            if (value > 0) {
                input.value = value - 1;
                updateTotal();
            }
        });
    });

    // Handle form submission
    document.getElementById('confirm-booking').addEventListener('click', function() {
        const customerName = document.getElementById('customer-name').value.trim();
        const customerPhone = document.getElementById('customer-phone').value.trim();
        
        if (!customerName || !customerPhone) {
            alert('Vui lòng nhập đầy đủ thông tin khách hàng');
            return;
        }
        
        if (selectedSeats.size === 0) {
            alert('Vui lòng chọn ít nhất một ghế');
            return;
        }
        
        // Prepare data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('suat_chieu_id', '{{ $suatChieu->id }}');
        formData.append('customer_name', customerName);
        formData.append('customer_phone', customerPhone);
        formData.append('customer_email', document.getElementById('customer-email').value.trim());
        
        // Add each seat ID as a separate form field
        Array.from(selectedSeats).forEach((seatId, index) => {
            formData.append(`ghe_ids[${index}]`, seatId);
        });
        
        // Add combos to form data
        const combos = [];
        document.querySelectorAll('[id^="combo-"]').forEach(input => {
            if (input.id.endsWith('-qty')) {
                const comboId = input.dataset.comboId;
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    combos.push({
                        id: comboId,
                        quantity: quantity
                    });
                }
            }
        });
        formData.append('combos', JSON.stringify(combos));
        
        // Show loading state
        const originalButtonText = confirmButton.innerHTML;
        confirmButton.disabled = true;
        confirmButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...';
        
        // Submit form
        fetch('{{ route("staff.donve.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error('Đã xảy ra lỗi. Vui lòng thử lại sau.');
            }
        })
        .then(data => {
            if (data.success) {
                // Show success message
                const successMessage = data.message || 'Đặt vé thành công!';
                alert(successMessage);
                
                // Redirect to the ticket page or print page
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.href = '{{ route("staff.donve.index") }}';
                }
            } else {
                // Show error message
                const errorMessage = data.message || 'Đã xảy ra lỗi khi đặt vé. Vui lòng thử lại.';
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Đã xảy ra lỗi. Vui lòng thử lại sau.');
        })
        .finally(() => {
            // Restore button state
            confirmButton.disabled = false;
            confirmButton.innerHTML = originalButtonText;
        });
    });
});
</script>
@endpush