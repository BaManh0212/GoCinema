@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-folder-plus"></i> Thêm Danh mục mới
            </h2>
            <small class="text-muted">Tạo danh mục phim mới trong hệ thống</small>
        </div>
        <div>
            <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    {{-- ✅ Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 📝 Form thêm danh mục --}}
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <form action="{{ route('admin.danhmuc.store') }}" method="POST">
            @csrf

            {{-- Tên danh mục --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-tag"></i> Tên danh mục
                </label>
                <input 
                    type="text" 
                    name="ten" 
                    class="form-control form-control-lg @error('ten') is-invalid @enderror"
                    value="{{ old('ten') }}"
                    placeholder="Nhập tên danh mục..."
                >
                @error('ten')
                    <div class="invalid-feedback mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Mô tả --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-textarea-t"></i> Mô tả
                </label>
                <textarea 
                    name="mo_ta" 
                    rows="4" 
                    class="form-control form-control-lg @error('mo_ta') is-invalid @enderror"
                    placeholder="Nhập mô tả ngắn cho danh mục (tùy chọn)...">{{ old('mo_ta') }}</textarea>
                @error('mo_ta')
                    <div class="invalid-feedback mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Nút hành động --}}
            <div class="d-flex justify-content-start gap-3 mt-4">
                <button type="submit" class="btn btn-success shadow-sm rounded-pill px-4">
                    <i class="bi bi-save"></i> Lưu danh mục
                </button>
                <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
            </div>
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

.card {
    border-radius: 1rem;
    background-color: #fff;
}

.form-control-lg {
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    border: 1px solid #ced4da;
    transition: all 0.2s ease;
}
.form-control-lg:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.25rem rgba(0,123,255,0.1);
}

.btn {
    transition: all 0.2s ease;
}
.btn:hover {
    transform: scale(1.05);
}
</style>
@endsection
