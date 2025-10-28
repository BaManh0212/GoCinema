@extends('admin.layouts.admin')

@section('title', 'Quản lý Voucher Vé Phim')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ====== TIÊU ĐỀ & NÚT HÀNH ĐỘNG ====== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                🎟️ Quản lý Voucher Vé Phim
            </h2>
            <small class="text-muted">Xem, lọc và quản lý các voucher đổi điểm</small>
        </div>
        <div>
            <a href="{{ route('admin.voucher.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm Voucher
            </a>
        </div>
    </div>

    {{-- ====== THÔNG BÁO ====== --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ====== BỘ LỌC ====== --}}
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.voucher.index') }}" class="row g-3 align-items-center">
                <div class="col-lg-3 col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên voucher"
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="loai" class="form-select">
                        <option value="">-- Loại --</option>
                        <option value="phan_tram" {{ request('loai') == 'phan_tram' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="so_tien" {{ request('loai') == 'so_tien' ? 'selected' : '' }}>Số tiền</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="kich_hoat" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="1" {{ request('kich_hoat') == '1' ? 'selected' : '' }}>Đang bật</option>
                        <option value="0" {{ request('kich_hoat') == '0' ? 'selected' : '' }}>Đang tắt</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="ap_dung_cho" class="form-select" disabled>
                        <option value="">Vé phim</option>
                    </select>
                </div>
                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.voucher.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                        <i class="bi bi-arrow-repeat"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ====== DANH SÁCH VOUCHER ====== --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-semibold mb-0 text-gradient">
                <i class="bi bi-ticket-detailed"></i> Danh sách Voucher
            </h5>
            <span class="badge bg-primary px-3 py-2 shadow-sm">{{ $vouchers->total() }} voucher</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th class="text-start ps-4">Tên Voucher</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Điểm cần</th>
                        <th>Giá trị</th>
                        <th>Số lượng</th>
                        <th>Hạn dùng</th>
                        <th>Kích hoạt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                        <tr class="table-row">
                            <td class="text-start ps-4 fw-semibold">
                                {{ $voucher->ten }}
                                <div class="small text-muted">#{{ $voucher->id }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info bg-opacity-75">
                                    {{ $voucher->loai == 'phan_tram' ? 'Phần trăm' : 'Số tiền' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($voucher->conHieuLuc())
                                    <span class="badge bg-success bg-opacity-75 px-3 py-2 shadow-sm">
                                        <i class="bi bi-check-circle"></i> Hoạt động
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-75 px-3 py-2 shadow-sm">
                                        <i class="bi bi-x-circle"></i> Hết hạn
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark px-3 py-2 shadow-sm">{{ $voucher->diem_can }}</span>
                            </td>
                            <td class="text-center text-success fw-bold">{{ $voucher->mo_ta_gia_tri }}</td>
                            <td class="text-center">
                                {{ $voucher->so_luong_da_dung }}/{{ $voucher->so_luong_toi_da }}
                            </td>
                            <td class="text-center">
                                @if($voucher->ngay_ket_thuc)
                                    {{ \Carbon\Carbon::parse($voucher->ngay_ket_thuc)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Vô hạn</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center" style="height:56px;">
                                    <div class="form-check form-switch m-0">
                                        <input type="checkbox" class="form-check-input align-middle"
                                            {{ $voucher->kich_hoat ? 'checked' : '' }}
                                            onchange="toggleStatus({{ $voucher->id }})"
                                            style="cursor:pointer;">
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.voucher.show', $voucher->id) }}"
                                       class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('admin.voucher.edit', $voucher->id) }}"
                                       class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1"
                                            onclick="confirmDelete({{ $voucher->id }})">
                                        <i class="bi bi-trash3"></i> Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có voucher nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PHÂN TRANG --}}
        @if($vouchers->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted small">
                        Hiển thị <strong>{{ $vouchers->firstItem() }}</strong>–<strong>{{ $vouchers->lastItem() }}</strong> 
                        trên tổng <strong>{{ $vouchers->total() }}</strong> voucher
                    </div>
                    <div>{{ $vouchers->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- FORM ẨN --}}
<form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
<form id="toggleForm" method="POST" style="display:none;">@csrf</form>

@push('scripts')
<script>
function toggleStatus(id) {
    if (confirm('Bạn có chắc muốn thay đổi trạng thái voucher này?')) {
        const form = document.getElementById('toggleForm');
        form.action = `/admin/voucher/${id}/toggle-status`;
        form.submit();
    } else window.location.reload();
}

function confirmDelete(id) {
    if (confirm('Xóa voucher này? Không thể khôi phục sau khi xóa.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/voucher/${id}`;
        form.submit();
    }
}
</script>

<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.table-header {
    background: linear-gradient(90deg, #007bff, #00c3ff);
}
.table-row {
    background-color: #fff;
    transition: all 0.25s ease-in-out;
}
.table-row:nth-child(even) { background-color: #f8f9fa; }
.table-row:hover { background-color: #e9f5ff; transform: scale(1.01); }
.table th {
    font-weight: 600;
    letter-spacing: 0.3px;
    border-bottom: none !important;
}
.table td { padding: 1rem 1.2rem; vertical-align: middle; }
.table td .form-check.form-switch {
    --bs-switch-width: 42px;  /* tuỳ chỉnh nếu muốn nhỏ/lớn hơn */
    --bs-switch-height: 22px;
    margin: 0;                /* đảm bảo không có margin thừa */
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Nếu muốn switch to hơn (desktop) */
.table td .form-check-input {
    width: var(--bs-switch-width);
    height: var(--bs-switch-height);
    margin: 0; /* ngăn bootstrap thêm left margin */
    transform: translateY(0); /* reset bất kỳ transform mặc định nào */
}

/* Giữ khoảng padding hàng, tránh nội dung ép sát */
.table td {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

/* Nếu dùng rounded-pill buttons ở action, giữ center */
.table td .d-flex > .btn {
    min-width: 56px;
}

</style>
@endpush
@endsection
