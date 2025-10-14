@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary">✏️ Sửa Combo</h2>
    <a href="{{ route('admin.combo.index') }}" class="btn btn-secondary mb-3">⬅ Quay lại danh sách</a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.combo.update', $combo->id) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                {{-- Tên Combo --}}
                <div class="form-group mb-3">
                    <label for="ten">Tên Combo</label>
                    <input type="text" name="ten" id="ten" class="form-control @error('ten') is-invalid @enderror" value="{{ old('ten', $combo->ten) }}">
                    @error('ten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Giá --}}
                <div class="form-group mb-3">
                    <label for="gia">Giá Combo</label>
                    <input type="number" name="gia" id="gia" class="form-control @error('gia') is-invalid @enderror" value="{{ old('gia', $combo->gia) }}">
                    @error('gia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mô tả --}}
                <div class="form-group mb-3">
                    <label for="mo_ta">Mô tả</label>
                    <textarea name="mo_ta" id="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror">{{ old('mo_ta', $combo->mo_ta) }}</textarea>
                    @error('mo_ta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Chi tiết Combo --}}
                <div id="combo-chi-tiet">
                    <h4 class="fw-bold">Chi tiết Combo</h4>
                    @foreach ($combo->chiTiet as $index => $chiTiet)
                        <div class="combo-item mb-3 d-flex align-items-center">
                            <div class="flex-grow-1 me-3">
                                <label for="san_pham_id_{{ $index }}" class="form-label">Sản phẩm</label>
                                <select name="chi_tiet[{{ $index }}][san_pham_id]" id="san_pham_id_{{ $index }}" class="form-select">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($sanPhams as $sanPham)
                                        <option value="{{ $sanPham->id }}" {{ old("chi_tiet.$index.san_pham_id", $chiTiet->san_pham_id) == $sanPham->id ? 'selected' : '' }}>
                                            {{ $sanPham->ten }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1 me-3">
                                <label for="so_luong_{{ $index }}" class="form-label">Số lượng</label>
                                <input type="number" name="chi_tiet[{{ $index }}][so_luong]" id="so_luong_{{ $index }}" class="form-control" value="{{ old("chi_tiet.$index.so_luong", $chiTiet->so_luong) }}">
                            </div>
                            <button type="button" class="btn btn-danger remove-combo-item" data-index="{{ $index }}">Xóa</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-combo-item" class="btn btn-primary mb-3">➕ Thêm sản phẩm</button>

                {{-- Nút Submit --}}
                <button type="submit" class="btn btn-success">Cập nhật</button>
            </form>
        </div>
    </div>
</div>

<script>
    let comboIndex = {{ $combo->chiTiet->count() }};
    document.getElementById('add-combo-item').addEventListener('click', function () {
        const comboChiTiet = document.getElementById('combo-chi-tiet');
        const newItem = `
            <div class="combo-item mb-3 d-flex align-items-center">
                <div class="flex-grow-1 me-3">
                    <label for="san_pham_id_${comboIndex}" class="form-label">Sản phẩm</label>
                    <select name="chi_tiet[${comboIndex}][san_pham_id]" id="san_pham_id_${comboIndex}" class="form-select">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($sanPhams as $sanPham)
                            <option value="{{ $sanPham->id }}">{{ $sanPham->ten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1 me-3">
                    <label for="so_luong_${comboIndex}" class="form-label">Số lượng</label>
                    <input type="number" name="chi_tiet[${comboIndex}][so_luong]" id="so_luong_${comboIndex}" class="form-control" value="1">
                </div>
                <button type="button" class="btn btn-danger remove-combo-item" data-index="${comboIndex}">Xóa</button>
            </div>
        `;
        comboChiTiet.insertAdjacentHTML('beforeend', newItem);
        comboIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-combo-item')) {
            const index = e.target.getAttribute('data-index');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `chi_tiet[${index}][_delete]`;
            input.value = '1';
            e.target.closest('.combo-item').appendChild(input);
            e.target.closest('.combo-item').style.display = 'none';
        }
    });
</script>
@endsection