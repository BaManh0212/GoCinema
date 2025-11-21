@extends('staff.layouts.staff')

@section('title', '🎬 Chọn Phim - Đặt Vé Tại Quầy')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-film"></i> Chọn Phim Đặt Vé Tại Quầy
            </h2>
            <small class="text-muted">Chọn phim để bắt đầu đặt vé tại quầy</small>
        </div>
        <a href="{{ route('staff.donve.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ✅ Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 🔍 Danh sách phim --}}
    <div class="row">
        @forelse($phims as $phim)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="position-relative">
                        @if($phim->anh_poster)
                            <img src="{{ asset('storage/' . $phim->anh_poster) }}"
                                 alt="{{ $phim->tieu_de }}"
                                 class="card-img-top"
                                 style="height: 300px; object-fit: cover;">
                        @else
                            <div class="bg-dark bg-opacity-25 d-flex align-items-center justify-content-center"
                                 style="height: 300px;">
                                <i class="bi bi-image text-white-50 fs-1"></i>
                            </div>
                        @endif
                        <div class="card-img-overlay d-flex align-items-end p-0">
                            <div class="bg-dark bg-opacity-75 w-100 p-3">
                                <h5 class="card-title text-white mb-1">{{ $phim->tieu_de }}</h5>
                                <p class="card-text text-white-50 small mb-2">
                                    <i class="bi bi-clock me-1"></i>{{ $phim->thoi_luong }} phút
                                    @if($phim->do_tuoi_gioi_han)
                                        <span class="badge bg-warning ms-2">{{ $phim->do_tuoi_gioi_han }}</span>
                                    @endif
                                </p>
                                <a href="{{ route('staff.donve.selectSuat', $phim->id) }}"
                                   class="btn btn-primary btn-sm rounded-pill w-100">
                                    <i class="bi bi-calendar-event me-1"></i> Chọn Suất Chiếu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-film text-muted fs-1 mb-3"></i>
                    <h5 class="text-muted">Không có phim nào đang chiếu</h5>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card {
    transition: transform 0.25s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
