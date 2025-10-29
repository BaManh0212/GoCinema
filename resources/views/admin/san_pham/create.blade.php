@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- Tiêu đề và breadcrumb --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">🛒 Thêm sản phẩm mới</h3>
        <a href="{{ route('admin.san_pham.index') }}" class="btn btn-secondary">
            ⬅️ Quay lại danh sách
        </a>
    </div>

    {{-- Hiển thị thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form thêm sản phẩm --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.san_pham.store') }}" method="POST" novalidate>
                @csrf

                {{-- Tên sản phẩm --}}
                <div class="mb-3">
                    <label for="ten" class="form-label fw-semibold">Tên sản phẩm</label>
                    <input type="text" 
                           name="ten" 
                           id="ten" 
                           class="form-control @error('ten') is-invalid @enderror" 
                           value="{{ old('ten') }}" 
                           placeholder="Nhập tên sản phẩm...">
                    @error('ten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Giá --}}
                <div class="mb-3">
                    <label for="gia" class="form-label fw-semibold">Giá</label>
                    <input type="number" 
                           name="gia" 
                           id="gia" 
                           class="form-control @error('gia') is-invalid @enderror" 
                           value="{{ old('gia') }}" 
                           placeholder="Nhập giá sản phẩm...">
                    @error('gia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Số lượng --}}
                <div class="mb-3">
                    <label for="so_luong" class="form-label fw-semibold">Số lượng</label>
                    <input type="number" 
                           name="so_luong" 
                           id="so_luong" 
                           class="form-control @error('so_luong') is-invalid @enderror" 
                           value="{{ old('so_luong') }}" 
                           placeholder="Nhập số lượng...">
                    @error('so_luong')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nút hành động --}}
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success me-2">
                        💾 Lưu sản phẩm
                    </button>
                    <a href="{{ route('admin.san_pham.index') }}" class="btn btn-secondary">
                        ❌ Hủy
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
