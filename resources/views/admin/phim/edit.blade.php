@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa phim')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center text-primary">✏️ Chỉnh sửa phim</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.phim.update', $phim->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tiêu đề phim --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề phim</label>
            <input type="text" name="tieu_de" class="form-control @error('tieu_de') is-invalid @enderror"
                   value="{{ old('tieu_de', $phim->tieu_de) }}" placeholder="Nhập tên phim...">
            @error('tieu_de') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Mô tả phim --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror" rows="4" placeholder="Nhập mô tả phim...">{{ old('mo_ta', $phim->mo_ta) }}</textarea>
            @error('mo_ta') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3">
            {{-- Cột trái --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Đạo diễn</label>
                    <input type="text" name="dao_dien" value="{{ old('dao_dien', $phim->dao_dien) }}" class="form-control @error('dao_dien') is-invalid @enderror" placeholder="Nhập tên đạo diễn...">
                    @error('dao_dien') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Diễn viên</label>
                    <input type="text" name="dien_vien" value="{{ old('dien_vien', $phim->dien_vien) }}" class="form-control @error('dien_vien') is-invalid @enderror" placeholder="Nhập tên diễn viên...">
                    @error('dien_vien') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Thời lượng (phút)</label>
                    <input type="number" name="thoi_luong" value="{{ old('thoi_luong', $phim->thoi_luong) }}" class="form-control @error('thoi_luong') is-invalid @enderror" placeholder="120">
                    @error('thoi_luong') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ngày công chiếu</label>
                    <input type="date" name="ngay_cong_chieu" value="{{ old('ngay_cong_chieu', $phim->ngay_cong_chieu) }}" class="form-control @error('ngay_cong_chieu') is-invalid @enderror">
                    @error('ngay_cong_chieu') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Cột phải --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Trailer (YouTube)</label>
                    <input type="url" name="trailer" value="{{ old('trailer', $phim->trailer) }}" class="form-control @error('trailer') is-invalid @enderror" placeholder="https://youtube.com/...">
                    @error('trailer') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Phụ đề</label>
                    <select name="phu_de" class="form-select @error('phu_de') is-invalid @enderror">
                        <option value="0" {{ old('phu_de', $phim->phu_de) == 0 ? 'selected' : '' }}>Không</option>
                        <option value="1" {{ old('phu_de', $phim->phu_de) == 1 ? 'selected' : '' }}>Có</option>
                    </select>
                    @error('phu_de') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

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
                    @error('ngon_ngu_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Giới hạn độ tuổi</label>
                    <input type="text" name="do_tuoi_gioi_han" value="{{ old('do_tuoi_gioi_han', $phim->do_tuoi_gioi_han) }}" class="form-control @error('do_tuoi_gioi_han') is-invalid @enderror" placeholder="VD: 13+, 18+, P">
                    @error('do_tuoi_gioi_han') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Danh mục --}}
        <div class="mb-3 mt-3">
            <label class="form-label fw-bold">Danh mục</label>
            <select name="danh_muc_ids[]" multiple class="form-select @error('danh_muc_ids') is-invalid @enderror">
                @php
                    $selectedDanhMucs = collect(old('danh_muc_ids', $phim->danhMucs->pluck('id') ?? []));
                @endphp
                @foreach($danhMucs as $dm)
                    <option value="{{ $dm->id }}" {{ $selectedDanhMucs->contains($dm->id) ? 'selected' : '' }}>
                        {{ $dm->ten }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Nhấn Ctrl để chọn nhiều danh mục</small>
            @error('danh_muc_ids') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Poster --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Poster phim</label>
            <input type="file" name="anh_poster" class="form-control @error('anh_poster') is-invalid @enderror"
                   accept="image/*" onchange="previewImage(event)">
            @error('anh_poster') <div class="text-danger mt-1">{{ $message }}</div> @enderror

            <div class="mt-3">
                <img id="preview"
                     src="{{ $phim->anh_poster ? asset('storage/' . $phim->anh_poster) : '#' }}"
                     alt="Poster phim"
                     class="img-fluid rounded shadow-sm {{ $phim->anh_poster ? '' : 'd-none' }}"
                     style="max-height: 250px; border: 1px solid #ddd; padding: 5px;">
            </div>
        </div>

        {{-- Nút hành động --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success px-4">💾 Cập nhật</button>
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
