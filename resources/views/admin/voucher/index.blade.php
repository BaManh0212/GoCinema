@extends('layouts.app')

@section('title', 'Ưu đãi đổi điểm')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-gift text-primary"></i> Ưu đãi đổi điểm
        </h2>
        <div>
            <button class="btn btn-outline-secondary me-2" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Tải lại
            </button>
            <a href="{{ route('admin.voucher.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm ưu đãi
            </a>
        </div>
    </div>

    <!-- Thông báo -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.voucher.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên voucher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Loại</label>
                    <select name="loai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="phan_tram" {{ request('loai') == 'phan_tram' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="so_tien" {{ request('loai') == 'so_tien' ? 'selected' : '' }}>Số tiền</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="kich_hoat" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('kich_hoat') == '1' ? 'selected' : '' }}>Đang bật</option>
                        <option value="0" {{ request('kich_hoat') == '0' ? 'selected' : '' }}>Đang tắt</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Áp dụng cho</label>
                    <select name="ap_dung_cho" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="ve" {{ request('ap_dung_cho') == 've' ? 'selected' : '' }}>Vé</option>
                        <option value="san_pham" {{ request('ap_dung_cho') == 'san_pham' ? 'selected' : '' }}>Sản phẩm</option>
                        <option value="tat_ca" {{ request('ap_dung_cho') == 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Lọc
                    </button>
                    <a href="{{ route('admin.voucher.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Đặt lại
                    </a>
                    <a href="{{ route('admin.voucher.statistics') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Thống kê
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách voucher -->
    <div class="card">
        <div class="card-body">
            @if($vouchers->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có voucher nào</p>
                    <a href="{{ route('admin.voucher.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tạo voucher đầu tiên
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Loại</th>
                                <th>Trạng thái</th>
                                <th>Điểm cần</th>
                                <th>Giá trị voucher</th>
                                <th>HSD (ngày)</th>
                                <th>Kích hoạt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                                <tr>
                                    <td>
                                        <strong>{{ $voucher->ten }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $voucher->mo_ta_ap_dung }}</span>
                                    </td>
                                    <td>
                                        @if($voucher->conHieuLuc())
                                            <span class="badge bg-success">Đang bật</span>
                                        @else
                                            <span class="badge bg-danger">Đang tắt</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($voucher->diem_can) }}</strong>
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ $voucher->mo_ta_gia_tri }}</strong>
                                    </td>
                                    <td>
                                        @if($voucher->ngay_ket_thuc)
                                            {{ $voucher->ngay_ket_thuc->diffInDays(now()) }} ngày
                                        @else
                                            ∞
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox" 
                                                {{ $voucher->kich_hoat ? 'checked' : '' }}
                                                onchange="toggleStatus({{ $voucher->id }})"
                                                style="cursor: pointer; width: 48px; height: 24px;"
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.voucher.edit', $voucher->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $voucher->id }})"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Hiển thị {{ $vouchers->firstItem() ?? 0 }} - {{ $vouchers->lastItem() ?? 0 }} 
                        trong tổng số {{ $vouchers->total() }} voucher
                    </div>
                    <div>
                        {{ $vouchers->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Form xóa ẩn -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Form toggle status ẩn -->
<form id="toggleForm" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
function toggleStatus(id) {
    if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái voucher này?')) {
        const form = document.getElementById('toggleForm');
        form.action = `/admin/voucher/${id}/toggle-status`;
        form.submit();
    } else {
        // Khôi phục lại trạng thái checkbox
        window.location.reload();
    }
}

function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa voucher này?\n\nLưu ý: Không thể xóa nếu đã có người dùng đổi voucher!')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/voucher/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
