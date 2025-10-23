@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">✏️ Chỉnh sửa danh mục</h3>

    <form action="{{ route('admin.danhmuc.update', $danhmuc->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tên danh mục</label>
            <input 
                type="text" 
                name="ten" 
                id="ten" 
                value="{{ old('ten', $danhmuc->ten) }}" 
                class="form-control" 
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Slug (tự động)</label>
            <input 
                type="text" 
                name="slug" 
                id="slug" 
                value="{{ old('slug', $danhmuc->slug) }}" 
                class="form-control" 
                readonly
            >
        </div>

        <button class="btn btn-success">💾 Cập nhật</button>
        <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
    </form>
</div>

{{-- Tự động cập nhật slug khi sửa tên --}}
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
@endsection
