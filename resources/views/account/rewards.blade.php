@extends('layouts.app')

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
                    <h2 class="mb-3"><i class="fas fa-gift me-2"></i>Đổi điểm lấy voucher</h2>
                    <p class="lead mb-0">Bạn có <strong>{{ number_format($user->diem) }} điểm</strong> - Sử dụng điểm để đổi voucher ưu đãi</p>
                </div>
            </div>

            <!-- Danh sách voucher -->
            <h4 class="mb-4">Voucher có thể đổi</h4>
            
            @if($vouchers->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Hiện tại chưa có voucher nào để đổi điểm.
                </div>
            @else
                <div class="row">
                    @foreach($vouchers as $voucher)
                        @php
                            $duDiem = $user->diem >= $voucher->diem_can;
                        @endphp
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 {{ $duDiem ? '' : 'opacity-75' }}">
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
                                                    @if($voucher->loai == 'giam_gia')
                                                        <i class="fas fa-percent text-success"></i> Giảm giá
                                                    @elseif($voucher->loai == 'mien_phi')
                                                        <i class="fas fa-gift text-info"></i> Miễn phí
                                                    @else
                                                        <i class="fas fa-tag text-warning"></i> Khác
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

                                    @if($voucher->ap_dung_cho)
                                        <div class="mb-3">
                                            <small class="text-muted">Áp dụng cho:</small>
                                            <div>
                                                <span class="badge bg-info">
                                                    {{ $voucher->moTaApDung }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($voucher->ngay_ket_thuc)
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>Hiệu lực đến: 
                                                <span class="text-danger">{{ $voucher->ngay_ket_thuc->format('d/m/Y') }}</span>
                                            </small>
                                        </div>
                                    @endif
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Điểm cần thiết:</small>
                                            <div class="fw-bold text-primary">{{ number_format($voucher->diem_can) }} điểm</div>
                                        </div>
                                        
                                        @if($duDiem)
                                            <form action="{{ route('account.redeem-voucher', $voucher->id) }}" method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc muốn đổi {{ number_format($voucher->diem_can) }} điểm lấy voucher này?')">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-exchange-alt me-2"></i>Đổi ngay
                                                </button>
                                            </form>
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
                    <h5><i class="fas fa-question-circle text-info me-2"></i>Cách tích điểm & sử dụng voucher</h5>
                    <ul class="mb-0">
                        <li>Mỗi lần đặt vé xem phim, bạn sẽ nhận được điểm thưởng</li>
                        <li>Điểm được quy đổi: 1000đ chi tiêu = 1 điểm tích lũy</li>
                        <li>Sử dụng điểm để đổi lấy voucher ưu đãi</li>
                        <li>Voucher sẽ được lưu trong mục "Voucher của tôi" sau khi đổi thành công</li>
                        <li>Kiểm tra thời hạn sử dụng voucher trước khi đổi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
