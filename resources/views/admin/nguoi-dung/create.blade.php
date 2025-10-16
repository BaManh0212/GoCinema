@extends('admin.layouts.admin')

@section('title', 'Thêm người dùng')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Thêm người dùng mới</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.nguoi-dung.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Họ tên <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" 
                                       value="{{ old('ho_ten') }}" required>
                                @error('ho_ten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" name="mat_khau" class="form-control @error('mat_khau') is-invalid @enderror" 
                                       required>
                                @error('mat_khau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" name="so_dien_thoai" class="form-control" 
                                       value="{{ old('so_dien_thoai') }}">
                            </div>
                        </div>

                         <input type="hidden" name="vai_tro_id" value="2">

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Điểm tích lũy</label>
                            <div class="col-sm-9">
                                <input type="number" name="diem_tich_luy" class="form-control" 
                                       value="{{ old('diem_tich_luy', 0) }}" min="0">
                                <small class="text-muted">Điểm ban đầu cho người dùng</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Trạng thái</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="trang_thai" 
                                           id="trang_thai" value="1" {{ old('trang_thai', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="trang_thai">
                                        Hoạt động
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="text-end">
                            <a href="{{ route('admin.nguoi-dung.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Lưu người dùng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
