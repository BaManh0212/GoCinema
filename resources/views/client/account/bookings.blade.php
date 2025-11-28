@extends('client.layouts.app')

@section('title', 'Lịch sử đặt vé')

@section('content')
<div class="container-fluid py-2">
    {{-- Header --}}

    {{-- Quick Stats --}}
    @if(!$bookings->isEmpty())
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px;">
                <div class="card-body text-center p-2">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-receipt fa-2x opacity-75"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $bookings->total() }}</h4>
                    <p class="mb-0 opacity-90 small">Tổng đơn hàng</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 10px;">
                <div class="card-body text-center p-2">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $bookings->where('trang_thai', 'da_thanh_toan')->count() }}</h4>
                    <p class="mb-0 opacity-90 small">Đã thanh toán</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; border-radius: 10px;">
                <div class="card-body text-center p-2">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $bookings->where('trang_thai', 'cho_thanh_toan')->count() }}</h4>
                    <p class="mb-0 opacity-90 small">Chờ thanh toán</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #e83e8c 0%, #dc3545 100%); color: white; border-radius: 10px;">
                <div class="card-body text-center p-2">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fas fa-wallet fa-2x opacity-75"></i>
                    </div>
                    <h4 class="mb-1 fw-bold">{{ number_format($bookings->where('trang_thai', 'da_thanh_toan')->sum('tong_tien')) }}đ</h4>
                    <p class="mb-0 opacity-90 small">Tổng chi tiêu</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Search & Filter --}}
    <div class="row mb-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%); color: white;">
                <div class="card-body p-2">
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
                <div class="card-header border-bottom-0 py-2" style="background: linear-gradient(135deg, #0f3460 0%, #1a1a2e 100%); color: white;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold text-white">
                                <i class="fas fa-history text-light me-2"></i>Lịch sử đặt vé
                            </h5>
                            <p class="text-light opacity-75 mb-0 small">Quản lý và theo dõi các đơn đặt vé của bạn</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('account.index') }}" class="btn btn-outline-light rounded-pill px-3 text-white">
                                <i class="fas fa-user me-1"></i>Hồ sơ cá nhân
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
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead class="table-header">
                                    <tr>
                                        <th class="border-0 text-light fw-semibold">Poster</th>
                                        <th class="border-0 text-light fw-semibold">Mã đơn</th>
                                        <th class="border-0 text-light fw-semibold">Phim</th>
                                        <th class="border-0 text-light fw-semibold">Chi tiết</th>
                                        <th class="border-0 text-light fw-semibold">Giá tiền</th>
                                        <th class="border-0 text-light fw-semibold">Trạng thái</th>
                                        <th class="border-0 text-light fw-semibold">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                    <tr class="booking-row" data-status="{{ $booking->trang_thai }}" data-movie="{{ $booking->suatChieu->phim->tieu_de ?? '' }}" data-code="{{ $booking->id }}">
                                        <td class="align-middle">
                                            <div class="movie-poster">
                                                @if($booking->suatChieu->phim->anh_poster)
                                                    <img src="{{ asset('storage/' . $booking->suatChieu->phim->anh_poster) }}"
                                                         alt="{{ $booking->suatChieu->phim->tieu_de }}"
                                                         class="rounded-3 shadow-sm"
                                                         style="width: 60px; height: 90px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                                                @else
                                                    <div class="bg-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 90px; border: 1px solid rgba(255,255,255,0.1);">
                                                        <i class="fas fa-film fa-lg text-light"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill mb-2">#{{ $booking->id }}</span>
                                                @if($booking->ma_giam_gia)
                                                    <span class="badge bg-success fs-6 px-2 py-1 rounded-pill">
                                                        <i class="fas fa-tag me-1"></i>{{ $booking->ma_giam_gia->ma }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <h6 class="mb-1 fw-bold text-white">{{ $booking->suatChieu->phim->tieu_de ?? 'N/A' }}</h6>
                                            <small class="text-light opacity-75">{{ $booking->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td class="align-middle">
                                            <div class="booking-info text-light opacity-75">
                                                <div class="mb-1">
                                                    <i class="fas fa-map-marker-alt me-2 text-info"></i>
                                                    <strong class="text-light">{{ $booking->suatChieu->phong->rap->ten ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="mb-1">
                                                    <i class="fas fa-calendar-alt me-2 text-info"></i>
                                                    {{ \Carbon\Carbon::parse($booking->suatChieu->gio_bat_dau)->format('d/m/Y H:i') }}
                                                </div>
                                                <div class="mb-1">
                                                    <i class="fas fa-chair me-2 text-info"></i>
                                                    @foreach($booking->chiTietVes as $detail)
                                                        <span class="badge bg-light text-dark me-1">{{ $detail->ghe->hang }}{{ $detail->ghe->cot }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="price-section">
                                                <div class="price-amount fs-5 fw-bold text-success mb-1">
                                                    {{ number_format($booking->tong_tien) }}đ
                                                </div>
                                                @if($booking->ma_giam_gia)
                                                    <div class="discount text-light opacity-75 small">
                                                        Giảm {{ number_format($booking->ma_giam_gia->gia_tri) }}đ
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($booking->trang_thai == 'da_thanh_toan')
                                                <span class="badge bg-success fs-6 px-3 py-1 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                                </span>
                                            @elseif($booking->trang_thai == 'cho_thanh_toan')
                                                <span class="badge bg-warning fs-6 px-3 py-1 rounded-pill">
                                                    <i class="fas fa-clock me-1"></i>Chờ thanh toán
                                                </span>
                                            @elseif($booking->trang_thai == 'da_huy')
                                                <span class="badge bg-danger fs-6 px-3 py-1 rounded-pill">
                                                    <i class="fas fa-times-circle me-1"></i>Đã hủy
                                                </span>
                                            @else
                                                <span class="badge bg-secondary fs-6 px-3 py-1 rounded-pill">{{ $booking->trang_thai }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="btn-group-vertical" role="group">
                                                <a href="{{ route('booking.confirm', $booking->id) }}"
                                                   class="btn btn-outline-light btn-sm rounded-pill mb-1 text-white border-light">
                                                    <i class="fas fa-eye me-1"></i>Xem
                                                </a>
                                                @if($booking->trang_thai == 'cho_thanh_toan')
                                                    <form method="POST" action="{{ route('booking.cancel', $booking->id) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn đặt vé này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                                            <i class="fas fa-times me-1"></i>Hủy
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

    .table-dark {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 10px;
        overflow: hidden;
    }

    .table-dark thead th {
        background: linear-gradient(135deg, #0f3460 0%, #1a1a2e 100%);
        border: none;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .table-dark tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.1);
        transition: all 0.2s ease;
    }

    .table-dark tbody tr:hover {
        background: rgba(255,255,255,0.05) !important;
        transform: scale(1.01);
    }

    .table-dark tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
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

    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .table-responsive {
            font-size: 0.85rem;
        }

        .table-dark thead th {
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .table-dark tbody td {
            padding: 0.5rem;
        }

        .btn-group-vertical .btn {
            margin-bottom: 0.25rem;
        }

        .movie-poster img {
            width: 50px;
            height: 75px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const timeFilter = document.getElementById('timeFilter');
    const bookingRows = document.querySelectorAll('.booking-row');

    function filterBookings() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const timeValue = timeFilter.value;

        bookingRows.forEach(row => {
            const movieName = row.dataset.movie.toLowerCase();
            const bookingCode = row.dataset.code.toString();
            const status = row.dataset.status;

            let showRow = true;

            // Search filter
            if (searchTerm) {
                showRow = movieName.includes(searchTerm) || bookingCode.includes(searchTerm);
            }

            // Status filter
            if (statusValue && showRow) {
                showRow = status === statusValue;
            }

            // Time filter (basic implementation - you might want to enhance this)
            if (timeValue && showRow) {
                // This would need actual date comparison - simplified for now
                showRow = true; // Placeholder
            }

            row.style.display = showRow ? 'table-row' : 'none';
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
