@extends('staff.layouts.staff')

@section('title', 'Sơ đồ ghế phòng chiếu')

@section('content')
<div class="container">
    <h4 class="mb-4 text-primary fw-bold">🎬 Sơ đồ ghế phòng chiếu</h4>

    {{-- Chú thích màu --}}
    <div class="mb-4 d-flex flex-wrap justify-content-center gap-3">
        <div><span class="legend-box seat-vip"></span> Ghế VIP</div>
        <div><span class="legend-box seat-doi"></span> Ghế đôi</div>
        <div><span class="legend-box seat-thuong"></span> Ghế thường</div>
        <div><span class="legend-box seat-bao-tri"></span> Ghế bảo trì</div>
    </div>

    {{-- Sơ đồ ghế --}}
    <div class="seat-map p-4 border rounded bg-white shadow-sm">
        <div class="screen mb-4">🎥 MÀN HÌNH CHIẾU</div>

        <div class="d-flex flex-column align-items-center">
            @php
                $hangs = range('A', 'K'); // 11 hàng
                $soCot = 13;
            @endphp

            @foreach ($hangs as $index => $hang)
                @php
                    if ($index < 3) $loai = 'vip';
                    elseif ($index < 6) $loai = 'doi';
                    else $loai = 'thuong';
                @endphp

                <div class="d-flex mb-2">
                    @for ($cot = 1; $cot <= $soCot; $cot++)
                        <div class="seat seat-{{ $loai }}"
                             data-hang="{{ $hang }}"
                             data-cot="{{ $cot }}"
                             data-loai="{{ $loai }}"
                             data-trangthai="hoat_dong">
                            {{ $hang }}{{ $cot }}
                        </div>
                    @endfor
                </div>

                @if ($hang == 'F')
                    <div style="height: 20px;"></div>
                @endif
            @endforeach
        </div>
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
.seat-vip { background-color: #FFD700; }
.seat-doi { background-color: #98FB98; }
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
});

// Gửi dữ liệu lên server
document.getElementById('btnSaveLayout').addEventListener('click', () => {
    const seats = Array.from(document.querySelectorAll('.seat')).map(seat => ({
        hang: seat.dataset.hang,
        cot: seat.dataset.cot,
        loai: seat.dataset.loai,
        trang_thai: seat.dataset.trangthai
    }));

    fetch(`{{ route('staff.staff.phongchieu.ghe.updateMap', $phong->id ?? 21) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ seats })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) alert('✅ Đã lưu trạng thái ghế thành công!');
        else alert('❌ Lưu thất bại!');
    })
    .catch(() => alert('❌ Lỗi kết nối!'));
});
</script>
@endpush
