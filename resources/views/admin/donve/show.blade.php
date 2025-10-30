@extends('admin.layouts.admin')

@section('title', '🎫 Chi tiết đơn đặt vé')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary mb-3">🎫 Chi tiết đơn: {{ $donVe->ma_don }}</h2>

    {{-- Hiển thị flash messages / lỗi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-secondary">Thông tin khách hàng</h5>
            <p><strong>Tên:</strong> {{ $donVe->nguoiDung->ten ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $donVe->nguoiDung->email ?? 'N/A' }}</p>
            <p><strong>Trạng thái:</strong> 
                <span class="badge bg-{{ $donVe->trang_thai == 'da_thanh_toan' ? 'success' : ($donVe->trang_thai == 'da_huy' ? 'danger' : 'secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $donVe->trang_thai)) }}
                </span>
            </p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-secondary">Thông tin suất chiếu</h5>
            <p><strong>Phim:</strong> {{ $donVe->suatChieu->phim->tieu_de ?? 'N/A' }}</p>
            <p><strong>Phòng chiếu:</strong> {{ $donVe->suatChieu->phongChieu->ten ?? 'N/A' }}</p>
            <p><strong>Giờ chiếu:</strong> {{ \Carbon\Carbon::parse($donVe->suatChieu->gio_bat_dau)->format('H:i d/m/Y') }}</p>
            <p><strong>Giá vé:</strong> {{ number_format($donVe->suatChieu->gia_ve, 0, ',', '.') }} đ</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-secondary">Danh sách vé</h5>
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã ghế</th>
                        <th>Loại ghế</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donVe->chiTietVes as $ct)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ct->ghe->hang ?? '' }}{{ $ct->ghe->cot ?? '' }}</td>
                            <td>{{ ucfirst($ct->loai_ghe) }}</td>
                            <td>{{ number_format($ct->gia, 0, ',', '.') }} đ</td>
                            <td>
                                <span class="badge bg-{{ $ct->trang_thai == 'da_thanh_toan' ? 'success' : ($ct->trang_thai == 'da_huy' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $ct->trang_thai)) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="mt-3 text-end fw-bold fs-5">
                Tổng tiền: <span class="text-danger">{{ number_format($donVe->tong_tien, 0, ',', '.') }} đ</span>
            </p>
        </div>
    </div>

    <div class="text-end">
        {{-- Form đổi trạng thái --}}
        <form action="{{ route('admin.donve.changeStatus', $donVe->id) }}" method="POST" class="d-inline-block me-2">
            @csrf
            <select name="trang_thai" class="form-select d-inline-block" style="width:220px; display:inline-block; vertical-align:middle;">
                <option value="cho_thanh_toan" {{ $donVe->trang_thai == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                <option value="da_thanh_toan" {{ $donVe->trang_thai == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="da_huy" {{ $donVe->trang_thai == 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
            </select>
            <button type="submit" class="btn btn-primary ms-2">Cập nhật trạng thái</button>
        </form>

        <a href="{{ route('admin.donve.print', $donVe->id) }}" class="btn btn-success">
            <i class="bi bi-printer"></i> In vé
        </a>
        <a href="{{ route('admin.donve.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>
@endsection
