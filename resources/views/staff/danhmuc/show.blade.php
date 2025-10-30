@extends('staff.layouts.staff')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gradient mb-1">
                <i class="bi bi-folder2-open"></i> Chi tiết danh mục
            </h2>
            <p class="text-muted mb-0">Thông tin chi tiết về danh mục và các phim liên quan</p>
        </div>
        <a href="{{ route('staff.danhmuc.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left-circle"></i> Quay lại danh sách
        </a>
    </div>

    {{-- 📋 Thông tin danh mục --}}
    <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <p class="mb-2"><strong>ID:</strong> {{ $danhmuc->id }}</p>
                    <p class="mb-2"><strong>Tên danh mục:</strong> <span class="text-primary">{{ $danhmuc->ten }}</span></p>
                    <p class="mb-2"><strong>Slug:</strong> <code>{{ $danhmuc->slug }}</code></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Tổng số phim:</strong>
                        <span class="badge bg-info text-dark px-3 py-2 shadow-sm">
                            {{ $danhmuc->phims->count() }}
                        </span>
                    </p>
                    <p class="mb-2 text-muted"><strong>Ngày tạo:</strong> {{ $danhmuc->created_at->format('d/m/Y H:i') }}</p>
                    <p class="mb-0 text-muted"><strong>Cập nhật:</strong> {{ $danhmuc->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 🎬 Danh sách phim --}}
    <h4 class="fw-bold mb-3 text-gradient"><i class="bi bi-film"></i> Danh sách phim trong danh mục</h4>

    @if($danhmuc->phims->count() > 0)
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-header text-white">
                        <tr class="text-center">
                            <th width="60">#</th>
                            <th class="text-start">Tên phim</th>
                            <th>Ngôn ngữ</th>
                            <th>Ngày khởi chiếu</th>
                            <th width="120">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($danhmuc->phims as $index => $phim)
                            <tr class="table-row">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-start fw-semibold">{{ $phim->tieu_de }}</td>
                                <td>{{ $phim->ngonNgu->ten ?? '—' }}</td>
                                <td>{{ $phim->ngay_cong_chieu ?? '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('staff.phim.show', $phim->id) }}" 
                                       class="btn btn-sm btn-info rounded-pill px-3 shadow-sm d-flex align-items-center gap-1 justify-content-center">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-warning shadow-sm rounded-3 mt-3 text-center">
            <i class="bi bi-exclamation-triangle"></i> Chưa có phim nào trong danh mục này.
        </div>
    @endif

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
</style>
@endsection
