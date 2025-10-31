@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary mb-4">
        <i class="bi bi-plus-circle"></i> 🎬 Tạo lịch suất chiếu tự động
    </h2>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <form action="{{ route('admin.suatchieu.autoStore') }}" method="POST">
                @csrf

                {{-- 🎞 Chọn phim --}}
                <div class="mb-3">
                    <label for="phim_id" class="form-label fw-bold">Phim</label>
                    <select name="phim_id" id="phim_id" class="form-select @error('phim_id') is-invalid @enderror">
                        <option value="">-- Chọn phim --</option>
                        @foreach ($phims as $phim)
                            <option value="{{ $phim->id }}" {{ old('phim_id') == $phim->id ? 'selected' : '' }}>
                                {{ $phim->tieu_de }} ({{ $phim->thoi_luong }} phút)
                            </option>
                        @endforeach
                    </select>
                    @error('phim_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- 🏢 Chọn phòng chiếu --}}
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

                {{-- 📅 Ngày bắt đầu - kết thúc --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ngày kết thúc</label>
                        <input type="date" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}" class="form-control">
                    </div>
                </div>

                {{-- 🕒 Giờ bắt đầu đầu tiên trong ngày --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Giờ chiếu đầu tiên trong ngày</label>
                    <input type="time" name="gio_bat_dau_ngay" value="{{ old('gio_bat_dau_ngay', '08:00') }}" class="form-control">
                </div>

                {{-- 💰 Giá vé --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Giá vé (VNĐ)</label>
                    <input type="number" name="gia_ve" min="0" step="1000"
                        value="{{ old('gia_ve', 70000) }}" class="form-control">
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Tạo tự động
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
