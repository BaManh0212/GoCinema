@extends('client.layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<style>
    body {
        background-color: #0b1630;
        font-family: 'Poppins', sans-serif;
        color: #fff;
    }

    .forgot-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding-top: 80px;
    }

    .forgot-card {
        background-color: #101c3d;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        padding: 2rem;
        width: 100%;
        max-width: 420px;
        transition: 0.3s ease;
    }

    .forgot-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 25px rgba(255, 0, 0, 0.1);
    }

    .forgot-card h3 {
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

    label {
        font-weight: 500;
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

    a.text-danger {
        color: #ff4b5c !important;
        transition: 0.2s ease;
    }

    a.text-danger:hover {
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
</style>

<div class="forgot-wrapper">
    <div class="forgot-card">
        <h3><i class="bi bi-envelope-paper-heart me-2"></i>Quên Mật Khẩu</h3>

        {{-- Thông báo trạng thái --}}
        @if (session('status'))
            <div class="alert alert-success text-center py-2 mb-3" role="alert">
                <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            </div>
        @endif

        <p class="text-muted text-center mb-4 small">
            Nhập email đã đăng ký của bạn. Chúng tôi sẽ gửi liên kết để đặt lại mật khẩu mới.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Địa chỉ email</label>
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required autofocus
                       placeholder="Nhập email của bạn">
                @error('email')
                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-danger">
                <i class="bi bi-send me-1"></i> Gửi liên kết đặt lại mật khẩu
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
