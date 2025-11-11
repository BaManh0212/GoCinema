@extends('client.layouts.app')

@section('title', 'Tin tức & Khuyến mãi - GoCinema')

@section('content')
<div class="container py-5 text-light">
    <h2 class="fw-bold mb-5 text-center text-gradient">Tin tức & Khuyến mãi</h2>

    <div class="row">
        {{-- 🔹 Sidebar lọc loại --}}
        <div class="col-md-3 mb-4">
            <div class="card bg-dark text-white shadow-sm rounded-4 p-3">
                <h6 class="fw-bold mb-3 text-gradient">Lọc theo loại</h6>
                <form method="GET" action="{{ route('baiviet.index') }}">
                    @php
                        $loaiRequest = (array) request('loai');
                    @endphp
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
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill w-100 mt-2">Áp dụng</button>
                    @if(request('loai'))
                        <a href="{{ route('baiviet.index') }}" class="btn btn-outline-light btn-sm rounded-pill w-100 mt-1">Đặt lại</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- 🔹 Danh sách bài viết --}}
        <div class="col-md-9">
            <div class="row g-4">
                @forelse ($baiviets as $bai)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm bg-dark text-white overflow-hidden card-hover">
                            @if($bai->hinh_anh)
                                <img src="{{ asset('storage/' . $bai->hinh_anh) }}" 
                                     class="card-img-top" 
                                     style="height:200px;object-fit:cover;transition: transform 0.3s;">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-gradient fw-bold mb-2">{{ Str::limit($bai->tieu_de, 50) }}</h5>
                                <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($bai->tom_tat, 100) }}</p>
                                <a href="{{ route('baiviet.show', $bai->slug) }}" 
                                   class="btn btn-danger btn-sm rounded-pill mt-auto shadow-sm">
                                    Đọc thêm <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-end">
                                <small class="text-muted">Ngày: {{ $bai->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">
                        <i class="bi bi-inbox display-4 mb-3"></i>
                        <p>Hiện chưa có bài viết nào.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $baiviets->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>


@endsection
