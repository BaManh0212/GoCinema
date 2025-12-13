@extends('admin.layouts.admin')

@section('title', '🎫 Quét QR đơn đặt vé')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">📷 Quét QR đơn đặt vé</h4>
                </div>
                <div class="card-body text-center">
                    <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
                    <div id="result" class="mt-3 p-2 rounded" style="min-height: 40px;"></div>

                    <button id="stopScan" class="btn btn-outline-secondary mt-3">
                        <i class="bi bi-x-circle"></i> Dừng quét
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let html5QrCode;
let currentCameraId = null;
let scanning = false;
const resultEl = document.getElementById('result');

// Tính base path dựa trên location để hỗ trợ /admin/scan-qr hoặc /admin/admin/scan-qr
const basePath = window.location.pathname.replace(/\/scan-qr\/?$/, '');
const ordersUrl = "{{ route('admin.donve.index') }}";
const checkUrl = basePath + '/scan-qr/check';

function proceedFetch(maDon) {
    fetch(checkUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ ma_don: maDon })
    })
    .then(async res => {
        const text = await res.text();
        try {
            const data = JSON.parse(text);
            if (res.ok && data.status && data.redirect) {
                resultEl.innerHTML = "<span class='text-success fw-bold'>Đơn hợp lệ! Chuyển trang...</span>";
                setTimeout(() => window.location.href = data.redirect, 600);
                return;
            } else {
                resultEl.innerHTML = "<span class='text-danger fw-bold'>" + (data.message || 'Đơn không hợp lệ') + "</span>";
            }
        } catch (parseErr) {
            console.error('Non-JSON response:', text);
            resultEl.innerHTML = "<span class='text-danger fw-bold'>Lỗi server! (Xem console)</span>";
        }

        // Nếu không thành công, cho phép quét lại (restart)
        tryRestartScanner();
    })
    .catch(err => {
        console.error('Fetch error:', err);
        resultEl.innerHTML = "<span class='text-danger fw-bold'>Lỗi server!</span>";
        tryRestartScanner();
    });
}

function tryRestartScanner() {
    scanning = false;
    if (!html5QrCode || !currentCameraId) return;
    html5QrCode.start(
        currentCameraId,
        { fps: 10, qrbox: 250 },
        onScanSuccess
    ).then(() => {
        resultEl.innerHTML = "<span class='text-info fw-bold'>Camera đã sẵn sàng. Hãy quét QR code.</span>";
    }).catch(err => {
        console.error('Không thể khởi động lại camera:', err);
        resultEl.innerHTML = "<span class='text-danger fw-bold'>Không thể khởi động camera: " + (err.message||err) + "</span>";
    });
}

function onScanSuccess(decodedText) {
    if (scanning) return; // chặn callback lặp
    scanning = true;

    resultEl.innerHTML = "<span class='text-primary fw-bold'>Đang kiểm tra đơn...</span>";

    let maDon = decodedText;
    try {
        const data = JSON.parse(decodedText);
        maDon = data.ma_don || decodedText;
    } catch(e){}

    // Nếu payload có dạng "MA_DON:1" -> lấy phần trước dấu ':'
    if (typeof maDon === 'string' && maDon.includes(':')) {
        maDon = maDon.split(':')[0].trim();
    }

    // Dừng scanner ngay để tránh gửi nhiều request
    if (html5QrCode) {
        html5QrCode.stop().catch(()=>{}).finally(() => {
            // clear UI camera view to release resources (không bắt buộc nhưng tốt)
            try { html5QrCode.clear(); } catch(_) {}
            proceedFetch(maDon);
        });
    } else {
        proceedFetch(maDon);
    }
}

// Request camera permission first
navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
    // Stop the stream immediately after permission is granted
    stream.getTracks().forEach(track => track.stop());

    // Now proceed to get cameras and start scanning
    Html5Qrcode.getCameras().then(cameras => {
        if(cameras && cameras.length){
            html5QrCode = new Html5Qrcode("reader");
            currentCameraId = cameras[0].id;
            html5QrCode.start(
                currentCameraId,
                { fps: 10, qrbox: 250 },
                onScanSuccess
            ).then(() => {
                resultEl.innerHTML = "<span class='text-info fw-bold'>Camera đã sẵn sàng. Hãy quét QR code.</span>";
            }).catch(err => {
                console.error(err);
                resultEl.innerHTML = "<span class='text-danger fw-bold'>Không thể khởi động camera: " + (err.message||err) + "</span>";
            });
        } else {
            resultEl.innerHTML = "<span class='text-warning fw-bold'>Không tìm thấy camera</span>";
        }
    }).catch(err => {
        console.error(err);
        resultEl.innerHTML = "<span class='text-danger fw-bold'>Lỗi khi truy cập camera: " + err.message + "</span>";
    });
}).catch(err => {
    console.error(err);
    resultEl.innerHTML = "<span class='text-danger fw-bold'>Quyền truy cập camera bị từ chối: " + err.message + "</span>";
});


document.getElementById('stopScan').addEventListener('click', () => {
    if (html5QrCode) {
        // Dừng quét camera trước, sau đó chuyển về trang quản lý đơn
        html5QrCode.stop().then(() => {
            resultEl.innerHTML = "<span class='text-muted fw-bold'>Quét đã dừng. Đang chuyển về quản lý đơn...</span>";
            // delay nhỏ để hiển thị trạng thái cho người dùng
            setTimeout(() => {
                window.location.href = ordersUrl;
            }, 400);
        }).catch(err => {
            console.error('Lỗi khi dừng quét:', err);
            // Nếu dừng thất bại vẫn chuyển về trang quản lý để tránh kẹt giao diện
            resultEl.innerHTML = "<span class='text-danger fw-bold'>Không thể dừng camera. Chuyển trang...</span>";
            setTimeout(() => {
                window.location.href = ordersUrl;
            }, 800);
        });
    } else {
        // Nếu scanner chưa khởi tạo, chuyển thẳng về trang quản lý
        window.location.href = ordersUrl;
    }
});
</script>
@endsection
