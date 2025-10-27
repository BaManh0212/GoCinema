@extends('admin.layouts.admin')

@section('title', 'Thêm phim mới')

@section('content')
<div class="container-fluid px-4">
    {{-- ===== TIÊU ĐỀ VÀ NÚT QUAY LẠI ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">🎬 Thêm phim mới</h2>
        <a href="{{ route('admin.phim.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
            ⬅ Quay lại danh sách
        </a>
    </div>

    {{-- ===== THÔNG BÁO ===== --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- ===== FORM NHẬP PHIM ===== --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.phim.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- TIÊU ĐỀ + MÔ TẢ --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">🎞️ Tiêu đề phim</label>
                    <input type="text" name="tieu_de" value="{{ old('tieu_de') }}"
                        class="form-control form-control-lg @error('tieu_de') is-invalid @enderror"
                        placeholder="Nhập tên phim...">
                    @error('tieu_de') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">📝 Mô tả</label>
                    <textarea name="mo_ta" rows="4"
                        class="form-control form-control-lg @error('mo_ta') is-invalid @enderror"
                        placeholder="Nhập mô tả phim...">{{ old('mo_ta') }}</textarea>
                    @error('mo_ta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- THÔNG TIN CHI TIẾT --}}
                <div class="row g-4">
                    {{-- CỘT TRÁI --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🎬 Đạo diễn</label>
                            <input type="text" name="dao_dien" value="{{ old('dao_dien') }}"
                                class="form-control form-control-lg @error('dao_dien') is-invalid @enderror"
                                placeholder="Nhập tên đạo diễn...">
                            @error('dao_dien') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">⭐ Diễn viên</label>
                            <input type="text" name="dien_vien" value="{{ old('dien_vien') }}"
                                class="form-control form-control-lg @error('dien_vien') is-invalid @enderror"
                                placeholder="Nhập tên các diễn viên...">
                            @error('dien_vien') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">⏱ Thời lượng (phút)</label>
                            <input type="number" name="thoi_luong" value="{{ old('thoi_luong') }}"
                                class="form-control form-control-lg @error('thoi_luong') is-invalid @enderror"
                                placeholder="120">
                            @error('thoi_luong') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">📅 Ngày công chiếu</label>
                            <input type="date" name="ngay_cong_chieu" value="{{ old('ngay_cong_chieu') }}"
                                class="form-control form-control-lg @error('ngay_cong_chieu') is-invalid @enderror">
                            @error('ngay_cong_chieu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- CỘT PHẢI --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🎥 Trailer (YouTube)</label>
                            <input type="url" name="trailer" value="{{ old('trailer') }}"
                                class="form-control form-control-lg @error('trailer') is-invalid @enderror"
                                placeholder="https://youtube.com/...">
                            @error('trailer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">💬 Phụ đề</label>
                            <select name="phu_de" class="form-select form-select-lg @error('phu_de') is-invalid @enderror">
                                <option value="0" {{ old('phu_de') == '0' ? 'selected' : '' }}>Không</option>
                                <option value="1" {{ old('phu_de') == '1' ? 'selected' : '' }}>Có</option>
                            </select>
                            @error('phu_de') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">🌐 Ngôn ngữ</label>
                            <select name="ngon_ngu_id" class="form-select form-select-lg @error('ngon_ngu_id') is-invalid @enderror">
                                <option value="">-- Chọn ngôn ngữ --</option>
                                @foreach($ngonNgus as $nn)
                                    <option value="{{ $nn->id }}" {{ old('ngon_ngu_id') == $nn->id ? 'selected' : '' }}>
                                        {{ $nn->ten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ngon_ngu_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">🔞 Giới hạn độ tuổi</label>
                            <input type="text" name="do_tuoi_gioi_han" value="{{ old('do_tuoi_gioi_han') }}"
                                class="form-control form-control-lg @error('do_tuoi_gioi_han') is-invalid @enderror"
                                placeholder="VD: 13+, 18+, P">
                            @error('do_tuoi_gioi_han') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">🗓️ Ngày kết thúc chiếu</label>
                            <input type="date" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}"
                                   class="form-control form-control-lg @error('ngay_ket_thuc') is-invalid @enderror">
                            @error('ngay_ket_thuc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">🎛️ Trạng thái</label>
                            <select name="trang_thai" class="form-select form-select-lg @error('trang_thai') is-invalid @enderror">
                                <option value="1" {{ old('trang_thai') == '1' ? 'selected' : '' }}>Đang chiếu</option>
                                <option value="2" {{ old('trang_thai') == '2' ? 'selected' : '' }}>Sắp chiếu</option>
                                <option value="0" {{ old('trang_thai') == '0' ? 'selected' : '' }}>Ngừng chiếu</option>
                            </select>
                            @error('trang_thai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">📐 Định dạng</label>
                            <input type="text" name="dinh_dang" value="{{ old('dinh_dang', '2D') }}" class="form-control form-control-lg @error('dinh_dang') is-invalid @enderror" placeholder="2D, 3D...">
                            @error('dinh_dang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- DANH MỤC --}}
                <div class="mb-4 mt-3">
                    <label class="form-label fw-semibold">📂 Danh mục phim</label>
                    <select name="danh_muc_ids[]" multiple
                        class="form-select form-select-lg @error('danh_muc_ids') is-invalid @enderror">
                        @foreach($danhMucs as $dm)
                            <option value="{{ $dm->id }}" {{ collect(old('danh_muc_ids'))->contains($dm->id) ? 'selected' : '' }}>
                                {{ $dm->ten }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Giữ Ctrl (hoặc Cmd) để chọn nhiều danh mục</small>
                    @error('danh_muc_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- POSTER --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">🖼 Poster phim</label>
                    <input type="file" name="anh_poster"
                        class="form-control form-control-lg @error('anh_poster') is-invalid @enderror"
                        accept="image/*" onchange="previewImage(event)">
                    @error('anh_poster') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    <div class="mt-3 text-center">
                        <img id="preview" src="#" alt="Xem trước poster"
                             class="img-fluid rounded shadow-sm d-none" style="max-height: 250px;">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold"> Banner ngang</label>
                    <input type="file" name="banner" class="form-control form-control-lg @error('banner') is-invalid @enderror" accept="image/*">
                    @error('banner') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- NÚT LƯU --}}
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg rounded-pill shadow px-5">
                        💾 Lưu phim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT XEM TRƯỚC ẢNH --}}
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('preview');
        output.src = reader.result;
        output.classList.remove('d-none');
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
