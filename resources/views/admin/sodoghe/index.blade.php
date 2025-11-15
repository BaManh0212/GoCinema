@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>📋 Danh sách phòng chiếu và sơ đồ ghế</h2>

    <a href="{{ route('admin.sodo.create') }}" class="btn btn-success mb-3">➕ Thêm ghế</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Phòng chiếu</th>
                <th>Hàng</th>
                <th>Cột</th>
                <th>Loại</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($phongs as $phong)
                @foreach($phong->soDoGhe ?? [] as $ghe)
                <tr>
                    <td>{{ $phong->ten }}</td>
                    {{-- <td>{{ ucfirst($ghe->loai) }}</td>
                    <td>{{ ucfirst($ghe->trang_thai) }}</td> --}}
                    <td>
                        {{-- <a href="{{ route('admin.sodo.edit', $ghe->id) }}" class="btn btn-sm btn-primary">Sửa</a> --}}
                        <form action="{{ route('admin.sodo.destroy', $ghe->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection
