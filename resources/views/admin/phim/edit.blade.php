@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa phim')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">✏️ Chỉnh sửa phim</h2>

    <form action="{{ route('admin.phim.update', $phim->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tiêu đề phim --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề phim</label>
            <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de', $phim->tieu_de) }}" required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea name="mo_ta" class="form-control" rows="4">{{ old('mo_ta', $phim->mo_ta) }}</textarea>
        </div>

        {{-- Trailer --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Trailer (link YouTube)</label>
            <input type="url" name="trailer" class="form-control" value="{{ old('trailer', $phim->trailer) }}">
        </div>

        {{-- Phụ đề --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Phụ đề</label>
            <select name="phu_de" class="form-select" required>
                <option value="0" {{ $phim->phu_de == 0 ? 'selected' : '' }}>Không</option>
                <option value="1" {{ $phim->phu_de == 1 ? 'selected' : '' }}>Có</option>
            </select>
        </div>

        {{-- Thời lượng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Thời lượng (phút)</label>
            <input type="number" name="thoi_luong" class="form-control" value="{{ old('thoi_luong', $phim->thoi_luong) }}" required>
        </div>

        {{-- Ngày công chiếu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày công chiếu</label>
            <input type="date" name="ngay_cong_chieu" class="form-control" value="{{ old('ngay_cong_chieu', $phim->ngay_cong_chieu) }}" required>
        </div>

        {{-- Giới hạn tuổi --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Giới hạn độ tuổi</label>
            <input type="text" name="do_tuoi_gioi_han" class="form-control" value="{{ old('do_tuoi_gioi_han', $phim->do_tuoi_gioi_han) }}">
        </div>

        {{-- Danh mục --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Danh mục</label>
            <select name="danh_muc_id" class="form-select" required>
                @foreach($danhMucs as $dm)
                    <option value="{{ $dm->id }}" {{ $phim->danh_muc_id == $dm->id ? 'selected' : '' }}>
                        {{ $dm->ten }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Ngôn ngữ --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngôn ngữ</label>
            <select name="ngon_ngu_id" class="form-select" required>
                @foreach($ngonNgus as $nn)
                    <option value="{{ $nn->id }}" {{ $phim->ngon_ngu_id == $nn->id ? 'selected' : '' }}>
                        {{ $nn->ten }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Upload poster --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Poster phim</label>
            <input type="file" name="anh_poster" class="form-control" accept="image/*" onchange="previewImage(event)">
            <div class="mt-3">
                <img id="preview" src="{{ asset('storage/' . $phim->anh_poster) }}" alt="Poster hiện tại" class="img-fluid rounded" style="max-height: 250px;">
            </div>
        </div>

        {{-- Thông tin ngày tháng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày tạo:</label>
            <input type="text" class="form-control" value="{{ $phim->ngay_tao ?? $phim->created_at }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày cập nhật:</label>
            <input type="text" class="form-control" value="{{ $phim->ngay_cap_nhat ?? $phim->updated_at }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày xóa (nếu có):</label>
            <input type="text" class="form-control" value="{{ $phim->ngay_xoa ?? $phim->deleted_at }}" disabled>
        </div>

        {{-- Nút lưu --}}
        <button type="submit" class="btn btn-primary px-4">💾 Cập nhật</button>
        <a href="{{ route('admin.phim.index') }}" class="btn btn-secondary px-4">⬅ Quay lại</a>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
