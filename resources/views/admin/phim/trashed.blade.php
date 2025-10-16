@extends('admin.layouts.admin')

@section('title', 'Thùng rác phim')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">🗑️ Thùng rác phim</h2>

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.phim.index') }}" class="btn btn-secondary">← Quay về danh sách</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Poster</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Ngôn ngữ</th>
                            <th>Ngày xóa</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($phims as $phim)
                            <tr>
                                <td>{{ $phim->id }}</td>
                                <td>
                                    @if($phim->anh_poster)
                                        <img src="{{ asset('storage/' . $phim->anh_poster) }}" alt="Poster" width="70" height="90" class="rounded shadow-sm">
                                    @else
                                        <span class="text-muted fst-italic">Chưa có</span>
                                    @endif
                                </td>
                                <td class="fw-semibold text-start">{{ $phim->tieu_de }}</td>
                                <td>{{ $phim->danhMuc->ten ?? '—' }}</td>
                                <td>{{ $phim->ngonNgu->ten ?? '—' }}</td>
                                <td>{{ $phim->deleted_at ? \Carbon\Carbon::parse($phim->deleted_at)->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    <form action="{{ route('admin.phim.restore', $phim->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc muốn khôi phục phim này?')">Khôi phục</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted">Thùng rác trống</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-4">
                    {{ $phims->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection


