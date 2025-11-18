@extends('client.layouts.app')

@section('title', 'Lịch sử đặt vé')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; overflow: hidden;">
                <div class="card-body text-white p-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xl me-4">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                        <i class="fas fa-ticket-alt fa-4x text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h2 class="mb-2 fw-bold">Lịch sử đặt vé</h2>
                                    <p class="mb-3 opacity-90 fs-5">
                                        <i class="fas fa-user-circle me-2"></i>{{ $user->ho_ten }}
                                    </p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="badge bg-white text-primary px-4 py-2 fs-6 rounded-pill">
                                            <i class="fas fa-star me-2"></i>
                                            <strong>{{ number_format($user->diem) }}</strong> điểm tích lũy
                                        </div>
                                        @if($user->so_dien_thoai)
                                        <div class="badge bg-white bg-opacity-25 text-white px-4 py-2 fs-6 rounded-pill">
                                            <i class="fas fa-phone me-2"></i>{{ $user->so_dien_thoai }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="{{ url('/') }}" class="btn btn-light btn-lg rounded-pill px-4 py-3 shadow">
                                <i class="fas fa-home me-2"></i>Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    @if(!$bookings->isEmpty())
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-receipt fa-3x opacity-75"></i>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ $bookings->total() }}</h3>
                    <p class="mb-0 opacity-90">Tổng đơn hàng</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-check-circle fa-3x opacity-75"></i>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ $bookings->where('trang_thai', 'da_thanh_toan')->count() }}</h3>
                    <p class="mb-0 opacity-90">Đã thanh toán</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-clock fa-3x opacity-75"></i>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ $bookings->where('trang_thai', 'cho_thanh_toan')->count() }}</h3>
                    <p class="mb-0 opacity-90">Chờ thanh toán</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #e83e8c 0%, #dc3545 100%); color: white; border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-wallet fa-3x opacity-75"></i>
                    </div>
                    <h3 class="mb-2 fw-bold">{{ number_format($bookings->where('trang_thai', 'da_thanh_toan')->sum('tong_tien')) }}đ</h3>
                    <p class="mb-0 opacity-90">Tổng chi tiêu</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Search & Filter --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%); color: white;">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-white">Tìm kiếm</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-0 text-light">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control border-0 bg-dark text-light" id="searchInput" placeholder="Tìm theo tên phim, mã đơn..." style="color: white !important;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-white">Trạng thái</label>
                            <select class="form-select border-0 bg-dark text-light custom-select" id="statusFilter" style="color: white !important; background-color: #000 !important;">
                                <option value="" style="color: white; background-color: #000;">Tất cả trạng thái</option>
                                <option value="da_thanh_toan" style="color: white; background-color: #000;">Đã thanh toán</option>
                                <option value="cho_thanh_toan" style="color: white; background-color: #000;">Chờ thanh toán</option>
                                <option value="da_huy" style="color: white; background-color: #000;">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-white">Thời gian</label>
                            <select class="form-select border-0 bg-dark text-light custom-select" id="timeFilter" style="color: white !important; background-color: #000 !important;">
                                <option value="" style="color: white; background-color: #000;">Tất cả thời gian</option>
                                <option value="7" style="color: white; background-color: #000;">7 ngày qua</option>
                                <option value="30" style="color: white; background-color: #000;">30 ngày qua</option>
                                <option value="90" style="color: white; background-color: #000;">3 tháng qua</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-light w-100 text-white border-light" onclick="resetFilters()">
                                <i class="fas fa-undo me-1"></i>Đặt lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div>
                    <strong>Thành công!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>Lỗi!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bookings List --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white;">
                <div class="card-header border-bottom-0 py-4" style="background: linear-gradient(135deg, #0f3460 0%, #1a1a2e 100%); color: white;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold text-white">
                                <i class="fas fa-history text-light me-3"></i>Lịch sử đặt vé
                            </h4>
                            <p class="text-light opacity-75 mb-0">Quản lý và theo dõi các đơn đặt vé của bạn</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('account.index') }}" class="btn btn-outline-light rounded-pill px-4 text-white">
                                <i class="fas fa-user me-2"></i>Hồ sơ cá nhân
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($bookings->isEmpty())
                        <div class="text-center py-5 px-4">
                            <div class="empty-state">
                                <div class="empty-state-icon mb-4">
                                    <i class="fas fa-ticket-alt fa-5x text-muted opacity-25"></i>
                                </div>
                                <h4 class="text-muted mb-3">Chưa có lịch sử đặt vé</h4>
                                <p class="text-muted mb-4 fs-5">Bạn chưa có đơn đặt vé nào. Hãy khám phá các bộ phim hấp dẫn và đặt vé ngay!</p>
                                <a href="{{ url('/') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow">
                                    <i class="fas fa-film me-2"></i>Khám phá phim
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="booking-list">
                            @foreach($bookings as $booking)
                            <div class="booking-item border-bottom p-4" data-status="{{ $booking->trang_thai }}" data-movie="{{ $booking->suatChieu->phim->tieu_de ?? '' }}" data-code="{{ $booking->id }}" style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1) !important;">
                                <div class="row align-items-center">
                                    <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                                        <div class="movie-poster">
                                            @if($booking->suatChieu->phim->poster)
                                                <img src="{{ asset('uploads/phim/' . $booking->suatChieu->phim->poster) }}"
                                                     alt="{{ $booking->suatChieu->phim->tieu_de }}"
                                                     class="rounded-3 shadow-sm w-100"
                                                     style="max-width: 120px; height: 160px; object-fit: cover; border: 2px solid rgba(255,255,255,0.1);">
                                            @else
                                                <div class="bg-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 120px; height: 160px; border: 2px solid rgba(255,255,255,0.1);">
                                                    <i class="fas fa-film fa-3x text-light"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-5 mb-3 mb-md-0">
                                        <div class="booking-details">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill me-3">#{{ $booking->id }}</span>
                                                @if($booking->ma_giam_gia)
                                                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">
                                                        <i class="fas fa-tag me-1"></i>{{ $booking->ma_giam_gia->ma }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h5 class="mb-2 fw-bold text-white">{{ $booking->suatChieu->phim->tieu_de ?? 'N/A' }}</h5>
                                            <div class="booking-info text-light opacity-75">
                                                <div class="mb-1">
                                                    <i class="fas fa-map-marker-alt me-2 text-info"></i>
                                                    <strong class="text-light">{{ $booking->suatChieu->phong->rap->ten ?? 'N/A' }}</strong> - Phòng {{ $booking->suatChieu->phong->ten ?? '' }}
                                                </div>
                                                <div class="mb-1">
                                                    <i class="fas fa-calendar-alt me-2 text-info"></i>
                                                    {{ \Carbon\Carbon::parse($booking->suatChieu->gio_bat_dau)->format('l, d/m/Y \l\ú\c H:i') }} -
                                                    {{ \Carbon\Carbon::parse($booking->suatChieu->gio_ket_thuc)->format('H:i') }}
                                                </div>
                                                <div class="mb-1">
                                                    <i class="fas fa-chair me-2 text-info"></i>
                                                    Ghế:
                                                    @foreach($booking->chiTietVes as $detail)
                                                        <span class="badge bg-light text-dark me-1">{{ $detail->ghe->hang }}{{ $detail->ghe->cot }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 mb-3 mb-md-0 text-center">
                                        <div class="price-section">
                                            <div class="price-amount fs-4 fw-bold text-success mb-1">
                                                {{ number_format($booking->tong_tien) }}đ
                                            </div>
                                            @if($booking->ma_giam_gia)
                                                <div class="discount text-light opacity-75 small">
                                                    Giảm {{ number_format($booking->ma_giam_gia->gia_tri) }}đ
                                                </div>
                                            @endif
                                            <div class="booking-date text-light opacity-75 small mt-2">
                                                {{ $booking->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 text-center">
                                        <div class="status-section mb-3">
                                            @if($booking->trang_thai == 'da_thanh_toan')
                                                <span class="badge bg-success fs-6 px-4 py-2 rounded-pill w-100">
                                                    <i class="fas fa-check-circle me-2"></i>Đã thanh toán
                                                </span>
                                            @elseif($booking->trang_thai == 'cho_thanh_toan')
                                                <span class="badge bg-warning fs-6 px-4 py-2 rounded-pill w-100">
                                                    <i class="fas fa-clock me-2"></i>Chờ thanh toán
                                                </span>
                                            @elseif($booking->trang_thai == 'da_huy')
                                                <span class="badge bg-danger fs-6 px-4 py-2 rounded-pill w-100">
                                                    <i class="fas fa-times-circle me-2"></i>Đã hủy
                                                </span>
                                            @else
                                                <span class="badge bg-secondary fs-6 px-4 py-2 rounded-pill w-100">{{ $booking->trang_thai }}</span>
                                            @endif
                                        </div>
                                        <div class="action-buttons">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('booking.confirm', $booking->id) }}"
                                                   class="btn btn-outline-light btn-sm rounded-pill px-3 text-white border-light">
                                                    <i class="fas fa-eye me-1"></i>Xem
                                                </a>
                                                @if($booking->trang_thai == 'cho_thanh_toan')
                                                    <form method="POST" action="{{ route('booking.cancel', $booking->id) }}"
                                                          class="d-inline ms-1"
                                                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn đặt vé này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                            <i class="fas fa-times me-1"></i>Hủy
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="p-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                            {{ $bookings->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-xl {
        animation: gentleFloat 4s ease-in-out infinite;
    }

    @keyframes gentleFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }

    .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 15px !important;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }

    .booking-item {
        transition: all 0.2s ease;
        background: #fff;
    }

    .booking-item:hover {
        background: rgba(255,255,255,0.1) !important;
        transform: translateX(5px);
    }

    .movie-poster img {
        transition: transform 0.3s ease;
    }

    .movie-poster img:hover {
        transform: scale(1.05);
    }

    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .btn {
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .empty-state {
        max-width: 500px;
        margin: 0 auto;
    }

    .empty-state-icon {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .form-control, .form-select {
        border-radius: 10px !important;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
    }

    .custom-select option {
        color: white !important;
        background-color: #000 !important;
    }

    .input-group-text {
        border-radius: 10px 0 0 10px !important;
    }

    .booking-list .booking-item:last-child {
        border-bottom: none !important;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
        }

        .booking-item {
            padding: 1rem !important;
        }

        .btn-group {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-group .btn {
            width: 100%;
        }
    }

    .status-section .badge {
        display: inline-block;
        min-width: 140px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const timeFilter = document.getElementById('timeFilter');
    const bookingItems = document.querySelectorAll('.booking-item');

    function filterBookings() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const timeValue = timeFilter.value;

        bookingItems.forEach(item => {
            const movieName = item.dataset.movie.toLowerCase();
            const bookingCode = item.dataset.code.toString();
            const status = item.dataset.status;

            let showItem = true;

            // Search filter
            if (searchTerm) {
                showItem = movieName.includes(searchTerm) || bookingCode.includes(searchTerm);
            }

            // Status filter
            if (statusValue && showItem) {
                showItem = status === statusValue;
            }

            // Time filter (basic implementation - you might want to enhance this)
            if (timeValue && showItem) {
                // This would need actual date comparison - simplified for now
                showItem = true; // Placeholder
            }

            item.style.display = showItem ? 'block' : 'none';
        });
    }

    searchInput.addEventListener('input', filterBookings);
    statusFilter.addEventListener('change', filterBookings);
    timeFilter.addEventListener('change', filterBookings);

    window.resetFilters = function() {
        searchInput.value = '';
        statusFilter.value = '';
        timeFilter.value = '';
        filterBookings();
    };

    // Add loading animation for actions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang xử lý...';
                submitBtn.disabled = true;
            }
        });
    });
});
</script>
@endpush
@endsection
