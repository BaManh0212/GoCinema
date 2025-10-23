@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-primary mb-0">
            ✏️ Cập nhật Combo
        </h2>
        <a href="{{ route('admin.combo.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    {{-- Card --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.combo.update', $combo->id) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                {{-- Thông tin Combo --}}
                <div class="row g-3">
                    {{-- Tên --}}
                    <div class="col-md-6">
                        <label for="ten" class="form-label fw-semibold">Tên Combo</label>
                        <input type="text" name="ten" id="ten"
                            class="form-control form-control-lg @error('ten') is-invalid @enderror"
                            placeholder="Nhập tên combo..." value="{{ old('ten', $combo->ten) }}">
                        @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giá --}}
                    <div class="col-md-6">
                        <label for="gia" class="form-label fw-semibold">Giá Combo (VNĐ)</label>
                        <input type="number" name="gia" id="gia"
                            class="form-control form-control-lg @error('gia') is-invalid @enderror"
                            placeholder="Nhập giá combo..." value="{{ old('gia', $combo->gia) }}">
                        @error('gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="col-md-12">
                        <label for="mo_ta" class="form-label fw-semibold">Mô tả Combo</label>
                        <textarea name="mo_ta" id="mo_ta" rows="3"
                            class="form-control form-control-lg @error('mo_ta') is-invalid @enderror"
                            placeholder="Nhập mô tả combo...">{{ old('mo_ta', $combo->mo_ta) }}</textarea>
                        @error('mo_ta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Chi tiết Combo --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-cart-check"></i> Chi tiết Combo</h5>
                    <button type="button" id="add-combo-item" class="btn btn-sm btn-primary rounded-pill">
                        <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center" id="combo-table">
                        <thead class="table-light">
                            <tr>
                                <th width="45%">Sản phẩm</th>
                                <th width="20%">Số lượng</th>
                                <th width="25%">Tồn kho</th>
                                <th width="10%">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="combo-body">
                            @php $chiTietOld = old('chi_tiet', $combo->chiTiet->toArray()); @endphp
                            @foreach ($chiTietOld as $index => $chiTiet)
                                @php
                                    $sp = $sanPhams->find($chiTiet['san_pham_id'] ?? null);
                                    $tonKho = $sp ? $sp->so_luong + ($chiTiet['so_luong'] * $combo->so_luong) : 0;
                                @endphp
                                <tr class="combo-item">
                                    <td>
                                        <select name="chi_tiet[{{ $index }}][san_pham_id]"
                                            class="form-select select-sanpham @error("chi_tiet.$index.san_pham_id") is-invalid @enderror">
                                            <option value="">-- Chọn sản phẩm --</option>
                                            @foreach ($sanPhams as $spLoop)
                                                <option value="{{ $spLoop->id }}" data-so-luong="{{ $spLoop->so_luong + ($chiTiet['so_luong'] * $combo->so_luong) }}"
                                                    {{ old("chi_tiet.$index.san_pham_id", $chiTiet['san_pham_id']) == $spLoop->id ? 'selected' : '' }}>
                                                    {{ $spLoop->ten }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("chi_tiet.$index.san_pham_id")
                                            <div class="invalid-feedback text-start">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" name="chi_tiet[{{ $index }}][so_luong]"
                                            class="form-control text-center so-luong-input @error("chi_tiet.$index.so_luong") is-invalid @enderror"
                                            min="1" value="{{ old("chi_tiet.$index.so_luong", $chiTiet['so_luong']) }}">
                                        @error("chi_tiet.$index.so_luong")
                                            <div class="invalid-feedback text-start">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="text-muted stock-info">
                                        {{ $sp ? $tonKho . ' SP trong kho' : '-' }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-combo-item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Số lượng combo --}}
                <div class="col-md-6">
                    <label for="so_luong" class="form-label fw-semibold">Số lượng Combo</label>
                    <input type="number" name="so_luong" id="so_luong"
                        class="form-control form-control-lg @error('so_luong') is-invalid @enderror"
                        value="{{ old('so_luong', $combo->so_luong) }}" min="1">
                    @error('so_luong')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="max-combo-hint" class="form-text text-primary mt-1 fw-semibold"></div>
                </div>

                {{-- Nút lưu --}}
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
    let comboIndex = {{ count(old('chi_tiet', $combo->chiTiet->toArray())) }};
    const sanPhamData = @json($sanPhams->keyBy('id'));

    // ➕ Thêm dòng
    document.getElementById('add-combo-item').addEventListener('click', () => {
        const tbody = document.getElementById('combo-body');
        const tr = document.createElement('tr');
        tr.classList.add('combo-item');
        tr.innerHTML = `
            <td>
                <select name="chi_tiet[${comboIndex}][san_pham_id]" class="form-select select-sanpham">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach ($sanPhams as $sp)
                        <option value="{{ $sp->id }}" data-so-luong="{{ $sp->so_luong }}">{{ $sp->ten }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="chi_tiet[${comboIndex}][so_luong]" 
                    class="form-control text-center so-luong-input" min="1" value="1" disabled>
            </td>
            <td class="text-muted stock-info">-</td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm remove-combo-item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        comboIndex++;
    });

    // ❌ Xóa dòng
    document.addEventListener('click', e => {
        if (e.target.closest('.remove-combo-item')) {
            e.target.closest('tr').remove();
            tinhToiDaCombo();
        }
    });

    // 🔁 Khi chọn sản phẩm
    document.addEventListener('change', e => {
        if (e.target.classList.contains('select-sanpham')) {
            const tr = e.target.closest('tr');
            const stockInfo = tr.querySelector('.stock-info');
            const soLuongInput = tr.querySelector('.so-luong-input');
            const sanPhamId = e.target.value;

            if (sanPhamId && sanPhamData[sanPhamId]) {
                const tonKho = sanPhamData[sanPhamId].so_luong;
                stockInfo.textContent = `${tonKho} SP trong kho`;
                soLuongInput.disabled = false;
                soLuongInput.max = tonKho;
            } else {
                stockInfo.textContent = '-';
                soLuongInput.value = 1;
                soLuongInput.disabled = true;
            }

            tinhToiDaCombo();
        }
    });

    // 🧮 Tính số combo tối đa
    document.addEventListener('input', e => {
        if (e.target.classList.contains('so-luong-input')) {
            tinhToiDaCombo();
        }
    });

    function tinhToiDaCombo() {
        const rows = document.querySelectorAll('.combo-item');
        let maxCombo = Infinity;

        rows.forEach(row => {
            const select = row.querySelector('.select-sanpham');
            const input = row.querySelector('.so-luong-input');
            const sanPhamId = select.value;
            const soLuong = parseInt(input.value || 0);

            if (sanPhamId && sanPhamData[sanPhamId] && soLuong > 0) {
                const tonKho = sanPhamData[sanPhamId].so_luong;
                const comboTheoSP = Math.floor(tonKho / soLuong);
                maxCombo = Math.min(maxCombo, comboTheoSP);
            }
        });

        const hint = document.getElementById('max-combo-hint');
        if (maxCombo === Infinity) {
            hint.textContent = '';
        } else {
            hint.textContent = `👉 Có thể tạo tối đa ${maxCombo} combo theo tồn kho hiện tại.`;
        }
    }
</script>
@endsection
