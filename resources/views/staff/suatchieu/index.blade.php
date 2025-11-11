@extends('staff.layouts.staff')

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
                <i class="bi bi-folder2-open"></i> Quản lý suất chiếu
            </h2>
            <small class="text-muted">Xem, lọc và quản lý các suất chiếu</small>
        </div>
        <div>
            <a href="{{ route('staff.suatchieu.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 me-2">
                <i class="bi bi-plus-circle"></i> Thêm suất chiếu
            </a>
        </div>
    </div>

    {{-- 🔍 Bộ lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.suatchieu.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên phim..." value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="ngay_chieu" class="form-control" value="{{ request('ngay_chieu') }}">
                </div>
                <div class="col-md-2">
                    <select name="phong_id" class="form-select rounded-pill">
                        <option value="">-- Chọn phòng chiếu --</option>
                        @foreach ($phongs as $phong)
                            <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                                {{ $phong->ten }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="trang_thai" class="form-select rounded-pill">
                        <option value="">-- Trạng thái --</option>
                        @foreach(['hoat_dong' => 'Hoạt động', 'tam_dung' => 'Tạm dừng', 'huy' => 'Hủy'] as $value => $label)
                            <option value="{{ $value }}" {{ request('trang_thai') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select rounded-pill">
                        <option value="">-- Sắp xếp theo --</option>
                        <option value="time_asc" {{ request('sort') == 'time_asc' ? 'selected' : '' }}>Giờ chiếu ↑</option>
                        <option value="time_desc" {{ request('sort') == 'time_desc' ? 'selected' : '' }}>Giờ chiếu ↓</option>
                        <option value="movie_asc" {{ request('sort') == 'movie_asc' ? 'selected' : '' }}>Tên phim (A→Z)</option>
                        <option value="movie_desc" {{ request('sort') == 'movie_desc' ? 'selected' : '' }}>Tên phim (Z→A)</option>
                    </select>
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-pill px-4 me-2">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('staff.suatchieu.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                        Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </div>
    {{-- ⚙️ Cập nhật trạng thái hàng loạt --}} 
    <div class="card mb-4 border-0 shadow-sm"> 
        <div class="card-body py-3"> 
            <form action="{{ route('staff.suatchieu.bulkUpdate') }}" method="POST" class="row g-3 align-items-end"> 
                @csrf 
                <div class="col-md-3"> 
                    <label class="form-label mb-0 fw-semibold">Ngày chiếu</label> 
                    <input type="date" name="ngay" class="form-control" required> 
                </div> 
                <div class="col-md-3"> 
                    <label class="form-label mb-0 fw-semibold">Phòng chiếu</label> 
                    <select name="phong_id" class="form-select rounded-pill"> 
                        <option value="">-- Tất cả phòng --</option> 
                        @foreach ($phongs as $phong) 
                        <option value="{{ $phong->id }}">{{ $phong->ten }}</option> 
                        @endforeach </select> </div> <div class="col-md-3"> 
                            <label class="form-label mb-0 fw-semibold">Trạng thái mới</label> 
                            <select name="trang_thai" class="form-select rounded-pill" required> 
                                <option value="hoat_dong">🟢 Hoạt động</option> 
                                <option value="tam_dung">⏸️ Tạm dừng</option> 
                                <option value="huy">❌ Hủy</option> </select> 
                            </div> <div class="col-md-3"> 
                                <label class="form-label mb-0 fw-semibold">Lý do (nếu có)</label> 
                                <input type="text" name="ly_do_huy" class="form-control" placeholder="VD: Bảo trì, sự cố, ..."> 
                            </div> 
                            <div class="col-12 text-end mt-3"> 
                                <button type="submit" class="btn btn-warning rounded-pill px-4 shadow-sm"> 
                                    <i class="bi bi-arrow-repeat"></i> Cập nhật trạng thái hàng loạt </button> 
                                </div> 
                            </form> 
                        </div> 
                    </div>

    {{-- 📋 Danh sách suất chiếu --}}
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
                        <tr>
                            <td>{{ $suatchieus->firstItem() + $key }}</td>
                            <td class="text-start ps-4">{{ $s->phim?->tieu_de ?? 'Không có' }}</td>
                            <td>{{ $s->phong?->ten ?? 'Không có' }}</td>
                            <td>{{ $gioBatDau->format('H:i d/m/Y') }}</td>
                            <td>{{ $gioKetThuc->format('H:i d/m/Y') }}</td>
                            <td>{{ number_format($s->gia_ve, 0, ',', '.') }}</td>

                            {{-- Trạng thái --}}
                            <td>
                                @if($now->gt($gioKetThuc))
                                    <span class="badge bg-secondary text-white">Kết thúc</span>
                                @else
                                    <form action="{{ route('staff.suatchieu.updateTrangThai', $s->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="trang_thai" class="form-select form-select-sm w-auto"
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
                        <a href="{{ route('staff.suatchieu.ghe', $s->id) }}" class="btn btn-sm btn-outline-info rounded-pill">
                            Ghế
                        </a>

                        @if($canEdit)
                            <a href="{{ route('staff.suatchieu.edit', $s->id) }}" class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                            <form action="{{ route('staff.suatchieu.destroy', $s->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        @else
                            <span class="btn btn-sm btn-outline-primary me-1 disabled" title="Không thể sửa suất đã bắt đầu">Sửa</span>
                            <span class="btn btn-sm btn-outline-danger disabled" title="Không thể xóa suất đã bắt đầu">Xóa</span>
                        @endif
                    </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted py-5">Không có suất chiếu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-end">
            {{ $suatchieus->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.table-row:hover { background-color: #e9f5ff; transform: scale(1.01); }
select.form-select-sm { min-width: 130px; }
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
