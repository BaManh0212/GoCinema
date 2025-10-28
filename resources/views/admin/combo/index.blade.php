@extends('admin.layouts.admin')

@section('title', 'Quản lý Combo')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-box-seam"></i> Danh sách Combo
            </h2>
            <small class="text-muted">Xem, quản lý và lọc các Combo hiện có</small>
        </div>
        <div>
            <a href="{{ route('admin.combo.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm Combo
            </a>
            <a href="{{ route('admin.combo.trashed') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
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

    {{-- 🔍 Tìm kiếm và Lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.combo.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên combo" 
                        value="{{ $filters['q'] ?? '' }}">
                </div>
                <div class="col-auto">
                    <select name="sort" class="form-select">
                        <option value="">-- Sắp xếp --</option>
                        <option value="moi_nhat" {{ ($filters['sort'] ?? '') == 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="cu_nhat" {{ ($filters['sort'] ?? '') == 'cu_nhat' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="gia_desc" {{ ($filters['sort'] ?? '') == 'gia_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="gia_asc" {{ ($filters['sort'] ?? '') == 'gia_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                    </select>
                </div>

                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Tìm kiếm</button>
                    <a href="{{ route('admin.combo.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
                </div>
            </form>

        </div>
    </div>

    {{-- 📋 Bảng Combo --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white text-center">
                    <tr>
                        <th style="width:70px;">STT</th>
                        <th class="text-start">Tên Combo</th>
                        <th class="text-start">Slug</th> {{-- 🆕 Thêm cột slug --}}
                        <th class="text-end">Giá (VNĐ)</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-center">Tổng SP</th>
                        <th class="text-start">Sản phẩm trong Combo</th>
                        <th class="text-start">Mô tả</th>
                        <th class="text-center">Ngày tạo</th>
                        <th style="width:180px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($combos as $combo)
                        @php
                            $tongSanPham = $combo->chiTiet->sum(fn($ct) => $ct->so_luong);
                        @endphp
                        <tr class="table-row">
                            <td class="text-center fw-bold text-muted">{{ $combo->id }}</td>
                            <td class="fw-semibold text-start">{{ $combo->ten }}</td>
                            <td class="text-start text-muted">{{ $combo->slug }}</td>
                            <td class="text-end">{{ number_format($combo->gia, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $combo->so_luong }}</td>
                            <td class="text-center">{{ $tongSanPham }}</td>

                            {{-- Sản phẩm trong Combo --}}
                            <td class="text-start">
                                @if ($combo->chiTiet->count() > 0)
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($combo->chiTiet as $ct)
                                            <li class="d-flex align-items-center mb-1">
                                                @if (!empty($ct->sanPham->hinh_anh))
                                                    <img src="{{ asset('uploads/sanpham/' . $ct->sanPham->hinh_anh) }}"
                                                         alt="{{ $ct->sanPham->ten }}"
                                                         width="35" height="35"
                                                         class="rounded me-2 border">
                                                @else
                                                    <div class="me-2 text-muted" style="width:35px; text-align:center;">📦</div>
                                                @endif
                                                <div>
                                                    <strong>{{ $ct->sanPham->ten }}</strong>
                                                    <span class="text-muted">x{{ $ct->so_luong }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted fst-italic">Không có sản phẩm</span>
                                @endif
                            </td>

                            <td class="text-start">{{ $combo->mo_ta }}</td>
                            <td class="text-center">
                                {{ $combo->created_at ? $combo->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.combo.edit', $combo->id) }}" 
                                       class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.combo.destroy', $combo->id) }}" 
                                          method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa Combo này không?');" class="d-inline">
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
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có Combo nào.
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

/*nút lọc và đặt lại nằm bên phải */
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
</style>
@endsection
