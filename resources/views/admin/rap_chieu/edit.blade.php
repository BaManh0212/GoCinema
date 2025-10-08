@extends('admin.layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Sửa rạp chiếu</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.rap.update', $rap->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="ten" class="form-label">Tên rạp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ten" name="ten"
                            value="{{ old('ten', $rap->ten) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="dia_chi" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dia_chi" name="dia_chi"
                            value="{{ old('dia_chi', $rap->dia_chi) }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.rap.index') }}" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>
        </div>
    </div>
@endsection
