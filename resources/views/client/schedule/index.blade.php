@extends('client.layouts.app')

@section('title', 'Lịch Chiếu Phim - GoCinema')

@section('content')
<section class="py-5">
<div class="container text-light">

    {{-- ================== TIÊU ĐỀ ================== --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-danger section-title">🎬 Lịch Chiếu Phim</h2>
        <p class="text-secondary mb-0">Xem lịch chiếu phim theo ngày tại GoCinema</p>
    </div>

    {{-- ================== CHỌN NGÀY ================== --}}
    <div class="mb-4">
        <div class="d-flex justify-content-center">
            <ul class="nav nav-pills gap-2 flex-nowrap overflow-auto pb-2" id="dayTabs" role="tablist" style="scrollbar-width:thin;">
                @foreach($dates as $date)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $date['date'] === $selectedDate ? 'active' : '' }} rounded-pill"
                           href="{{ route('schedule.index', ['date' => $date['date'], 'phim' => $selectedPhim, 'danh_muc' => $selectedDanhMuc]) }}"
                           style="white-space: nowrap;">
                            @if($date['is_today'])
                                <strong>Hôm nay</strong>
                            @else
                                {{ $date['label'] }}
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- ================== BỘ LỌC ================== --}}
    <form method="GET" action="{{ route('schedule.index') }}" class="bg-dark px-4 py-3 rounded-4 shadow-sm mb-5 filter-box">
        <input type="hidden" name="date" value="{{ $selectedDate }}">
        <div class="row g-3 align-items-end">
            {{-- Lọc theo phim --}}
            <div class="col-md-4">
                <label for="phim" class="form-label fw-semibold text-light">Lọc theo phim</label>
                <select id="phim" name="phim" class="form-select bg-body-tertiary border-secondary text-light">
                    <option value="">-- Tất cả phim --</option>
                    @foreach($allPhims as $phim)
                        <option value="{{ $phim->id }}" {{ $selectedPhim == $phim->id ? 'selected' : '' }}>
                            {{ $phim->tieu_de }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lọc theo danh mục --}}
            <div class="col-md-4">
                <label for="danh_muc" class="form-label fw-semibold text-light">Lọc theo danh mục</label>
                <select id="danh_muc" name="danh_muc" class="form-select bg-body-tertiary border-secondary text-light">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($danhMucs as $dm)
                        <option value="{{ $dm->id }}" {{ $selectedDanhMuc == $dm->id ? 'selected' : '' }}>
                            {{ $dm->ten }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nút lọc --}}
            <div class="col-md-4 d-grid">
                <button class="btn btn-danger fw-semibold" type="submit">
                    <i class="bi bi-funnel-fill me-1"></i> Áp dụng bộ lọc
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
                        <div class="overlay d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.6);">
                            <span class="text-white fw-bold" style="font-size: 1.1rem;">Xem chi tiết</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center text-muted fs-5 py-4">
                <i class="bi bi-calendar-x-fill display-4 d-block mb-3"></i>
                Không có phim nào có suất chiếu vào ngày {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}.
            </div>
        @endforelse
    </div>

</div>
</section>

@endsection

@push('styles')
<style>
/* Bộ lọc */
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
.form-control:focus, .form-select:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220,53,69,.25) !important;
}

/* Day tabs */
.nav-pills .nav-link {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #f8f9fa;
    transition: all 0.3s ease;
}
.nav-pills .nav-link:hover {
    background: rgba(229,57,53,0.2);
    border-color: #e53935;
    color: #fff;
}
.nav-pills .nav-link.active {
    background: #e53935;
    border-color: #e53935;
    color: #fff;
}
</style>
@endpush
