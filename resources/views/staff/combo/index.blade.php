@extends('staff.layouts.staff')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <span style="font-size: 1.5rem;">📦</span> Danh sách Combo
        </h2>
    </div>

    {{-- Thông báo thành công --}}
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
                        <th>Số lượng</th>
                        <th>Tổng sản phẩm</th>
                        <th>Sản phẩm trong Combo</th>
                        <th>Mô tả</th>
                        <th>Ngày tạo</th>
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

                            {{-- Hiển thị danh sách sản phẩm trong Combo --}}
                            <td>
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

                            <td>{{ $combo->mo_ta }}</td>
                            <td class="text-center">
                                {{ $combo->created_at ? $combo->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Không có Combo nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
