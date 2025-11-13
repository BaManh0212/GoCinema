@extends('admin.layouts.admin')

@section('title', 'Tạo suất chiếu tự động')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-calendar2-plus"></i> Tạo suất chiếu tự động
            </h2>
            <small class="text-muted">Tạo lịch chiếu tự động cho phim với nhiều tùy chọn</small>
        </div>
        <div>
            <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <form action="{{ route('admin.suatchieu.autoStore') }}" method="POST">
        @csrf

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
                        <select name="phim_id" id="phim_id" class="form-select form-select-lg rounded-pill @error('phim_id') is-invalid @enderror">
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
                        <label for="phong_id" class="form-label fw-semibold">🏢 Phòng chiếu</label>
                        <select name="phong_id" id="phong_id" class="form-select form-select-lg rounded-pill @error('phong_id') is-invalid @enderror">
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
                        <label class="form-label fw-semibold">📅 Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}" class="form-control form-control-lg rounded-pill">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">📅 Ngày kết thúc</label>
                        <input type="date" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}" class="form-control form-control-lg rounded-pill">
                    </div>

                    {{-- Giờ chiếu đầu tiên + gợi ý --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">⏰ Giờ chiếu đầu tiên trong ngày</label>
                        <div class="input-group">
                            <input type="time" id="gio_bat_dau_ngay" name="gio_bat_dau_ngay"
                                value="{{ old('gio_bat_dau_ngay', '08:00') }}"
                                class="form-control form-control-lg rounded-pill">
                            <button class="btn btn-outline-primary rounded-pill" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                💡 Gợi ý
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item gio-goi-y" href="#">🌅 08:00</a></li>
                                <li><a class="dropdown-item gio-goi-y" href="#">☀️ 10:00</a></li>
                                <li><a class="dropdown-item gio-goi-y" href="#">🌞 13:00</a></li>
                                <li><a class="dropdown-item gio-goi-y" href="#">🌇 15:30</a></li>
                                <li><a class="dropdown-item gio-goi-y" href="#">🌆 18:00</a></li>
                                <li><a class="dropdown-item gio-goi-y" href="#">🌙 20:30</a></li>
                            </ul>
                        </div>
                        <div class="form-text text-muted">💡 Chọn nhanh hoặc nhập tay thời gian bắt đầu chiếu.</div>
                    </div>

                    {{-- Giờ chiếu cố định --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">⏰ Chọn giờ chiếu cố định (có thể chọn nhiều)</label>
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
                                        ⏰ {{ $gio }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
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
                        <input type="number" name="gia_ve" min="0" step="1000"
                            value="{{ old('gia_ve', 70000) }}" class="form-control form-control-lg rounded-pill"
                            placeholder="Nhập giá vé...">
                        <div class="form-text text-muted">💰 Giá vé mặc định: 70,000 VNĐ</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔘 Nút hành động --}}
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.suatchieu.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="bi bi-magic"></i> Tạo tự động
            </button>
        </div>
    </form>

            {{-- 📋 Bảng preview suất chiếu đề xuất --}}
            @if(session('preview') && !empty(session('preview')))
                <div class="mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-eye"></i> 👀 Đề xuất suất chiếu
                        </h5>
                        <form action="{{ route('admin.suatchieu.storePreview') }}" method="POST" class="d-inline" id="store-preview-form-top">
                            @csrf
                            <input type="hidden" name="preview_data" id="preview-data-input-top" value="{{ json_encode(session('preview')) }}">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-check-circle"></i> Lưu vào danh sách
                            </button>
                        </form>
                    </div>

                    {{-- 🔍 Bộ lọc cho bảng preview --}}
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body py-3">
                            <form id="preview-filter-form" class="row g-3 align-items-center">
                                <div class="col-md-3">
                                    <input type="text" id="filter-phim" class="form-control rounded-pill" placeholder="🔍 Tìm theo tên phim...">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" id="filter-ngay" class="form-control rounded-pill">
                                </div>
                                <div class="col-md-3">
                                    <select id="filter-phong" class="form-select rounded-pill">
                                        <option value="">-- Chọn phòng chiếu --</option>
                                        @foreach ($phongs as $phong)
                                            <option value="{{ $phong->ten }}">{{ $phong->ten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter-trang-thai" class="form-select rounded-pill">
                                        <option value="">-- Trạng thái --</option>
                                        <option value="ok">✅ OK</option>
                                        <option value="conflict">❌ Trùng lấn</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="preview-table">
                            <thead class="table-header text-white">
                                <tr class="text-center">
                                    <th style="width: 70px;">STT</th>
                                    <th class="text-start">🎬 Phim</th>
                                    <th>🏢 Phòng</th>
                                    <th>⏰ Bắt đầu</th>
                                    <th>⏰ Kết thúc</th>
                                    <th>💰 Giá vé</th>
                                    <th>📊 Trạng thái</th>
                                    <th width="120px">⚙️ Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="preview-table-body">
                                @foreach(session('preview') as $index => $suat)
                                    <tr class="table-row {{ $suat['conflict'] ? 'table-danger' : '' }}" data-index="{{ $index }}" data-phim="{{ $suat['phim_ten'] }}" data-phong="{{ $suat['phong_ten'] }}" data-ngay="{{ \Carbon\Carbon::parse($suat['gio_bat_dau'])->format('Y-m-d') }}" data-trang-thai="{{ $suat['conflict'] ? 'conflict' : 'ok' }}">
                                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="fw-semibold">{{ $suat['phim_ten'] }}</td>
                                        <td class="text-center">{{ $suat['phong_ten'] }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($suat['gio_bat_dau'])->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($suat['gio_ket_thuc'])->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">{{ number_format($suat['gia_ve']) }} VNĐ</td>
                                        <td class="text-center">
                                            @if($suat['conflict'])
                                                <span class="badge bg-danger rounded-pill px-3">❌ Trùng lấn</span>
                                            @else
                                                <span class="badge bg-success rounded-pill px-3">✅ OK</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm remove-row" data-index="{{ $index }}">
                                                <i class="bi bi-trash3"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
            @endif
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

            // Xóa row trong bảng preview
            $(document).on('click', '.remove-row', function() {
                const index = $(this).data('index');
                $(`tr[data-index="${index}"]`).remove();

                // Cập nhật hidden input
                let previewDataTop = JSON.parse($('#preview-data-input-top').val());
                previewDataTop.splice(index, 1);
                $('#preview-data-input-top').val(JSON.stringify(previewDataTop));

                // Cập nhật STT
                $('#preview-table-body tr').each(function(i) {
                    $(this).find('td:first').text(i + 1);
                    $(this).attr('data-index', i);
                    $(this).find('.remove-row').attr('data-index', i);
                });
            });

            // Lọc bảng preview
            function filterPreviewTable() {
                const phimFilter = $('#filter-phim').val().toLowerCase();
                const ngayFilter = $('#filter-ngay').val();
                const phongFilter = $('#filter-phong').val();
                const trangThaiFilter = $('#filter-trang-thai').val();

                $('#preview-table-body tr').each(function() {
                    const row = $(this);
                    const phim = row.data('phim').toLowerCase();
                    const ngay = row.data('ngay');
                    const phong = row.data('phong');
                    const trangThai = row.data('trang-thai');

                    const matchPhim = !phimFilter || phim.includes(phimFilter);
                    const matchNgay = !ngayFilter || ngay === ngayFilter;
                    const matchPhong = !phongFilter || phong === phongFilter;
                    const matchTrangThai = !trangThaiFilter || trangThai === trangThaiFilter;

                    if (matchPhim && matchNgay && matchPhong && matchTrangThai) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });

                // Cập nhật STT sau khi lọc
                let visibleIndex = 1;
                $('#preview-table-body tr:visible').each(function() {
                    $(this).find('td:first').text(visibleIndex++);
                });
            }

            // Gắn sự kiện lọc
            $('#filter-phim, #filter-ngay, #filter-phong, #filter-trang-thai').on('input change', filterPreviewTable);

            // Khởi tạo DataTable cho preview table (không phân trang)
            if ($('#preview-table').length > 0) {
                $('#preview-table').DataTable({
                    paging: false, // Tắt phân trang
                    searching: false, // Tắt search mặc định của DataTable
                    ordering: false, // Tắt sắp xếp
                    info: false, // Tắt thông tin số lượng
                    lengthChange: false, // Tắt chọn số lượng hiển thị
                    language: {
                        emptyTable: "Không có dữ liệu"
                    }
                });
            }
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
