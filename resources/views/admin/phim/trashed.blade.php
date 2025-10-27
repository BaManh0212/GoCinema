@extends('admin.layouts.admin')

@section('title', '🗑️ Thùng rác phim')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-gradient">
                <i class="bi bi-trash3"></i> Thùng rác phim
            </h2>
            <small class="text-muted">Danh sách các phim đã bị xóa tạm thời</small>
        </div>
        <a href="{{ route('admin.phim.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    {{-- ✅ Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm rounded-3">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm rounded-3">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 🎬 Bảng phim --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            @if ($phims->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead class="bg-gradient text-white"
                            style="background: linear-gradient(90deg, #007bff, #00c3ff);">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="10%">Poster</th>
                                <th class="text-start">Tên phim</th>
                                <th>Danh mục</th>
                                <th>Ngôn ngữ</th>
                                <th>Ngày xóa</th>
                                <th width="25%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($phims as $phim)
                                <tr class="align-middle hover-row">
                                    <td class="fw-semibold">{{ $phim->id }}</td>
                                    <td>
                                        @if($phim->anh_poster)
                                            <img src="{{ asset('storage/' . $phim->anh_poster) }}"
                                                 alt="{{ $phim->tieu_de }}"
                                                 class="rounded shadow-sm"
                                                 style="width:70px;height:auto;aspect-ratio:2/3;object-fit:cover;">
                                        @else
                                            <span class="text-muted fst-italic">Chưa có</span>
                                        @endif
                                    </td>
                                    <td class="text-start fw-semibold text-primary">{{ $phim->tieu_de }}</td>
                                    <td>
                                        @if($phim->danhMucs && $phim->danhMucs->count())
                                            @foreach($phim->danhMucs as $dm)
                                                <span class="badge bg-warning text-dark">{{ $dm->ten }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $phim->ngonNgu->ten ?? '—' }}</td>
                                    <td class="text-muted">
                                        {{ $phim->deleted_at ? \Carbon\Carbon::parse($phim->deleted_at)->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            {{-- Khôi phục --}}
                                            <form action="{{ route('admin.phim.restore', $phim->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success rounded-pill px-3 shadow-sm"
                                                        onclick="return confirm('Bạn có chắc muốn khôi phục phim này?')">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                                </button>
                                            </form>

                                            {{-- Xóa vĩnh viễn --}}
                                            <form action="{{ route('admin.phim.forceDelete', $phim->id) }}" method="POST"
                                                  onsubmit="return confirm('⚠️ Xóa vĩnh viễn phim này? Hành động này không thể hoàn tác!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-x-circle"></i> Xóa vĩnh viễn
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $phims->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Không có phim nào trong thùng rác 📭
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 🎨 CSS --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card {
    border-radius: 1rem;
    background-color: #fff;
}

.table thead th {
    color: #fff !important;
    border: none;
}

.hover-row:hover {
    background-color: #f8faff;
    transition: 0.2s ease;
}

.btn {
    font-size: 0.9rem;
    transition: all 0.2s ease;
}
.btn:hover {
    transform: scale(1.05);
}
</style>
@endsection
