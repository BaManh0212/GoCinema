@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gradient mb-1">
                <i class="bi bi-box-seam"></i> Thêm Combo Mới
            </h2>
            <p class="text-muted mb-0">Tạo combo sản phẩm mới để bán kèm vé hoặc ưu đãi</p>
        </div>
        <a href="{{ route('admin.combo.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left-circle"></i> Quay lại danh sách
        </a>
    </div>

    {{-- 🧾 Form thêm combo --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.combo.store') }}" method="POST" novalidate>
                @csrf

                {{-- 🔹 Thông tin Combo --}}
                <h5 class="fw-bold text-gradient mb-3"><i class="bi bi-info-circle"></i> Thông tin Combo</h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="ten" class="form-label fw-semibold">Tên Combo</label>
                        <input type="text" name="ten" id="ten"
                            class="form-control form-control-lg @error('ten') is-invalid @enderror"
                            placeholder="Nhập tên combo..." value="{{ old('ten') }}">
                        @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="gia" class="form-label fw-semibold">Giá Combo (VNĐ)</label>
                        <input type="number" name="gia" id="gia"
                            class="form-control form-control-lg @error('gia') is-invalid @enderror"
                            placeholder="Nhập giá combo..." value="{{ old('gia') }}">
                        @error('gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="mo_ta" class="form-label fw-semibold">Mô tả Combo</label>
                        <textarea name="mo_ta" id="mo_ta" rows="3"
                            class="form-control form-control-lg @error('mo_ta') is-invalid @enderror"
                            placeholder="Nhập mô tả combo...">{{ old('mo_ta') }}</textarea>
                        @error('mo_ta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 🔸 Chi tiết Combo --}}
                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <h5 class="fw-bold text-gradient mb-0"><i class="bi bi-cart-check"></i> Chi tiết Combo</h5>
                    <button type="button" id="add-combo-item" class="btn btn-sm btn-primary rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                    </button>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle text-center" id="combo-table">
                        <thead class="table-header text-white">
                            <tr>
                                <th width="45%">Sản phẩm</th>
                                <th width="20%">Số lượng</th>
                                <th width="25%">Tồn kho</th>
                                <th width="10%">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="combo-body">
                            @php $chiTietOld = old('chi_tiet', []); @endphp
                            @forelse ($chiTietOld as $index => $chiTiet)
                                <tr class="combo-item">
                                    <td>
                                        <select name="chi_tiet[{{ $index }}][san_pham_id]"
                                            class="form-select @error("chi_tiet.$index.san_pham_id") is-invalid @enderror">
                                            <option value="">-- Chọn sản phẩm --</option>
                                            @foreach ($sanPhams as $sp)
                                                <option value="{{ $sp->id }}" data-so-luong="{{ $sp->so_luong }}"
                                                    {{ old("chi_tiet.$index.san_pham_id") == $sp->id ? 'selected' : '' }}>
                                                    {{ $sp->ten }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("chi_tiet.$index.san_pham_id")
                                            <div class="invalid-feedback text-start">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" name="chi_tiet[{{ $index }}][so_luong]"
                                            class="form-control text-center @error("chi_tiet.$index.so_luong") is-invalid @enderror"
                                            min="1" value="{{ old("chi_tiet.$index.so_luong", 1) }}">
                                        @error("chi_tiet.$index.so_luong")
                                            <div class="invalid-feedback text-start">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="text-muted stock-info">
                                        @php
                                            $sp = $sanPhams->find($chiTiet['san_pham_id'] ?? null);
                                        @endphp
                                        {{ $sp ? $sp->so_luong . ' SP trong kho' : '-' }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-combo-item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-row" class="text-center text-muted">
                                    <td colspan="4">Chưa có sản phẩm nào được thêm</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 🔹 Số lượng combo --}}
                <div class="col-md-6 mb-4">
                    <label for="so_luong" class="form-label fw-semibold">Số lượng Combo</label>
                    <input type="number" name="so_luong" id="so_luong"
                        class="form-control form-control-lg @error('so_luong') is-invalid @enderror"
                        value="{{ old('so_luong', 1) }}" min="1">
                    @error('so_luong')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="max-combo-hint" class="form-text text-primary mt-1 fw-semibold"></div>
                </div>

                {{-- 🔘 Nút lưu --}}
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-4 rounded-pill shadow-sm">
                        <i class="bi bi-save"></i> Lưu Combo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.table-header {
    background: linear-gradient(90deg, #007bff, #00c3ff);
}
.table td, .table th {
    vertical-align: middle;
    padding: 0.9rem;
}
.combo-item:hover {
    background-color: #e9f5ff;
    transform: scale(1.01);
    transition: all 0.25s ease-in-out;
}
</style>

{{-- 🧠 Script --}}
<script>
    let comboIndex = {{ count(old('chi_tiet', [])) }};
    const sanPhamData = @json($sanPhams->keyBy('id'));

    document.getElementById('add-combo-item').addEventListener('click', () => {
        const tbody = document.getElementById('combo-body');
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

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

    document.addEventListener('click', e => {
        if (e.target.closest('.remove-combo-item')) {
            e.target.closest('tr').remove();
            const tbody = document.getElementById('combo-body');
            if (!tbody.querySelector('.combo-item')) {
                tbody.innerHTML = `<tr id="empty-row">
                    <td colspan="4" class="text-muted text-center">Chưa có sản phẩm nào được thêm</td>
                </tr>`;
            }
            tinhToiDaCombo();
        }
    });

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
        hint.textContent = (maxCombo === Infinity)
            ? ''
            : `👉 Có thể tạo tối đa ${maxCombo} combo theo tồn kho hiện tại.`;
    }
</script>
@endsection
