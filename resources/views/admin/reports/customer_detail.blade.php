@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 text-gray-800 mb-3">Khách hàng: {{ $summary['khach_hang'] }}</h1>
        @include('admin.reports.partials.filter')

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-primary text-uppercase mb-1">Tổng đơn</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['tong_don']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-success text-uppercase mb-1">Đã thanh toán</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['da_thanh_toan']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-info text-uppercase mb-1">Chi tiêu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($summary['chi_tieu'], 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Phim</th>
                            <th class="text-end">Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $o)
                            <tr>
                                <td>{{ $o->ma_don }}</td>
                                <td>{{ $o->phim }}</td>
                                <td class="text-end">{{ number_format($o->tong_tien, 0, ',', '.') }}đ</td>
                                <td><span
                                        class="badge bg-{{ $o->trang_thai === 'da_thanh_toan' ? 'success' : ($o->trang_thai === 'da_huy' ? 'danger' : 'secondary') }}">{{ $o->trang_thai }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($o->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('admin.reports.orders.show', $o->id) }}">Chi tiết</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
