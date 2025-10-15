@extends('staff.layouts.staff')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">📦 Danh sách sản phẩm</h2>
        <div>
            <a href="{{ route('staff.san_pham.create') }}" class="btn btn-success me-2">
                ➕ Thêm sản phẩm
            </a>
            <a href="{{ route('staff.san_pham.trashed') }}" class="btn btn-outline-warning">
                🗑️ Thùng rác
            </a>
        </div>
    </div>

    {{-- Hiển thị thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Bảng danh sách sản phẩm --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá (VNĐ)</th>
                        <th>Số lượng</th>
                        <th width="200px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sanPhams as $sanPham)
                        <tr>
                            <td class="text-center">{{ $sanPham->id }}</td>
                            <td>{{ $sanPham->ten }}</td>
                            <td class="text-end">{{ number_format($sanPham->gia, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $sanPham->so_luong }}</td>
                            <td class="text-center">
                                <a href="{{ route('staff.san_pham.edit', $sanPham->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    ✏️ Sửa
                                </a>
                                <form action="{{ route('staff.san_pham.destroy', $sanPham->id) }}" 
                                      method="POST" 
                                      style="display:inline;"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Không có sản phẩm nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
