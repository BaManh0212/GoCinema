@extends('staff.layouts.staff')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">📦 Danh sách đồ ăn và đồ lưu niệm</h2>
    </div>

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
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sanPhams as $sanPham)
                        <tr>
                            <td class="text-center">{{ $sanPham->id }}</td>
                            <td>{{ $sanPham->ten }}</td>
                            <td class="text-end">{{ number_format($sanPham->gia, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $sanPham->so_luong }}</td>
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
