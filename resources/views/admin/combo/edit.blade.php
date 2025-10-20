@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-primary">✏️ Cập nhật Combo</h2>
        <a href="{{ route('admin.combo.index') }}" class="btn btn-outline-secondary rounded-pill">
            ⬅ Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.combo.update', $combo->id) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                {{-- Thông tin chung --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="ten" class="form-label fw-semibold">📦 Tên Combo</label>
                        <input type="text" name="ten" id="ten"
                            class="form-control form-control-lg @error('ten') is-invalid @enderror"
                            value="{{ old('ten', $combo->ten) }}">
                        @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="gia" class="form-label fw-semibold">💰 Giá Combo</label>
                        <input type="number" name="gia" id="gia"
                            class="form-control form-control-lg @error('gia') is-invalid @enderror"
                            value="{{ old('gia', $combo->gia) }}">
                        @error('gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="mo_ta" class="form-label fw-semibold">📝 Mô tả</label>
                        <textarea name="mo_ta" id="mo_ta"
                            class="form-control form-control-lg @error('mo_ta') is-invalid @enderror"
                            rows="3">{{ old('mo_ta', $combo->mo_ta) }}</textarea>
                        @error('mo_ta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tính toán số lượng tối đa --}}
                    @php
                        $maxCombo = PHP_INT_MAX;
                        foreach ($combo->chiTiet as $ct) {
                            $sp = $sanPhams->firstWhere('id', $ct->san_pham_id);
                            if ($sp) {
                                $totalAvailable = $sp->so_luong + ($ct->so_luong * $combo->so_luong);
                                $maxCombo = min($maxCombo, intdiv($totalAvailable, $ct->so_luong));
                            }
                        }
                    @endphp

                    <div class="col-md-6">
                        <label for="so_luong" class="form-label fw-semibold">🔢 Số lượng Combo</label>
                        <input type="number" name="so_luong" id="so_luong"
                            class="form-control form-control-lg @error('so_luong') is-invalid @enderror"
                            value="{{ old('so_luong', $combo->so_luong) }}" max="{{ $maxCombo }}">
                        <small class="text-muted">Tối đa: {{ $maxCombo }} combo dựa trên tồn kho hiện tại</small>
                        @error('so_luong')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Chi tiết Combo --}}
                <div id="combo-chi-tiet">
                    <h4 class="fw-bold mb-3 text-secondary">🧾 Chi tiết Combo</h4>
                    @php
                        $chiTietOld = old('chi_tiet', $combo->chiTiet->toArray());
                    @endphp
                    @foreach ($chiTietOld as $index => $chiTiet)
                        <div class="combo-item border rounded-4 p-3 mb-3 bg-light d-flex align-items-center flex-wrap">
                            <div class="flex-grow-1 me-3">
                                <label for="san_pham_id_{{ $index }}" class="form-label">🎯 Sản phẩm</label>
                                <select name="chi_tiet[{{ $index }}][san_pham_id]"
                                    id="san_pham_id_{{ $index }}"
                                    class="form-select form-select-lg @error("chi_tiet.$index.san_pham_id") is-invalid @enderror">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($sanPhams as $sanPham)
                                        <option value="{{ $sanPham->id }}"
                                            {{ old("chi_tiet.$index.san_pham_id", $chiTiet['san_pham_id']) == $sanPham->id ? 'selected' : '' }}>
                                            {{ $sanPham->ten }} (Còn: {{ $sanPham->so_luong + ($chiTiet['so_luong'] * $combo->so_luong) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error("chi_tiet.$index.san_pham_id")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="flex-grow-1 me-3">
                                <label for="so_luong_{{ $index }}" class="form-label">📦 Số lượng</label>
                                <input type="number" name="chi_tiet[{{ $index }}][so_luong]"
                                    id="so_luong_{{ $index }}"
                                    class="form-control form-control-lg @error("chi_tiet.$index.so_luong") is-invalid @enderror"
                                    value="{{ old("chi_tiet.$index.so_luong", $chiTiet['so_luong']) }}">
                                @error("chi_tiet.$index.so_luong")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-outline-danger remove-combo-item mt-4">🗑 Xóa</button>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-combo-item" class="btn btn-primary mb-4 rounded-pill">
                    ➕ Thêm sản phẩm
                </button>

                {{-- Thông báo lỗi tổng --}}
                @if ($errors->has('chi_tiet'))
                    <div class="alert alert-danger">
                        {{ $errors->first('chi_tiet') }}
                    </div>
                @endif

                <div class="text-end">
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-4">
                        💾 Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let comboIndex = {{ count(old('chi_tiet', $combo->chiTiet->toArray())) }};

    document.getElementById('add-combo-item').addEventListener('click', function () {
        const comboChiTiet = document.getElementById('combo-chi-tiet');
        const newItem = `
            <div class="combo-item border rounded-4 p-3 mb-3 bg-light d-flex align-items-center flex-wrap">
                <div class="flex-grow-1 me-3">
                    <label for="san_pham_id_${comboIndex}" class="form-label">🎯 Sản phẩm</label>
                    <select name="chi_tiet[${comboIndex}][san_pham_id]" id="san_pham_id_${comboIndex}" class="form-select form-select-lg">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($sanPhams as $sanPham)
                            <option value="{{ $sanPham->id }}">{{ $sanPham->ten }} (Còn: {{ $sanPham->so_luong }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1 me-3">
                    <label for="so_luong_${comboIndex}" class="form-label">📦 Số lượng</label>
                    <input type="number" name="chi_tiet[${comboIndex}][so_luong]" id="so_luong_${comboIndex}" class="form-control form-control-lg" value="1">
                </div>
                <button type="button" class="btn btn-outline-danger remove-combo-item mt-4">🗑 Xóa</button>
            </div>`;
        comboChiTiet.insertAdjacentHTML('beforeend', newItem);
        comboIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-combo-item')) {
            e.target.closest('.combo-item')?.remove();
        }
    });
</script>
@endsection
