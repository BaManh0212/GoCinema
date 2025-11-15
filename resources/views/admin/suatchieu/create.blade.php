@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Tiêu đề --}}
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-calendar2-plus text-primary fs-2 me-2"></i>
        <h2 class="fw-bold text-primary mb-0">Tạo lịch suất chiếu tự động</h2>
    </div>

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.suatchieu.autoStore') }}" method="POST">
                @csrf

                {{-- 🎞️ Thông tin cơ bản --}}
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-secondary mb-3">
                        <i class="bi bi-film text-primary me-2"></i>Thông tin phim & phòng chiếu
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="phim_id" class="form-label fw-semibold">Phim</label>
                            <select name="phim_id" id="phim_id" class="form-select select2 @error('phim_id') is-invalid @enderror">
                                <option value="">-- Chọn phim --</option>
                                @foreach ($phims as $phim)
                                    <option value="{{ $phim->id }}" {{ old('phim_id') == $phim->id ? 'selected' : '' }}>
                                        🎬 {{ $phim->tieu_de }} ({{ $phim->thoi_luong }} phút)
                                    </option>
                                @endforeach
                            </select>
                            @error('phim_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phong_id" class="form-label fw-semibold">Phòng chiếu</label>
                            <select name="phong_id" id="phong_id" class="form-select select2 @error('phong_id') is-invalid @enderror">
                                <option value="">-- Chọn phòng --</option>
                                @foreach ($phongs as $phong)
                                    <option value="{{ $phong->id }}" {{ old('phong_id') == $phong->id ? 'selected' : '' }}>
                                        🏢 {{ $phong->ten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('phong_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- 📅 Thời gian --}}
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold text-secondary mb-3">
                        <i class="bi bi-clock-history text-primary me-2"></i>Thời gian chiếu
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="date" name="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}" class="form-control rounded-3 shadow-sm-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                            <input type="date" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}" class="form-control rounded-3 shadow-sm-sm">
                        </div>

                        {{-- Giờ chiếu đầu tiên + gợi ý --}}
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold">Giờ chiếu đầu tiên trong ngày</label>
                            <div class="input-group">
                                <input type="time" id="gio_bat_dau_ngay" name="gio_bat_dau_ngay"
                                    value="{{ old('gio_bat_dau_ngay', '08:00') }}"
                                    class="form-control rounded-start-3 shadow-sm-sm">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    Gợi ý
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item gio-goi-y" href="#">08:00</a></li>
                                    <li><a class="dropdown-item gio-goi-y" href="#">10:00</a></li>
                                    <li><a class="dropdown-item gio-goi-y" href="#">13:00</a></li>
                                    <li><a class="dropdown-item gio-goi-y" href="#">15:30</a></li>
                                    <li><a class="dropdown-item gio-goi-y" href="#">18:00</a></li>
                                    <li><a class="dropdown-item gio-goi-y" href="#">20:30</a></li>
                                </ul>
                            </div>
                            <div class="form-text text-muted">Chọn nhanh hoặc nhập tay thời gian bắt đầu chiếu.</div>
                        </div>

                        {{-- Giờ chiếu cố định --}}
                        <div class="col-12 mt-3">
                            <label class="form-label fw-semibold">Chọn giờ chiếu cố định (có thể chọn nhiều)</label>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $gioCoDinh = ['08:00','11:00','14:00','17:00','20:00'];
                                @endphp
                                @foreach($gioCoDinh as $gio)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="gio_co_dinh[]" value="{{ $gio }}"
                                            id="gio_{{ str_replace(':','',$gio) }}"
                                            {{ is_array(old('gio_co_dinh')) && in_array($gio, old('gio_co_dinh')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gio_{{ str_replace(':','',$gio) }}">
                                            {{ $gio }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- 💰 Giá vé --}}
                <div class="mb-4">
                    <h5 class="fw-bold text-secondary mb-3">
                        <i class="bi bi-cash-stack text-primary me-2"></i>Giá vé
                    </h5>
                    <input type="number" name="gia_ve" min="0" step="1000"
                        value="{{ old('gia_ve', 70000) }}" class="form-control rounded-3 shadow-sm-sm w-50">
                </div>

                {{-- 🔘 Nút hành động --}}
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-outline-secondary px-4 me-2 rounded-3">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                        <i class="bi bi-save"></i> Tạo tự động
                    </button>
                </div>
            </form>
        </div>
    </div>
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

            // Gợi ý giờ chiếu
            $('.gio-goi-y').on('click', function(e) {
                e.preventDefault();
                $('#gio_bat_dau_ngay').val($(this).text().trim());
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
</style>
@endpush
