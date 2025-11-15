@extends('client.layouts.app')

@section('title', 'Voucher của tôi')

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
                    <a href="{{ route('account.rewards') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-gift me-2"></i> Đổi điểm thưởng
                    </a>
                    <a href="{{ route('account.my-vouchers') }}" class="list-group-item list-group-item-action active">
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
            <div class="card mb-4 bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white py-4">
                    <h2 class="mb-2"><i class="fas fa-ticket-alt me-2"></i>Voucher vé phim của tôi</h2>
                    <p class="mb-0">Quản lý và sử dụng các voucher giảm giá vé đã đổi</p>
                    <small class="text-white-50">Voucher có hiệu lực 30 ngày kể từ ngày đổi</small>
                </div>
            </div>

            @if($vouchers->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Bạn chưa có voucher nào. 
                    <a href="{{ route('account.rewards') }}" class="alert-link">Đổi điểm ngay</a> để nhận voucher ưu đãi!
                </div>
            @else
                <div class="row">
                    @foreach($vouchers as $voucherNguoiDung)
                        @php
                            $voucher = $voucherNguoiDung->voucher;
                            $conHieuLuc = $voucherNguoiDung->conSuDungDuoc();
                            $trangThai = $voucherNguoiDung->trang_thai;
                        @endphp
                        
                        <div class="col-md-12 mb-3">
                            <div class="card {{ !$conHieuLuc ? 'opacity-75' : '' }}">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <!-- Voucher Info -->
                                        <div class="col-md-7">
                                            <div class="d-flex align-items-start mb-2">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-ticket-alt fa-lg"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="mb-1">{{ $voucher->ten }}</h5>
                                                    @if($voucher->mo_ta)
                                                        <p class="text-muted small mb-1">{{ $voucher->mo_ta }}</p>
                                                    @endif
                                                    <div class="mt-2">
                                                        <span class="badge bg-success me-1">{{ $voucher->moTaGiaTri }}</span>
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-film me-1"></i> Chỉ dành cho VÉ
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status & Actions -->
                                        <div class="col-md-5">
                                            <div class="text-end">
                                                <!-- Trạng thái -->
                                                @if($trangThai == 'chua_su_dung')
                                                    @if($conHieuLuc)
                                                        <span class="badge bg-success fs-6 mb-2">
                                                            <i class="fas fa-check-circle"></i> Có thể sử dụng
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger fs-6 mb-2">
                                                            <i class="fas fa-times-circle"></i> Đã hết hạn
                                                        </span>
                                                    @endif
                                                @elseif($trangThai == 'da_su_dung')
                                                    <span class="badge bg-secondary fs-6 mb-2">
                                                        <i class="fas fa-check"></i> Đã sử dụng
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning fs-6 mb-2">
                                                        <i class="fas fa-ban"></i> Đã hủy
                                                    </span>
                                                @endif

                                                <!-- Thông tin thời gian -->
                                                <div class="small text-muted mb-2">
                                                    <div>
                                                        <i class="fas fa-calendar-plus"></i> Đổi: {{ $voucherNguoiDung->ngay_doi->format('d/m/Y H:i') }}
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-calendar-times"></i> HSD: {{ $voucherNguoiDung->ngay_han->format('d/m/Y') }}
                                                        <span class="badge bg-info ms-1">30 ngày</span>
                                                    </div>
                                                    @if($conHieuLuc && $trangThai == 'chua_su_dung')
                                                        @php
                                                            $ngayConLai = now()->diffInDays($voucherNguoiDung->ngay_han, false);
                                                        @endphp
                                                        @if($ngayConLai <= 7 && $ngayConLai > 0)
                                                            <div class="text-warning fw-bold">
                                                                <i class="fas fa-exclamation-triangle"></i> Còn {{ $ngayConLai }} ngày
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>

                                                <!-- Mã voucher -->
                                                @if($conHieuLuc && $trangThai == 'chua_su_dung')
                                                    <div class="mt-2">
                                                        <small class="text-muted">Mã voucher:</small>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="form-control text-center fw-bold" 
                                                                   value="{{ $voucherNguoiDung->ma_code ?? 'VC' . str_pad($voucherNguoiDung->id, 6, '0', STR_PAD_LEFT) }}" 
                                                                   readonly 
                                                                   id="code-{{ $voucherNguoiDung->id }}">
                                                            <button class="btn btn-outline-secondary" type="button" 
                                                                    onclick="copyCode({{ $voucherNguoiDung->id }})">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                        <small class="text-info">Sử dụng mã này tại quầy để nhận ưu đãi</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $vouchers->links() }}
                </div>
            @endif

            <!-- Hướng dẫn -->
            <div class="card bg-light mt-4">
                <div class="card-body">
                    <h5><i class="fas fa-question-circle text-info me-2"></i>Hướng dẫn sử dụng voucher vé phim</h5>
                    <ul class="mb-0">
                        <li>Voucher sẽ được lưu sau khi bạn đổi điểm thành công</li>
                        <li><strong class="text-primary">Voucher có hiệu lực 30 ngày</strong> kể từ ngày đổi</li>
                        <li>Mỗi voucher có mã riêng, bạn có thể sao chép để sử dụng</li>
                        <li>Voucher <strong class="text-danger">CHỈ ÁP DỤNG CHO VÉ PHIM</strong>, không áp dụng cho bắp nước</li>
                        <li>Xuất trình mã voucher khi đặt vé online hoặc tại quầy để được giảm giá</li>
                        <li>Kiểm tra HSD trước khi sử dụng - voucher hết hạn không thể dùng lại</li>
                        <li>Voucher đã sử dụng hoặc hết hạn không thể dùng lại</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyCode(id) {
    const input = document.getElementById('code-' + id);
    input.select();
    document.execCommand('copy');
    
    // Show notification
    alert('Đã sao chép mã voucher!');
}
</script>
@endsection
