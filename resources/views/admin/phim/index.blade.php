@extends('admin.layouts.admin')

@section('title', 'Quản lý Phim')

@section('content')
<div class="container py-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-film"></i> Danh sách Phim
            </h2>
            <small class="text-muted">Quản lý, chỉnh sửa và theo dõi thông tin phim</small>
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

    {{-- 🎯 Bộ lọc phim --}}
<div class="card border-0 shadow-sm mb-4 rounded-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.phim.index') }}">
            <div class="row g-3 align-items-center">

                {{-- 🔍 Tìm kiếm theo tên phim --}}
                <div class="col-lg-3 col-md-6">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control rounded-pill ps-4"
                        placeholder="🔍 Tìm theo tên phim...">
                </div>

                {{-- 📁 Lọc theo danh mục --}}
                <div class="col-lg-2 col-md-4">
                    <select name="danh_muc_id" class="form-select rounded-pill">
                        <option value="">📂 Danh mục</option>
                        @foreach($danhMucs as $dm)
                            <option value="{{ $dm->id }}" {{ request('danh_muc_id') == $dm->id ? 'selected' : '' }}>
                                {{ $dm->ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 🗣️ Lọc theo ngôn ngữ --}}
                <div class="col-lg-2 col-md-4">
                    <select name="ngon_ngu_id" class="form-select rounded-pill">
                        <option value="">🗣️ Ngôn ngữ</option>
                        @foreach($ngonNgus as $nn)
                            <option value="{{ $nn->id }}" {{ request('ngon_ngu_id') == $nn->id ? 'selected' : '' }}>
                                {{ $nn->ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 🎞️ Lọc theo trạng thái --}}
                <div class="col-lg-2 col-md-4">
                    <select name="trang_thai" class="form-select rounded-pill">
                        <option value="">🎞️ Trạng thái</option>
                        <option value="2" {{ request('trang_thai') == '2' ? 'selected' : '' }}>Sắp chiếu</option>
                        <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>Đang chiếu</option>
                        <option value="0" {{ request('trang_thai') == '0' ? 'selected' : '' }}>Ngưng chiếu</option>
                    </select>
                </div>

                {{-- 🔘 Nút thao tác --}}
                <div class="col-lg-3 col-md-12 text-end">
                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-funnel"></i> Lọc
                        </button>
                        <a href="{{ route('admin.phim.index') }}" class="btn btn-outline-danger rounded-pill px-4">
                            <i class="bi bi-x-circle"></i> Xóa
                        </a>
                    </div>
                </div>

            </div>
        </form>
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


    {{-- 🎬 Danh sách phim --}}
    @forelse($phims as $phim)
        @php
            $today = \Carbon\Carbon::now();
            $ngayBatDau = \Carbon\Carbon::parse($phim->ngay_cong_chieu);
            $ngayKetThuc = $phim->ngay_ket_thuc ? \Carbon\Carbon::parse($phim->ngay_ket_thuc) : null;

            if ($today->lt($ngayBatDau)) {
                $status = ['Sắp chiếu', 'bg-info text-dark'];
            } elseif ($ngayKetThuc && $today->gt($ngayKetThuc)) {
                $status = ['Ngưng chiếu', 'bg-secondary text-white'];
            } else {
                $status = ['Đang chiếu', 'bg-success text-white'];
            }
        @endphp

        <div class="movie-card shadow-sm p-3 mb-4 rounded-4 d-flex align-items-center bg-white justify-content-between">
            {{-- 🎞️ Poster --}}
            <div class="movie-poster me-3">
                @if($phim->anh_poster)
                    <img src="{{ asset('storage/' . $phim->anh_poster) }}" alt="Poster" class="poster-img rounded-3">
                @else
                    <div class="poster-placeholder">No Image</div>
                @endif
            </div>

            {{-- 📋 Thông tin --}}
            <div class="movie-details flex-grow-1">
                <div class="d-flex align-items-center mb-1">
                    <h5 class="fw-bold text-primary mb-0">{{ strtoupper($phim->tieu_de) }}</h5>
                    <span class="badge ms-2 {{ $status[1] }}">{{ $status[0] }}</span>
                </div>

                <ul class="list-unstyled small text-secondary mb-2">
                    <li>⏱️ <strong>Thời lượng:</strong> {{ $phim->thoi_luong ?? '—' }} phút</li>
                    <li>📅 <strong>Ngày công chiếu:</strong> {{ optional($phim->ngay_cong_chieu)->format('d/m/Y') ?? '—' }}</li>
                    <li>📅 <strong>Ngày kết thúc:</strong> {{ optional($phim->ngay_ket_thuc)->format('d/m/Y') ?? '—' }}</li>
                    <li>🎬 <strong>Đạo diễn:</strong> {{ $phim->dao_dien ?? '—' }}</li>
                    <li>👥 <strong>Diễn viên:</strong> {{ $phim->dien_vien ?? '—' }}</li>
                    <li>🗣️ <strong>Ngôn ngữ:</strong> {{ $phim->ngonNgu->ten ?? '—' }}</li>
                    <li>📁 <strong>Danh mục:</strong>
                        @forelse($phim->danhMucs as $dm)
                            <span class="badge bg-light text-dark border">{{ $dm->ten }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </li>
                    <li>💾 <strong>Định dạng:</strong> {{ $phim->dinh_dang }}</li>
                    <li>💬 <strong>Phụ đề:</strong> {{ $phim->phu_de ? 'Có' : 'Không' }}</li>
                    <li>🔞 <strong>Giới hạn tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?? 'P' }}</li>
                </ul>
            </div>

            {{-- 🧩 Hành động --}}
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

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $phims->appends(request()->query())->links() }}
    </div>
</div>

{{-- 💅 CSS --}}
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
.movie-poster {
    width: 200px;
    height: 300px;
    overflow: hidden;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.movie-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 16px;
    transition: transform 0.3s ease;
}
.movie-poster img:hover {
    transform: scale(1.05);
}
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
.movie-details {
    flex: 1;
    padding: 0 15px;
}
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
.filter-input, .filter-select {
    border-radius: 50px;
    padding: 0.6rem 1.1rem;
    font-size: 0.95rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}
.filter-input:focus, .filter-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
}
.filter-btn, .filter-reset-btn {
    border-radius: 50px;
    padding: 0.55rem 1.2rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}
.filter-btn:hover {
    background-color: #0b5ed7;
    box-shadow: 0 3px 10px rgba(13,110,253,0.25);
}
.filter-reset-btn:hover {
    background-color: #dc3545;
    color: #fff;
    box-shadow: 0 3px 10px rgba(220,53,69,0.25);
}
.card {
    border-radius: 20px;
}
.form-select,
.form-control {
    transition: all 0.2s ease;
}

.form-select:focus,
.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.25);
    border-color: #3b82f6;
}

.btn {
    transition: 0.2s ease;
}
.btn:hover {
    transform: translateY(-1px);
}

</style>
</style>
@endsection
