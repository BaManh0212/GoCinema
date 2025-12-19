@extends('admin.layouts.admin')

@section('content')
@php
    use Carbon\Carbon;
    $now = Carbon::now();
@endphp

<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-calendar2-event"></i> Quản lý suất chiếu
            </h2>
            <small class="text-muted">Xem, lọc và quản lý các suất chiếu</small>
        </div>
        <div>
            <a href="{{ route('admin.suatchieu.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm suất chiếu
            </a>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.suatchieu.index') }}" class="row g-3 align-items-center">
                <div class="col-auto">
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên phim..." value="{{ request('q') }}">
                </div>
                <div class="col-auto">
                    <input type="date" name="ngay_chieu" class="form-control" value="{{ request('ngay_chieu') }}">
                </div>
                <div class="col-auto">
                    <select name="phong_id" class="form-select rounded-pill">
                        <option value="">-- Chọn phòng chiếu --</option>
                        @foreach ($phongs as $phong)
                            <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                                {{ $phong->ten }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="trang_thai" class="form-select rounded-pill">
                        <option value="">-- Trạng thái --</option>
                        @foreach(['hoat_dong' => 'Hoạt động', 'tam_dung' => 'Tạm dừng', 'huy' => 'Hủy'] as $value => $label)
                            <option value="{{ $value }}" {{ request('trang_thai') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="sort" class="form-select rounded-pill">
                        <option value="">-- Sắp xếp theo --</option>
                        <option value="time_asc" {{ request('sort') == 'time_asc' ? 'selected' : '' }}>Giờ chiếu ↑</option>
                        <option value="time_desc" {{ request('sort') == 'time_desc' ? 'selected' : '' }}>Giờ chiếu ↓</option>
                        <option value="movie_asc" {{ request('sort') == 'movie_asc' ? 'selected' : '' }}>Tên phim (A→Z)</option>
                        <option value="movie_desc" {{ request('sort') == 'movie_desc' ? 'selected' : '' }}>Tên phim (Z→A)</option>
                    </select>
                </div>
                <div class="ms-auto text-end">
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

    {{-- 📋 Danh sách suất chiếu --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th style="width: 70px;">STT</th>
                        <th class="text-start">Phim</th>
                        <th>Phòng chiếu</th>
                        <th>Giờ bắt đầu</th>
                        <th>Giờ kết thúc</th>
                        <th>Giá vé (VNĐ)</th>
                        <th>Trạng thái</th>
                        <th width="220px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suatchieus as $key => $s)
                        @php
                            $gioBatDau = Carbon::parse($s->gio_bat_dau);
                            $gioKetThuc = Carbon::parse($s->gio_ket_thuc);
                            $canEdit = $now->lt($gioBatDau); // có thể sửa/xóa nếu chưa bắt đầu
                        @endphp
                        <tr class="table-row">
                            <td class="text-center fw-bold text-muted">{{ $suatchieus->firstItem() + $key }}</td>
                            <td class="fw-semibold">{{ $s->phim?->tieu_de ?? 'Không có' }}</td>
                            <td class="text-center">{{ $s->phong?->ten ?? 'Không có' }}</td>
                            <td class="text-center">{{ $gioBatDau->format('H:i d/m/Y') }}</td>
                            <td class="text-center">{{ $gioKetThuc->format('H:i d/m/Y') }}</td>
                            <td class="text-center">{{ number_format($s->gia_ve, 0, ',', '.') }}</td>

                            {{-- Trạng thái --}}
                            <td>
                                @if($now->gt($gioKetThuc))
                                    <span class="badge bg-secondary text-white">Kết thúc</span>
                                @else
                                    <form action="{{ route('admin.suatchieu.updateTrangThai', $s->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="trang_thai" class="form-select rounded-pill form-select rounded-pill-sm w-auto"
                                                onchange="this.form.submit()"
                                                {{ !$canEdit ? 'disabled title=Không thể thay đổi trạng thái suất đã bắt đầu' : '' }}>
                                            @foreach(['hoat_dong'=>'🟢 Hoạt động','tam_dung'=>'⏸️ Tạm dừng','huy'=>'❌ Hủy'] as $value=>$label)
                                                <option value="{{ $value }}" {{ $s->trang_thai==$value?'selected':'' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @endif
                            </td>

                            {{-- Hành động --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- <a href="{{ route('admin.suatchieu.show', $s->id) }}" 
                                    class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                        <i class="bi bi-eye"></i> Ghế
                                    </a> --}}

                                    @if($canEdit)
                                        {{-- ✏️ Nút sửa --}}
                                        <a href="{{ route('admin.suatchieu.edit', $s->id) }}"
                                           class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                            <i class="bi bi-pencil-square"></i> Sửa
                                        </a>

                                        {{-- 🗑️ Nút xóa --}}
                                        <form action="{{ route('admin.suatchieu.destroy', $s->id) }}"
                                              method="POST" onsubmit="return confirm('Xóa suất chiếu này?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                                                <i class="bi bi-trash3"></i> Xóa
                                            </button>
                                        </form>
                                    @else
                                        <span class="btn btn-sm btn-outline-primary disabled rounded-pill px-3" title="Không thể sửa suất đã bắt đầu">
                                            <i class="bi bi-pencil-square"></i> Sửa
                                        </span>
                                        <span class="btn btn-sm btn-outline-danger disabled rounded-pill px-3" title="Không thể xóa suất đã bắt đầu">
                                            <i class="bi bi-trash3"></i> Xóa
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có suất chiếu nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-end">
                {{ $suatchieus->links('pagination::bootstrap-5') }}
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
.ms-auto {
    margin-left: auto !important;
}
.text-end {
    text-align: right !important;
}
select.form-select.rounded-pill-sm {
    min-width: 130px;
}
.disabled {
    pointer-events: none;
    opacity: 0.6;
    cursor: not-allowed;
}
.badge {
    font-size: 0.85rem;
    padding: 0.35em 0.6em;
}
</style>
@endsection
