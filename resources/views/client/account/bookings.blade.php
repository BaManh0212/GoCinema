@extends('client.layouts.app')

@section('title', 'Lịch sử đặt vé')

@section('content')
<div class="container py-5">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg me-4">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-ticket-alt fa-3x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-1">Lịch sử đặt vé</h3>
                            <p class="mb-2 opacity-75">
                                <i class="fas fa-user me-2"></i>{{ $user->ho_ten }}
                            </p>
                            <div class="d-flex gap-3">
                                <div class="badge bg-white text-primary px-3 py-2">
                                    <i class="fas fa-star me-1"></i>
                                    <strong>{{ number_format($user->diem) }}</strong> điểm
                                </div>
                                @if($user->so_dien_thoai)
                                <div class="badge bg-white bg-opacity-25 text-white px-3 py-2">
                                    <i class="fas fa-phone me-1"></i>{{ $user->so_dien_thoai }}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ url('/') }}" class="btn btn-light">
                                <i class="fas fa-home me-1"></i>Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Lịch sử đặt vé --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history text-primary me-2"></i>Lịch sử đặt vé của bạn
                        </h5>
                        <a href="{{ route('account.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user me-1"></i>Quay lại hồ sơ
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($bookings->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có lịch sử đặt vé</h5>
                            <p class="text-muted">Bạn chưa đặt vé nào. Hãy đặt vé xem phim ngay!</p>
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="fas fa-film me-1"></i>Đặt vé ngay
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Phim</th>
                                        <th>Rạp & Phòng</th>
                                        <th>Suất chiếu</th>
                                        <th>Ghế</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đặt</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">#{{ $booking->id }}</strong>
                                                @if($booking->ma_giam_gia)
                                                    <br><small class="text-muted">Mã: {{ $booking->ma_giam_gia->ma }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($booking->suatChieu->phim->poster)
                                                        <img src="{{ asset('uploads/phim/' . $booking->suatChieu->phim->poster) }}"
                                                             alt="{{ $booking->suatChieu->phim->ten_phim }}"
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 60px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $booking->suatChieu->phim->tieu_de ?? 'N/A' }}</strong>
                                                        <br><small class="text-muted">{{ Str::limit($booking->suatChieu->phim->mo_ta ?? '', 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $booking->suatChieu->phong->rap->ten ?? 'N/A' }}</strong>
                                                <br><small class="text-muted">Phòng {{ $booking->suatChieu->phong->ten ?? '' }}</small>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($booking->suatChieu->gio_bat_dau)->format('l, d/m/Y H:i') }} -
                                                {{ \Carbon\Carbon::parse($booking->suatChieu->gio_ket_thuc)->format('H:i') }}
                                                <br><small class="text-muted">{{ $booking->suatChieu->gio_chieu ?? '' }}</small>
                                            </td>
                                            <td>
                                                @foreach($booking->chiTietVes as $detail)
                                                    <span class="badge bg-secondary me-1">{{ $detail->ghe->hang }}{{ $detail->ghe->cot }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ number_format($booking->tong_tien) }}đ</strong>
                                                @if($booking->ma_giam_gia)
                                                    <br><small class="text-muted">Giảm {{ number_format($booking->ma_giam_gia->gia_tri) }}đ</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->trang_thai == 'da_thanh_toan')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                                    </span>
                                                @elseif($booking->trang_thai == 'cho_thanh_toan')
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Chờ thanh toán
                                                    </span>
                                                @elseif($booking->trang_thai == 'da_huy')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Đã hủy
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $booking->trang_thai }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('booking.confirm', $booking->id) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($booking->trang_thai == 'cho_thanh_toan')
                                                        <form method="POST" action="{{ route('booking.cancel', $booking->id) }}"
                                                              class="d-inline"
                                                              onsubmit="return confirm('Bạn có chắc muốn hủy đơn đặt vé này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hủy đơn">
                                                                <i class="fas fa-times"></i>
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
                        <div class="mt-4">
                            {{ $bookings->links() }}
                        </div>

                        {{-- Thống kê --}}
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <div class="card-body py-3">
                                        <h4 class="mb-1">{{ $bookings->total() }}</h4>
                                        <small>Tổng đơn</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                    <div class="card-body py-3">
                                        <h4 class="mb-1">{{ $bookings->where('trang_thai', 'da_thanh_toan')->count() }}</h4>
                                        <small>Đã thanh toán</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                    <div class="card-body py-3">
                                        <h4 class="mb-1">{{ $bookings->where('trang_thai', 'cho_thanh_toan')->count() }}</h4>
                                        <small>Chờ thanh toán</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                                    <div class="card-body py-3">
                                        <h4 class="mb-1">{{ number_format($bookings->where('trang_thai', 'da_thanh_toan')->sum('tong_tien')) }}đ</h4>
                                        <small>Tổng chi tiêu</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
    }

    .avatar-lg {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .table th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .badge {
        font-size: 0.75rem;
    }

    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endpush
@endsection
