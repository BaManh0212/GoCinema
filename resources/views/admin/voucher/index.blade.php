@extends('admin.layouts.admin')

@section('title', 'Quản lý Voucher Vé Phim')

@section('content')
<div class="container-fluid py-4">

    {{-- Header với gradient --}}
    <div class="card shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="card-body text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fw-bold">
                        <i class="fas fa-ticket-alt me-2"></i>Quản lý Voucher Vé Phim
                    </h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-info-circle me-1"></i>
                        Voucher giảm giá vé phim - Có giới hạn số lượng & thời hạn sử dụng
                    </p>
                </div>
                <div>
                    <button class="btn btn-light" onclick="window.location.reload()" title="Tải lại">
                        <i class="fas fa-sync-alt"></i> Tải lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bộ lọc với style đẹp hơn --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-filter text-primary me-2"></i>Bộ lọc tìm kiếm
            </h5>
        </div>
        <div class="card-body bg-light">
            <form method="GET" action="{{ route('admin.voucher.index') }}" class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search text-primary me-1"></i>Tìm kiếm
                    </label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Nhập tên voucher..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-tag text-success me-1"></i>Loại
                    </label>
                    <select name="loai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="phan_tram" {{ request('loai') == 'phan_tram' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="so_tien" {{ request('loai') == 'so_tien' ? 'selected' : '' }}>Số tiền</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-toggle-on text-warning me-1"></i>Trạng thái
                    </label>
                    <select name="kich_hoat" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('kich_hoat') == '1' ? 'selected' : '' }}>Đang bật</option>
                        <option value="0" {{ request('kich_hoat') == '0' ? 'selected' : '' }}>Đang tắt</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-film text-info me-1"></i>Áp dụng cho
                    </label>
                    <select name="ap_dung_cho" class="form-select" disabled>
                        <option value="">Vé phim</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-12">
                    <label class="form-label fw-semibold d-block">&nbsp;</label>
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>Lọc
                        </button>
                        <button type="button" 
                                class="btn btn-outline-secondary flex-fill" 
                                onclick="window.location.href='{{ route('admin.voucher.index') }}'">
                            <i class="fas fa-redo me-1"></i>Đặt lại
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng danh sách với style đẹp --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list text-primary me-2"></i>Danh sách Voucher
                </h5>
                <span class="badge bg-primary">{{ $vouchers->total() }} voucher</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($vouchers->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">Chưa có voucher nào</h5>
                    <p class="text-muted mb-0">Voucher sẽ được quản lý tại đây</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th class="text-start ps-4" style="min-width: 200px;">
                                    <i class="fas fa-ticket-alt text-primary me-1"></i>Tên Voucher
                                </th>
                                <th style="width: 100px;">
                                    <i class="fas fa-tag text-success me-1"></i>Loại
                                </th>
                                <th style="width: 100px;">
                                    <i class="fas fa-toggle-on text-warning me-1"></i>Trạng thái
                                </th>
                                <th style="width: 100px;">
                                    <i class="fas fa-star text-danger me-1"></i>Điểm
                                </th>
                                <th style="width: 150px;">
                                    <i class="fas fa-money-bill-wave text-success me-1"></i>Giá trị
                                </th>
                                <th style="width: 120px;">
                                    <i class="fas fa-box text-info me-1"></i>Số lượng
                                </th>
                                <th style="width: 100px;">
                                    <i class="fas fa-clock text-primary me-1"></i>HSD
                                </th>
                                <th style="width: 80px;">Kích hoạt</th>
                                <th style="width: 180px;">
                                    <i class="fas fa-cog me-1"></i>Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                                <tr>
                                    <td class="text-start ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-ticket-alt text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $voucher->ten }}</div>
                                                <small class="text-muted">ID: #{{ $voucher->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-gradient">
                                            <i class="fas fa-film me-1"></i>Vé phim
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($voucher->conHieuLuc())
                                            <span class="badge bg-success bg-gradient shadow-sm px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Đang bật
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-gradient shadow-sm px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i>Đang tắt
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark bg-gradient shadow-sm px-3 py-2">
                                            <i class="fas fa-star me-1"></i>{{ number_format($voucher->diem_can, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-success" style="font-size: 1.05rem;">
                                            <i class="fas fa-gift me-1"></i>{{ $voucher->mo_ta_gia_tri }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $conLai = $voucher->so_luong_toi_da - $voucher->so_luong_da_dung;
                                            $phanTram = ($voucher->so_luong_toi_da > 0) ? ($conLai / $voucher->so_luong_toi_da * 100) : 0;
                                        @endphp
                                        <div class="d-flex flex-column align-items-center gap-1">
                                            <span class="badge {{ $phanTram > 50 ? 'bg-success' : ($phanTram > 20 ? 'bg-warning text-dark' : 'bg-danger') }} bg-gradient shadow-sm px-3 py-2" style="font-size: 0.85rem;">
                                                <i class="fas fa-boxes me-1"></i>{{ $conLai }}/{{ $voucher->so_luong_toi_da }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-arrow-down me-1"></i>Đã dùng: {{ $voucher->so_luong_da_dung }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($voucher->ngay_ket_thuc)
                                            @php
                                                $ngayConLai = (int) now()->diffInDays($voucher->ngay_ket_thuc, false);
                                            @endphp
                                            @if($ngayConLai > 0)
                                                <span class="badge bg-success bg-gradient shadow-sm px-3 py-2">
                                                    <i class="fas fa-calendar-check me-1"></i>{{ $ngayConLai }} ngày
                                                </span>
                                            @elseif($ngayConLai == 0)
                                                <span class="badge bg-warning text-dark bg-gradient shadow-sm px-3 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Hôm nay
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-gradient shadow-sm px-3 py-2">
                                                    <i class="fas fa-calendar-times me-1"></i>Đã hết
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary bg-gradient shadow-sm px-3 py-2">
                                                <i class="fas fa-infinity me-1"></i>Vô hạn
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input 
                                                class="form-check-input shadow-sm"
                                                type="checkbox"
                                                {{ $voucher->kich_hoat ? 'checked' : '' }}
                                                onchange="toggleStatus({{ $voucher->id }})"
                                                style="cursor: pointer; width: 45px; height: 22px;"
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.voucher.show', $voucher->id) }}" 
                                               class="btn btn-sm btn-info shadow-sm" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Xem chi tiết voucher"
                                               style="min-width: 38px;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.voucher.edit', $voucher->id) }}" 
                                               class="btn btn-sm btn-warning shadow-sm" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Chỉnh sửa voucher"
                                               style="min-width: 38px;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger shadow-sm" 
                                                    onclick="confirmDelete({{ $voucher->id }})" 
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="Xóa voucher"
                                                    style="min-width: 38px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        
        {{-- Phân trang đẹp custom --}}
        @if($vouchers->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            Hiển thị <strong class="text-primary">{{ $vouchers->firstItem() ?? 0 }}</strong> – 
                            <strong class="text-primary">{{ $vouchers->lastItem() ?? 0 }}</strong> 
                            trong tổng số <strong class="text-primary">{{ $vouchers->total() }}</strong> voucher
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Voucher pagination">
                            <ul class="pagination justify-content-end mb-0">
                                {{-- Previous Button --}}
                                @if ($vouchers->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-angle-double-left me-1"></i>Trước
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $vouchers->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-angle-double-left me-1"></i>Trước
                                        </a>
                                    </li>
                                @endif

                                {{-- Page Numbers --}}
                                @php
                                    $start = max($vouchers->currentPage() - 2, 1);
                                    $end = min($start + 4, $vouchers->lastPage());
                                    $start = max($end - 4, 1);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $vouchers->url(1) }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page == $vouchers->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $vouchers->url($page) }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endfor

                                @if($end < $vouchers->lastPage())
                                    @if($end < $vouchers->lastPage() - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $vouchers->url($vouchers->lastPage()) }}">{{ $vouchers->lastPage() }}</a>
                                    </li>
                                @endif

                                {{-- Next Button --}}
                                @if ($vouchers->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $vouchers->nextPageUrl() }}" rel="next">
                                            Sau<i class="fas fa-angle-double-right ms-1"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            Sau<i class="fas fa-angle-double-right ms-1"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Form ẩn --}}
<form id="deleteForm" method="POST" style="display: none;">@csrf @method('DELETE')</form>
<form id="toggleForm" method="POST" style="display: none;">@csrf</form>

@push('styles')
<style>
    /* Custom Styling */
    .avatar-sm {
        width: 45px;
        height: 45px;
    }
    
    /* Hover effects cho các hàng */
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }
    
    /* Button action với hiệu ứng đẹp */
    .btn {
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    .btn-info {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }
    
    .btn-info:hover {
        background: linear-gradient(135deg, #138496, #117a8b);
        color: white;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #000;
    }
    
    .btn-warning:hover {
        background: linear-gradient(135deg, #e0a800, #d39e00);
        color: #000;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, #c82333, #bd2130);
        color: white;
    }
    
    /* Badge với gradient */
    .badge.bg-gradient {
        background: linear-gradient(135deg, var(--bs-badge-color, currentColor), var(--bs-badge-bg));
    }
    
    /* Switch toggle đẹp hơn */
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
    }
    
    /* Card shadow mượt */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
    }
    
    /* Alert với animation */
    .alert {
        animation: slideDown 0.4s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Tooltip custom */
    .tooltip-inner {
        background-color: #2c3e50;
        font-size: 0.85rem;
        padding: 8px 12px;
        border-radius: 6px;
    }
    
    /* Table responsive với scroll mượt */
    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f1f5f9;
    }
    
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    /* Custom Pagination Style */
    .pagination {
        gap: 5px;
    }
    
    .pagination .page-link {
        border: 1px solid #dee2e6;
        border-radius: 8px !important;
        color: #495057;
        padding: 8px 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 0;
    }
    
    .pagination .page-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        font-weight: 700;
    }
    
    .pagination .page-item.disabled .page-link {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
        cursor: not-allowed;
    }
    
    .pagination .page-link i {
        font-size: 0.85rem;
    }
</style>
@endpush

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function toggleStatus(id) {
    if (confirm('🔄 Bạn có chắc chắn muốn thay đổi trạng thái voucher này?')) {
        const form = document.getElementById('toggleForm');
        form.action = `/admin/voucher/${id}/toggle-status`;
        form.submit();
    } else {
        // Reload để reset switch về trạng thái cũ
        window.location.reload();
    }
}

function confirmDelete(id) {
    if (confirm('⚠️ XÓA VOUCHER\n\nBạn có chắc chắn muốn xóa voucher này?\n\n❌ Lưu ý: Không thể xóa nếu đã có người dùng đổi voucher!')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/voucher/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
