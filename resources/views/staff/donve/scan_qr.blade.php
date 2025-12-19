@extends('staff.layouts.staff')

@section('title', '🎫 Quét QR đơn đặt vé (Staff)')

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

// base path & urls
const basePath = window.location.pathname.replace(/\/scan-qr\/?$/, '');
const ordersUrl = "{{ route('staff.donve.index') }}";
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
  html5QrCode.start(currentCameraId, { fps: 10, qrbox: 250 }, onScanSuccess)
    .then(() => resultEl.innerHTML = "<span class='text-info fw-bold'>Camera đã sẵn sàng. Hãy quét QR code.</span>")
    .catch(err => resultEl.innerHTML = "<span class='text-danger fw-bold'>Không thể khởi động camera: " + (err.message||err) + "</span>");
}

function onScanSuccess(decodedText) {
  if (scanning) return;
  scanning = true;
  resultEl.innerHTML = "<span class='text-primary fw-bold'>Đang kiểm tra đơn...</span>";
  let maDon = decodedText;
  try { const data = JSON.parse(decodedText); maDon = data.ma_don || decodedText; } catch(e){}
  if (typeof maDon === 'string' && maDon.includes(':')) maDon = maDon.split(':')[0].trim();
  if (html5QrCode) {
    html5QrCode.stop().catch(()=>{}).finally(() => { try{ html5QrCode.clear(); }catch(_){} proceedFetch(maDon); });
  } else proceedFetch(maDon);
}

navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
  stream.getTracks().forEach(track => track.stop());
  Html5Qrcode.getCameras().then(cameras => {
    if(cameras && cameras.length){
      html5QrCode = new Html5Qrcode("reader");
      currentCameraId = cameras[0].id;
      html5QrCode.start(currentCameraId, { fps: 10, qrbox: 250 }, onScanSuccess)
        .then(() => resultEl.innerHTML = "<span class='text-info fw-bold'>Camera đã sẵn sàng. Hãy quét QR code.</span>")
        .catch(err => resultEl.innerHTML = "<span class='text-danger fw-bold'>Không thể khởi động camera: " + (err.message||err) + "</span>");
    } else resultEl.innerHTML = "<span class='text-warning fw-bold'>Không tìm thấy camera</span>";
  }).catch(err => resultEl.innerHTML = "<span class='text-danger fw-bold'>Lỗi khi truy cập camera: " + err.message + "</span>");
}).catch(err => resultEl.innerHTML = "<span class='text-danger fw-bold'>Quyền truy cập camera bị từ chối: " + err.message + "</span>");

document.getElementById('stopScan').addEventListener('click', () => {
  if (html5QrCode) {
    html5QrCode.stop().then(() => {
      resultEl.innerHTML = "<span class='text-muted fw-bold'>Quét đã dừng. Đang chuyển về quản lý đơn...</span>";
      setTimeout(() => { window.location.href = ordersUrl; }, 400);
    }).catch(() => { window.location.href = ordersUrl; });
  } else { window.location.href = ordersUrl; }
});
</script>
@endsection