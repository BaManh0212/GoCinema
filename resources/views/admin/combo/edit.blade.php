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
                <div class="mb-3">
                    <label for="ten" class="form-label">Tên Combo</label>
                    <input type="text" name="ten" id="ten" class="form-control @error('ten') is-invalid @enderror" value="{{ old('ten', $combo->ten) }}">
                    @error('ten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Giá --}}
                <div class="mb-3">
                    <label for="gia" class="form-label">Giá (VNĐ)</label>
                    <input type="number" name="gia" id="gia" class="form-control @error('gia') is-invalid @enderror" value="{{ old('gia', $combo->gia) }}">
                    @error('gia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mô tả --}}
                <div class="mb-3">
                    <label for="mo_ta" class="form-label">Mô tả</label>
                    <textarea name="mo_ta" id="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror">{{ old('mo_ta', $combo->mo_ta) }}</textarea>
                    @error('mo_ta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Chi tiết Combo --}}
                <div id="combo-chi-tiet">
                    @if ($combo->chiTiet->isEmpty())
                        <p class="text-muted">Không có chi tiết combo nào.</p>
                    @else
                        @foreach ($combo->chiTiet as $index => $chiTiet)
                            <div class="combo-item mb-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="san_pham_id" class="form-label">Sản phẩm</label>
                                        <select name="chi_tiet[{{ $index }}][san_pham_id]" class="form-select">
                                            <option value="">-- Chọn sản phẩm --</option>
                                            @foreach ($sanPhams as $sanPham)
                                                <option value="{{ $sanPham->id }}" {{ $sanPham->id == $chiTiet->san_pham_id ? 'selected' : '' }}>
                                                    {{ $sanPham->ten }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="so_luong" class="form-label">Số lượng</label>
                                        <input type="number" name="chi_tiet[{{ $index }}][so_luong]" class="form-control" value="{{ $chiTiet->so_luong }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-combo-item" data-index="{{ $index }}">Xóa</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
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
            <div class="combo-item mb-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="san_pham_id" class="form-label">Sản phẩm</label>
                        <select name="chi_tiet[${comboIndex}][san_pham_id]" class="form-select">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach ($sanPhams as $sanPham)
                                <option value="{{ $sanPham->id }}">{{ $sanPham->ten }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="so_luong" class="form-label">Số lượng</label>
                        <input type="number" name="chi_tiet[${comboIndex}][so_luong]" class="form-control" value="1">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-combo-item">Xóa</button>
                    </div>
                </div>
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