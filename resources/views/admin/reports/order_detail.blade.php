@extends('admin.layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 text-gray-800 mb-0">Chi tiết đơn: {{ $order->ma_don }}</h1>
        </div>

        <div class="row">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="mb-2"><strong>Khách hàng: </strong>{{ $order->khach_hang }} ({{ $order->email }})</div>
                        <div class="mb-2"><strong>Phim: </strong>{{ $order->phim }}</div>
                        <div class="mb-2"><strong>Rạp: </strong>{{ $order->rap }}</div>
                        <div class="mb-2"><strong>Trạng thái: </strong>
                            <span
                                class="badge bg-{{ $order->trang_thai === 'da_thanh_toan' ? 'success' : ($order->trang_thai === 'da_huy' ? 'danger' : 'secondary') }}">{{ $order->trang_thai }}</span>
                        </div>
                        <div class="mb-2"><strong>Ngày tạo:
                            </strong>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-md-6 mb-3">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <div class="text-xs text-muted">SL vé</div>
                                <div class="h5 mb-0">{{ number_format($summary['tong_ve']) }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="text-xs text-muted">Tiền vé</div>
                                <div class="h5 mb-0">{{ number_format($summary['tien_ve'], 0, ',', '.') }}đ</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="text-xs text-muted">Combo</div>
                                <div class="h5 mb-0">{{ number_format($summary['tien_combo'], 0, ',', '.') }}đ</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="text-xs text-muted">Sản phẩm</div>
                                <div class="h5 mb-0">{{ number_format($summary['tien_sp'], 0, ',', '.') }}đ</div>
                            </div>
                        </div>
                        <div class="mt-2"><span class="text-xs text-muted">Đã thanh toán:</span>
                            <strong>{{ number_format($summary['da_thanh_toan'], 0, ',', '.') }}đ</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Vé -->
            <div class="col-lg-6 mb-3">
                <div class="card shadow">
                    <div class="card-header py-2"><strong>Vé</strong></div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ghế</th>
                                    <th class="text-end">Giá</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $t)
                                    <tr>
                                        <td>{{ $t->id }}</td>
                                        <td>{{ $t->hang }}{{ $t->cot }}</td>
                                        <td class="text-end">{{ number_format($t->gia, 0, ',', '.') }}đ</td>
                                        <td>{{ $t->trang_thai }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Combo -->
            <div class="col-lg-6 mb-3">
                <div class="card shadow">
                    <div class="card-header py-2"><strong>Combo</strong></div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Combo</th>
                                    <th class="text-end">SL</th>
                                    <th class="text-end">Giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($combos as $c)
                                    <tr>
                                        <td>{{ $c->ten }}</td>
                                        <td class="text-end">{{ number_format($c->so_luong) }}</td>
                                        <td class="text-end">{{ number_format($c->gia, 0, ',', '.') }}đ</td>
                                        <td class="text-end">{{ number_format($c->thanh_tien, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm -->
            <div class="col-lg-6 mb-3">
                <div class="card shadow">
                    <div class="card-header py-2"><strong>Sản phẩm</strong></div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-end">SL</th>
                                    <th class="text-end">Giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $p)
                                    <tr>
                                        <td>{{ $p->ten }}</td>
                                        <td class="text-end">{{ number_format($p->so_luong) }}</td>
                                        <td class="text-end">{{ number_format($p->gia, 0, ',', '.') }}đ</td>
                                        <td class="text-end">{{ number_format($p->thanh_tien, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Thanh toán -->
            <div class="col-lg-6 mb-3">
                <div class="card shadow">
                    <div class="card-header py-2"><strong>Thanh toán</strong></div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>PT</th>
                                    <th class="text-end">Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Mã GD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $p)
                                    <tr>
                                        <td>{{ $p->phuong_thuc }}</td>
                                        <td class="text-end">{{ number_format($p->so_tien, 0, ',', '.') }}đ</td>
                                        <td>{{ $p->trang_thai }}</td>
                                        <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $p->ma_giao_dich }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
