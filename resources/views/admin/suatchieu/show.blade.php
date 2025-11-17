@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h3 class="mb-3">🎬 Sơ đồ ghế – Suất chiếu: {{ $suatChieu->id }} – Phòng: {{ $phong->ten }}</h3>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Legend --}}
    <div class="mb-3">
        <span class="legend-item seat-thuong"></span> Ghế thường
        <span class="legend-item seat-vip"></span> Ghế VIP
        <span class="legend-item seat-doi"></span> Ghế đôi
        <span class="legend-item seat-bao-tri"></span> Bảo trì
        <span class="legend-item seat-dat"></span> Đã đặt
    </div>

    {{-- Sơ đồ ghế --}}
    <div class="card p-3 overflow-auto mb-4">
        @php
            $matrix2D = [];
            $nextId = 1;
            foreach ($matrix as $seat) {
                if(!isset($seat['id'])) $seat['id'] = $nextId++;
                $hang = $seat['hang'] ?? 'X';
                $cot = $seat['cot'] ?? 0;
                $matrix2D[$hang][$cot] = $seat;
            }
            ksort($matrix2D);
            foreach ($matrix2D as &$row) ksort($row);
        @endphp
        @foreach($matrix2D as $row)
            <div class="d-flex mb-2 justify-content-center">
                @foreach($row as $seat)
                    @php
                        $loai = $seat['loai'] ?? 'thuong';
                        $class = match($loai){
                            'vip'=>'seat-vip',
                            'doi'=>'seat-doi',
                            default=>'seat-thuong'
                        };
                        $trangThai = $seat['trang_thai'] ?? 'hoat_dong';
                        $gheId = $seat['id'];
                        if(isset($trangThaiGhe[$gheId]) && $trangThaiGhe[$gheId]==='da_dat') $trangThai='da_dat';
                        if($trangThai==='bao_tri') $class.=' seat-bao-tri';
                        if($trangThai==='da_dat') $class.=' seat-dat';
                    @endphp

                    <div class="seat {{ $class }}">
                        {{ $seat['hang'] }}-{{ $seat['cot'] }}
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
.seat{width:55px;height:55px;border:1px solid #ccc;margin:3px;text-align:center;line-height:55px;color:white;font-weight:bold;border-radius:6px;flex-shrink:0;}
.seat-thuong{background:#91b8f3;}
.seat-vip{background:#94e774;}
.seat-doi{background:#4dbd72;}
.seat-bao-tri{background:#6c757d !important;}
.seat-dat{background:#ff7f50 !important;}
.legend-item{display:inline-block;width:25px;height:25px;margin-right:8px;vertical-align:middle;border-radius:4px;}
</style>
@endpush
