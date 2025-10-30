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
            <a href="{{ route('admin.voucher.trashed') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                <i class="bi bi-trash"></i> Thùng rác
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
                    <input type="text" name="search" class="form-control" placeholder="🔍 Tìm theo tên voucher"
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="loai" class="form-select rounded-pill">
                        <option value="">-- Loại --</option>
                        <option value="phan_tram" {{ request('loai') == 'phan_tram' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="so_tien" {{ request('loai') == 'so_tien' ? 'selected' : '' }}>Số tiền</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="trang_thai" class="form-select rounded-pill">
                        <option value="">-- Trạng thái --</option>
                        <option value="hoat_dong" {{ request('trang_thai') == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="sap_hoat_dong" {{ request('trang_thai') == 'sap_hoat_dong' ? 'selected' : '' }}>Sắp hoạt động</option>
                        <option value="het_han" {{ request('trang_thai') == 'het_han' ? 'selected' : '' }}>Hết hạn</option>
                        <option value="da_tat" {{ request('trang_thai') == 'da_tat' ? 'selected' : '' }}>Đã tắt</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="ap_dung_cho" class="form-select rounded-pill" disabled>
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
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white text-center">
                    <tr>
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
                        @php
                            $startsAt = $voucher->bat_dau ?? $voucher->ngay_bat_dau ?? $voucher->ap_dung_tu ?? $voucher->start_at ?? null;
                            $isFuture = $startsAt ? \Carbon\Carbon::parse($startsAt)->isFuture() : false;
                        @endphp

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
                                @if(!$voucher->kich_hoat)
                                    <span class="badge rounded-pill bg-secondary px-3 py-2 shadow-sm">
                                        <i class="bi bi-pause-circle"></i> Đã tắt
                                    </span>
                                @elseif($isFuture)
                                    <span class="badge rounded-pill bg-info text-dark px-3 py-2 shadow-sm" 
                                          title="Bắt đầu: {{ \Carbon\Carbon::parse($startsAt)->format('d/m/Y H:i') }}">
                                        <i class="bi bi-clock-history"></i> Sắp hoạt động
                                    </span>
                                @elseif(!$voucher->conHieuLuc())
                                    <span class="badge rounded-pill bg-danger px-3 py-2 shadow-sm">
                                        <i class="bi bi-x-circle"></i> Hết hạn
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-success px-3 py-2 shadow-sm">
                                        <i class="bi bi-check-circle"></i> Hoạt động
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
                                <div class="form-check form-switch m-0 d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input" style="cursor:pointer;"
                                           onchange="toggleStatus({{ $voucher->id }})"
                                           {{ $voucher->kich_hoat ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.voucher.show', $voucher->id) }}"
                                       class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('admin.voucher.edit', $voucher->id) }}"
                                       class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm"
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
.badge {
    font-size: 0.9rem;
    box-shadow: 0 0.2rem 0.5rem rgba(0,0,0,0.15);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s;
}
.badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.4rem 0.8rem rgba(0,0,0,0.2);
}
</style>
@endpush
@endsection
