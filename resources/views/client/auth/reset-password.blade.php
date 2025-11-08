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
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        width: 100%;
        max-width: 420px;
    }

    .reset-card h3 {
        text-align: center;
        color: #ff4b5c;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .form-control {
        background-color: #0e1730;
        border: 1px solid #1f2b50;
        color: #fff;
        border-radius: 10px;
        padding: 10px 14px;
    }

    .btn-danger {
        width: 100%;
        background-color: #ff4b5c;
        border: none;
        border-radius: 10px;
        padding: 10px;
        font-weight: 600;
    }

    .btn-danger:hover {
        background-color: #e64051;
    }

    .text-center a {
        color: #ff4b5c !important;
    }
</style>

<div class="reset-wrapper">
    <div class="reset-card">
        <h3><i class="bi bi-shield-lock-fill me-2"></i>Đặt lại mật khẩu</h3>

        @if (session('status'))
            <div class="alert alert-success small text-center">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="mb-3">
        <label for="email" class="form-label">Địa chỉ email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
               class="form-control @error('email') is-invalid @enderror"
               placeholder="Nhập email của bạn">
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Mật khẩu mới</label>
        <input id="password" type="password" name="password" required
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Nhập mật khẩu mới">
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="form-control" placeholder="Nhập lại mật khẩu mới">
    </div>

    <button type="submit" class="btn btn-danger">
        Cập nhật mật khẩu
    </button>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}">Quay lại đăng nhập</a>
    </div>
</form>

    </div>
</div>
@endsection
