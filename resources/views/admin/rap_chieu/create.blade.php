@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">➕ Thêm mới rạp chiếu</h1>

        {{-- ✅ Hiển thị thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ✅ Hiển thị lỗi chung nếu có --}}
        @if ($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.rap.store') }}" method="POST">
                    @csrf
                    {{-- Tên rạp --}}
                    <div class="mb-3">
                        <label for="ten" class="form-label fw-bold">Tên rạp <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            id="ten"
                            name="ten"
                            class="form-control @error('ten') is-invalid @enderror"
                            value="{{ old('ten') }}"
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
                            value="{{ old('dia_chi') }}"
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
                            value="{{ old('so_dien_thoai') }}"
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
                            value="{{ old('email') }}"
                            placeholder="VD: rapchieu@gmail.com"
                        >
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút thao tác --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.rap.index') }}" class="btn btn-secondary">
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
