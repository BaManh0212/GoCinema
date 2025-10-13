@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <i class="bi bi-building"></i> 🎬 Danh sách rạp chiếu
        </h2>
        <a href="{{ route('admin.rap.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm rạp
        </a>
    </div>

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
                        <th>Tên rạp</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th width="220px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($raps as $key => $r)
                        <tr>
                            <td>{{ $raps->firstItem() + $key }}</td>
                            <td class="text-start ps-4">{{ $r->ten }}</td>
                            <td class="text-start ps-4">{{ $r->dia_chi }}</td>
                            <td>{{ $r->so_dien_thoai }}</td>
                            <td>{{ $r->email }}</td>
                            <td>
                                <a href="{{ route('admin.rap.edit', $r->id) }}" 
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                      <i class="bi bi-trash3"></i> Xóa
                                </button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-5">
                                Không có rạp nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer d-flex justify-content-end">
            {{ $raps->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
