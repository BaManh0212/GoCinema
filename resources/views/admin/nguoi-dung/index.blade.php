@extends('admin.layouts.admin')

@section('title', 'Quản lý Người dùng')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-users me-2"></i> Quản lý Người dùng
        </h2>
        <a href="{{ route('admin.nguoi-dung.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-user-plus me-1"></i> Thêm nhân viên mới
        </a>
    </div>

    {{-- Bộ lọc vai trò --}}
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.nguoi-dung.index') }}" 
           class="btn btn-sm {{ request('vai_tro_id') ? 'btn-outline-secondary' : 'btn-secondary' }}">
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
               class="btn btn-sm btn-outline-{{ $style }} {{ request('vai_tro_id') == $vt->id ? 'active' : '' }}">
                {{ ucfirst($vt->ten) }}
            </a>
        @endforeach
    </div>

    {{-- Bảng người dùng --}}
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light text-primary">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Vai trò</th>
                        <th>Điểm</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nguoiDung as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="fw-semibold">{{ ucfirst($user->ho_ten) }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->so_dien_thoai ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $user->vaiTro->ten == 'quan_ly' ? 'success' : ($user->vaiTro->ten == 'nhan_vien' ? 'info' : 'primary') }}">
                                    {{ $user->vaiTro->ten ?? '—' }}
                                </span>
                            </td>
                            <td class="fw-bold text-primary">
                                {{ number_format($user->diem ?? 0, 0, ',', '.') }}
                                <i class="fas fa-star text-warning ms-1"></i>
                            </td>
                            <td>
                                {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
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
                                       class="btn btn-sm btn-success" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.nguoi-dung.toggle', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                            class="btn btn-sm {{ $user->trang_thai ? 'btn-danger' : 'btn-success' }}" 
                                            title="{{ $user->trang_thai ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                            <i class="fas fa-{{ $user->trang_thai ? 'lock' : 'unlock' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted py-3">Không có người dùng nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
