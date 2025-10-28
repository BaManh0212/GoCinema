@extends('admin.layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">
            <i class="fas fa-user-circle me-2"></i>Chi tiết người dùng
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.nguoi-dung.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
            <a href="{{ route('admin.nguoi-dung.edit', $nguoiDung->id) }}" class="btn btn-warning text-white">
                <i class="fas fa-edit me-1"></i>Sửa
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- ===== THÔNG TIN CƠ BẢN ===== --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-gradient bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin cơ bản</h5>
                </div>
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle bg-light border p-2 d-inline-flex justify-content-center align-items-center" style="width:110px; height:110px;">
                            <i class="fas fa-user fa-4x text-primary"></i>
                        </div>
                    </div>

                    <h4 class="fw-bold mt-2">{{ $nguoiDung->ho_ten }}</h4>
                    <p class="text-muted mb-1"><i class="fas fa-envelope me-1"></i>{{ $nguoiDung->email }}</p>
                    <p class="text-muted mb-3"><i class="fas fa-phone me-1"></i>{{ $nguoiDung->so_dien_thoai ?? 'Chưa cập nhật' }}</p>

                    <span class="badge bg-{{ $nguoiDung->trang_thai ? 'success' : 'danger' }} px-3 py-2 fs-6 mb-3">
                        <i class="fas {{ $nguoiDung->trang_thai ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                        {{ $nguoiDung->trang_thai ? 'Hoạt động' : 'Bị khóa' }}
                    </span>

                    <hr>

                    <div class="text-start">
                        <p class="mb-2"><strong>ID:</strong> {{ $nguoiDung->id }}</p>
                        <p class="mb-2">
                            <strong>Vai trò:</strong>
                            <span class="badge bg-info">{{ $nguoiDung->vaiTro->ten ?? 'N/A' }}</span>
                        </p>
                        <p class="mb-0"><strong>Ngày tạo:</strong> {{ $nguoiDung->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- ===== THỐNG KÊ ĐIỂM ===== --}}
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-header bg-gradient bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Thống kê điểm</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h6 class="text-muted">Điểm hiện tại</h6>
                        <h2 class="fw-bold text-primary">
                            <i class="fas fa-star text-warning me-2"></i>{{ number_format($nguoiDung->diem_tich_luy) }}
                        </h2>
                    </div>
                    <hr>
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="bg-success text-white rounded-3 p-3 shadow-sm">
                                <small class="fw-semibold d-block">Tổng tích lũy</small>
                                <h5 class="fw-bold mt-1 mb-0">+{{ number_format($tongTichLuy) }}</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-danger text-white rounded-3 p-3 shadow-sm">
                                <small class="fw-semibold d-block">Tổng sử dụng</small>
                                <h5 class="fw-bold mt-1 mb-0">-{{ number_format($tongSuDung) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== LỊCH SỬ GIAO DỊCH ĐIỂM ===== --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-gradient bg-warning py-3">
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
                            <table class="table align-middle table-hover">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Thời gian</th>
                                        <th>Loại</th>
                                        <th>Điểm</th>
                                        <th>Mô tả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichSuDiem as $index => $ls)
                                        <tr>
                                            <td class="text-center fw-semibold">{{ $lichSuDiem->firstItem() + $index }}</td>
                                            <td>
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                {{ $ls->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-center">
                                                @if($ls->hanh_dong == 'tich_luy')
                                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                        <i class="fas fa-arrow-up me-1"></i>Tích lũy
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                                        <i class="fas fa-arrow-down me-1"></i>Sử dụng
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($ls->hanh_dong == 'tich_luy')
                                                    <span class="fw-bold text-success">
                                                        +{{ number_format($ls->diem) }}
                                                    </span>
                                                @else
                                                    <span class="fw-bold text-danger">
                                                        -{{ number_format($ls->diem) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $ls->mo_ta ?: 'Không có mô tả' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

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
