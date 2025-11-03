@extends('client.layouts.app')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">Quên Mật Khẩu</h4>

                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <p class="text-muted">
                            Quên mật khẩu? Không có gì phải lo lắng. Chỉ cần nhập email của bạn và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu mới.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-danger">
                                Gửi liên kết đặt lại mật khẩu
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none text-danger">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
