@extends('admin.layouts.admin')

@section('content')
    <h1>Sửa sản phẩm</h1>
    <form action="{{ route('admin.san_pham.update', $sanPham->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="ten">Tên sản phẩm</label>
            <input type="text" name="ten" id="ten" class="form-control" value="{{ $sanPham->ten }}" required>
        </div>
        <div class="form-group">
            <label for="gia">Giá</label>
            <input type="number" name="gia" id="gia" class="form-control" value="{{ $sanPham->gia }}" required>
        </div>
        <div class="form-group">
            <label for="so_luong">Số lượng</label>
            <input type="number" name="so_luong" id="so_luong" class="form-control" value="{{ $sanPham->so_luong }}" required>
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>
@endsection