@extends('client.layouts.app')

@section('title', $baiviet->tieu_de)

@section('content')
<div class="container py-5 mt-5 text-light">

    {{-- Chi tiết bài viết --}}
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">

            {{-- Tiêu đề lớn, gradient --}}
            <h1 class="fw-bold mb-4 text-gradient" style="font-size:2.8rem; line-height:1.2;">
                {{ $baiviet->tieu_de }}
            </h1>

            {{-- Ngày đăng & ngày kết thúc --}}
            <div class="mb-3 text-muted small d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-1">
                    <i class="bi bi-calendar-event"></i>
                    <span>Ngày phát hành: {{ \Carbon\Carbon::parse($baiviet->ngay_phat_hanh)->format('d/m/Y') }}</span>
                </div>
                @if($baiviet->ngay_ket_thuc)
                <div class="d-flex align-items-center gap-1">
                    <i class="bi bi-calendar-x"></i>
                    <span>Ngày kết thúc: {{ \Carbon\Carbon::parse($baiviet->ngay_ket_thuc)->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>


            {{-- Hình ảnh bài viết --}}
            @if($baiviet->hinh_anh)
                <div class="mb-4 rounded overflow-hidden shadow-lg">
                    <img src="{{ asset('storage/' . $baiviet->hinh_anh) }}" 
                         class="img-fluid w-100" 
                         style="max-height:500px; object-fit:cover;">
                </div>
            @endif

            {{-- Nội dung --}}
            <div class="fs-5" style="line-height:1.8; color:#e0e0e0;">
                {!! nl2br(e($baiviet->noi_dung)) !!}
            </div>
        </div>
    </div>

    {{-- Bài viết liên quan --}}
    @if($lienquan->count())
    <div class="mt-5">
        <h3 class="text-white mb-4 fw-bold" style="font-size:1.8rem;">Bài viết liên quan</h3>
        <div class="row g-4">
            @foreach ($lienquan as $item)
                <div class="col-md-4">
                    <a href="{{ route('client.baiviet.show', $item->slug) }}" class="text-decoration-none text-light">
                        <div class="card bg-dark border-0 shadow-sm hover-card rounded-4 overflow-hidden">
                            <div class="overflow-hidden" style="height:180px;">
                                <img src="{{ asset('storage/' . $item->hinh_anh) }}" 
                                     class="w-100 h-100" 
                                     style="object-fit:cover; transition: transform .3s;">
                            </div>
                            <div class="card-body p-3">
                                <h6 class="mb-1 fw-semibold">{{ Str::limit($item->tieu_de, 60) }}</h6>
                                <small class="text-muted">Ngày phát hành: {{ $item->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- CSS chuyên nghiệp --}}
<style>
/* Gradient tiêu đề */
.text-gradient {
    background: linear-gradient(90deg, #ff416c, #ff4b2b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.text-muted {
    color: #adb5bd !important;
}

/* Card hover */
.hover-card {
    transition: all 0.3s ease-in-out;
}
.hover-card:hover img {
    transform: scale(1.07);
    transition: transform 0.3s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(255, 75, 43, 0.4);
}

/* Nội dung bài viết */
.fs-5 {
    font-size: 1.15rem;
}

/* Responsive spacing */
@media (max-width:768px) {
    h1.text-gradient {
        font-size: 2rem !important;
    }
    .hover-card div[style*="height:180px"] {
        height:150px !important;
    }
}
</style>
@endsection
