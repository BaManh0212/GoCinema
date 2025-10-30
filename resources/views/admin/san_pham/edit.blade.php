@extends('admin.layouts.admin')

@section('content')
    <h1>Sửa sản phẩm</h1>

    {{-- ✅ Hiển thị thông báo thành công (sau khi update) --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.san_pham.update', $sanPham->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        {{-- ✅ Tên sản phẩm --}}
        <div class="form-group mb-3">
            <label for="ten">Tên sản phẩm</label>
            <input type="text" 
                   name="ten" 
                   id="ten" 
                   class="form-control @error('ten') is-invalid @enderror" 
                   value="{{ old('ten', $sanPham->ten) }}">
            @error('ten')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
                <div class="form-group mb-3">
            <label for="slug">Slug (tự động tạo)</label>
            <input type="text" 
                name="slug" 
                id="slug" 
                class="form-control bg-light" 
                value="{{ old('slug', $sanPham->slug) }}" 
                readonly>
        </div>

        {{-- ✅ Giá --}}
        <div class="form-group mb-3">
            <label for="gia">Giá</label>
            <input type="number" 
                   name="gia" 
                   id="gia" 
                   class="form-control @error('gia') is-invalid @enderror" 
                   value="{{ old('gia', $sanPham->gia) }}">
            @error('gia')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- ✅ Số lượng --}}
        <div class="form-group mb-3">
            <label for="so_luong">Số lượng</label>
            <input type="number" 
                   name="so_luong" 
                   id="so_luong" 
                   class="form-control @error('so_luong') is-invalid @enderror" 
                   value="{{ old('so_luong', $sanPham->so_luong) }}">
            @error('so_luong')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const tenInput = document.getElementById('ten');
    const slugInput = document.getElementById('slug');

    tenInput.addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // bỏ dấu
            .replace(/[^a-z0-9\s-]/g, '') // bỏ ký tự đặc biệt
            .trim()
            .replace(/\s+/g, '-');
        slugInput.value = slug;
    });
});
</script>
@endsection
