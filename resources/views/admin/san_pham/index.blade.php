@extends('admin.layouts.admin')

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
        <div>
            <a href="{{ route('admin.san_pham.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm sản phẩm
            </a>
            <a href="{{ route('admin.san_pham.trashed') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
        </div>
    </div>
    {{-- 🔍 Tìm kiếm và Lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.san_pham.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên" 
                           value="{{ $filters['q'] ?? '' }}">
                </div>
                <div class="col-auto">
                    <select name="sort" class="form-select rounded-pill">
                        <option value="">-- Sắp xếp --</option>
                        <option value="gia_desc" {{ ($filters['sort'] ?? '') == 'gia_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="gia_asc" {{ ($filters['sort'] ?? '') == 'gia_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                    </select>
                </div>
                
                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Tìm kiếm</button>
                    <a href="{{ route('admin.san_pham.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>


    {{-- 📋 Bảng sản phẩm --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white text-center">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th class="text-start">Tên sản phẩm</th>
                        <th class="text-start">Slug</th>
                        <th class="text-end">Giá (VNĐ)</th>
                        <th class="text-center">Số lượng</th>
                        <th style="width:180px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sanPhams as $sanPham)
                        <tr class="table-row">
                            <td class="text-center fw-bold text-muted">{{ $sanPham->id }}</td>
                            <td class="fw-semibold text-start">{{ $sanPham->ten }}</td>
                            <td class="text-start text-muted">{{ $sanPham->slug }}</td>
                            <td class="text-end">{{ number_format($sanPham->gia, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $sanPham->so_luong }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.san_pham.edit', $sanPham->id) }}" 
                                       class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.san_pham.destroy', $sanPham->id) }}" 
                                          method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này không?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-trash3"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
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
            <div class="mt-3">
                {{ $sanPhams->links('pagination::bootstrap-5') }}
            </div>
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
/*nút lọc và đặt lại nằm bên phải */
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
</style>
@endsection
