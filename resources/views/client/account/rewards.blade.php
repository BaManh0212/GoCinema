@extends('client.layouts.app')

@section('title', 'Đổi điểm thưởng')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5 class="card-title">{{ $user->ho_ten }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="badge bg-primary fs-6 mb-3">
                        <i class="fas fa-star"></i> {{ number_format($user->diem) }} điểm
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('account.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="{{ route('account.rewards') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-gift me-2"></i> Đổi điểm thưởng
                    </a>
                    <a href="{{ route('account.my-vouchers') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-ticket-alt me-2"></i> Voucher của tôi
                    </a>
                    <a href="{{ route('account.point-history') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i> Lịch sử điểm
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Thông báo -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Header -->
            <div class="card mb-4 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center py-4">
                    <h2 class="mb-3"><i class="fas fa-ticket-alt me-2"></i>Đổi điểm lấy voucher VÉ PHIM</h2>
                    <p class="lead mb-0">Bạn có <strong>{{ number_format($user->diem) }} điểm</strong> - Sử dụng điểm để đổi voucher giảm giá vé phim</p>
                    <small class="text-white-50">Voucher chỉ áp dụng cho vé xem phim, có hiệu lực 30 ngày từ ngày đổi</small>
                </div>
            </div>

            <!-- Danh sách voucher -->
            <h4 class="mb-4">
                <i class="fas fa-film me-2"></i>Voucher giảm giá vé phim
                <span class="badge bg-info ms-2">Chỉ dành cho vé</span>
            </h4>
            
            @if($vouchers->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Hiện tại chưa có voucher nào để đổi điểm.
                </div>
            @else
                <div class="row">
                    @foreach($vouchers as $voucher)
                        @php
                            $duDiem = $user->diem >= $voucher->diem_can;
                            $conVoucher = $voucher->conVoucherDeDoi();
                            $coTheDoiDuoc = $duDiem && $conVoucher;
                        @endphp
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 {{ $coTheDoiDuoc ? '' : 'opacity-75' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title">
                                            <i class="fas fa-ticket-alt text-primary me-2"></i>{{ $voucher->ten }}
                                        </h5>
                                        <span class="badge {{ $duDiem ? 'bg-success' : 'bg-secondary' }} fs-6">
                                            {{ number_format($voucher->diem_can) }} điểm
                                        </span>
                                    </div>
                                    
                                    @if($voucher->mo_ta)
                                        <p class="text-muted">{{ $voucher->mo_ta }}</p>
                                    @endif
                                    
                                    <!-- Chi tiết voucher -->
                                    <div class="mb-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="text-muted">Loại voucher:</small>
                                                <div class="fw-bold">
                                                    @if($voucher->loai == 'phan_tram')
                                                        <i class="fas fa-percent text-success"></i> Giảm theo %
                                                    @else
                                                        <i class="fas fa-money-bill text-success"></i> Giảm theo tiền
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Giá trị:</small>
                                                <div class="fw-bold text-success">
                                                    {{ $voucher->moTaGiaTri }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Số lượng còn lại -->
                                    <div class="mb-3">
                                        @php
                                            $conLai = $voucher->so_luong_toi_da - $voucher->so_luong_da_dung;
                                            $phanTram = ($voucher->so_luong_toi_da > 0) ? ($conLai / $voucher->so_luong_toi_da * 100) : 0;
                                        @endphp
                                        <small class="text-muted">Số lượng còn lại:</small>
                                        <div>
                                            <span class="badge {{ $phanTram > 50 ? 'bg-success' : ($phanTram > 20 ? 'bg-warning' : 'bg-danger') }} fs-6">
                                                {{ $conLai }}/{{ $voucher->so_luong_toi_da }}
                                            </span>
                                            @if($conLai <= 10 && $conLai > 0)
                                                <span class="text-danger ms-2"><i class="fas fa-fire"></i> Sắp hết!</span>
                                            @elseif($conLai == 0)
                                                <span class="text-danger ms-2"><i class="fas fa-times-circle"></i> Đã hết!</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Hiển thị áp dụng cho VÉ -->
                                    <div class="mb-3">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-film me-1"></i> Chỉ dành cho VÉ PHIM
                                        </span>
                                    </div>

                                    <!-- HSD = 30 ngày từ ngày đổi -->
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>Hiệu lực: 
                                            <span class="text-info fw-bold">30 ngày</span> kể từ ngày đổi
                                        </small>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Điểm cần thiết:</small>
                                            <div class="fw-bold text-primary">{{ number_format($voucher->diem_can) }} điểm</div>
                                        </div>
                                        
                                        @if($coTheDoiDuoc)
                                            <form action="{{ route('account.redeem-voucher', $voucher->id) }}" method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc muốn đổi {{ number_format($voucher->diem_can) }} điểm lấy voucher này?')">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-exchange-alt me-2"></i>Đổi ngay
                                                </button>
                                            </form>
                                        @elseif(!$conVoucher)
                                            <button class="btn btn-danger" disabled>
                                                <i class="fas fa-ban me-2"></i>Đã hết
                                            </button>
                                        @else
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-lock me-2"></i>Chưa đủ điểm
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Hướng dẫn tích điểm -->
            <div class="card bg-light mt-4">
                <div class="card-body">
                    <h5><i class="fas fa-question-circle text-info me-2"></i>Cách tích điểm & sử dụng voucher vé phim</h5>
                    <ul class="mb-0">
                        <li>Mỗi lần đặt vé xem phim, bạn sẽ nhận được điểm thưởng</li>
                        <li>Điểm được quy đổi: 1000đ chi tiêu = 1 điểm tích lũy</li>
                        <li>Sử dụng điểm để đổi lấy voucher giảm giá <strong class="text-danger">VÉ PHIM</strong></li>
                        <li><strong class="text-primary">Voucher có hiệu lực 30 ngày</strong> kể từ ngày đổi</li>
                        <li>Voucher sẽ được lưu trong mục "Voucher của tôi" sau khi đổi thành công</li>
                        <li>Voucher <strong>CHỈ ÁP DỤNG CHO VÉ XEM PHIM</strong>, không áp dụng cho bắp nước hoặc sản phẩm khác</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
