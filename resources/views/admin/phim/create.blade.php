@extends('admin.layouts.admin')

@section('title', 'Thêm phim mới')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">🎬 Thêm phim mới</h2>

    <form action="{{ route('admin.phim.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Tên phim --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tên phim</label>
            <input type="text" name="tieu_de" class="form-control" placeholder="Nhập tên phim..." required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea name="mo_ta" class="form-control" rows="4" placeholder="Nhập mô tả phim..."></textarea>
        </div>

        {{-- Thời lượng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Thời lượng (phút)</label>
            <input type="number" name="thoi_luong" class="form-control" placeholder="120" required>
        </div>

        {{-- Ngày khởi chiếu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày khởi chiếu</label>
            <input type="date" name="ngay_khoi_chieu" class="form-control" required>
        </div>

        {{-- Chọn danh mục --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Danh mục</label>
            <select name="danh_muc_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($danhMucs as $dm)
                    <option value="{{ $dm->id }}">{{ $dm->ten }}</option>
                @endforeach
            </select>
        </div>

        {{-- Chọn thể loại --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Thể loại</label>
            <select name="the_loai_id[]" class="form-select" multiple>
                @foreach($theLoais as $tl)
                    <option value="{{ $tl->id }}">{{ $tl->ten }}</option>
                @endforeach
            </select>
            <small class="text-muted">Giữ Ctrl để chọn nhiều thể loại</small>
        </div>

        {{-- Ngôn ngữ --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngôn ngữ</label>
            <select name="ngon_ngu_id" class="form-select" required>
                <option value="">-- Chọn ngôn ngữ --</option>
                @foreach($ngonNgus as $nn)
                    <option value="{{ $nn->id }}">{{ $nn->ten }}</option>
                @endforeach
            </select>
        </div>

        {{-- Định dạng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Định dạng</label>
            <select name="dinh_dang_id" class="form-select" required>
                <option value="">-- Chọn định dạng --</option>
                @foreach($dinhDangs as $dd)
                    <option value="{{ $dd->id }}">{{ $dd->ten }}</option>
                @endforeach
            </select>
        </div>

        {{-- Upload ảnh poster --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Poster phim</label>
            <input type="file" name="poster" class="form-control" accept="image/*" onchange="previewImage(event)">
            <div class="mt-3">
                <img id="preview" src="#" alt="Xem trước ảnh" class="img-fluid rounded d-none" style="max-height: 250px;">
            </div>
        </div>

        {{-- Nút lưu --}}
        <button type="submit" class="btn btn-success px-4">💾 Lưu phim</button>
        <a href="{{ route('admin.phim.index') }}" class="btn btn-secondary px-4">⬅ Quay lại</a>
    </form>
</div>

{{-- Preview ảnh --}}
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview');
        output.src = reader.result;
        output.classList.remove('d-none');
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
