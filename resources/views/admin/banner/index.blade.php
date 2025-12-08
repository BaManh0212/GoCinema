@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- 🏷️ Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-gradient mb-0">📋 Danh sách Banner</h2>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-success shadow-sm rounded-pill px-4">➕ Thêm Banner</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 📋 Bảng Banner --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-header text-white">
                    <tr class="text-center">
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Type</th>
                        <th>Ảnh/Video</th>
                        <th>Hiển thị</th>
                        <th>Thứ tự</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr class="table-row">
                        <td class="text-center fw-bold text-muted">{{ $banner->id }}</td>
                        <td>{{ $banner->title }}</td>
                        <td class="text-center">{{ ucfirst($banner->type) }}</td>
                        <td class="text-center">
                            @if($banner->type === 'image' && $banner->image)
                                <img src="{{ asset('storage/'.$banner->image) }}" width="100" class="rounded">
                            @elseif($banner->type === 'video' && $banner->video_url)
                                @if(pathinfo($banner->video_url, PATHINFO_EXTENSION) === 'mp4')
                                    <video width="150" controls>
                                        <source src="{{ asset('storage/'.$banner->video_url) }}" type="video/mp4">
                                    </video>
                                @else
                                    <iframe width="200" height="100" src="{{ $banner->video_url }}" frameborder="0" allowfullscreen></iframe>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm {{ $banner->is_active ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $banner->is_active ? 'Hiển thị' : 'Ẩn' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-center">{{ $banner->display_order }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">✏️ Sửa</a>
                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">🗑️ Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Chưa có banner nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $banners->links('pagination::bootstrap-5') }}
    </div>

</div>

{{-- 🎨 CSS đồng bộ với trang liên hệ --}}
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.table-header {
    background: linear-gradient(90deg, #007bff, #00c3ff);
}

.table-row {
    background-color: #fff;
    transition: all 0.25s ease-in-out;
}
.table-row:nth-child(even) {
    background-color: #f8f9fa;
}
.table-row:hover {
    background-color: #e9f5ff;
    transform: scale(1.01);
}

.table th {
    font-weight: 600;
    letter-spacing: 0.3px;
    border-bottom: none !important;
}
.table td {
    padding: 1rem 1.2rem;
    vertical-align: middle;
}
</style>
@endsection
