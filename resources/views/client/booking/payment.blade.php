@extends('client.layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Countdown Timer -->
        <div id="countdown-timer" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-center">
            <h2 class="text-lg font-semibold text-blue-800 mb-2">Thời gian còn lại để thanh toán</h2>
            <div id="timer-display" class="text-3xl font-bold text-blue-600">10:00</div>
            <p class="text-sm text-blue-600 mt-2">Vui lòng hoàn tất thanh toán trước khi hết thời gian</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Thanh toán</h1>

            <!-- Thông tin đơn hàng -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Thông tin đơn hàng</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Mã đơn hàng:</p>
                            <p class="text-black">{{ $donDatVe->ma_don }}</p>
                        </div>
                        <!-- <div>
                            <p class="text-sm text-gray-600">Trạng thái:</p>
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($donDatVe->trang_thai === 'cho_thanh_toan') bg-yellow-100 text-yellow-800
                                @elseif($donDatVe->trang_thai === 'da_thanh_toan') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $donDatVe->trang_thai }}
                            </span>
                        </div> -->
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
                                    <p class="text-primary text-sm">{{ $chiTietVe->ghe->so_ghe_ngoi ?: ($chiTietVe->ghe->hang . $chiTietVe->ghe->cot) }}</p>
                                    <p class="text-sm text-gray-600">{{ \App\Helpers\SeatHelper::getSeatTypeName($chiTietVe->loai_ghe) }}</p>
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
                            <p class="text-sm font-semibold text-black">{{ $combo->ten }}</p>
                            <p class="text-sm text-black">Số lượng: {{ $combo->so_luong }}</p>
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
                <form id="paymentForm" action="{{ route('booking.process-payment', $donDatVe->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="momo" class="mr-3" checked>
                                <div class="flex items-center">
                                    <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square-1024x1024.png" alt="MoMo" class="w-8 h-8 mr-3">
                                    <div>
                                        <p class="font-semibold text-black">Ví MoMo</p>
                                        <p class="text-sm text-black">Thanh toán nhanh qua ví điện tử</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4"> 
                            <label class="flex items-center"> 
                                <input type="radio" name="payment_method" value="vnpay" class="mr-3"> 
                                <div class="flex items-center"> 
                                    <img src="https://vinadesign.vn/uploads/images/2023/05/vnpay-logo-vinadesign-25-12-57-55.jpg" alt="VNPay" class="w-8 h-8 mr-3"> 
                                    <div> 
                                        <p class="font-semibold text-black">VNPay</p> 
                                        <p class="text-sm text-black">Thanh toán nhanh qua cổng VNPay</p> 
                                    </div> 
                                </div> 
                            </label> 
                        </div>
                    </div>
                    
                    <!-- Nút thanh toán -->
                    <div class="text-center mt-8">
                        <button type="submit" id="payButton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300">
                            Thanh toán {{ number_format($donDatVe->tong_tien) }}đ
                        </button>
                        <p class="text-sm text-black mt-2">Bằng cách nhấn thanh toán, bạn đồng ý với điều khoản sử dụng</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = 600; // 10 minutes in seconds
    const timerDisplay = document.getElementById('timer-display');
    const countdownTimer = document.getElementById('countdown-timer');
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');

    // Flag to prevent multiple cancellation calls
    let bookingCancelled = false;
    let isNavigatingAway = false;
    let isSubmittingPayment = false; // ← NEW: Flag to prevent cancel during payment submission

    // Prevent booking cancellation when user submits payment form
    paymentForm.addEventListener('submit', function(e) {
        isSubmittingPayment = true;
        bookingCancelled = true; // Mark as cancelled to prevent other events from triggering
        console.log('Payment form submitted - preventing booking cancellation');
    });

    // Refresh seat holds every 5 minutes to keep them active during payment
    let holdRefreshInterval = setInterval(function() {
        if (bookingCancelled) return;

        fetch('/booking/hold-seats', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                suat_chieu_id: {{ $donDatVe->suat_chieu_id }},
                ghe_ids: {{ json_encode($donDatVe->chiTietVes->pluck('ghe_id')->toArray()) }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Seat holds refreshed successfully');
            } else {
                console.error('Failed to refresh seat holds:', data.message);
            }
        })
        .catch(error => {
            console.error('Error refreshing seat holds:', error);
        });
    }, 2 * 60 * 1000); // 2 minutes

    // Check if page was loaded from cache (back button) and booking should be cancelled
    // This handles the case where user pressed back button and page was restored from cache
    if (performance.navigation && performance.navigation.type === 2) {
        // Page was loaded via back/forward button
        if ('{{ $donDatVe->trang_thai }}' === 'cho_thanh_toan' && !bookingCancelled) {
            cancelBookingOnExit();
        }
    }

    // Function to cancel booking when user leaves page
    function cancelBookingOnExit() {
        if (bookingCancelled || isSubmittingPayment) return; // ← UPDATED: Don't cancel if submitting payment
        bookingCancelled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const url = `/booking/ajax-cancel/{{ $donDatVe->id }}`;
        
        // Use fetch with keepalive (modern, reliable replacement for sendBeacon)
        if (window.fetch && csrfToken) {
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('page_exit', '1');
            
            fetch(url, {
                method: 'POST',
                body: formData,
                keepalive: true, // Critical for page unload requests
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(e => console.error('Error canceling booking (fetch):', e));
            
            return;
        }
        
        // Fallback to sendBeacon if fetch keepalive not supported (rare)
        if (navigator.sendBeacon && csrfToken) {
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('page_exit', '1');
            
            if (navigator.sendBeacon(url, formData)) {
                return;
            }
        }

        // Fallback to synchronous XHR (legacy)
        try {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url, false); // Synchronous
            xhr.setRequestHeader('Content-Type', 'application/json');
            if (csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            }
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(JSON.stringify({ page_exit: true }));
        } catch (e) {
            console.error('Error canceling booking (xhr):', e);
        }
    }

    // Detect when user is leaving the page (multiple events for reliability)
    window.addEventListener('beforeunload', function(e) {
        if (!bookingCancelled && !isSubmittingPayment && '{{ $donDatVe->trang_thai }}' === 'cho_thanh_toan') {
            cancelBookingOnExit();
        }
    });

    // pagehide is more reliable than beforeunload for mobile browsers
    window.addEventListener('pagehide', function(e) {
        if (!bookingCancelled && !isSubmittingPayment && '{{ $donDatVe->trang_thai }}' === 'cho_thanh_toan') {
            cancelBookingOnExit();
        }
    });

    // Detect back/forward button navigation
    window.addEventListener('popstate', function(e) {
        if (!bookingCancelled && !isSubmittingPayment && '{{ $donDatVe->trang_thai }}' === 'cho_thanh_toan') {
            isNavigatingAway = true;
            cancelBookingOnExit();
        }
    });

    // Detect when page is shown from cache (user came back via back button)
    window.addEventListener('pageshow', function(e) {
        // Reset flags to ensure we can process cancellation if needed
        isSubmittingPayment = false;
        
        // Force reset bookingCancelled to allow cancellation to run again
        // This is crucial because it was set to true during form submission
        bookingCancelled = false;

        // Check if this is a back/forward navigation
        // Support both modern and legacy APIs
        let isBackNavigation = e.persisted || 
                               (window.performance && window.performance.navigation && window.performance.navigation.type === 2);
                               
        // Modern API check
        if (!isBackNavigation && window.performance && window.performance.getEntriesByType) {
            const navEntries = window.performance.getEntriesByType('navigation');
            if (navEntries.length > 0 && navEntries[0].type === 'back_forward') {
                isBackNavigation = true;
            }
        }

        // If page was loaded from cache or back button and booking is still pending
        if (isBackNavigation && '{{ $donDatVe->trang_thai }}' === 'cho_thanh_toan') {
            console.log('Returned from payment gateway or back button - cancelling booking');
            
            // Cancel immediately
            cancelBookingOnExit();
            
            // Redirect back to booking page after a short delay
            setTimeout(() => {
                window.location.href = '/booking?suat_chieu_id={{ $donDatVe->suat_chieu_id }}';
            }, 300);
        }
    });

    // Also detect visibility change (tab switching, minimizing, etc.)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden' && !bookingCancelled && !isSubmittingPayment && '{{ $donDatVe->trang_thai }}' === 'cho_thanh_toan') {
            // Give user a moment to come back before cancelling
            setTimeout(() => {
                if (document.visibilityState === 'hidden' && !bookingCancelled && !isSubmittingPayment) {
                    cancelBookingOnExit();
                }
            }, 5000); // Reduced to 5 seconds for faster response
        }
    });

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        timerDisplay.textContent = formattedTime;

        // Turn red when 2 minutes remaining
        if (timeLeft <= 120) {
            countdownTimer.classList.remove('bg-blue-50', 'border-blue-200');
            countdownTimer.classList.add('bg-red-50', 'border-red-200');
            timerDisplay.classList.remove('text-blue-600');
            timerDisplay.classList.add('text-red-600');
            countdownTimer.querySelector('h2').classList.remove('text-blue-800');
            countdownTimer.querySelector('h2').classList.add('text-red-800');
            countdownTimer.querySelector('p').classList.remove('text-blue-600');
            countdownTimer.querySelector('p').classList.add('text-red-600');
        }

        if (timeLeft <= 0) {
            // Time expired - cancel booking
            timerDisplay.textContent = '00:00';
            countdownTimer.querySelector('h2').textContent = 'Thời gian thanh toán đã hết';
            countdownTimer.querySelector('p').textContent = 'Đơn hàng sẽ được hủy và ghế sẽ được trả về';

            // Disable form
            paymentForm.style.pointerEvents = 'none';
            paymentForm.style.opacity = '0.5';
            payButton.disabled = true;
            payButton.textContent = 'Đã hết thời gian thanh toán';

            // Cancel booking via AJAX (hết thời gian - xóa hoàn toàn)
            fetch(`/booking/ajax-cancel/{{ $donDatVe->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ time_expired: true })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        window.location.href = '/booking?suat_chieu_id={{ $donDatVe->suat_chieu_id }}';
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error canceling booking:', error);
                // Still redirect even if AJAX fails
                setTimeout(() => {
                    window.location.href = '/booking?suat_chieu_id={{ $donDatVe->suat_chieu_id }}';
                }, 3000);
            });

            return;
        }

        timeLeft--;
        setTimeout(updateTimer, 1000);
    }

    updateTimer();
});
</script>

@endsection
