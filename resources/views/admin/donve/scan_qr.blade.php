@extends('admin.layouts.admin')

@section('content')
<h3>Quét QR đơn đặt vé</h3>

<div id="reader" style="width: 400px;"></div>
<div id="result" class="mt-3"></div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    function onScanSuccess(decodedText) {
        document.getElementById('result').innerHTML = "Đang kiểm tra đơn...";

        fetch("{{ route('admin.admin.scan.qr.check') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ ma_don: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                window.location.href = data.redirect;
            } else {
                document.getElementById('result').innerHTML = "<span style='color:red'>" + data.message + "</span>";
            }
        });
    }

    const html5QrCode = new Html5Qrcode("reader");
    Html5Qrcode.getCameras().then(cameras => {
        html5QrCode.start(
            cameras[0].id, 
            { fps: 10, qrbox: 250 },
            onScanSuccess
        );
    });
</script>
@endsection@extends('admin.layouts.admin')

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
                    {{-- <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary mt-3 ms-2">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let html5QrCode;
const resultEl = document.getElementById('result');

function onScanSuccess(decodedText) {
    resultEl.innerHTML = "<span class='text-primary fw-bold'>Đang kiểm tra đơn...</span>";

    let maDon = decodedText;
    try {
        const data = JSON.parse(decodedText);
        maDon = data.ma_don || decodedText;
    } catch(e){}

    fetch("{{ route('admin.admin.scan.qr.check') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ ma_don: maDon })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status && data.redirect) {
            resultEl.innerHTML = "<span class='text-success fw-bold'>Đơn hợp lệ! Chuyển trang...</span>";
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 800); // delay để thấy thông báo
        } else {
            resultEl.innerHTML = "<span class='text-danger fw-bold'>" + data.message + "</span>";
        }
    })
    .catch(err => {
        console.error(err);
        resultEl.innerHTML = "<span class='text-danger fw-bold'>Lỗi server!</span>";
    });
}

Html5Qrcode.getCameras().then(cameras => {
    if(cameras && cameras.length){
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            cameras[0].id,
            { fps: 10, qrbox: 250 },
            onScanSuccess
        );
    } else {
        resultEl.innerHTML = "<span class='text-warning fw-bold'>Không tìm thấy camera</span>";
    }
});

document.getElementById('stopScan').addEventListener('click', () => {
    if(html5QrCode){
        html5QrCode.stop().then(() => {
            resultEl.innerHTML = "<span class='text-muted fw-bold'>Quét đã dừng</span>";
        }).catch(err => console.error(err));
    }
});
</script>
@endsection

