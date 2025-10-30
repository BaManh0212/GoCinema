@extends('admin.layouts.admin')

@section('title', '🗑️ Thùng rác Voucher Vé Phim')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-trash3"></i> Thùng rác Voucher Vé Phim
            </h2>
            <small class="text-muted">Danh sách các voucher đã bị xóa tạm thời</small>
        </div>
        <a href="{{ route('admin.voucher.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    {{-- ✅ Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 🧾 Bảng voucher --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            @if ($vouchers->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead style="background: linear-gradient(90deg, #007bff, #00c3ff);" class="text-white">
                            <tr>
                                <th width="5%">ID</th>
                                <th class="text-start">Tên Voucher</th>
                                <th>Loại</th>
                                <th>Điểm cần</th>
                                <th>Giá trị</th>
                                <th>Ngày xóa</th>
                                <th width="25%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vouchers as $voucher)
                                <tr class="align-middle hover-row">
                                    <td class="fw-semibold">{{ $voucher->id }}</td>
                                    <td class="text-start fw-semibold text-primary">{{ $voucher->ten }}</td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-75">
                                            {{ $voucher->loai == 'phan_tram' ? 'Phần trăm' : 'Số tiền' }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-warning text-dark">{{ $voucher->diem_can }}</span></td>
                                    <td class="text-success fw-semibold">{{ $voucher->mo_ta_gia_tri }}</td>
                                    <td class="text-muted">
                                        {{ $voucher->deleted_at ? \Carbon\Carbon::parse($voucher->deleted_at)->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            {{-- 🔄 Khôi phục --}}
                                            <form action="{{ route('admin.voucher.restore', $voucher->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success rounded-pill px-3 shadow-sm"
                                                    onclick="return confirm('Bạn có chắc muốn khôi phục voucher này?')">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                                </button>
                                            </form>

                                            {{-- ❌ Xóa vĩnh viễn --}}
                                            <form action="{{ route('admin.voucher.forceDelete', $voucher->id) }}" method="POST"
                                                onsubmit="return confirm('⚠️ Xóa vĩnh viễn voucher này? Hành động này không thể hoàn tác!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-x-circle"></i> Xóa vĩnh viễn
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
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
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Không có voucher nào trong thùng rác 📭
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card {
    border-radius: 1rem;
    background-color: #fff;
}

.table thead th {
    color: #fff !important;
    border: none;
}

.hover-row:hover {
    background-color: #f8faff;
    transition: 0.2s ease;
}

.btn {
    font-size: 0.9rem;
    transition: all 0.2s ease;
}
.btn:hover {
    transform: scale(1.05);
}
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
</style>
@endsection
