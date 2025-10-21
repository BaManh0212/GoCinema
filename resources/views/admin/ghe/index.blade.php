@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary mb-3">
        🎟️ Quản lý ghế - {{ $phong->ten }}
    </h2>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('admin.phongchieu.index') }}" class="btn btn-secondary">
                ← Quay lại danh sách phòng chiếu
            </a>

            {{-- Form thêm ghế --}}
            <form action="{{ route('admin.phongchieu.ghe.store', $phong->id) }}" method="POST" class="d-flex gap-2">
                @csrf
                <input type="text" name="hang" class="form-control" placeholder="Hàng (A, B...)" style="width:100px">
                <input type="number" name="cot" class="form-control" placeholder="Cột (1,2...)" style="width:100px">
                <select name="loai" class="form-select" style="width:130px">
                    <option value="thuong">Thường</option>
                    <option value="vip">VIP</option>
                    <option value="doi">Đôi</option>
                </select>
                <button class="btn btn-primary">➕ Thêm ghế</button>
            </form>
        </div>

        {{-- Sơ đồ ghế --}}
        <div class="seat-map text-center mt-4">
            @forelse($ghes as $hang => $danhSachGhe)
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <strong class="me-3">{{ $hang }}</strong>

                    {{-- Dãy 1 --}}
                    <div class="d-flex me-5">
                        @foreach($danhSachGhe->slice(0, 5) as $ghe)
                            <form action="{{ route('admin.phongchieu.ghe.destroy', $ghe->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa ghế {{ $hang }}{{ $ghe->cot }} không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="seat border-0 
                                    @if($ghe->trang_thai === 'hong') bg-danger
                                    @elseif($ghe->trang_thai === 'bao_tri') bg-warning
                                    @else bg-success @endif
                                    text-white rounded-2 me-1"
                                    style="width:40px; height:40px;" 
                                    title="Xóa ghế {{ $hang }}{{ $ghe->cot }}">
                                    {{ $ghe->cot }}
                                </button>
                            </form>
                        @endforeach
                    </div>

                    {{-- Dãy 2 --}}
                    <div class="d-flex ms-5">
                        @foreach($danhSachGhe->slice(5) as $ghe)
                            <form action="{{ route('admin.phongchieu.ghe.destroy', $ghe->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa ghế {{ $hang }}{{ $ghe->cot }} không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="seat border-0 
                                    @if($ghe->trang_thai === 'hong') bg-danger
                                    @elseif($ghe->trang_thai === 'bao_tri') bg-warning
                                    @else bg-success @endif
                                    text-white rounded-2 me-1"
                                    style="width:40px; height:40px;" 
                                    title="Xóa ghế {{ $hang }}{{ $ghe->cot }}">
                                    {{ $ghe->cot }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-muted">Chưa có ghế nào trong phòng này.</p>
            @endforelse
        </div>
    </div>
</div>

<style>
.seat {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.2s;
}
.seat:hover {
    transform: scale(1.1);
    opacity: 0.8;
}
</style>
@endsection
