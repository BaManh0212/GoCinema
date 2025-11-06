@extends('admin.layouts.admin')

@section('title')
    {{ isset($baiviet) ? 'Chỉnh sửa bài viết' : 'Thêm bài viết' }}
@endsection

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-gradient">
            <i class="bi bi-newspaper"></i>
            {{ isset($baiviet) ? 'Chỉnh sửa bài viết' : 'Thêm bài viết' }}
        </h2>
        <a href="{{ route('admin.baiviet.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
    </div>

    {{-- 📝 Form --}}
    <div class="card shadow-sm rounded-4 p-4">
        <form 
            action="{{ isset($baiviet) ? route('admin.baiviet.update', $baiviet->id) : route('admin.baiviet.store') }}" 
            method="POST" 
            enctype="multipart/form-data"
        >
            @csrf
            @if(isset($baiviet))
                @method('PUT')
            @endif

            {{-- Tiêu đề --}}
            <div class="mb-3">
                <label for="tieu_de" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" 
                       name="tieu_de" 
                       id="tieu_de" 
                       class="form-control @error('tieu_de') is-invalid @enderror"
                       value="{{ old('tieu_de', $baiviet->tieu_de ?? '') }}"
                       placeholder="Nhập tiêu đề bài viết">
                @error('tieu_de')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tóm tắt --}}
            <div class="mb-3">
                <label for="tom_tat" class="form-label">Tóm tắt</label>
                <textarea name="tom_tat" 
                          id="tom_tat" 
                          class="form-control @error('tom_tat') is-invalid @enderror" 
                          rows="3" 
                          placeholder="Nhập tóm tắt">{{ old('tom_tat', $baiviet->tom_tat ?? '') }}</textarea>
                @error('tom_tat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nội dung --}}
            <div class="mb-3">
                <label for="noi_dung" class="form-label">Nội dung <span class="text-danger">*</span></label>
                <textarea name="noi_dung" 
                          id="noi_dung" 
                          class="form-control @error('noi_dung') is-invalid @enderror" 
                          rows="6"
                          placeholder="Nhập nội dung bài viết">{{ old('noi_dung', $baiviet->noi_dung ?? '') }}</textarea>
                @error('noi_dung')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Loại bài viết --}}
            <div class="mb-3">
                <label for="loai" class="form-label">Loại bài viết <span class="text-danger">*</span></label>
                <select name="loai" id="loai" class="form-select @error('loai') is-invalid @enderror">
                    <option value="">-- Chọn loại --</option>
                    <option value="tin-tuc" {{ old('loai', $baiviet->loai ?? '') == 'tin-tuc' ? 'selected' : '' }}>Tin tức</option>
                    <option value="khuyen-mai" {{ old('loai', $baiviet->loai ?? '') == 'khuyen-mai' ? 'selected' : '' }}>Khuyến mãi</option>
                </select>
                @error('loai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ngày phát hành --}}
            <div class="mb-3">
                <label for="ngay_phat_hanh" class="form-label">Ngày phát hành <span class="text-danger">*</span></label>
                <input type="date" 
                       name="ngay_phat_hanh" 
                       id="ngay_phat_hanh" 
                       class="form-control @error('ngay_phat_hanh') is-invalid @enderror"
                       value="{{ old('ngay_phat_hanh', isset($baiviet->ngay_phat_hanh) ? $baiviet->ngay_phat_hanh->format('Y-m-d') : '') }}">
                @error('ngay_phat_hanh')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ngày kết thúc --}}
            <div class="mb-3">
                <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc</label>
                <input type="date" 
                       name="ngay_ket_thuc" 
                       id="ngay_ket_thuc" 
                       class="form-control @error('ngay_ket_thuc') is-invalid @enderror"
                       value="{{ old('ngay_ket_thuc', isset($baiviet->ngay_ket_thuc) ? $baiviet->ngay_ket_thuc->format('Y-m-d') : '') }}">
                @error('ngay_ket_thuc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Hình ảnh --}}
            <div class="mb-3">
                <label for="hinh_anh" class="form-label">Hình ảnh</label>
                <input type="file" name="hinh_anh" id="hinh_anh" class="form-control @error('hinh_anh') is-invalid @enderror">
                @error('hinh_anh')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- Hiển thị hình cũ khi edit --}}
                @if(isset($baiviet) && $baiviet->hinh_anh)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $baiviet->hinh_anh) }}" alt="Hình bài viết" class="img-thumbnail" width="200">
                    </div>
                @endif
            </div>

            {{-- Trạng thái hiển thị --}}
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $baiviet->is_active ?? 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Hiển thị bài viết</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-success rounded-pill px-4">
                {{ isset($baiviet) ? 'Cập nhật' : 'Thêm bài viết' }}
            </button>
        </form>
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
@endsection
