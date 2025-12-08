@extends('admin.layouts.admin')

@section('title', 'Quản lý Mã Giảm Giá')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-ticket-perforated"></i> Quản lý Mã Giảm Giá
            </h2>
            <small class="text-muted">Xem, tìm kiếm và quản lý các mã giảm giá hệ thống</small>
        </div>
        <div>
            <a href="{{ route('admin.ma_giam_gia.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm mã
            </a>
            <a href="{{ route('admin.ma_giam_gia.trash') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                <i class="bi bi-trash"></i> Thùng rác
            </a>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ma_giam_gia.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control" placeholder="Tìm mã giảm giá..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-auto">
                    <select name="kich_hoat" class="form-select rounded-pill">
                        <option value="">-- Trạng thái --</option>
                        <option value="1" {{ request('kich_hoat') === '1' ? 'selected' : '' }}>Kích hoạt</option>
                        <option value="0" {{ request('kich_hoat') === '0' ? 'selected' : '' }}>Vô hiệu hóa</option>
                    </select>
                </div>

                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Tìm kiếm</button>
                    <a href="{{ route('admin.ma_giam_gia.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    {{-- 📋 Bảng dữ liệu --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th style="width: 60px;">STT</th>
                        <th class="text-start">Mã</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Giảm tối đa</th>
                        <th>Đơn tối thiểu</th>
                        <th>Áp dụng</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th style="width: 220px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maGiamGia as $item)
                        @php
                            $now = \Carbon\Carbon::now();
                            $status = '';
                            $statusClass = '';

                            if ($item->ngay_bat_dau && $item->ngay_ket_thuc) {
                                $start = \Carbon\Carbon::parse($item->ngay_bat_dau);
                                $end = \Carbon\Carbon::parse($item->ngay_ket_thuc);

                                if ($now->lt($start)) {
                                    $status = 'Sắp bắt đầu';
                                    $statusClass = 'warning';
                                } elseif ($now->between($start, $end)) {
                                    $status = 'Đang diễn ra';
                                    $statusClass = 'success';
                                } else {
                                    $status = 'Đã hết hạn';
                                    $statusClass = 'danger';
                                }
                            } else {
                                $status = 'Không xác định';
                                $statusClass = 'secondary';
                            }
                        @endphp

                        <tr class="table-row text-center">
                            <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-start">{{ $item->ma }}</td>
                            <td>{{ $item->loai == 'phan_tram' ? 'Giảm %' : 'Giảm tiền' }}</td>
                            <td>
                                {{ $item->loai == 'phan_tram' ? $item->gia_tri.'%' : number_format($item->gia_tri).'đ' }}
                            </td>
                            <td>
                                {{ $item->loai == 'phan_tram' 
                                    ? ($item->giam_toi_da ? number_format($item->giam_toi_da).'đ' : '—')
                                    : '—' }}
                            </td>
                            <td>
                                {{ $item->gia_tri_don_hang_toi_thieu ? number_format($item->gia_tri_don_hang_toi_thieu).'đ' : '—' }}
                            </td>
                            <td class="text-capitalize">{{ $item->ap_dung_cho }}</td>

                            <td>
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <form action="{{ route('admin.ma_giam_gia.toggle', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $item->kich_hoat ? 'btn-success' : 'btn-secondary' }} rounded-pill px-3 shadow-sm">
                                            {{ $item->kich_hoat ? 'Đang kích hoạt' : 'Đã tắt' }}
                                        </button>
                                    </form>

                                    <span class="badge bg-{{ $statusClass }} mt-1 px-3 py-1">
                                        {{ $status }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                {{ $item->ngay_bat_dau ? \Carbon\Carbon::parse($item->ngay_bat_dau)->format('d/m/Y') : '—' }}
                                <br>
                                {{ $item->ngay_ket_thuc ? \Carbon\Carbon::parse($item->ngay_ket_thuc)->format('d/m/Y') : '—' }}
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.ma_giam_gia.show', $item->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    {{-- ✏️ Sửa --}}
                                    <a href="{{ route('admin.ma_giam_gia.edit', $item->id) }}"
                                       class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>

                                    {{-- 🗑️ Xóa --}}
                                    <form action="{{ route('admin.ma_giam_gia.destroy', $item->id) }}"
                                          method="POST" onsubmit="return confirm('Xóa mã giảm giá này?')" class="d-inline">
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
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có mã giảm giá nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 📄 Phân trang --}}
    <div class="mt-3">
        {{ $maGiamGia->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- 🎨 CSS --}}
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
