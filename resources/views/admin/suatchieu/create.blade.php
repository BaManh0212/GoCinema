@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary mb-4">
        <i class="bi bi-plus-circle"></i> 🎬 Thêm mới suất chiếu
    </h2>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <form action="{{ route('admin.suatchieu.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="phim_id" class="form-label fw-bold">Phim</label>
                    <select name="phim_id" id="phim_id" class="form-select @error('phim_id') is-invalid @enderror">
                        <option value="">-- Chọn phim --</option>
                        @foreach ($phims as $phim)
                            <option value="{{ $phim->id }}" {{ old('phim_id') == $phim->id ? 'selected' : '' }}>
                                {{ $phim->tieu_de }}
                            </option>
                        @endforeach
                    </select>
                    @error('phim_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="phong_id" class="form-label fw-bold">Phòng chiếu</label>
                    <select name="phong_id" id="phong_id" class="form-select @error('phong_id') is-invalid @enderror">
                        <option value="">-- Chọn phòng --</option>
                        @foreach ($phongs as $phong)
                            <option value="{{ $phong->id }}" {{ old('phong_id') == $phong->id ? 'selected' : '' }}>
                                {{ $phong->ten }}
                            </option>
                        @endforeach
                    </select>
                    @error('phong_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gio_bat_dau" class="form-label fw-bold">Giờ bắt đầu</label>
                        <input type="datetime-local" name="gio_bat_dau" id="gio_bat_dau"
                            value="{{ old('gio_bat_dau') }}" class="form-control @error('gio_bat_dau') is-invalid @enderror">
                        @error('gio_bat_dau') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gio_ket_thuc" class="form-label fw-bold">Giờ kết thúc</label>
                        <input type="datetime-local" name="gio_ket_thuc" id="gio_ket_thuc"
                            value="{{ old('gio_ket_thuc') }}" class="form-control @error('gio_ket_thuc') is-invalid @enderror">
                        @error('gio_ket_thuc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="gia_ve" class="form-label fw-bold">Giá vé (VNĐ)</label>
                    <input type="number" name="gia_ve" id="gia_ve" min="0" step="1000"
                        value="{{ old('gia_ve') }}" class="form-control @error('gia_ve') is-invalid @enderror">
                    @error('gia_ve') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu suất chiếu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
