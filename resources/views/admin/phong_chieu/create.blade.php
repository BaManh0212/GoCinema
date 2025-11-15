@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">➕ Thêm mới phòng chiếu</h1>

    {{-- Thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Thông báo lỗi --}}
    @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.phongchieu.store') }}" method="POST">
                @csrf

                {{-- Tên phòng --}}
                <div class="mb-3">
                    <label for="ten" class="form-label fw-bold">Tên phòng chiếu <span class="text-danger">*</span></label>
                    <input 
                        type="text"
                        id="ten"
                        name="ten"
                        class="form-control @error('ten') is-invalid @enderror"
                        value="{{ old('ten') }}"
                        placeholder="Nhập tên phòng chiếu">
                    @error('ten')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Định dạng --}}
                <div class="mb-3">
                    <label for="dinh_dang_id" class="form-label fw-bold">Định dạng</label>
                    <select name="dinh_dang_id" id="dinh_dang_id" class="form-select">
                        <option value="">-- Chọn định dạng --</option>
                        @foreach($dinhdangs as $dd)
                            <option value="{{ $dd->id }}" {{ old('dinh_dang_id') == $dd->id ? 'selected' : '' }}>
                                {{ $dd->ten }}
                            </option>
                        @endforeach
                    </select>
                    @error('dinh_dang_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label for="trang_thai" class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                    <select name="trang_thai" id="trang_thai" class="form-select">
                        <option value="hoat_dong" {{ old('trang_thai') == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="bao_tri" {{ old('trang_thai') == 'bao_tri' ? 'selected' : '' }}>Bảo trì</option>
                        <option value="ngung_su_dung" {{ old('trang_thai') == 'ngung_su_dung' ? 'selected' : '' }}>Ngừng sử dụng</option>
                    </select>
                    @error('trang_thai')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ma trận ghế --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Sơ đồ ghế</label>
                    <div class="mb-2">
                        <button type="button" id="addRow" class="btn btn-sm btn-success">➕ Thêm hàng</button>
                        <button type="button" id="addCol" class="btn btn-sm btn-info">➕ Thêm cột</button>
                    </div>
                    <div id="seatMatrix" class="d-flex flex-column overflow-auto p-2 border" style="max-height:300px;"></div>
                </div>

                <input type="hidden" name="ma_tran" id="ma_tran">

                {{-- Nút --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.phongchieu.index') }}" class="btn btn-secondary">← Quay lại</a>
                    <button type="submit" class="btn btn-primary">💾 Thêm mới phòng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.seat {
    width:45px; height:45px; border:1px solid #ccc; margin:3px;
    text-align:center; line-height:45px; color:white; font-weight:bold; border-radius:5px;
    cursor:pointer; transition:0.2s; flex-shrink:0;
}
.seat:hover { transform: scale(1.1); box-shadow:0 0 5px #00000040; }

.seat-thuong { background:#91b8f3; }
.seat-vip { background:#94e774; }
.seat-doi { background:#4dbd72; }
.seat-bao-tri { background:#6c757d !important; }
</style>
@endpush

@push('scripts')
<script>
const seatMatrix = document.getElementById('seatMatrix');
let rows = 5;
let cols = 8;

function renderMatrix(){
    seatMatrix.innerHTML = '';
    for(let r=0; r<rows; r++){
        const rowDiv = document.createElement('div');
        rowDiv.classList.add('d-flex','mb-1','justify-content-center');
        for(let c=0; c<cols; c++){
            const seat = document.createElement('div');
            seat.classList.add('seat','seat-thuong');
            seat.dataset.hang = r+1;
            seat.dataset.cot = c+1;
            seat.dataset.loai = 'thuong';
            seat.dataset.trangthai = 'hoat_dong';
            seat.textContent = `${r+1}-${c+1}`;

            seat.addEventListener('click', ()=>{
                if(seat.dataset.loai === 'thuong') seat.dataset.loai='vip';
                else if(seat.dataset.loai==='vip') seat.dataset.loai='doi';
                else seat.dataset.loai='thuong';
                seat.className='seat seat-'+seat.dataset.loai;
            });

            rowDiv.appendChild(seat);
        }
        seatMatrix.appendChild(rowDiv);
    }
}

document.getElementById('addRow').addEventListener('click',()=>{rows++;renderMatrix();});
document.getElementById('addCol').addEventListener('click',()=>{cols++;renderMatrix();});

document.querySelector('form').addEventListener('submit',e=>{
    const data=[];
    seatMatrix.querySelectorAll('.seat').forEach(seat=>{
        data.push({
            hang: seat.dataset.hang,
            cot: seat.dataset.cot,
            loai: seat.dataset.loai,
            trang_thai: seat.dataset.trangthai
        });
    });
    document.getElementById('ma_tran').value=JSON.stringify(data);
});

renderMatrix();
</script>
@endpush
