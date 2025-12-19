@extends('staff.layouts.staff')

@section('title', 'Sơ đồ ghế phòng chiếu')

@section('content')
<div class="container">
    <h4 class="mb-4 text-primary fw-bold">🎬 Sơ đồ ghế phòng chiếu</h4>

    {{-- Chú thích màu --}}
    <div class="mb-4 d-flex flex-wrap justify-content-center gap-3">
        <div><span class="legend-box seat-vip"></span> Ghế VIP (vàng)</div>
        <div><span class="legend-box seat-doi"></span> Ghế đôi (hồng)</div>
        <div><span class="legend-box seat-thuong"></span> Ghế thường</div>
        <div><span class="legend-box seat-bao-tri"></span> Ghế bảo trì</div>
    </div>

    {{-- Công cụ quản lý ghế --}}
    <div class="mb-4 d-flex flex-wrap gap-3 align-items-center">
        <div class="d-flex gap-2">
            <button id="btnConvertVip" class="btn btn-warning" disabled>⭐ Chuyển thành VIP</button>
            <button id="btnConvertNormal" class="btn btn-secondary" disabled>🪑 Chuyển thành Thường</button>
            <button id="btnConvertDouble" class="btn btn-info" disabled>💑 Chuyển thành Đôi</button>
        </div>
        <small class="text-muted">Chọn hàng để chuyển đổi loại ghế. Double-click ghế để chuyển đổi loại hoặc trạng thái</small>
    </div>

    {{-- Sơ đồ ghế --}}
    <div class="seat-map p-4 border rounded bg-white shadow-sm" style="display: grid; grid-template-columns: 50px 1fr; gap: 10px; align-items: center;">
        {{-- Màn hình --}}
        <div class="screen mb-4" style="grid-column: 1 / -1; width: {{ $phong->so_cot * 45 + 40 }}px; justify-self: center;">🎥 MÀN HÌNH CHIẾU</div>

        {{-- Lối vào --}}
        {{-- <div class="d-flex justify-content-start mb-3" style="grid-column: 1 / -1; padding-left: 50px;">
            <div class="seat-preview seat-entrance">VÀO</div>
        </div> --}}

        @php
            $hangLetters = range('A', chr(ord('A') + $phong->so_hang - 1));
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
                    $cot = 1;
                    $hasDoubleInRow = false;
                    for ($c = 1; $c <= $phong->so_cot; $c++) {
                        $keyCheck = $hang . '-' . $c;
                        $gheCheck = $ghes->get($keyCheck);
                        if ($gheCheck && $gheCheck->loai == 'doi') {
                            $hasDoubleInRow = true;
                            break;
                        }
                    }
                @endphp
                @while ($cot <= $phong->so_cot)
                    @php
                        $key = $hang . '-' . $cot;
                        $ghe = $ghes->get($key);
                        $currentLoai = $ghe ? $ghe->loai : 'thuong';
                        $currentTrangThai = $ghe ? $ghe->trang_thai : 'hoat_dong';
                        $isDouble = $currentLoai == 'doi';
                        $isLastSeat = $cot == $phong->so_cot;
                        $skipLastIfDoubleAndOdd = $isLastSeat && $hasDoubleInRow && $phong->so_cot % 2 == 1;
                    @endphp

                    @if(!$skipLastIfDoubleAndOdd)
                    <div class="seat seat-{{ $currentLoai }} {{ $currentTrangThai == 'bao_tri' ? 'seat-bao-tri' : '' }} {{ $isDouble ? 'double-seat' : '' }}"
                         data-hang="{{ $hang }}"
                         data-cot="{{ $cot }}"
                         data-loai="{{ $currentLoai }}"
                         data-trangthai="{{ $currentTrangThai }}">
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

            @if ($hang == chr(ord('A') + ceil($phong->so_hang / 2) - 1))
                <div style="grid-column: 1 / -1; height: 20px;"></div>
            @endif
        @endforeach

        {{-- Lối ra --}}
        {{-- <div class="d-flex justify-content-end mt-3" style="grid-column: 1 / -1; padding-right: 50px;">
            <div class="seat-preview seat-exit">RA</div>
        </div> --}}
    </div>

    {{-- Nút lưu sơ đồ --}}
    <div class="mt-4 text-end">
        <button id="btnSaveLayout" class="btn btn-success px-4">💾 Lưu sơ đồ</button>
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
.seat-doi { background-color: #b6ffb7ff; }
.seat-thuong { background-color: #87CEFA; }
.seat-bao-tri { background-color: #d1d5db !important; }

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
// Toggle trạng thái ghế
document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', () => {
        const isBaoTri = seat.dataset.trangthai === 'bao_tri';

        if (isBaoTri) {
            seat.dataset.trangthai = 'hoat_dong';
            seat.classList.remove('seat-bao-tri');
            seat.classList.add('seat-' + seat.dataset.loai);
        } else {
            seat.dataset.trangthai = 'bao_tri';
            seat.classList.remove('seat-vip', 'seat-doi', 'seat-thuong');
            seat.classList.add('seat-bao-tri');
        }
    });

    // Double-click để chuyển đổi loại ghế
    seat.addEventListener('dblclick', () => {
        const currentLoai = seat.dataset.loai;
        const currentTrangThai = seat.dataset.trangthai;

        if (currentTrangThai === 'bao_tri') {
            // Nếu đang bảo trì, chuyển về hoạt động với loại cũ
            seat.dataset.trangthai = 'hoat_dong';
            seat.classList.remove('seat-bao-tri');
            seat.classList.add('seat-' + currentLoai);

            // Cập nhật text hiển thị
            if (currentLoai === 'doi') {
                seat.textContent = '💑';
            } else {
                seat.textContent = seat.dataset.hang + seat.dataset.cot;
            }
        } else {
            // Nếu đang hoạt động, chuyển đổi loại ghế
            let newLoai;
            if (currentLoai === 'thuong') {
                newLoai = 'vip';
            } else if (currentLoai === 'vip') {
                newLoai = 'thuong';
            } else if (currentLoai === 'doi') {
                // Ghế đôi chỉ chuyển sang bảo trì
                seat.dataset.trangthai = 'bao_tri';
                seat.classList.remove('seat-doi');
                seat.classList.add('seat-bao-tri');
                seat.textContent = '🔧';
                return;
            }

            // Cập nhật UI cho ghế thường/VIP
            if (newLoai) {
                seat.dataset.loai = newLoai;
                seat.classList.remove('seat-thuong', 'seat-vip');
                seat.classList.add('seat-' + newLoai);
                seat.textContent = seat.dataset.hang + seat.dataset.cot;
            }
        }
    });
});

// Cập nhật trạng thái nút chuyển đổi
function updateConvertButtons() {
    const checkedRows = document.querySelectorAll('.row-checkbox:checked');
    const btnVip = document.getElementById('btnConvertVip');
    const btnNormal = document.getElementById('btnConvertNormal');
    const btnDouble = document.getElementById('btnConvertDouble');

    const hasSelection = checkedRows.length > 0;
    btnVip.disabled = !hasSelection;
    btnNormal.disabled = !hasSelection;
    btnDouble.disabled = !hasSelection;
}

// Lắng nghe sự kiện checkbox
document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateConvertButtons);
});

// Chuyển đổi hàng thành VIP
document.getElementById('btnConvertVip').addEventListener('click', () => {
    const selectedRows = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

    if (selectedRows.length === 0) {
        alert('Vui lòng chọn ít nhất một hàng!');
        return;
    }

    fetch(`{{ route('staff.phongchieu.ghe.convertRowsToVip', $phong->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ rows: selectedRows })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            // Reload để cập nhật sơ đồ ghế
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(() => alert('❌ Lỗi kết nối!'));
});

// Chuyển đổi thành ghế thường
document.getElementById('btnConvertNormal').addEventListener('click', () => {
    const selectedRows = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

    if (selectedRows.length === 0) {
        alert('Vui lòng chọn ít nhất một hàng!');
        return;
    }

    fetch(`{{ route('staff.phongchieu.ghe.convertRowsToNormal', $phong->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ rows: selectedRows })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            // Reload để cập nhật sơ đồ ghế
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(() => alert('❌ Lỗi kết nối!'));
});

// Chuyển đổi thành ghế đôi
document.getElementById('btnConvertDouble').addEventListener('click', () => {
    const selectedRows = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

    if (selectedRows.length === 0) {
        alert('Vui lòng chọn ít nhất một hàng!');
        return;
    }

    if (!confirm('Bạn có chắc muốn chuyển đổi các hàng đã chọn thành ghế đôi? Hành động này sẽ gộp 2 ghế thành 1.')) {
        return;
    }

    fetch(`{{ route('staff.phongchieu.ghe.convertToDoubleSeats', $phong->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ rows: selectedRows })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            // Reload để cập nhật sơ đồ ghế (vì ghế đôi gộp ô và xóa ghế thừa)
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(() => alert('❌ Lỗi kết nối!'));
});

// Gửi dữ liệu lên server
document.getElementById('btnSaveLayout').addEventListener('click', () => {
    const seats = Array.from(document.querySelectorAll('.seat')).map(seat => ({
        hang: seat.dataset.hang,
        cot: seat.dataset.cot,
        loai: seat.dataset.loai,
        trang_thai: seat.dataset.trangthai
    }));

    fetch(`{{ route('staff.phongchieu.ghe.updateMap', $phong->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ seats })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('✅ Đã lưu trạng thái ghế thành công!');
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            const message = data.message || 'Lưu thất bại!';
            alert('❌ ' + message);
        }
    })
    .catch(() => alert('❌ Lỗi kết nối!'));
});
</script>
@endpush
