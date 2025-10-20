@extends('staff.layouts.staff')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Chỉnh sửa danh mục</h3>

    <form action="{{ route('staff.danhmuc.update', $danhmuc->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Tên danh mục</label>
            <input type="text" name="ten" value="{{ $danhmuc->ten }}" class="form-control" required>
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('staff.danhmuc.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
