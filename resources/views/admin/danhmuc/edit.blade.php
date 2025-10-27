@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-pencil-square"></i> Chỉnh sửa Danh mục
            </h2>
            <small class="text-muted">Cập nhật thông tin danh mục phim</small>
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

    {{-- 📝 Form chỉnh sửa --}}
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <form action="{{ route('admin.danhmuc.update', $danhmuc->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-tag"></i> Tên danh mục
                </label>
                <input 
                    type="text" 
                    name="ten" 
                    id="ten"
                    value="{{ old('ten', $danhmuc->ten) }}" 
                    class="form-control form-control-lg @error('ten') is-invalid @enderror"
                    placeholder="Nhập tên danh mục..."
                    required
                >
                @error('ten')
                    <div class="invalid-feedback mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-link-45deg"></i> Slug (tự động)
                </label>
                <input 
                    type="text" 
                    name="slug" 
                    id="slug" 
                    value="{{ old('slug', $danhmuc->slug) }}" 
                    class="form-control form-control-lg bg-light"
                    readonly
                >
            </div>

            <div class="d-flex justify-content-start gap-3 mt-4">
                <button type="submit" class="btn btn-success shadow-sm rounded-pill px-4">
                    <i class="bi bi-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ✨ Script: tự động tạo slug --}}
<script>
document.getElementById('ten').addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // bỏ dấu tiếng Việt
        .replace(/[^a-z0-9\s-]/g, '') // bỏ ký tự đặc biệt
        .trim()
        .replace(/\s+/g, '-');
    document.getElementById('slug').value = slug;
});
</script>

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
