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
                    <p><strong>🔗 Slug:</strong> {{ $phim->slug }}</p>
                    <p><strong>📜 Mô tả:</strong> {{ $phim->mo_ta ?? '—' }}</p>
                    <p><strong>⏱️ Thời lượng:</strong> {{ $phim->thoi_luong }} phút</p>
                    <p><strong>📅 Ngày công chiếu:</strong> {{ $phim->ngay_cong_chieu ? \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') : '—' }}</p>
                    <p><strong>📅 Ngày kết thúc:</strong> {{ $phim->ngay_ket_thuc ? \Carbon\Carbon::parse($phim->ngay_ket_thuc)->format('d/m/Y') : '—' }}</p>
                    <p><strong>🎬 Đạo diễn:</strong> {{ $phim->dao_dien ?? '—' }}</p>
                    <p><strong>👥 Diễn viên:</strong> {{ $phim->dien_vien ?? '—' }}</p>
                    <p><strong>🗣️ Ngôn ngữ:</strong> {{ $phim->ngonNgu->ten ?? '—' }}</p>
                    <p><strong>🔞 Giới hạn tuổi:</strong> {{ $phim->do_tuoi_gioi_han ?? 'P' }}</p>
                    <p><strong>📐 Định dạng:</strong> {{ $phim->dinh_dang ?? '2D' }}</p>
                    <p><strong>🎛️ Trạng thái:</strong>
                        @php
                            $trangThai = $phim->trang_thai_tu_dong;
                            $class = match($trangThai) {
                            'Sắp chiếu' => 'bg-info text-dark',
                            'Đang chiếu' => 'bg-success',
                            'Ngưng chiếu' => 'bg-secondary text-white',
                            default => 'bg-light text-dark'
                            };
                        @endphp

                        <span class="badge {{ $class }}">{{ $trangThai }}</span>
                    </p>
                    <p><strong>⭐ Đánh giá:</strong> {{ $phim->danh_gia ?? 0 }} / 10</p>
                    <p><strong>👁️ Lượt xem:</strong> {{ $phim->luot_xem ?? 0 }}</p>
                    <p><strong>📁 Danh mục:</strong> 
                        @if($phim->danhMucs && $phim->danhMucs->count())
                        @foreach($phim->danhMucs as $dm)
                            <span class="badge bg-warning text-dark">{{ $dm->ten }}</span>
                        @endforeach
                        @else
                            <span class="text-muted">—</span>
                        @endif
                        </p>
                    <p><strong>🎞️ Trailer:</strong> 
                        @if($phim->trailer)
                            <a href="{{ $phim->trailer }}" target="_blank" class="text-decoration-none">Xem trailer</a>
                        @else
                            Không có
                        @endif
                    </p>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
