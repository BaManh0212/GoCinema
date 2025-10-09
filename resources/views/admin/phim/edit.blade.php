@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa phim')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">✏️ Chỉnh sửa phim</h2>

    <form action="{{ route('admin.phim.update', $phim->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tên phim --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tên phim</label>
            <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de', $phim->tieu_de) }}" required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea name="mo_ta" class="form-control" rows="4">{{ old('mo_ta', $phim->mo_ta) }}</textarea>
        </div>

        {{-- Thời lượng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Thời lượng (phút)</label>
            <input type="number" name="thoi_luong" class="form-control" value="{{ old('thoi_luong', $phim->thoi_luong) }}" required>
        </div>

        {{-- Ngày khởi chiếu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày khởi chiếu</label>
            <input type="date" name="ngay_khoi_chieu" class="form-control" value="{{ old('ngay_khoi_chieu', $phim->ngay_khoi_chieu) }}" required>
        </div>

        {{-- Chọn danh mục --}}
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

        {{-- Chọn thể loại --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Thể loại</label>
            <select name="the_loai_id[]" class="form-select" multiple>
                @foreach($theLoais as $tl)
                    <option value="{{ $tl->id }}" {{ in_array($tl->id, $phim->theLoais->pluck('id')->toArray()) ? 'selected' : '' }}>
                        {{ $tl->ten }}
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

        {{-- Định dạng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Định dạng</label>
            <select name="dinh_dang_id" class="form-select" required>
                @foreach($dinhDangs as $dd)
                    <option value="{{ $dd->id }}" {{ $phim->dinh_dang_id == $dd->id ? 'selected' : '' }}>
                        {{ $dd->ten }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Upload poster --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Poster phim</label>
            <input type="file" name="poster" class="form-control" accept="image/*" onchange="previewImage(event)">
            <div class="mt-3">
                <img id="preview" src="{{ asset('storage/' . $phim->poster) }}" alt="Poster hiện tại" class="img-fluid rounded" style="max-height: 250px;">
            </div>
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
