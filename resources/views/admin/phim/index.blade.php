@extends('admin.layouts.admin')

@section('title', 'Quản lý phim')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <h2 class="text-center mb-4 fw-bold text-primary">🎬 Danh sách phim</h2>

    {{-- Nút thao tác --}}
    <div class="d-flex justify-content-end mb-4 gap-2">
        <a href="{{ route('admin.phim.trashed') }}" class="btn btn-outline-secondary">
            🗑️ Thùng rác
        </a>
        <a href="{{ route('admin.phim.create') }}" class="btn btn-success">
            ➕ Thêm phim mới
        </a>
    </div>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Danh sách phim --}}
    <div class="row">
        @forelse($phims as $phim)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 overflow-hidden movie-card">
                    <div class="d-flex p-3">
                        
                        {{-- Poster phim --}}
                        <div class="me-3" style="width: 150px; height: 210px; overflow: hidden; border-radius: 10px;">
                            @if($phim->anh_poster)
                                <img src="{{ asset('storage/' . $phim->anh_poster) }}" 
                                     alt="Poster phim" 
                                     class="img-fluid h-100 w-100 object-fit-cover">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center h-100 text-muted">
                                    <small>No Image</small>
                                </div>
                            @endif
                        </div>

                        {{-- Thông tin phim --}}
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-primary text-uppercase mb-2">{{ $phim->tieu_de }}</h5>
                            <span class="badge {{ $phim->ngay_cong_chieu > now() ? 'bg-info text-dark' : 'bg-success' }} mb-2">
                                {{ $phim->ngay_cong_chieu > now() ? 'Sắp chiếu' : 'Đang chiếu' }}
                            </span>

                            <ul class="list-unstyled small mb-2">
                                <li>⏱️ <strong>Thời lượng:</strong> {{ $phim->thoi_luong }} phút</li>
                                <li>📅 <strong>Công chiếu:</strong> {{ \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') }}</li>
                                <li>🗣️ <strong>Ngôn ngữ:</strong> {{ $phim->ngonNgu->ten ?? '—' }}</li>
                                <li>🎬 <strong>Đạo diễn:</strong> {{ $phim->dao_dien ?? '—' }}</li>
                                <li>👥 <strong>Diễn viên:</strong> {{ $phim->dien_vien ?? '—' }}</li>
                                <li>📁 <strong>Danh mục:</strong>
                                    @if($phim->danhMuc)
                                        <span class="badge bg-warning text-dark">{{ $phim->danhMuc->ten }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </li>
                                <li>🔞 <strong>Độ tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?? 'P' }}</li>
                            </ul>

                            {{-- Hành động --}}
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.phim.show', $phim->id) }}" class="btn btn-sm btn-outline-primary">
                                    👁️ Xem chi tiết
                                </a>
                                <a href="{{ route('admin.phim.edit', $phim->id) }}" class="btn btn-sm btn-outline-primary">
                                    ✏️ Sửa
                                </a>
                                <form action="{{ route('admin.phim.destroy', $phim->id) }}" method="POST" 
                                      onsubmit="return confirm('Xác nhận xóa phim này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted mt-4">
                Không có phim nào trong hệ thống 📭
            </div>
        @endforelse
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $phims->links('pagination::bootstrap-5') }}
    </div>

</div>

{{-- Hiệu ứng hover nhẹ --}}
<style>
.movie-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.movie-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}
</style>
@endsection
