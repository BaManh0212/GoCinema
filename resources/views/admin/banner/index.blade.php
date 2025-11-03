@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary mb-3">📋 Danh sách Banner</h2>

    <a href="{{ route('admin.banners.create') }}" class="btn btn-success mb-3">➕ Thêm Banner</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Tiêu đề</th>
                <th>Type</th>
                <th>Ảnh/Video</th>
                <th>Hiển thị</th>
                <th>Thứ tự</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($banners as $banner)
            <tr>
                <td>{{ $banner->id }}</td>
                <td>{{ $banner->title }}</td>
                <td>{{ ucfirst($banner->type) }}</td>
                <td>
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
                <td>
                    <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm {{ $banner->is_active ? 'btn-success' : 'btn-secondary' }}">
                            {{ $banner->is_active ? 'Hiển thị' : 'Ẩn' }}
                        </button>
                    </form>
                </td>
                <td>{{ $banner->display_order }}</td>
                <td>
                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-primary">✏️ Sửa</a>
                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">🗑️ Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $banners->links() }}
</div>
@endsection
