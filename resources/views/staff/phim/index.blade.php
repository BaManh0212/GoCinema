@extends('staff.layouts.staff')

@section('title', 'Quản lý phim')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <h2 class="text-center mb-4 fw-bold text-primary">🎬 Danh sách phim</h2>

    {{-- Nút thao tác --}}
    <div class="d-flex justify-content-end mb-4 gap-2 flex-wrap">
        <a href="{{ route('staff.phim.create') }}" class="btn btn-success">
            ➕ Thêm phim mới
        </a>
    </div>

    {{-- Danh sách phim --}}
    <div class="row g-3">
        @forelse($phims as $phim)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 overflow-hidden movie-card">
                    
                    {{-- Poster --}}
                    <div style="height: 250px; overflow: hidden;">
                        @if($phim->anh_poster)
                            <img src="{{ asset('storage/' . $phim->anh_poster) }}" 
                                 alt="Poster phim" 
                                 class="img-fluid w-100 h-100 object-fit-cover">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center h-100 text-muted">
                                <small>No Image</small>
                            </div>
                        @endif
                    </div>

                    {{-- Nội dung --}}
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold text-primary text-uppercase mb-2">{{ $phim->tieu_de }}</h5>

                        <span class="badge {{ $phim->ngay_cong_chieu > now() ? 'bg-info text-dark' : 'bg-success' }} mb-2">
                            {{ $phim->ngay_cong_chieu > now() ? 'Sắp chiếu' : 'Đang chiếu' }}
                        </span>

                        <ul class="list-unstyled small mb-3">
                            <li>⏱️ <strong>Thời lượng:</strong> {{ $phim->thoi_luong }} phút</li>
                            <li>📅 <strong>Công chiếu:</strong> {{ \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') }}</li>
                            <li>🗣️ <strong>Ngôn ngữ:</strong> {{ $phim->ngonNgu->ten ?? '—' }}</li>
                            <li>🎬 <strong>Đạo diễn:</strong> {{ $phim->dao_dien ?? '—' }}</li>
                            <li>👥 <strong>Diễn viên:</strong> {{ $phim->dien_vien ?? '—' }}</li>
                            <li>📁 <strong>Danh mục:</strong>
                                @if($phim->danhMucs && $phim->danhMucs->count())
                                    @foreach($phim->danhMucs as $dm)
                                        <span class="badge bg-warning text-dark">{{ $dm->ten }}</span>
                                    @endforeach
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </li>

                            <li>🔞 <strong>Độ tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?? 'P' }}</li>
                        </ul>

                        {{-- Hành động --}}
                        <div class="mt-auto d-flex flex-wrap gap-2">
                            <a href="{{ route('staff.phim.show', $phim->id) }}" class="btn btn-sm btn-outline-primary">
                                👁️ Xem chi tiết
                            </a>
                            <a href="{{ route('staff.phim.edit', $phim->id) }}" class="btn btn-sm btn-outline-primary">
                                ✏️ Sửa
                            </a>
                            <form action="{{ route('staff.phim.destroy', $phim->id) }}" method="POST" 
                                  onsubmit="return confirm('Xác nhận xóa phim này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Xóa</button>
                            </form>
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

{{-- Hiệu ứng hover --}}
<style>
.movie-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.movie-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}
.object-fit-cover {
    object-fit: cover;
}
</style>
@endsection
