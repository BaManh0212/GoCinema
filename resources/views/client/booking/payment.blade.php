@extends('client.layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Thanh toán</h1>

            <!-- Thông tin đơn hàng -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Thông tin đơn hàng</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Mã đơn hàng:</p>
                            <p class="font-semibold">{{ $donDatVe->ma_don }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Trạng thái:</p>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($donDatVe->trang_thai === 'cho_thanh_toan') bg-yellow-100 text-yellow-800
                                @elseif($donDatVe->trang_thai === 'da_thanh_toan') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $donDatVe->trang_thai }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin phim -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Thông tin phim</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <img src="{{ asset('storage/' . $donDatVe->suatChieu->phim->anh_poster) }}"
                             alt="{{ $donDatVe->suatChieu->phim->tieu_de }}"
                             class="w-full md:w-32 h-48 object-cover rounded-lg">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800">{{ $donDatVe->suatChieu->phim->tieu_de }}</h3>
                            <div class="mt-2 space-y-1 text-sm text-gray-600">
                                <p><strong>Rạp:</strong> {{ $donDatVe->suatChieu->phong->rap->ten }}</p>
                                <p><strong>Phòng:</strong> {{ $donDatVe->suatChieu->phong->ten }}</p>
                                <p><strong>Suất chiếu:</strong> {{ \Carbon\Carbon::parse($donDatVe->suatChieu->gio_bat_dau)->format('d/m/Y H:i') }}</p>
                                <p><strong>Thời lượng:</strong> {{ $donDatVe->suatChieu->phim->thoi_luong }} phút</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin ghế -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Ghế đã chọn</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($donDatVe->chiTietVes as $chiTietVe)
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">{{ $chiTietVe->ghe->so_ghe_ngoi }}</p>
                                    <p class="text-sm text-gray-600">{{ ucfirst($chiTietVe->loai_ghe) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-blue-600">{{ number_format($chiTietVe->gia) }}đ</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Combo đã chọn -->
            @if($combos->count() > 0)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Combo đã chọn</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    @foreach($combos as $combo)
                    <div class="flex justify-between items-center py-2">
                        <div>
                            <p class="font-semibold">{{ $combo->ten }}</p>
                            <p class="text-sm text-gray-600">Số lượng: {{ $combo->so_luong }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-blue-600">{{ number_format($combo->gia * $combo->so_luong) }}đ</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Mã giảm giá -->
            @if($donDatVe->maGiamGia)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Mã giảm giá</h2>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-green-800">{{ $donDatVe->maGiamGia->ma }}</p>
                            <p class="text-sm text-green-600">{{ $donDatVe->maGiamGia->mo_ta }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-800">-{{ number_format($donDatVe->tong_tien - ($donDatVe->chiTietVes->sum('gia') + $combos->sum(function($combo) { return $combo->gia * $combo->so_luong; }))) }}đ</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tổng tiền -->
            <div class="mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-blue-800">Tổng tiền</h3>
                        <h3 class="text-2xl font-bold text-blue-800">{{ number_format($donDatVe->tong_tien) }}đ</h3>
                    </div>
                </div>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Chọn phương thức thanh toán</h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="momo" class="mr-3" checked>
                            <div class="flex items-center">
                                <img src="https://developers.momo.vn/v3/img/logo-momo.png" alt="MoMo" class="w-8 h-8 mr-3">
                                <div>
                                    <p class="font-semibold">Ví MoMo</p>
                                    <p class="text-sm text-gray-600">Thanh toán nhanh qua ví điện tử</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="zalopay" class="mr-3">
                            <div class="flex items-center">
                                <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay.png" alt="ZaloPay" class="w-8 h-8 mr-3">
                                <div>
                                    <p class="font-semibold">Ví ZaloPay</p>
                                    <p class="text-sm text-gray-600">Thanh toán nhanh qua ví điện tử</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="bank" class="mr-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-sm">B</span>
                                </div>
                                <div>
                                    <p class="font-semibold">Chuyển khoản ngân hàng</p>
                                    <p class="text-sm text-gray-600">Thanh toán qua chuyển khoản</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Nút thanh toán -->
            <div class="text-center">
                <button id="payButton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300">
                    Thanh toán {{ number_format($donDatVe->tong_tien) }}đ
                </button>
                <p class="text-sm text-gray-600 mt-2">Bằng cách nhấn thanh toán, bạn đồng ý với điều khoản sử dụng</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal thông báo thanh toán không thành công -->
<div id="paymentFailedModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <h2 class="text-xl font-semibold mb-4 text-red-600">Thanh toán không thành công</h2>
        <p class="mb-6 text-gray-700">Thanh toán thất bại do bạn đã rời khỏi trang thanh toán. Đơn vé sẽ không được lưu và ghế giữ sẽ bị hủy.</p>
        <div class="flex justify-end space-x-4">
            <button id="modalCancelBtn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Đóng</button>
            <button id="modalConfirmBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Xác nhận</button>
        </div>
    </div>
</div>

<script>
document.getElementById('payButton').addEventListener('click', function() {
    const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;

    // Hiển thị loading
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
    this.disabled = true;

    // Gửi request thanh toán
    fetch('/booking/process-payment/{{ $donDatVe->id }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_method: selectedPayment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect_url) {
                // Redirect đến trang thanh toán bên thứ 3
                window.location.href = data.redirect_url;
            } else {
                // Redirect đến trang xác nhận
                window.location.href = data.redirect;
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra khi thanh toán');
            // Reset button
            document.getElementById('payButton').innerHTML = 'Thanh toán {{ number_format($donDatVe->tong_tien) }}đ';
            document.getElementById('payButton').disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thanh toán');
        // Reset button
        document.getElementById('payButton').innerHTML = 'Thanh toán {{ number_format($donDatVe->tong_tien) }}đ';
        document.getElementById('payButton').disabled = false;
    });
});

// Modal handling and cancel order if user navigates away

const modal = document.getElementById('paymentFailedModal');
const modalConfirmBtn = document.getElementById('modalConfirmBtn');
const modalCancelBtn = document.getElementById('modalCancelBtn');

let navigationCanceled = false; // flag to prevent multiple cancels
let pendingNavigationUrl = null;

function showModalAndCancelOrder(urlToNavigate) {
    if (navigationCanceled) return;
    navigationCanceled = true;
    pendingNavigationUrl = urlToNavigate;
    modal.classList.remove('hidden');
}

modalConfirmBtn.addEventListener('click', function() {
    // Call cancel API
    fetch('/booking/ajax-cancel/{{ $donDatVe->id }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            modal.classList.add('hidden');
            if (pendingNavigationUrl) {
                window.location.href = pendingNavigationUrl;
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra khi hủy đơn.');
            modal.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Cancel order error:', error);
        alert('Có lỗi xảy ra khi hủy đơn.');
        modal.classList.add('hidden');
    });
});

modalCancelBtn.addEventListener('click', function() {
    modal.classList.add('hidden');
    navigationCanceled = false;
    pendingNavigationUrl = null;
});

// Intercept all link clicks
document.querySelectorAll('a[href]').forEach(function(link) {
    link.addEventListener('click', function(event) {
        const href = link.getAttribute('href');

        // If link is external or no href, ignore
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
            return;
        }

        // Prevent navigation and show modal
        event.preventDefault();
        showModalAndCancelOrder(href);
    });
});

// Listen to beforeunload event for browser back or tab close
window.addEventListener('beforeunload', function(event) {
    // Send async cancel request (may not complete before unload)
    navigator.sendBeacon('/booking/ajax-cancel/{{ $donDatVe->id }}', new Blob([JSON.stringify({})], {type: 'application/json'}));

    // Show default browser message (some browsers ignore custom text)
    const message = 'Bạn có chắc chắn muốn rời khỏi trang thanh toán? Thanh toán sẽ không thành công.';
    (event || window.event).returnValue = message;
    return message;
});
</script>
@endsection
