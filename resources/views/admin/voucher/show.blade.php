@extends('admin.layouts.admin')

@section('title', 'Chi tiết voucher')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            🎁 Chi tiết ưu đãi: {{ $voucher->ten }}
        </h2>
        <a href="{{ route('admin.voucher.index') }}" class="btn btn-secondary">
            ⬅ Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <!-- Thông tin chính -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    Thông tin voucher
                </div>
                <div class="card-body">
                    <p><strong>Loại:</strong> 
                        <span class="badge bg-info">{{ $voucher->mo_ta_ap_dung }}</span>
                    </p>
                    <p><strong>Giá trị:</strong> 
                        <span class="text-primary">{{ $voucher->mo_ta_gia_tri }}</span>
                    </p>
                    <p><strong>Áp dụng cho:</strong>
                        {{ ucfirst(str_replace('_', ' ', $voucher->ap_dung_cho)) }}
                    </p>
                    <p><strong>Số điểm cần:</strong> {{ number_format($voucher->diem_can) }}</p>
                    <p><strong>Số lần sử dụng:</strong> {{ $voucher->so_lan_su_dung }}</p>
                    <p><strong>Giá trị đơn hàng tối thiểu:</strong> 
                        {{ $voucher->gia_tri_don_hang_toi_thieu ? number_format($voucher->gia_tri_don_hang_toi_thieu) . ' đ' : 'Không có' }}
                    </p>
                    <p><strong>Ngày bắt đầu:</strong> 
                        {{ $voucher->ngay_bat_dau ? $voucher->ngay_bat_dau->format('d/m/Y') : 'Không giới hạn' }}
                    </p>
                    <p><strong>Ngày kết thúc:</strong> 
                        {{ $voucher->ngay_ket_thuc ? $voucher->ngay_ket_thuc->format('d/m/Y') : 'Không giới hạn' }}
                    </p>
                    <p><strong>Trạng thái:</strong>
                        @if($voucher->kich_hoat)
                            <span class="badge bg-success">Đang kích hoạt</span>
                        @else
                            <span class="badge bg-danger">Đang tắt</span>
                        @endif
                    </p>
                    <p><strong>Mô tả:</strong> {{ $voucher->mo_ta ?? 'Không có mô tả' }}</p>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white fw-bold">
                    📊 Thống kê sử dụng
                </div>
                <div class="card-body">
                    <p><strong>Tổng số lượt đổi:</strong> {{ $stats->so_luot_doi ?? 0 }}</p>
                    <p><strong>Số người dùng đã đổi:</strong> {{ $stats->so_nguoi_da_doi ?? 0 }}</p>
                    <p><strong>Tổng điểm đã đổi:</strong> {{ number_format($stats->tong_diem_da_doi ?? 0) }}</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning fw-bold">
                    🧍‍♂️ Người dùng gần đây
                </div>
                <div class="card-body">
                    @if($voucher->nguoiDungDaDoi->isEmpty())
                        <p class="text-muted">Chưa có ai đổi voucher này.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($voucher->nguoiDungDaDoi as $nguoi)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $nguoi->ten ?? 'Người dùng #'.$nguoi->id }}</span>
                                    <small class="text-muted">
                                        {{ $nguoi->pivot->created_at->diffForHumans() }}
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
