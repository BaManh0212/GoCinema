@extends('client.layouts.app')

@section('title', 'Phim - ' . $danhMuc->ten)

@section('content')
<div class="container py-5 mt-5 text-light">

    {{-- ================== TIÊU ĐỀ ================== --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-danger section-title">🎬 {{ $danhMuc->ten }}</h2>
        <p class="text-secondary mb-0">Khám phá các bộ phim thuộc danh mục "{{ $danhMuc->ten }}" tại GoCinema</p>
    </div>

    {{-- ================== BỘ LỌC ================== --}}
    <form method="GET" action="{{ route('movies.category', $danhMuc->slug) }}" class="bg-dark px-4 py-3 rounded-4 shadow-sm mb-5 filter-box">
        <div class="row g-3 align-items-end">
            {{-- Ô tìm kiếm --}}
            <div class="col-md-4">
                <label for="search" class="form-label fw-semibold text-light">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-secondary text-light border-secondary">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="search" name="search"
                           class="form-control bg-body-tertiary text-light border-secondary"
                           placeholder="Nhập tên phim..." value="{{ request('search') }}">
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="col-md-3">
                <label for="trang_thai" class="form-label fw-semibold text-light">Trạng thái</label>
                <select id="trang_thai" name="trang_thai" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="dang_chieu" {{ request('trang_thai') == 'dang_chieu' ? 'selected' : '' }}>🎟️ Đang chiếu</option>
                    <option value="sap_chieu" {{ request('trang_thai') == 'sap_chieu' ? 'selected' : '' }}>⏳ Sắp chiếu</option>
                </select>
            </div>

            {{-- Nút lọc --}}
            <div class="col-md-2 d-grid">
                <button class="btn btn-danger fw-semibold" type="submit">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    {{-- ================== DANH SÁCH PHIM ================== --}}
    <div class="row g-4">
        @forelse ($movies as $phim)
            <div class="col-6 col-md-3">
                <a href="{{ route('movies.show', $phim->slug) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 movie-card overflow-hidden position-relative">
                        {{-- Ảnh poster --}}
                        @if($phim->anh_poster)
                            <img src="{{ asset('storage/' . $phim->anh_poster) }}" class="card-img-top poster-img" alt="{{ $phim->tieu_de }}">
                        @else
                            <div class="card-img-top bg-secondary" style="height:280px;border-radius:8px 8px 0 0;"></div>
                        @endif

                        {{-- Nhãn trạng thái góc trên --}}
                        @php
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $ngayBatDau = $phim->ngay_cong_chieu ?? $phim->ngay_khoi_chieu ?? null;
                            $ngayKetThuc = $phim->ngay_ket_thuc ?? null;

                            if ($ngayBatDau && \Carbon\Carbon::parse($ngayBatDau)->lte($today) &&
                                (!$ngayKetThuc || \Carbon\Carbon::parse($ngayKetThuc)->gte($today))) {
                                $status = 'dang_chieu';
                            } else {
                                $status = 'sap_chieu';
                            }
                        @endphp
                        <div class="status-badge {{ $status === 'dang_chieu' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $status === 'dang_chieu' ? 'Đang chiếu' : 'Sắp chiếu' }}
                        </div>

                        {{-- Thông tin phim --}}
                        <div class="card-body text-center p-3">
                            <h6 class="card-title text-truncate mb-1 fw-semibold">{{ $phim->tieu_de }}</h6>
                            @if($phim->danhMucs->count())
                                <small class="text-info d-block mb-2">
                                    <i class="bi bi-tags-fill me-1"></i>
                                    {{ $phim->danhMucs->pluck('ten')->join(', ') }}
                                </small>
                            @endif
                            <small class="text-muted d-block mb-1">
                                <i class="bi bi-clock me-1"></i>Thời lượng: {{ $phim->thoi_luong }} phút
                            </small>

                            @if($phim->do_tuoi_gioi_han)
                                <small class="badge bg-danger">Độ tuổi: {{ $phim->do_tuoi_gioi_han }}</small>
                            @endif
                        </div>

                        {{-- Overlay --}}
                        <div class="overlay d-flex justify-content-center align-items-center">
                            <span class="text-white fw-bold">Xem chi tiết</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center text-muted fs-5 py-4">Không có phim nào trong danh mục này.</div>
        @endforelse
    </div>

    {{-- PHÂN TRANG --}}
    <div class="mt-5 d-flex justify-content-center">
        {{ $movies->appends(request()->query())->links() }}
    </div>
</div>

{{-- ================== CSS ================== --}}
<style>
.section-title {
    font-size: 2.6rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-shadow: 0 0 10px rgba(255,77,77,0.6);
}
.filter-box {
    border: 1px solid rgba(255,255,255,0.1);
    background: linear-gradient(145deg, #141a2a, #101624);
}
.movie-card {
    border-radius: 10px;
    background: #0f1625;
    transition: all 0.25s ease;
    position: relative;
}
.movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.5);
}
.poster-img {
    height: 280px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    transition: transform 0.25s ease;
}
.movie-card:hover .poster-img { transform: scale(1.05); }
.status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 2;
    box-shadow: 0 0 8px rgba(0,0,0,0.3);
}
.movie-card .overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.55);
    opacity: 0;
    transition: opacity 0.25s ease-in-out;
}
.movie-card:hover .overlay { opacity: 1; }
form.bg-dark {
    background-color: #141a2a !important;
    border-radius: 16px;
}
.form-control, .form-select {
    background-color: #1b2333 !important;
    border: 1px solid #495057 !important;
    color: #f8f9fa !important;
    border-radius: 8px !important;
}
.input-group-text {
    background-color: #1b2333 !important;
    border: 1px solid #495057 !important;
    color: #f8f9fa !important;
}
.form-control:focus, .form-select:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220,53,69,.25) !important;
}
</style>
@endsection
