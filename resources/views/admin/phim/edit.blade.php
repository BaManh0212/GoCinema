@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa phim')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center text-primary">✏️ Chỉnh sửa phim</h2>
    
    {{-- ✅ Hiển thị thông báo thành công (sau khi update) --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('admin.phim.update', $phim->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tiêu đề phim --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề phim</label>
            <input type="text" name="tieu_de" class="form-control @error('tieu_de') is-invalid @enderror"
                   value="{{ old('tieu_de', $phim->tieu_de) }}" placeholder="Nhập tên phim...">
            @error('tieu_de')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror" rows="4"
                      placeholder="Nhập mô tả phim...">{{ old('mo_ta', $phim->mo_ta) }}</textarea>
            @error('mo_ta')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Đạo diễn --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Đạo diễn</label>
            <input type="text" name="dao_dien" class="form-control @error('dao_dien') is-invalid @enderror"
                   value="{{ old('dao_dien', $phim->dao_dien) }}" placeholder="Nhập tên đạo diễn...">
            @error('dao_dien')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Diễn viên --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Diễn viên</label>
            <input type="text" name="dien_vien" class="form-control @error('dien_vien') is-invalid @enderror"
                   value="{{ old('dien_vien', $phim->dien_vien) }}" placeholder="Nhập tên các diễn viên, cách nhau bằng dấu phẩy...">
            @error('dien_vien')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Trailer --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Trailer (link YouTube)</label>
            <input type="url" name="trailer" class="form-control @error('trailer') is-invalid @enderror"
                   value="{{ old('trailer', $phim->trailer) }}" placeholder="https://youtube.com/...">
            @error('trailer')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Phụ đề --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Phụ đề</label>
            <select name="phu_de" class="form-select @error('phu_de') is-invalid @enderror">
                <option value="0" {{ old('phu_de', $phim->phu_de) == 0 ? 'selected' : '' }}>Không</option>
                <option value="1" {{ old('phu_de', $phim->phu_de) == 1 ? 'selected' : '' }}>Có</option>
            </select>
            @error('phu_de')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Thời lượng --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Thời lượng (phút)</label>
            <input type="number" name="thoi_luong" class="form-control @error('thoi_luong') is-invalid @enderror"
                   value="{{ old('thoi_luong', $phim->thoi_luong) }}" placeholder="120">
            @error('thoi_luong')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Ngày công chiếu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày công chiếu</label>
            <input type="date" name="ngay_cong_chieu" class="form-control @error('ngay_cong_chieu') is-invalid @enderror"
                   value="{{ old('ngay_cong_chieu', $phim->ngay_cong_chieu) }}">
            @error('ngay_cong_chieu')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Giới hạn tuổi --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Giới hạn độ tuổi</label>
            <input type="text" name="do_tuoi_gioi_han" class="form-control @error('do_tuoi_gioi_han') is-invalid @enderror"
                   value="{{ old('do_tuoi_gioi_han', $phim->do_tuoi_gioi_han) }}" placeholder="VD: 13+, 18+, P">
            @error('do_tuoi_gioi_han')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Danh mục --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Danh mục</label>
            <select name="danh_muc_id" class="form-select @error('danh_muc_id') is-invalid @enderror">
                <option value="">-- Chọn danh mục --</option>
                @foreach($danhMucs as $dm)
                    <option value="{{ $dm->id }}" {{ old('danh_muc_id', $phim->danh_muc_id) == $dm->id ? 'selected' : '' }}>
                        {{ $dm->ten }}
                    </option>
                @endforeach
            </select>
            @error('danh_muc_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Ngôn ngữ --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngôn ngữ</label>
            <select name="ngon_ngu_id" class="form-select @error('ngon_ngu_id') is-invalid @enderror">
                <option value="">-- Chọn ngôn ngữ --</option>
                @foreach($ngonNgus as $nn)
                    <option value="{{ $nn->id }}" {{ old('ngon_ngu_id', $phim->ngon_ngu_id) == $nn->id ? 'selected' : '' }}>
                        {{ $nn->ten }}
                    </option>
                @endforeach
            </select>
            @error('ngon_ngu_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Upload poster --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Poster phim</label>
            <input type="file" name="anh_poster" class="form-control @error('anh_poster') is-invalid @enderror"
                   accept="image/*" onchange="previewImage(event)">
            @error('anh_poster')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="mt-3">
                <img id="preview"
                     src="{{ $phim->anh_poster ? asset('storage/' . $phim->anh_poster) : '#' }}"
                     alt="Poster phim"
                     class="img-fluid rounded {{ $phim->anh_poster ? '' : 'd-none' }}"
                     style="max-height: 250px;">
            </div>
        </div>

        {{-- Thông tin ngày tháng --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Ngày tạo:</label>
                <input type="text" class="form-control" value="{{ $phim->created_at }}" disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Ngày cập nhật:</label>
                <input type="text" class="form-control" value="{{ $phim->updated_at }}" disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Ngày xóa (nếu có):</label>
                <input type="text" class="form-control" value="{{ $phim->deleted_at ?? '—' }}" disabled>
            </div>
        </div>

        {{-- Nút lưu --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">💾 Cập nhật</button>
            <a href="{{ route('admin.phim.index') }}" class="btn btn-secondary px-4">⬅ Quay lại</a>
        </div>
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
