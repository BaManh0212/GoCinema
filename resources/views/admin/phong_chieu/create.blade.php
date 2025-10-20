@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">➕ Thêm mới phòng chiếu</h1>

        {{-- ✅ Thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ✅ Thông báo lỗi --}}
        @if ($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.phongchieu.store') }}" method="POST">
                    @csrf

                    {{-- Tên phòng --}}
                    <div class="mb-3">
                        <label for="ten" class="form-label fw-bold">Tên phòng chiếu <span class="text-danger">*</span></label>
                        <input 
                            type="text"
                            id="ten"
                            name="ten"
                            class="form-control @error('ten') is-invalid @enderror"
                            value="{{ old('ten') }}"
                            placeholder="Nhập tên phòng chiếu">
                        @error('ten')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tổng ghế --}}
                    <div class="mb-3">
                        <label for="tong_ghe" class="form-label fw-bold">Tổng số ghế <span class="text-danger">*</span></label>
                        <input 
                            type="number"
                            id="tong_ghe"
                            name="tong_ghe"
                            class="form-control @error('tong_ghe') is-invalid @enderror"
                            value="{{ old('tong_ghe') }}"
                            placeholder="Nhập tổng số ghế"
                            min="1">
                        @error('tong_ghe')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Định dạng --}}
                    <div class="mb-3">
                        <label for="dinh_dang_id" class="form-label fw-bold">Định dạng</label>
                        <select name="dinh_dang_id" id="dinh_dang_id" class="form-select">
                            <option value="">-- Chọn định dạng --</option>
                            @foreach($dinhdangs as $dd)
                                <option value="{{ $dd->id }}" {{ old('dinh_dang_id') == $dd->id ? 'selected' : '' }}>
                                    {{ $dd->ten }}
                                </option>
                            @endforeach
                        </select>
                        @error('dinh_dang_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Sơ đồ --}}
                    <div class="mb-3">
                        <label for="so_do" class="form-label fw-bold">Sơ đồ (tùy chọn)</label>
                        <input 
                            type="text"
                            id="so_do"
                            name="so_do"
                            class="form-control"
                            value="{{ old('so_do') }}"
                            placeholder="VD: A1-A10, B1-B10...">
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <label for="trang_thai" class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                        <select name="trang_thai" id="trang_thai" class="form-select">
                            <option value="hoat_dong" {{ old('trang_thai') == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="bao_tri" {{ old('trang_thai') == 'bao_tri' ? 'selected' : '' }}>Bảo trì</option>
                            <option value="ngung_su_dung" {{ old('trang_thai') == 'ngung_su_dung' ? 'selected' : '' }}>Ngừng sử dụng</option>
                        </select>
                        @error('trang_thai')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.phongchieu.index') }}" class="btn btn-secondary">
                            ← Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            💾 Thêm mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
