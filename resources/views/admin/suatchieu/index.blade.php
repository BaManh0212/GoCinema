@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
        {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-folder2-open"></i> Quản lý xuất chiếu
            </h2>
            <small class="text-muted">Xem, lọc và quản lý các xuất chiếu</small>
        </div>
        <div>
            <a href="{{ route('admin.suatchieu.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm xuất chiếu
            </a>
        </div>
    </div>

 {{-- 🔍 Bộ lọc --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.suatchieu.index') }}" class="row g-3 align-items-center">

            {{-- Ô tìm kiếm theo tên phim --}}
            <div class="col-md-3">
                <input type="text" name="q" class="form-control"
                    placeholder="Tìm theo tên phim..." value="{{ request('q') }}">
            </div>

            {{-- Ngày chiếu --}}
            <div class="col-md-2">
                <input type="date" name="ngay_chieu" class="form-control" value="{{ request('ngay_chieu') }}">
            </div>

            {{-- Phòng chiếu --}}
            <div class="col-md-2">
                <select name="phong_id" class="form-select">
                    <option value="">-- Chọn phòng chiếu --</option>
                    @foreach ($phongs as $phong)
                        <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                            {{ $phong->ten }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Trạng thái --}}
            <div class="col-md-2">
                <select name="trang_thai" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="sap_chieu" {{ request('trang_thai') == 'sap_chieu' ? 'selected' : '' }}>Sắp chiếu</option>
                    <option value="dang_chieu" {{ request('trang_thai') == 'dang_chieu' ? 'selected' : '' }}>Đang chiếu</option>
                    <option value="da_chieu" {{ request('trang_thai') == 'da_chieu' ? 'selected' : '' }}>Đã chiếu</option>
                </select>
            </div>

            {{-- Sắp xếp --}}
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="">-- Sắp xếp theo --</option>
                    <option value="time_asc" {{ request('sort') == 'time_asc' ? 'selected' : '' }}>Giờ chiếu ↑</option>
                    <option value="time_desc" {{ request('sort') == 'time_desc' ? 'selected' : '' }}>Giờ chiếu ↓</option>
                    <option value="movie_asc" {{ request('sort') == 'movie_asc' ? 'selected' : '' }}>Tên phim (A → Z)</option>
                    <option value="movie_desc" {{ request('sort') == 'movie_desc' ? 'selected' : '' }}>Tên phim (Z → A)</option>
                </select>
            </div>

            {{-- Nút tìm kiếm và đặt lại --}}
            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                    Đặt lại
                </a>
            </div>
        </form>
    </div>
</div>


    {{-- ✅ Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
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
                                <a href="{{ route('admin.admin.suatchieu.ghe', $s->id) }}" 
                                    class="btn btn-sm btn-outline-info rounded-pill">
                                <i class="bi bi-grid-3x3-gap"></i> Ghế
                                </a>
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
