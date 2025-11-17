@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-folder2-open"></i> Quản lý phòng chiếu
            </h2>
            <small class="text-muted">Xem, lọc và quản lý các phòng chiếu</small>
        </div>
        <div>
            <a href="{{ route('admin.phongchieu.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm phòng chiếu
            </a>
        </div>
    </div>

  {{-- 🔍 Bộ lọc --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.phongchieu.index') }}" class="row g-3 align-items-center">

            {{-- Ô tìm kiếm --}}
            <div class="col-md-4">
                <input type="text" name="q" class="form-control"
                    placeholder="Tìm theo tên hoặc mã phòng"
                    value="{{ request('q') }}">
            </div>

            {{-- Lọc theo rạp chiếu (nếu có) --}}
            {{-- <div class="col-md-3">
                <select name="rap_id" class="form-select">
                    <option value="">-- Chọn rạp chiếu --</option>
                    @foreach($raps as $rap)
                        <option value="{{ $rap->id }}" {{ request('rap_id') == $rap->id ? 'selected' : '' }}>
                            {{ $rap->ten_rap }}
                        </option>
                    @endforeach
                </select>
            </div> --}}

            {{-- Sắp xếp --}}
            <div class="col-md-3">
                <select name="sort" class="form-select rounded-pill">
                    <option value="">-- Sắp xếp theo --</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên (A → Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên (Z → A)</option>
                    <option value="seats_desc" {{ request('sort') == 'seats_desc' ? 'selected' : '' }}>Số ghế nhiều nhất</option>
                    <option value="seats_asc" {{ request('sort') == 'seats_asc' ? 'selected' : '' }}>Số ghế ít nhất</option>
                    <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Cũ nhất</option>
                </select>
            </div>

            {{-- Nút hành động --}}
             <div class="ms-auto text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">Tìm kiếm</button>
                    <a href="{{ route('admin.phongchieu.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">Đặt lại</a>
            </div>

        </form>
    </div>
</div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-primary text-uppercase text-secondary">
                    <tr>
                        <th>STT</th>
                        <th>Tên phòng</th>
                        <th>Mã phòng</th>
                        <th>Định dạng</th>
                        <th>Trạng thái</th>
                        <th width="200px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($phongchieus as $key => $p)
                        <tr>
                            <td>{{ $phongchieus->firstItem() + $key }}</td>
                            <td class="text-start ps-4">{{ $p->ten }}</td>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->dinhDang?->ten ?? 'Không có' }}</td>
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
                                <a href="{{ route('admin.admin.sodoghe.show', $p->id) }}" class="btn btn-sm btn-outline-info me-1">
        <i class="bi bi-grid"></i> Sơ đồ ghế
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

{{-- 🎨 CSS tùy chỉnh --}}
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

.btn-light {
    background-color: #f8f9fa;
    border-color: #ced4da;
    transition: all 0.2s ease;
}
.btn-light:hover {
    background-color: #e9ecef;
    transform: scale(1.05);
}

.card {
    border-radius: 1rem;
}
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
</style>
@endsection
