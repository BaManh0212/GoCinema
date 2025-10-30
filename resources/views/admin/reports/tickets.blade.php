@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 text-gray-800 mb-3">Danh sách vé ({{ \Carbon\Carbon::parse($from)->format('d/m') }} -
            {{ \Carbon\Carbon::parse($to)->format('d/m') }})</h1>
        @include('admin.reports.partials.filter')

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-primary text-uppercase mb-1">Tổng vé</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['tong_ve']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-success text-uppercase mb-1">Đã thanh toán / sử dụng</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['da_thanh_toan']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-left-danger shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-danger text-uppercase mb-1">Đã hủy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['da_huy']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Phim</th>
                            <th>Rạp</th>
                            <th class="text-end">Giá</th>
                            <th>Loại ghế</th>
                            <th>Trạng thái</th>
                            <th>Ngày mua</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ $r->id }}</td>
                                <td>{{ $r->phim }}</td>
                                <td>{{ $r->rap }}</td>
                                <td class="text-end">{{ number_format($r->gia, 0, ',', '.') }}đ</td>
                                <td>{{ $r->loai_ghe }}</td>
                                <td>{{ $r->trang_thai }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->ngay_mua)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
