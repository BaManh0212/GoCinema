@extends('client.layouts.app')

@section('title', 'Đăng ký tài khoản')

@section('content')
<style>
    body {
        background-color: #0b1630;
        font-family: 'Poppins', sans-serif;
        color: #fff;
    }

    .register-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding-top: 80px;
    }

    .register-card {
        background-color: #101c3d;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        padding: 2rem;
        width: 100%;
        max-width: 480px;
        transition: 0.3s ease;
    }

    .register-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 25px rgba(255, 0, 0, 0.1);
    }

    .register-card h3 {
        text-align: center;
        font-weight: 700;
        margin-bottom: 1.2rem;
        color: #ff4b5c;
    }

    .form-control {
        background-color: #0e1730;
        border: 1px solid #1f2b50;
        color: #fff;
        border-radius: 10px;
        padding: 10px 14px;
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: #ff4b5c;
        box-shadow: none;
    }

    .form-label {
        font-weight: 500;
        color: #cfd2da;
    }

    .btn-register {
        width: 100%;
        background-color: #ff4b5c;
        border: none;
        border-radius: 10px;
        padding: 10px;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-register:hover {
        background-color: #e64051;
        transform: translateY(-1px);
    }

    a.text-danger {
        color: #ff4b5c !important;
        transition: 0.2s ease;
    }

    a.text-danger:hover {
        text-decoration: underline;
        color: #ff6b7a !important;
    }

    .login-text {
        text-align: center;
        margin-top: 1rem;
        color: #ccc;
    }

    .login-text a {
        font-weight: 600;
    }

    .invalid-feedback {
        color: #ff9e9e;
        font-size: 0.9rem;
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <h3><i class="bi bi-person-plus-fill me-2"></i>Đăng Ký</h3>
        <p class="text-center text-secondary small mb-4">Tạo tài khoản để đặt vé và nhận ưu đãi độc quyền!</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Họ tên -->
            <div class="mb-3">
                <label for="ho_ten" class="form-label">Họ tên</label>
                <input id="ho_ten" type="text"
                       class="form-control @error('ho_ten') is-invalid @enderror"
                       name="ho_ten" value="{{ old('ho_ten') }}" required autofocus
                       placeholder="Nhập họ và tên của bạn">
                @error('ho_ten')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required
                       placeholder="Nhập email của bạn">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Số điện thoại -->
            <div class="mb-3">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input id="so_dien_thoai" type="text"
                       class="form-control @error('so_dien_thoai') is-invalid @enderror"
                       name="so_dien_thoai" value="{{ old('so_dien_thoai') }}" required
                       placeholder="Nhập số điện thoại">
                @error('so_dien_thoai')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mật khẩu -->
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required
                       placeholder="Nhập mật khẩu">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <input id="password_confirmation" type="password" class="form-control"
                       name="password_confirmation" required
                       placeholder="Nhập lại mật khẩu">
            </div>

            <!-- Nút đăng ký -->
            <button type="submit" class="btn btn-register">
                <i class="bi bi-person-check-fill me-1"></i> Đăng ký
            </button>

            <div class="login-text">
                <p class="mb-0">Đã có tài khoản?
                    <a href="{{ route('login') }}" class="text-danger">Đăng nhập ngay</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
