@extends('layouts.app')

@section('title', 'Quản lý Điểm tích lũy')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-star me-2"></i>Quản lý Điểm tích lũy</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.diem-tich-luy.statistics') }}" class="btn btn-info me-2">
                <i class="fas fa-chart-bar me-1"></i>Thống kê
            </a>
            <a href="{{ route('admin.diem-tich-luy.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm/Trừ điểm
            </a>
        </div>
    </div>

    <!-- Thông báo -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6><i class="fas fa-users me-2"></i>Tổng người dùng</h6>
                    <h2>{{ number_format($tongNguoiDung) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6><i class="fas fa-arrow-up me-2"></i>Tổng tích lũy</h6>
                    <h2>+{{ number_format($tongTichLuy) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6><i class="fas fa-arrow-down me-2"></i>Tổng sử dụng</h6>
                    <h2>-{{ number_format($tongSuDung) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6><i class="fas fa-star me-2"></i>Tổng điểm hiện tại</h6>
                    <h2>{{ number_format($tongDiem) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.diem-tich-luy.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tìm kiếm tên hoặc email..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="hanh_dong" class="form-select">
                            <option value="">Tất cả loại</option>
                            <option value="tich_luy" {{ request('hanh_dong') == 'tich_luy' ? 'selected' : '' }}>Tích lũy</option>
                            <option value="su_dung" {{ request('hanh_dong') == 'su_dung' ? 'selected' : '' }}>Sử dụng</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tu_ngay" class="form-control" 
                               value="{{ request('tu_ngay') }}" placeholder="Từ ngày">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="den_ngay" class="form-control" 
                               value="{{ request('den_ngay') }}" placeholder="Đến ngày">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>
                        <a href="{{ route('admin.diem-tich-luy.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng lịch sử -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 20%">Người dùng</th>
                            <th style="width: 15%">Thời gian</th>
                            <th style="width: 12%">Loại</th>
                            <th style="width: 10%">Điểm</th>
                            <th style="width: 33%">Mô tả</th>
                            <th style="width: 5%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lichSuDiem as $ls)
                            <tr>
                                <td>{{ $ls->id }}</td>
                                <td>
                                    <a href="{{ route('admin.nguoi-dung.show', $ls->nguoi_dung_id) }}" 
                                       class="text-decoration-none">
                                        <strong>{{ $ls->nguoiDung->ho_ten ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $ls->nguoiDung->email ?? 'N/A' }}</small>
                                    </a>
                                </td>
                                <td>
                                    <i class="fas fa-clock text-muted me-1"></i>
                                    {{ $ls->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    @if($ls->hanh_dong == 'tich_luy')
                                        <span class="badge bg-success">
                                            <i class="fas fa-plus me-1"></i>Tích lũy
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-minus me-1"></i>Sử dụng
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($ls->hanh_dong == 'tich_luy')
                                        <strong class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i>+{{ number_format($ls->diem) }}
                                        </strong>
                                    @else
                                        <strong class="text-danger">
                                            <i class="fas fa-arrow-down me-1"></i>-{{ number_format($ls->diem) }}
                                        </strong>
                                    @endif
                                </td>
                                <td>{{ $ls->mo_ta ?: 'N/A' }}</td>
                                <td>
                                    <form action="{{ route('admin.diem-tich-luy.destroy', $ls->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa giao dịch này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    Không có dữ liệu
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $lichSuDiem->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
