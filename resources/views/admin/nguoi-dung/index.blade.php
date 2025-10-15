@extends('admin.layouts.admin')

@section('title', 'Quản lý Người dùng')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-users me-2"></i>Quản lý Người dùng</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.nguoi-dung.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm người dùng
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.nguoi-dung.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tìm kiếm tên, email, SĐT..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="vai_tro_id" class="form-select">
                            <option value="">Tất cả vai trò</option>
                            @foreach($vaiTros as $vt)
                                <option value="{{ $vt->id }}" {{ request('vai_tro_id') == $vt->id ? 'selected' : '' }}>
                                    {{ $vt->ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="trang_thai" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Khóa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort_by" class="form-select">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                            <option value="diem_tich_luy" {{ request('sort_by') == 'diem_tich_luy' ? 'selected' : '' }}>Điểm</option>
                            <option value="ho_ten" {{ request('sort_by') == 'ho_ten' ? 'selected' : '' }}>Tên</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Tìm kiếm
                        </button>
                        <a href="{{ route('admin.nguoi-dung.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 20%">Họ tên</th>
                            <th style="width: 20%">Email</th>
                            <th style="width: 12%">SĐT</th>
                            <th style="width: 10%">Vai trò</th>
                            <th style="width: 10%">Điểm</th>
                            <th style="width: 10%">Trạng thái</th>
                            <th style="width: 13%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nguoiDung as $nd)
                            <tr>
                                <td>{{ $nd->id }}</td>
                                <td>
                                    <strong>{{ $nd->ho_ten }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $nd->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>{{ $nd->email }}</td>
                                <td>{{ $nd->so_dien_thoai ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $nd->vaiTro->ten ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ number_format($nd->diem_tich_luy) }}</strong>
                                    <i class="fas fa-star text-warning"></i>
                                </td>
                                <td>
                                    @if($nd->trang_thai)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Khóa</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.nguoi-dung.show', $nd->id) }}" 
                                           class="btn btn-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.nguoi-dung.edit', $nd->id) }}" 
                                           class="btn btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.nguoi-dung.toggle-status', $nd->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary" 
                                                    title="{{ $nd->trang_thai ? 'Khóa' : 'Mở khóa' }}">
                                                <i class="fas fa-{{ $nd->trang_thai ? 'lock' : 'unlock' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.nguoi-dung.destroy', $nd->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
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
                {{ $nguoiDung->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
