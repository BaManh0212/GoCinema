@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h3>➕ Thêm sơ đồ ghế</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.sodo.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="phong_id">Chọn phòng</label>
            <select name="phong_id" id="phong_id" class="form-select">
                <option value="">-- Chọn phòng --</option>
                @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}">{{ $phong->ten }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Ma trận ghế</label>
            <div>
                <button type="button" id="addRow" class="btn btn-sm btn-success mb-2">➕ Thêm hàng</button>
                <button type="button" id="addCol" class="btn btn-sm btn-info mb-2">➕ Thêm cột</button>
            </div>

            <div id="seatMatrix" class="d-flex flex-column"></div>
        </div>

        <input type="hidden" name="ma_tran" id="ma_tran">
        <button type="submit" class="btn btn-primary">💾 Lưu sơ đồ</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
const seatMatrix = document.getElementById('seatMatrix');
let rows = 5;
let cols = 8;

function renderMatrix() {
    seatMatrix.innerHTML = '';
    for (let r = 0; r < rows; r++) {
        const rowDiv = document.createElement('div');
        rowDiv.classList.add('d-flex', 'mb-1');
        for (let c = 0; c < cols; c++) {
            const seat = document.createElement('div');
            seat.classList.add('seat', 'seat-thuong');
            seat.dataset.hang = r + 1;
            seat.dataset.cot = c + 1;
            seat.dataset.loai = 'thuong';
            seat.dataset.trangthai = 'hoat_dong';
            seat.style.width = '40px';
            seat.style.height = '40px';
            seat.style.border = '1px solid #ccc';
            seat.style.margin = '2px';
            seat.style.textAlign = 'center';
            seat.style.lineHeight = '40px';
            seat.style.cursor = 'pointer';
            seat.textContent = r+1 + '-' + (c+1);

            seat.addEventListener('click', () => {
                if (seat.dataset.loai === 'thuong') seat.dataset.loai = 'vip';
                else if (seat.dataset.loai === 'vip') seat.dataset.loai = 'doi';
                else seat.dataset.loai = 'thuong';

                seat.className = 'seat seat-' + seat.dataset.loai;
            });

            rowDiv.appendChild(seat);
        }
        seatMatrix.appendChild(rowDiv);
    }
}

document.getElementById('addRow').addEventListener('click', () => { rows++; renderMatrix(); });
document.getElementById('addCol').addEventListener('click', () => { cols++; renderMatrix(); });

document.querySelector('form').addEventListener('submit', (e) => {
    const data = [];
    seatMatrix.querySelectorAll('.seat').forEach(seat => {
        data.push({
            hang: seat.dataset.hang,
            cot: seat.dataset.cot,
            loai: seat.dataset.loai,
            trang_thai: seat.dataset.trangthai
        });
    });
    document.getElementById('ma_tran').value = JSON.stringify(data);
});

renderMatrix();
</script>
@endpush
