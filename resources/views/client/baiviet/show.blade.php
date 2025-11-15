@extends('client.layouts.app')

@section('title', $baiviet->tieu_de)

@section('content')
<style>
.text-gradient {
    background: linear-gradient(90deg, #a72121, #a72121);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.hover-card:hover img {
    transform: scale(1.08);
}
</style>

<div class="container py-5 text-light">

    {{-- Banner đầu bài --}}
    <div class="text-center mb-5">
        <div class="rounded-4 overflow-hidden shadow-lg mx-auto" style="max-width: 800px;">
            @if($baiviet->hinh_anh)
                <img src="{{ asset('uploads/tintuc/' . $baiviet->hinh_anh) }}" class="img-fluid w-100"
                     style="max-height:450px;object-fit:cover;">
            @endif
        </div>
    </div>

    {{-- Thông tin bài viết --}}
    <div class="col-lg-8 mx-auto mb-5 bg-dark p-4 rounded-4 shadow-sm">
        <h1 class="fw-bold text-gradient mb-3">{{ $baiviet->tieu_de }}</h1>
        <ul class="list-unstyled mb-4">
            <li>⏱️ <strong>Thời lượng:</strong> {{ $baiviet->thoi_luong ?? '—' }} phút</li>
            <li>📅 <strong>Ngày công chiếu:</strong> {{ $baiviet->ngay_phat_hanh ? \Carbon\Carbon::parse($baiviet->ngay_phat_hanh)->format('d/m/Y') : '—' }}</li>
            <li>📅 <strong>Ngày kết thúc:</strong> {{ $baiviet->ngay_ket_thuc ? \Carbon\Carbon::parse($baiviet->ngay_ket_thuc)->format('d/m/Y') : '—' }}</li>
            <li>🎬 <strong>Đạo diễn:</strong> {{ $baiviet->dao_dien ?? '—' }}</li>
            <li>👥 <strong>Diễn viên:</strong> {{ $baiviet->dien_vien ?? '—' }}</li>
            <li>🗣️ <strong>Ngôn ngữ:</strong> {{ $baiviet->ngon_ngu ?? '—' }}</li>
            <li>📁 <strong>Danh mục:</strong> {{ ucfirst($baiviet->loai) }}</li>
            <li>💾 <strong>Định dạng:</strong> {{ $baiviet->dinh_dang ?? '2D' }}</li>
            <li>💬 <strong>Phụ đề:</strong> {{ $baiviet->phu_de ?? 'Không' }}</li>
            <li>🔞 <strong>Giới hạn tuổi:</strong> {{ $baiviet->gioi_han_tuoi ?? 'P' }}</li>
        </ul>
        <div class="fs-5" style="line-height:1.8; color:#e0e0e0;">
            {!! nl2br(e($baiviet->noi_dung)) !!}
        </div>
    </div>

    {{-- Bài viết liên quan --}}
    @if($lienquan->count())
        <div class="mt-5">
            <h3 class="text-gradient mb-4 fw-bold text-center">Bài viết liên quan</h3>
            <div class="row g-4">
                @foreach ($lienquan as $item)
                    <div class="col-md-4">
                        <a href="{{ route('baiviet.show', $item->slug) }}" class="text-decoration-none text-light">
                            <div class="card bg-dark border-0 shadow-sm hover-card rounded-4 overflow-hidden">
                                @if($item->hinh_anh)
                                    <img src="{{ asset('uploads/tintuc/' . $item->hinh_anh) }}"
                                         class="w-100"
                                         style="height:180px;object-fit:cover;">
                                @endif
                                <div class="card-body p-3">
                                    <h6 class="fw-semibold mb-1">{{ Str::limit($item->tieu_de, 60) }}</h6>
                                    <small class="text-muted">📅 {{ $item->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection
