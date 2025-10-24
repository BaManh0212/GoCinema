@extends('admin.layouts.admin')

@section('title', '🗑️ Thùng rác sản phẩm')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-trash3"></i> Thùng rác sản phẩm
            </h2>
            <small class="text-muted">Danh sách các sản phẩm đã bị xóa tạm thời</small>
        </div>
        <a href="{{ route('admin.san_pham.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
     {{-- 🔍 Tìm kiếm và Lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.san_pham.trashed') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên sản phẩm" 
                        value="{{ $filters['q'] ?? '' }}">
                </div>
                <div class="col-auto">
                    <select name="sort" class="form-select">
                        <option value="">-- Sắp xếp --</option>
                        <option value="moi_nhat" {{ ($filters['sort'] ?? '') == 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="cu_nhat" {{ ($filters['sort'] ?? '') == 'cu_nhat' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="gia_asc" {{ ($filters['sort'] ?? '') == 'gia_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="gia_desc" {{ ($filters['sort'] ?? '') == 'gia_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                    </select>
                </div>

                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Tìm kiếm</button>
                    <a href="{{ route('admin.san_pham.trashed') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
                </div>
            </form>

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
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 🧾 Bảng sản phẩm --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            @if ($sanPhams->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead class="bg-gradient text-white"
                            style="background: linear-gradient(90deg, #007bff, #00c3ff);">
                            <tr>
                                <th width="5%">ID</th>
                                <th class="text-start">Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Ngày xóa</th>
                                <th width="25%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sanPhams as $sp)
                                <tr class="align-middle hover-row">
                                    <td class="fw-semibold">{{ $sp->id }}</td>
                                    <td class="text-start fw-semibold text-primary">{{ $sp->ten }}</td>
                                    <td>{{ number_format($sp->gia, 0, ',', '.') }} đ</td>
                                    <td>{{ $sp->so_luong }}</td>
                                    <td class="text-muted">
                                        {{ $sp->deleted_at ? \Carbon\Carbon::parse($sp->deleted_at)->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            {{-- Khôi phục --}}
                                            <form action="{{ route('admin.san_pham.restore', $sp->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success rounded-pill px-3 shadow-sm"
                                                        onclick="return confirm('Bạn có chắc muốn khôi phục sản phẩm này?')">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                                </button>
                                            </form>

                                            {{-- Xóa vĩnh viễn --}}
                                            <form action="{{ route('admin.san_pham.forceDelete', $sp->id) }}" method="POST"
                                                  onsubmit="return confirm('⚠️ Xóa vĩnh viễn sản phẩm này? Hành động này không thể hoàn tác!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-x-circle"></i> Xóa vĩnh viễn
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Không có sản phẩm nào trong thùng rác 📭
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card {
    border-radius: 1rem;
    background-color: #fff;
}

.table thead th {
    color: #fff !important;
    border: none;
}

.hover-row:hover {
    background-color: #f8faff;
    transition: 0.2s ease;
}

.btn {
    font-size: 0.9rem;
    transition: all 0.2s ease;
}
.btn:hover {
    transform: scale(1.05);
}
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
</style>
@endsection
