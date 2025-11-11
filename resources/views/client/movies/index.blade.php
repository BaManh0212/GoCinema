@extends('client.layouts.app')

@section('title', 'Tất cả phim - GoCinema')

@section('content')
<section class="py-5">
<div class="container text-light">

    {{-- ================== TIÊU ĐỀ ================== --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-danger section-title">🎬 Tất cả phim</h2>
        <p class="text-secondary mb-0">Khám phá những bộ phim mới nhất và hấp dẫn nhất tại GoCinema</p>
    </div>

    {{-- ================== BỘ LỌC ================== --}}
    <form method="GET" action="{{ route('movies.index') }}" class="bg-dark px-4 py-3 rounded-4 shadow-sm mb-5 filter-box">
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

            {{-- Danh mục --}}
            <div class="col-md-4">
                <label for="danh_muc" class="form-label fw-semibold text-light">Danh mục</label>
                <select id="danh_muc" name="danh_muc" class="form-select bg-body-tertiary border-secondary text-light">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($danhMucs as $dm)
                        <option value="{{ $dm->id }}" {{ request('danh_muc') == $dm->id ? 'selected' : '' }}>
                            {{ $dm->ten }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Trạng thái --}}
            <div class="col-md-3">
                <label for="trang_thai" class="form-label fw-semibold text-light">Trạng thái</label>
                <select id="trang_thai" name="trang_thai" class="form-select bg-body-tertiary border-secondary text-light">
                    <option value="">-- Tất cả --</option>
                    <option value="dang_chieu" {{ request('trang_thai') == 'dang_chieu' ? 'selected' : '' }}>🎟️ Đang chiếu</option>
                    <option value="sap_chieu" {{ request('trang_thai') == 'sap_chieu' ? 'selected' : '' }}>⏳ Sắp chiếu</option>
                </select>
            </div>

            {{-- Nút lọc --}}
            <div class="col-md-1 d-grid">
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
                        {{-- Danh mục --}}    
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
            <div class="col-12 text-center text-muted fs-5 py-4">Không có phim nào phù hợp.</div>
        @endforelse
    </div>

    {{-- PHÂN TRANG --}}
    <div class="mt-5 d-flex justify-content-center">
        {{ $movies->appends(request()->query())->links() }}
    </div>
</div>
</section>


@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
/* Bộ lọc */
form.bg-dark {
    background-color: #141a2a !important;
    border-radius: 16px;
}
.form-control, .form-select,
.select2-container--bootstrap-5 .select2-selection--single {
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

/* Select2 */
.select2-container--bootstrap-5 .select2-selection__rendered {
    color: #f8f9fa !important;
}
.select2-container--bootstrap-5 .select2-results__option {
    background-color: #1b2333;
    color: #f8f9fa;
}
.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #dc3545 !important;
    color: #fff !important;
}
.select2-dropdown {
    background-color: #1b2333 !important;
    border: 1px solid #495057 !important;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    const $form = $('form');
    const $danhMuc = $('#danh_muc');
    const $trangThai = $('#trang_thai');

    $danhMuc.select2({
        theme: 'bootstrap-5',
        placeholder: '-- Tất cả danh mục --',
        allowClear: true,
        width: '100%'
    });

    // Auto submit khi thay đổi filter
    $danhMuc.on('change', () => $form.submit());
    $trangThai.on('change', () => $form.submit());
});
</script>
@endpush
