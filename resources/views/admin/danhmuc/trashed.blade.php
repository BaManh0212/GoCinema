@extends('admin.layouts.admin')

@section('title', '🗑️ Thùng rác danh mục')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <h2 class="text-center mb-4 text-danger fw-bold">
        🗑️ Thùng rác danh mục
    </h2>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    {{-- Nút quay lại --}}
    <div class="mb-3 text-end">
        <a href="{{ route('admin.danhmuc.index') }}" class="btn btn-secondary">
            ⬅️ Quay lại danh sách
        </a>
    </div>

    {{-- Danh sách danh mục --}}
    <div class="card shadow border-0">
        <div class="card-body">
            @if ($danhmucs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">#</th>
                                <th>Tên danh mục</th>
                                <th>Ngày xóa</th>
                                <th width="20%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($danhmucs as $key => $dm)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="fw-bold text-primary">{{ $dm->ten }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($dm->deleted_at)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            {{-- Nút khôi phục --}}
                                            <form action="{{ route('admin.danhmuc.restore', $dm->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    🔄 Khôi phục
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    Không có danh mục nào trong thùng rác 📭
                </div>
            @endif
        </div>
    </div>

</div>

{{-- CSS nhẹ --}}
<style>
    table th, table td {
        vertical-align: middle !important;
    }
</style>
@endsection
