@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-primary mb-0"><i class="bi bi-box-seam"></i> Thêm Combo Mới</h2>
        <a href="{{ route('admin.combo.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body">
            <form action="{{ route('admin.combo.store') }}" method="POST" novalidate>
                @csrf

                <div class="row g-3">
                    {{-- Tên Combo --}}
                    <div class="col-md-6">
                        <label for="ten" class="form-label fw-semibold">Tên Combo</label>
                        <input type="text" name="ten" id="ten" 
                            class="form-control form-control-lg @error('ten') is-invalid @enderror" 
                            placeholder="Nhập tên combo..." value="{{ old('ten') }}">
                        @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giá --}}
                    <div class="col-md-6">
                        <label for="gia" class="form-label fw-semibold">Giá Combo (VNĐ)</label>
                        <input type="number" name="gia" id="gia" 
                            class="form-control form-control-lg @error('gia') is-invalid @enderror" 
                            placeholder="Nhập giá combo..." value="{{ old('gia') }}">
                        @error('gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Số lượng Combo --}}
                    <div class="col-md-6">
                        <label for="so_luong" class="form-label fw-semibold">Số lượng Combo</label>
                        <input type="number" name="so_luong" id="so_luong" 
                            class="form-control form-control-lg @error('so_luong') is-invalid @enderror" 
                            value="{{ old('so_luong', 1) }}" min="1">
                        @error('so_luong')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="col-md-12">
                        <label for="mo_ta" class="form-label fw-semibold">Mô tả Combo</label>
                        <textarea name="mo_ta" id="mo_ta" rows="3" 
                            class="form-control @error('mo_ta') is-invalid @enderror" 
                            placeholder="Nhập mô tả combo...">{{ old('mo_ta') }}</textarea>
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
                                <th width="50%">Sản phẩm</th>
                                <th width="25%">Số lượng</th>
                                <th width="20%">Tồn kho</th>
                                <th width="5%">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="combo-body">
                            @php $chiTietOld = old('chi_tiet', []); @endphp
                            @forelse ($chiTietOld as $index => $chiTiet)
                                <tr class="combo-item">
                                    <td>
                                        <select name="chi_tiet[{{ $index }}][san_pham_id]" class="form-select">
                                            <option value="">-- Chọn sản phẩm --</option>
                                            @foreach ($sanPhams as $sp)
                                                <option value="{{ $sp->id }}" data-so-luong="{{ $sp->so_luong }}"
                                                    {{ old("chi_tiet.$index.san_pham_id") == $sp->id ? 'selected' : '' }}>
                                                    {{ $sp->ten }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="chi_tiet[{{ $index }}][so_luong]" 
                                            class="form-control text-center" min="1"
                                            value="{{ old("chi_tiet.$index.so_luong", 1) }}">
                                    </td>
                                    <td class="text-muted stock-info">-</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-combo-item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-muted text-center" id="empty-row">
                                    <td colspan="4">Chưa có sản phẩm nào được thêm</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($errors->has('chi_tiet'))
                    <div class="alert alert-danger mt-2">{{ $errors->first('chi_tiet') }}</div>
                @endif

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="bi bi-save"></i> Lưu Combo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let comboIndex = {{ count(old('chi_tiet', [])) }};
    const sanPhamData = @json($sanPhams->keyBy('id'));

    // ➕ Thêm dòng mới
    document.getElementById('add-combo-item').addEventListener('click', function () {
        const tbody = document.getElementById('combo-body');
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        const newRow = document.createElement('tr');
        newRow.classList.add('combo-item');
        newRow.innerHTML = `
            <td>
                <select name="chi_tiet[${comboIndex}][san_pham_id]" class="form-select">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach ($sanPhams as $sp)
                        <option value="{{ $sp->id }}" data-so-luong="{{ $sp->so_luong }}">{{ $sp->ten }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="chi_tiet[${comboIndex}][so_luong]" class="form-control text-center" min="1" value="1"></td>
            <td class="text-muted stock-info">-</td>
            <td><button type="button" class="btn btn-outline-danger btn-sm remove-combo-item"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(newRow);
        comboIndex++;
    });

    // ❌ Xóa dòng
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-combo-item')) {
            e.target.closest('tr').remove();
            const tbody = document.getElementById('combo-body');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `<tr id="empty-row"><td colspan="4" class="text-muted text-center">Chưa có sản phẩm nào được thêm</td></tr>`;
            }
        }
    });

    // 🔁 Cập nhật tồn kho
    document.addEventListener('change', function (e) {
        if (e.target.tagName === 'SELECT' && e.target.closest('.combo-item')) {
            const tr = e.target.closest('tr');
            const sanPhamId = e.target.value;
            const stockInfo = tr.querySelector('.stock-info');
            if (sanPhamId && sanPhamData[sanPhamId]) {
                stockInfo.textContent = `${sanPhamData[sanPhamId].so_luong} SP trong kho`;
            } else {
                stockInfo.textContent = '-';
            }
        }
    });
</script>
@endsection
