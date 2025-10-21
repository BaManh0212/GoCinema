@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <i class="bi bi-camera-reels"></i> 🎬 Danh sách suất chiếu
        </h2>
        <a href="{{ route('admin.suatchieu.create') }}" class="btn btn-primary rounded-3 shadow-sm">
            <i class="bi bi-plus-circle"></i> Thêm suất chiếu
        </a>
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
                        <th>Phim</th>
                        <th>Phòng chiếu</th>
                        <th>Giờ bắt đầu</th>
                        <th>Giờ kết thúc</th>
                        <th>Giá vé (VNĐ)</th>
                        <th width="200px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suatchieus as $key => $s)
                        <tr>
                            <td>{{ $suatchieus->firstItem() + $key }}</td>
                            <td class="text-start ps-4">{{ $s->phim?->tieu_de ?? 'Không có' }}</td>
                            <td>{{ $s->phong?->ten ?? 'Không có' }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->gio_bat_dau)->format('H:i d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->gio_ket_thuc)->format('H:i d/m/Y') }}</td>
                            <td>{{ number_format($s->gia_ve, 0, ',', '.') }}</td>
                            <td>
                                {{-- <a href="{{ route('admin.suatchieu.ghe', $s->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="bi bi-ui-checks"></i> Quản lý ghế
                                </a> --}}
                                <a href="{{ route('admin.suatchieu.edit', $s->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <form action="{{ route('admin.suatchieu.destroy', $s->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa suất chiếu này không?')">
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
                                Không có suất chiếu nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        <div class="card-footer d-flex justify-content-end">
            {{ $suatchieus->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
