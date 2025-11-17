@extends('client.layouts.app')

@section('title', 'Đặt vé - ' . $suatChieu->phim->tieu_de)

@section('content')
<div class="container my-5">
    <div class="row">

        {{-- ===================== THÔNG TIN PHIM ===================== --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-film me-2"></i>{{ $suatChieu->phim->tieu_de }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- Poster --}}
                        <div class="col-md-4">
                            @if ($suatChieu->phim->anh_poster)
                                <img src="{{ asset('storage/' . $suatChieu->phim->anh_poster) }}"
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                     style="height: 200px;">
                                    <i class="bi bi-image text-white fs-1"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="col-md-8">
                            <h6 class="text-muted">{{ $suatChieu->phong->rap->ten }}</h6>
                            <p><i class="bi bi-calendar-event me-2"></i>
                                {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('l, d/m/Y') }}
                            </p>
                            <p><i class="bi bi-clock me-2"></i>
                                {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($suatChieu->gio_ket_thuc)->format('H:i') }}
                            </p>
                            <p><i class="bi bi-geo-alt me-2"></i>{{ $suatChieu->phong->ten }}</p>
                            <p><i class="bi bi-tag me-2"></i>{{ number_format($suatChieu->gia_ve) }}đ/vé</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== CHỌN GHẾ ===================== --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-square me-2"></i>Chọn ghế (Tối đa 2 ghế)</h5>
                </div>

                <div class="card-body">

                    {{-- Chú thích --}}
                    <div class="mb-4 d-flex flex-wrap justify-content-center gap-3">
                        <div><span class="legend-box seat-vip"></span> VIP</div>
                        <div><span class="legend-box seat-doi"></span> Đôi</div>
                        <div><span class="legend-box seat-thuong"></span> Thường</div>
                        <div><span class="legend-box seat-da-thanh-toan"></span> Đã đặt</div>
                        <div><span class="legend-box seat-giu-tam"></span> Giữ tạm</div>
                        <div><span class="legend-box seat-chon"></span> Đang chọn</div>
                    </div>

                    {{-- Sơ đồ ghế --}}
                    <div class="seat-map p-4 border rounded bg-light">
                        <div class="screen mb-4">🎥 MÀN HÌNH</div>

                        <div class="d-flex flex-column align-items-center">

                            @foreach ($ghes as $hang => $dsGhe)
                                <div class="d-flex mb-2">

                                    @foreach ($dsGhe as $ghe)
                                        @php
                                            $status = $gheStatuses[$ghe->id] ?? 'hoat_dong';
                                            $isDat = in_array($ghe->id, $gheDaDat);
                                            $isGiuTam = in_array($ghe->id, $giuTamIds);
                                            $isDisabled = false;
                                            $seatClass = 'seat';

                                            if ($isDat) {
                                                $seatClass .= ' seat-da-thanh-toan';
                                                $isDisabled = true;
                                            }
                                            elseif ($isGiuTam) {
                                                $seatClass .= ' seat-giu-tam';
                                                $isDisabled = true;
                                            }
                                            elseif (in_array($status, ['bao_tri', 'vo_hieu_hoa'])) {
                                                $seatClass .= ' seat-dat';
                                                $isDisabled = true;
                                            } else {
                                                $seatClass .= ' seat-' . $ghe->loai;
                                            }
                                        @endphp

                                        <button type="button"
                                            class="{{ $seatClass }} {{ $isDisabled ? 'disabled' : '' }}"
                                            data-ghe-id="{{ $ghe->id }}"
                                            data-hang="{{ $ghe->hang }}"
                                            data-cot="{{ $ghe->cot }}"
                                            data-loai="{{ $ghe->loai }}"
                                            {{ $isDisabled ? 'disabled style=pointer-events:none' : '' }}>
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

        {{-- ===================== THANH TOÁN ===================== --}}
        <div class="col-lg-4">

            <div class="card shadow-sm sticky-top" style="top:20px;">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Thanh toán</h5>
                </div>

                <div class="card-body">

                    {{-- Vé --}}
                    <h6>Vé phim</h6>
                    <div id="ticket-summary"><p class="text-muted">Chưa chọn ghế</p></div>
                    <div class="d-flex justify-content-between">
                        <span>Tổng vé:</span>
                        <span id="ticket-total">0đ</span>
                    </div>

                    <hr>

                    {{-- Combo --}}
                    <h6>Combo & Đồ ăn</h6>
                    <div id="combo-list"></div>

                    @if ($combos->count())
                        @foreach ($combos as $combo)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div class="flex-grow-1">
                                    <strong>{{ $combo->ten }}</strong><br>
                                    <small>{{ number_format($combo->gia) }}đ</small>
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
                    @endif

                    <div class="d-flex justify-content-between mt-2">
                        <span>Tổng combo:</span>
                        <span id="combo-total">0đ</span>
                    </div>

                    <hr>

                    {{-- Voucher --}}
                    <label class="form-label">Mã giảm giá</label>
                    <div class="input-group">
                        <input type="text" id="voucher-code" class="form-control">
                        <button class="btn btn-outline-secondary" id="apply-voucher">Áp dụng</button>
                    </div>
                    <div id="voucher-message" class="mt-1"></div>

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

                    @if (!auth()->check())
                        <p class="text-muted mt-2 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đặt vé.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal xác nhận --}}
<div class="modal fade" id="confirmModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Xác nhận đặt vé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Bạn có chắc muốn đặt?</p>
                <div id="confirm-details"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-danger" id="confirm-book">Xác nhận</button>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- ========================================================= --}}
{{-- ====================== CSS GHẾ =========================== --}}
{{-- ========================================================= --}}
@push('styles')
<style>
.seat {
    width: 45px;
    height: 45px;
    margin: 4px;
    border-radius: 8px;
    border: 1px solid #ccc;
    color:#222;
    background:#d9d9d9;
    font-size: 12px;
    cursor: pointer;
    line-height:45px;
    text-align:center;
    user-select:none;
    transition:.2s;
}
.seat-vip { background:#FFD700; }
.seat-doi { background:#98FB98; }
.seat-thuong { background:#87CEFA; }
.seat-da-thanh-toan { background:#DC3545 !important; color:white; cursor:not-allowed; }
.seat-giu-tam { background:#FFA500 !important; color:white; cursor:not-allowed; }
.seat-dat { background:#888 !important; color:white; cursor:not-allowed; }
.seat-chon { background:#28a745 !important; color:white; }
.seat.disabled { pointer-events:none !important; }

.legend-box {
    display:inline-block; width:25px; height:25px;
    border-radius:6px; border:1px solid #ccc;
}

.screen {
    background:#222; color:#fff;
    width:60%; text-align:center;
    padding:10px 0; border-radius:5px;
    margin:auto; font-weight:bold;
    letter-spacing:2px;
}

.seat:not(.disabled):hover {
    transform: scale(1.08);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.selected-seat-badge {
    background:#28a745; color:white;
    padding:4px 8px;
    border-radius:4px;
    font-size:12px;
}
</style>
@endpush

{{-- ========================================================= --}}
{{-- ====================== JAVASCRIPT ======================== --}}
{{-- ========================================================= --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

let selectedSeats = [];
let comboQuantities = {};
let voucherDiscount = 0;
let baseTicketPrice = {{ $suatChieu->gia_ve }};
let combosData = @json($combos);

$(document).ready(function() {

    // Init combo qty
    combosData.forEach(c => comboQuantities[c.id] = 0);

    // ========================= CLICK GHẾ =========================
    $('.seat-map').on('click', '.seat:not(.disabled)', function() {

        const id = $(this).data('ghe-id');

        if ($(this).hasClass('seat-chon')) {
            $(this).removeClass('seat-chon');
            selectedSeats = selectedSeats.filter(i => i !== id);
        } else {
            if (selectedSeats.length >= 2) {
                alert("Chỉ chọn tối đa 2 ghế");
                return;
            }
            $(this).addClass('seat-chon');
            selectedSeats.push(id);
        }

        updateSelectedSeatsDisplay();
        updateTotals();
        toggleBookButton();
    });

    // ========================= COMBO + - ========================
    $('.plus-btn').click(function () {
        const id = $(this).data('combo-id');
        comboQuantities[id]++;
        updateComboDisplay(id);
        updateTotals();
    });

    $('.minus-btn').click(function () {
        const id = $(this).data('combo-id');
        if (comboQuantities[id] > 0) {
            comboQuantities[id]--;
            updateComboDisplay(id);
            updateTotals();
        }
    });

    // ========================= APPLY VOUCHER ====================
    $('#apply-voucher').click(function () {
        const code = $('#voucher-code').val().trim();
        if (!code) {
            $('#voucher-message').html(`<small class="text-danger">Nhập mã giảm giá</small>`);
            return;
        }

        $.post('{{ route("booking.check-voucher") }}', {
            _token:'{{ csrf_token() }}',
            code,
            suat_chieu_id: {{ $suatChieu->id }},
            ghe_ids: selectedSeats,
            combo_items: getComboItems()
        })
        .done(res => {
            if (res.success) {
                voucherDiscount = res.discount;
                $('#voucher-message').html(`<small class="text-success">Giảm ${formatCurrency(res.discount)}</small>`);
            } else {
                voucherDiscount = 0;
                $('#voucher-message').html(`<small class="text-danger">${res.message}</small>`);
            }
            updateTotals();
        });
    });

    // ========================= NÚT ĐẶT VÉ =========================
    $('#book-btn').click(function(){
        if (!selectedSeats.length) return alert("Chọn ghế trước");

        let html = `
            <div><strong>Ghế:</strong> ${getSelectedSeatLabels().join(', ')}</div>
            <div><strong>Tổng:</strong> ${formatCurrency(calculateTotal())}</div>
        `;

        $('#confirm-details').html(html);
        $('#confirmModal').modal('show');
    });

    // ========================= XÁC NHẬN ĐẶT ========================
    $('#confirm-book').click(function(){

        $('#confirm-book').prop('disabled', true).text("Đang xử lý...");

        $.post('{{ route("booking.holdSeats") }}', {
            _token:'{{ csrf_token() }}',
            suat_chieu_id:{{ $suatChieu->id }},
            ghe_ids:selectedSeats
        })
        .done(res=>{
            if (res.success) {
                $.post('{{ route("booking.store") }}', {
                    _token:'{{ csrf_token() }}',
                    suat_chieu_id:{{ $suatChieu->id }},
                    ghe_ids:selectedSeats,
                    combo_items:getComboItems(),
                    ma_giam_gia:$('#voucher-code').val()
                })
                .done(res=>{
                    if (res.success) location.href = res.redirect;
                    else alert(res.message);
                })
                .always(()=>{
                    $('#confirm-book').prop('disabled', false).text("Xác nhận");
                    $('#confirmModal').modal('hide');
                });
            } else {
                alert(res.message);
                $('#confirm-book').prop('disabled', false).text("Xác nhận");
            }
        });
    });

});


// ======================= HÀM XỬ LÝ GHẾ =========================

function getSelectedSeatLabels() {
    return selectedSeats.map(id=>{
        const seat = $('.seat[data-ghe-id="'+id+'"]');
        return seat.data('hang') + seat.data('cot');
    });
}

function updateSelectedSeatsDisplay() {
    let list = getSelectedSeatLabels();

    $('#selected-seats').text(list.length ? list.join(', ') : "Chưa chọn ghế nào");

    let html = '';
    list.forEach(l => html += `<span class="badge bg-success selected-seat-badge">${l}</span>`);
    $('#selected-seats-list').html(html);
}

// ======================= COMBO =========================

function updateComboDisplay(id) {
    $('.combo-quantity[data-combo-id="'+id+'"]').text(comboQuantities[id]);
}

function getComboItems() {
    return Object.keys(comboQuantities)
        .filter(id => comboQuantities[id] > 0)
        .map(id => ({ combo_id: id, so_luong: comboQuantities[id] }));
}

// ======================= TÍNH TIỀN =========================

function getSeatPrice(type) {
    if (type === 'vip') return baseTicketPrice * 1.5;
    if (type === 'doi') return baseTicketPrice * 2;
    return baseTicketPrice;
}

function calculateTotal() {
    let ticket = 0;

    selectedSeats.forEach(id=>{
        const seat = $('.seat[data-ghe-id="'+id+'"]');
        ticket += getSeatPrice(seat.data('loai'));
    });

    let combo = getComboItems().reduce((sum, item)=>{
        let c = combosData.find(x => x.id == item.combo_id);
        return sum + c.gia * item.so_luong;
    }, 0);

    return ticket + combo - voucherDiscount;
}

function updateTotals() {
    let seatHtml = "";
    let ticket = 0;

    selectedSeats.forEach(id=>{
        const seat = $('.seat[data-ghe-id="'+id+'"]');
        const label = seat.data('hang') + seat.data('cot');
        const price = getSeatPrice(seat.data('loai'));

        ticket += price;
        seatHtml += `<div>${label} = ${formatCurrency(price)}</div>`;
    });

    $('#ticket-summary').html(seatHtml || `<p class="text-muted">Chưa chọn ghế</p>`);
    $('#ticket-total').text(formatCurrency(ticket));

    // Combo
    let combo = 0;
    let comboHtml = "";
    getComboItems().forEach(item=>{

        let c = combosData.find(x => x.id == item.combo_id);
        combo += c.gia * item.so_luong;

        comboHtml += `<div>${c.ten} x${item.so_luong} = ${formatCurrency(c.gia * item.so_luong)}</div>`;
    });

    $('#combo-list').html(comboHtml);
    $('#combo-total').text(formatCurrency(combo));

    // Tổng chung
    $('#grand-total').text(formatCurrency(calculateTotal()));
}

function toggleBookButton() {
    const loggedIn = {{ auth()->check() ? "true" : "false" }};
    $('#book-btn').prop('disabled', !selectedSeats.length || !loggedIn);
}

function formatCurrency(a) {
    return new Intl.NumberFormat('vi-VN', {style:'currency', currency:'VND'}).format(a);
}

</script>
@endpush
