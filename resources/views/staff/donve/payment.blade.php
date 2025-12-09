@extends('staff.layouts.staff')

@section('title', 'Thanh toán - Đơn Đặt Vé Tại Quầy')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-9">
            <!-- Thông tin đơn hàng -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-info-circle me-2"></i>
                        Thông Tin Đơn Hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <small class="text-muted d-block mb-1">Mã Đơn</small>
                                <h6 class="mb-3">
                                    <span class="badge bg-info fs-6">{{ $donDatVe->ma_don }}</span>
                                </h6>
                                <small class="text-muted d-block mb-1">Phim</small>
                                <p class="mb-0 fw-500">{{ $donDatVe->suatChieu->phim->tieu_de }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <small class="text-muted d-block mb-1">Suất Chiếu</small>
                                <p class="mb-3 fw-500">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('d/m/Y') }}
                                    <span class="badge bg-light text-dark ms-2">{{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('H:i') }}</span>
                                </p>
                                <small class="text-muted d-block mb-1">Phòng Chiếu</small>
                                <p class="mb-0 fw-500">
                                    <i class="bi bi-door-closed me-2 text-primary"></i>
                                    {{ $donDatVe->suatChieu->phong->ten }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách ghế -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-chair me-2"></i>
                        Ghế Đã Chọn
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $seatsByType = [];
                        $totalSeats = 0;
                        $seatPrices = [];
                        
                        foreach ($donDatVe->chiTietVes as $ct) {
                            $type = $ct->loai_ghe ?? 'thuong';
                            if (!isset($seatsByType[$type])) {
                                $seatsByType[$type] = [];
                                $seatPrices[$type] = [];
                            }
                            $seatsByType[$type][] = $ct->ghe->hang . $ct->ghe->cot;
                            $seatPrices[$type][] = (int)$ct->gia;
                            $totalSeats++;
                        }
                    @endphp
                    
                    @foreach($seatsByType as $type => $seats)
                        <div class="mb-4">
                            <!-- Type header -->
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2" style="border-bottom: 2px solid {{ $type === 'vip' ? '#FFD700' : ($type === 'doi' ? '#98FB98' : '#87CEFA') }};">
                                <div>
                                    <span class="badge" style="background: {{ $type === 'vip' ? '#FFD700' : ($type === 'doi' ? '#98FB98' : '#87CEFA') }}; color: #000; font-weight: 700; padding: 0.6rem 1rem;">
                                        {{ $type === 'vip' ? 'VIP' : ($type === 'doi' ? 'ĐÔI' : 'THƯỜNG') }}
                                    </span>
                                    <small class="text-muted ms-3">{{ count($seats) }} ghế</small>
                                </div>
                            </div>
                            
                            <!-- Seats with prices -->
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                        @foreach($seats as $index => $seat)
                                            <tr>
                                                <td class="ps-0">
                                                    <span class="badge bg-white">{{ $seat }}</span>
                                                </td>
                                                <td class="text-end pe-0">
                                                    <span class="fw-bold text-dark">{{ number_format($seatPrices[$type][$index], 0, ',', '.') }}đ</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Subtotal -->
                            <div class="text-end pt-2" style="border-top: 1px solid #dee2e6;">
                                <small class="text-muted">Cộng:</small>
                                <span class="fw-bold text-success ms-3" style="font-size: 1.1rem;">{{ number_format(array_sum($seatPrices[$type]), 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Combo nếu có -->
            @if($combos->count() > 0)
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-cup-hot me-2"></i>
                        Combo & Đồ Ăn
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @foreach($combos as $combo)
                                    <tr>
                                        <td class="fw-500">{{ $combo->ten }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-light text-dark">x{{ $combo->so_luong }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($combo->gia * $combo->so_luong, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar thanh toán -->
        <div class="col-lg-3">
            <!-- Tóm tắt giá -->
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-receipt me-2"></i>
                        Chi Tiết Thanh Toán
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Giá vé -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Vé ({{ $totalSeats }} ghế)</small>
                            <small class="fw-500">
                                @php
                                    $ticketTotal = 0;
                                    foreach ($donDatVe->chiTietVes as $ct) {
                                        $ticketTotal += $ct->gia;
                                    }
                                @endphp
                                {{ number_format($ticketTotal, 0, ',', '.') }}đ
                            </small>
                        </div>
                    </div>

                    <!-- Combo -->
                    @if($combos->count() > 0)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Combo</small>
                            <small class="fw-500">
                                @php
                                    $comboTotal = 0;
                                    foreach ($combos as $combo) {
                                        $comboTotal += $combo->gia * $combo->so_luong;
                                    }
                                @endphp
                                {{ number_format($comboTotal, 0, ',', '.') }}đ
                            </small>
                        </div>
                    </div>
                    @endif

                    <!-- Tổng tiền -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Tổng Cộng</h6>
                            <h4 class="mb-0 text-success">{{ number_format($donDatVe->tong_tien, 0, ',', '.') }}đ</h4>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <form id="paymentForm" action="{{ route('staff.donve.processPayment', $donDatVe->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold mb-3">Chọn Phương Thức</label>
                            
                            <!-- Thanh toán tiền mặt -->
                            <div class="payment-method mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" value="cash" id="cash" checked>
                                <label class="form-check-label w-100" for="cash">
                                    <div class="p-3 border rounded-2 cursor-pointer payment-option">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-cash-coin fs-5 text-success me-3"></i>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-bold">Tiền Mặt</p>
                                                <small class="text-muted">Thanh toán tại quầy</small>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- MoMo -->
                            <div class="payment-method mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" value="momo" id="momo">
                                <label class="form-check-label w-100" for="momo">
                                    <div class="p-3 border rounded-2 cursor-pointer payment-option">
                                        <div class="d-flex align-items-center">
                                            <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square-1024x1024.png" alt="MoMo" class="me-3" style="width: 35px; height: 35px; border-radius: 4px;">
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-bold">Ví MoMo</p>
                                                <small class="text-muted">Thanh toán qua ứng dụng</small>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" id="payButton" class="btn btn-success btn-lg w-100 rounded-2 fw-bold">
                            <i class="bi bi-check-circle me-2"></i>
                            Thanh Toán Ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient {
        background-attachment: fixed;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .info-item {
        padding: 8px 0;
    }
    
    .info-item small {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .payment-option {
        transition: all 0.3s ease;
        cursor: pointer;
        background-color: #fff;
    }
    
    .payment-option:hover {
        border-color: #667eea !important;
        background-color: #f8f9ff;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    
    input[type="radio"]:checked + label .payment-option {
        border-color: #667eea !important;
        background-color: #f0f4ff;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }
    
    .payment-method {
        position: relative;
    }
    
    .payment-method input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }
    
    .btn-success:active {
        transform: translateY(0);
    }
    
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.5rem 0.75rem;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
    
    .sticky-top {
        top: 20px;
        z-index: 100;
    }
    
    h4, h5, h6 {
        color: #333;
    }
    
    .fw-500 {
        font-weight: 500;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');

    // Smooth transition for payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'momo') {
                payButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Thanh Toán Qua MoMo';
            } else {
                payButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Thanh Toán Ngay';
            }
        });
    });

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
