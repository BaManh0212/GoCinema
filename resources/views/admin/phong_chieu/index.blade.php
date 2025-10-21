@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <i class="bi bi-door-open"></i> 🪑 Danh sách phòng chiếu
        </h2>
    </div>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-primary text-uppercase text-secondary">
                    <tr>
                        <th>STT</th>
                        <th>Tên phòng</th>
                       
                        <th>Định dạng</th>
                        <th>Tổng ghế</th>
                        <th>Trạng thái</th>
                        <th width="200px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($phongchieus as $key => $p)
                        <tr>
                            <td>{{ $phongchieus->firstItem() + $key }}</td>
                            <td class="text-start ps-4">{{ $p->ten }}</td>
                            <td>{{ $p->dinhDang?->ten ?? 'Không có' }}</td>
                            <td>{{ $p->tong_ghe }}</td>
                            <td>
                                @if($p->trang_thai == 'hoat_dong')
                                    <span class="badge bg-success">Hoạt động</span>
                                @elseif($p->trang_thai == 'bao_tri')
                                    <span class="badge bg-warning text-dark">Bảo trì</span>
                                @else
                                    <span class="badge bg-danger">Ngừng sử dụng</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.phongchieu.ghe', $p->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="bi bi-ui-checks"></i> Quản lý ghế
                                </a>
                                <a href="{{ route('admin.phongchieu.edit', $p->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <form action="{{ route('admin.phongchieu.destroy', $p->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa phòng này không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash3"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted py-5">
                                Không có phòng chiếu nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        <div class="card-footer d-flex justify-content-end">
            {{ $phongchieus->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
