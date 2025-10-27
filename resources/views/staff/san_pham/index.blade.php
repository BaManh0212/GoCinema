@extends('staff.layouts.staff')

@section('title', 'Quản lý Sản phẩm')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-basket3-fill"></i> Danh sách đồ ăn & đồ lưu niệm
            </h2>
            <small class="text-muted">Xem, quản lý và lọc các sản phẩm hiện có</small>
        </div>
    </div>

    {{-- ✅ Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 📋 Bảng sản phẩm --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white text-center">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th class="text-start">Tên sản phẩm</th>
                        <th class="text-end">Giá (VNĐ)</th>
                        <th class="text-center">Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sanPhams as $sanPham)
                        <tr class="table-row">
                            <td class="text-center fw-bold text-muted">{{ $sanPham->id }}</td>
                            <td class="fw-semibold text-start">{{ $sanPham->ten }}</td>
                            <td class="text-end">{{ number_format($sanPham->gia, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $sanPham->so_luong }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có sản phẩm nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- 🎨 CSS đồng bộ --}}
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
.table-row:nth-child(even) {
    background-color: #f8f9fa;
}
.table-row:hover {
    background-color: #e9f5ff;
    transform: scale(1.01);
}
.table th {
    font-weight: 600;
    letter-spacing: 0.3px;
    border-bottom: none !important;
}
.table td {
    padding: 1rem 1.2rem;
    vertical-align: middle;
}
.card {
    border-radius: 1rem;
}
</style>
@endsection
