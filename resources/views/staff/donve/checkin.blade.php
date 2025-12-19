@extends('staff.layouts.staff')

@section('title', 'Check-in theo mã đơn')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Check-in theo mã đơn</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Chọn phương thức check-in -->
    <div id="methodSelect" class="mb-6">
        <p class="mb-2 text-sm text-gray-600">Chọn hình thức check-in trước khi tiếp tục:</p>
        <div class="flex gap-3">
            <button id="btnQr" type="button" class="px-4 py-2 rounded bg-white border hover:bg-gray-50">📷 Quét QR / Barcode</button>
            <button id="btnManual" type="button" class="px-4 py-2 rounded bg-white border hover:bg-gray-50">⌨️ Nhập mã tay</button>
        </div>
    </div>

    <!-- Container nội dung (ẩn cho đến khi chọn phương thức) -->
    <div id="methodContainers" style="display:none;">
        <div id="qrContainer" class="card p-4 bg-white rounded shadow mb-4" style="display:none;">
            <h2 class="font-semibold mb-3">Quét mã QR</h2>
            <div id="reader" style="width:100%; max-width:420px; margin:0 auto;"></div>
            <div id="qrResult" class="mt-3 p-2 rounded" style="min-height:40px;"></div>
            <div class="mt-3 flex gap-2">
                <button id="stopScan" type="button" class="inline-flex items-center px-3 py-2 border rounded text-sm bg-gray-200">Dừng quét</button>
                <button id="restartScan" type="button" class="inline-flex items-center px-3 py-2 border rounded text-sm bg-blue-500 text-white">Bắt đầu lại</button>
            </div>
        </div>

        <div id="manualContainer" class="card p-4 bg-white rounded shadow mb-4" style="display:none;">
            <h2 class="font-semibold mb-3">Nhập tay mã đơn</h2>

            @php
                $actionRoute = route('staff.donve.checkinByCode');
            @endphp
            <form id="manualForm" action="{{ $actionRoute }}" method="POST" class="max-w-lg">
                @csrf

                <label for="ma_don" class="block text-sm font-medium text-gray-700">Mã đơn</label>
                <div class="mt-1 flex">
                    <input id="ma_don" name="ma_don" type="text" required
                           value="{{ old('ma_don', $maDon ?? '') }}"
                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Check-in
                    </button>
                </div>

                <p class="text-sm text-gray-500 mt-3">Nhập mã đơn (ví dụ: mã in trên vé) và nhấn Check-in.</p>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
(function(){
    // Elements
    const methodSelect = document.getElementById('methodSelect');
    const methodContainers = document.getElementById('methodContainers');
    const btnQr = document.getElementById('btnQr');
    const btnManual = document.getElementById('btnManual');
    const qrContainer = document.getElementById('qrContainer');
    const manualContainer = document.getElementById('manualContainer');
    const resultEl = document.getElementById('qrResult');
    const stopBtn = document.getElementById('stopScan');
    const restartBtn = document.getElementById('restartScan');

    const checkUrl = "{{ route('staff.donve.checkinByCode') }}";
    const ordersUrl = "{{ route('staff.donve.index') }}";

    let html5QrCode = null;
    let currentCameraId = null;
    let scanning = false;

    // Helper show/hide
    function showMessage(html){ resultEl.innerHTML = html; }
    function showMethod(method){
        methodContainers.style.display = '';
        qrContainer.style.display = (method === 'qr') ? '' : 'none';
        manualContainer.style.display = (method === 'manual') ? '' : 'none';
        // start scanner only when selected
        if (method === 'qr') startScanner();
        else stopScanner();
        // focus input when manual
        if (method === 'manual'){
            const input = document.getElementById('ma_don');
            if (input) input.focus();
        }
    }

    // Buttons
    btnQr.addEventListener('click', ()=>{ showMethod('qr'); });
    btnManual.addEventListener('click', ()=>{ showMethod('manual'); });

    // Preselect via query param ?method=qr or ?method=manual
    const urlParams = new URLSearchParams(window.location.search);
    const pre = urlParams.get('method');
    if (pre === 'qr' || pre === 'manual') {
        methodSelect.style.display = 'none';
        showMethod(pre);
    }

    // Scanner functions
    function proceedFetch(maDon) {
        fetch(checkUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ ma_don: maDon })
        })
        .then(async res => {
            const text = await res.text();
            try {
                const data = JSON.parse(text);
                if (res.ok && data.redirect) {
                    showMessage("<span class='text-success fw-bold'>Đơn hợp lệ! Chuyển trang...</span>");
                    setTimeout(()=>window.location.href = data.redirect, 600);
                    return;
                } else {
                    showMessage("<span class='text-danger fw-bold'>" + (data.message || data.error || 'Đơn không hợp lệ') + "</span>");
                }
            } catch (e) {
                console.error('Non-JSON response', text);
                showMessage("<span class='text-danger fw-bold'>Lỗi server (xem console)</span>");
            }
            tryRestartScanner();
        })
        .catch(err => {
            console.error('Fetch error', err);
            showMessage("<span class='text-danger fw-bold'>Lỗi server!</span>");
            tryRestartScanner();
        });
    }

    function tryRestartScanner(){
        scanning = false;
        if (!html5QrCode || !currentCameraId) return;
        html5QrCode.start(currentCameraId, { fps: 10, qrbox: 250 }, onScanSuccess)
            .then(()=> showMessage("<span class='text-info fw-bold'>Camera sẵn sàng. Hãy quét QR code.</span>"))
            .catch(err=> showMessage("<span class='text-danger fw-bold'>Không thể khởi động camera: "+(err.message||err)+"</span>"));
    }

    function onScanSuccess(decodedText){
        if (scanning) return;
        scanning = true;
        showMessage("<span class='text-primary fw-bold'>Đang kiểm tra đơn...</span>");
        let maDon = decodedText;
        try{ const data = JSON.parse(decodedText); maDon = data.ma_don || maDon; } catch(e){}
        if (typeof maDon === 'string' && maDon.includes(':')) maDon = maDon.split(':')[0].trim();

        if (html5QrCode){
            html5QrCode.stop().catch(()=>{}).finally(()=>{ try{ html5QrCode.clear(); }catch(_){} proceedFetch(maDon); });
        } else proceedFetch(maDon);
    }

    function startScanner(){
        if (html5QrCode) return tryRestartScanner();
        if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)){
            showMessage("<span class='text-warning fw-bold'>Trình duyệt không hỗ trợ camera.</span>");
            return;
        }
        navigator.mediaDevices.getUserMedia({ video: true }).then(stream=>{
            stream.getTracks().forEach(t=>t.stop());
            Html5Qrcode.getCameras().then(cameras=>{
                if (cameras && cameras.length){
                    html5QrCode = new Html5Qrcode("reader");
                    currentCameraId = cameras[0].id;
                    html5QrCode.start(currentCameraId, { fps: 10, qrbox: 250 }, onScanSuccess)
                        .then(()=> showMessage("<span class='text-info fw-bold'>Camera đã sẵn sàng. Hãy quét QR code.</span>"))
                        .catch(err=> showMessage("<span class='text-danger fw-bold'>Không thể khởi động camera: "+(err.message||err)+"</span>"));
                } else showMessage("<span class='text-warning fw-bold'>Không tìm thấy camera</span>");
            }).catch(err=> showMessage("<span class='text-danger fw-bold'>Lỗi khi truy cập camera: "+(err.message||err)+"</span>"));
        }).catch(err=> showMessage("<span class='text-danger fw-bold'>Quyền truy cập camera bị từ chối: "+(err.message||err)+"</span>"));
    }

    function stopScanner(){
        if (!html5QrCode) return;
        html5QrCode.stop().then(()=>{ try{ html5QrCode.clear(); }catch(_){} html5QrCode = null; currentCameraId = null; })
            .catch(()=>{ html5QrCode = null; currentCameraId = null; });
    }

    stopBtn.addEventListener('click', ()=>{
        if (html5QrCode){
            html5QrCode.stop().then(()=>{ showMessage("<span class='text-muted fw-bold'>Quét đã dừng. Đang chuyển về quản lý đơn...</span>"); setTimeout(()=>window.location.href = ordersUrl,400); })
            .catch(err=> { console.error('Stop error',err); window.location.href = ordersUrl; });
        } else window.location.href = ordersUrl;
    });

    restartBtn.addEventListener('click', ()=>{ tryRestartScanner(); });

    // Ensure selector visible on first load if no preselect
    if (!pre) {
        methodContainers.style.display = 'none';
        // methodSelect remains visible to force user choose
    } else {
        methodSelect.style.display = 'none';
    }

})();
</script>
@endsection
