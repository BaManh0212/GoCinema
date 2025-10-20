@extends('admin.layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-user me-2"></i>Chi tiết người dùng</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.nguoi-dung.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
            <a href="{{ route('admin.nguoi-dung.edit', $nguoiDung->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Sửa
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Thông tin cơ bản</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4>{{ $nguoiDung->ho_ten }}</h4>
                    <p class="text-muted">{{ $nguoiDung->email }}</p>
                    
                    <div class="badge bg-{{ $nguoiDung->trang_thai ? 'success' : 'danger' }} fs-6 mb-3">
                        {{ $nguoiDung->trang_thai ? 'Hoạt động' : 'Khóa' }}
                    </div>

                    <hr>

                    <table class="table table-sm">
                        <tr>
                            <td class="text-start"><strong>ID:</strong></td>
                            <td class="text-end">{{ $nguoiDung->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-start"><strong>Vai trò:</strong></td>
                            <td class="text-end">
                                <span class="badge bg-info">{{ $nguoiDung->vaiTro->ten ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-start"><strong>SĐT:</strong></td>
                            <td class="text-end">{{ $nguoiDung->so_dien_thoai ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-start"><strong>Ngày tạo:</strong></td>
                            <td class="text-end">{{ $nguoiDung->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Thống kê điểm -->
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Thống kê điểm</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Điểm hiện tại</h6>
                        <h2 class="text-primary">
                            <i class="fas fa-star text-warning me-2"></i>
                            {{ number_format($nguoiDung->diem_tich_luy) }}
                        </h2>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <small>Tổng tích lũy</small>
                                    <h4>+{{ number_format($tongTichLuy) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body py-2">
                                    <small>Tổng sử dụng</small>
                                    <h4>-{{ number_format($tongSuDung) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.diem-tich-luy.create') }}?nguoi_dung_id={{ $nguoiDung->id }}" 
                           class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-plus me-1"></i>Thêm/Trừ điểm
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử điểm -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử giao dịch điểm</h5>
                </div>
                <div class="card-body">
                    @if($lichSuDiem->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có lịch sử giao dịch</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 20%">Thời gian</th>
                                        <th style="width: 15%">Loại</th>
                                        <th style="width: 15%">Điểm</th>
                                        <th style="width: 45%">Mô tả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichSuDiem as $index => $ls)
                                        <tr>
                                            <td>{{ $lichSuDiem->firstItem() + $index }}</td>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $lichSuDiem->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
