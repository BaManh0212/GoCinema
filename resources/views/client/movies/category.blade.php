@extends('client.layouts.app')

@section('title', 'Phim - ' . $danhMuc->ten)

@section('content')
<div class="container py-5 text-light">

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
                            <h6 class="card-title text-white mb-1 fw-semibold">{{ $phim->tieu_de }}</h6>
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


@endsection

@push('styles')
<style>
/* Movie Card Styling */
.movie-card {
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
    background: #1a2035;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.movie-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3), 0 0 20px rgba(255, 107, 107, 0.2);
    border-color: rgba(255, 107, 107, 0.3);
}

/* Movie Poster Styling */
.poster-img {
    width: 100%;
    height: 380px;
    object-fit: cover;
    border-radius: 16px 16px 0 0;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border-bottom: 1px solid rgba(0, 0, 0, 0.2);
    filter: brightness(1);
}

.movie-card:hover .poster-img {
    transform: scale(1.05);
    filter: brightness(1.1);
}

/* Card Body */
.card-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    background: transparent;
    z-index: 2;
}

/* Movie Title */
.card-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #fff;
    font-size: 1.1rem;
    line-height: 1.3;
    min-height: 2.6em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Movie Info */
.movie-info {
    color: #a0aec0;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
}

/* Status Badge */
.status-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    z-index: 3;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.bg-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.bg-warning {
    background: linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%);
    color: #1a202c !important;
}

/* Overlay Effects */
.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.8) 0%, rgba(185, 28, 28, 0.8) 100%);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 3;
    border-radius: 16px;
}

.movie-card:hover .overlay {
    opacity: 1;
}

.overlay span {
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Filter Box */
.filter-box {
    background: #1a2035 !important;
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.form-control, .form-select,
.select2-container--bootstrap-5 .select2-selection--single {
    background-color: #1a2035 !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
    border-radius: 8px !important;
    transition: all 0.2s ease;
}

.input-group-text {
    background-color: #1a2035 !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #a0aec0 !important;
}

.form-control:focus, .form-select:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
}

/* Select2 Customization */
.select2-container--bootstrap-5 .select2-selection__rendered {
    color: #fff !important;
}

.select2-container--bootstrap-5 .select2-selection--single {
    height: 38px;
    display: flex;
    align-items: center;
}

.select2-container--bootstrap-5 .select2-selection__arrow {
    height: 36px;
}

.select2-container--bootstrap-5 .select2-results__option {
    background-color: #1a2035;
    color: #fff;
    padding: 8px 16px;
}

.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #dc3545 !important;
    color: #fff !important;
}

.select2-dropdown {
    background-color: #1a2035 !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    border-radius: 8px !important;
    overflow: hidden;
}

/* Pagination Styling */
.pagination .page-link {
    background-color: #1a2035;
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #a0aec0;
    margin: 0 2px;
    border-radius: 8px !important;
}

.pagination .page-item.active .page-link {
    background-color: #dc3545;
    border-color: #dc3545;
}

.pagination .page-link:hover {
    background-color: #2d3748;
    color: #fff;
}
</style>
@endpush
