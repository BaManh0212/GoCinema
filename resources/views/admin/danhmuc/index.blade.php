@extends('admin.layouts.admin')

@section('title', 'Quản lý bài viết')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-folder2-open"></i> Quản lý Danh mục
            </h2>
            <small class="text-muted">Xem, lọc và quản lý các danh mục phim</small>
        </div>
        <div>
            <a href="{{ route('admin.danhmuc.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm danh mục
            </a>
            <a href="{{ route('admin.danhmuc.trashed') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
<div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.danhmuc.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên danh mục" 
                        value="{{ $filters['q'] ?? '' }}">
                </div>
                <div class="col-auto">
                    <select name="sort" class="form-select rounded-pill">
                        <option value="">-- Sắp xếp theo --</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên (A → Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên (Z → A)</option>
                    <option value="phim_count_desc" {{ request('sort') == 'phim_count_desc' ? 'selected' : '' }}>Nhiều phim nhất</option>
                    <option value="phim_count_asc" {{ request('sort') == 'phim_count_asc' ? 'selected' : '' }}>Ít phim nhất</option>
                    </select>
                </div>

                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Tìm kiếm</button>
                    <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
                </div>
            </form>

        </div>
    </div>


    {{-- 📋 Bảng danh sách --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th style="width: 70px;">STT</th>
                        <th class="text-start">Tên danh mục</th>
                        <th class="text-start">Slug</th>
                        <th style="width: 160px;">Số lượng phim</th>
                        <th style="width: 220px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($danhmucs as $dm)
                        <tr class="table-row">
                            <td class="text-center fw-bold text-muted">{{ $dm->id }}</td>
                            <td class="fw-semibold">{{ $dm->ten }}</td>
                            <td class="text-muted">{{ $dm->slug }}</td>
                            <td class="text-center">
                                <span class="badge bg-info bg-opacity-75 px-3 py-2 shadow-sm">
                                    {{ $dm->phims_count }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.danhmuc.show', $dm->id) }}" 
                                       class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    {{-- ✏️ Nút sửa --}}
                                    <a href="{{ route('admin.danhmuc.edit', $dm->id) }}" 
                                       class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>

                                    {{-- 🗑️ Nút xóa --}}
                                    <form action="{{ route('admin.danhmuc.destroy', $dm->id) }}" 
                                          method="POST" onsubmit="return confirm('Xóa danh mục này?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-trash3"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có bài viết nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-end">
                {{ $danhmucs->links('pagination::bootstrap-5') }}
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
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
</style>
@endsection
