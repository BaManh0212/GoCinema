@extends('admin.layouts.admin')

@section('title', '🗑️ Thùng rác danh mục')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-trash3"></i> Thùng rác danh mục
            </h2>
            <small class="text-muted">Danh sách các danh mục đã bị xóa tạm thời</small>
        </div>
        <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
    {{-- 🔍 Form tìm kiếm --}}
    <form method="GET" action="{{ route('admin.danhmuc.trashed') }}" class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="keyword" class="form-control rounded-start-pill"
                    placeholder="🔍 Tìm theo tên danh mục..."
                    value="{{ request('keyword') }}">
            <button class="btn btn-primary rounded-end-pill shadow-sm" type="submit">
                <i class="bi bi-search"></i> Tìm
            </button>
        </div>
    </form>


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

    {{-- 🗂️ Bảng danh mục --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            @if ($danhmucs->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead class="bg-gradient text-white" style="background: linear-gradient(90deg, #007bff, #00c3ff);">
                            <tr>
                                <th width="5%">STT</th>
                                <th>Tên danh mục</th>
                                <th>Ngày xóa</th>
                                <th width="25%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($danhmucs as $key => $dm)
                                <tr class="align-middle hover-row">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="fw-semibold text-primary">{{ $dm->ten }}</td>
                                    <td class="text-muted">
                                        {{ \Carbon\Carbon::parse($dm->deleted_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            {{-- Khôi phục --}}
                                            <form action="{{ route('admin.danhmuc.restore', $dm->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                                </button>
                                            </form>

                                            {{-- Xóa vĩnh viễn --}}
                                            <form action="{{ route('admin.danhmuc.forceDelete', $dm->id) }}" method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn danh mục này không?')">
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
                    Không có danh mục nào trong thùng rác 📭
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
</style>
@endsection
