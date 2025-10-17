@extends('admin.layouts.admin')

@section('title', '📋 Quản lý đơn đặt vé')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary mb-4">
        🎟️ Danh sách đơn đặt vé
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Bộ lọc trạng thái --}}
    <form method="GET" class="row mb-3">
        <div class="col-md-3">
            <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                <option value="">-- Lọc theo trạng thái --</option>
                <option value="cho_thanh_toan" {{ request('trang_thai') == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                <option value="dat_coc" {{ request('trang_thai') == 'dat_coc' ? 'selected' : '' }}>Đặt cọc</option>
                <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="da_huy" {{ request('trang_thai') == 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donDatVes as $don)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-primary">{{ $don->ma_don }}</td>
                            <td>{{ $don->nguoiDung->ten ?? 'N/A' }}</td>
                            <td>{{ $don->suatChieu->phim->tieu_de ?? 'N/A' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($don->suatChieu->gio_bat_dau)->format('H:i d/m/Y') }}
                            </td>
                            <td>{{ number_format($don->tong_tien, 0, ',', '.') }} đ</td>
                            <td>
                                @php
                                    $color = match($don->trang_thai) {
                                        'cho_thanh_toan' => 'secondary',
                                        'dat_coc' => 'warning',
                                        'da_thanh_toan' => 'success',
                                        'da_huy' => 'danger',
                                        default => 'dark'
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $don->trang_thai)) }}
                                </span>
                            </td>
                            <td>{{ $don->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.donve.show', $don->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                                <a href="{{ route('admin.donve.print', $don->id) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-printer"></i> In vé
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $donDatVes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
