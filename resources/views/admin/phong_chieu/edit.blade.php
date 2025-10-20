@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">✏️ Chỉnh sửa phòng chiếu</h1>

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
                <form action="{{ route('admin.phongchieu.update', $phongchieu->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tên phòng --}}
                    <div class="mb-3">
                        <label for="ten" class="form-label fw-bold">Tên phòng chiếu <span class="text-danger">*</span></label>
                        <input 
                            type="text"
                            id="ten"
                            name="ten"
                            class="form-control @error('ten') is-invalid @enderror"
                            value="{{ old('ten', $phongchieu->ten) }}">
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
                            value="{{ old('tong_ghe', $phongchieu->tong_ghe) }}"
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
                                <option value="{{ $dd->id }}" {{ old('dinh_dang_id', $phongchieu->dinh_dang_id) == $dd->id ? 'selected' : '' }}>
                                    {{ $dd->ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sơ đồ --}}
                    <div class="mb-3">
                        <label for="so_do" class="form-label fw-bold">Sơ đồ (tùy chọn)</label>
                        <input 
                            type="text"
                            id="so_do"
                            name="so_do"
                            class="form-control"
                            value="{{ old('so_do', $phongchieu->so_do) }}">
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <label for="trang_thai" class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                        <select name="trang_thai" id="trang_thai" class="form-select">
                            <option value="hoat_dong" {{ old('trang_thai', $phongchieu->trang_thai) == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="bao_tri" {{ old('trang_thai', $phongchieu->trang_thai) == 'bao_tri' ? 'selected' : '' }}>Bảo trì</option>
                            <option value="ngung_su_dung" {{ old('trang_thai', $phongchieu->trang_thai) == 'ngung_su_dung' ? 'selected' : '' }}>Ngừng sử dụng</option>
                        </select>
                    </div>

                    {{-- Nút --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.phongchieu.index') }}" class="btn btn-secondary">
                            ← Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            💾 Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
