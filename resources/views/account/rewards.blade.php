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
                    <h2 class="mb-3"><i class="fas fa-gift me-2"></i>Đổi điểm lấy ưu đãi</h2>
                    <p class="lead mb-0">Bạn có <strong>{{ number_format($user->diem) }} điểm</strong> - Quy đổi: 1000đ = 1 điểm</p>
                </div>
            </div>

            <!-- Danh sách combo -->
            <h4 class="mb-4">Combo có thể đổi</h4>
            
            @if($combos->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Hiện tại chưa có combo nào để đổi điểm.
                </div>
            @else
                <div class="row">
                    @foreach($combos as $combo)
                        @php
                            $diemCanThiet = ceil($combo->gia / 1000);
                            $duDiem = $user->diem >= $diemCanThiet;
                        @endphp
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 {{ $duDiem ? '' : 'opacity-75' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title">
                                            <i class="fas fa-box text-primary me-2"></i>{{ $combo->ten }}
                                        </h5>
                                        <span class="badge {{ $duDiem ? 'bg-success' : 'bg-secondary' }} fs-6">
                                            {{ number_format($diemCanThiet) }} điểm
                                        </span>
                                    </div>
                                    
                                    @if($combo->mo_ta)
                                        <p class="text-muted">{{ $combo->mo_ta }}</p>
                                    @endif
                                    
                                    <!-- Chi tiết combo -->
                                    @if($combo->chiTiet && $combo->chiTiet->count() > 0)
                                        <div class="mb-3">
                                            <strong>Bao gồm:</strong>
                                            <ul class="list-unstyled ms-3 mt-2">
                                                @foreach($combo->chiTiet as $ct)
                                                    <li>
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        {{ $ct->so_luong }}x {{ $ct->sanPham->ten ?? 'Sản phẩm' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Giá gốc:</small>
                                            <div class="text-decoration-line-through">{{ number_format($combo->gia) }}đ</div>
                                        </div>
                                        
                                        @if($duDiem)
                                            <form action="{{ route('account.redeem-combo', $combo->id) }}" method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc muốn đổi {{ number_format($diemCanThiet) }} điểm lấy combo này?')">
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
                    <h5><i class="fas fa-question-circle text-info me-2"></i>Cách tích điểm</h5>
                    <ul class="mb-0">
                        <li>Mỗi lần đặt vé xem phim, bạn sẽ nhận được điểm thưởng</li>
                        <li>Điểm được quy đổi: 1000đ chi tiêu = 1 điểm tích lũy</li>
                        <li>Sử dụng điểm để đổi lấy combo ưu đãi tại rạp</li>
                        <li>Điểm tích lũy không có hạn sử dụng</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
