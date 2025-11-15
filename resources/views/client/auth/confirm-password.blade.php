@extends('client.layouts.app')

@section('title', 'Xác nhận mật khẩu')

@section('content')
<style>
    body {
        background-color: #0b1630;
        font-family: 'Poppins', sans-serif;
        color: #fff;
    }

    .confirm-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding-top: 80px;
    }

    .confirm-card {
        background-color: #101c3d;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        padding: 2rem;
        width: 100%;
        max-width: 420px;
        transition: 0.3s ease;
    }

    .confirm-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 25px rgba(255, 0, 0, 0.1);
    }

    .confirm-card h3 {
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

    .text-muted {
        color: #cfd2da !important;
    }

    .invalid-feedback {
        color: #ff9e9e;
        font-size: 0.9rem;
    }
</style>

<div class="confirm-wrapper">
    <div class="confirm-card">
        <h3><i class="bi bi-shield-check me-2"></i>Xác nhận mật khẩu</h3>

        <p class="text-muted text-center mb-4 small">
            Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="current-password"
                       placeholder="Nhập mật khẩu của bạn">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger">
                <i class="bi bi-check-circle me-1"></i> Xác nhận
            </button>
        </form>
    </div>
</div>
@endsection
