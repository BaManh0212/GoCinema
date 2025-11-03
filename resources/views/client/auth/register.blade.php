@extends('client.layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">Đăng Ký Tài Khoản</h4>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                        <!-- Họ tên -->
                        <div class="mb-3">
                            <label for="ho_ten" class="form-label">Họ tên</label>
                            <input id="ho_ten" type="text" class="form-control @error('ho_ten') is-invalid @enderror" 
                                   name="ho_ten" value="{{ old('ho_ten') }}" required autofocus>
                            @error('ho_ten')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Số điện thoại -->
                        <div class="mb-3">
                            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                            <input id="so_dien_thoai" type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror" 
                                   name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" required>
                            @error('so_dien_thoai')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                            <input id="password_confirmation" type="password" class="form-control"
                                   name="password_confirmation" required>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-danger">
                                Đăng ký
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Đã có tài khoản? 
                                <a href="{{ route('login') }}" class="text-decoration-none text-danger">Đăng nhập ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
