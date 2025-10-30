@extends('admin.layouts.admin')

@section('title', 'Thùng rác mã giảm giá')

@section('content')
<div class="container mt-4">
    {{-- ===== TIÊU ĐỀ ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-gradient">
            <i class="bi bi-ticket-perforated"></i> 🗑️ Thùng Rác Mã Giảm Giá
        </h2>
        <a href="{{ route('admin.ma_giam_gia.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- ===== BẢNG DANH SÁCH ===== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="text-white" style="background: linear-gradient(90deg, #0d6efd, #0dcaf0);">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Mã</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Ngày xóa</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maGiamGia as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-primary">{{ $item->ma }}</td>
                            <td>
                                @if ($item->loai === 'phan_tram')
                                    <span class="badge bg-info text-dark">Giảm %</span>
                                @else
                                    <span class="badge bg-success">Giảm tiền</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->loai == 'phan_tram' ? $item->gia_tri.'%' : number_format($item->gia_tri).'đ' }}
                            </td>
                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                {{-- Nút khôi phục --}}
                                <form action="{{ route('admin.ma_giam_gia.restore', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3"
                                        onclick="return confirm('Khôi phục mã này?')">
                                        <i class="bi bi-arrow-clockwise"></i> Khôi phục
                                    </button>
                                </form>

                                {{-- Nút xóa vĩnh viễn --}}
                                <form action="{{ route('admin.ma_giam_gia.forceDelete', $item->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Xóa vĩnh viễn mã này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="bi bi-trash"></i> Xóa vĩnh viễn
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-emoji-frown"></i> Không có mã giảm giá nào trong thùng rác
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PHÂN TRANG --}}
    <div class="mt-4 d-flex justify-content-center">
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
