@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Danh sách danh mục</h3>

    <div class="mb-3 d-flex flex-wrap gap-2">
        <a href="{{ route('admin.danhmuc.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> ➕ Thêm danh mục
        </a>
        <a href="{{ route('admin.danhmuc.trashed') }}" class="btn btn-outline-secondary">
            <i class="bi bi-trash"></i> 🗑️ Thùng rác
        </a>
    </div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($danhmucs as $dm)
            <tr>
                <td>{{ $dm->id }}</td>
                <td>{{ $dm->ten }}</td>
                <td>
                    <a href="{{ route('admin.danhmuc.edit', $dm->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('admin.danhmuc.destroy', $dm->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
