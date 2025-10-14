@extends('admin.layouts.admin')

@section('title', 'Chi tiết phim')

@section('content')
<div class="container mt-4">

    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">🎬 {{ $phim->tieu_de }}</h4>
            <a href="{{ route('admin.phim.index') }}" class="btn btn-light btn-sm">⬅ Quay lại</a>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- Poster --}}
                <div class="col-md-4 text-center">
                    @if($phim->anh_poster)
                        <img src="{{ asset('storage/' . $phim->anh_poster) }}" alt="Poster"
                             class="img-fluid rounded shadow-sm mb-3" style="max-height: 400px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                             style="height: 400px;">
                             <span class="text-muted">No Image</span>
                        </div>
                    @endif
                </div>

                {{-- Thông tin phim --}}
                <div class="col-md-8">
                    <h5 class="text-primary fw-bold mb-3">📄 Thông tin phim</h5>

                    <p><strong>🎬 Tiêu đề:</strong> {{ $phim->tieu_de }}</p>
                    <p><strong>📜 Mô tả:</strong> {{ $phim->mo_ta ?? '—' }}</p>
                    <p><strong>📅 Ngày công chiếu:</strong> {{ \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') }}</p>
                    <p><strong>⏱️ Thời lượng:</strong> {{ $phim->thoi_luong }} phút</p>
                    <p><strong>🔞 Giới hạn tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?? 'P' }}</p>
                    <p><strong>🎞️ Trailer:</strong> 
                        @if($phim->trailer)
                            <a href="{{ $phim->trailer }}" target="_blank" class="text-decoration-none">Xem trailer</a>
                        @else
                            Không có
                        @endif
                    </p>
                    <p><strong>📁 Danh mục:</strong> 
                        <span class="badge bg-warning text-dark">{{ $phim->danhMuc->ten ?? '—' }}</span>
                    </p>
                    <p><strong>🗣️ Ngôn ngữ:</strong> {{ $phim->ngonNgu->ten ?? '—' }}</p>
                    <p><strong>🎬 Đạo diễn:</strong> {{ $phim->dao_dien ?? '—' }}</p>
                    <p><strong>👥 Diễn viên:</strong> {{ $phim->dien_vien ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
