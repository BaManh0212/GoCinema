@extends('staff.layouts.staff')

@section('title', 'Thanh toán - Đơn Đặt Vé Tại Quầy')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>
                        Thanh Toán Đơn Đặt Vé
                    </h3>
                </div>
                <div class="card-body p-5">
                    <!-- Thông tin đơn hàng -->
                    <div class="mb-4">
                        <h5 class="mb-3">Thông Tin Đơn Hàng</h5>
                        <div class="bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Mã đơn:</strong> 
                                        <span class="badge bg-info">{{ $donDatVe->ma_don }}</span>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Phim:</strong> {{ $donDatVe->suatChieu->phim->tieu_de }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Suất chiếu:</strong> 
                                        {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>Phòng:</strong> {{ $donDatVe->suatChieu->phong->ten }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách ghế -->
                    <div class="mb-4">
                        <h5 class="mb-3">Ghế Đã Chọn</h5>
                        <div class="bg-light p-3 rounded">
                            @php
                                $seatsByType = [];
                                foreach ($donDatVe->chiTietVes as $ct) {
                                    $type = $ct->loai_ghe ?? 'thuong';
                                    if (!isset($seatsByType[$type])) {
                                        $seatsByType[$type] = [];
                                    }
                                    $seatsByType[$type][] = $ct->ghe->hang . $ct->ghe->cot;
                                }
                            @endphp
                            
                            @foreach($seatsByType as $type => $seats)
                                <div class="mb-2">
                                    <strong>{{ $type === 'vip' ? 'VIP' : ($type === 'doi' ? 'Đôi' : 'Thường') }}:</strong>
                                    {{ implode(', ', $seats) }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Combo nếu có -->
                    @if($combos->count() > 0)
                    <div class="mb-4">
                        <h5 class="mb-3">Combo Đã Chọn</h5>
                        <div class="bg-light p-3 rounded">
                            @foreach($combos as $combo)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ $combo->ten }} (x{{ $combo->so_luong }})</span>
                                    <strong>{{ number_format($combo->gia * $combo->so_luong, 0, ',', '.') }}đ</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Tổng tiền -->
                    <div class="mb-5">
                        <div class="bg-primary bg-opacity-10 border-left border-primary p-3 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Tổng Tiền:</h5>
                                <h3 class="mb-0 text-primary">{{ number_format($donDatVe->tong_tien, 0, ',', '.') }}đ</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="mb-4">
                        <h5 class="mb-3">Phương Thức Thanh Toán</h5>
                        <form id="paymentForm" action="{{ route('staff.donve.processPayment', $donDatVe->id) }}" method="POST">
                            @csrf
                            <div class="card border mb-3">
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" value="cash" id="cash" checked>
                                        <label class="form-check-label" for="cash">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-cash-coin me-3" style="font-size: 32px; color: #28a745;"></i>
                                                <div>
                                                    <p class="mb-0 fw-bold">Thanh Toán Tiền Mặt</p>
                                                    <small class="text-muted">Thanh toán ngay tại quầy</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" value="momo" id="momo">
                                        <label class="form-check-label" for="momo">
                                            <div class="d-flex align-items-center">
                                                <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square-1024x1024.png" alt="MoMo" class="me-3" style="width: 40px; height: 40px;">
                                                <div>
                                                    <p class="mb-0 fw-bold">Ví MoMo</p>
                                                    <small class="text-muted">Thanh toán qua ví MoMo</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" id="payButton" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Thanh Toán {{ number_format($donDatVe->tong_tien, 0, ',', '.') }}đ
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Nút quay lại -->
                    <div class="text-center">
                        <a href="{{ route('staff.donve.confirm', $donDatVe->id) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Quay Lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');

    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const url = this.getAttribute('action');
        const paymentMethod = formData.get('payment_method');

        // Disable button
        payButton.disabled = true;
        const originalText = payButton.innerHTML;
        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (paymentMethod === 'momo' && data.payUrl) {
                    // Redirect to MoMo payment page
                    window.location.href = data.payUrl;
                } else if (paymentMethod === 'cash' && data.redirect) {
                    // Redirect to booking confirmation page
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Thanh toán thành công!');
                    window.location.href = data.redirect || '{{ route("staff.donve.index") }}';
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                payButton.disabled = false;
                payButton.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
            payButton.disabled = false;
            payButton.innerHTML = originalText;
        });
    });
});
</script>
@endsection
