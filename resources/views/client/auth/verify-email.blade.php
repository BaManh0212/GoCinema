@extends('client.layouts.app')

@section('title', 'Xác minh email')

@section('content')
<style>
    body {
        background-color: #0b1630;
        font-family: 'Poppins', sans-serif;
        color: #fff;
    }

    .verify-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding-top: 80px;
    }

    .verify-card {
        background-color: #101c3d;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        padding: 2rem;
        width: 100%;
        max-width: 480px;
        transition: 0.3s ease;
    }

    .verify-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 25px rgba(255, 0, 0, 0.1);
    }

    .verify-card h3 {
        text-align: center;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #ff4b5c;
    }

    .btn-danger {
        background-color: #ff4b5c;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-danger:hover {
        background-color: #e64051;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(255, 77, 92, 0.3);
    }

    .btn-outline-secondary {
        border: 1px solid #6c757d;
        color: #6c757d;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
        transform: translateY(-1px);
    }

    .text-muted {
        color: #cfd2da !important;
    }

    .alert-success {
        background-color: rgba(40, 167, 69, 0.15);
        border: 1px solid rgba(40, 167, 69, 0.4);
        color: #7aff9b;
        border-radius: 10px;
        font-size: 14px;
    }
</style>

<div class="verify-wrapper">
    <div class="verify-card">
        <h3><i class="bi bi-envelope-check me-2"></i>Xác minh email</h3>

        <p class="text-muted text-center mb-4 small">
            Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn có thể xác minh địa chỉ email bằng cách nhấp vào liên kết chúng tôi vừa gửi cho bạn không? Nếu bạn không nhận được email, chúng tôi sẽ vui lòng gửi cho bạn một email khác.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success text-center py-2 mb-3" role="alert">
                <i class="bi bi-check-circle me-1"></i> Một liên kết xác minh mới đã được gửi đến địa chỉ email bạn cung cấp trong quá trình đăng ký.
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-send me-1"></i> Gửi lại email xác minh
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
