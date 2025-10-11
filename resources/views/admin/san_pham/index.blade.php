@extends('admin.layouts.admin')

@section('content')
    <h1>Danh sách sản phẩm</h1>
    <a href="{{ route('admin.san_pham.create') }}" class="btn btn-primary">Thêm sản phẩm</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sanPhams as $sanPham)
                <tr>
                    <td>{{ $sanPham->id }}</td>
                    <td>{{ $sanPham->ten }}</td>
                    <td>{{ $sanPham->gia }}</td>
                    <td>{{ $sanPham->so_luong }}</td>
                    <td>
                        <a href="{{ route('admin.san_pham.edit', $sanPham->id) }}" class="btn btn-warning">Sửa</a>
                        <form action="{{ route('admin.san_pham.destroy', $sanPham->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection