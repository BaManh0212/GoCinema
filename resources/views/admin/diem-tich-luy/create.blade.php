@extends('layouts.app')

@section('title', 'Thêm/Trừ điểm')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-star me-2"></i>Thêm/Trừ điểm cho người dùng</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.diem-tich-luy.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Người dùng <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select name="nguoi_dung_id" id="nguoi_dung_id" 
                                        class="form-select @error('nguoi_dung_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn người dùng --</option>
                                    @foreach($nguoiDungs as $nd)
                                        <option value="{{ $nd->id }}" 
                                                data-diem="{{ $nd->diem_tich_luy }}"
                                                {{ old('nguoi_dung_id', request('nguoi_dung_id')) == $nd->id ? 'selected' : '' }}>
                                            {{ $nd->ho_ten }} ({{ $nd->email }}) - {{ number_format($nd->diem_tich_luy) }} điểm
                                        </option>
                                    @endforeach
                                </select>
                                @error('nguoi_dung_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="diem_hien_tai" class="mt-2 text-muted" style="display: none;">
                                    Điểm hiện tại: <strong class="text-primary fs-5"><span id="so_diem">0</span> điểm</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Loại giao dịch <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="hanh_dong" id="tich_luy" 
                                           value="tich_luy" {{ old('hanh_dong', 'tich_luy') == 'tich_luy' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="tich_luy">
                                        <i class="fas fa-plus-circle me-2"></i>Thêm điểm (Tích lũy)
                                    </label>

                                    <input type="radio" class="btn-check" name="hanh_dong" id="su_dung" 
                                           value="su_dung" {{ old('hanh_dong') == 'su_dung' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="su_dung">
                                        <i class="fas fa-minus-circle me-2"></i>Trừ điểm (Sử dụng)
                                    </label>
                                </div>
                                @error('hanh_dong')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Số điểm <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" name="diem" id="diem" 
                                       class="form-control @error('diem') is-invalid @enderror" 
                                       value="{{ old('diem') }}" min="1" required>
                                @error('diem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nhập số điểm muốn thêm hoặc trừ</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Mô tả <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror" 
                                          rows="3" required>{{ old('mo_ta') }}</textarea>
                                @error('mo_ta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Lý do thêm/trừ điểm</small>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div id="preview" class="alert alert-info" style="display: none;">
                            <h6><i class="fas fa-info-circle me-2"></i>Xem trước:</h6>
                            <p class="mb-0">
                                <span id="preview_action"></span> 
                                <strong id="preview_diem"></strong> điểm
                                <br>
                                Điểm sau giao dịch: <strong id="preview_after"></strong> điểm
                            </p>
                        </div>

                        <hr>

                        <div class="text-end">
                            <a href="{{ route('admin.diem-tich-luy.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Xác nhận
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
document.addEventListener('DOMContentLoaded', function() {
    const nguoiDungSelect = document.getElementById('nguoi_dung_id');
    const diemInput = document.getElementById('diem');
    const hanhDongRadios = document.querySelectorAll('input[name="hanh_dong"]');
    const diemHienTaiDiv = document.getElementById('diem_hien_tai');
    const soDiemSpan = document.getElementById('so_diem');
    const previewDiv = document.getElementById('preview');
    
    let currentDiem = 0;

    // Hiển thị điểm hiện tại khi chọn người dùng
    nguoiDungSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            currentDiem = parseInt(selectedOption.getAttribute('data-diem')) || 0;
            soDiemSpan.textContent = currentDiem.toLocaleString();
            diemHienTaiDiv.style.display = 'block';
            updatePreview();
        } else {
            diemHienTaiDiv.style.display = 'none';
            previewDiv.style.display = 'none';
        }
    });

    // Trigger nếu đã có người dùng được chọn từ trước
    if (nguoiDungSelect.value) {
        nguoiDungSelect.dispatchEvent(new Event('change'));
    }

    // Update preview khi thay đổi
    diemInput.addEventListener('input', updatePreview);
    hanhDongRadios.forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });

    function updatePreview() {
        const diem = parseInt(diemInput.value) || 0;
        const hanhDong = document.querySelector('input[name="hanh_dong"]:checked')?.value;
        
        if (nguoiDungSelect.value && diem > 0 && hanhDong) {
            let afterDiem;
            let action;
            
            if (hanhDong === 'tich_luy') {
                afterDiem = currentDiem + diem;
                action = '<span class="text-success">Thêm</span>';
                document.getElementById('preview_diem').className = 'text-success';
            } else {
                afterDiem = currentDiem - diem;
                action = '<span class="text-danger">Trừ</span>';
                document.getElementById('preview_diem').className = 'text-danger';
            }
            
            document.getElementById('preview_action').innerHTML = action;
            document.getElementById('preview_diem').textContent = diem.toLocaleString();
            document.getElementById('preview_after').textContent = afterDiem.toLocaleString();
            
            previewDiv.style.display = 'block';
        } else {
            previewDiv.style.display = 'none';
        }
    }
});
</script>
@endpush
@endsection
