@extends('admin.layouts.admin')

@section('title', '🗑️ Thùng rác Combo')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-trash3"></i> Thùng rác Combo
            </h2>
            <small class="text-muted">Danh sách các combo đã bị xóa tạm thời</small>
        </div>
        <a href="{{ route('admin.combo.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
    {{-- 🔍 Tìm kiếm và Lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.combo.trashed') }}" class="row g-3 align-items-center">
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
                    <a href="{{ route('admin.combo.trashed') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
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

    {{-- 🗂️ Bảng Combo --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            @if ($combos->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead class="bg-gradient text-white" style="background: linear-gradient(90deg, #007bff, #00c3ff);">
                            <tr>
                                <th width="5%">STT</th>
                                <th>Tên combo</th>
                                <th>Giá</th>
                                <th>Ngày xóa</th>
                                <th width="25%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($combos as $key => $combo)
                                <tr class="align-middle hover-row">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="fw-semibold text-primary">{{ $combo->ten }}</td>
                                    <td>{{ number_format($combo->gia, 0, ',', '.') }} đ</td>
                                    <td class="text-muted">
                                        {{ \Carbon\Carbon::parse($combo->deleted_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            {{-- Khôi phục --}}
                                            <form action="{{ route('admin.combo.restore', $combo->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                                </button>
                                            </form>

                                            {{-- Xóa vĩnh viễn --}}
                                            <form action="{{ route('admin.combo.forceDelete', $combo->id) }}" method="POST"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn combo này không?')">
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
                    Không có combo nào trong thùng rác 📭
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
