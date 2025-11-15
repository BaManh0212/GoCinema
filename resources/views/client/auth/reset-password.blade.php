@extends('client.layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<style>
    body {
        background-color: #0b1630;
        font-family: 'Poppins', sans-serif;
        color: #fff;
    }

    .reset-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding-top: 80px;
    }

    .reset-card {
        background-color: #101c3d;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        padding: 2rem;
        width: 100%;
        max-width: 420px;
        transition: 0.3s ease;
    }

    .reset-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 25px rgba(255, 0, 0, 0.1);
    }

    .reset-card h3 {
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
        background-color: #0f1a3d;
    }

    .form-label {
        font-weight: 500;
        color: #cfd2da;
    }

    .btn-danger {
        width: 100%;
        background-color: #ff4b5c;
        border: none;
        border-radius: 10px;
        padding: 10px;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #e64051;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(255, 77, 92, 0.3);
    }

    .text-center a {
        color: #ff4b5c !important;
        transition: 0.2s ease;
    }

    .text-center a:hover {
        color: #ff6b7a !important;
        text-decoration: underline;
    }

    .alert-success {
        background-color: rgba(40, 167, 69, 0.15);
        border: 1px solid rgba(40, 167, 69, 0.4);
        color: #7aff9b;
        border-radius: 10px;
        font-size: 14px;
    }

    .invalid-feedback {
        color: #ff9e9e;
        font-size: 0.9rem;
    }
</style>

<div class="reset-wrapper">
    <div class="reset-card">
        <h3><i class="bi bi-shield-lock-fill me-2"></i>Đặt lại mật khẩu</h3>

        @if (session('status'))
            <div class="alert alert-success text-center py-2 mb-3" role="alert">
                <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            </div>
        @endif

        <p class="text-muted text-center mb-4 small">
            Nhập mật khẩu mới cho tài khoản của bạn.
        </p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Địa chỉ email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="Nhập email của bạn">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mật khẩu mới -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Mật khẩu mới</label>
                <input id="password" type="password" name="password" required
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Nhập mật khẩu mới">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="form-control" placeholder="Nhập lại mật khẩu mới">
            </div>

            <button type="submit" class="btn btn-danger">
                <i class="bi bi-check-circle me-1"></i> Cập nhật mật khẩu
            </button>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-danger text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại đăng nhập
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
