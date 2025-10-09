@extends('admin.layouts.admin')

@section('title', 'Quản lý phim')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-4">🎬 Danh sách phim</h2>

    {{-- Nút thêm phim mới --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.phim.create') }}" class="btn btn-success">

            ➕ Thêm phim mới
        </a>
    </div>

    {{-- Bảng danh sách phim --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Ngôn ngữ</th>
                        <th>Thời lượng</th>
                        <th>Ngày chiếu</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($phims as $phim)
                    <tr>
                        <td>{{ $phim->id }}</td>

                        {{-- Ảnh poster --}}
                        <td>
                            @if($phim->poster)
                                <img src="{{ asset('storage/' . $phim->poster) }}" alt="Poster" width="80" height="100" class="rounded shadow-sm">
                            @else
                                <span class="text-muted fst-italic">Chưa có ảnh</span>
                            @endif
                        </td>

                        {{-- Tiêu đề --}}
                        <td class="text-start fw-semibold">{{ $phim->tieu_de }}</td>

                        {{-- Danh mục --}}
                        <td>{{ $phim->danhMuc->ten ?? '—' }}</td>

                        {{-- Ngôn ngữ --}}
                        <td>{{ $phim->ngonNgu->ten ?? '—' }}</td>

                        {{-- Thời lượng --}}
                        <td>{{ $phim->thoi_luong }} phút</td>

                        {{-- Ngày chiếu --}}
                        <td>{{ \Carbon\Carbon::parse($phim->ngay_khoi_chieu)->format('d/m/Y') }}</td>

                        {{-- Hành động --}}
                        <td>
                            <a href="{{ route('admin.phim.edit', $phim->id) }}" class="btn btn-sm btn-primary me-2">
                                ✏️ Sửa
                            </a>

                            <form action="{{ route('admin.phim.destroy', $phim->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận xóa phim này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    🗑️ Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-muted">Không có phim nào trong hệ thống</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Phân trang --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $phims->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
