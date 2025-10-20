@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">🗑️ Thùng rác sản phẩm</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.san_pham.index') }}" class="btn btn-secondary mb-3">⬅ Quay lại danh sách</a>

    @if($sanPhams->count() > 0)
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Ngày xóa</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sanPhams as $sp)
                    <tr>
                        <td>{{ $sp->id }}</td>
                        <td>{{ $sp->ten }}</td>
                        <td>{{ number_format($sp->gia, 0, ',', '.') }} đ</td>
                        <td>{{ $sp->so_luong }}</td>
                        <td>{{ $sp->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.san_pham.restore', $sp->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PUT')
                                <button class="btn btn-success btn-sm">Khôi phục</button>
                            </form>
                            <form action="{{ route('admin.san_pham.forceDelete', $sp->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn sản phẩm này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">❌ Xóa vĩnh viễn</button>
                                </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">Không có sản phẩm nào trong thùng rác.</div>
    @endif
</div>
@endsection
