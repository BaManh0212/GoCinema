@extends('admin.layouts.admin')

@section('title', 'Quản lý phim')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">🎬 Danh sách phim</h2>

        {{-- Nút thêm phim mới và Thùng rác --}}
        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('admin.phim.trashed') }}" class="btn btn-outline-secondary">
                🗑️ Thùng rác
            </a>
            <a href="{{ route('admin.phim.create') }}" class="btn btn-success">
                ➕ Thêm phim mới
            </a>
        </div>

        {{-- Bảng danh sách phim --}}
        <div class="card shadow-sm">
           <div class="card-body table-responsive">
    <table class="table table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Poster</th>
                            <th>Tiêu đề</th>
                            <th>Mô tả</th>
                            <th>Trailer</th>
                            <th>Phụ đề</th>
                            <th>Thời lượng</th>
                            <th>Ngày công chiếu</th>
                            <th>Giới hạn tuổi</th>
                            <th>Danh mục</th>
                            <th>Ngôn ngữ</th>
                            <th>Ngày tạo</th>
                            <th>Ngày cập nhật</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($phims as $phim)
                            <tr>
                                <td>{{ $phim->id }}</td>

                                {{-- Ảnh poster --}}
                                <td>
                                    @if($phim->anh_poster)
                                        <img src="{{ asset('storage/' . $phim->anh_poster) }}" alt="Poster" width="70" height="90"
                                            class="rounded shadow-sm">
                                    @else
                                        <span class="text-muted fst-italic">Chưa có</span>
                                    @endif
                                </td>

                                {{-- Tiêu đề --}}
                                <td class="fw-semibold text-start">{{ $phim->tieu_de }}</td>

                                {{-- Mô tả --}}
                                <td class="text-start"
                                    style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $phim->mo_ta }}
                                </td>

                                {{-- Trailer --}}
                                <td>
                                    @if($phim->trailer)
                                        <a href="{{ $phim->trailer }}" target="_blank" class="text-decoration-none">🎥 Xem</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- Phụ đề --}}
                                <td>
                                    @if($phim->phu_de)
                                        ✅ Có
                                    @else
                                        ❌ Không
                                    @endif
                                </td>

                                {{-- Thời lượng --}}
                                <td>{{ $phim->thoi_luong }} phút</td>

                                {{-- Ngày công chiếu --}}
                                <td>
                                    {{ $phim->ngay_cong_chieu ? \Carbon\Carbon::parse($phim->ngay_cong_chieu)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Giới hạn tuổi --}}
                                <td>{{ $phim->do_tuoi_gioi_han ?? '—' }}</td>

                                {{-- Danh mục --}}
                                <td>{{ $phim->danhMuc->ten ?? '—' }}</td>

                                {{-- Ngôn ngữ --}}
                                <td>{{ $phim->ngonNgu->ten ?? '—' }}</td>

                                {{-- Ngày tạo --}}
                                <td>
                                    {{ $phim->created_at ? \Carbon\Carbon::parse($phim->created_at)->format('d/m/Y H:i') : '—' }}
                                </td>

                                {{-- Ngày cập nhật --}}
                                <td>
                                    {{ $phim->updated_at ? \Carbon\Carbon::parse($phim->updated_at)->format('d/m/Y H:i') : '—' }}
                                </td>


                                {{-- Hành động --}}
                                <td>
                                    <a href="{{ route('admin.phim.edit', $phim->id) }}" class="btn btn-sm btn-primary me-2">
                                        ✏️ Sửa
                                    </a>

                                    <form action="{{ route('admin.phim.destroy', $phim->id) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Xác nhận xóa phim này?');">
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
                                <td colspan="14" class="text-muted">Không có phim nào trong hệ thống</td>
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