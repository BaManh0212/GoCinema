@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <span style="font-size: 1.5rem;">📦</span> Danh sách Combo
        </h2>
        <div>
            <a href="{{ route('admin.combo.create') }}" class="btn btn-success me-2">
                ➕ Thêm Combo
            </a>
            <a href="{{ route('admin.combo.trashed') }}" class="btn btn-warning">
                🗑️ Thùng rác
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light text-center">
    <tr>
        <th>ID</th>
        <th>Tên Combo</th>
        <th>Giá (VNĐ)</th>
        <th>Số lượng</th> {{-- Số lượng combo --}}
        <th>Tổng sản phẩm</th> {{-- Tổng số lượng sản phẩm bên trong --}}
        <th>Mô tả</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
    </tr>
</thead>
<tbody>
    @forelse ($combos as $combo)
        @php
            $tongSanPham = $combo->chiTiet->sum(fn($ct) => $ct->so_luong);
        @endphp
        <tr>
            <td class="text-center">{{ $combo->id }}</td>
            <td>{{ $combo->ten }}</td>
            <td class="text-end">{{ number_format($combo->gia, 0, ',', '.') }}</td>
            <td class="text-center">{{ $combo->so_luong }}</td>
            <td class="text-center">{{ $tongSanPham }}</td>
            <td>{{ $combo->mo_ta }}</td>
            <td class="text-center">{{ $combo->created_at ? $combo->created_at->format('d/m/Y H:i') : '-' }}</td>
            <td class="text-center">
                <a href="{{ route('admin.combo.edit', $combo->id) }}" class="btn btn-sm btn-outline-primary me-1">✏️ Sửa</a>
                <form action="{{ route('admin.combo.destroy', $combo->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa Combo này không?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Xóa</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center text-muted py-4">Không có Combo nào.</td>
        </tr>
    @endforelse
</tbody>

            </table>
        </div>
    </div>
</div>
@endsection