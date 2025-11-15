@extends('admin.layouts.admin')

@section('title', 'Quản lý bài viết')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-newspaper"></i> Quản lý bài viết
            </h2>
            <small class="text-muted">Xem, lọc và quản lý tất cả bài viết</small>
        </div>
        <div>
            <a href="{{ route('admin.baiviet.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm bài viết
            </a>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.baiviet.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề..." 
                        value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="loai" class="form-select rounded-pill">
                        <option value="">-- Loại bài viết --</option>
                        <option value="tin-tuc" {{ request('loai')=='tin-tuc' ? 'selected' : '' }}>Tin tức</option>
                        <option value="khuyen-mai" {{ request('loai')=='khuyen-mai' ? 'selected' : '' }}>Khuyến mãi</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="trang_thai" class="form-select rounded-pill">
                        <option value="">-- Trạng thái --</option>
                        <option value="dang_hien_thi" {{ request('trang_thai')=='dang_hien_thi'?'selected':'' }}>Đang hiển thị</option>
                        <option value="chua_phat_hanh" {{ request('trang_thai')=='chua_phat_hanh'?'selected':'' }}>Chưa phát hành</option>
                        <option value="da_ket_thuc" {{ request('trang_thai')=='da_ket_thuc'?'selected':'' }}>Đã kết thúc</option>
                        <option value="an" {{ request('trang_thai')=='an'?'selected':'' }}>Ẩn</option>
                    </select>
                </div>
                <div class="col-auto ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Tìm kiếm</button>
                    <a href="{{ route('admin.baiviet.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
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
                        <th>Hình ảnh</th>
                        <th class="text-start">Tiêu đề</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Ngày phát hành</th>
                        <th>Ngày kết thúc</th>
                        <th style="width: 220px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baiviets as $key => $bv)
                        <tr class="table-row text-center">
                            <td class="fw-bold text-muted">{{ $baiviets->firstItem() + $key }}</td>
                            <td>
                                @if($bv->hinh_anh)
                                    <img src="{{ asset('storage/'.$bv->hinh_anh) }}" alt="" width="80" class="rounded">
                                @else
                                    <small class="text-muted">Không có</small>
                                @endif
                            </td>
                            <td class="text-start fw-semibold">{{ $bv->tieu_de }}</td>
                            <td>
                                <span class="badge bg-{{ $bv->loai=='tin-tuc'?'primary':'warning' }}">
                                    {{ ucfirst(str_replace('-', ' ', $bv->loai)) }}
                                </span>
                            </td>
                            <td>
    @php
        if (!$bv->is_active) {
            $statusClass = 'btn-secondary';
            $statusText = 'Ẩn';
            $statusIcon = 'x-circle';
        } elseif ($bv->ngay_phat_hanh && now()->lt(\Carbon\Carbon::parse($bv->ngay_phat_hanh))) {
            $statusClass = 'btn-warning';
            $statusText = 'Chưa phát hành';
            $statusIcon = 'clock';
        } elseif ($bv->ngay_ket_thuc && now()->gt(\Carbon\Carbon::parse($bv->ngay_ket_thuc))) {
            $statusClass = 'btn-danger';
            $statusText = 'Đã kết thúc';
            $statusIcon = 'calendar-x';
        } else {
            $statusClass = 'btn-success';
            $statusText = 'Đang hiển thị';
            $statusIcon = 'check-circle';
        }
    @endphp

    <form action="{{ route('admin.baiviet.toggle', $bv->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-sm {{ $statusClass }}">
            <i class="bi bi-{{ $statusIcon }}"></i> {{ $statusText }}
        </button>
    </form>
</td>

                            <td>
                                @if($bv->ngay_phat_hanh)
                                    {{ \Carbon\Carbon::parse($bv->ngay_phat_hanh)->format('d/m/Y') }}
                                @else
                                    <small class="text-muted">Chưa đặt</small>
                                @endif
                            </td>
                            <td>
                                @if($bv->ngay_ket_thuc)
                                    {{ \Carbon\Carbon::parse($bv->ngay_ket_thuc)->format('d/m/Y') }}
                                @else
                                    <small class="text-muted">Chưa đặt</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.baiviet.edit', $bv->id) }}" 
                                    class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>

                                    <form action="{{ route('admin.baiviet.destroy', $bv->id) }}" 
                                        method="POST" onsubmit="return confirm('Xóa bài viết này?')" class="d-inline">
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
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có bài viết nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-end">
                {{ $baiviets->links('pagination::bootstrap-5') }}
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
