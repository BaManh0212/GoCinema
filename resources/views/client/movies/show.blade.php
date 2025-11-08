@extends('layouts.client') {{-- đổi theo layout client của bạn --}}

@php
    // Hàm embed YouTube đơn giản
    function youtubeEmbed($url) {
        if (!$url) return null;
        // https://youtu.be/ID hoặc watch?v=ID
        if (preg_match('~youtu\.be/([^\?&]+)~', $url, $m)) $id = $m[1];
        elseif (preg_match('~v=([^&]+)~', $url, $m)) $id = $m[1];
        else $id = null;
        return $id ? "https://www.youtube.com/embed/$id" : null;
    }
    $embed = youtubeEmbed($phim->trailer);
@endphp

@section('title', $phim->tieu_de)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-4">
            @if($embed)
                <div class="aspect-video w-full rounded-lg overflow-hidden bg-black">
                    <iframe width="100%" height="100%" src="{{ $embed }}" title="Trailer"
                        frameborder="0" allowfullscreen></iframe>
                </div>
            @endif

            <h1 class="text-2xl font-bold">{{ $phim->tieu_de }}</h1>

            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span>Thời lượng: {{ $phim->thoi_luong ?? '—' }} phút</span>
                <span>•</span>
                <span>Khởi chiếu: {{ $phim->ngay_cong_chieu ? \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') : '—' }}</span>
                <span>•</span>
                <span>Định dạng: {{ $phim->dinh_dang ?? '2D' }}</span>
            </div>

            <p class="text-gray-800 leading-7">{{ $phim->mo_ta }}</p>

            {{-- LỊCH CHIẾU --}}
            <div class="mt-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-lg">Lịch chiếu 7 ngày tới</h2>
                    <a href="#lichchieu" class="text-blue-600 text-sm">Làm mới</a>
                </div>

                @if($lichChieuTheoNgay->isEmpty())
                    <div class="p-4 border rounded-md text-gray-600">Hiện chưa có lịch chiếu.</div>
                @else
                    <div id="lichchieu" class="space-y-4">
                        @foreach($lichChieuTheoNgay as $ngay => $ds)
                            <div class="border rounded-lg p-4">
                                <div class="font-medium mb-2">
                                    {{ \Carbon\Carbon::parse($ngay)->isoFormat('dddd, DD/MM/YYYY') }}
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($ds as $s)
                                        @php
                                            $gio = \Carbon\Carbon::parse($s->gio_bat_dau)->format('H:i');
                                            $rap = $s->phong->rap->ten ?? null;
                                            $phong = $s->phong->ten ?? null;
                                        @endphp
                                        <a href="{{ url('/booking?suat_chieu_id='.$s->id) }}"
                                           class="inline-flex items-center border px-3 py-2 rounded-md hover:bg-gray-50">
                                            <span class="font-semibold">{{ $gio }}</span>
                                            @if($rap || $phong)
                                                <span class="ml-2 text-sm text-gray-600">{{ $rap ? $rap.' • ' : '' }}{{ $phong }}</span>
                                            @endif
                                            <span class="ml-3 text-sm text-gray-700">{{ number_format($s->gia_ve, 0, ',', '.') }}đ</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- CỘT PHẢI: Poster + Điểm --}}
        <aside class="space-y-4">
            <div class="border rounded-lg overflow-hidden">
                <img src="{{ asset($phim->anh_poster ?? 'images/no-poster.jpg') }}" alt="Poster" class="w-full">
            </div>
            <div class="border rounded-lg p-4">
                <div class="text-sm text-gray-600 mb-1">Đánh giá trung bình</div>
                <div class="text-3xl font-bold">{{ number_format($diemTB, 1) }}<span class="text-base">/5</span></div>
                <div class="text-sm text-gray-500">{{ $soDanhGia }} lượt đánh giá</div>
            </div>
        </aside>
    </div>

    {{-- BÌNH LUẬN / ĐÁNH GIÁ --}}
    <div class="mt-10">
        <h2 class="font-semibold text-lg mb-3">Bình luận & đánh giá</h2>

        @auth
        <form action="{{ route('phim.danh_gia.luu', $phim->slug) }}" method="POST" class="border rounded-lg p-4 mb-6">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Số sao</label>
                <select name="so_sao" class="border rounded-md px-3 py-2">
                    @for($i=1;$i<=5;$i++)
                        <option value="{{ $i }}">{{ $i }} sao</option>
                    @endfor
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Bình luận</label>
                <textarea name="binh_luan" rows="3" class="w-full border rounded-md px-3 py-2" placeholder="Cảm nhận của bạn..."></textarea>
            </div>
            <button class="bg-red-600 text-white px-4 py-2 rounded-md">Gửi đánh giá</button>
        </form>
        @else
            <div class="p-4 border rounded-md text-gray-700 mb-6">
                Vui lòng <a href="{{ url('/dang-nhap') }}" class="text-blue-600 underline">đăng nhập</a> để gửi đánh giá.
            </div>
        @endauth

        @php
            $danhgias = $phim->danhGias()->with('nguoiDung')->latest()->take(20)->get();
        @endphp

        <div class="space-y-4">
            @forelse($danhgias as $dg)
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-1">
                        <div class="font-medium">{{ $dg->nguoiDung->name ?? 'Người dùng' }}</div>
                        <div class="text-sm text-gray-600">{{ $dg->created_at?->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="text-yellow-500 mb-1">{{ str_repeat('★', (int)$dg->so_sao) }}{{ str_repeat('☆', 5-(int)$dg->so_sao) }}</div>
                    <div class="text-gray-800">{{ $dg->binh_luan }}</div>
                </div>
            @empty
                <div class="p-4 border rounded-md text-gray-600">Chưa có đánh giá nào.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
