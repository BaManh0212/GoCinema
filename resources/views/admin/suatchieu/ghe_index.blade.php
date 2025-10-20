@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🎫 Quản lý ghế: Suất chiếu {{ $suatchieu->phim->tieu_de }} ({{ $suatchieu->gio_bat_dau }})</h2>

    <div class="d-flex flex-column align-items-start">
        @foreach($ghes as $hang => $danhSachGhe)
            <div class="mb-2 d-flex">
                <span class="me-3 fw-bold">{{ $hang }}</span>
                @foreach($danhSachGhe as $ghe)
                    @php
                        $daDat = in_array($ghe->id, $giuTamIds);
                        $class = $daDat ? 'btn-primary' : 'btn-light';
                        if($ghe->trang_thai == 'hong') $class = 'btn-danger';
                        elseif($ghe->trang_thai == 'bao_tri') $class = 'btn-warning text-dark';
                    @endphp
                    <div 
                        class="btn btn-sm me-1 mb-1 {{ $class }}" 
                        style="width: 40px; height: 40px; padding: 0;"
                        title="Ghế {{ $hang }}{{ $ghe->cot }} - Loại: {{ $ghe->loai }}"
                    >
                        {{ $ghe->cot }}
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection
