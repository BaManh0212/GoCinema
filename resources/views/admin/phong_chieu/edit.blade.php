@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">✏️ Chỉnh sửa phòng chiếu</h1>

    {{-- Thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Thông báo lỗi --}}
    @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.phongchieu.update', $phongchieu->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Tên phòng --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên phòng chiếu <span class="text-danger">*</span></label>
                    <input type="text" name="ten" class="form-control @error('ten') is-invalid @enderror"
                           value="{{ old('ten', $phongchieu->ten) }}" placeholder="Nhập tên phòng chiếu">
                    @error('ten')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Định dạng --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Định dạng <span class="text-danger">*</span></label>
                    <select name="dinh_dang_id" class="form-select @error('dinh_dang_id') is-invalid @enderror">
                        <option value="">-- Chọn định dạng --</option>
                        @foreach($dinhdangs as $dd)
                            <option value="{{ $dd->id }}" {{ old('dinh_dang_id', $phongchieu->dinh_dang_id) == $dd->id ? 'selected' : '' }}>
                                {{ $dd->ten }}
                            </option>
                        @endforeach
                    </select>
                    @error('dinh_dang_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Sơ đồ --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Sơ đồ (tùy chọn)</label>
                    <input type="text" name="so_do" class="form-control" value="{{ old('so_do', $phongchieu->so_do) }}" placeholder="VD: A1-A10, B1-B10...">
                </div>

                {{-- Số hàng --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Số hàng <span class="text-danger">*</span></label>
                    <input type="number" id="so_hang" name="so_hang" class="form-control"
                           value="{{ old('so_hang', $phongchieu->so_hang) }}" min="1" max="50">
                </div>

                {{-- Số cột --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Số cột <span class="text-danger">*</span></label>
                    <input type="number" id="so_cot" name="so_cot" class="form-control"
                           value="{{ old('so_cot', $phongchieu->so_cot) }}" min="1" max="50">
                </div>

                {{-- Tổng ghế --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tổng số ghế (tự động)</label>
                    <input type="text" id="tong_ghe_display" class="form-control" value="{{ $phongchieu->so_hang * $phongchieu->so_cot }} ghế" readonly>
                    <small class="text-muted">Tổng ghế = Số hàng × Số cột</small>
                </div>

                {{-- Preview sơ đồ ghế --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Sơ đồ ghế (xem trước)</label>
                    <div id="seat-preview" class="seat-preview-container" style="max-height: 500px; overflow-y: auto;"></div>
                    <small class="text-muted">Thay đổi số hàng/cột để xem sơ đồ ghế</small>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                    <select name="trang_thai" class="form-select">
                        <option value="hoat_dong" {{ $phongchieu->trang_thai == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="bao_tri" {{ $phongchieu->trang_thai == 'bao_tri' ? 'selected' : '' }}>Bảo trì</option>
                        <option value="ngung_su_dung" {{ $phongchieu->trang_thai == 'ngung_su_dung' ? 'selected' : '' }}>Ngừng sử dụng</option>
                    </select>
                </div>

                {{-- Nút --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.phongchieu.index') }}" class="btn btn-secondary">← Quay lại</a>
                    <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- CSS giống file create --}}
<style>
.seat-preview { width: 35px; height: 35px; margin: 3px; border-radius: 6px; border: 2px solid #ddd; text-align: center; line-height: 35px; font-size: 11px; font-weight: 600; color: #333; display: inline-block; }
.seat-screen { background: #2c3e50; color: white; border-radius: 6px; border: 2px solid #2c3e50; font-weight: bold; }
.seat-preview-container { display: flex; flex-direction: column; align-items: center; padding: 20px; background: #f8f9fa; border-radius: 10px; border: 2px solid #e9ecef; }
.seat-row { display: flex; align-items: center; margin-bottom: 8px; }
.seat-row-label { width: 40px; text-align: center; font-weight: bold; }
.seat-entrance { background: #ff6b6b; color: white; border-color: #ff6b6b; }
.seat-exit { background: #4ecdc4; color: white; border-color: #4ecdc4; }
.seat-entrance-row, .seat-exit-row { width: 100%; display: flex; margin: 15px 0; }
.seat-entrance-row { justify-content: flex-start; padding-left: 50px; }
.seat-exit-row { justify-content: flex-end; padding-right: 50px; }
</style>
@endpush

@push('scripts')
{{-- JS giống file create --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const soHangInput = document.getElementById('so_hang');
    const soCotInput = document.getElementById('so_cot');
    const tongGheDisplay = document.getElementById('tong_ghe_display');
    const seatPreview = document.getElementById('seat-preview');

    function updateTongGhe() {
        const soHang = +soHangInput.value || 0;
        const soCot = +soCotInput.value || 0;
        tongGheDisplay.value = (soHang * soCot) + " ghế";
        renderSeats(soHang, soCot);
    }

    function renderSeats(soHang, soCot) {
        seatPreview.innerHTML = "";
        if (!soHang || !soCot) return;

        const screen = document.createElement('div');
        screen.style.display = "flex";
        screen.style.justifyContent = "center";
        screen.style.width = "100%";
        screen.style.marginBottom = "10px";

        const screenBox = document.createElement('div');
        screenBox.className = "seat-screen";
        screenBox.style.width = (soCot * 38) + "px";
        screenBox.style.height = "25px";
        screenBox.style.lineHeight = "25px";
        screenBox.textContent = "MÀN HÌNH";
        // Tạo span để căn giữa chữ
        const screenText = document.createElement('span');
        screenText.style.position = "absolute";
        screenText.style.top = "50%";
        screenText.style.left = "50%";
        screenText.style.transform = "translate(-50%, -50%)";
        screenText.style.fontWeight = "bold";   
        screenText.style.color = "white";
        screenBox.style.position = "relative"; 
        screenText.textContent = "MÀN HÌNH";

        screenBox.textContent = ""; // xóa text cũ
        screenBox.appendChild(screenText);

        screen.appendChild(screenBox);
        seatPreview.appendChild(screen);

        const entranceWrap = document.createElement('div');
        entranceWrap.className = "seat-entrance-row";
        entranceWrap.innerHTML = `<div class="seat-preview seat-entrance">VÀO</div>`;
        seatPreview.appendChild(entranceWrap);

        for (let r = 0; r < soHang; r++) {
            const row = document.createElement('div');
            row.className = "seat-row";

            const label = document.createElement('div');
            label.className = "seat-row-label";
            label.textContent = String.fromCharCode(65 + r);
            row.appendChild(label);

            for (let c = 1; c <= soCot; c++) {
                const seat = document.createElement('div');
                seat.className = "seat-preview";
                seat.textContent = c;
                row.appendChild(seat);
            }

            seatPreview.appendChild(row);
        }

        const exitWrap = document.createElement('div');
        exitWrap.className = "seat-exit-row";
        exitWrap.innerHTML = `<div class="seat-preview seat-exit">RA</div>`;
        seatPreview.appendChild(exitWrap);
    }

    soHangInput.addEventListener('input', updateTongGhe);
    soCotInput.addEventListener('input', updateTongGhe);

    updateTongGhe();
});
</script>
@endpush
