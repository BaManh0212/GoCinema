@extends('admin.layouts.admin')

@section('title', 'Chi tiết mã giảm giá')

@section('content')
<div class="container mt-4">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-gradient mb-0">
            🎟️ Chi Tiết Mã Giảm Giá
        </h2>
        <a href="{{ route('admin.ma_giam_gia.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ===== CARD CHI TIẾT ===== --}}
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-header bg-gradient p-3 text-white fw-semibold">
            <i class="bi bi-info-circle-fill me-2"></i> Thông tin chi tiết
        </div>

        <div class="card-body px-5 py-4 bg-light">
            <div class="row g-4">

                {{-- Mã giảm giá --}}
                <div class="col-md-6">
                    <p class="text-muted mb-1">Mã giảm giá</p>
                    <h4 class="fw-bold text-primary">{{ $maGiamGia->ma }}</h4>
                </div>

                {{-- Loại giảm giá --}}
                <div class="col-md-6">
                    <p class="text-muted mb-1">Loại giảm giá</p>
                    <span class="badge {{ $maGiamGia->loai === 'phan_tram' ? 'bg-info text-dark' : 'bg-success' }} fs-6 px-3 py-2 rounded-pill shadow-sm">
                        {{ $maGiamGia->loai === 'phan_tram' ? 'Giảm theo %' : 'Giảm theo số tiền' }}
                    </span>
                </div>

                {{-- Giá trị giảm --}}
                <div class="col-md-4">
                    <p class="text-muted mb-1">Giá trị giảm</p>
                    <h5 class="fw-semibold text-dark">
                        {{ $maGiamGia->loai === 'phan_tram' ? $maGiamGia->gia_tri.'%' : number_format($maGiamGia->gia_tri).'đ' }}
                    </h5>
                </div>

                {{-- Giảm tối đa --}}
                @if($maGiamGia->loai === 'phan_tram' && $maGiamGia->giam_toi_da)
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Giảm tối đa</p>
                        <h5 class="fw-semibold text-dark">{{ number_format($maGiamGia->giam_toi_da) }}đ</h5>
                    </div>
                @endif

                {{-- Áp dụng cho --}}
                <div class="col-md-4">
                    <p class="text-muted mb-1">Áp dụng cho</p>
                    <h5 class="fw-semibold text-secondary">
                        @switch($maGiamGia->ap_dung_cho)
                            @case('ve') Vé @break
                            @case('san_pham') Sản phẩm @break
                            @default Tất cả
                        @endswitch
                    </h5>
                </div>

                {{-- Số lượng --}}
                <div class="col-md-4">
                    <p class="text-muted mb-1">Số lượng</p>
                    <h5 class="fw-semibold">{{ $maGiamGia->so_luong }}</h5>
                </div>

                {{-- Ngày bắt đầu --}}
                <div class="col-md-4">
                    <p class="text-muted mb-1">Ngày bắt đầu</p>
                    <h5 class="fw-semibold">{{ \Carbon\Carbon::parse($maGiamGia->ngay_bat_dau)->format('d/m/Y') }}</h5>
                </div>

                {{-- Ngày kết thúc --}}
                <div class="col-md-4">
                    <p class="text-muted mb-1">Ngày kết thúc</p>
                    <h5 class="fw-semibold">{{ \Carbon\Carbon::parse($maGiamGia->ngay_ket_thuc)->format('d/m/Y') }}</h5>
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-4">
                    <p class="text-muted mb-1">Trạng thái</p>
                    @php
                        $today = now();
                        if ($maGiamGia->ngay_bat_dau > $today) {
                            $status = ['Sắp bắt đầu', 'bg-secondary'];
                        } elseif ($maGiamGia->ngay_ket_thuc < $today) {
                            $status = ['Hết hạn', 'bg-danger'];
                        } else {
                            $status = ['Đang hoạt động', 'bg-success'];
                        }
                    @endphp
                    <span class="badge {{ $status[1] }} fs-6 px-3 py-2 rounded-pill shadow-sm">{{ $status[0] }}</span>
                </div>

                {{-- Ngày tạo và cập nhật --}}
                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-1">Ngày tạo</p>
                    <h6 class="fw-semibold text-dark">{{ $maGiamGia->created_at->format('d/m/Y H:i') }}</h6>
                </div>

                <div class="col-md-6 mt-3">
                    <p class="text-muted mb-1">Cập nhật gần nhất</p>
                    <h6 class="fw-semibold text-dark">{{ $maGiamGia->updated_at->format('d/m/Y H:i') }}</h6>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== NÚT HÀNH ĐỘNG ===== --}}
    <div class="mt-4 text-end">
        <a href="{{ route('admin.ma_giam_gia.edit', $maGiamGia->id) }}" 
           class="btn btn-warning rounded-pill px-4 me-2 shadow-sm">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa
        </a>
        <a href="{{ route('admin.ma_giam_gia.index') }}" 
           class="btn btn-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
    </div>
</div>

{{-- ===== CSS TÙY CHỈNH ===== --}}
@push('styles')
<style>
    .text-gradient {
        background: linear-gradient(90deg, #007bff, #6610f2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .bg-gradient {
        background: linear-gradient(90deg, #0d6efd, #6610f2);
    }
    .card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
</style>
@endpush
@endsection
