@extends('client.layouts.app')

@section('title', 'Tài khoản cá nhân')

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
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-1">{{ $user->ho_ten }}</h3>
                            <p class="mb-2 opacity-75">
                                <i class="fas fa-envelope me-2"></i>{{ $user->email }}
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

    {{-- Tabs Navigation --}}
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                        <i class="fas fa-user-edit me-2"></i>Thông tin cá nhân
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab">
                        <i class="fas fa-ticket-alt me-2"></i>Lịch sử vé
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="points-tab" data-bs-toggle="tab" data-bs-target="#points" type="button" role="tab">
                        <i class="fas fa-gift me-2"></i>Điểm thưởng & Voucher
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                        <i class="fas fa-key me-2"></i>Đổi mật khẩu
                    </button>
                </li>
            </ul>

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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Có lỗi xảy ra:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Tab Content --}}
            <div class="tab-content" id="profileTabsContent">
                {{-- Thông tin cá nhân --}}
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit text-primary me-2"></i>Cập nhật thông tin cá nhân
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('account.update-profile') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-user text-primary me-1"></i>Họ và tên
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               name="ho_ten" 
                                               class="form-control @error('ho_ten') is-invalid @enderror" 
                                               value="{{ old('ho_ten', $user->ho_ten) }}" 
                                               required>
                                        @error('ho_ten')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-envelope text-primary me-1"></i>Email
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               name="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-phone text-primary me-1"></i>Số điện thoại
                                        </label>
                                        <input type="text" 
                                               name="so_dien_thoai" 
                                               class="form-control @error('so_dien_thoai') is-invalid @enderror" 
                                               value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}" 
                                               placeholder="Nhập số điện thoại">
                                        @error('so_dien_thoai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar text-primary me-1"></i>Ngày tham gia
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="{{ $user->created_at->format('d/m/Y') }}" 
                                               readonly>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i>Đặt lại
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Lịch sử vé --}}
                <div class="tab-pane fade" id="tickets" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-ticket-alt text-primary me-2"></i>Lịch sử đặt vé
                            </h5>
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
                                                <th>Suất chiếu</th>
                                                <th>Ghế</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày đặt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bookings as $booking)
                                                <tr>
                                                    <td><strong>#{{ $booking->id }}</strong></td>
                                                    <td>{{ $booking->suatChieu->phim->ten_phim ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $booking->suatChieu->ngay_chieu ?? 'N/A' }}<br>
                                                        <small class="text-muted">{{ $booking->suatChieu->gio_chieu ?? '' }}</small>
                                                    </td>
                                                    <td>
                                                        @foreach($booking->chiTietVes as $detail)
                                                            <span class="badge bg-secondary">{{ $detail->ghe->so_ghe_ngoi ?? '' }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td><strong class="text-primary">{{ number_format($booking->tong_tien) }}đ</strong></td>
                                                    <td>
                                                        @if($booking->trang_thai == 'da_thanh_toan')
                                                            <span class="badge bg-success">Đã thanh toán</span>
                                                        @elseif($booking->trang_thai == 'cho_thanh_toan')
                                                            <span class="badge bg-warning">Chờ thanh toán</span>
                                                        @else
                                                            <span class="badge bg-danger">Đã hủy</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    {{ $bookings->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Điểm thưởng & Voucher --}}
                <div class="tab-pane fade" id="points" role="tabpanel">
                    <div class="row">
                        {{-- Thẻ điểm --}}
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="card-body text-white text-center p-4">
                                    <i class="fas fa-star fa-3x mb-3"></i>
                                    <h3 class="mb-1">{{ number_format($user->diem) }}</h3>
                                    <p class="mb-0">Điểm thưởng hiện có</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="card-body text-white text-center p-4">
                                    <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                    <h3 class="mb-1">{{ $myVouchers->count() }}</h3>
                                    <p class="mb-0">Voucher của tôi</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="card-body text-white text-center p-4">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <h3 class="mb-1">{{ $lichSuDiem->count() }}</h3>
                                    <p class="mb-0">Giao dịch điểm</p>
                                </div>
                            </div>
                        </div>

                        {{-- Voucher của tôi --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-ticket-alt text-primary me-2"></i>Voucher của tôi
                                        </h5>
                                        <a href="{{ route('account.rewards') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus me-1"></i>Đổi voucher mới
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($myVouchers->isEmpty())
                                        <div class="text-center py-4">
                                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Bạn chưa có voucher nào</p>
                                            <a href="{{ route('account.rewards') }}" class="btn btn-primary">
                                                Đổi điểm lấy voucher
                                            </a>
                                        </div>
                                    @else
                                        <div class="row">
                                            @foreach($myVouchers->take(3) as $userVoucher)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card border h-100">
                                                        <div class="card-body">
                                                            <h6 class="card-title">{{ $userVoucher->voucher->ten }}</h6>
                                                            <p class="text-primary fw-bold mb-2">
                                                                {{ $userVoucher->voucher->mo_ta_gia_tri }}
                                                            </p>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">
                                                                    HSD: {{ $userVoucher->ngay_han->format('d/m/Y') }}
                                                                </small>
                                                                @if($userVoucher->trang_thai == 'chua_su_dung')
                                                                    <span class="badge bg-success">Chưa dùng</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Đã dùng</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($myVouchers->count() > 3)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('account.my-vouchers') }}" class="btn btn-outline-primary">
                                                    Xem tất cả voucher
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Lịch sử điểm --}}
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-history text-primary me-2"></i>Lịch sử điểm gần đây
                                        </h5>
                                        <a href="{{ route('account.point-history') }}" class="btn btn-sm btn-outline-primary">
                                            Xem tất cả
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($lichSuDiem->isEmpty())
                                        <div class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Chưa có lịch sử điểm</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Thời gian</th>
                                                        <th>Nội dung</th>
                                                        <th class="text-end">Điểm</th>
                                                        <th class="text-end">Số dư</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($lichSuDiem->take(5) as $history)
                                                        <tr>
                                                            <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>{{ $history->mo_ta }}</td>
                                                            <td class="text-end">
                                                                @if($history->loai_giao_dich == 'cong')
                                                                    <span class="text-success fw-bold">+{{ number_format($history->so_diem) }}</span>
                                                                @else
                                                                    <span class="text-danger fw-bold">-{{ number_format($history->so_diem) }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">{{ number_format($history->so_du_sau) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Đổi mật khẩu --}}
                <div class="tab-pane fade" id="password" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-key text-primary me-2"></i>Đổi mật khẩu
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('account.change-password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div class="alert alert-info border-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Lưu ý:</strong> Mật khẩu mới phải có ít nhất 6 ký tự
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-lock text-primary me-1"></i>Mật khẩu hiện tại
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" 
                                                   name="current_password" 
                                                   class="form-control @error('current_password') is-invalid @enderror" 
                                                   required>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-key text-primary me-1"></i>Mật khẩu mới
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" 
                                                   name="new_password" 
                                                   class="form-control @error('new_password') is-invalid @enderror" 
                                                   required>
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-check-circle text-primary me-1"></i>Xác nhận mật khẩu mới
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" 
                                                   name="new_password_confirmation" 
                                                   class="form-control" 
                                                   required>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo me-1"></i>Đặt lại
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-key me-1"></i>Đổi mật khẩu
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-tabs-custom {
        border-bottom: 2px solid #dee2e6;
    }
    
    .nav-tabs-custom .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }
    
    .nav-tabs-custom .nav-link:hover {
        color: #667eea;
        border-bottom: 2px solid #667eea;
    }
    
    .nav-tabs-custom .nav-link.active {
        color: #667eea;
        border-bottom: 2px solid #667eea;
        background: transparent;
    }
    
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
</style>
@endpush
@endsection
