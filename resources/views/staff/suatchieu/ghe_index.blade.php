@extends('staff.layouts.staff')

@section('title', 'Sơ đồ ghế phòng chiếu')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary fw-bold">🎬 Quản lý ghế suất chiếu</h4>
        <a href="{{ route('staff.suatchieu.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $suatchieu->phim->tieu_de }} - {{ $suatchieu->phong->ten }}</h5>
            <small class="text-muted">{{ \Carbon\Carbon::parse($suatchieu->gio_bat_dau)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($suatchieu->gio_ket_thuc)->format('H:i') }}</small>
        </div>
        <div class="card-body">
            {{-- Chú thích màu --}}
            <div class="mb-4 d-flex flex-wrap justify-content-center gap-3">
                <div><span class="legend-box seat-vip"></span> Ghế VIP</div>
                <div><span class="legend-box seat-doi"></span> Ghế đôi</div>
                <div><span class="legend-box seat-thuong"></span> Ghế thường</div>
                <div><span class="legend-box seat-bao-tri"></span> Ghế bảo trì</div>
                <div><span class="legend-box seat-dat"></span> Ghế đã đặt</div>
                <div><span class="legend-box seat-giu-tam"></span> Ghế giữ tạm</div>
            </div>

            {{-- Sơ đồ ghế --}}
            <div class="seat-map p-4 border rounded bg-white shadow-sm" style="display: grid; grid-template-columns: 50px 1fr; gap: 10px; align-items: center;">
                {{-- Màn hình --}}
                <div class="screen mb-4" style="grid-column: 1 / -1; width: {{ $suatchieu->phong->so_cot * 45 + 40 }}px; justify-self: center;">🎥 MÀN HÌNH CHIẾU</div>

                {{-- Lối vào --}}
                <div class="d-flex justify-content-start mb-3" style="grid-column: 1 / -1; padding-left: 50px;">
                    <div class="seat-preview seat-entrance">VÀO</div>
                </div>

                @php
                    $hangLetters = range('A', chr(ord('A') + $suatchieu->phong->so_hang - 1));
                @endphp

                @foreach ($hangLetters as $index => $hang)
                    {{-- Checkbox chọn hàng --}}
                    <div class="d-flex align-items-center justify-content-center" style="height: 45px;">
                        <input type="checkbox" class="row-checkbox" value="{{ $hang }}" id="row-{{ $hang }}">
                        <label for="row-{{ $hang }}" class="ms-1 fw-bold">{{ $hang }}</label>
                    </div>

                    {{-- Ghế trong hàng --}}
                    <div class="row-seats d-flex align-items-center mb-2">
                        @php
                            $danhSachGhe = $ghes->get($hang, collect());
                            $cot = 1;
                            $hasDoubleInRow = false;
                            for ($c = 1; $c <= $suatchieu->phong->so_cot; $c++) {
                                $gheCheck = $danhSachGhe->firstWhere('cot', $c);
                                if ($gheCheck && $gheCheck->loai == 'doi') {
                                    $hasDoubleInRow = true;
                                    break;
                                }
                            }
                        @endphp
                        @while ($cot <= $suatchieu->phong->so_cot)
                            @php
                                $ghe = $danhSachGhe->firstWhere('cot', $cot);
                                $currentLoai = $ghe ? $ghe->loai : 'thuong';
                                $isDouble = $currentLoai == 'doi';
                                $isLastSeat = $cot == $suatchieu->phong->so_cot;
                                $skipLastIfDoubleAndOdd = $isLastSeat && $hasDoubleInRow && $suatchieu->phong->so_cot % 2 == 1;
                                $trangthai = $ghe ? ($gheStatuses[$ghe->id] ?? 'hoat_dong') : 'hoat_dong';
                                if ($ghe && in_array($ghe->id, $giuTamIds)) {
                                    $trangthai = 'giu_tam';
                                } elseif ($ghe && in_array($ghe->id, $gheDaDat)) {
                                    $trangthai = 'da_dat';
                                }
                                $classes = 'seat seat-' . $currentLoai;
                                if ($trangthai === 'bao_tri') {
                                    $classes = 'seat seat-bao-tri';
                                } elseif ($trangthai === 'da_dat') {
                                    $classes = 'seat seat-dat';
                                } elseif ($trangthai === 'giu_tam') {
                                    $classes = 'seat seat-giu-tam';
                                }
                                if ($isDouble) $classes .= ' double-seat';
                            @endphp

                            @if(!$skipLastIfDoubleAndOdd)
                            <div class="{{ $classes }}"
                                 data-ghe-id="{{ $ghe ? $ghe->id : '' }}"
                                 data-hang="{{ $hang }}"
                                 data-cot="{{ $cot }}"
                                 data-loai="{{ $currentLoai }}"
                                 data-trangthai="{{ $trangthai }}">
                                @if($isDouble)
                                    💑
                                    @php $cot += 1; @endphp {{-- Bỏ qua ghế tiếp theo --}}
                                @else
                                    {{ $hang }}{{ $cot }}
                                @endif
                            </div>
                            @endif

                            @php $cot++; @endphp
                        @endwhile
                    </div>

                    @if ($hang == chr(ord('A') + ceil($suatchieu->phong->so_hang / 2) - 1))
                        <div style="grid-column: 1 / -1; height: 20px;"></div>
                    @endif
                @endforeach

                {{-- Lối ra --}}
                <div class="d-flex justify-content-end mt-3" style="grid-column: 1 / -1; padding-right: 50px;">
                    <div class="seat-preview seat-exit">RA</div>
                </div>
            </div>

            {{-- Nút lưu --}}
            <div class="d-flex justify-content-end mt-3">
                <button id="save-changes" class="btn btn-success" disabled>Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.seat {
    width: 45px;
    height: 45px;
    margin: 4px;
    border-radius: 8px;
    border: 1px solid #ccc;
    text-align: center;
    line-height: 45px;
    font-size: 12px;
    cursor: pointer;
    font-weight: 500;
    color: #222;
    user-select: none;
}
.double-seat {
    width: 94px !important; /* 45px * 2 + margin */
}
.seat-vip { background-color: #FFD700; }
.seat-doi { background-color: #98FB98; }
.seat-thuong { background-color: #87CEFA; }
.seat-bao-tri { background-color: #d1d5db !important; }
/* .seat-vo-hieu-hoa { background-color: #6B7280 !important; } removed */
.seat-dat { background-color: #FF6347 !important; cursor: not-allowed; }
.seat-giu-tam { background-color: #FFA500 !important; cursor: not-allowed; }

.legend-box {
    display: inline-block;
    width: 25px; height: 25px;
    margin-right: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    vertical-align: middle;
}

.screen {
    background-color: #222;
    color: #fff;
    text-align: center;
    font-weight: 600;
    padding: 10px 0;
    border-radius: 5px;
    width: 60%;
    margin: 0 auto;
    letter-spacing: 2px;
}

.seat:hover {
    transform: scale(1.08);
    transition: 0.15s;
}
.seat-preview {
    width: 35px;
    height: 35px;
    margin: 3px;
    border-radius: 6px;
    border: 2px solid #ddd;
    text-align: center;
    line-height: 35px;
    font-size: 11px;
    font-weight: 600;
    color: #333;
    display: inline-block;
}

.seat-entrance {
    background: #ff6b6b;
    color: white;
    border-color: #ff6b6b;
}
.seat-exit {
    background: #4ecdc4;
    color: white;
    border-color: #4ecdc4;
}
</style>
@endpush

@push('scripts')
<script>
let changes = {}; // Lưu trữ các thay đổi

// Toggle trạng thái ghế
document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', async () => {
        // Nếu ghế đã đặt => không đổi trạng thái
        if(seat.dataset.trangthai === 'da_dat') return;

        const currentStatus = seat.dataset.trangthai;
        let newStatus, newClass;

        // Chu kỳ: hoat_dong -> bao_tri -> hoat_dong
        if (currentStatus === 'hoat_dong') {
            newStatus = 'bao_tri';
            newClass = 'seat-bao-tri';
            // Call API to update normal seat status
            await updateSeatStatus(seat.dataset.gheId, newStatus, '{{ route("staff.suatchieu.ghe.updateTrangThai", $suatchieu->id) }}');
        } else if (currentStatus === 'bao_tri') {
            newStatus = 'hoat_dong';
            newClass = 'seat-' + seat.dataset.loai;
            // Call API to update normal seat status
            await updateSeatStatus(seat.dataset.gheId, newStatus, '{{ route("staff.suatchieu.ghe.updateTrangThai", $suatchieu->id) }}');
        } else {
            // fallback: nếu trạng thái không hợp lệ
            return;
        }

        // Cập nhật UI
        seat.dataset.trangthai = newStatus;
        seat.classList.remove('seat-vip', 'seat-doi', 'seat-thuong', 'seat-bao-tri', 'seat-giu-tam' /* 'seat-vo-hieu-hoa' removed */);
        seat.classList.add(newClass);

        // Lưu thay đổi vào object
        changes[seat.dataset.gheId] = newStatus;

        // Kích hoạt nút lưu
        document.getElementById('save-changes').disabled = false;
    });
});

// Hàm cập nhật trạng thái ghế qua AJAX
function updateSeatStatus(gheId, trangThai, url) {
    fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ghe_id: gheId,
            trang_thai: trangThai
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('❌ Cập nhật thất bại: ' + (data.message || 'Lỗi không xác định'));
            // Có thể reload trang để khôi phục trạng thái
            location.reload();
        }
    })
    .catch(() => {
        alert('❌ Lỗi kết nối!');
        location.reload();
    });
}

// Xử lý nút lưu
document.getElementById('save-changes').addEventListener('click', () => {
    const url = '{{ route("staff.suatchieu.ghe.updateTrangThai", $suatchieu->id) }}';
    const promises = [];

    for (const [gheId, trangThai] of Object.entries(changes)) {
        promises.push(updateSeatStatus(gheId, trangThai, url));
    }

    Promise.all(promises).then(() => {
        alert('✅ Lưu thay đổi thành công!');
        changes = {}; // Reset changes
        document.getElementById('save-changes').disabled = true;
        // Quay lại trang index
        window.location.href = '{{ route("staff.suatchieu.index") }}';
    }).catch(() => {
        alert('❌ Có lỗi xảy ra khi lưu!');
    });
});

// ================== Đồng bộ theo thời gian thực ==================
function applyLiveStatus(payload) {
    const gheStatuses = payload.ghe_statuses || {};
    const giuTamIds = new Set(payload.giu_tam_ids || []);
    const gheDaDats = new Set(payload.ghe_da_dat || []);

    document.querySelectorAll('.seat').forEach(seat => {
        const gheId = parseInt(seat.dataset.gheId, 10);
        const loai = seat.dataset.loai; // thuong/vip/doi

        let status = gheStatuses[gheId] || 'hoat_dong';
        if (gheDaDats.has(gheId)) status = 'da_dat';
        else if (giuTamIds.has(gheId)) status = 'giu_tam';

        // Cập nhật dataset + class
        seat.dataset.trangthai = status;
        seat.classList.remove('seat-vip','seat-doi','seat-thuong','seat-bao-tri' /* 'seat-vo_hieu_hoa','seat-vo-hieu-hoa' removed */, 'seat-dat','seat-giu-tam');

        if (status === 'da_dat') {
            seat.classList.add('seat-dat');
        } else if (status === 'giu_tam') {
            seat.classList.add('seat-giu-tam');
        } else if (status === 'bao_tri') {
            seat.classList.add('seat-bao-tri');
        } /* else if (status === 'vo_hieu_hoa') {
            seat.classList.add('seat-vo-hieu-hoa');
        } */ else {
            // hoạt động -> theo loại ghế
            seat.classList.add('seat-' + loai);
        }
    });
}

async function fetchLiveStatus() {
    try {
        const res = await fetch(`{{ route('staff.suatchieu.seatStatus', $suatchieu->id) }}`);
        if (!res.ok) return;
        const data = await res.json();
        if (data && data.success) applyLiveStatus(data);
    } catch (_) { /* ignore transient errors */ }
}

// Poll mỗi 5s và fetch ngay khi tải trang
fetchLiveStatus();
setInterval(fetchLiveStatus, 5000);
</script>
@endpush
