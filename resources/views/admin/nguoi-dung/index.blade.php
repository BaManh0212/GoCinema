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
                <i class="fas fa-plus me-2"></i>Thêm nhân viên mới
            </a>
        </div>
    </div>

    {{-- Thông báo --}}
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

    {{-- Bảng danh sách --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 15%">Họ tên</th>
                            <th style="width: 20%">Email</th>
                            <th style="width: 10%">SĐT</th>
                            <th style="width: 10%">Vai trò</th>
                            <th style="width: 10%">Điểm</th>
                            <th style="width: 10%">Ngày tạo</th>
                            <th style="width: 10%">Trạng thái</th>
                            <th style="width: 10%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nguoiDung as $nd)
                            <tr class="text-center">
                                <td>{{ $nd->id }}</td>
                                <td class="text-start"><strong>{{ $nd->ho_ten }}</strong></td>
                                <td>{{ $nd->email }}</td>
                                <td>{{ $nd->so_dien_thoai ?? '—' }}</td>
                                <td><span class="badge bg-info">{{ $nd->vaiTro->ten ?? 'N/A' }}</span></td>
                                <td>
                                    <strong class="text-primary">{{ number_format($nd->diem_tich_luy) }}</strong>
                                    <i class="fas fa-star text-warning"></i>
                                </td>
                                <td>{{ $nd->created_at ? $nd->created_at->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    @if($nd->trang_thai)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Khóa</span>
                                    @endif
                                </td>
                                <td>
    {{-- Nút xem chi tiết --}}
    <a href="{{ route('admin.nguoi-dung.show', $nd->id) }}" 
       class="btn btn-info btn-sm me-1" title="Xem chi tiết">
        <i class="fas fa-eye"></i>
    </a>

    {{-- Nút khóa / mở khóa --}}
    @if($nd->trang_thai)
        <form action="{{ route('admin.nguoi-dung.toggle', $nd->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger btn-sm"
                    onclick="return confirm('Bạn có chắc muốn KHÓA tài khoản này không?')"
                    title="Khóa tài khoản">
                <i class="fas fa-lock"></i>
            </button>
        </form>
    @else
        <form action="{{ route('admin.nguoi-dung.toggle', $nd->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success btn-sm"
                    onclick="return confirm('Mở khóa tài khoản này?')"
                    title="Mở khóa tài khoản">
                <i class="fas fa-lock-open"></i>
            </button>
        </form>
    @endif
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    Không có dữ liệu
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $nguoiDung->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
