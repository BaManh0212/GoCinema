@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Thêm danh mục mới</h3>

    <form action="{{ route('admin.danhmuc.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tên danh mục</label>
            <input type="text" name="ten" class="form-control" required>
        </div>

        <button class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
