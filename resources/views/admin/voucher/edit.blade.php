@extends('admin.layouts.admin')

@section('title', 'Sửa voucher đổi điểm')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Sửa voucher đổi điểm
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.voucher.update', $voucher->id) }}" id="voucherForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="ten" class="form-label">Tên voucher <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('ten') is-invalid @enderror" 
                                       id="ten" 
                                       name="ten" 
                                       value="{{ old('ten', $voucher->ten) }}"
                                       placeholder="Ví dụ: Voucher 1.000.000đ"
                                       required>
                                @error('ten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="diem_can" class="form-label">Điểm cần <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('diem_can') is-invalid @enderror" 
                                       id="diem_can" 
                                       name="diem_can" 
                                       value="{{ old('diem_can', $voucher->diem_can) }}"
                                       min="1"
                                       placeholder="VD: 200"
                                       required>
                                @error('diem_can')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="loai" class="form-label">Loại voucher <span class="text-danger">*</span></label>
                                <select class="form-select @error('loai') is-invalid @enderror" 
                                        id="loai" 
                                        name="loai"
                                        onchange="toggleGiamToiDa()" 
                                        required>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="phan_tram" {{ old('loai', $voucher->loai) == 'phan_tram' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    <option value="so_tien" {{ old('loai', $voucher->loai) == 'so_tien' ? 'selected' : '' }}>Số tiền (đ)</option>
                                </select>
                                @error('loai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="gia_tri" class="form-label">Giá trị <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('gia_tri') is-invalid @enderror" 
                                       id="gia_tri" 
                                       name="gia_tri" 
                                       value="{{ old('gia_tri', $voucher->gia_tri) }}"
                                       min="0"
                                       step="0.01"
                                       placeholder="VD: 10 hoặc 50000"
                                       required>
                                @error('gia_tri')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3" id="giam_toi_da_group" style="display: none;">
                                <label for="giam_toi_da" class="form-label">Giảm tối đa</label>
                                <input type="number" 
                                       class="form-control @error('giam_toi_da') is-invalid @enderror" 
                                       id="giam_toi_da" 
                                       name="giam_toi_da" 
                                       value="{{ old('giam_toi_da', $voucher->giam_toi_da) }}"
                                       min="0"
                                       step="0.01"
                                       placeholder="VD: 50000">
                                @error('giam_toi_da')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Chỉ cho voucher %</small>
                            </div>

                            <div class="col-md-3">
                                <label for="ap_dung_cho" class="form-label">Áp dụng cho <span class="text-danger">*</span></label>
                                <select class="form-select @error('ap_dung_cho') is-invalid @enderror" 
                                        id="ap_dung_cho" 
                                        name="ap_dung_cho" 
                                        required>
                                    <option value="tat_ca" {{ old('ap_dung_cho', $voucher->ap_dung_cho) == 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
                                    <option value="ve" {{ old('ap_dung_cho', $voucher->ap_dung_cho) == 've' ? 'selected' : '' }}>Vé xem phim</option>
                                    <option value="san_pham" {{ old('ap_dung_cho', $voucher->ap_dung_cho) == 'san_pham' ? 'selected' : '' }}>Sản phẩm</option>
                                </select>
                                @error('ap_dung_cho')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="gia_tri_don_hang_toi_thieu" class="form-label">Giá trị đơn hàng tối thiểu</label>
                                <input type="number" 
                                       class="form-control @error('gia_tri_don_hang_toi_thieu') is-invalid @enderror" 
                                       id="gia_tri_don_hang_toi_thieu" 
                                       name="gia_tri_don_hang_toi_thieu" 
                                       value="{{ old('gia_tri_don_hang_toi_thieu', $voucher->gia_tri_don_hang_toi_thieu) }}"
                                       min="0"
                                       step="0.01"
                                       placeholder="VD: 50000">
                                @error('gia_tri_don_hang_toi_thieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Để trống nếu không giới hạn</small>
                            </div>

                            <div class="col-md-4">
                                <label for="so_luong_toi_da" class="form-label">Số lượng voucher <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('so_luong_toi_da') is-invalid @enderror" 
                                       id="so_luong_toi_da" 
                                       name="so_luong_toi_da" 
                                       value="{{ old('so_luong_toi_da', $voucher->so_luong_toi_da) }}"
                                       min="1"
                                       placeholder="VD: 100"
                                       required>
                                @error('so_luong_toi_da')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Đã dùng: {{ $voucher->so_luong_da_dung }}/{{ $voucher->so_luong_toi_da }}</small>
                            </div>

                            <div class="col-md-4">
                                <label for="so_lan_su_dung" class="form-label">Số lần sử dụng <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('so_lan_su_dung') is-invalid @enderror" 
                                       id="so_lan_su_dung" 
                                       name="so_lan_su_dung" 
                                       value="{{ old('so_lan_su_dung', $voucher->so_lan_su_dung) }}"
                                       min="1"
                                       required>
                                @error('so_lan_su_dung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label d-block">Trạng thái</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="kich_hoat" 
                                           name="kich_hoat"
                                           {{ old('kich_hoat', $voucher->kich_hoat) ? 'checked' : '' }}
                                           style="width: 48px; height: 24px;">
                                    <label class="form-check-label ms-2" for="kich_hoat">
                                        Kích hoạt voucher
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu</label>
                                <input type="date" 
                                       class="form-control @error('ngay_bat_dau') is-invalid @enderror" 
                                       id="ngay_bat_dau" 
                                       name="ngay_bat_dau" 
                                       value="{{ old('ngay_bat_dau', $voucher->ngay_bat_dau?->format('Y-m-d')) }}">
                                @error('ngay_bat_dau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Để trống nếu bắt đầu ngay</small>
                            </div>

                            <div class="col-md-6">
                                <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc</label>
                                <input type="date" 
                                       class="form-control @error('ngay_ket_thuc') is-invalid @enderror" 
                                       id="ngay_ket_thuc" 
                                       name="ngay_ket_thuc" 
                                       value="{{ old('ngay_ket_thuc', $voucher->ngay_ket_thuc?->format('Y-m-d')) }}">
                                @error('ngay_ket_thuc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Để trống nếu không giới hạn</small>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.voucher.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle hiển thị trường "Giảm tối đa" khi chọn loại voucher
function toggleGiamToiDa() {
    const loai = document.getElementById('loai').value;
    const giamToiDaGroup = document.getElementById('giam_toi_da_group');
    
    if (loai === 'phan_tram') {
        giamToiDaGroup.style.display = 'block';
    } else {
        giamToiDaGroup.style.display = 'none';
    }
}

// Gọi khi load trang
document.addEventListener('DOMContentLoaded', function() {
    toggleGiamToiDa();
});
</script>
@endpush

@push('scripts')
<script>
document.getElementById('voucherForm').addEventListener('submit', function(e) {
    const ngayBatDau = document.getElementById('ngay_bat_dau').value;
    const ngayKetThuc = document.getElementById('ngay_ket_thuc').value;
    
    if (ngayBatDau && ngayKetThuc && ngayKetThuc < ngayBatDau) {
        e.preventDefault();
        alert('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu!');
        return false;
    }
});
</script>
@endpush
@endsection
