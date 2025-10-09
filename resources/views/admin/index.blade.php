@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Chào mừng đến với trang quản trị 🎬</h2>
    <p>Chọn một chức năng bên trái để bắt đầu quản lý:</p>

    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Rạp chiếu</h5>
                    <p>Quản lý thông tin các rạp.</p>
                    <a href="{{ route('admin.rap.index') }}" class="btn btn-primary">Vào quản lý</a>

                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Phòng chiếu</h5>
                    <p>Quản lý các phòng chiếu trong rạp.</p>
                    <a href="#" class="btn btn-primary">Vào quản lý</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Ghế</h5>
                    <p>Quản lý sơ đồ ghế và trạng thái.</p>
                    <a href="#" class="btn btn-primary">Vào quản lý</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
