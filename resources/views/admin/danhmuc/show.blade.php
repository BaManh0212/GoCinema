@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">📂 Chi tiết danh mục: {{ $danhmuc->ten }}</h3>
        <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-outline-secondary rounded-pill">
            ⬅ Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $danhmuc->id }}</p>
            <p><strong>Tên danh mục:</strong> {{ $danhmuc->ten }}</p>
            <p><strong>Slug:</strong> {{ $danhmuc->slug }}</p>
            <p><strong>Số lượng phim:</strong> {{ $danhmuc->phims->count() }}</p>
        </div>
    </div>

    <h4 class="fw-bold mb-3">🎬 Danh sách phim trong danh mục này</h4>

    @if($danhmuc->phims->count() > 0)
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên phim</th>
                <th>Ngôn ngữ</th>
                <th>Ngày khởi chiếu</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($danhmuc->phims as $phim)
            <tr>
                <td>{{ $phim->id }}</td>
                <td>{{ $phim->tieu_de }}</td>
                <td>{{ $phim->ngonNgu->ten ?? '—' }}</td>
                <td>{{ $phim->ngay_cong_chieu ?? '—' }}</td>
                <td>
                    <a href="{{ route('admin.phim.show', $phim->id) }}" class="btn btn-info btn-sm">
                        👁 Xem
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="alert alert-warning">Chưa có phim nào trong danh mục này.</div>
    @endif
</div>
@endsection
