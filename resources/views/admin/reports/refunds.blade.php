@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 text-gray-800 mb-3">Hoàn tiền</h1>
        @include('admin.reports.partials.filter')

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Tổng: {{ number_format($summary['so_giao_dich']) }} giao dịch —
                    {{ number_format($summary['tong_tien'], 0, ',', '.') }}đ
                </h6>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Thời gian</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ $r->ma_don }}</td>
                                <td>{{ $r->khach_hang }}</td>
                                <td>{{ number_format($r->so_tien, 0, ',', '.') }}đ</td>
                                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $r->ma_giao_dich }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
