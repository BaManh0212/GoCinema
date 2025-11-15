@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Tổng quan doanh thu</h1>
        @include('admin.reports.partials.filter')

        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-primary text-uppercase mb-1">Tổng doanh thu</div>
                        <div class="h5 font-weight-bold">{{ number_format($summary['tong_doanh_thu'] ?? 0, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-success text-uppercase mb-1">Vé</div>
                        <div class="h5 font-weight-bold">{{ number_format($summary['ve'] ?? 0, 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-info text-uppercase mb-1">Combo</div>
                        <div class="h5 font-weight-bold">{{ number_format($summary['combo'] ?? 0, 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="text-xs text-warning text-uppercase mb-1">Sản phẩm</div>
                        <div class="h5 font-weight-bold">{{ number_format($summary['san_pham'] ?? 0, 0, ',', '.') }}đ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo ngày</h6>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th class="text-right">Vé</th>
                            <th class="text-right">Combo</th>
                            <th class="text-right">Sản phẩm</th>
                            <th class="text-right">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($days as $d)
                            <tr>
                                <td>{{ $d['ngay'] }}</td>
                                <td class="text-right">{{ number_format($d['ve'], 0, ',', '.') }}đ</td>
                                <td class="text-right">{{ number_format($d['combo'], 0, ',', '.') }}đ</td>
                                <td class="text-right">{{ number_format($d['san_pham'], 0, ',', '.') }}đ</td>
                                <td class="text-right">{{ number_format($d['tong'], 0, ',', '.') }}đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
