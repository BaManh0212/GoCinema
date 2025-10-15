@extends('staff.layouts.staff')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Thêm danh mục mới</h3>

    <form action="{{ route('staff.danhmuc.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tên danh mục</label>
            <input 
                type="text" 
                name="ten" 
                class="form-control @error('ten') is-invalid @enderror"
                value="{{ old('ten') }}"
                placeholder="Nhập tên danh mục..."
            >

            {{-- Hiển thị lỗi --}}
            @error('ten')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button class="btn btn-success">💾 Lưu</button>
        <a href="{{ route('staff.danhmuc.index') }}" class="btn btn-secondary">⬅️ Hủy</a>
    </form>
</div>
@endsection
