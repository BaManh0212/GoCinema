@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold text-primary mb-3">
        {{ $banner->id ? '✏️ Chỉnh sửa Banner' : '➕ Thêm Banner' }}
    </h2>

    <form action="{{ $banner->id ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($banner->id)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}">
        </div>

        <div class="mb-3">
            <label>Loại Banner</label>
            <select name="type" class="form-select" id="bannerType">
                <option value="image" {{ old('type', $banner->type) == 'image' ? 'selected' : '' }}>Ảnh</option>
                <option value="video" {{ old('type', $banner->type) == 'video' ? 'selected' : '' }}>Video</option>
            </select>
        </div>

        <div id="imageField" class="mb-3">
            <label>Ảnh banner</label>
            <input type="file" name="image" class="form-control">
            @if($banner->image)
                <img src="{{ asset('storage/'.$banner->image) }}" width="150" class="mt-2 rounded">
            @endif
        </div>

        <div id="videoField" class="mb-3">
            <label>Video (upload MP4 hoặc link YouTube)</label>
            <input type="file" name="video_file" class="form-control mb-2" accept="video/mp4">
            <input type="text" name="video_url" class="form-control" placeholder="hoặc dán link video..." value="{{ old('video_url', $banner->video_url) }}">
            @if($banner->video_url && pathinfo($banner->video_url, PATHINFO_EXTENSION) === 'mp4')
                <video width="200" controls class="mt-2">
                    <source src="{{ asset('storage/'.$banner->video_url) }}" type="video/mp4">
                </video>
            @elseif($banner->video_url)
                <iframe width="300" height="150" src="{{ $banner->video_url }}" frameborder="0" allowfullscreen class="mt-2"></iframe>
            @endif
        </div>

        <div class="mb-3">
            <label>Liên kết khi click</label>
            <input type="text" name="link" class="form-control" value="{{ old('link', $banner->link) }}">
        </div>

        <div class="mb-3">
            <label>Thứ tự hiển thị</label>
            <input type="number" name="display_order" class="form-control" value="{{ old('display_order', $banner->display_order) }}">
        </div>

        <div class="form-check form-switch mb-3">
            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
            <label for="isActive" class="form-check-label">Hiển thị</label>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">{{ $banner->id ? 'Cập nhật' : 'Thêm mới' }}</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>

<script>
const typeSelect = document.getElementById('bannerType');
const imageField = document.getElementById('imageField');
const videoField = document.getElementById('videoField');

function toggleFields() {
    if (typeSelect.value === 'image') {
        imageField.style.display = 'block';
        videoField.style.display = 'none';
    } else {
        imageField.style.display = 'none';
        videoField.style.display = 'block';
    }
}

toggleFields();
typeSelect.addEventListener('change', toggleFields);
</script>
@endsection
