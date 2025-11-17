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
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($suatChieu->phim->anh_poster)
                                <img src="{{ asset('storage/' . $suatChieu->phim->anh_poster) }}"
                                     alt="{{ $suatChieu->phim->tieu_de }}"
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                     style="height: 200px;">
                                    <i class="bi bi-image text-white fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted mb-2">{{ $suatChieu->phong->rap->ten }}</h6>
                            <p class="mb-1">
                                <i class="bi bi-calendar-event me-2"></i>
                                {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('l, d/m/Y') }}
                            </p>
                            <p class="mb-1">
                                <i class="bi bi-clock me-2"></i>
                                {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($suatChieu->gio_ket_thuc)->format('H:i') }}
                            </p>
                            <p class="mb-1">
                                <i class="bi bi-geo-alt me-2"></i>
                                {{ $suatChieu->phong->ten }}
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-tag me-2"></i>
                                {{ number_format($suatChieu->gia_ve, 0, ',', '.') }}đ/vé
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chọn ghế --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-square me-2"></i>
                        Chọn ghế (Tối đa 2 ghế cho thanh toán tại quầy)
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
                        <div><span class="legend-box seat-chon"></span> Ghế đã chọn</div>
                    </div>

                    {{-- Sơ đồ ghế --}}
                    <div class="seat-map p-4 border rounded bg-light">
                        <div class="screen mb-4">🎥 MÀN HÌNH CHIẾU</div>

                        <div class="d-flex flex-column align-items-center">
                            @foreach ($ghes as $hang => $danhSachGhe)
                                <div class="d-flex mb-2">
                                    @foreach ($danhSachGhe as $ghe)
                                        @php
                                            $classes = 'seat seat-' . $ghe->loai;
                                            $trangthai = $gheStatuses[$ghe->id] ?? 'hoat_dong';
                                            $disabled = false;

                                            if(in_array($ghe->id, $gheDaDat)){
                                                $classes = 'seat seat-da-thanh-toan disabled';
                                                $trangthai = 'da_dat';
                                                $disabled = true;
                                            } elseif(in_array($ghe->id, $giuTamIds)){
                                                $classes = 'seat seat-giu-tam disabled';
                                                $trangthai = 'giu_tam';
                                                $disabled = true;
                                            } elseif($trangthai === 'bao_tri' || $trangthai === 'vo_hieu_hoa'){
                                                $classes = 'seat seat-dat disabled';
                                                $disabled = true;
                                            }
                                        @endphp

                                        <button type="button" class="seat {{ $classes }} {{ $disabled ? 'disabled' : '' }}"
                                                data-ghe-id="{{ $ghe->id }}"
                                                data-hang="{{ $ghe->hang }}"
                                                data-cot="{{ $ghe->cot }}"
                                                data-loai="{{ $ghe->loai }}"
                                                data-trangthai="{{ $trangthai }}"
                                                {{ $disabled ? 'disabled="disabled" style="pointer-events: none;"' : '' }}>
                                            {{ $ghe->hang }}{{ $ghe->cot }}
                                        </button>
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
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                        <div class="flex-grow-1">
                                            <strong>{{ $combo->ten }}</strong><br>
                                            <small class="text-muted">{{ number_format($combo->gia, 0, ',', '.') }}đ</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-sm btn-outline-secondary minus-btn" data-combo-id="{{ $combo->id }}">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <span class="mx-2 combo-quantity fw-bold" data-combo-id="{{ $combo->id }}">0</span>
                                            <button class="btn btn-sm btn-outline-secondary plus-btn" data-combo-id="{{ $combo->id }}">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mt-2">Không có combo nào khả dụng</p>
                        @endif
                        <div class="d-flex justify-content-between mt-2">
                            <span>Tổng combo:</span>
                            <span id="combo-total">0đ</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Mã giảm giá --}}
                    <div class="mb-3">
                        <label for="voucher-code" class="form-label">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="voucher-code" placeholder="Nhập mã giảm giá">
                            <button class="btn btn-outline-secondary" type="button" id="apply-voucher">Áp dụng</button>
                        </div>
                        <div id="voucher-message" class="mt-1"></div>
                    </div>

                    <hr>

                    {{-- Tổng cộng --}}
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <span>Tổng cộng:</span>
                        <span id="grand-total">0đ</span>
                    </div>

                    {{-- Nút đặt vé --}}
                    <button class="btn btn-danger w-100 mt-3" id="book-btn" disabled>
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
@endsection

@push('styles')
<style>
.seat {
    width: 45px;
    height: 45px;
    margin: 4px;
    border-radius: 8px;
    border: 1px solid #ccc;
    text-align: center;
    line-height: 45px;
    font-size: 12px;
    cursor: pointer;
    font-weight: 500;
    color: #222;
    user-select: none;
    transition: all 0.2s;
}
.seat-vip { background-color: #FFD700; }
.seat-doi { background-color: #98FB98; }
.seat-thuong { background-color: #87CEFA; }
.seat-dat { background-color: #FF6347 !important; cursor: not-allowed; }
.seat-da-thanh-toan { background-color: #DC3545 !important; cursor: not-allowed; pointer-events: none; }
.seat-giu-tam { background-color: #FFA500 !important; cursor: not-allowed; pointer-events: none; }
.seat-chon { background-color: #28a745 !important; color: white; }
.seat.disabled { cursor: not-allowed !important; pointer-events: none; }

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

.seat:not(.disabled):hover {
    transform: scale(1.08);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.selected-seat-badge {
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
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

$(document).ready(function() {
    // Khởi tạo combo quantities
    combosData.forEach(combo => {
        comboQuantities[combo.id] = 0;
    });

    // Click ghế
    $('.seat-map').on('click', '.seat:not(.disabled)', function() {
        const gheId = $(this).data('ghe-id');
        const hang = $(this).data('hang');
        const cot = $(this).data('cot');
        const seatLabel = hang + cot;

        if ($(this).hasClass('seat-chon')) {
            // Bỏ chọn
            $(this).removeClass('seat-chon');
            selectedSeats = selectedSeats.filter(id => id !== gheId);
        } else {
            // Chọn (kiểm tra tối đa 2 ghế cho thanh toán tại quầy)
            if (selectedSeats.length >= 2) {
                alert('Chỉ được chọn tối đa 2 ghế cho thanh toán tại quầy!');
                return;
            }
            $(this).addClass('seat-chon');
            selectedSeats.push(gheId);
        }

        updateSelectedSeatsDisplay();
        updateTotals();
        invalidateAppliedVoucher();
        toggleBookButton();
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

    // Áp dụng voucher
    $('#apply-voucher').click(function() {
        const code = $('#voucher-code').val().trim();
        if (!code) {
            $('#voucher-message').html('<small class="text-danger">Vui lòng nhập mã giảm giá</small>');
            return;
        }

        // Gửi AJAX để kiểm tra voucher
        $.post('{{ route("booking.check-voucher") }}', {
            _token: '{{ csrf_token() }}',
            code: code,
            suat_chieu_id: {{ $suatChieu->id }},
            ghe_ids: selectedSeats,
            combo_items: getComboItems()
        })
        .done(function(response) {
            if (response.success) {
                voucherDiscount = response.discount;
                appliedVoucherNdId = response.voucher_nd_id || null;
                appliedCode = code;
                $('#voucher-message').html('<small class="text-success">Áp dụng thành công: -' + formatCurrency(response.discount) + '</small>');
                updateTotals();
            } else {
                voucherDiscount = 0;
                appliedVoucherNdId = null;
                appliedCode = null;
                $('#voucher-message').html('<small class="text-danger">' + response.message + '</small>');
                updateTotals();
            }
        })
        .fail(function() {
            $('#voucher-message').html('<small class="text-danger">Lỗi kết nối</small>');
        });
    });

    // Đặt vé
    $('#book-btn').click(function() {
        if (selectedSeats.length === 0) {
            alert('Vui lòng chọn ít nhất 1 ghế!');
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

    // Xác nhận đặt vé
    $('#confirm-book').click(function() {
        $('#confirm-book').prop('disabled', true).text('Đang xử lý...');

        // Giữ tạm ghế trước
        $.post('{{ route("booking.holdSeats") }}', {
            _token: '{{ csrf_token() }}',
            suat_chieu_id: {{ $suatChieu->id }},
            ghe_ids: selectedSeats
        })
        .done(function(response) {
            if (response.success) {
                // Đặt vé
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
                        window.location.href = response.redirect;
                    } else {
                        alert(response.message);
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }
                })
                .fail(function() {
                    alert('Có lỗi xảy ra khi đặt vé!');
                })
                .always(function() {
                    $('#confirm-book').prop('disabled', false).text('Xác nhận đặt vé');
                    $('#confirmModal').modal('hide');
                });
            } else {
                alert(response.message);
                $('#confirm-book').prop('disabled', false).text('Xác nhận đặt vé');
                $('#confirmModal').modal('hide');
            }
        })
        .fail(function() {
            alert('Có lỗi xảy ra khi giữ ghế!');
            $('#confirm-book').prop('disabled', false).text('Xác nhận đặt vé');
            $('#confirmModal').modal('hide');
        });
    });
});

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
        const seat = $('.seat[data-ghe-id="' + gheId + '"]');
        return seat.data('hang') + seat.data('cot');
    });
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
    switch(seatType) {
        case 'vip':
            return baseTicketPrice * 1.5;
        case 'doi':
            return baseTicketPrice * 2;
        default:
            return baseTicketPrice;
    }
}

function calculateTotal() {
    let ticketTotal = 0;
    selectedSeats.forEach(gheId => {
        const seat = $('.seat[data-ghe-id="' + gheId + '"]');
        const seatType = seat.data('loai');
        ticketTotal += getSeatPrice(seatType);
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
            const seat = $('.seat[data-ghe-id="' + gheId + '"]');
            const seatType = seat.data('loai');
            const seatLabel = seat.data('hang') + seat.data('cot');
            const price = getSeatPrice(seatType);
            ticketTotal += price;
            ticketSummaryHtml += '<div>' + seatLabel + ' (' + getSeatTypeName(seatType) + ') = ' + formatCurrency(price) + '</div>';
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
</script>
@endpush
