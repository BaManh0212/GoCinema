@extends('admin.layouts.admin')

@section('title', 'Quản lý Phim')

@section('content')
<div class="container py-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-box-seam"></i> Danh sách Phim
            </h2>
            <small class="text-muted">Xem, quản lý và lọc các Phim hiện có</small>
        </div>
        <div>
            <a href="{{ route('admin.phim.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm Phim Mới
            </a>
            <a href="{{ route('admin.phim.trashed') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                <i class="bi bi-trash"></i> Thùng rác
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

    {{-- 🎥 Danh sách phim --}}

    @forelse($phims as $phim)
        <div class="movie-card shadow-sm p-3 mb-4 rounded-4 d-flex align-items-center justify-content-between bg-white">

            {{-- Poster --}}
            <div class="movie-poster flex-shrink-0 me-3">
                @if($phim->anh_poster)
                    <img src="{{ asset('storage/' . $phim->anh_poster) }}" alt="Poster" class="poster-img rounded-3">
                @else
                    <div class="poster-placeholder">No Image</div>
                @endif
            </div>

            {{-- Nội dung --}}
            <div class="movie-details flex-grow-1">
                <div class="d-flex align-items-center mb-1">
                    <h5 class="fw-bold text-primary mb-0">{{ strtoupper($phim->tieu_de) }}</h5>
                    <span class="badge ms-2 {{ $phim->ngay_cong_chieu > now() ? 'bg-info text-dark' : 'bg-success' }}">
                        {{ $phim->ngay_cong_chieu > now() ? 'Sắp chiếu' : 'Đang chiếu' }}
                    </span>
                </div>

                <ul class="list-unstyled small text-secondary mb-2">
                    <li>⏱️ <strong>Thời lượng:</strong> {{ $phim->thoi_luong }} phút</li>
                    <li>📅 <strong>Công chiếu:</strong> {{ \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') }}</li>
                    <li>� <strong>Ngày kết thúc:</strong> {{ optional($phim->ngay_ket_thuc) ? \Carbon\Carbon::parse($phim->ngay_ket_thuc)->format('d/m/Y') : '—' }}</li>
                    <li>�🗣️ <strong>Ngôn ngữ:</strong> {{ $phim->ngonNgu->ten ?? '—' }}</li>
                    <li>🎬 <strong>Đạo diễn:</strong> {{ $phim->dao_dien ?? '—' }}</li>
                    <li>👥 <strong>Diễn viên:</strong> {{ $phim->dien_vien ?? '—' }}</li>
                    <li>📁 <strong>Danh mục:</strong>
                        @forelse($phim->danhMucs as $dm)
                            <span class="badge bg-light text-dark border">{{ $dm->ten }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </li>
                    <li>🔞 <strong>Độ tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?? 'P' }}</li>
                </ul>
            </div>

            {{-- Nút hành động --}}
            <div class="movie-actions text-end d-flex flex-column gap-2 ms-3">
                <a href="{{ route('admin.phim.show', $phim->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Chi tiết</a>
                <a href="{{ route('admin.phim.edit', $phim->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Sửa</a>
                <form action="{{ route('admin.phim.destroy', $phim->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa phim này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Xóa</button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center text-muted mt-5">Không có phim nào trong hệ thống 📭</div>
    @endforelse

</div>

<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.movie-card {
    border: 1px solid #eef1f5;
    transition: all 0.25s ease;
    background-color: #fff;
}
.movie-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

/* ✅ Poster hiển thị đầy đủ, đúng tỉ lệ */
.movie-poster {
    width: 200px;                /* to hơn cho rõ nét */
    height: 300px;               /* tỉ lệ chuẩn poster 2:3 */
    overflow: hidden;
    border-radius: 16px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.movie-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;         /* ✅ ảnh tràn full khung */
    object-position: top;      /* hoặc center, tuỳ ảnh */
    border-radius: 16px;
    transition: transform 0.3s ease;
}


.movie-poster img:hover {
    transform: scale(1.05);
}

/* Nếu không có ảnh */
.poster-placeholder {
    width: 200px;
    height: 300px;
    background: #f0f0f0;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    font-weight: 600;
}

/* Nội dung phim */
.movie-details {
    flex: 1;
    padding: 0 15px;
}

/* Badge & nút */
.badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.7rem;
    border-radius: 1rem;
}
.movie-actions .btn {
    font-size: 0.85rem;
    border-radius: 12px;
    transition: all 0.2s ease;
}
.movie-actions .btn:hover {
    transform: scale(1.05);
}
</style>

@endsection
