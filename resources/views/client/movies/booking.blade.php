@extends('client.layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">🎬 Đặt vé – Phòng: {{ $phong->ten }}</h3>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Legend --}}
    <div class="mb-3">
        <span class="legend-item seat-thuong"></span> Ghế thường
        <span class="legend-item seat-vip"></span> Ghế VIP
        <span class="legend-item seat-doi"></span> Ghế đôi
        <span class="legend-item seat-bao-tri"></span> Bảo trì
        <span class="legend-item seat-dat"></span> Đã đặt
        <span class="legend-item seat-chon"></span> Ghế chọn
    </div>

    <form method="POST" action="{{ route('booking.store') }}">
        @csrf
        <input type="hidden" name="suat_chieu_id" value="{{ $suatChieu->id }}">
        <div id="selectedSeats"></div>

        {{-- Sơ đồ ghế --}}
        <div class="card p-3 overflow-auto mb-4">
            @php
            $matrix2D = [];
            $nextId = 1; // Nếu ma_tran chưa có id
            foreach ($matrix as $seat) {
                if(!isset($seat['id'])) $seat['id'] = $nextId++;
                $hang = $seat['hang'] ?? 'X';
                $cot = $seat['cot'] ?? 0;
                $matrix2D[$hang][$cot] = $seat;
            }
            ksort($matrix2D);
            foreach ($matrix2D as &$row) ksort($row);
            @endphp

            @foreach($matrix2D as $row)
            <div class="d-flex mb-2 justify-content-center">
                @foreach($row as $seat)
                    @php
                        $loai = $seat['loai'] ?? 'thuong';
                        $class = match($loai){
                            'vip'=>'seat-vip',
                            'doi'=>'seat-doi',
                            default=>'seat-thuong'
                        };
                        $trangThai = $seat['trang_thai'] ?? 'hoat_dong';
                        $gheId = $seat['id'];
                        $giaGhe = $seat['gia'] ?? ($suatChieu->gia_ve ?? 0);

                        if(isset($trangThaiGhe[$gheId]) && $trangThaiGhe[$gheId]==='da_dat') $trangThai='da_dat';
                        if($trangThai==='bao_tri') $class.=' seat-bao-tri';
                        if($trangThai==='da_dat') $class.=' seat-dat';
                    @endphp

                    <div class="seat {{ $class }}" data-id="{{ $gheId }}" data-gia="{{ $giaGhe }}" data-trangthai="{{ $trangThai }}">
                        {{ $seat['hang'] }}-{{ $seat['cot'] }}
                    </div>
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Combo --}}
        <h5 class="mt-4 mb-2">🍿 Combo đồ ăn</h5>
        <div class="d-flex flex-wrap gap-3 mb-3">
            @foreach($combos as $combo)
            <div class="combo-card card p-3 shadow-sm text-center position-relative">
                <div class="combo-name fw-bold mb-2">{{ $combo->ten }}</div>
                <div class="combo-price text-primary mb-2" data-gia="{{ $combo->gia }}">{{ number_format($combo->gia,0,',','.') }}đ</div>
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-decrease">-</button>
                    <input type="number" min="0" value="0" class="form-control form-control-sm text-center combo-qty" name="combo_quantities[{{ $combo->id }}]" style="width:60px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-increase">+</button>
                </div>
            </div>
            @endforeach
        </div>
{{-- Mã giảm giá --}}
<div class="card p-3 mb-3">
    <h5 class="mb-2">🎁 Mã giảm giá</h5>

    <div class="input-group">
        <input type="text" id="couponCode" class="form-control" placeholder="Nhập mã giảm giá...">
        <button type="button" id="applyCoupon" class="btn btn-success">Áp dụng</button>
    </div>
    <div id="couponMessage" class="mt-2 text-danger small"></div>

    <input type="hidden" name="coupon_id" id="couponId">
    <input type="hidden" name="coupon_discount" id="couponDiscount">
</div>
<div class="card p-3 mb-3">
    <h5 class="mb-1">Tạm tính: <span id="subtotalPrice">0</span> đ</h5>

    <h6 class="text-success mb-2">
        Giảm giá: <span id="discountAmount">0</span> đ
    </h6>

    <h4 class="fw-bold">
        Tổng tiền: <span id="totalPrice">0</span> đ
    </h4>
</div>

        <button type="submit" class="btn btn-primary mt-2">Đặt vé</button>
    </form>
</div>
@endsection

@push('styles')
<style>
.seat{width:55px;height:55px;border:1px solid #ccc;margin:3px;text-align:center;line-height:55px;color:white;cursor:pointer;font-weight:bold;border-radius:6px;transition:0.2s;flex-shrink:0;}
.seat:hover{transform:scale(1.15);box-shadow:0 0 8px #00000040;}
.seat-thuong{background:#91b8f3;}
.seat-vip{background:#94e774;}
.seat-doi{background:#4dbd72;}
.seat-bao-tri{background:#6c757d !important;}
.seat-dat{background:#ff7f50 !important;}
.seat-chon{outline:3px solid yellow;}
.legend-item{display:inline-block;width:25px;height:25px;margin-right:8px;vertical-align:middle;border-radius:4px;}
.combo-card{cursor:pointer;width:150px;position:relative;display:inline-block;transition:transform 0.2s,box-shadow 0.2s;}
.combo-card:hover{transform:scale(1.05);box-shadow:0 4px 15px rgba(0,0,0,0.2);}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectedSeatsContainer = document.getElementById('selectedSeats');
    let couponDiscount = 0;

    function calcSubtotal() {
        let subtotal = 0;
        document.querySelectorAll('.seat.seat-chon').forEach(seat => {
            subtotal += parseFloat(seat.dataset.gia) || 0;
        });
        document.querySelectorAll('.combo-card').forEach(card => {
            const qty = parseInt(card.querySelector('.combo-qty').value) || 0;
            const price = parseFloat(card.querySelector('.combo-price').dataset.gia) || 0;
            subtotal += qty * price;
        });
        return subtotal;
    }

    function updateTotal() {
        let subtotal = calcSubtotal();
        document.getElementById('subtotalPrice').innerText = new Intl.NumberFormat().format(subtotal);
        document.getElementById('discountAmount').innerText = new Intl.NumberFormat().format(couponDiscount);
        let total = Math.max(0, subtotal - couponDiscount);
        document.getElementById('totalPrice').innerText = new Intl.NumberFormat().format(total);
    }

    function resetCoupon() {
        couponDiscount = 0;
        document.getElementById('couponId').value = '';
        document.getElementById('couponDiscount').value = '';
        document.getElementById('couponMessage').innerHTML = '';
    }

    // Click chọn ghế
    document.querySelectorAll('.seat').forEach(seat => {
        seat.addEventListener('click', function () {
            let status = this.dataset.trangthai;
            if (status === 'bao_tri' || status === 'da_dat') return;
            status = (status === 'chon') ? 'hoat_dong' : 'chon';
            this.dataset.trangthai = status;
            this.classList.toggle('seat-chon', status === 'chon');

            let id = this.dataset.id;
            if (status === 'chon' && id) {
                if (!selectedSeatsContainer.querySelector('input[data-seatid="' + id + '"]')) {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ghe_ids[]';
                    input.value = id;
                    input.dataset.seatid = id;
                    selectedSeatsContainer.appendChild(input);
                }
            } else {
                let input = selectedSeatsContainer.querySelector('input[data-seatid="' + id + '"]');
                if (input) input.remove();
            }

            resetCoupon();
            updateTotal();
        });
    });

    // Combo
    document.querySelectorAll('.combo-card').forEach(card => {
        const btnIncrease = card.querySelector('.btn-increase');
        const btnDecrease = card.querySelector('.btn-decrease');
        const input = card.querySelector('.combo-qty');

        btnIncrease.addEventListener('click', () => {
            input.value = parseInt(input.value) || 0;
            input.value = parseInt(input.value) + 1;
            resetCoupon();
            updateTotal();
        });
        btnDecrease.addEventListener('click', () => {
            input.value = Math.max(0, (parseInt(input.value) || 0) - 1);
            resetCoupon();
            updateTotal();
        });
        input.addEventListener('input', () => {
            resetCoupon();
            updateTotal();
        });
    });

    // Áp dụng mã giảm giá
    document.getElementById('applyCoupon').addEventListener('click', function () {
        const code = document.getElementById('couponCode').value.trim();
        const subtotal = calcSubtotal();

        if (!code) {
            document.getElementById('couponMessage').innerText = "Vui lòng nhập mã!";
            return;
        }

        fetch("{{ route('coupon.check') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                code: code,
                subtotal: subtotal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.status) {
                resetCoupon();
                document.getElementById('couponMessage').innerHTML = `<span class="text-danger">${data.message}</span>`;
            } else {
                couponDiscount = data.discount;
                document.getElementById('couponId').value = data.coupon_id;
                document.getElementById('couponDiscount').value = data.discount;
                document.getElementById('couponMessage').innerHTML = `<span class="text-success">${data.message}</span>`;
            }
            updateTotal();
        })
        .catch(err => {
            console.error(err);
            document.getElementById('couponMessage').innerText = "Lỗi server, vui lòng thử lại!";
        });
    });

    updateTotal();
});
</script>

@endpush
