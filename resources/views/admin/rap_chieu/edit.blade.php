@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">✏️ Sửa rạp chiếu</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.rap.update', $rap->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tên rạp --}}
                    <div class="mb-3">
                        <label for="ten" class="form-label fw-bold">Tên rạp <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="ten" 
                            name="ten"
                            class="form-control @error('ten') is-invalid @enderror"
                            value="{{ old('ten', $rap->ten) }}"
                            placeholder="Nhập tên rạp"
                        >
                        @error('ten')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Địa chỉ --}}
                    <div class="mb-3">
                        <label for="dia_chi" class="form-label fw-bold">Địa chỉ <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="dia_chi" 
                            name="dia_chi"
                            class="form-control @error('dia_chi') is-invalid @enderror"
                            value="{{ old('dia_chi', $rap->dia_chi) }}"
                            placeholder="Nhập địa chỉ rạp"
                        >
                        @error('dia_chi')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Số điện thoại --}}
                    <div class="mb-3">
                        <label for="so_dien_thoai" class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="so_dien_thoai" 
                            name="so_dien_thoai"
                            class="form-control @error('so_dien_thoai') is-invalid @enderror"
                            value="{{ old('so_dien_thoai', $rap->so_dien_thoai ?? '') }}"
                            placeholder="VD: 0987654321"
                        >
                        @error('so_dien_thoai')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $rap->email ?? '') }}"
                            placeholder="VD: rapchieu@gmail.com"
                        >
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.rap.index') }}" class="btn btn-secondary">
                            ← Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            💾 Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
