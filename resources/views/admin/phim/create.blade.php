@extends('admin.layouts.admin')

@section('title', 'Thêm phim mới')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">🎬 Thêm phim mới</h2>

    <form action="{{ route('admin.phim.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Tiêu đề phim --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề phim</label>
            <input type="text" name="tieu_de" class="form-control" placeholder="Nhập tên phim..." required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea name="mo_ta" class="form-control" rows="4" placeholder="Nhập mô tả phim..."></textarea>
        </div>

        {{-- Trailer --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Trailer (link YouTube)</label>
            <input type="url" name="trailer" class="form-control" placeholder="https://youtube.com/...">
        </div>

        {{-- Phụ đề --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Phụ đề</label>
            <select name="phu_de" class="form-select" required>
                <option value="0">Không</option>
                <option value="1">Có</option>
            </select>
        </div>

        {{-- Thời lượng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Thời lượng (phút)</label>
            <input type="number" name="thoi_luong" class="form-control" placeholder="120" required>
        </div>

        {{-- Ngày công chiếu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày công chiếu</label>
            <input type="date" name="ngay_cong_chieu" class="form-control" required>
        </div>

        {{-- Giới hạn tuổi --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Giới hạn độ tuổi</label>
            <input type="text" name="do_tuoi_gioi_han" class="form-control" placeholder="VD: 13+, 18+, P">
        </div>

        {{-- Danh mục --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Danh mục</label>
            <select name="danh_muc_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach($danhMucs as $dm)
                    <option value="{{ $dm->id }}">{{ $dm->ten }}</option>
                @endforeach
            </select>
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

        {{-- Upload poster --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Poster phim</label>
            <input type="file" name="anh_poster" class="form-control" accept="image/*" onchange="previewImage(event)">
            <div class="mt-3">
                <img id="preview" src="#" alt="Xem trước poster" class="img-fluid rounded d-none" style="max-height: 250px;">
            </div>
        </div>

        {{-- Nút lưu --}}
        <button type="submit" class="btn btn-success px-4">💾 Lưu phim</button>
        <a href="{{ route('admin.phim.index') }}" class="btn btn-secondary px-4">⬅ Quay lại</a>
    </form>
</div>

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
