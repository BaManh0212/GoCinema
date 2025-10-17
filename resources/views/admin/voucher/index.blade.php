@extends('admin.layouts.admin')

@section('title', '🎁 Ưu đãi đổi điểm')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">
            <i class="fas fa-gift me-2"></i> Ưu đãi đổi điểm
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Tải lại
            </button>
            <a href="{{ route('admin.voucher.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Thêm ưu đãi
            </a>
        </div>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bộ lọc tìm kiếm --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.voucher.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">🔍 Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập tên voucher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">📂 Loại</label>
                    <select name="loai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="phan_tram" {{ request('loai') == 'phan_tram' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="so_tien" {{ request('loai') == 'so_tien' ? 'selected' : '' }}>Số tiền</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">⚙️ Trạng thái</label>
                    <select name="kich_hoat" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('kich_hoat') == '1' ? 'selected' : '' }}>Đang bật</option>
                        <option value="0" {{ request('kich_hoat') == '0' ? 'selected' : '' }}>Đang tắt</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">🎟️ Áp dụng cho</label>
                    <select name="ap_dung_cho" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="ve" {{ request('ap_dung_cho') == 've' ? 'selected' : '' }}>Vé</option>
                        <option value="san_pham" {{ request('ap_dung_cho') == 'san_pham' ? 'selected' : '' }}>Sản phẩm</option>
                        <option value="tat_ca" {{ request('ap_dung_cho') == 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-search me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.voucher.index') }}" class="btn btn-outline-secondary flex-grow-1">
                        <i class="fas fa-undo me-1"></i> Đặt lại
                    </a>
                    <a href="{{ route('admin.voucher.statistics') }}" class="btn btn-info flex-grow-1 text-white">
                        <i class="fas fa-chart-bar me-1"></i> Thống kê
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng danh sách --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if($vouchers->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Hiện chưa có voucher nào.</p>
                    <a href="{{ route('admin.voucher.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Tạo voucher đầu tiên
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center align-middle">
                            <tr>
                                <th>Tên ưu đãi</th>
                                <th>Loại</th>
                                <th>Trạng thái</th>
                                <th>Điểm cần</th>
                                <th>Giá trị</th>
                                <th>Hạn sử dụng</th>
                                <th>Kích hoạt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($vouchers as $voucher)
                                <tr>
                                    <td class="text-start fw-semibold">{{ $voucher->ten }}</td>
                                    <td><span class="badge bg-info">{{ $voucher->mo_ta_ap_dung }}</span></td>
                                    <td>
                                        @if($voucher->conHieuLuc())
                                            <span class="badge bg-success">Đang bật</span>
                                        @else
                                            <span class="badge bg-danger">Đang tắt</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($voucher->diem_can) }}</td>
                                    <td class="text-primary fw-semibold">{{ $voucher->mo_ta_gia_tri }}</td>
                                    <td>
                                        @if($voucher->ngay_ket_thuc)
                                            <span class="text-muted">
                                                Còn {{ $voucher->ngay_ket_thuc->diffInDays(now()) }} ngày
                                            </span>
                                        @else
                                            ∞
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input 
                                                class="form-check-input"
                                                type="checkbox"
                                                {{ $voucher->kich_hoat ? 'checked' : '' }}
                                                onchange="toggleStatus({{ $voucher->id }})"
                                                style="cursor:pointer; width:50px; height:25px;"
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.voucher.edit', $voucher->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete({{ $voucher->id }})">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <small class="text-muted">
                        Hiển thị {{ $vouchers->firstItem() ?? 0 }}–{{ $vouchers->lastItem() ?? 0 }} /
                        {{ $vouchers->total() }} voucher
                    </small>
                    <div>{{ $vouchers->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Form ẩn --}}
<form id="deleteForm" method="POST" style="display: none;">@csrf @method('DELETE')</form>
<form id="toggleForm" method="POST" style="display: none;">@csrf</form>

@push('scripts')
<script>
function toggleStatus(id) {
    if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái voucher này?')) {
        const form = document.getElementById('toggleForm');
        form.action = `/admin/voucher/${id}/toggle-status`;
        form.submit();
    } else {
        window.location.reload();
    }
}

function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa voucher này?\n\n⚠️ Lưu ý: Không thể xóa nếu đã có người dùng đổi voucher!')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/voucher/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
