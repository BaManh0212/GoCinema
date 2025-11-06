@extends('client.layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<style>
    body {
        background-color: #0b1630;
        font-family: 'Poppins', sans-serif;
        color: #fff;
    }

    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding-top: 80px;
    }

    .login-card {
        background-color: #101c3d;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        padding: 2rem;
        width: 100%;
        max-width: 420px;
        transition: 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 25px rgba(255, 0, 0, 0.1);
    }

    .login-card h3 {
        text-align: center;
        font-weight: 700;
        margin-bottom: 1.5rem;
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

    .form-check-label {
        color: #cfd2da;
    }

    .btn-login {
        width: 100%;
        background-color: #ff4b5c;
        border: none;
        border-radius: 10px;
        padding: 10px;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-login:hover {
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

    .register-text {
        text-align: center;
        margin-top: 1rem;
        color: #ccc;
    }

    .register-text a {
        font-weight: 600;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <h3><i class="bi bi-person-circle me-2"></i>Đăng Nhập</h3>

        @if (session('status'))
            <div class="alert alert-success text-center small py-2 mb-3" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required autofocus
                       placeholder="Nhập email của bạn">
                @error('email')
                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required
                       placeholder="Nhập mật khẩu">
                @error('password')
                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-danger small">Quên mật khẩu?</a>
                @endif
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
            </button>

            <div class="register-text">
                <p class="mb-0">Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="text-danger">Đăng ký ngay</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
