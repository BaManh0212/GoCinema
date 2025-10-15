@extends('admin.layouts.admin')

@section('title', 'Sửa người dùng')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Sửa thông tin người dùng</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.nguoi-dung.update', $nguoiDung->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Họ tên <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" 
                                       value="{{ old('ho_ten', $nguoiDung->ho_ten) }}" required>
                                @error('ho_ten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $nguoiDung->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Mật khẩu mới</label>
                            <div class="col-sm-9">
                                <input type="password" name="mat_khau" class="form-control @error('mat_khau') is-invalid @enderror">
                                @error('mat_khau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Để trống nếu không muốn đổi mật khẩu</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" name="so_dien_thoai" class="form-control" 
                                       value="{{ old('so_dien_thoai', $nguoiDung->so_dien_thoai) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Vai trò <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select name="vai_tro_id" class="form-select @error('vai_tro_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn vai trò --</option>
                                    @foreach($vaiTros as $vt)
                                        <option value="{{ $vt->id }}" 
                                                {{ old('vai_tro_id', $nguoiDung->vai_tro_id) == $vt->id ? 'selected' : '' }}>
                                            {{ $vt->ten }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vai_tro_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Điểm tích lũy</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" 
                                       value="{{ $nguoiDung->diem_tich_luy }}" disabled>
                                <small class="text-muted">
                                    Để thay đổi điểm, vui lòng sử dụng chức năng 
                                    <a href="{{ route('admin.diem-tich-luy.create') }}?nguoi_dung_id={{ $nguoiDung->id }}">
                                        Thêm/Trừ điểm
                                    </a>
                                </small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Trạng thái</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="trang_thai" 
                                           id="trang_thai" value="1" 
                                           {{ old('trang_thai', $nguoiDung->trang_thai) ? 'checked' : '' }}>
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
                                <i class="fas fa-save me-1"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
