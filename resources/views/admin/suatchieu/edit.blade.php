@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa suất chiếu')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-pencil-square"></i> ✏️ Chỉnh sửa suất chiếu
            </h2>
            <small class="text-muted">Cập nhật thông tin suất chiếu</small>
        </div>
        <div>
            <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <form action="{{ route('admin.suatchieu.update', $suatchieu->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- 🎞️ Thông tin cơ bản --}}
        <div class="card section-card mb-4 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="fw-bold text-primary mb-0">
                    <i class="bi bi-film"></i> 🎬 Thông tin phim & phòng chiếu
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="phim_id" class="form-label fw-semibold">🎬 Phim</label>
                        <select name="phim_id" id="phim_id" class="form-select form-select-lg rounded-pill select2 @error('phim_id') is-invalid @enderror">
                            <option value="">-- Chọn phim --</option>
                            @foreach ($phims as $phim)
                                <option value="{{ $phim->id }}" {{ $suatchieu->phim_id == $phim->id ? 'selected' : '' }}>
                                    🎬 {{ $phim->tieu_de }} ({{ $phim->thoi_luong }} phút)
                                </option>
                            @endforeach
                        </select>
                        @error('phim_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phong_id" class="form-label fw-semibold">🏢 Phòng chiếu</label>
                        <select name="phong_id" id="phong_id" class="form-select form-select-lg rounded-pill select2 @error('phong_id') is-invalid @enderror">
                            <option value="">-- Chọn phòng --</option>
                            @foreach ($phongs as $phong)
                                <option value="{{ $phong->id }}" {{ $suatchieu->phong_id == $phong->id ? 'selected' : '' }}>
                                    🏢 {{ $phong->ten }}
                                </option>
                            @endforeach
                        </select>
                        @error('phong_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 📅 Thời gian --}}
        <div class="card section-card mb-4 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="fw-bold text-primary mb-0">
                    <i class="bi bi-clock-history"></i> 📅 Thời gian chiếu
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="gio_bat_dau" class="form-label fw-semibold">⏰ Giờ bắt đầu</label>
                        <input type="datetime-local" name="gio_bat_dau" id="gio_bat_dau"
                            value="{{ old('gio_bat_dau', date('Y-m-d\TH:i', strtotime($suatchieu->gio_bat_dau))) }}"
                            class="form-control form-control-lg rounded-pill @error('gio_bat_dau') is-invalid @enderror">
                        @error('gio_bat_dau') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="gio_ket_thuc" class="form-label fw-semibold">⏰ Giờ kết thúc</label>
                        <input type="datetime-local" name="gio_ket_thuc" id="gio_ket_thuc"
                            value="{{ old('gio_ket_thuc', date('Y-m-d\TH:i', strtotime($suatchieu->gio_ket_thuc))) }}"
                            class="form-control form-control-lg rounded-pill @error('gio_ket_thuc') is-invalid @enderror">
                        @error('gio_ket_thuc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 💰 Giá vé --}}
        <div class="card section-card mb-4 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="fw-bold text-primary mb-0">
                    <i class="bi bi-cash-stack"></i> 💰 Giá vé
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="number" name="gia_ve" id="gia_ve" min="0" step="1000"
                            value="{{ old('gia_ve', $suatchieu->gia_ve) }}"
                            class="form-control form-control-lg rounded-pill @error('gia_ve') is-invalid @enderror"
                            placeholder="Nhập giá vé...">
                        <div class="form-text text-muted">💰 Giá vé mặc định: 70,000 VNĐ</div>
                        @error('gia_ve') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔘 Nút hành động --}}
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-save"></i> Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection

{{-- ⚙️ Script & CSS --}}
@push('scripts')
    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Kích hoạt Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Chọn --',
                allowClear: true
            });
        });
    </script>

    <style>
    /* =================== Card & Container =================== */
    .card {
        background: linear-gradient(180deg, #ffffff, #f1f5f9);
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }
    .container {
        max-width: 900px;
    }

    /* =================== Titles & Icons =================== */
    h2, h5 {
        color: #1e3a8a;
    }
    h2 i, h5 i {
        vertical-align: middle;
    }
    h5 {
        font-size: 1.15rem;
    }
    .fw-bold {
        font-weight: 600;
    }

    /* =================== Inputs & Select =================== */
    .form-control, .form-select {
        border-radius: 0.75rem;
        padding: 0.5rem 0.75rem;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #ced4da;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        outline: none;
    }

    /* =================== Select2 =================== */
/* Đồng bộ chiều cao select2 */
.select2-container .select2-selection--single {
    height: 45px !important; /* chiều cao đủ chứa emoji + text */
    border-radius: 0.75rem !important;
    border: 1px solid #ced4da !important;
    display: flex;
    align-items: center; /* căn giữa nội dung theo chiều dọc */
}

/* Text hiển thị trong select2 */
.select2-selection__rendered {
    line-height: normal !important; /* không để line-height cố định */
    padding-left: 0.5rem; /* khoảng cách từ viền */
    white-space: nowrap; /* không xuống dòng */
    overflow: hidden;
    text-overflow: ellipsis; /* nếu dài sẽ hiển thị dấu ... */
}

/* Mũi tên dropdown */
.select2-selection__arrow {
    height: 45px !important;
    display: flex;
    align-items: center; /* căn giữa mũi tên */
}

/* Kết quả dropdown */
.select2-results__option {
    white-space: normal; /* vẫn cho xuống dòng khi list quá dài */
}

/* Input date/time đồng bộ chiều cao */
input[type="date"], input[type="time"], .form-control {
    height: 45px;
    padding: 0.5rem 0.75rem;
    border-radius: 0.75rem;
}
.row.g-3 {
    gap: 1rem;
}


    /* =================== Checkbox =================== */
    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.2rem;
        accent-color: #4e73df;
        cursor: pointer;
    }
    .form-check-label {
        font-weight: 500;
        margin-left: 0.3rem;
    }

    /* =================== Buttons =================== */
    .btn-primary {
        background: linear-gradient(90deg, #4e73df, #224abe);
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #224abe, #1e3a8a);
    }
    .btn-outline-secondary {
        border-radius: 0.75rem;
        font-weight: 500;
    }

    /* =================== Dropdown Gợi ý giờ =================== */
    .dropdown-menu {
        border-radius: 0.75rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .dropdown-item:hover {
        background-color: #4e73df;
        color: #fff;
    }

    /* =================== Form helper text =================== */
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* =================== Spacing & layout =================== */
    .mb-4, .mb-3, .mt-3, .mt-4 {
        margin-bottom: 1rem !important;
        margin-top: 1rem !important;
    }
    .gap-2 > .form-check {
        margin-bottom: 0.5rem;
    }

    /* =================== Table Styles =================== */
    .table-header {
        background: linear-gradient(90deg, #4e73df, #224abe);
        border-radius: 0.75rem 0.75rem 0 0;
    }
    .table-row:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        transition: all 0.2s ease;
    }
    .table-danger {
        background-color: #ffe6e6 !important;
    }
    .table-danger:hover {
        background-color: #ffcccc !important;
    }

    /* =================== Badge Styles =================== */
    .badge {
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* =================== Text Gradient =================== */
    .text-gradient {
        background: linear-gradient(90deg, #4e73df, #224abe);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* =================== Section Cards =================== */
    .section-card {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }
    .section-card:hover {
        border-color: #4e73df;
        box-shadow: 0 5px 15px rgba(78,115,223,0.1) !important;
    }
    .section-card .card-header {
        border-radius: 1rem 1rem 0 0 !important;
        padding: 1rem 1.5rem;
    }
    .section-card .card-body {
        padding: 1.5rem;
    }
</style>
@endpush
