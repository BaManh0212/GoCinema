@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary">➕ Thêm Combo</h2>
    <a href="{{ route('admin.combo.index') }}" class="btn btn-secondary mb-3">⬅ Quay lại danh sách</a>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.combo.store') }}" method="POST" novalidate>
                @csrf

                {{-- Tên Combo --}}
                <div class="mb-3">
                    <label for="ten" class="form-label">Tên Combo</label>
                    <input type="text" name="ten" id="ten" class="form-control @error('ten') is-invalid @enderror" value="{{ old('ten') }}">
                    @error('ten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Giá --}}
                <div class="mb-3">
                    <label for="gia" class="form-label">Giá (VNĐ)</label>
                    <input type="number" name="gia" id="gia" class="form-control @error('gia') is-invalid @enderror" value="{{ old('gia') }}">
                    @error('gia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mô tả --}}
                <div class="mb-3">
                    <label for="mo_ta" class="form-label">Mô tả</label>
                    <textarea name="mo_ta" id="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror">{{ old('mo_ta') }}</textarea>
                    @error('mo_ta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Chi tiết Combo --}}
                <div id="combo-chi-tiet">
                    <h4 class="fw-bold">Chi tiết Combo</h4>
                    <div class="combo-item mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="san_pham_id" class="form-label">Sản phẩm</label>
                                <select name="chi_tiet[0][san_pham_id]" class="form-select @error('chi_tiet.0.san_pham_id') is-invalid @enderror">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($sanPhams as $sanPham)
                                        <option value="{{ $sanPham->id }}">{{ $sanPham->ten }}</option>
                                    @endforeach
                                </select>
                                @error('chi_tiet.0.san_pham_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="so_luong" class="form-label">Số lượng</label>
                                <input type="number" name="chi_tiet[0][so_luong]" class="form-control @error('chi_tiet.0.so_luong') is-invalid @enderror" value="{{ old('chi_tiet.0.so_luong') }}">
                                @error('chi_tiet.0.so_luong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-combo-item">Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-combo-item" class="btn btn-primary mb-3">➕ Thêm sản phẩm</button>

                {{-- Nút Submit --}}
                <button type="submit" class="btn btn-success">Lưu</button>
            </form>
        </div>
    </div>
</div>

<script>
    let comboIndex = 1;
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
                        <input type="number" name="chi_tiet[${comboIndex}][so_luong]" class="form-control">
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
            e.target.closest('.combo-item').remove();
        }
    });
</script>
@endsection