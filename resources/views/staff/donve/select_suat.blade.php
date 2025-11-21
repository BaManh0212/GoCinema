@extends('staff.layouts.staff')

@section('title', '🎬 Chọn Suất Chiếu - Đặt Vé Tại Quầy')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-calendar-event"></i> Chọn Suất Chiếu
            </h2>
            <small class="text-muted">{{ $phim->tieu_de }}</small>
        </div>
        <div>
            <a href="{{ route('staff.donve.create') }}" class="btn btn-outline-secondary rounded-pill me-2">
                <i class="bi bi-arrow-left"></i> Chọn Phim Khác
            </a>
            <a href="{{ route('staff.donve.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-house"></i> Trang Chủ
            </a>
        </div>
    </div>

    {{-- ✅ Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 🎭 Thông tin phim --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    @if($phim->anh_poster)
                        <img src="{{ asset('storage/' . $phim->anh_poster) }}"
                             alt="{{ $phim->tieu_de }}"
                             class="img-fluid rounded shadow"
                             style="max-height: 200px;">
                    @else
                        <div class="bg-dark bg-opacity-25 rounded d-flex align-items-center justify-content-center"
                             style="height: 200px;">
                            <i class="bi bi-image text-white-50 fs-1"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <h4 class="mb-2">{{ $phim->tieu_de }}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Thời lượng:</strong> {{ $phim->thoi_luong }} phút</p>
                            <p class="mb-1"><strong>Đạo diễn:</strong> {{ $phim->dao_dien ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Diễn viên:</strong> {{ $phim->dien_vien ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Độ tuổi:</strong>
                                @if($phim->do_tuoi_gioi_han)
                                    <span class="badge bg-warning">{{ $phim->do_tuoi_gioi_han }}</span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                    <p class="mb-0"><strong>Mô tả:</strong> {{ Str::limit($phim->mo_ta, 200) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 📅 Danh sách suất chiếu --}}
    @if($suatChieus->count() > 0)
        @foreach($suatChieus as $ngay => $suatTrongNgay)
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-date me-2"></i>
                        {{ \Carbon\Carbon::parse($ngay)->format('l, d/m/Y') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($suatTrongNgay as $suatChieu)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm suat-chieu-card" data-suat-id="{{ $suatChieu->id }}">
                                    <div class="card-body text-center">
                                        <h6 class="card-title mb-2">
                                            {{ \Carbon\Carbon::parse($suatChieu->gio_bat_dau)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($suatChieu->gio_ket_thuc)->format('H:i') }}
                                        </h6>
                                        <p class="card-text mb-2">
                                            <i class="bi bi-building me-1"></i>
                                            {{ $suatChieu->phong->ten }}
                                        </p>
                                        <p class="card-text mb-2">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $suatChieu->phong->rap->ten }}
                                        </p>
                                        <p class="card-text mb-3">
                                            <strong class="text-primary">{{ number_format($suatChieu->gia_ve, 0, ',', '.') }}đ</strong>
                                        </p>
                                        <a href="{{ route('staff.donve.selectSeats', $suatChieu->id) }}"
                                           class="btn btn-primary btn-sm w-100 rounded-pill">
                                            <i class="bi bi-ticket-perforated me-1"></i>
                                            Chọn Ghế
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted fs-1 mb-3"></i>
            <h5 class="text-muted">Không có suất chiếu nào khả dụng</h5>
            <p class="text-muted">Phim này hiện không có lịch chiếu trong tương lai.</p>
            <a href="{{ route('staff.donve.create') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Quay lại chọn phim khác
            </a>
        </div>
    @endif
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.suat-chieu-card {
    transition: transform 0.25s ease-in-out, box-shadow 0.25s ease-in-out;
    cursor: pointer;
}
.suat-chieu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
