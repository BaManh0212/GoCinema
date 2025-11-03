@extends('client.layouts.app')

@section('title', 'Lịch sử điểm')

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
                <div class="list-group list-group-flush">
                    <a href="{{ route('account.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="{{ route('account.rewards') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-gift me-2"></i> Đổi điểm thưởng
                    </a>
                    <a href="{{ route('account.my-vouchers') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-ticket-alt me-2"></i> Voucher của tôi
                    </a>
                    <a href="{{ route('account.point-history') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-history me-2"></i> Lịch sử điểm
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử giao dịch điểm</h4>
                </div>
                <div class="card-body">
                    @if($lichSuDiem->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có lịch sử giao dịch điểm</p>
                        </div>
                    @else
                        <!-- Thống kê -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6>Tổng điểm tích lũy</h6>
                                        <h3>
                                            <i class="fas fa-plus-circle me-2"></i>
                                            {{ number_format($lichSuDiem->where('hanh_dong', 'tich_luy')->sum('diem')) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h6>Tổng điểm đã sử dụng</h6>
                                        <h3>
                                            <i class="fas fa-minus-circle me-2"></i>
                                            {{ number_format($lichSuDiem->where('hanh_dong', 'su_dung')->sum('diem')) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bảng lịch sử -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 20%">Thời gian</th>
                                        <th style="width: 15%">Loại giao dịch</th>
                                        <th style="width: 15%">Điểm</th>
                                        <th style="width: 45%">Mô tả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichSuDiem as $index => $ls)
                                        <tr>
                                            <td>{{ $lichSuDiem->firstItem() + $index }}</td>
                                            <td>
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                {{ $ls->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                @if($ls->hanh_dong == 'tich_luy')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-plus me-1"></i>Tích lũy
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-minus me-1"></i>Sử dụng
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ls->hanh_dong == 'tich_luy')
                                                    <strong class="text-success">
                                                        <i class="fas fa-arrow-up me-1"></i>+{{ number_format($ls->diem) }}
                                                    </strong>
                                                @else
                                                    <strong class="text-danger">
                                                        <i class="fas fa-arrow-down me-1"></i>-{{ number_format($ls->diem) }}
                                                    </strong>
                                                @endif
                                            </td>
                                            <td>{{ $ls->mo_ta ?: 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $lichSuDiem->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
