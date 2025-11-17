@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h3 class="mb-3">👁 Xem sơ đồ ghế – Phòng: {{ $phong->ten }}</h3>

    {{-- Nút lưu trạng thái --}}
    <button id="saveSeats" class="btn btn-primary mb-3">💾 Cập nhật trạng thái ghế</button>

    {{-- Legend --}}
    <div class="mb-3">
        <span class="legend-item seat-thuong"></span> Ghế thường
        <span class="legend-item seat-vip"></span> Ghế VIP
        <span class="legend-item seat-doi"></span> Ghế đôi
        <span class="legend-item seat-bao-tri"></span> Bảo trì
    </div>

    <div class="card p-3 overflow-auto">
        @php
            $matrix2D = [];
            foreach ($matrix as $seat) {
                $matrix2D[$seat['hang']][$seat['cot']] = $seat;
            }
        @endphp

        @foreach($matrix2D as $row)
            <div class="d-flex mb-2 justify-content-center">
                @foreach($row as $seat)
                    @php
                        $class = match($seat['loai']) {
                            'vip' => 'seat-vip',
                            'doi' => 'seat-doi',
                            default => 'seat-thuong'
                        };
                        if(($seat['trang_thai'] ?? '') == 'bao_tri'){
                            $class .= ' seat-bao-tri';
                        }
                    @endphp

                    <div class="seat {{ $class }}"
                         data-hang="{{ $seat['hang'] }}"
                         data-cot="{{ $seat['cot'] }}"
                         data-loai="{{ $seat['loai'] }}"
                         data-trangthai="{{ $seat['trang_thai'] ?? 'hoat_dong' }}">
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
    /* Ghế */
    .seat { 
        width:55px; height:55px; border:1px solid #ccc; margin:3px; 
        text-align:center; line-height:55px; color:white; cursor:pointer;
        font-weight:bold; border-radius:6px;
        transition:0.2s;
        flex-shrink:0;
    }
    .seat:hover { transform: scale(1.15); box-shadow: 0 0 8px #00000040; }

    /* Loại ghế */
    .seat-thuong { background: #91b8f3; } 
    .seat-vip    { background: #94e774; } 
    .seat-doi    { background: #4dbd72; } 

    /* Trạng thái */
    .seat-bao-tri { background: #6c757d !important; }

    /* Legend */
    .legend-item { display:inline-block; width:25px; height:25px; margin-right:8px; vertical-align:middle; border-radius:4px; }
</style>
@endpush

@push('scripts')
<script>
const seats = document.querySelectorAll('.seat');

// Click vào ghế → chuyển trạng thái bảo trì
seats.forEach(seat => {
    seat.addEventListener('click', function(){
        let status = this.dataset.trangthai;
        status = (status === 'bao_tri') ? 'hoat_dong' : 'bao_tri';
        this.dataset.trangthai = status;

        this.classList.remove('seat-bao-tri');
        if(status === 'bao_tri') this.classList.add('seat-bao-tri');
    });
});

// Lưu trạng thái vào DB
document.getElementById('saveSeats').addEventListener('click', function(){
    const matrixUpdate = [];
    seats.forEach(seat => {
        matrixUpdate.push({
            hang: seat.dataset.hang,
            cot: seat.dataset.cot,
            trang_thai: seat.dataset.trangthai
        });
    });

    fetch("{{ route('admin.admin.sodo.updateSeatStatus') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            phong_id: {{ $phong->id }},
            matrix: matrixUpdate
        })
    }).then(res => res.json())
      .then(res => {
          if(res.success) alert('Đã cập nhật trạng thái ghế thành công!');
      });
});
</script>
@endpush
