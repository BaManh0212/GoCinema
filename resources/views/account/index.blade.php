@extends('client.layouts.app')

@section('title', 'Tài khoản của tôi')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5 class="card-title">{{ $user->ho_ten }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="badge bg-primary fs-6 mb-3">
                        <i class="fas fa-star"></i> {{ number_format($user->diem) }} điểm
                    </div>
                </div>
                <style>
                .list-group-item {
                    background-color: #fff !important;
                    color: #000 !important;
                    border: 1px solid #dee2e6 !important;
                    font-weight: 500 !important;
                    opacity: 1 !important;
                    filter: none !important;
                    text-shadow: none !important;
                }

                .list-group-item:hover {
                    background-color: #f1f1f1 !important;
                    color: #000 !important;
                }

                .list-group-item.active {
                    background-color: #0d6efd !important;
                    color: #fff !important;
                    border-color: #0d6efd !important;
                }

                .list-group-item i {
                    color: inherit !important;
                }
                </style>

                <div class="list-group list-group-flush">
                    <a href="{{ route('account.index') }}"
                    class="list-group-item list-group-item-action active">
                        <i class="fas fa-user me-2"></i> Thông tin tài khoản
                    </a>

                    <a href="{{ route('account.rewards') }}"
                    class="list-group-item list-group-item-action">
                        <i class="fas fa-gift me-2"></i> Đổi điểm thưởng
                    </a>

                    <a href="{{ route('account.my-vouchers') }}"
                    class="list-group-item list-group-item-action">
                        <i class="fas fa-ticket-alt me-2"></i> Voucher của tôi
                    </a>

                    <a href="{{ route('account.point-history') }}"
                    class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i> Lịch sử điểm
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Thông báo -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Thông tin cá nhân -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.update-profile') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Họ và tên</label>
                            <div class="col-sm-9">
                                <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" 
                                       value="{{ old('ho_ten', $user->ho_ten) }}" required>
                                @error('ho_ten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Số điện thoại</label>
                            <div class="col-sm-9">
                                <input type="text" name="so_dien_thoai" class="form-control" 
                                       value="{{ old('so_dien_thoai', $user->so_dien_thoai ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Vai trò</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $user->vaiTro->ten ?? 'N/A' }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Điểm tích lũy</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ number_format($user->diem) }}" disabled>
                                    <span class="input-group-text"><i class="fas fa-star text-warning"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật thông tin
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Đổi mật khẩu -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.change-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Mật khẩu hiện tại</label>
                            <div class="col-sm-9">
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Mật khẩu mới</label>
                            <div class="col-sm-9">
                                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Xác nhận mật khẩu mới</label>
                            <div class="col-sm-9">
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lịch sử điểm gần đây -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử điểm gần đây</h5>
                </div>
                <div class="card-body">
                    @if($lichSuDiem->isEmpty())
                        <p class="text-muted text-center py-4">Chưa có lịch sử giao dịch điểm</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Loại</th>
                                        <th>Điểm</th>
                                        <th>Mô tả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichSuDiem as $ls)
                                        <tr>
                                            <td>{{ $ls->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($ls->hanh_dong == 'tich_luy')
                                                    <span class="badge bg-success">Tích lũy</span>
                                                @else
                                                    <span class="badge bg-danger">Sử dụng</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ls->hanh_dong == 'tich_luy')
                                                    <span class="text-success">+{{ number_format($ls->diem) }}</span>
                                                @else
                                                    <span class="text-danger">-{{ number_format($ls->diem) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $ls->mo_ta ?: 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('account.point-history') }}" class="btn btn-outline-primary">
                                Xem tất cả lịch sử <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
