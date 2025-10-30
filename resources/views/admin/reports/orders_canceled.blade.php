@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 text-gray-800 mb-3">Đơn đã hủy</h1>
        @include('admin.reports.partials.filter')

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $o)
                            <tr>
                                <td>{{ $o->ma_don }}</td>
                                <td>{{ $o->khach_hang }}</td>
                                <td>{{ number_format($o->tong_tien, 0, ',', '.') }}đ</td>
                                <td>{{ \Carbon\Carbon::parse($o->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
