@extends('admin.layouts.admin')

@section('title', '📋 Quản lý Đơn Đặt Vé')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-ticket-detailed"></i> Quản lý Đơn Đặt Vé
            </h2>
            <small class="text-muted">Xem, lọc và quản lý danh sách đơn đặt vé của khách hàng</small>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.donve.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select name="trang_thai" class="form-select rounded-pill">
                        <option value="">-- Lọc theo trạng thái --</option>
                        <option value="cho_thanh_toan" {{ request('trang_thai') == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="qua_han" {{ request('trang_thai') == 'qua_han' ? 'selected' : '' }}>Quá hạn</option>
                        <option value="da_huy" {{ request('trang_thai') == 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>

                <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-4 me-2">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                    <a href="{{ route('admin.donve.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm px-4">
                        <i class="bi bi-arrow-clockwise"></i> Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 📋 Bảng dữ liệu --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th style="width: 60px;">#</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th style="width: 200px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donDatVes as $don)
                        @php
                            $color = match($don->trang_thai) {
                                'cho_thanh_toan' => 'secondary',
                                'da_thanh_toan' => 'success',
                                'da_huy' => 'danger',
                                'qua_han' => 'warning',
                                'da_checkin' => 'info',
                                default => 'dark'
                            };
                        @endphp

                        <tr class="table-row text-center">
                            <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-primary">{{ $don->ma_don }}</td>
                            <td>{{ $don->nguoiDung->ho_ten ?? 'N/A' }}</td>
                            <td>{{ $don->suatChieu->phim->tieu_de ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($don->suatChieu->gio_bat_dau)->format('H:i d/m/Y') }}</td>
                            <td class="fw-semibold">{{ number_format($don->tong_tien, 0, ',', '.') }} đ</td>

                            <td>
                                <span class="badge-status bg-{{ $color }}">
                                    @if($don->trang_thai === 'qua_han')
                                        Quá hạn
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $don->trang_thai)) }}
                                    @endif
                                </span>
                            </td>

                            <td>{{ $don->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($don->trang_thai === 'da_huy')
                                        <button class="btn btn-sm btn-secondary rounded-pill px-3 shadow-sm" disabled title="Đơn đã hủy">
                                            <i class="bi bi-eye"></i> Xem
                                        </button>
                                        <button class="btn btn-sm btn-secondary rounded-pill px-3 shadow-sm" disabled title="Đơn đã hủy">
                                            <i class="bi bi-printer"></i> In vé
                                        </button>
                                    @else
                                        <a href="{{ route('admin.donve.show', $don->id) }}" class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        @php $canPrint = in_array($don->trang_thai, ['da_thanh_toan','da_checkin']); @endphp
                                        @if($canPrint)
                                            <a href="{{ route('admin.donve.print', $don->id) }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                <i class="bi bi-printer"></i> In vé
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" disabled title="Chỉ in khi đã thanh toán hoặc đã check-in">
                                                <i class="bi bi-printer"></i> In vé
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có đơn đặt vé nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 📄 Phân trang --}}
    <div class="mt-3">
        {{ $donDatVes->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.table-header {
    background: linear-gradient(90deg, #007bff, #00c3ff);
}

.table-row {
    background-color: #fff;
    transition: all 0.25s ease-in-out;
}
.table-row:nth-child(even) {
    background-color: #f8f9fa;
}
.table-row:hover {
    background-color: #e9f5ff;
    transform: scale(1.01);
}

.table th {
    font-weight: 600;
    border-bottom: none !important;
}
.table td {
    padding: 1rem 1.2rem;
    vertical-align: middle;
}

.card {
    border-radius: 1rem;
}

/* 🌈 Badge trạng thái */
.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: capitalize;
    min-width: 100px;
    display: inline-block;
    text-align: center;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.badge-status.bg-secondary {
    background: linear-gradient(135deg, #a0a4ab, #7a7e85);
}
.badge-status.bg-success {
    background: linear-gradient(135deg, #00b09b, #96c93d);
}
.badge-status.bg-danger {
    background: linear-gradient(135deg, #ff4b2b, #ff416c);
}
.badge-status.bg-dark {
    background: linear-gradient(135deg, #232526, #414345);
}
</style>
@endsection
