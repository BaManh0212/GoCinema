@extends('client.layouts.app')

@section('title', 'Tin tức & Khuyến mãi - GoCinema')

@section('content')
<style>
    .text-gradient {
        background: linear-gradient(90deg, #a72121, #a72121);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .card-hover:hover img {
        transform: scale(1.08);
    }
    .card-hover {
        transition: all 0.3s ease-in-out;
    }
    .card-hover:hover {
        box-shadow: 0 0 20px rgba(171, 32, 41, 0.25);
    }

    /* Phân trang đẹp */
    .pagination .page-item .page-link {
        color: #fff;
        background-color: #343a40;
        border: none;
        margin: 0 2px;
    }
    .pagination .page-item.active .page-link {
        background-color: #a72121;
        border-color: #a72121;
    }
    .pagination .page-item .page-link:hover {
        background-color: #c63857;
        color: #fff;
    }
</style>

<div class="container py-5 text-light">
    <div class="row">
        {{-- Sidebar lọc loại --}}
        <div class="col-md-3 mb-4">
            <div class="card bg-dark text-white shadow-sm rounded-4 p-3">
                <h6 class="fw-bold mb-3 text-gradient">Lọc theo loại</h6>
                <form method="GET" action="{{ route('baiviet.index') }}">
                    @php $loaiRequest = (array) request('loai'); @endphp
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="loai[]" value="tin-tuc"
                               id="loaiTinTuc" {{ in_array('tin-tuc', $loaiRequest) ? 'checked' : '' }}>
                        <label class="form-check-label" for="loaiTinTuc">Tin tức</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="loai[]" value="khuyen-mai"
                               id="loaiKhuyenMai" {{ in_array('khuyen-mai', $loaiRequest) ? 'checked' : '' }}>
                        <label class="form-check-label" for="loaiKhuyenMai">Khuyến mãi</label>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm rounded-pill w-100 mt-2 text-dark fw-bold">Áp dụng</button>
                    @if(request('loai'))
                        <a href="{{ route('baiviet.index') }}" class="btn btn-outline-light btn-sm rounded-pill w-100 mt-1">Đặt lại</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Danh sách bài viết --}}
        <div class="col-md-9">
            <div class="row g-4">
                @forelse ($baiviets as $bai)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('baiviet.show', $bai->slug) }}" class="text-decoration-none text-white">
                            <div class="card h-100 border-0 shadow-sm bg-dark text-white overflow-hidden card-hover rounded-4">
                                @if($bai->hinh_anh)
                                    <img src="{{ asset('uploads/tintuc/' . $bai->hinh_anh) }}"
                                         class="card-img-top"
                                         style="height:230px;object-fit:cover; border-bottom: 2px solid #c63857;">
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-gradient fw-bold mb-2">{{ Str::limit($bai->tieu_de, 50) }}</h5>
                                    <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($bai->tom_tat, 100) }}</p>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-end">
                                    <small class="text-muted">📅 {{ $bai->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">
                        <i class="bi bi-inbox display-4 mb-3"></i>
                        <p>Hiện chưa có bài viết nào.</p>
                    </div>
                @endforelse
            </div>

            {{-- Thông tin số lượng & phân trang --}}
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
                <div class="text-muted mb-2 mb-md-0">
                    Hiển thị {{ $baiviets->firstItem() ?? 0 }} đến {{ $baiviets->lastItem() ?? 0 }} của {{ $baiviets->total() ?? 0 }} kết quả
                </div>
                <div>
                    {{ $baiviets->links('pagination::bootstrap-5') }}
                </div>
            </div>

            {{-- 3 phim mới nhất --}}
            <div class="mt-5">
                <h4 class="fw-bold mb-3 text-gradient">Phim mới nhất</h4>
                <div class="row g-4">
                    @foreach($baiviets->take(3) as $phim)
                        <div class="col-md-4">
                            <a href="{{ route('baiviet.show', $phim->slug) }}" class="text-decoration-none text-light">
                                <div class="card bg-dark border-0 shadow-sm rounded-4 card-hover">
                                    @if($phim->hinh_anh)
                                        <img src="{{ asset('uploads/tintuc/' . $phim->hinh_anh) }}"
                                             class="w-100 rounded-top-4"
                                             style="height:200px; object-fit:cover;">
                                    @endif
                                    <div class="card-body text-center">
                                        <h6 class="fw-semibold">{{ Str::limit($phim->tieu_de, 50) }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
