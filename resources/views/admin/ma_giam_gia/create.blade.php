@extends('admin.layouts.admin')

@section('title', 'Thêm Mã Giảm Giá')

@section('content')
<div class="container py-4">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-gradient mb-0">
            🎟️ Thêm Mã Giảm Giá
        </h2>
        <a href="{{ route('admin.ma_giam_gia.index') }}" class="btn btn-outline-primary rounded-3 px-4 fw-semibold">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ===== FORM ===== --}}
    <div class="card border-0 shadow-lg rounded-4 p-4">
        <form action="{{ route('admin.ma_giam_gia.store') }}" method="POST" id="voucherForm">
            @csrf

            <div class="row g-4">
                {{-- Mã giảm giá --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary">Mã giảm giá</label>
                    <input type="text" name="ma"
                        class="form-control form-control-lg @error('ma') is-invalid @enderror"
                        placeholder="Nhập mã (VD: GOCINEMA50)" value="{{ old('ma') }}" required>
                    @error('ma') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Loại giảm giá --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary">Loại giảm giá</label>
                    <select name="loai" id="loai"
                        class="form-select form-select-lg @error('loai') is-invalid @enderror" required>
                        <option value="phan_tram" {{ old('loai') == 'phan_tram' ? 'selected' : '' }}>Giảm theo %</option>
                        <option value="so_tien" {{ old('loai') == 'so_tien' ? 'selected' : '' }}>Giảm theo số tiền</option>
                    </select>
                    @error('loai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Giá trị giảm --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary">Giá trị giảm</label>
                    <input type="number" min="1" name="gia_tri"
                        class="form-control form-control-lg @error('gia_tri') is-invalid @enderror"
                        placeholder="VD: 10 hoặc 50000" value="{{ old('gia_tri') }}" required>
                    @error('gia_tri') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Giảm tối đa --}}
                <div class="col-md-4" id="giam_toi_da_wrapper" style="display:none;">
                    <label class="form-label fw-semibold text-primary">Giảm tối đa (VNĐ)</label>
                    <input type="number" min="1" name="giam_toi_da"
                        class="form-control form-control-lg @error('giam_toi_da') is-invalid @enderror"
                        placeholder="Nhập số tiền tối đa" value="{{ old('giam_toi_da') }}">
                    @error('giam_toi_da') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Giá trị đơn hàng tối thiểu --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary">Đơn hàng tối thiểu (VNĐ)</label>
                    <input type="number" min="0" name="gia_tri_don_hang_toi_thieu"
                        class="form-control form-control-lg @error('gia_tri_don_hang_toi_thieu') is-invalid @enderror"
                        placeholder="Để trống nếu không yêu cầu" value="{{ old('gia_tri_don_hang_toi_thieu') }}">
                    @error('gia_tri_don_hang_toi_thieu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Giá trị đơn hàng tối thiểu để áp dụng mã</small>
                </div>

                {{-- Áp dụng cho --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary">Áp dụng cho</label>
                    <select name="ap_dung_cho"
                        class="form-select form-select-lg @error('ap_dung_cho') is-invalid @enderror">
                        <option value="tat_ca" {{ old('ap_dung_cho') == 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
                        <option value="ve" {{ old('ap_dung_cho') == 've' ? 'selected' : '' }}>Vé</option>
                        <option value="san_pham" {{ old('ap_dung_cho') == 'san_pham' ? 'selected' : '' }}>Sản phẩm</option>
                    </select>
                    @error('ap_dung_cho') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Số lượng --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary">Số lượng</label>
                    <input type="number" min="1" name="so_luong"
                        class="form-control form-control-lg @error('so_luong') is-invalid @enderror"
                        value="{{ old('so_luong') }}" required>
                    @error('so_luong') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Ngày bắt đầu --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary">Ngày bắt đầu</label>
                    <input type="date" name="ngay_bat_dau" id="ngay_bat_dau"
                        class="form-control form-control-lg @error('ngay_bat_dau') is-invalid @enderror"
                        value="{{ old('ngay_bat_dau') }}">
                    @error('ngay_bat_dau') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Ngày kết thúc --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary">Ngày kết thúc</label>
                    <input type="date" name="ngay_ket_thuc" id="ngay_ket_thuc"
                        class="form-control form-control-lg @error('ngay_ket_thuc') is-invalid @enderror"
                        value="{{ old('ngay_ket_thuc') }}">
                    @error('ngay_ket_thuc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Nút lưu --}}
            <div class="mt-5 text-end">
                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3 shadow-sm fw-semibold">
                    <i class="bi bi-save"></i> Lưu mã giảm giá
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== SCRIPT ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectLoai = document.getElementById('loai');
    const giamToiDaWrapper = document.getElementById('giam_toi_da_wrapper');
    const ngayBatDau = document.getElementById('ngay_bat_dau');
    const ngayKetThuc = document.getElementById('ngay_ket_thuc');

    function toggleGiamToiDa() {
        giamToiDaWrapper.style.display = selectLoai.value === 'phan_tram' ? 'block' : 'none';
    }

    function validateDates() {
        if (ngayBatDau.value && ngayKetThuc.value && ngayKetThuc.value < ngayBatDau.value) {
            alert('⛔ Ngày kết thúc không được nhỏ hơn ngày bắt đầu!');
            ngayKetThuc.value = '';
        }
    }

    toggleGiamToiDa();
    selectLoai.addEventListener('change', toggleGiamToiDa);
    ngayKetThuc.addEventListener('change', validateDates);
});
</script>

{{-- ===== STYLE ===== --}}
@push('styles')
<style>
.text-gradient {
    background: linear-gradient(90deg, #0d6efd, #4e9cff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.card {
    background-color: #fff;
    border-radius: 16px;
}
.form-label {
    font-size: 15px;
}
.form-control-lg, .form-select-lg {
    border-radius: 12px;
    padding: 0.75rem 1rem;
}
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
.btn-primary {
    background: linear-gradient(90deg, #0d6efd, #4e9cff);
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #0b5ed7, #3c8bff);
}
.btn-outline-primary {
    border-color: #0d6efd;
    color: #0d6efd;
}
.btn-outline-primary:hover {
    background: #0d6efd;
    color: white;
}
</style>
@endpush
@endsection
