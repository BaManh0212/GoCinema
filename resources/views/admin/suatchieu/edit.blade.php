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

        <div class="row">
            {{-- Cột trái: Thông tin cơ bản & Giá vé --}}
            <div class="col-md-6">
                {{-- 🎞️ Thông tin cơ bản --}}
                <div class="card section-card mb-4 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-film"></i> 🎬 Thông tin phim & phòng chiếu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="phim_id" class="col-sm-3 col-form-label fw-semibold">🎬 Phim</label>
                            <div class="col-sm-9">
                                <select name="phim_id" id="phim_id" class="form-select form-select-lg @error('phim_id') is-invalid @enderror" style="max-width: 100%; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    <option value="">-- Chọn phim --</option>
                                    @foreach ($phims as $phim)
                                        <option value="{{ $phim->id }}" {{ $suatchieu->phim_id == $phim->id ? 'selected' : '' }} data-subtext="{{ $phim->thoi_luong }} phút">
                                            {{ Str::limit($phim->tieu_de, 30) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('phim_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text text-muted">Tên phim sẽ được rút gọn nếu quá dài</div>
                            </div>
                        </div>
                        <style>
                            .select2-container {
                                width: 100% !important;
                            }
                            .select2-results__option {
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                            }
                        </style>
                        <div class="row mb-3">
                            <label for="phong_id" class="col-sm-3 col-form-label fw-semibold">🏢 Phòng chiếu</label>
                            <div class="col-sm-9">
                                <select name="phong_id" id="phong_id" class="form-select form-select-lg rounded-pill @error('phong_id') is-invalid @enderror">
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

                {{-- 💰 Giá vé --}}
                <div class="card section-card mb-4 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-cash-stack"></i> 💰 Giá vé
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-semibold">💰 Giá vé</label>
                            <div class="col-sm-9">
                                <input type="number" name="gia_ve" min="0" step="1000"
                                    value="{{ old('gia_ve', $suatchieu->gia_ve) }}" class="form-control form-control-lg rounded-pill"
                                    placeholder="Nhập giá vé...">
                                <div class="form-text text-muted">💰 Giá vé mặc định: 70,000 VNĐ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Thời gian --}}
            <div class="col-md-6">
                <div class="card section-card mb-4 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-clock-history"></i> 📅 Thời gian chiếu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-semibold">📅 Ngày chiếu</label>
                            <div class="col-sm-9">
                                <input type="date" name="ngay_chieu" value="{{ old('ngay_chieu', \Carbon\Carbon::parse($suatchieu->gio_bat_dau)->format('Y-m-d')) }}" class="form-control form-control-lg rounded-pill">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-semibold">⏰ Giờ bắt đầu</label>
                            <div class="col-sm-9">
                                <input type="time" name="gio_bat_dau" value="{{ old('gio_bat_dau', \Carbon\Carbon::parse($suatchieu->gio_bat_dau)->format('H:i')) }}" class="form-control form-control-lg rounded-pill">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-semibold">⏰ Giờ kết thúc</label>
                            <div class="col-sm-9">
                                <input type="time" name="gio_ket_thuc" value="{{ old('gio_ket_thuc', \Carbon\Carbon::parse($suatchieu->gio_ket_thuc)->format('H:i')) }}" class="form-control form-control-lg rounded-pill">
                            </div>
                        </div>
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
@push('styles')
    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
        .select2-container .select2-selection--single {
            height: 45px !important;
            border-radius: 0.75rem !important;
            border: 1px solid #ced4da !important;
            padding: 0.3rem 0.5rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 10px !important;
        }

        /* =================== Buttons =================== */
        .btn {
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(90deg, #4e73df, #224abe);
            border: none;
        }

        .btn-outline-secondary {
            border: 1px solid #d1d5db;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* =================== Form Elements =================== */
        .form-label, .col-form-label {
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.4rem;
        }

        .form-text {
            font-size: 0.825rem;
            margin-top: 0.4rem;
        }

        .invalid-feedback {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        /* =================== Responsive Adjustments =================== */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .d-flex {
                flex-direction: column;
            }
            
            .gap-2 {
                gap: 0.5rem !important;
            }
        }

        /* =================== Animation =================== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.3s ease-out;
        }

        /* =================== Text Gradient =================== */
        .text-gradient {
            background: linear-gradient(90deg, #4e73df, #224abe);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        /* =================== Section Card =================== */
        .section-card {
            border-left: 4px solid #4e73df;
        }

        /* =================== Form Control LG =================== */
        .form-control-lg, .form-select-lg {
            padding: 0.75rem 1rem;
            font-size: 1.05rem;
        }

        /* =================== Custom Utilities =================== */
        .rounded-pill {
            border-radius: 50rem !important;
        }

        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* =================== Custom Scrollbar =================== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush

@push('scripts')
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Khởi tạo Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Chọn --',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true,
                templateResult: formatMovieOption,
                templateSelection: formatMovieSelection
            });

            // Định dạng hiển thị option trong dropdown
            function formatMovieOption(movie) {
                if (!movie.id) { return movie.text; }
                var $movie = $(
                    '<div class="d-flex justify-content-between align-items-center">' +
                    '   <span>' + movie.text + '</span>' +
                    '   <span class="text-muted small">' + $(movie.element).data('subtext') + '</span>' +
                    '</div>'
                );
                return $movie;
            }

            // Định dạng khi đã chọn
            function formatMovieSelection(movie) {
                if (!movie.id) { return movie.text; }
                return $('<span>').text(movie.text);
            }

            // Tự động tính giờ kết thúc dựa trên thời lượng phim
            $('#phim_id').on('change', function() {
                var phimId = $(this).val();
                var selectedOption = $(this).find('option:selected');
                var thoiLuong = selectedOption.data('subtext'); // Định dạng: "120 phút"
                
                if (thoiLuong) {
                    var minutes = parseInt(thoiLuong);
                    var gioBatDau = $('input[name="gio_bat_dau"]').val();
                    
                    if (gioBatDau) {
                        var [hours, mins] = gioBatDau.split(':').map(Number);
                        var date = new Date();
                        date.setHours(hours, mins, 0);
                        
                        // Thêm thời lượng phim
                        date.setMinutes(date.getMinutes() + minutes);
                        
                        // Format lại giờ và phút
                        var endHours = String(date.getHours()).padStart(2, '0');
                        var endMinutes = String(date.getMinutes()).padStart(2, '0');
                        
                        // Cập nhật giờ kết thúc
                        $('input[name="gio_ket_thuc"]').val(endHours + ':' + endMinutes);
                    }
                }
            });
        });
    </script>
@endpush
