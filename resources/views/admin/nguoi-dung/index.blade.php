@extends('admin.layouts.admin')

@section('title', 'Quản lý Người dùng')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-people-fill"></i> Quản lý Người dùng
            </h2>
            <small class="text-muted">Xem, lọc và quản lý danh sách người dùng hệ thống</small>
        </div>
        <div>
            <a href="{{ route('admin.nguoi-dung.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-person-plus"></i> Thêm nhân viên mới
            </a>
        </div>
    </div>

    {{-- 🎚️ Bộ lọc vai trò --}}
    <div class="card shadow-sm mb-4 p-3 bg-light border-0">
        <div class="d-flex justify-content-center flex-wrap gap-2">
            <a href="{{ route('admin.nguoi-dung.index') }}" 
               class="btn btn-sm {{ request('vai_tro_id') ? 'btn-outline-secondary' : 'btn-secondary' }} rounded-pill">
               Tất cả
            </a>
            @foreach($vaiTros as $vt)
                @php
                    $style = match($vt->ten) {
                        'quan_ly' => 'success',
                        'nhan_vien' => 'info',
                        'khach_hang' => 'primary',
                        default => 'secondary'
                    };
                @endphp
                <a href="{{ route('admin.nguoi-dung.index', ['vai_tro_id' => $vt->id]) }}" 
                   class="btn btn-sm btn-outline-{{ $style }} rounded-pill {{ request('vai_tro_id') == $vt->id ? 'active' : '' }}">
                    {{ ucfirst($vt->ten) }}
                </a>
            @endforeach
        </div>
                </div>

    {{-- 📋 Bảng người dùng --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th>#</th>
                        <th class="text-start">Họ tên</th>
                        <th class="text-start">Email</th>
                        <th>SĐT</th>
                        <th>Vai trò</th>
                        <th>Điểm</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nguoiDung as $user)
                        <tr class="table-row">
                            <td class="text-center fw-bold text-muted">{{ $user->id }}</td>
                            <td class="fw-semibold text-start">{{ ucfirst($user->ho_ten) }}</td>
                            <td class="text-start">{{ $user->email }}</td>
                            <td>{{ $user->so_dien_thoai ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $user->vaiTro->ten == 'quan_ly' ? 'success' : ($user->vaiTro->ten == 'nhan_vien' ? 'info' : 'primary') }} bg-opacity-75 px-3 py-2 shadow-sm">
                                    {{ ucfirst($user->vaiTro->ten ?? '—') }}
                                </span>
                            </td>
                            <td class="fw-bold text-primary">
                                {{ number_format($user->diem ?? 0, 0, ',', '.') }}
                                <i class="bi bi-star-fill text-warning"></i>
                            </td>
                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                @if ($user->trang_thai)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Khóa</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.nguoi-dung.show', $user->id) }}" 
                                       class="btn btn-sm btn-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    
                                    <form action="{{ route('admin.nguoi-dung.toggle', $user->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái người dùng này?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                            class="btn btn-sm {{ $user->trang_thai ? 'btn-danger' : 'btn-primary' }} rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-{{ $user->trang_thai ? 'lock' : 'unlock' }}"></i>
                                            {{ $user->trang_thai ? 'Khóa' : 'Mở' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có người dùng nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- 🎨 CSS đồng bộ --}}
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
    letter-spacing: 0.3px;
    border-bottom: none !important;
}
.table td {
    padding: 1rem 1.2rem;
    vertical-align: middle;
}
.card {
    border-radius: 1rem;
}
</style>
@endsection
